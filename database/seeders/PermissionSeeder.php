<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'admin' => 'Administrator',
            'company_admin' => 'Company Admin',
            'employee' => 'Employee',
        ];

        foreach ($roles as $role => $display_name) {
            Role::updateOrCreate(
                ['name' => $role],
                ['display_name' => $display_name]
            );
        }

        $permissionsArray = [
            // users
            'user' => [
                'view users',
                'create users',
                'edit users',
                'delete users',
				'login as other user',
            ],
        ];

        $admin = Role::updateOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator']
        );

        foreach ($permissionsArray as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate([
                    'group' => $group,
                    'name' => $permission,
                ], []);
                $admin->givePermissionTo($permission);
            }
        }
    }
}
