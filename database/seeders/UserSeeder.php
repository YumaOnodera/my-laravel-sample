<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        User::factory()
            ->count(50)
            ->sequence(fn ($sequence) => [
                'is_admin' => $sequence->index === 0 ? 1 : 0
            ])
            ->create();
    }
}
