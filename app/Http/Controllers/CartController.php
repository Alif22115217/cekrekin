<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Carts;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(Request $request, $id, $userId) {
        $cart = new Carts();
        $alat = Alat::find($id);

        // Pastikan alat ditemukan
        if (!$alat) {
            return back()->with('error', 'Alat tidak ditemukan.');
        }

        // Validasi jika btn tidak ada atau tidak sesuai durasi yang tersedia
        $harga = 0;  // Set default harga

        if ($request['btn'] == '24') {
            $harga = $alat->harga24;
        } elseif ($request['btn'] == '12') {
            $harga = $alat->harga12;
        } elseif ($request['btn'] == '6') {
            $harga = $alat->harga6;
        } else {
            return back()->with('error', 'Durasi yang dipilih tidak valid.');
        }

        // Jika $harga masih 0, berarti durasi tidak valid
        if ($harga == 0) {
            return back()->with('error', 'Durasi tidak valid.');
        }

        // Simpan ke keranjang
        $cart->user_id = $userId;
        $cart->alat_id = $alat->id;
        $cart->harga = $harga;
        $cart->durasi = $request['btn'];

        $cart->save();

        return back()->with('success', 'Berhasil ditambahkan ke keranjang');
    }

    public function destroy($id) {
        $alat = Carts::find($id);
        $alat->delete();

        return back();
    }
}
