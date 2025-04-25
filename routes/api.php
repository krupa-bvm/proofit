<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\RolePermissionController;
use PragmaRX\Google2FALaravel\Support\Authenticator;

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

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
    
// });
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/upload', [CertificateController::class, 'upload']);
    Route::get('/certificates', [CertificateController::class, 'index']);

});

Route::get('/verify/{certificate_id}', [CertificateController::class, 'verifyById']);
Route::post('/verify-file', [CertificateController::class, 'verifyByFile']);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/2fa/setup', [AuthController::class, 'setup']);
    Route::post('/2fa/verify', [AuthController::class, 'verify']);
});

Route::middleware(['auth:api', '2fa.admin'])->prefix('admin')->group(function () {
    Route::middleware('role:admin')->get('/admin-data', fn() => response()->json(['data' => 'Admin only data']));

    Route::middleware('role:super_admin')->group(function () {
        Route::post('/roles', [RolePermissionController::class, 'createRole']);
        Route::post('/permissions', [RolePermissionController::class, 'createPermission']);
        Route::post('/assign-role', [RolePermissionController::class, 'assignRole']);
        Route::post('/assign-permission', [RolePermissionController::class, 'assignPermission']);
    });
});
