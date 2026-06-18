<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('register', [App\Http\Controllers\Auth\CompanyRegistrationController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\CompanyRegistrationController::class, 'store'])->name('register.store');
});

Route::middleware(['auth', \App\Http\Middleware\TenantMiddleware::class])->group(function () {
    Route::get('/', function () {
        if (auth()->user()->isManagement()) {
            return redirect('/admin/dashboard');
        }
        return redirect('/dashboard');
    });

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Management Portal
    Route::middleware(['management'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Companies
        Route::get('companies', [\App\Http\Controllers\Admin\CompanyController::class, 'index'])->name('companies.index');
        Route::get('companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'show'])->name('companies.show');
        Route::post('companies/{company}/approve', [\App\Http\Controllers\Admin\CompanyController::class, 'approve'])->name('companies.approve');
        Route::post('companies/{company}/suspend', [\App\Http\Controllers\Admin\CompanyController::class, 'suspend'])->name('companies.suspend');

        // Branch Approvals
        Route::get('branch-approvals', [\App\Http\Controllers\Admin\BranchApprovalController::class, 'index'])->name('branch-approvals.index');
        Route::post('branch-approvals/{branch}/approve', [\App\Http\Controllers\Admin\BranchApprovalController::class, 'approve'])->name('branch-approvals.approve');
        Route::post('branch-approvals/{branch}/reject', [\App\Http\Controllers\Admin\BranchApprovalController::class, 'reject'])->name('branch-approvals.reject');

        // Subscriptions
        Route::get('subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
        
        // Users
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        
        // Support Tickets
        Route::get('support-tickets', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('support-tickets.index');
        Route::post('support-tickets/{ticket}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('support-tickets.reply');
        
        // Feature Flags & Settings
        Route::get('feature-flags', [\App\Http\Controllers\Admin\FeatureFlagController::class, 'index'])->name('feature-flags.index');
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    });

    // Company Portal
    Route::middleware(['company'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('medicines', \App\Http\Controllers\MedicineController::class);
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
        
        // Purchase Returns
        Route::get('/purchase-returns/get-medicines', [App\Http\Controllers\PurchaseReturnController::class, 'getMedicines'])->name('purchase-returns.medicines');
        Route::get('/purchase-returns/get-batches', [App\Http\Controllers\PurchaseReturnController::class, 'getBatches'])->name('purchase-returns.batches');
        Route::resource('purchase-returns', App\Http\Controllers\PurchaseReturnController::class)->except(['edit', 'update', 'destroy']);

        // Purchases
        Route::get('/purchases/search-medicines', [App\Http\Controllers\PurchaseController::class, 'searchMedicines'])->name('purchases.search');
        Route::get('/purchases/suppliers/{supplier}/pending-credit-notes', [App\Http\Controllers\PaymentController::class, 'pendingCreditNotes'])->name('purchases.pending-credit-notes');
        Route::post('/purchases/{purchase}/pay', [\App\Http\Controllers\PaymentController::class, 'storeSupplierPayment'])->name('purchases.pay');
        Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
        
        // POS
        Route::get('pos/search', [\App\Http\Controllers\PosController::class, 'searchMedicines'])->name('pos.search');
        Route::get('pos/browse', [\App\Http\Controllers\PosController::class, 'browseMedicines'])->name('pos.browse');
        Route::post('pos/quick-customer', [\App\Http\Controllers\PosController::class, 'quickCustomer'])->name('pos.quick-customer');
        Route::resource('pos', \App\Http\Controllers\PosController::class)->only(['index', 'store']);
        
        // Sales
        Route::resource('sales', \App\Http\Controllers\SaleController::class)->only(['index', 'show']);
        Route::get('/sale-returns/create', [\App\Http\Controllers\SaleReturnController::class, 'create'])->name('sale-returns.create');
        Route::post('/sale-returns', [\App\Http\Controllers\SaleReturnController::class, 'store'])->name('sale-returns.store');
        
        // Customers
        Route::resource('customers', \App\Http\Controllers\CustomerController::class);
        Route::post('customers/{customer}/receive-payment', [\App\Http\Controllers\CustomerController::class, 'receivePayment'])->name('customers.receivePayment');

        // Reports
        Route::get('reports/expiry', [\App\Http\Controllers\ReportController::class, 'expiry'])->name('reports.expiry');
        Route::get('reports/low-stock', [\App\Http\Controllers\ReportController::class, 'lowStock'])->name('reports.lowStock');
        
        // Company Admin Panel
        Route::prefix('company')->name('company.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Company\DashboardController::class, 'index'])->name('dashboard');
            
            Route::resource('areas', \App\Http\Controllers\Company\AreaController::class)->except(['create', 'edit']);
            Route::post('areas/{area}/branches', [\App\Http\Controllers\Company\AreaController::class, 'assignBranches'])->name('areas.assign-branches');
            
            Route::resource('users', \App\Http\Controllers\Company\UserController::class);
            Route::resource('roles', \App\Http\Controllers\Company\RoleController::class);
            
            Route::get('permissions', [\App\Http\Controllers\Company\PermissionController::class, 'index'])->name('permissions.index');
            Route::post('permissions', [\App\Http\Controllers\Company\PermissionController::class, 'store'])->name('permissions.store');
            
            Route::resource('branches', \App\Http\Controllers\Company\BranchController::class);
            
            Route::get('settings', [\App\Http\Controllers\Company\SettingController::class, 'index'])->name('settings.index');
            Route::post('settings', [\App\Http\Controllers\Company\SettingController::class, 'update'])->name('settings.update');
            
            Route::get('subscription', [\App\Http\Controllers\Company\SubscriptionController::class, 'index'])->name('subscription.index');
        });
    });
});
