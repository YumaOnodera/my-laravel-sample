<?php

namespace Tests\Feature\Auth;

use App\Mail\Auth\Restore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
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
    public function test_success()
    {
        Mail::fake();

        $user = User::factory()->create([
            'deleted_at' => now()
        ]);

        $response = $this->post(self::API_URL, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $afterUpdate = User::where('id', $user->id)->first();

        // 対象データが復活しているか確認する
        $this->assertNull($afterUpdate->deleted_at);

        $response->assertStatus(204);

        Mail::assertSent(Restore::class, static function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_can_not_restore_with_invalid_password()
    {
        $user = User::factory()->create([
            'deleted_at' => now()
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('exception.auth.restore'));

        $response = $this->withoutExceptionHandling()->post(self::API_URL, [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(500);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
