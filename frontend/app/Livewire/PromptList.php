<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\WordPress\WordPressPromptService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Livewire Component: PromptList
 *
 * Displays the list of prompts consumed via WordPress REST API.
 * Supports reactive filtering by taxonomy (tecnica and engine) and
 * client-side text search via Alpine.js.
 *
 * @package App\Livewire
 */
#[Title('Biblioteca de Prompts — Engenharia de Prompts')]
#[Layout('layouts.app')]
final class PromptList extends Component
{
    /**
     * Taxonomy filter "Técnica" (e.g., "chain-of-thought").
     * Synchronized with the URL query string.
     */
    #[Url(as: 'tecnica', except: '')]
    public string $tecnica = '';

    /**
     * Taxonomy filter "Engine" (e.g., "gpt", "claude").
     * Synchronized with the URL query string.
     */
    #[Url(as: 'engine', except: '')]
    public string $engine = '';

    /**
     * Returns the filtered prompts via Service.
     * The #[Computed] annotation caches the request (avoids multiple calls per render).
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function prompts(): array
    {
        return app(WordPressPromptService::class)->getAllPrompts(
            tecnica: $this->tecnica ?: null,
            engine: $this->engine ?: null,
        );
    }

    /**
     * Returns the available techniques for the filter.
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function tecnicas(): array
    {
        return app(WordPressPromptService::class)->getTecnicas();
    }

    /**
     * Returns the available engines for the filter.
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function engines(): array
    {
        return app(WordPressPromptService::class)->getEngines();
    }

    /**
     * Resets both filters to their initial state.
     */
    public function clearFilters(): void
    {
        $this->tecnica = '';
        $this->engine = '';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.prompt-list');
    }
}
