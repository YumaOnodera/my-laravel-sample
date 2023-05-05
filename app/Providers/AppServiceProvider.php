<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Meilisearch\Client;
use Meilisearch\Meilisearch;

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

        $this->updateFilterableAttributes();
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

        // dataキーの除去
        JsonResource::withoutWrapping();
    }

    /**
     * SQLログを出力する
     *
     * @return void
     */
    private function addQueryListener(): void
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

    /**
     * フィルター可能な属性を検索エンジンに設定する
     *
     * @return void
     */
    private function updateFilterableAttributes(): void
    {
        if (class_exists(Meilisearch::class)) {
            $client = app(Client::class);
            $config = config('scout.meilisearch.settings');
            collect($config)
                ->each(function ($settings, $class) use ($client) {
                    $model = new $class;
                    $index = $client->index($model->searchableAs());
                    collect($settings)
                        ->each(function ($params, $method) use ($index) {
                            $index->{$method}($params);
                        });
                });
        }
    }
}
