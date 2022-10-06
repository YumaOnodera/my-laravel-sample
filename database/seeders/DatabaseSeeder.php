<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();

        $models = [
            User::class,
            Post::class
        ];
        $this->ScoutImport($models);
    }

    /**
     * データを検索インデックスに反映させる
     *
     * @param array $models
     * @return void
     */
    private function ScoutImport(array $models): void
    {
        foreach ($models as $model) {
            Artisan::call('scout:flush', [
                'model' => $model
            ]);
            Artisan::call('scout:import', [
                'model' => $model
            ]);
        }
    }
}
