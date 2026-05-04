<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\PromptList;
use App\Livewire\PromptManager;
use App\Livewire\PromptForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Listagem pública
Route::get('/prompts', PromptList::class)->name('prompts.index');

Route::get('/dashboard', function () {
    return redirect()->route('prompts.manage');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/prompts/manage', PromptManager::class)->name('prompts.manage');
    Route::get('/prompts/create', PromptForm::class)->name('prompts.create');
    Route::get('/prompts/{id}/edit', PromptForm::class)->name('prompts.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
