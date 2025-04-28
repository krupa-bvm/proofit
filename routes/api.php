<?php

use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\superAdmin\userController as superAdminUserController;
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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [UserController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('/2fa/setup', [AuthController::class, 'setup']);
    Route::post('/2fa/verify', [AuthController::class, 'verify']);
});

Route::middleware(['auth:api', '2fa.admin'])->prefix('admin')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/certificates', [AdminCertificateController::class, 'index']);
        Route::get('/certificates/search', [AdminCertificateController::class, 'search']);
        Route::get('/certificates/{certificate_id}', [AdminCertificateController::class, 'show']);
        Route::post('/certificates/{certificate_id}/update-status', [AdminCertificateController::class, 'updateStatus']);
        Route::get('/audit-report', [AdminCertificateController::class, 'downloadReport']);
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::post('/roles', [RolePermissionController::class, 'createRole']);
        Route::post('/permissions', [RolePermissionController::class, 'createPermission']);
        Route::post('/assign-role', [RolePermissionController::class, 'assignRole']);
        Route::post('/assign-permission', [RolePermissionController::class, 'assignPermission']);
        Route::get('/show-user-role', [RolePermissionController::class, 'getAllUsersWithRoles']);
        Route::get('/', [RolePermissionController::class, '']);

        Route::get('/users', [superAdminUserController::class, 'index']);
        Route::post('/users/{user_id}/ban', [superAdminUserController::class, 'ban']);
        Route::post('/users/{user_id}/reset-password', [superAdminUserController::class, 'resetPassword']);
    });
});
