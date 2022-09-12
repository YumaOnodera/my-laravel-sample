<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UpdateEmailTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users/update-email';

    /**
     * 対象データが送信した値で更新され、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_success()
    {
        $expected = User::factory()->create();

        $request = [
            'email' => 'test@example.co.jp'
        ];
        $response = $this->actingAs($expected)->put(self::API_URL, $request);

        $expected->email = $request['email'];
        $expected->email_verified_at = null;
        $afterUpdate = User::where('id', 1)->first();

        $response->assertStatus(204);

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($expected, $afterUpdate);
    }

    /**
     * 2つのModelの値が同じ値であることを確認する
     *
     * @param Model $expected
     * @param Model $actual
     * @return void
     */
    public function assertSameData (Model $expected, Model $actual)
    {
        $this->assertSame($expected->name, $actual->name);
        $this->assertSame($expected->email, $actual->email);
        $this->assertSame((string) $expected->email_verified_at, (string) $actual->email_verified_at);
        $this->assertSame($expected->password, $actual->password);
        $this->assertSame($expected->remember_token, $actual->remember_token);
        $this->assertSame((string) $expected->created_at, (string) $actual->created_at);
        $this->assertSame((string) $expected->deleted_at, (string) $actual->deleted_at);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
