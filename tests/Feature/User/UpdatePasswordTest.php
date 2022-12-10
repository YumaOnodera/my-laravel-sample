<?php

namespace Tests\Feature\User;

use App\Mail\User\UpdatePassword;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 対象データが送信した値で更新されることを確認する
     *
     * @return void
     */
    public function test_can_update_password()
    {
        Mail::fake();

        $user = User::factory()->create();
        $password = 'password';

        $uri = sprintf('%s/%s/%s', self::API_URL, $user->id, 'update-password');
        $response = $this->actingAs($user)->put($uri, [
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user->password = $password;

        $afterUpdate = User::find($user->id);

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($user, $afterUpdate);

        $response->assertStatus(204);

        Mail::assertSent(UpdatePassword::class, static function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
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
        $password = 'password';

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'update-password');
        $response = $this->actingAs($requestUser)->put($uri, [
            'password' => $password,
            'password_confirmation' => $password,
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
        $this->assertTrue(Hash::check($expected->password, $actual->password));
        $this->assertSame($expected->remember_token, $actual->remember_token);
        $this->assertSame($expected->restore_token, $actual->restore_token);
        $this->assertSame($expected->is_admin, $actual->is_admin);
        $this->assertSame((string) $expected->created_at, (string) $actual->created_at);
        $this->assertSame((string) $expected->deleted_at, (string) $actual->deleted_at);
    }
}
