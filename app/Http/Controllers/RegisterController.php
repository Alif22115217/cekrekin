<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;  // <-- Tambahkan ini

class RegisterController extends Controller
{
    // Menampilkan halaman pendaftaran
    public function index() {
        return view('registration');
    }

    // Menyimpan data pendaftaran pengguna
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|max:255|confirmed',
            'telepon_kode' => 'required',
            'telepon' => 'required|regex:/^[0-9]+$/|max:15'
        ]);

        // Hash password before saving
        $validated['password'] = Hash::make($validated['password']);

        $validated['telepon'] = $validated['telepon_kode'] . $validated['telepon'];
        unset($validated['telepon_kode']);

        // Save the user to the database
        $user = User::create($validated);

        // Check if the user was created
        if (!$user) {
            Log::error('User creation failed', $validated);
        }

        return redirect(route('home'))->with('registrasi', 'Registrasi Berhasil, Silakan login untuk mulai menyewa');
    }

}
