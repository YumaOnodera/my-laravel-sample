<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CheckDeletedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleted_user()
    {
        $user = User::factory()->create([
            'deleted_at' => now()
        ]);

        $response = $this->post('/deleted-user/check', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'id' => 1,
                'is_deleted' => true
            ]);
    }

    public function test_not_deleted_user()
    {
        $user = User::factory()->create();

        $response = $this->post('/deleted-user/check', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'id' => null,
                'is_deleted' => false
            ]);
    }

    public function test_not_deleted_user_with_invalid_password()
    {
        $user = User::factory()->create();

        $response = $this->post('/deleted-user/check', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'id' => null,
                'is_deleted' => false
            ]);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
