<?php

namespace Tests\Feature\Auth;

use App\Mail\Auth\Restore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class RestoreTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'restore';

    /**
     * 対象データが復活し、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_can_restore_data()
    {
        Mail::fake();

        $restoreToken = Str::random(10);

        $user = User::factory()->create([
            'deleted_at' => now(),
            'restore_token' => $restoreToken,
        ]);

        $response = $this->post(self::API_URL, [
            'restore_token' => $restoreToken,
        ]);

        $afterUpdate = User::find($user->id);

        // 対象データが復活しているか確認する
        $this->assertNull($afterUpdate->deleted_at);
        // ユーザー復活用トークンが破棄されているか確認する
        $this->assertNull($afterUpdate->restore_token);

        $response->assertStatus(204);

        Mail::assertSent(Restore::class, static function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * トークンが異なる時、実行できないことを確認する
     *
     * @return void
     */
    public function test_can_not_restore_data_with_invalid_token()
    {
        User::factory()->create([
            'deleted_at' => now(),
            'restore_token' => Str::random(10),
        ]);

        $response = $this->post(self::API_URL, [
            'restore_token' => Str::random(10),
        ]);

        $response->assertStatus(422);
    }
}
