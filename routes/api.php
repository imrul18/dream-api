<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\ParentalAdvisoryController;
use App\Http\Controllers\SubGenreController;
use App\Http\Controllers\SupportCenterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YoutubeRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::middleware('admin')->group(function () {
            Route::prefix('user')->group(function () {
                Route::controller(UserController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::prefix('artist')->group(function () {
                Route::controller(ArtistController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::prefix('language')->group(function () {
                Route::controller(LanguageController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::prefix('genre')->group(function () {
                Route::controller(GenreController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::prefix('sub-genre')->group(function () {
                Route::controller(SubGenreController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::prefix('label')->group(function () {
                Route::controller(LabelController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::prefix('format')->group(function () {
                Route::controller(FormatController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::prefix('parental-advisory')->group(function () {
                Route::controller(ParentalAdvisoryController::class)->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
                    Route::get('/{id}', 'show');
                    Route::post('/{id}', 'update');
                });
            });
            Route::get('audio', [AudioController::class, 'index']);
            Route::get('audio/{id}', [AudioController::class, 'show']);
            Route::post('audio/{id}', [AudioController::class, 'update']);

            Route::prefix('option')->group(function () {
                Route::controller(OptionController::class)->group(function () {
                    Route::get('user', 'user');
                    Route::get('genre', 'genre');
                });
            });

            Route::controller(SupportCenterController::class)->group(
                function () {
                    Route::get('support-center', 'index');
                    Route::get('support-center/{id}', 'show');
                    Route::post('support-center/{id}', 'update');
                    Route::post('support-message', 'sendMessageFromAdmin');
                }
            );

            Route::controller(YoutubeRequestController::class)->group(
                function () {
                    Route::get('youtube-request', 'index');
                    Route::post('youtube-request/{id}', 'update');
                }
            );
        });
    });

    Route::controller(AudioController::class)->group(function () {
        Route::post('audio', 'store');
        Route::get('audio', 'index');
    });
    Route::prefix('artist')->group(function () {
        Route::controller(ArtistController::class)->group(function () {
            Route::get('', 'userIndex');
            Route::post('', 'userStore');
            Route::post('/{id}', 'userUpdate');
        });
    });
    Route::prefix('label')->group(function () {
        Route::controller(LabelController::class)->group(function () {
            Route::get('', 'userIndex');
            Route::post('', 'userStore');
            Route::post('/{id}', 'userUpdate');
        });
    });
    Route::prefix('option')->group(function () {
        Route::controller(OptionController::class)->group(function () {
            Route::get('artist', 'artist');
            Route::get('language', 'language');
            Route::get('genre', 'genre');
            Route::get('label', 'label');
            Route::get('format', 'format');
            Route::get('parental-advisory', 'parentalAdvisory');
        });
    });
    Route::get('support-center', [SupportCenterController::class, 'index']);
    Route::get('support-center/{id}', [SupportCenterController::class, 'show']);
    Route::post('support-message', [SupportCenterController::class, 'sendMessageFromUser']);
});
