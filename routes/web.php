<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Middleware\VerifyCsrfToken;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/broadcast-order', function () {
    $user = auth()->user() ?? User::find(2);
    broadcast(new \App\Events\OrderDispatched($user));

    return 'order event broadcasted';
});


Route::get('/broadcast-chat', function () {
    $user = auth()->user() ?? User::find(1);
    broadcast(new \App\Events\Example($user, Message::find(1)));

    return 'event broadcasted';
});

// Route::get('/broadcast', function () {
//     broadcast(new \App\Events\Example());

//     return 'event broadcasted';
// });


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
