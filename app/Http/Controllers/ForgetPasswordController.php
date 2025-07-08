<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    /**
     * Menampilkan form lupa password.
     */
    public function index()
    {
        return view('forgetpassword');
    }

    /**
     * Mengirim email berisi link reset password.
     */
    public function sendResetLink(Request $request)
    {
        // Validate email input
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem kami.',
        ]);

        // Get user by email
        $user = User::where('email', $request->email)->first();

        // Generate token
        $token = Str::random(64); // Use hashing for security if needed, for example: Hash::make($token);

        // Insert or update token in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Send email for password reset
        Mail::send('emails.forgetpassword', ['token' => $token, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Rental Kamera Online: Permintaan Reset Password');
        });

        return back()->with('message', 'Silakan cek email Anda untuk reset password.');
    }

    /**
     * Menampilkan form reset password.
     */
    public function resetPasswordIndex($token)
    {
        // Ensure the token is valid and not expired
        $resetEntry = DB::table('password_resets')->where('token', $token)->first();

        if (!$resetEntry || Carbon::parse($resetEntry->created_at)->addMinutes(60)->isPast()) {
            return redirect()->route('forgetpassword.index')->withErrors([
                'token' => 'Token tidak valid atau telah kedaluwarsa!',
            ]);
        }

        return view('resetpassword', ['token' => $token]);
    }

    /**
     * Menyimpan password baru dari form reset password.
     */
    public function resetPassword(Request $request)
    {
        // Validate input form
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            'token'                 => 'required',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem kami.',
        ]);

        // Get reset entry based on email and token
        $resetEntry = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetEntry) {
            return back()->withInput()->withErrors([
                'token' => 'Token tidak valid atau telah kedaluwarsa!',
            ]);
        }

        // Get the user based on email
        $user = User::where('email', $request->email)->first();

        // Ensure the new password is not the same as the old password
        if (Hash::check($request->password, $user->password)) {
            return back()->withInput()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password lama.',
            ]);
        }

        // Update user password
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the reset token after a successful password update
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Redirect to home with success message
        return redirect(route('home'))->with('success_reset_password', 'Reset Password Berhasil!');
    }
}
