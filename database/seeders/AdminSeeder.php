<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            [
                'email' => config('app.admin_email'),
                'name' => config('app.admin_name'),
                'password' => Hash::make(config('app.admin_email')),
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole);
        }
    }
}
