<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_password_reset_link()
    {
        Notification::fake();

        $user = User::factory()->create();

        // Simulate sending password reset link
        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => $user->email
        ]);

        $response->assertOk();

        // Assert that the CustomResetPassword notification was sent to the user
        Notification::assertSentTo(
            $user,
            \App\Notifications\CustomResetPassword::class,
            function ($notification) use ($user) {
                // Get the email object from the notification
                $mail = $notification->toMail($user);

                Log::info('Mail object:', ['mail' => $mail]);

                // Check that the reset link URL contains the token
                return str_contains($mail->actionUrl, 'token=');
            }
        );
    }

    public function test_resets_password_with_valid_token()
    {
        Notification::fake();

        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/api/v1/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password'
        ]);

        $response->assertOk()
            ->assertJson(['status' => __(Password::PASSWORD_RESET)]);

        // Verify password was actually changed
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_doesnt_send_reset_link_for_invalid_email()
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => 'nonexistent@example.com'
        ]);

        // Check if status is 422 and check if error message is under 'email' key
        $response->assertStatus(422)
            ->assertJsonFragment(['email' => "We can't find a user with that email address."]);

        // Ensure no notifications are sent
        Notification::assertNothingSent();
    }
}
