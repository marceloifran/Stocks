<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::Create([
            'name' => 'Valeria',
            'email' => 'valeria.singh@bmi.com.ar',
            'password' =>'1234',
            'email_verified_at' => now()]);
    }
}
