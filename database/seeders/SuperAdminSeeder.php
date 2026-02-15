<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Super Admin role exists (it should be created by RoleSeeder, but fail-safe)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Check if Super Admin user exists
        $superAdmin = User::whereHas('roles', function ($query) {
            $query->where('name', 'Super Admin');
        })->first();

        if (! $superAdmin) {
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@invenbill.online',
                'password' => Hash::make('Invenbill@2026'),
                'country_code' => '91',
                'mobile' => '9079436383',
                'gender' => 'male',
            ]);

            $superAdmin->assignRole($superAdminRole);
            $this->command->info('Super Admin created successfully.');
        } else {
            $this->command->info('Super Admin already exists.');
        }
    }
}
