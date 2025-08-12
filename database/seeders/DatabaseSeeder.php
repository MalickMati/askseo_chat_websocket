<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'CEO',
            'email' => 'ceo@askseo.com',
            'password' => Hash::make('password123'),
            'status' => 'active',  
            'status_mode' => 'offline', 
            'image' => null,  
            'type' => 'super_admin', 
        ]);
    }
    // public function run(): void
    // {
    //     $path = database_path('seeders/seed.sql');

    //     // Check if the file exists
    //     if (File::exists($path)) {
    //         // Get the contents of the SQL file
    //         $sql = File::get($path);

    //         // Execute the SQL queries
    //         DB::unprepared($sql);

    //         $this->command->info('SQL Seed file executed successfully.');
    //     } else {
    //         $this->command->error('SQL file not found.');
    //     }
    // }
}
