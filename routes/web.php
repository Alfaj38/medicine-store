<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\PublicController::class, 'home'])->name('home');
Route::get('/features', [\App\Http\Controllers\PublicController::class, 'features'])->name('features');
Route::get('/pricing', [\App\Http\Controllers\PublicController::class, 'pricing'])->name('pricing');
Route::get('/contact', [\App\Http\Controllers\PublicController::class, 'contact'])->name('contact');

Route::get('/partner', [\App\Http\Controllers\PublicController::class, 'partner'])->name('partner');
Route::post('/partner/apply', [\App\Http\Controllers\Auth\ResellerApplicationController::class, 'store'])->name('partner.apply');

Route::get('/demo-center', [\App\Http\Controllers\PublicController::class, 'demoCenter'])->name('demo-center');
Route::get('/book-demo', [\App\Http\Controllers\PublicController::class, 'bookDemo'])->name('book-demo');
Route::get('/track-order', [\App\Http\Controllers\PublicController::class, 'trackOrderPage'])->name('track.order.page');
Route::post('/track-order', [\App\Http\Controllers\PublicController::class, 'trackOrder'])->name('track.order');
Route::post('/book-demo', [\App\Http\Controllers\DemoRequestController::class, 'store'])->name('demo.store');
Route::get('/r/{code}', [\App\Http\Controllers\ReferralLinkController::class, 'redirect'])->name('referral.link');
Route::get('/api/promo-code/validate', [\App\Http\Controllers\ReferralLinkController::class, 'validateCode'])->name('promo-code.validate');
Route::get('/success', [\App\Http\Controllers\PublicController::class, 'success'])->name('success');

// Location API
Route::get('/api/locations/divisions', [\App\Http\Controllers\LocationController::class, 'divisions'])->name('locations.divisions');
Route::get('/api/locations/districts/{division_id}', [\App\Http\Controllers\LocationController::class, 'districts'])->name('locations.districts');
Route::get('/api/locations/upazilas/{district_id}', [\App\Http\Controllers\LocationController::class, 'upazilas'])->name('locations.upazilas');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('register', [App\Http\Controllers\Auth\CompanyRegistrationController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\CompanyRegistrationController::class, 'store'])->name('register.store');
});

Route::post('/reseller/logout', [\App\Http\Controllers\Auth\ResellerSessionController::class, 'destroy'])->name('reseller.logout');
Route::get('/reseller/login', [\App\Http\Controllers\Auth\ResellerSessionController::class, 'create'])->name('reseller.login');
Route::get('/reseller/pending', function () {
    return Inertia::render('Reseller/Pending');
})->name('reseller.pending');

Route::middleware(['reseller'])->prefix('reseller')->name('reseller.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Reseller\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/codes', [\App\Http\Controllers\Reseller\ReferralCodeController::class, 'index'])->name('codes.index');
    Route::post('/codes', [\App\Http\Controllers\Reseller\ReferralCodeController::class, 'store'])->name('codes.store');
    Route::patch('/codes/{id}/toggle', [\App\Http\Controllers\Reseller\ReferralCodeController::class, 'toggle'])->name('codes.toggle');
    Route::get('/companies', [\App\Http\Controllers\Reseller\ReferredCompanyController::class, 'index'])->name('companies');
    Route::get('/commissions', [\App\Http\Controllers\Reseller\CommissionController::class, 'index'])->name('commissions');
    Route::get('/wallet', [\App\Http\Controllers\Reseller\WalletController::class, 'index'])->name('wallet');
    Route::get('/withdrawals', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'store'])->name('withdrawals.store');

    // New Missing Routes
    Route::get('/payouts', [\App\Http\Controllers\Reseller\PayoutController::class, 'index'])->name('payouts');
    Route::get('/reports', [\App\Http\Controllers\Reseller\ReportController::class, 'index'])->name('reports');
    Route::get('/marketing', [\App\Http\Controllers\Reseller\MarketingController::class, 'index'])->name('marketing');
    Route::get('/marketing/brand-kit', [\App\Http\Controllers\Reseller\MarketingController::class, 'downloadBrandKit'])->name('marketing.brand-kit');
    Route::get('/marketing/social-pack', [\App\Http\Controllers\Reseller\MarketingController::class, 'downloadSocialPack'])->name('marketing.social-pack');
    Route::get('/marketing/email-templates', [\App\Http\Controllers\Reseller\MarketingController::class, 'viewEmailTemplates'])->name('marketing.email-templates');
    
    Route::get('/settings', [\App\Http\Controllers\Reseller\SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Reseller\SettingController::class, 'update'])->name('settings.update');
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

    Route::get('/email/verify', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('flash', ['success' => 'Verification link sent!']);
    })->middleware(['throttle:6,1'])->name('verification.send');


    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Management Portal
    Route::middleware(['management', 'verified'])->prefix('admin')->name('admin.')->group(function () {
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
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        
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

        // Affiliate Management
        Route::prefix('affiliate')->name('affiliate.')->group(function () {
            Route::get('/applications', [\App\Http\Controllers\Admin\AffiliateController::class, 'applications'])->name('applications');
            Route::patch('/{id}/approve', [\App\Http\Controllers\Admin\AffiliateController::class, 'approve'])->name('approve');
            Route::patch('/{id}/reject', [\App\Http\Controllers\Admin\AffiliateController::class, 'reject'])->name('reject');
            Route::get('/resellers', [\App\Http\Controllers\Admin\AffiliateController::class, 'resellers'])->name('resellers');
            Route::get('/resellers/{id}', [\App\Http\Controllers\Admin\AffiliateController::class, 'show'])->name('resellers.show');
            Route::get('/commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions');
            Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals');
            Route::patch('/withdrawals/{id}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
            Route::patch('/withdrawals/{id}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');
            Route::patch('/withdrawals/{id}/pay', [\App\Http\Controllers\Admin\WithdrawalController::class, 'pay'])->name('withdrawals.pay');
        });
    });

    // Company Portal
    Route::middleware(['company', 'verified'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        // Catalog & Inventory
        Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
        
        // Inventory Opening Stock
        Route::get('/inventory/opening-stock', [\App\Http\Controllers\OpeningStockController::class, 'index'])->name('inventory.opening-stock');
        Route::get('/inventory/opening-stock/search', [\App\Http\Controllers\OpeningStockController::class, 'search'])->name('inventory.opening-stock.search');
        Route::post('/inventory/opening-stock', [\App\Http\Controllers\OpeningStockController::class, 'store'])->name('inventory.opening-stock.store');
        
        // Purchase Returns
        Route::get('/purchase-returns/get-medicines', [App\Http\Controllers\PurchaseReturnController::class, 'getMedicines'])->name('purchase-returns.medicines');
        Route::get('/purchase-returns/get-batches', [App\Http\Controllers\PurchaseReturnController::class, 'getBatches'])->name('purchase-returns.batches');
        Route::resource('purchase-returns', App\Http\Controllers\PurchaseReturnController::class)->except(['edit', 'update', 'destroy']);

        // Requisitions
        Route::get('requisitions/search-medicines', [\App\Http\Controllers\RequisitionController::class, 'searchMedicines'])->name('requisitions.search');
        Route::get('requisitions/auto-generate', [\App\Http\Controllers\RequisitionController::class, 'autoGenerate'])->name('requisitions.auto-generate');
        Route::patch('requisitions/{requisition}/status', [\App\Http\Controllers\RequisitionController::class, 'updateStatus'])->name('requisitions.status');
        Route::resource('requisitions', \App\Http\Controllers\RequisitionController::class);
        
        // Purchase Orders
        Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);

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
        
        // Online Orders
        Route::resource('online-orders', \App\Http\Controllers\OnlineOrderController::class)->only(['index', 'show', 'update']);

        // Customers
        Route::resource('customers', \App\Http\Controllers\CustomerController::class);
        Route::post('customers/{customer}/receive-payment', [\App\Http\Controllers\CustomerController::class, 'receivePayment'])->name('customers.receivePayment');

        // Prescriptions
        Route::resource('prescriptions', \App\Http\Controllers\PrescriptionController::class)->only(['index', 'update']);

        // HR & Payroll
        Route::prefix('hr')->name('hr.')->group(function () {
            // Employees
            Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
            Route::post('/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
            Route::put('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');

            // Attendance
            Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');

            // Payroll
            Route::get('/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
            Route::post('/payroll', [\App\Http\Controllers\PayrollController::class, 'store'])->name('payroll.store');
        });

        // Accounting
        Route::prefix('accounting')->name('accounting.')->group(function () {
            // Chart of Accounts
            Route::get('/accounts', [\App\Http\Controllers\Accounting\AccountController::class, 'index'])->name('accounts.index');
            Route::post('/accounts', [\App\Http\Controllers\Accounting\AccountController::class, 'store'])->name('accounts.store');
            Route::put('/accounts/{account}', [\App\Http\Controllers\Accounting\AccountController::class, 'update'])->name('accounts.update');
            Route::delete('/accounts/{account}', [\App\Http\Controllers\Accounting\AccountController::class, 'destroy'])->name('accounts.destroy');

            // Transactions (Income & Expenses)
            Route::get('/transactions', [\App\Http\Controllers\Accounting\TransactionController::class, 'index'])->name('transactions.index');
            Route::post('/transactions', [\App\Http\Controllers\Accounting\TransactionController::class, 'store'])->name('transactions.store');
            Route::put('/transactions/{transaction}', [\App\Http\Controllers\Accounting\TransactionController::class, 'update'])->name('transactions.update');
            Route::delete('/transactions/{transaction}', [\App\Http\Controllers\Accounting\TransactionController::class, 'destroy'])->name('transactions.destroy');
        });

        // Contact Messages
        Route::patch('/contact-messages/{contact_message}/status', [\App\Http\Controllers\Company\ContactMessageController::class, 'updateStatus'])->name('contact-messages.status');
        Route::resource('contact-messages', \App\Http\Controllers\Company\ContactMessageController::class)->only(['index', 'destroy']);

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
            Route::post('subscription/upgrade', [\App\Http\Controllers\Company\SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
            
            Route::get('seo-analytics', [\App\Http\Controllers\Company\SeoAnalyticsController::class, 'index'])->name('seo-analytics.index');
            Route::post('seo-analytics', [\App\Http\Controllers\Company\SeoAnalyticsController::class, 'update'])->name('seo-analytics.update');
        });
    });
});

Route::get('/pharmacy/{company:slug}', [\App\Http\Controllers\StorefrontController::class, 'show'])->name('storefront.show');

// Storefront Placeholder Routes (For Multi-Tenant Modules)
Route::get('/pharmacy/{company:slug}/delivery/{area}', [\App\Http\Controllers\StorefrontController::class, 'deliveryArea'])->name('storefront.deliveryArea');
Route::get('/pharmacy/{company:slug}/medicines', [\App\Http\Controllers\StorefrontController::class, 'medicines'])->name('storefront.medicines');
Route::get('/pharmacy/{company:slug}/medicine/{medicine}', [\App\Http\Controllers\StorefrontController::class, 'medicineDetails'])->name('storefront.medicine.details');
Route::get('/pharmacy/{company:slug}/search', [\App\Http\Controllers\StorefrontController::class, 'medicines'])->name('storefront.search');
Route::get('/pharmacy/{company:slug}/checkout', [\App\Http\Controllers\StorefrontController::class, 'checkout'])->name('storefront.checkout');
Route::post('/pharmacy/{company:slug}/checkout', [\App\Http\Controllers\StorefrontController::class, 'placeOrder'])->name('storefront.placeOrder');
Route::get('/pharmacy/{company:slug}/order/{trackingNumber}', [\App\Http\Controllers\StorefrontController::class, 'orderSuccess'])->name('storefront.orderSuccess');

// Dynamic OG Image
Route::get('/og-image/medicine/{company:slug}/{medicineSlug}', [\App\Http\Controllers\OgImageController::class, 'medicine'])->name('og.medicine');

Route::get('/pharmacy/{company:slug}/prescription', [\App\Http\Controllers\StorefrontController::class, 'prescription'])->name('storefront.prescription');
Route::post('/pharmacy/{company:slug}/prescription', [\App\Http\Controllers\StorefrontController::class, 'uploadPrescription'])->name('storefront.prescription.upload');

Route::get('/store/{company:slug}/blog', function ($slug) {
    return Inertia::render('Storefront/Placeholder', ['title' => 'Health Blog & Wellness', 'companySlug' => $slug]);
})->name('storefront.blog');

Route::get('/store/{company:slug}/contact', [\App\Http\Controllers\StorefrontController::class, 'contact'])->name('storefront.contact');
Route::post('/store/{company:slug}/contact', [\App\Http\Controllers\StorefrontController::class, 'submitContact'])->name('storefront.contact.submit');

// Reseller / Affiliate Portal Routes
Route::prefix('reseller')->name('reseller.')->group(function () {
    Route::middleware('guest:reseller')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Auth\ResellerSessionController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Auth\ResellerSessionController::class, 'store']);
    });

    Route::post('/logout', [\App\Http\Controllers\Auth\ResellerSessionController::class, 'destroy'])->name('logout');
    
    Route::get('/pending', function () {
        return Inertia::render('Reseller/Pending');
    })->name('pending');
});

