<?php

namespace Tests\Feature\EmailReset;

use App\Models\EmailReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/email-resets';

    /**
     * 対象データが送信した値で更新されることを確認する
     *
     * @return void
     */
    public function test_can_update_email()
    {
        $user = User::factory()->create();
        $emailReset = EmailReset::factory()->create();

        $response = $this->actingAs($user)->put(self::API_URL.'/'.$emailReset->token);

        $user->email = $emailReset->new_email;
        $user->email_verified_at = now();

        $afterUpdate = User::find($user->id);

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($user, $afterUpdate);

        $isExists = EmailReset::where('id', $emailReset->id)->exists();

        // 一時テーブルの対象データが削除されていることを確認する
        $this->assertFalse($isExists);

        $response->assertStatus(204);
    }

    /**
     * トークンが異なる時、実行できないことを確認する
     *
     * @return void
     */
    public function test_can_not_update_email_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(self::API_URL.'/'.Str::random(10));

        $response->assertStatus(422);
    }

    /**
     * トークンが期限切れの時、実行できないことを確認する
     *
     * @return void
     */
    public function test_can_not_update_email_with_expired_token()
    {
        $user = User::factory()->create();
        $expiration = config('const.email_resets.expire');
        $emailReset = EmailReset::factory()->create([
            'created_at' => Carbon::now()->subMinutes($expiration),
        ]);

        $response = $this->actingAs($user)->put(self::API_URL.'/'.$emailReset->token);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                'message' => [
                    'token' => [
                        __('exception.email_resets.token_expired'),
                    ],
                ],
            ]);
    }

    /**
     * 2つのModelの値が同じ値であることを確認する
     *
     * @param  Model  $expected
     * @param  Model  $actual
     * @return void
     */
    public function assertSameData(Model $expected, Model $actual)
    {
        $this->assertSame($expected->name, $actual->name);
        $this->assertSame($expected->email, $actual->email);
        $this->assertSame((string) $expected->email_verified_at, (string) $actual->email_verified_at);
        $this->assertSame($expected->password, $actual->password);
        $this->assertSame($expected->remember_token, $actual->remember_token);
        $this->assertSame($expected->restore_token, $actual->restore_token);
        $this->assertSame($expected->is_admin, $actual->is_admin);
        $this->assertSame((string) $expected->created_at, (string) $actual->created_at);
        $this->assertSame((string) $expected->deleted_at, (string) $actual->deleted_at);
    }
}
