<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(Mundial2026Seeder::class);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'cedula' => '10000000',
                'password' => Hash::make('SJnKkvvLBpZIOoTsdZkwWMEDgWlFXvdn'),
                'is_admin' => true,
                'approved_at' => now(),
            ]
        );
    }
}
