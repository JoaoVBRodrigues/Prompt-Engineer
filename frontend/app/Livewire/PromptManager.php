<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\WordPress\WordPressPromptService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gerenciar Prompts — Engenharia de Prompts')]
#[Layout('layouts.app')]
class PromptManager extends Component
{
    public function deletePrompt(int $id)
    {
        $service = app(WordPressPromptService::class);
        $success = $service->deletePrompt($id);

        if ($success) {
            session()->flash('status', 'Prompt deletado com sucesso!');
        } else {
            session()->flash('error', 'Falha ao deletar o prompt.');
        }
    }

    public function render()
    {
        $prompts = app(WordPressPromptService::class)->getAllPrompts();

        return view('livewire.prompt-manager', [
            'prompts' => $prompts,
        ]);
    }
}
