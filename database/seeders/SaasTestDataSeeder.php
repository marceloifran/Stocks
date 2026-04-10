<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class SaasTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Test Company
        $company = Company::create(['name' => 'Tech Solutions Corp']);

        // Create Admin User for the company
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@techsolutions.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        // Create Suppliers for the company
        Supplier::create([
            'company_id' => $company->id,
            'name' => 'Hardware Distritos',
            'email' => 'ventas@hardwareDist.com',
            'phone' => '12345678',
            'category' => 'Hardware',
        ]);

        Supplier::create([
            'company_id' => $company->id,
            'name' => 'Software Experts',
            'email' => 'info@soft-experts.com',
            'phone' => '87654321',
            'category' => 'Software',
        ]);
    }
}
