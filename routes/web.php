<?php

use App\Models\Task;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'tasks' => Task::query()->orderBy('is_completed', 'asc')->latest('updated_at')->get()->map(fn ($task) => [
            'id' => $task->id,
            'name' => $task->name,
            'slug' => $task->slug,
            'is_completed' => $task->is_completed,
            'description' => $task->description,
        ]),
        'completed' => Task::query()->where('is_completed', 1)->count(),
        'pending' => Task::query()->where('is_completed', 0)->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/tasks.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
