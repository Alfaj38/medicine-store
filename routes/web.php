<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\PublicController::class, 'home'])->name('home');
Route::get('/features', [\App\Http\Controllers\PublicController::class, 'features'])->name('features');
Route::get('/pricing', [\App\Http\Controllers\PublicController::class, 'pricing'])->name('pricing');
Route::get('/contact', [\App\Http\Controllers\PublicController::class, 'contact'])->name('contact');

Route::get('/demo-center', [\App\Http\Controllers\PublicController::class, 'demoCenter'])->name('demo-center');
Route::get('/book-demo', [\App\Http\Controllers\PublicController::class, 'bookDemo'])->name('book-demo');
Route::post('/book-demo', [\App\Http\Controllers\DemoRequestController::class, 'store'])->name('demo.store');
Route::get('/success', [\App\Http\Controllers\PublicController::class, 'success'])->name('success');
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('register', [App\Http\Controllers\Auth\CompanyRegistrationController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\CompanyRegistrationController::class, 'store'])->name('register.store');
});

Route::middleware(['auth', \App\Http\Middleware\TenantMiddleware::class])->group(function () {
    // We already have /dashboard defined in the groups below, but let's add a redirect for /app or similar if needed.
    // The home route is now public. User will go to /dashboard after login.
    Route::get('/app', function () {
        if (auth()->user()->isManagement()) {
            return redirect('/admin/dashboard');
        }
        return redirect('/dashboard');
    });

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Management Portal
    Route::middleware(['management'])->prefix('admin')->name('admin.')->group(function () {
        // Demo Requests Management
        Route::get('demo-requests', [\App\Http\Controllers\Admin\DemoRequestController::class, 'index'])->name('demo-requests.index');
        Route::get('demo-requests/{demoRequest}', [\App\Http\Controllers\Admin\DemoRequestController::class, 'show'])->name('demo-requests.show');
        Route::patch('demo-requests/{demoRequest}', [\App\Http\Controllers\Admin\DemoRequestController::class, 'update'])->name('demo-requests.update');
        Route::delete('demo-requests/{demoRequest}', [\App\Http\Controllers\Admin\DemoRequestController::class, 'destroy'])->name('demo-requests.destroy');
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Companies
        Route::get('companies', [\App\Http\Controllers\Admin\CompanyController::class, 'index'])->name('companies.index');
        Route::get('companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'show'])->name('companies.show');
        Route::post('companies/{company}/approve', [\App\Http\Controllers\Admin\CompanyController::class, 'approve'])->name('companies.approve');
        Route::post('companies/{company}/suspend', [\App\Http\Controllers\Admin\CompanyController::class, 'suspend'])->name('companies.suspend');
        Route::post('companies/{company}/impersonate', [\App\Http\Controllers\Admin\CompanyController::class, 'impersonate'])->name('companies.impersonate');

        // Branch Approvals
        Route::get('branch-approvals', [\App\Http\Controllers\Admin\BranchApprovalController::class, 'index'])->name('branch-approvals.index');
        Route::post('branch-approvals/{branch}/approve', [\App\Http\Controllers\Admin\BranchApprovalController::class, 'approve'])->name('branch-approvals.approve');
        Route::post('branch-approvals/{branch}/reject', [\App\Http\Controllers\Admin\BranchApprovalController::class, 'reject'])->name('branch-approvals.reject');

        // Subscriptions & Billing
        Route::get('subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('payments', [\App\Http\Controllers\Admin\SubscriptionPaymentController::class, 'index'])->name('payments.index');
        Route::get('coupons', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
        
        // Users
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        
        // Support Tickets
        Route::get('support-tickets', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('support-tickets.index');
        Route::post('support-tickets/{ticket}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('support-tickets.reply');
        
        // Feature Flags & Settings & Monitoring & Security & Analytics
        Route::get('feature-flags', [\App\Http\Controllers\Admin\FeatureFlagController::class, 'index'])->name('feature-flags.index');
        Route::get('monitoring', [\App\Http\Controllers\Admin\MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('security/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('security.audit-logs');
        Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

        // CRM
        Route::get('crm/leads', [\App\Http\Controllers\Admin\Crm\LeadController::class, 'index'])->name('crm.leads.index');
        Route::post('crm/leads/{lead}/status', [\App\Http\Controllers\Admin\Crm\LeadController::class, 'updateStatus'])->name('crm.leads.update-status');
        Route::post('crm/leads/{lead}/note', [\App\Http\Controllers\Admin\Crm\LeadController::class, 'storeNote'])->name('crm.leads.store-note');

        // Placeholder Routes for unbuilt modules
        Route::get('trial-accounts', fn() => inertia('Admin/Placeholder', ['title' => 'Trial Accounts']))->name('trial-accounts.index');
        Route::get('packages', [\App\Http\Controllers\Admin\PackageController::class, 'index'])->name('packages.index');
        Route::post('packages/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'update'])->name('packages.update');
        Route::get('crm/trial-funnel', fn() => inertia('Admin/Placeholder', ['title' => 'Trial Funnel']))->name('crm.trial-funnel.index');
        Route::get('users/roles', fn() => inertia('Admin/Placeholder', ['title' => 'User Roles & Permissions']))->name('roles.index');
        Route::get('users/login-history', fn() => inertia('Admin/Placeholder', ['title' => 'Login History']))->name('login-history.index');
        Route::get('support/announcements', fn() => inertia('Admin/Placeholder', ['title' => 'Global Announcements']))->name('announcements.index');
        Route::get('security/failed-logins', fn() => inertia('Admin/Placeholder', ['title' => 'Failed Logins Monitor']))->name('security.failed-logins');
        Route::get('monitoring/system-health', fn() => inertia('Admin/Placeholder', ['title' => 'Advanced System Health']))->name('monitoring.system-health');
        Route::get('monitoring/queue-monitor', fn() => inertia('Admin/Placeholder', ['title' => 'Queue Operations Monitor']))->name('monitoring.queue-monitor');
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('settings/email', [\App\Http\Controllers\Admin\SettingController::class, 'email'])->name('settings.email');
        Route::get('settings/sms', [\App\Http\Controllers\Admin\SettingController::class, 'sms'])->name('settings.sms');
        Route::get('settings/payment-gateway', [\App\Http\Controllers\Admin\SettingController::class, 'paymentGateway'])->name('settings.payment-gateway');
        Route::resource('banks', \App\Http\Controllers\Admin\BankController::class)->except(['create', 'show', 'edit']);
        Route::resource('bank-branches', \App\Http\Controllers\Admin\BankBranchController::class)->only(['store', 'update', 'destroy']);
    });

    // Company Portal
    Route::middleware(['company'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('items', \App\Http\Controllers\ItemController::class);
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
            Route::get('settings/export', [\App\Http\Controllers\Company\SettingController::class, 'export'])->name('settings.export');

            Route::resource('bank-accounts', \App\Http\Controllers\Company\BankAccountController::class)->only(['store', 'update', 'destroy']);
            Route::resource('documents', \App\Http\Controllers\Company\DocumentController::class)->only(['store', 'destroy']);
            
            Route::get('subscription', [\App\Http\Controllers\Company\SubscriptionController::class, 'index'])->name('subscription.index');
        });
    });
});

Route::get('/store/{company:slug}', [\App\Http\Controllers\StorefrontController::class, 'show'])->name('storefront.show');
