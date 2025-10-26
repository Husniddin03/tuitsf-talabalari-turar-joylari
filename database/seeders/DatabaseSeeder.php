<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(StudentsSeeder::class);

        User::factory()->create([
            'name' => 'tuit_admin',
            'email' => 'tuitadmin@gmail.com',
            'chat_id' => 7213131586,
            'password' => 'tuitsecret',
            'role' => 'admin'
        ]);
    }
}
