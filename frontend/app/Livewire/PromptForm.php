<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\WordPress\WordPressPromptService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Livewire Component: PromptForm
 *
 * Handles the creation and updating of prompts via the WordPress REST API.
 *
 * @package App\Livewire
 */
#[Title('Editor de Prompt — Engenharia de Prompts')]
#[Layout('layouts.app')]
class PromptForm extends Component
{
    public ?int $promptId = null;
    public string $titulo = '';
    public string $estrutura = '';
    public string $variaveis = ''; // Comma separated in the input
    public string $exemplo = '';
    public array $selectedTecnicas = [];
    public array $selectedEngines = [];

    public function mount(?int $id = null)
    {
        if ($id) {
            $this->promptId = $id;
            $this->loadPromptData($id);
        }
    }

    /**
     * Loads prompt data from the WordPress API if an ID is provided.
     *
     * @param int $id The prompt ID.
     */
    private function loadPromptData(int $id)
    {
        $service = app(WordPressPromptService::class);
        $prompt = $service->getPromptById($id);

        if (!$prompt) {
            return redirect()->route('prompts.manage')->with('error', 'Prompt não encontrado.');
        }

        $this->titulo = $prompt['title']['rendered'] ?? '';
        $acf = $prompt['acf'] ?? [];
        $this->estrutura = $acf['estrutura_prompt'] ?? '';
        $this->variaveis = implode(', ', $acf['variaveis_necessarias'] ?? []);
        $this->exemplo = $acf['exemplo_saida'] ?? '';

        $termos = $prompt['_embedded']['wp:term'] ?? [];
        $tecnicas = $termos[0] ?? [];
        $engines = $termos[1] ?? [];

        $this->selectedTecnicas = array_column($tecnicas, 'id');
        $this->selectedEngines = array_column($engines, 'id');
    }

    /**
     * Validates and saves the prompt (creates or updates).
     */
    public function save()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'estrutura' => 'required|string',
        ], [
            'titulo.required' => 'O título é obrigatório.',
            'estrutura.required' => 'A estrutura do prompt é obrigatória.',
        ]);

        $service = app(WordPressPromptService::class);

        // Process variables
        $varsArray = array_map('trim', explode(',', $this->variaveis));
        $varsArray = array_filter($varsArray);

        $data = [
            'title' => $this->titulo,
            'estrutura_prompt' => $this->estrutura,
            'variaveis_necessarias' => array_values($varsArray),
            'exemplo_saida' => $this->exemplo,
            'tecnica' => array_map('intval', $this->selectedTecnicas),
            'engine' => array_map('intval', $this->selectedEngines),
        ];

        if ($this->promptId) {
            $success = $service->updatePrompt($this->promptId, $data);
            $msg = 'Prompt atualizado com sucesso!';
        } else {
            $success = $service->createPrompt($data);
            $msg = 'Prompt criado com sucesso!';
        }

        if ($success) {
            return redirect()->route('prompts.manage')->with('status', $msg);
        }

        session()->flash('error', 'Falha ao salvar no WordPress. Verifique logs.');
    }

    public function render()
    {
        $service = app(WordPressPromptService::class);

        return view('livewire.prompt-form', [
            'tecnicasDisponiveis' => $service->getTecnicas(),
            'enginesDisponiveis' => $service->getEngines(),
        ]);
    }
}
