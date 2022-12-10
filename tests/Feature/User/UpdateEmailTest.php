<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Notifications\EmailVerification;
use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UpdateEmailTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 対象データが送信した値で更新されることを確認する
     *
     * @return void
     */
    public function test_can_update_email()
    {
        Notification::fake();

        $user = User::factory()->create();
        $email = Factory::create('ja_JP')->email();

        $uri = sprintf('%s/%s/%s', self::API_URL, $user->id, 'update-email');
        $response = $this->actingAs($user)->put($uri, [
            'email' => $email,
        ]);

        $user->email = $email;
        $user->email_verified_at = null;

        $afterUpdate = User::find($user->id);

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($user, $afterUpdate);

        $response->assertStatus(204);

        Notification::assertSentTo($user, EmailVerification::class);
    }

    /**
     * 他のユーザーを対象にできないことを確認する
     *
     * @return void
     */
    public function test_can_not_update_other_users_data()
    {
        $requestUser = User::factory()->create();
        $otherUser = User::factory()->create();
        $email = Factory::create('ja_JP')->email();

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'update-email');
        $response = $this->actingAs($requestUser)->put($uri, [
            'email' => $email,
        ]);

        $response->assertStatus(403);
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
