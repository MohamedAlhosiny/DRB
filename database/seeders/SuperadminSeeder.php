<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::firstOrCreate(['email' => 'superadmin@gmail.com'] ,
         [
            'name' => 'superadmin' ,
            'password' => Hash::make('1234567890'),
            'role' => 'superadmin'
        ]);
    }
}
