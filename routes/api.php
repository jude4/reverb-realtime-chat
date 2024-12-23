<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\BroadcastController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', function (Request $request) {
    return User::whereNot('id', $request->user()->id)->get();
})->middleware('auth:sanctum');

Route::get('/users/{user}', function (User $user) {
    return $user;
})->middleware('auth:sanctum');

// Register route
Route::post('/register', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => ['required', 'confirmed', Password::defaults()],
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], 422);
    }

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->save();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['token' => $token, 'message' => 'User registered successfully'], 201);
});

// Login route
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = Auth::user();
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json(['message' => 'Login successful', 'token' => $token, 'status' => true]);
    }

    return response()->json(['message' => 'Invalid credentials', 'status' => false], 401);
});


