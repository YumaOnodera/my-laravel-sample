<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 開発環境はSQLログを出力させる
        if (
            config('app.debug') === true
            && (app()->environment('local') || app()->environment('testing'))
        ) {
            $this->addQueryListener();
        }
    }

    private function addQueryListener()
    {
        DB::listen(
            static function ($query) {
                $sql = $query->sql;
                foreach ($query->bindings as $i => $iValue) {
                    $sql = preg_replace("/\?/", $query->bindings[$i], $sql, 1);
                }
                Log::channel('sql')->debug(sprintf('%s (%.2fms)', $sql, $query->time));
            }
        );
    }
}
