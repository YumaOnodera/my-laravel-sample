<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Tests\ShowTestCase;
use Throwable;

class ShowTest extends ShowTestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private const API_URL = 'api/users';

    /**
     * 存在するデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     * @throws Throwable
     */
    public function test_request_data()
    {
        $user = User::factory()->create([
            'deleted_at' => null,
        ]);

        $this->assertRequestData(self::API_URL, $user);
    }

    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_request_not_found_data()
    {
        $this->assertRequestNotFoundData(self::API_URL);
    }

    /**
     * 論理削除されたデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_request_soft_delete_data()
    {
        $user = User::factory()->create([
            'deleted_at' => now(),
        ]);

        $this->assertRequestSoftDeleteData(self::API_URL, $user, true);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
