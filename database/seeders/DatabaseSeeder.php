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
        $head = Role::create(['name' => 'head']);
        $staff = Role::create(['name' => 'staff']);

        // ===================== PERMISSIONS =====================
        $allPermissions = [
            // Superuser permissions
            ['slug' => 'user.manage', 'description' => 'Manage user roles'],
            ['slug' => 'role.manage', 'description' => 'Manage roles'],
            ['slug' => 'permission.manage', 'description' => 'Manage permissions'],
            ['slug' => 'superuser.access', 'description' => 'Access superuser panel'],
            ['slug' => 'superuser.outlet.manage', 'description' => 'Superuser outlet manage'],
            
            // Head permissions
            ['slug' => 'head.access', 'description' => 'Access Head Dashboard'],
            ['slug' => 'head.team.manage', 'description' => 'Manage Team Members'],
            ['slug' => 'head.performance.view', 'description' => 'View Performance Reports'],
            ['slug' => 'head.tasks.view', 'description' => 'View Team Tasks'],
            ['slug' => 'head.targets.manage', 'description' => 'Manage Team Targets'],
            ['slug' => 'head.qa.manage', 'description' => 'Manage QA Templates'],
            ['slug' => 'head.qa.assign', 'description' => 'Assign QA Templates'],

            // Staff permissions
            ['slug' => 'staff.access', 'description' => 'Access Staff Dashboard'], // Tambahkan ini
            ['slug' => 'staff.qa.submit', 'description' => 'Submit QA Reports'],
            
            // Tambahkan izin staff lainnya jika Anda mengimplementasikan rute staff.profile, staff.my-performance, staff.my-tasks
            ['slug' => 'staff.profile.view', 'description' => 'View own profile'],
            ['slug' => 'staff.performance.view', 'description' => 'View personal performance reports'],
            ['slug' => 'staff.tasks.view', 'description' => 'View assigned tasks'],
        ];
        
        foreach ($allPermissions as $permission) {
            Permission::create($permission);
        }

        // ===================== ROLE PERMISSION ASSIGNMENT =====================
        // Beri semua permission ke superuser
        $superuser->permissions()->attach(Permission::all()->pluck('id'));

        // Assign permission ke role head
        $headPermissions = [
            'head.access',
            'head.team.manage',
            'head.performance.view',
            'head.tasks.view',
            'head.targets.manage',
            'head.qa.manage',
            'head.qa.assign'
        ];
        $head->permissions()->attach(
            Permission::whereIn('slug', $headPermissions)->pluck('id')
        );

        // Assign permission ke role staff
        $staffPermissions = [
            'staff.access', // Tambahkan izin ini
            'staff.qa.submit',
            'staff.profile.view', // Tambahkan ini sesuai rute yang diusulkan
            'staff.performance.view', // Tambahkan ini sesuai rute yang diusulkan
            'staff.tasks.view', // Tambahkan ini sesuai rute yang diusulkan
        ];
        $staff->permissions()->attach(
            Permission::whereIn('slug', $staffPermissions)->pluck('id')
        );

        // ===================== USERS =====================
        // Buat superuser
        User::create([
            'name' => 'Super User',
            'email' => 'super@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $superuser->id
        ]);

        // Buat contoh head
        User::create([
            'name' => 'Head User',
            'email' => 'head@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $head->id
        ]);

        // Buat contoh staff
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $staff->id
        ]);
    }
}