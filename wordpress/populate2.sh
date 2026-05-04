#!/bin/bash
# Create taxonomies
wp term create tecnica 'Chain of Thought' --allow-root || true
wp term create tecnica 'Few-Shot Prompting' --allow-root || true
wp term create tecnica 'Zero-Shot Prompting' --allow-root || true
wp term create engine 'Claude 3.5 Sonnet' --allow-root || true
wp term create engine 'Llama 3' --allow-root || true
wp term create engine 'Midjourney' --allow-root || true

# Post 1
ID1=$(wp post create --post_type=prompt --post_title='Análise de Código com CoT' --post_status=publish --porcelain --allow-root)
wp post term set $ID1 tecnica 'Chain of Thought' --allow-root
wp post term set $ID1 engine 'Claude 3.5 Sonnet' --allow-root
wp post meta update $ID1 estrutura_prompt 'Você é um especialista em code review. Analise o código abaixo usando a técnica Chain of Thought.
Passo 1: Identifique a linguagem e o framework.
Passo 2: Verifique problemas de segurança.
Passo 3: Sugira melhorias de performance.
Código: {codigo}' --allow-root
wp post meta add $ID1 variaveis_necessarias 'codigo' --allow-root
wp post meta update $ID1 exemplo_saida 'Passo 1: A linguagem é PHP e o framework é Laravel.
Passo 2: Nenhuma injeção de SQL detectada na query informada.
Passo 3: Use eager loading na linha 42 para evitar problema de N+1 queries.' --allow-root

# Post 2
ID2=$(wp post create --post_type=prompt --post_title='Gerador de Componentes Vue' --post_status=publish --porcelain --allow-root)
wp post term set $ID2 tecnica 'Few-Shot Prompting' --allow-root
wp post term set $ID2 engine 'Llama 3' --allow-root
wp post meta update $ID2 estrutura_prompt 'Crie um componente Vue 3 (Composition API) com base na descrição.
Exemplo 1: Descrição: Botão primário. Saída: <template><button class="btn-primary">...</button></template>
Exemplo 2: Descrição: Card com título. Saída: <template><div class="card"><h2>...</h2></div></template>

Agora você:
Descrição: {descricao}' --allow-root
wp post meta add $ID2 variaveis_necessarias 'descricao' --allow-root
wp post meta update $ID2 exemplo_saida '<template>
  <div class="modal">
    <header>Modal Title</header>
    <slot></slot>
  </div>
</template>
<script setup>
</script>' --allow-root

# Post 3
ID3=$(wp post create --post_type=prompt --post_title='Cyberpunk Cityscape' --post_status=publish --porcelain --allow-root)
wp post term set $ID3 tecnica 'Zero-Shot Prompting' --allow-root
wp post term set $ID3 engine 'Midjourney' --allow-root
wp post meta update $ID3 estrutura_prompt '/imagine prompt: A sprawling cyberpunk metropolis at night, neon lights reflecting on wet asphalt, volumetric fog, highly detailed, photorealistic, 8k, cinematic lighting, dramatic angle --ar 16:9 --v 6.0 --style raw' --allow-root
wp post meta update $ID3 exemplo_saida '[ Uma imagem hiper realista gerada pelo Midjourney, mostrando uma cidade futurista chuvosa com cores neons fortes como roxo e ciano ]' --allow-root

echo "Dados inseridos com sucesso!"
