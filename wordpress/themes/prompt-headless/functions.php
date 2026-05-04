<?php
/**
 * Prompt Headless theme functions.
 *
 * Responsibilities:
 * - Register the `prompt` Custom Post Type (CPT)
 * - Register the `tecnica` and `engine` taxonomies
 * - Enable REST API support for the CPT and taxonomies
 * - Register metadata (`post_meta`) and expose them via REST
 * - Configure CORS headers to allow consumption by Laravel
 *
 * @package PromptHeadless
 * @since   1.0.0
 */

declare(strict_types=1);

// ─── Minimal theme support ───────────────────────────────────────────────────

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
});

// ─── Application Passwords via HTTP ───────────────────────────────────────────
// Necessary for Docker / localhost where SSL is not present and proxy IPs are not 127.0.0.1
add_filter('wp_is_application_passwords_available_for_request', '__return_true');
add_filter('wp_is_application_passwords_available', '__return_true');

// ─── CORS: allow Laravel application to consume the API ─────────────────────

add_action('rest_api_init', function (): void {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');

    add_filter('rest_pre_serve_request', function (bool $served): bool {
        $allowed_origins = [
            'http://localhost:8000', // Laravel dev server
            'http://127.0.0.1:8000',
        ];

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (in_array($origin, $allowed_origins, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        } else {
            header('Access-Control-Allow-Origin: http://localhost:8000');
        }

        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');

        return $served;
    });
}, 15);

// ─── CPT: prompt ─────────────────────────────────────────────────────────────

add_action('init', function (): void {
    register_post_type('prompt', [
        'labels' => [
            'name'               => __('Prompts', 'prompt-headless'),
            'singular_name'      => __('Prompt', 'prompt-headless'),
            'add_new'            => __('Adicionar Prompt', 'prompt-headless'),
            'add_new_item'       => __('Adicionar Novo Prompt', 'prompt-headless'),
            'edit_item'          => __('Editar Prompt', 'prompt-headless'),
            'view_item'          => __('Ver Prompt', 'prompt-headless'),
            'search_items'       => __('Buscar Prompts', 'prompt-headless'),
            'not_found'          => __('Nenhum prompt encontrado.', 'prompt-headless'),
            'not_found_in_trash' => __('Nenhum prompt na lixeira.', 'prompt-headless'),
        ],
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => ['slug' => 'prompt'],
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-editor-code',
        'supports'            => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions'],
        // Enable full REST API support
        'show_in_rest'        => true,
        'rest_base'           => 'prompt',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ]);
});

// ─── Taxonomy: tecnica ───────────────────────────────────────────────────────

add_action('init', function (): void {
    register_taxonomy('tecnica', ['prompt'], [
        'labels' => [
            'name'              => __('Técnicas', 'prompt-headless'),
            'singular_name'     => __('Técnica', 'prompt-headless'),
            'search_items'      => __('Buscar Técnicas', 'prompt-headless'),
            'all_items'         => __('Todas as Técnicas', 'prompt-headless'),
            'parent_item'       => __('Técnica Pai', 'prompt-headless'),
            'parent_item_colon' => __('Técnica Pai:', 'prompt-headless'),
            'edit_item'         => __('Editar Técnica', 'prompt-headless'),
            'update_item'       => __('Atualizar Técnica', 'prompt-headless'),
            'add_new_item'      => __('Adicionar Nova Técnica', 'prompt-headless'),
            'new_item_name'     => __('Nome da Nova Técnica', 'prompt-headless'),
            'menu_name'         => __('Técnicas', 'prompt-headless'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'tecnica'],
        // Enable REST API support
        'show_in_rest'      => true,
        'rest_base'         => 'tecnica',
    ]);
});

// ─── Taxonomy: engine ────────────────────────────────────────────────────────

add_action('init', function (): void {
    register_taxonomy('engine', ['prompt'], [
        'labels' => [
            'name'              => __('Engines', 'prompt-headless'),
            'singular_name'     => __('Engine', 'prompt-headless'),
            'search_items'      => __('Buscar Engines', 'prompt-headless'),
            'all_items'         => __('Todas as Engines', 'prompt-headless'),
            'edit_item'         => __('Editar Engine', 'prompt-headless'),
            'update_item'       => __('Atualizar Engine', 'prompt-headless'),
            'add_new_item'      => __('Adicionar Nova Engine', 'prompt-headless'),
            'new_item_name'     => __('Nome da Nova Engine', 'prompt-headless'),
            'menu_name'         => __('Engines', 'prompt-headless'),
        ],
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'engine'],
        // Enable REST API support
        'show_in_rest'      => true,
        'rest_base'         => 'engine',
    ]);
});

// ─── Post Meta: ACF/custom fields exposed in the REST API ─────────────────
// Registers fields via register_post_meta() to expose them in /wp-json/wp/v2/prompt
// If the ACF plugin is installed, it will override these values automatically.

add_action('init', function (): void {

    // Field: estrutura_prompt — The prompt template/code
    register_post_meta('prompt', 'estrutura_prompt', [
        'type'              => 'string',
        'description'       => 'The prompt code or template.',
        'single'            => true,
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback'     => '__return_true',
        'show_in_rest'      => true,
    ]);

    // Field: variaveis_necessarias — Array of template variables
    register_post_meta('prompt', 'variaveis_necessarias', [
        'type'              => 'string',
        'description'       => 'List of variables the user will need to fill out.',
        'single'            => false, // false = array of values
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => '__return_true',
        'show_in_rest'      => [
            'schema' => [
                'type'  => 'array',
                'items' => ['type' => 'string'],
            ],
        ],
    ]);

    // Field: exemplo_saida — Example output generated by the AI
    register_post_meta('prompt', 'exemplo_saida', [
        'type'              => 'string',
        'description'       => 'Textual or code example of the result generated by the AI.',
        'single'            => true,
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback'     => '__return_true',
        'show_in_rest'      => true,
    ]);
});

// ─── REST Filter: include terms (_embedded) and meta in CPT results ─────

add_filter('rest_prompt_query', function (array $args, WP_REST_Request $request): array {
    // Ensures terms (taxonomies) are embeddable via ?_embed
    return $args;
}, 10, 2);

// ─── Add autofetch link for embedded terms ─────────────────────

add_filter('rest_prepare_prompt', function (WP_REST_Response $response, WP_Post $post, WP_REST_Request $request): WP_REST_Response {
    // Ensures taxonomy terms appear in _embedded
    $response->add_link(
        'wp:term',
        rest_url('/wp/v2/tecnica?post=' . $post->ID),
        ['embeddable' => true]
    );
    $response->add_link(
        'wp:term',
        rest_url('/wp/v2/engine?post=' . $post->ID),
        ['embeddable' => true]
    );

    return $response;
}, 10, 3);

// ─── Sanitization: remove scripts from content returned by the API ───────────────

add_filter('the_content', 'wp_kses_post');
