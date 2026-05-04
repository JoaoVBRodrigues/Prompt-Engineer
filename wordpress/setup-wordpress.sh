#!/usr/bin/env bash
# =============================================================================
# setup-wordpress.sh
# Script de instalação automática do WordPress via WP-CLI dentro do Docker.
# Execute após: docker compose up -d
# Uso: bash wordpress/setup-wordpress.sh
# =============================================================================

set -euo pipefail

WP_URL="http://localhost:8080"
WP_TITLE="Engenharia de Prompts CMS"
WP_ADMIN_USER="admin"
WP_ADMIN_PASSWORD="admin123"
WP_ADMIN_EMAIL="admin@prompts.local"
CONTAINER="prompts_wpcli"

echo "⏳ Aguardando MySQL inicializar (30s)..."
sleep 30

echo "🔧 Instalando WordPress..."
docker exec "$CONTAINER" wp core install \
  --url="$WP_URL" \
  --title="$WP_TITLE" \
  --admin_user="$WP_ADMIN_USER" \
  --admin_password="$WP_ADMIN_PASSWORD" \
  --admin_email="$WP_ADMIN_EMAIL" \
  --skip-email \
  --allow-root

echo "🎨 Ativando tema prompt-headless..."
docker exec "$CONTAINER" wp theme activate prompt-headless --allow-root

echo "🔌 Ativando plugin prompt-rest-fields..."
docker exec "$CONTAINER" wp plugin activate prompt-rest-fields --allow-root

echo "🔗 Atualizando permalinks para suporte REST API..."
docker exec "$CONTAINER" wp rewrite structure '/%postname%/' --allow-root
docker exec "$CONTAINER" wp rewrite flush --allow-root

echo "📦 Criando prompts de exemplo..."
docker exec "$CONTAINER" wp post create \
  --post_type=prompt \
  --post_title="Gerador de Arquitetura MVC" \
  --post_status=publish \
  --allow-root \
  --porcelain

# Obtém o ID do post recém-criado
POST_ID=$(docker exec "$CONTAINER" wp post list \
  --post_type=prompt \
  --post_status=publish \
  --fields=ID \
  --format=ids \
  --allow-root | tr -d '[:space:]')

echo "🏷️ Adicionando taxonomias ao prompt de exemplo (ID: $POST_ID)..."
docker exec "$CONTAINER" wp term create tecnica "Role-Prompting" --allow-root || true
docker exec "$CONTAINER" wp term create engine "GPT" --allow-root || true
docker exec "$CONTAINER" wp post term set "$POST_ID" tecnica "Role-Prompting" --allow-root
docker exec "$CONTAINER" wp post term set "$POST_ID" engine "GPT" --allow-root

echo "📝 Adicionando meta fields ao prompt de exemplo..."
docker exec "$CONTAINER" wp post meta update "$POST_ID" estrutura_prompt \
  "Atue como um desenvolvedor sênior. Crie a estrutura MVC completa para a entidade {entidade}, incluindo Controller, Model e Views básicas." \
  --allow-root

docker exec "$CONTAINER" wp post meta add "$POST_ID" variaveis_necessarias "entidade" --allow-root

docker exec "$CONTAINER" wp post meta update "$POST_ID" exemplo_saida \
  "Aqui estão as controllers e models para a entidade solicitada: ..." \
  --allow-root

echo ""
echo "✅ WordPress configurado com sucesso!"
echo ""
echo "   Admin:  $WP_URL/wp-admin"
echo "   Login:  $WP_ADMIN_USER / $WP_ADMIN_PASSWORD"
echo "   API:    $WP_URL/wp-json/wp/v2/prompt?_embed"
echo ""
