<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    // ログイン中のユーザーが自分のマイページにアクセスした時用のルーティング
    Route::get('/mycloset', [ItemController::class, 'index'])->name('item.index');
    // ログイン中のユーザーが他の人のマイページにアクセスした時用のルーティング
    Route::get('/mycloset/{user}', [ItemController::class, 'otherIndex'])->name('item.otherIndex');
    Route::post('/mycloset', [ItemController::class, 'store'])->name('item.store');
    Route::put('/mycloset/{item}', [ItemController::class, 'update'])->name('item.update');
    Route::delete('mycloset/{item}', [ItemController::class, 'delete'])->name('item.delte');
});

Route::middleware('auth')->group(function () {
    Route::post('/follow/{followed_user}', [UserController::class, 'follow'])->name('user.follow');
    Route::get('/followers/{user}', [UserController::class, 'checkFollowers'])->name('user.followers');
    Route::get('/followees/{user}', [UserController::class, 'checkFollowees'])->name('user.followees');
});

Route::middleware('auth')->group(function () {
    Route::get('/myposts', [PostController::class, 'myPosts'])->name('post.myPosts');
    Route::get('/myposts/{user}', [PostController::class, 'otherPosts'])->name('post.otherPosts');
    Route::get('/dashboard/followed', [PostController::class, 'index'])->name('post.index');
    Route::get('/dashboard', [PostController::class, 'recomend'])->name('dashboard');
    Route::post('/post/{user}', [PostController::class, 'store'])->name('post.store');
    Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
    Route::post('post/{favorited_post}/favorites', [PostController::class, 'favorites'])->name('post.favorites');
    Route::delete('post/{post}', [PostController::class, 'delete'])->name('post.delte');
});

require __DIR__.'/auth.php';
