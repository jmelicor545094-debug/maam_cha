<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');
        if ($adminEmail && $adminPassword) {
            User::firstOrCreate(
                ['email' => $adminEmail],
                ['name' => 'Medcare Administrator', 'password' => Hash::make($adminPassword), 'role' => 'admin']
            );
        }

        $staffEmail = env('STAFF_EMAIL');
        $staffPassword = env('STAFF_PASSWORD');
        if ($staffEmail && $staffPassword) {
            User::firstOrCreate(
                ['email' => $staffEmail],
                ['name' => 'Medcare Staff', 'password' => Hash::make($staffPassword), 'role' => 'staff']
            );
        }

        foreach ([
            'create-product', 'update-product', 'destroy-product',
            'create-category', 'update-category', 'destroy-category',
            'create-supplier', 'update-supplier', 'destroy-supplier',
            'create-user', 'update-user', 'destroy-user',
            'create-purchase', 'update-purchase', 'destroy-purchase',
            'approve-stock-in', 'approve-adjustment', 'dispense-sales-order', 'view-reports',
        ] as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
