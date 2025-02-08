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
            'name' => 'Usuario 1',
            'email' => 'usuario1@empresa.com.ar',
            'password' =>'1234',
            'email_verified_at' => now()]);
        User::Create([
            'name' => 'Usuario2',
            'email' => 'usuario2@empresa.com.ar',
            'password' =>'1234',
            'email_verified_at' => now()]);
    }
}
