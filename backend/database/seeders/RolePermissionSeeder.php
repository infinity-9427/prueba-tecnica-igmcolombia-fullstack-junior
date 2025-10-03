<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Invoice permissions
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'manage invoices',
            
            // Client permissions
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'manage clients',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',
            
            // File permissions
            'upload files',
            'download files',
            'delete files',
            
            // System permissions
            'access admin panel',
            'view reports',
            'export data',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Manager role - can manage invoices and clients but not users
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'manage invoices',
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'manage clients',
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'upload files',
            'download files',
            'delete files',
            'view reports',
            'export data',
        ]);

        // User role - basic access to invoices and clients
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view invoices',
            'create invoices',
            'edit invoices',
            'view clients',
            'create clients',
            'edit clients',
            'upload files',
            'download files',
        ]);

        // Accountant role - focused on invoice management and reporting
        $accountantRole = Role::create(['name' => 'accountant']);
        $accountantRole->givePermissionTo([
            'manage invoices',
            'view invoices',
            'create invoices',
            'edit invoices',
            'view clients',
            'upload files',
            'download files',
            'view reports',
            'export data',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}