<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validasi input login
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Silakan isi email.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Silakan isi password.'
        ]);

        // Cek apakah email terdaftar
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            // Email tidak ditemukan
            return back()->withErrors(['email' => 'Email tidak terdaftar.']);
        }

        // Ambil percobaan login dari session berdasarkan email
        $attempts = session()->get('login_attempts_' . $user->id, 0);
        $blockedUntil = session()->get('blocked_until_' . $user->id);

        // Cek apakah user diblokir
        if ($blockedUntil && now()->lt($blockedUntil)) {
            $remaining = $blockedUntil->diffInMinutes(now());
            return back()->withErrors(['email' => "Login diblokir untuk email ini. Silakan coba lagi setelah $remaining menit."]);
        }

        // Coba autentikasi
        if (Auth::attempt($credentials)) {
            // Login berhasil
            $request->session()->regenerate();

            // Reset percobaan login
            session()->forget(['login_attempts_' . $user->id, 'blocked_until_' . $user->id]);

            // Arahkan sesuai role
            if (Auth::user()->role != 0) {
                return redirect()->route('admin.index');
            } else {
                return redirect()->route('member.index');
            }
        }

        // Password salah → Tambah hitungan percobaan login
        session()->put('login_attempts_' . $user->id, $attempts + 1);

        if ($attempts + 1 >= 3) {
            // Blokir user selama 5 menit
            session()->put('blocked_until_' . $user->id, now()->addMinutes(5));
            return back()->withErrors(['password' => 'Anda telah salah memasukkan password 3 kali. Email ini diblokir selama 5 menit.']);
        }

        return back()->withErrors(['password' => 'Password salah. Percobaan login: ' . ($attempts + 1) . '/3']);
    }


    public function logout(Request $request)
    {
        $user = Auth::user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Reset percobaan login hanya jika user sebelumnya login
        if ($user) {
            session()->forget(['login_attempts_' . $user->id, 'blocked_until_' . $user->id]);
        }

        return redirect('/');
    }
}
