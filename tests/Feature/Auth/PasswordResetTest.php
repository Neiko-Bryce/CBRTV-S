<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Public forgot/reset-password flows are intentionally disabled in this app
 * (see routes/web.php). These tests lock that behavior so the suite stays green
 * without exposing email reset to users.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_route_is_hidden(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertNotFound();
    }

    public function test_forgot_password_post_does_not_send_reset_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertNotFound();
        Notification::assertNothingSent();
        Notification::assertNotSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_is_hidden(): void
    {
        $response = $this->get('/reset-password/fake-token');

        $response->assertNotFound();
    }

    public function test_reset_password_post_is_hidden(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/reset-password', [
            'token' => 'fake-token',
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertNotFound();
        Notification::assertNothingSent();
    }
}
