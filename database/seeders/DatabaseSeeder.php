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
        User::where('email', '!=', 'admin@gmail.com')->delete();
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Medcare Administrator', 'password' => Hash::make('admin123'), 'role' => 'admin']
        );

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
