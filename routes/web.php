<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Head\AssignTemplateController;
use App\Http\Controllers\Head\HeadOutletController;
use App\Http\Controllers\Head\HeadReportController;
use App\Http\Controllers\Head\QaTemplateController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Staff\QaReportController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SuperuserController;
use App\Http\Controllers\UserProfileController;

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


// Authentication Routes
Route::get('/signin', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/signin', [LoginController::class, 'login'])->name('loginProcess');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (Semua yang butuh auth di sini)
Route::middleware('auth')->group(function () {
    // Profile Routes - place these inside your middleware('auth') group
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [UserProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [UserProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/update-password', [UserProfileController::class, 'updatePassword'])->name('profile.update-password');
    });

    // Superuser Routes
    Route::prefix('superuser')->group(function () {
        Route::get('/dashboard', [SuperuserController::class, 'dashboard'])
            ->middleware('permission:superuser.access')->name('superuser.dashboard');
        
        //users
        Route::prefix('users')->middleware('permission:user.manage')->group(function () {
            Route::get('/users', [SuperuserController::class, 'manageUsers'])->name('users.manage');
            Route::put('/users/{user}/role', [SuperuserController::class, 'updateUserRole'])->name('users.update.role');
            Route::get('/users/{user}/edit', [SuperuserController::class, 'editUser'])->name('users.edit');
            Route::put('/users/{user}', [SuperuserController::class, 'updateUser'])->name('users.update');
            Route::delete('/users/{user}', [SuperuserController::class, 'destroyUser'])->name('users.destroy');
            Route::put('/users/{userId}/restore', [SuperuserController::class, 'restoreUser'])->name('users.restore');
            Route::get('/users/create', [SuperuserController::class, 'createUserForm'])->name('users.create');
            Route::post('/users', [SuperuserController::class, 'storeUser'])->name('users.store');
        });
            
        // roles
        Route::prefix('roles')->middleware('permission:role.manage')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('roles.index');
            Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
            Route::get('/{role}/permissions', [PermissionController::class, 'assignRolePermissions']) ->name('roles.permissions');
            Route::put('/{role}/permissions', [PermissionController::class, 'updateRolePermissions']);
        });
            
        // permissions
        Route::prefix('permissions')->middleware('permission:permission.manage')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create');
            Route::post('/', [PermissionController::class, 'store'])->name('permissions.store');
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        });

        // Remote Management
        Route::prefix('remotes')->middleware('permission:superuser.remotes.manage')->group(function () {
            Route::get('/', [OutletController::class, 'index'])->name('outlets.index');
            Route::get('/create', [OutletController::class, 'create'])->name('outlets.create');
            Route::post('/store', [OutletController::class, 'store'])->name('outlets.store');
            Route::get('/{outlet}', [OutletController::class, 'show'])->name('outlets.show');
            Route::get('/{outlet}/edit', [OutletController::class, 'edit'])->name('outlets.edit');
            Route::put('/{outlet}', [OutletController::class, 'update'])->name('outlets.update');
            Route::delete('/{outlet}', [OutletController::class, 'destroy'])->name('outlets.destroy');
        
            // Head management for outlets
            Route::get('/{outlet}/assign-heads', [OutletController::class, 'assignHeads'])->name('outlets.assign-heads');
            Route::post('/{outlet}/update-heads', [OutletController::class, 'updateHeads'])->name('outlets.update-heads');
        });
    });

    // Group route untuk head
    Route::prefix('timhub')->middleware('permission:timhub.access')->group(function () {
        // Dashboard
        Route::get('/dashboard', [HeadController::class, 'dashboard'])->name('head.dashboard');

        Route::prefix('report-templates')->middleware('permission:timhub.reporting.manage')->group(function () {
            Route::get('/', [QaTemplateController::class, 'index'])->name('head.qa-templates');
            Route::get('/create', [QaTemplateController::class, 'create'])->name('head.qa-templates.create');
            Route::post('/', [QaTemplateController::class, 'store'])->name('head.qa-templates.store');
            Route::get('/{template}/edit', [QaTemplateController::class, 'edit'])->name('head.qa-templates.edit');
            Route::put('/{template}', [QaTemplateController::class, 'update'])->name('head.qa-templates.update');
            Route::delete('/{template}', [QaTemplateController::class, 'destroy'])->name('head.qa-templates.destroy');
        });

        Route::prefix('assignments')->middleware('permission:timhub.reporting.assign')->group(function () {
            Route::get('/', [AssignTemplateController::class, 'index'])->name('head.assignments.index');
            Route::get('/create', [AssignTemplateController::class, 'create'])->name('head.assignments.create');
            Route::post('/store', [AssignTemplateController::class, 'store'])->name('head.assignments.store');
            Route::get('/{assignment}', [AssignTemplateController::class, 'show'])->name('head.assignments.show');
            Route::delete('/{assignment}', [AssignTemplateController::class, 'destroy'])->name('head.assignments.destroy');
            Route::get('/{assignment}/export-pdf', [AssignTemplateController::class, 'exportPdf'])->name('head.assignments.export-pdf');
        });

        Route::prefix('remotes')->middleware('permission:timhub.remote.manage')->group(function () {
            Route::get('/', [HeadOutletController::class, 'index'])->name('head.outlets.index');
            Route::get('/{outlet}', [HeadOutletController::class, 'show'])->name('head.outlets.show');
            Route::get('/{outlet}/assign-staff', [HeadOutletController::class, 'assignStaff'])->name('head.outlets.assign-staff');
            Route::post('/{outlet}/staff', [HeadOutletController::class, 'updateStaffAssignments'])->name('head.outlets.update-staff');
        });

        Route::prefix('reports')->group(function () {
            Route::get('/', [HeadReportController::class, 'index'])->name('head.reports.index');
            Route::get('/{report}', [HeadReportController::class, 'show'])->name('head.reports.show');
            Route::post('/{report}/review', [HeadReportController::class, 'review'])->name('head.reports.review');
        });

        // AJAX route
        Route::post('/get-outlet-data', [AssignTemplateController::class, 'getOutletData'])->name('head.get-outlet-data');

    });

    // Staff Routes
    Route::prefix('staff')->middleware(['permission:staff.access'])->group(function () {
        // Dashboard Staff Umum
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
        
        // QA Reporting
        Route::prefix('reports')->group(function () {
            Route::get('/', [QaReportController::class, 'index'])->name('staff.qa-reports.index');
            Route::get('/create/{assignment}', [QaReportController::class, 'create'])->name('staff.qa-reports.create');
            Route::post('/{assignment}', [QaReportController::class, 'store'])->name('staff.qa-reports.store');
            Route::get('/{report}', [QaReportController::class, 'show'])->name('staff.qa-reports.show');
        });
    });

});