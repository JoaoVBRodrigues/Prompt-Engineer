<?php

declare(strict_types=1);

namespace App\Services\WordPress;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WordPressPromptService
 *
 * Handles all communication with the /wp-json/wp/v2/prompt endpoint
 * of the Headless WordPress REST API. Applies caching via Laravel Cache Facade
 * to reduce latency and load on the WordPress server.
 *
 * Golden rule: This class is read-only. No data is persisted
 * to the local Laravel database.
 *
 * @package App\Services\WordPress
 * @since   1.0.0
 */
final class WordPressPromptService
{
    /**
     * Base URL of the WordPress REST API.
     */
    private readonly string $baseUrl;

    /**
     * Cache lifetime in seconds.
     */
    private readonly int $cacheTtl;

    /**
     * Prefix for all cache keys in this service.
     */
    private const CACHE_PREFIX = 'wp_prompts';

    private readonly string $apiUser;
    private readonly string $apiPassword;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.wordpress.api_url'), '/');
        $this->cacheTtl = (int) config('services.wordpress.cache_ttl', 300);
        $this->apiUser = (string) config('services.wordpress.api_user', '');
        $this->apiPassword = (string) config('services.wordpress.api_password', '');
    }

    /**
     * Returns all published prompts, optionally filtered by taxonomy.
     *
     * Applies cache with configurable TTL. In case of API failure,
     * returns an empty array and logs the error.
     *
     * @param  string|null $tecnica  Taxonomy slug "tecnica" to filter.
     * @param  string|null $engine   Taxonomy slug "engine" to filter.
     * @param  int         $perPage  Number of results per page.
     * @param  int         $page     Current page (pagination).
     * @return array<int, array<string, mixed>>
     */
    public function getAllPrompts(
        ?string $tecnica = null,
        ?string $engine = null,
        int $perPage = 12,
        int $page = 1,
    ): array {
        $cacheKey = $this->buildCacheKey('list', $tecnica, $engine, $perPage, $page);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($tecnica, $engine, $perPage, $page): array {
            return $this->fetchPrompts($tecnica, $engine, $perPage, $page);
        });
    }

    /**
     * Returns a single prompt by ID.
     *
     * @param  int $id  WordPress post ID.
     * @return array<string, mixed>|null  Returns null if not found.
     */
    public function getPromptById(int $id): ?array
    {
        $cacheKey = static::CACHE_PREFIX . ":single:{$id}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id): ?array {
            return $this->fetchSinglePrompt($id);
        });
    }

    /**
     * Returns all registered techniques (tecnicas).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTecnicas(): array
    {
        $cacheKey = static::CACHE_PREFIX . ':tecnicas';

        return Cache::remember($cacheKey, $this->cacheTtl, function (): array {
            return $this->fetchTaxonomyTerms('tecnica');
        });
    }

    /**
     * Returns all registered engines.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEngines(): array
    {
        $cacheKey = static::CACHE_PREFIX . ':engines';

        return Cache::remember($cacheKey, $this->cacheTtl, function (): array {
            return $this->fetchTaxonomyTerms('engine');
        });
    }

    /**
     * Invalidates all cache related to prompts.
     * Useful for WordPress webhooks or manual refresh.
     */
    public function flushCache(): void
    {
        Cache::forget(static::CACHE_PREFIX . ':tecnicas');
        Cache::forget(static::CACHE_PREFIX . ':engines');

        // Clear paginated list keys (up to 10 pages per combination)
        foreach (range(1, 10) as $page) {
            Cache::forget($this->buildCacheKey('list', null, null, 12, $page));
        }
    }

    // ─── Write Methods (CRUD) ────────────────────────────────────────────

    /**
     * Creates a new prompt in WordPress.
     * @param array{title: string, content: string, tecnica: array<int>, engine: array<int>, estrutura_prompt: string, variaveis_necessarias: array<string>, exemplo_saida: string} $data
     */
    public function createPrompt(array $data): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->apiUser, $this->apiPassword)
                ->acceptJson()
                ->post("{$this->baseUrl}/wp/v2/prompt", [
                    'title'   => $data['title'],
                    'content' => $data['content'] ?? '',
                    'status'  => 'publish',
                    'tecnica' => $data['tecnica'] ?? [],
                    'engine'  => $data['engine'] ?? [],
                    'meta'    => [
                        'estrutura_prompt'      => $data['estrutura_prompt'] ?? '',
                        'variaveis_necessarias' => $data['variaveis_necessarias'] ?? [],
                        'exemplo_saida'         => $data['exemplo_saida'] ?? '',
                    ],
                ]);

            $response->throw();
            $this->flushCache();

            return $response->json();
        } catch (ConnectionException|RequestException $e) {
            Log::error("[WordPressPromptService] Failed to create prompt.", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Updates an existing prompt.
     */
    public function updatePrompt(int $id, array $data): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->apiUser, $this->apiPassword)
                ->acceptJson()
                ->post("{$this->baseUrl}/wp/v2/prompt/{$id}", [
                    'title'   => $data['title'],
                    'content' => $data['content'] ?? '',
                    'status'  => 'publish',
                    'tecnica' => $data['tecnica'] ?? [],
                    'engine'  => $data['engine'] ?? [],
                    'meta'    => [
                        'estrutura_prompt'      => $data['estrutura_prompt'] ?? '',
                        'variaveis_necessarias' => $data['variaveis_necessarias'] ?? [],
                        'exemplo_saida'         => $data['exemplo_saida'] ?? '',
                    ],
                ]);

            $response->throw();
            $this->flushCache();

            return $response->json();
        } catch (ConnectionException|RequestException $e) {
            Log::error("[WordPressPromptService] Failed to update prompt ID {$id}.", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Removes a prompt (moves to trash or forces deletion).
     */
    public function deletePrompt(int $id, bool $force = true): bool
    {
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->apiUser, $this->apiPassword)
                ->acceptJson()
                ->delete("{$this->baseUrl}/wp/v2/prompt/{$id}", [
                    'force' => $force,
                ]);

            $response->throw();
            $this->flushCache();

            return true;
        } catch (ConnectionException|RequestException $e) {
            Log::error("[WordPressPromptService] Failed to delete prompt ID {$id}.", ['error' => $e->getMessage()]);
            return false;
        }
    }

    // ─── Comments API ────────────────────────────────────────────────────────

    /**
     * Fetches approved comments for a specific prompt.
     *
     * @param int $postId
     * @return array<int, array<string, mixed>>
     */
    public function getComments(int $postId): array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$this->baseUrl}/wp/v2/comments", [
                    'post'   => $postId,
                    'status' => 'approve',
                    'order'  => 'asc', // oldest first
                ]);

            $response->throw();

            return $response->json() ?? [];
        } catch (ConnectionException|RequestException $e) {
            Log::error("[WordPressPromptService] Failed to fetch comments for post ID {$postId}.", [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Posts a new comment to a prompt using the custom REST endpoint.
     *
     * @param int $postId
     * @param string $authorName
     * @param string $authorEmail
     * @param string $content
     * @return bool
     */
    public function postComment(int $postId, string $authorName, string $authorEmail, string $content): bool
    {
        try {
            $response = Http::timeout(10)
                ->withBasicAuth($this->apiUser, $this->apiPassword)
                ->acceptJson()
                ->post("{$this->baseUrl}/prompt/v1/comment", [
                    'post_id'      => $postId,
                    'author_name'  => $authorName,
                    'author_email' => $authorEmail,
                    'content'      => $content,
                ]);

            $response->throw();

            return true;
        } catch (ConnectionException|RequestException $e) {
            Log::error("[WordPressPromptService] Failed to post comment to post ID {$postId}.", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // ─── Private fetch methods ────────────────────────────────────────────

    /**
     * Fetches prompts from the WordPress API.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchPrompts(
        ?string $tecnica,
        ?string $engine,
        int $perPage,
        int $page,
    ): array {
        try {
            $params = [
                '_embed'   => 'wp:term',  // Includes taxonomy terms in the payload
                'per_page' => $perPage,
                'page'     => $page,
                'status'   => 'publish',
                '_fields'  => 'id,title,acf,_links,_embedded', // Optimizes payload
            ];

            if ($tecnica !== null && $tecnica !== '') {
                $params['tecnica'] = $tecnica;
            }

            if ($engine !== null && $engine !== '') {
                $params['engine'] = $engine;
            }

            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$this->baseUrl}/wp/v2/prompt", $params);

            $response->throw();

            return $response->json() ?? [];

        } catch (ConnectionException $e) {
            Log::error('[WordPressPromptService] Connection failed with WordPress API.', [
                'url'   => "{$this->baseUrl}/wp/v2/prompt",
                'error' => $e->getMessage(),
            ]);

            return [];

        } catch (RequestException $e) {
            Log::error('[WordPressPromptService] Error response from WordPress API.', [
                'status' => $e->response->status(),
                'body'   => $e->response->body(),
            ]);

            return [];
        }
    }

    /**
     * Fetches a single prompt by ID.
     *
     * @return array<string, mixed>|null
     */
    private function fetchSinglePrompt(int $id): ?array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$this->baseUrl}/wp/v2/prompt/{$id}", [
                    '_embed' => 'wp:term',
                ]);

            if ($response->status() === 404) {
                return null;
            }

            $response->throw();

            return $response->json();

        } catch (ConnectionException|RequestException $e) {
            Log::error("[WordPressPromptService] Failed to fetch prompt ID {$id}.", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetches terms for a taxonomy.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchTaxonomyTerms(string $taxonomy): array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$this->baseUrl}/wp/v2/{$taxonomy}", [
                    'per_page' => 100,
                    '_fields'  => 'id,name,slug,count',
                ]);

            $response->throw();

            return $response->json() ?? [];

        } catch (ConnectionException|RequestException $e) {
            Log::error("[WordPressPromptService] Failed to fetch terms for '{$taxonomy}'.", [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Builds a deterministic cache key based on the provided parameters.
     */
    private function buildCacheKey(mixed ...$parts): string
    {
        $normalized = array_map(
            fn (mixed $part): string => $part === null ? 'null' : (string) $part,
            $parts
        );

        return static::CACHE_PREFIX . ':' . implode(':', $normalized);
    }
}
