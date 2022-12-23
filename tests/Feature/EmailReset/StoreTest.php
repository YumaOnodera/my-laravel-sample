<?php

namespace Tests\Feature\EmailReset;

use App\Mail\EmailReset\Store;
use App\Models\EmailReset;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Throwable;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/email-resets/send-reset-link';

    /**
     * 対象データが送信した値で作成されることを確認する
     *
     * @return void
     *
     * @throws Throwable
     */
    public function test_can_store_data()
    {
        Mail::fake();

        $user = User::factory()->create();
        $newEmail = Factory::create('ja_JP')->email();

        $response = $this->actingAs($user)->post(self::API_URL, [
            'new_email' => $newEmail,
        ]);

        $emailReset = EmailReset::where('new_email', $newEmail)->first();

        // 対象データが送信した値で作成されていることを確認する
        $this->assertSame($user->id, $emailReset->user_id);
        $this->assertSame($newEmail, $emailReset->new_email);
        $this->assertNotNull($emailReset->token);

        $response->assertStatus(204);

        Mail::assertSent(Store::class, static function ($mail) use ($emailReset) {
            return $mail->hasTo($emailReset->new_email);
        });
    }

    /**
     * 存在するメールアドレスを指定した時、実行できないことを確認する
     *
     * @return void
     */
    public function test_can_not_store_data_with_found_email()
    {
        $users = User::factory(10)->create();
        $newEmail = $users->random()->new_email;

        $response = $this->actingAs($users->first())->post(self::API_URL, [
            'new_email' => $newEmail,
        ]);

        $response->assertStatus(422);
    }
}
