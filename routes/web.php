<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SampleRequestController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AssignmentController;

// OPTIONAL: enforce that {id} parameters are numeric to avoid "codification" being matched by {id}
Route::pattern('id', '[0-9]+');

Route::get('/', [PublicController::class, 'landing'])->name('public.landing');

// Public routes (no authentication required)
Route::prefix('public')
    ->name('public.')
    ->group(function () {
        Route::get('/sample-request', [PublicController::class, 'sampleRequest'])->name('sample-request');
        Route::post('/sample-request', [PublicController::class, 'submitRequest'])->name('submit-request');
        Route::get('/request-success/{code}', [PublicController::class, 'requestSuccess'])->name('request-success');
        Route::get('/tracking', [PublicController::class, 'tracking'])->name('tracking');
        Route::post('/track-sample', [PublicController::class, 'trackSample'])->name('track-sample');
        Route::get('/feedback/{code}', [PublicController::class, 'feedback'])->name('feedback');
    });

// Authentication routes (separate config file)
require __DIR__ . '/../config/auth.php';

// Authentication routes - Manual implementation (guest / auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Protected routes - All require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Sample Request routes - Permission check in controller
    Route::prefix('sample-requests')
        ->name('sample-requests.')
        ->group(function () {
            Route::get('/', [SampleRequestController::class, 'index'])->name('index');
            Route::get('/create', [SampleRequestController::class, 'create'])->name('create');
            Route::post('/', [SampleRequestController::class, 'store'])->name('store');
            Route::get('/{id}', [SampleRequestController::class, 'show'])->whereNumber('id')->name('show');
            Route::get('/{id}/edit', [SampleRequestController::class, 'edit'])->whereNumber('id')->name('edit');
            Route::put('/{id}', [SampleRequestController::class, 'update'])->whereNumber('id')->name('update');
            Route::delete('/{id}', [SampleRequestController::class, 'destroy'])->whereNumber('id')->name('destroy');
            Route::post('/{id}/approve', [SampleRequestController::class, 'approve'])->whereNumber('id')->name('approve');
            Route::post('/{id}/archive', [SampleRequestController::class, 'archive'])->whereNumber('id')->name('archive');
            Route::patch('/{id}/status', [SampleRequestController::class, 'updateStatus'])->whereNumber('id')->name('update-status');
        });

    // Sample Management routes - static routes placed BEFORE parameter routes
//     Route::prefix('samples')
//         ->name('samples.')
//         ->group(function () {
//             // Index
//             Route::get('/',[SampleController::class, 'index'])->name('index');

//             // Static / special routes (must be BEFORE {id} routes)
//             Route::get('/codification', [SampleController::class, 'codificationIndex'])->name('codification.index');

//     // Codification subgroup (clear naming and numeric constraints)
//     Route::prefix('codification')
//         ->name('codification.')
//         ->group(function () {
//             Route::get('/', [SampleController::class, 'codificationIndex'])->name('index');
//             Route::get('/{id}', [SampleController::class, 'showCodification'])->whereNumber('id')->name('show');
//             Route::get('/report', [SampleController::class, 'showReport'])->whereNumber('id')->name('report');
//             Route::post('/codify', [SampleController::class, 'processCodification'])->whereNumber('id')->name('process');
//         });

//     // Routes that use {id} (constrained to numbers)
//     Route::get('/{id}', [SampleController::class, 'show'])->whereNumber('id')->name('show');
//     Route::get('/{id}/preview', [SampleController::class, 'preview'])->whereNumber('id')->name('preview');
//     Route::get('/{id}/edit', [SampleController::class, 'edit'])->whereNumber('id')->name('edit');
//     Route::put('/{id}', [SampleController::class, 'update'])->whereNumber('id')->name('update');
//     Route::post('/{id}/approve', [SampleController::class, 'approve'])->whereNumber('id')->name('approve');
//     Route::post('/{id}/reject', [SampleController::class, 'reject'])->whereNumber('id')->name('reject');
//     Route::post('/{id}/archive', [SampleController::class, 'archive'])->whereNumber('id')->name('archive');
//     Route::post('/{id}/codify', [SampleController::class, 'codify'])->whereNumber('id')->name('codify');
//     Route::get('/{id}/print-form', [SampleController::class, 'printForm'])->whereNumber('id')->name('print-form');
// });

// Sample Management routes - static routes BEFORE parameter routes
Route::prefix('samples')
    ->name('samples.')
    ->group(function () {
        // Index
        Route::get('/', [SampleController::class, 'index'])->name('index');

        // Codification overview
        Route::get('/codification', [SampleController::class, 'codificationIndex'])->name('codification.index');

        // Codification subgroup (use {id} where needed)
        Route::prefix('codification')
            ->name('codification.')
            ->group(function () {
                // /samples/codification/                 -> index
                Route::get('/', [SampleController::class, 'codificationIndex'])->name('index');

                // /samples/codification/{id}              -> show specific codification
                Route::get('/{id}', [SampleController::class, 'showCodification'])
                    ->whereNumber('id')
                    ->name('show');

                // /samples/codification/{id}/report       -> report for a specific codification
                Route::get('/{id}/report', [SampleController::class, 'showReport'])
                    ->whereNumber('id')
                    ->name('report');

                // /samples/codification/{id}/process      -> process codification (POST)
                Route::post('/{id}/process', [SampleController::class, 'processCodification'])
                    ->whereNumber('id')
                    ->name('process');
            });

        // Routes that use {id} (constrained to numbers) - these are sample-level routes
        Route::get('/{id}', [SampleController::class, 'show'])->whereNumber('id')->name('show');
        Route::get('/{id}/preview', [SampleController::class, 'preview'])->whereNumber('id')->name('preview');
        Route::get('/{id}/edit', [SampleController::class, 'edit'])->whereNumber('id')->name('edit');
        Route::put('/{id}', [SampleController::class, 'update'])->whereNumber('id')->name('update');
        Route::post('/{id}/approve', [SampleController::class, 'approve'])->whereNumber('id')->name('approve');
        Route::post('/{id}/reject', [SampleController::class, 'reject'])->whereNumber('id')->name('reject');
        Route::post('/{id}/archive', [SampleController::class, 'archive'])->whereNumber('id')->name('archive');

        // if you want a "codify" action directly under sample, make sure the controller method exists
        // either change this to call the existing method name, or implement SampleController::codify()
        Route::post('/{id}/codify', [SampleController::class, 'processCodification'])->whereNumber('id')->name('codify');

        Route::get('/{id}/print-form', [SampleController::class, 'printForm'])->whereNumber('id')->name('print-form');
        Route::get('/{id}/verification-form', [SampleController::class, 'printVerificationForm'])->whereNumber('id')->name('verification-form');
    });


    // Parameter Management routes - Permission check in controller
    Route::prefix('parameters')
        ->name('parameters.')
        ->group(function () {
            Route::get('/', [ParameterController::class, 'index'])->name('index');
            Route::get('/create', [ParameterController::class, 'create'])->name('create');
            Route::post('/', [ParameterController::class, 'store'])->name('store');
            Route::get('/{id}', [ParameterController::class, 'show'])->whereNumber('id')->name('show');
            Route::get('/{id}/edit', [ParameterController::class, 'edit'])->whereNumber('id')->name('edit');
            Route::put('/{id}', [ParameterController::class, 'update'])->whereNumber('id')->name('update');
            Route::delete('/{id}', [ParameterController::class, 'destroy'])->whereNumber('id')->name('destroy');

            Route::get('/sample-types', [ParameterController::class, 'sampleTypes'])->name('sample-types');
            Route::post('/sample-types', [ParameterController::class, 'storeSampleType'])->name('sample-types.store');
        });

    // API routes
    Route::prefix('api')
        ->name('api.')
        ->group(function () {
            Route::get('/parameters/sample-type/{id}', [ParameterController::class, 'getParametersBySampleType'])->whereNumber('id')->name('parameters.by-sample-type');
        });

    // Placeholder routes for other modules with permission checks
    Route::get('/assignments', [AssignmentController::class, 'index'])
    ->name('assignments.index');

    Route::post('/assignments/{sample}', [AssignmentController::class, 'assign'])
    ->whereNumber('sample')
    ->name('assignments.assign');

    // Route::get('/testing', function () {
    //     if (!Auth::user()->hasPermission(4)) {
    //         abort(403, 'Unauthorized access to Testing module');
    //     }
    //     return view('placeholder', ['module' => 'Testing', 'moduleId' => 4]);
    // })->name('testing.index');

    Route::get('/testing', [TestingController::class, 'index'])
    ->name('testing.index');

    Route::get('/reviews', function () {
        if (!Auth::user()->hasPermission(5)) {
            abort(403, 'Unauthorized access to Review module');
        }
        return view('placeholder', ['module' => 'Review', 'moduleId' => 5]);
    })->name('reviews.index');

    Route::get('/certificates', function () {
        if (!Auth::user()->hasPermission(6)) {
            abort(403, 'Unauthorized access to Certificates module');
        }
        return view('placeholder', ['module' => 'Certificates', 'moduleId' => 6]);
    })->name('certificates.index');

    Route::get('/invoices', function () {
        if (!Auth::user()->hasPermission(7)) {
            abort(403, 'Unauthorized access to Invoice module');
        }
        return view('placeholder', ['module' => 'Invoice', 'moduleId' => 7]);
    })->name('invoices.index');

    Route::get('/users', function () {
        if (!Auth::user()->hasPermission(9)) {
            abort(403, 'Unauthorized access to User Management module');
        }
        return view('placeholder', ['module' => 'User Management', 'moduleId' => 9]);
    })->name('users.index');

    Route::get('/settings', function () {
        if (!Auth::user()->hasPermission(10)) {
            abort(403, 'Unauthorized access to System Settings module');
        }
        return view('placeholder', ['module' => 'System Settings', 'moduleId' => 10]);
    })->name('settings.index');
});
