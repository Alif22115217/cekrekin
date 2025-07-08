<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ForgetPasswordTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_request_reset_password_link()
    {
        // Create a test user with an existing email
        $user = User::factory()->create([
            'email' => 'nursetyoalif56@gmail.com', // Match this email with the one in your database
        ]);

        // Fake email sending to prevent actual emails from being sent
        Mail::fake();

        // Send the request to reset the password
        $response = $this->post(route('forgetpassword.sendlink'), [
            'email' => $user->email,
        ]);

        // Ensure user is redirected with success message
        $response->assertRedirect(route('forgetpassword.index'));
        $response->assertSessionHas('message', 'Silakan cek email Anda untuk reset password.');

        // Assert that the reset password email was sent exactly once
        Mail::assertSent(\Illuminate\Mail\Message::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->build()->getSubject() === 'Rental Kamera Online: Permintaan Reset Password';
        });
    }

    /** @test */
    public function user_cannot_request_reset_password_link_with_invalid_email()
    {
        // Fake email sending to prevent actual emails from being sent
        Mail::fake();

        // Send the request with an invalid email (non-existing email)
        $response = $this->post(route('forgetpassword.sendlink'), [
            'email' => 'invalid@example.com', // Non-existing email
        ]);

        // Ensure the user is redirected back with errors
        $response->assertSessionHasErrors('email');

        // Ensure the redirection goes back to the forget password form
        $response->assertRedirect(route('forgetpassword.index'));

        // Ensure no email was sent
        Mail::assertNothingSent();
    }

    /** @test */
    public function reset_password_link_is_sent_with_valid_token()
    {
        // Create a test user with an existing email in your database
        $user = User::factory()->create([
            'email' => 'alifvimanto69@gmail.com', // Ensure the email matches one from your database
        ]);

        // Fake email sending to prevent actual emails from being sent
        Mail::fake();

        // Send the request to reset password
        $response = $this->post(route('forgetpassword.sendlink'), [
            'email' => $user->email,
        ]);

        // Assert that the reset password email was sent
        Mail::assertSent(\Illuminate\Mail\Message::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->build()->getSubject() === 'Rental Kamera Online: Permintaan Reset Password';
        });

        // Get the token from the database
        $token = DB::table('password_resets')->where('email', $user->email)->first()->token;

        // Send a GET request to the password reset page with the token
        $response = $this->get(route('password.reset', ['token' => $token]));

        // Assert that the response contains the token and renders the reset page
        $response->assertStatus(200);
        $response->assertSee($token);
    }
}
