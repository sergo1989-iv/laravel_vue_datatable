<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'              => 'Roman',
            'email'             => 'roman@web-team.top',
            'password'          => Hash::make('acD-gSk-Gtc-2yK'),
            'email_verified_at' => now(),
        ]);

        User::factory(1000)->create();
    }
}
