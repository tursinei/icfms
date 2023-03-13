<?php

use App\Http\Controllers\AbstractFileController;
use App\Http\Controllers\AdminAbstractsController;
use App\Http\Controllers\AdminFullpaperController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FullPaperController;
use App\Http\Controllers\GitController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\SysinfoController;
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

if (env('APP_ENV') === 'production') {
    URL::forceSchema('https');
}

Route::get('/',  [IndexController::class,'index']);
Route::get('git-pull',[GitController::class,'pull']);
Route::middleware('auth')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::get('/dashboard/abstract/{id}', [DashboardController::class, 'getAbstracts'])->name('dashboard.abstract');
    Route::resource('personal', PersonalController::class)->only(['index', 'store']);
    Route::resource('abstract', AbstractFileController::class)->except(['update']);
    Route::resource('fullpaper', FullPaperController::class)->except(['update','edit']);
    Route::resource('user', UserController::class)->except(['edit']);
    Route::post('user/changePass', [UserController::class,'changePass'])->name('user.changepass');
    Route::get('user/download/excel', [UserController::class,'excelFile'])->name('user.excel');
    Route::resource('abstracts', AdminAbstractsController::class)->only(['index','create']);
    Route::resource('fullpapers', AdminFullpaperController::class)->only(['index','create']);
    Route::resource('announcement', AnnouncementsController::class)->only(['index','destroy','store']);
    Route::get('announcement/file/{id}', [AnnouncementsController::class, 'attachment'])->name('announcement.file');
    Route::post('announcement/preview', [AnnouncementsController::class, 'preview'])->name('announcement.preview');
    Route::resource('payment', PaymentController::class)->except(['edit','update', 'show']);
    Route::get('payment/{payment}/{action}',[PaymentController::class,'show'])->name('payment.show');
    Route::post('email/resend-verification', [EmailVerificationNotificationController::class, 'resendEmail'])
                ->name('verification.resend');
    Route::resource('setting',SysinfoController::class)->only(['index','store']);
});

require __DIR__ . '/auth.php';
