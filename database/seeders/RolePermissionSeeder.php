<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superadmin = Role::create(['name' => 'superadmin']);
        $admin = Role::create(['name' => 'admin']);
        $customer = Role::create(['name' => 'customer']);

        // Create permissions
        $manageUsers = Permission::create(['name' => 'manage-users']);
        $manageBookings  = Permission::create(['name' => 'manage-bookings']);

        // Assign permissions to roles
        $superadmin->permissions()->attach([$manageUsers->id, $manageBookings->id]);
        $admin->permissions()->attach([$manageBookings->id]);

        // Assign roles to users
        $user = User::find(1); 
        $user->roles()->attach($superadmin);

    }
}
