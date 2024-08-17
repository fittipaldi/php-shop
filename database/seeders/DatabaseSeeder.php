<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Start - Seed for the API Tokens
        DB::table('api_tokens')->truncate();

        DB::table('api_tokens')->insert([
            'token' => 'iXf6omDZmOYheWw3TGxz054rhLfEjgD75KvdzXtWgqZMcKAkZia9e39WJs9EIByS',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        // End - Seed for the API Tokens
    }
}
