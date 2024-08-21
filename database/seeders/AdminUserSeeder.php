<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Arch',
            'email' => 'battlemaster.aoi@icloud.com',
            'password' => Hash::make('20010816enDo%'),
            'authorities' => 2,
            'organization_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
