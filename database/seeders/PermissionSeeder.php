<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the modules and their standard actions
        $modules = [
            'Dashboard' => ['view'],
            'Point of Sale' => ['view', 'add', 'edit', 'delete'],
            'Medicine Management' => ['view', 'add', 'edit', 'delete'],
            'Sales Management' => ['view', 'add', 'edit', 'delete'],
            'Sales Invoices' => ['view', 'add', 'edit', 'delete'],
            'Online Orders' => ['view', 'add', 'edit', 'delete'],
            'Storefront Messages' => ['view', 'add', 'edit', 'delete'],
            'Purchase Management' => ['view', 'add', 'edit', 'delete'],
            'Inventory' => ['view', 'add', 'edit', 'delete'],
            'Customers' => ['view', 'add', 'edit', 'delete'],
            'Suppliers' => ['view', 'add', 'edit', 'delete'],
            'Requisitions' => ['view', 'add', 'edit', 'delete'],
            'Purchase Orders' => ['view', 'add', 'edit', 'delete'],
            'Purchases GRN' => ['view', 'add', 'edit', 'delete'],
            'Prescriptions' => ['view', 'add', 'edit', 'delete'],
            'Reports & Analytics' => ['view', 'add', 'edit', 'delete'],
            'HR & Payroll' => ['view', 'add', 'edit', 'delete'],
            'Employees' => ['view', 'add', 'edit', 'delete'],
            'Attendance' => ['view', 'add', 'edit', 'delete'],
            'Payroll Processing' => ['view', 'add', 'edit', 'delete'],
            'Accounting' => ['view', 'add', 'edit', 'delete'],
            'Income & Expenses' => ['view', 'add', 'edit', 'delete'],
            'Chart of Accounts' => ['view', 'add', 'edit', 'delete'],
            'Expenses' => ['view', 'add', 'edit', 'delete'],
            'Settings' => ['view', 'add', 'edit', 'delete'],
            'System' => ['view', 'add', 'edit', 'delete'],
        ];

        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        foreach ($modules as $module => $actions) {
            $moduleKey = Str::slug($module);
            foreach ($actions as $action) {
                $permissionName = "{$moduleKey}.{$action}";
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }
    }
}
