
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\EmployeeAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\MailLogController;
use App\Http\Controllers\Employee\BillingController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\OrderController;
use App\Services\SystemHealthService;

Route::get('/', function () {
    return redirect()->route('employee.login');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Employee Authentication
|--------------------------------------------------------------------------
*/

Route::get('/employee/login', [EmployeeAuthController::class, 'showLogin'])
    ->name('employee.login');

Route::post('/employee/login', [EmployeeAuthController::class, 'login'])
    ->name('employee.login.submit');

Route::post('/employee/logout', [EmployeeAuthController::class, 'logout'])
    ->name('employee.logout');


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('/users/{user}/status', [UserController::class, 'status'])
            ->name('users.status');

        Route::resource('employees', EmployeeController::class)->except(['show']);
        Route::patch('/employees/{employee}/status', [EmployeeController::class, 'status'])
            ->name('employees.status');

        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::patch('/customers/{customer}/status', [CustomerController::class, 'status'])
            ->name('customers.status');

        Route::resource('products', ProductController::class)
            ->except(['show']);

        Route::resource('stocks', StockController::class)
            ->except(['show']);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/download', [AdminOrderController::class, 'download'])->name('orders.download');
        Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');
        Route::post('/orders/{order}/email', [AdminOrderController::class, 'email'])->name('orders.email');

        Route::get('/mail-logs', [MailLogController::class, 'index'])
            ->name('mail-logs');

        Route::get('/health', fn (SystemHealthService $health) => response()->json($health->check()))
            ->name('health');
    });


/*
|--------------------------------------------------------------------------
| Employee
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {

        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/billing', [BillingController::class, 'create'])
            ->name('billing');

        Route::post('/billing', [BillingController::class, 'store'])
            ->name('billing.store');

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');
        Route::get('/orders/{order}/download', [OrderController::class, 'download'])
            ->name('orders.download');
        Route::get('/orders/{order}/print', [OrderController::class, 'print'])
            ->name('orders.print');
        Route::post('/orders/{order}/email', [OrderController::class, 'email'])
            ->name('orders.email');

        Route::get('/health', fn (SystemHealthService $health) => response()->json($health->check()))
            ->name('health');
    });