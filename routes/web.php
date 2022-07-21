<?php

use App\Http\Controllers\AbstractFileController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FullPaperController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    return view('front.login');
});
Route::middleware('auth')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::get('/dashboard/abstract/{id}', [DashboardController::class, 'getAbstracts'])->name('dashboard.abstract');
    Route::resource('personal', PersonalController::class)->only(['index', 'store']);
    Route::resource('abstract', AbstractFileController::class)->except(['update']);
    Route::resource('fullpaper', FullPaperController::class)->except(['update','edit']);
    Route::resource('user', UserController::class);
    Route::resource('announcement', AnnouncementsController::class)->only(['index','destroy','store']);
    Route::get('announcement/file/{id}', [AnnouncementsController::class, 'attachment'])->name('announcement.file');
    Route::post('announcement/preview', [AnnouncementsController::class, 'preview'])->name('announcement.preview');
});

require __DIR__ . '/auth.php';
