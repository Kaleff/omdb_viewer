<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MovieController::class, 'index'])->name('home');
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/movies/{imdbID}', [MovieController::class, 'show'])->name('movies.show');
