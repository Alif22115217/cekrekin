<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlatApiController extends Controller
{
    public function showAllAlat()
    {
        if (request('category')) {
            $query = request('category');
            $filtered = DB::table('alats')
                ->join('categories', 'categories.id', '=', 'alats.kategori_id')
                ->where('kategori_id', $query)
                ->get(['alats.id', 'kategori_id', 'nama_alat', 'harga24', 'harga12', 'harga6', 'nama_kategori']);

            if ($filtered->isNotEmpty()) {
                return response()->json([
                    'message' => 'success',
                    'data' => $filtered
                ], 200);
            } else {
                return response()->json([
                    'message' => 'NOT FOUND',
                    'data' => []
                ], 404);
            }
        } else {
            $alat = DB::table('alats')
                ->join('categories', 'categories.id', '=', 'alats.kategori_id')
                ->get(['alats.id', 'kategori_id', 'nama_alat', 'harga24', 'harga12', 'harga6', 'nama_kategori']);

            return response()->json([
                'message' => 'success',
                'data' => $alat
            ], 200);
        }
    }

    public function showAllCategory()
    {
        return response()->json([
            'message' => 'success',
            'data' => Category::all(['id', 'nama_kategori'])
        ]);
    }

    public function detail($id)
    {
        $alat = Alat::find($id, ['id', 'kategori_id', 'nama_alat', 'harga24', 'harga12', 'harga6']);

        $booked = DB::table('orders')
            ->join('alats', 'alats.id', '=', 'orders.alat_id')
            ->join('payments', 'payments.id', '=', 'orders.payment_id')
            ->where('alats.id', $id)
            ->where('orders.status', 2)
            ->where('payments.status', 3)
            ->get(['starts AS start', 'ends AS end', 'durasi']);

        return response()->json([
            "message" => "success",
            "data" => $alat,
            "booked" => $booked
        ], 200);
    }

    public function searchAlatByName(Request $request)
    {
        $query = $request->input('query');
        
        // // Log query untuk memastikan parameter diterima dengan benar
        // \Log::info('Search query:', ['query' => $query]);

        if (!$query) {
            return response()->json([
                'message' => 'Query parameter is required',
                'data' => []
            ], 400);
        }

        // Lanjutkan dengan query pencarian
        $alats = DB::table('alats')
        ->join('categories', 'categories.id', '=', 'alats.kategori_id')
        ->whereRaw('LOWER(nama_alat) LIKE ?', ['%' . strtolower($query) . '%'])
        ->get(['alats.id', 'kategori_id', 'nama_alat', 'deskripsi', 'harga24', 'harga12', 'harga6', 'gambar', 'created_at', 'updated_at', 'stok', 'spesifikasi']);


        if ($alats->isNotEmpty()) {
            return response()->json([
                'message' => 'success',
                'data' => $alats
            ], 200);
        } else {
            return response()->json([
                'message' => 'no results found',
                'data' => []
            ], 404);
        }
    }


    public function createAlat(Request $request)
    {
        $this->validate($request, [
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|integer|exists:categories,id',
            'harga24' => 'required|numeric',
            'harga12' => 'required|numeric',
            'harga6' => 'required|numeric',
        ]);

        $alat = new Alat();
        $alat->nama_alat = $request->input('nama_alat');
        $alat->kategori_id = $request->input('kategori_id');
        $alat->harga24 = $request->input('harga24');
        $alat->harga12 = $request->input('harga12');
        $alat->harga6 = $request->input('harga6');

        // Handle image upload if exists
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $alat->gambar = $imageName;
        }

        $alat->save();

        return response()->json([
            'message' => 'Alat added successfully',
            'data' => $alat
        ], 201);
    }
}
