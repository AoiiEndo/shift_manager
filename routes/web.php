<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Enums\Authority;

Route::get('/', [LoginController::class, 'showLoginForm']);
Route::post('/', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // 管理者
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.store');

    // 契約者
    Route::get('/contractor/index', [ContractorController::class, 'index'])->name('contractor.index');
    Route::get('/cotractor/create', [ContractorController::class, 'create'])->name('contractor.create');
    Route::post('/cotractor/store', [ContractorController::class, 'store'])->name('contractor.store');
    Route::delete('/contractor/{employee}', [ContractorController::class, 'destroy'])->name('contractor.destroy');
    Route::get('/availabilities/{date}', [ShiftController::class, 'getAvailabilitiesByDate']);
    Route::post('/shifts/store', [ShiftController::class, 'store'])->name('shifts.store');

    // 被雇用者
    Route::get('/employee/index', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee/store', [EmployeeController::class, 'store'])->name('employee.store');
    Route::post('/employee/delete', [EmployeeController::class, 'delete'])->name('employee.delete');
    Route::post('/employee/update', [EmployeeController::class, 'update'])->name('employee.update');
    Route::post('/employee/checkShift', [EmployeeController::class, 'checkShift'])->name('employee.checkShift');


    // // シフト保存
    // Route::post('/save-shift', [ShiftController::class, 'store'])->name('saveShift');
});