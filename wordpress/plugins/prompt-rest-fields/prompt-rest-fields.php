<?php
/**
 * Plugin Name: Prompt REST Fields
 * Plugin URI:  #
 * Description: Registers and configures custom fields for the Prompt CPT in the WordPress REST API. Compatible with ACF — if ACF is active, this plugin reinforces field exposure.
 * Version:     1.0.0
 * Author:      Engenharia de Prompts
 * License:     GPL-2.0-or-later
 * Text Domain: prompt-rest-fields
 * Requires at least: 6.4
 * Requires PHP: 8.2
 *
 * @package PromptRestFields
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

// ─── Ensure ACF fields appear in the REST API response ───────────────
// When ACF is active, it manages fields under the `acf` key.
// This plugin adds a compatibility layer if ACF is not installed,
// exposing the same fields via `meta` in the API payload.

add_action('rest_api_init', function (): void {

    /**
     * Registers the 'acf' virtual field for the 'prompt' REST API endpoint.
     *
     * This provides a compatibility layer that exposes prompt metadata under the
     * 'acf' key, ensuring the API payload maintains a consistent structure
     * regardless of whether the Advanced Custom Fields plugin is active.
     */
    register_rest_field(
        'prompt',
        'acf',
        [
            'get_callback' => function (array $post_arr): array {
                $post_id = (int) $post_arr['id'];

                /** @var string $estrutura */
                $estrutura = get_post_meta($post_id, 'estrutura_prompt', true) ?: '';

                /** @var string[] $variaveis - get_post_meta with $single=false returns an array */
                $variaveis = get_post_meta($post_id, 'variaveis_necessarias') ?: [];

                /** @var string $exemplo */
                $exemplo = get_post_meta($post_id, 'exemplo_saida', true) ?: '';

                return [
                    'estrutura_prompt'     => $estrutura,
                    'variaveis_necessarias' => array_values(array_filter($variaveis)),
                    'exemplo_saida'        => $exemplo,
                ];
            },
            'update_callback' => null, // Read-only via public API
            'schema'          => [
                'description' => 'Campos customizados do prompt (compatível com ACF).',
                'type'        => 'object',
                'context'     => ['view', 'embed'],
                'properties'  => [
                    'estrutura_prompt' => [
                        'type'        => 'string',
                        'description' => 'O código ou template do prompt.',
                    ],
                    'variaveis_necessarias' => [
                        'type'        => 'array',
                        'description' => 'Lista de variáveis que o usuário precisará preencher.',
                        'items'       => ['type' => 'string'],
                    ],
                    'exemplo_saida' => [
                        'type'        => 'string',
                        'description' => 'Exemplo textual ou de código do resultado gerado pela IA.',
                    ],
                ],
            ],
        ]
    );
});
