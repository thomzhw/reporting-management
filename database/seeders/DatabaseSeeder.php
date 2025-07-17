<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Roles
        $superuser = Role::create(['name' => 'superuser']);
        $head = Role::create(['name' => 'timhub']);
        $staff = Role::create(['name' => 'staff']);

        // ===================== PERMISSIONS =====================
        $allPermissions = [
            // Superuser permissions
            ['slug' => 'user.manage', 'description' => 'Manage user roles'],
            ['slug' => 'role.manage', 'description' => 'Manage roles'],
            ['slug' => 'permission.manage', 'description' => 'Manage permissions'],
            ['slug' => 'superuser.access', 'description' => 'Access superuser panel'],
            ['slug' => 'superuser.remotes.manage', 'description' => 'Superuser remotes manage'],
            
            // Head permissions
            ['slug' => 'timhub.access', 'description' => 'Access Timhub Dashboard'],
            ['slug' => 'timhub.performance.view', 'description' => 'View Performance Reports'],
            ['slug' => 'timhub.tasks.view', 'description' => 'View Team Tasks'],
            ['slug' => 'timhub.reporting.manage', 'description' => 'Manage Reporting Templates'],
            ['slug' => 'timhub.reporting.assign', 'description' => 'Assign Report'],
            ['slug' => 'timhub.remote.manage', 'description' => 'Manage Remotes Head'],

            // Staff permissions
            ['slug' => 'staff.access', 'description' => 'Access Staff Dashboard'],
        ];
        
        foreach ($allPermissions as $permission) {
            Permission::create($permission);
        }

        // ===================== ROLE PERMISSION ASSIGNMENT =====================
        // Assign ONLY superuser permissions to superuser role
        $superuserPermissions = [
            'user.manage',
            'role.manage',
            'permission.manage',
            'superuser.access',
            'superuser.remotes.manage'
        ];
        $superuser->permissions()->attach(
            Permission::whereIn('slug', $superuserPermissions)->pluck('id')
        );

        // Assign permission ke role head
        $headPermissions = [
            'timhub.access',
            'timhub.team.manage',
            'timhub.performance.view',
            'timhub.tasks.view',
            'timhub.reporting.manage',
            'timhub.reporting.assign',
            'timhub.remote.manage',
        ];
        $head->permissions()->attach(
            Permission::whereIn('slug', $headPermissions)->pluck('id')
        );

        // Assign permission ke role staff
        $staffPermissions = [
            'staff.access',
        ];
        $staff->permissions()->attach(
            Permission::whereIn('slug', $staffPermissions)->pluck('id')
        );

        // ===================== USERS =====================
        // Buat superuser
        User::create([
            'name' => 'Superuser',
            'email' => 'superuser@mitrakom.com',
            'password' => Hash::make('password123'),
            'role_id' => $superuser->id
        ]);

        // Buat contoh head
        User::create([
            'name' => 'Helpdesk',
            'email' => 'helpdesk@mitrakom.com',
            'password' => Hash::make('password123'),
            'role_id' => $head->id
        ]);

        // Buat contoh staff
        User::create([
            'name' => 'Staff',
            'email' => 'teknisi@mitrakom.com',
            'password' => Hash::make('password123'),
            'role_id' => $staff->id
        ]);
    }
}