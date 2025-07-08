<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Konstanta Role untuk memudahkan pembacaan kode
    const MEMBER = 0;
    const ADMIN = 1;
    const SUPERADMIN = 2;

    // Promosikan user ke admin
    public function promote($id)
    {
        // Cek apakah user ditemukan
        $user = User::find($id);
        
        if (!$user) {
            return back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Pastikan hanya superadmin yang bisa mempromosikan menjadi admin
        if (Auth::user()->role === self::SUPERADMIN) {
            $user->role = self::ADMIN;
            $user->save();
            return back()->with('success', 'User telah dipromosikan menjadi Admin');
        }

        return back()->with('error', 'Hanya Superadmin yang bisa mempromosikan pengguna menjadi Admin.');
    }

    // Promosikan user ke superadmin
    public function promoteToSuperAdmin($id)
    {
        // Cek apakah user ditemukan
        $user = User::find($id);
        
        if (!$user) {
            return back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Pastikan hanya superadmin yang bisa mempromosikan menjadi superadmin
        if (Auth::user()->role === self::SUPERADMIN) {
            $user->role = self::SUPERADMIN;
            $user->save();
            return back()->with('success', 'User telah dipromosikan menjadi Superadmin');
        }

        return back()->with('error', 'Hanya Superadmin yang bisa mempromosikan pengguna menjadi Superadmin.');
    }

    // Demote user menjadi member
    public function demote($id)
    {
        // Cek apakah user ditemukan
        $user = User::find($id);

        if (!$user) {
            return back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Pastikan hanya superadmin yang bisa menurunkan menjadi member
        if (Auth::user()->role === self::SUPERADMIN) {
            $user->role = self::MEMBER;
            $user->save();
            return back()->with('success', 'User telah diturunkan menjadi Member');
        }

        return back()->with('error', 'Hanya Superadmin yang bisa menurunkan pengguna menjadi Member.');
    }

    // Tampilkan halaman edit akun
    public function edit()
    {
        return view('account', [
            'user' => User::find(Auth::id())
        ]);
    }

    // Update informasi akun
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        // Validasi input
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'telepon' => 'required|max:15'
        ]);

        // Update data pengguna
        $user->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'telepon' => $request['telepon']
        ]);

        return back()->with('updated', 'Berhasil melakukan perubahan');
    }

    // Ganti password
    public function changePassword(Request $request)
    {
        $user = User::find(Auth::id());

        // Validasi input password
        $this->validate($request, [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:8|confirmed', // Menambahkan validasi untuk konfirmasi password
        ]);

        // Cek apakah password lama benar
        if (Hash::check($request['oldPassword'], $user->password)) {
            // Update password baru
            $user->update([
                'password' => Hash::make($request['newPassword'])
            ]);

            // Logout setelah mengganti password
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('authenticate')->with('message', 'Silakan login ulang setelah mengganti password.');
        } else {
            return back()->with('message', 'Password saat ini salah');
        }
    }
}
