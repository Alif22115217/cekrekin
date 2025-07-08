<?php

namespace App\Http\Controllers;

use App\Mail\OrderAccepted;
use App\Mail\OrderPaid;
use App\Mail\ReservationCancelled;
use App\Models\User;
use App\Models\Carts;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function show() {
        $payment = Payment::with(['user','order'])->where('user_id', Auth::id());
        return view('member.reservasi',[
            'reservasi' => $payment->where('status','!=', 4)->orderBy('id','DESC')->get(),
            'riwayat' => Payment::with(['user','order'])->where('user_id', Auth::id())->where('status', 4)->orderBy('id','DESC')->get()
        ]);
    }

    public function detail($id) {
        $detail = Order::where('payment_id', $id)->get();
        $payment = Payment::find($id);

        if($payment->user_id == Auth::id()) {
            return view('member.detailreservasi',[
                'detail' => $detail,
                'total' => $payment->total,
                'paymentId' => $payment->id,
                'paymentStatus' => $detail->first()->payment->status,
                'bukti' => $payment->bukti
            ]);
        } else {
            return abort(403, "Forbidden");
        }
    }

    public function create(Request $request) {
        $cart = Carts::where('user_id', Auth::id())->get();
        $pembayaran = new Payment();

        $pembayaran->no_invoice = Auth::id()."/".Carbon::now()->timestamp;
        $pembayaran->user_id = Auth::id();
        $pembayaran->total = $cart->sum('harga');
        $pembayaran->save();

        foreach($cart as $c) {
            // Ambil data alat
            $alat = $c->alat;

            // Validasi stok cukup
            if ($alat->stok < 1) {
                return redirect()->back()->with('error', 'Stok alat "' . $alat->nama_alat . '" tidak mencukupi!');
            }

            // Kurangi stok
            $alat->stok -= 1;
            $alat->save();

            // Simpan order
            Order::create([
                'alat_id' => $c->alat_id,
                'user_id' => $c->user_id,
                'payment_id' => $pembayaran->id,
                'durasi' => $c->durasi,
                'starts' => date('Y-m-d H:i', strtotime($request['start_date'].$request['start_time'])),
                'ends' => date('Y-m-d H:i', strtotime($request['start_date'].$request['start_time']."+".$c->durasi." hours")),
                'harga' => $c->harga,
            ]);
            $c->delete();
        }

        return redirect(route('order.show'));
    }

    public function destroy($id) {
        $payment = Payment::find($id);

        $payment->delete();

        return redirect(route('order.show'));
    }

        public function acc(Request $request, $paymentId) {
        // Validate the paymentId
        $payment = Payment::find($paymentId);
        if (!$payment) {
            return redirect()->route('penyewaan.index')->with('error', 'Payment not found.');
        }

        $orders = $request->order;

        // Update order statuses
        foreach($orders as $o) {
            Order::where('id', $o)->update(['status' => 2]);
        }

        // Update payment status
        $payment->update(['status' => 2]);

        // Mark related orders as approved
        Order::where('payment_id', $paymentId)->where('status', 1)->update(['status' => 3]);

        // Update the total price for the payment
        $payment->update(['total' => Order::where('payment_id', $paymentId)->where('status', 2)->sum('harga')]);

        // Send email
        Mail::to($payment->user->email)->send(new OrderAccepted($payment));

        // // Send additional email to admin
        // Mail::to('alifvimanto69@gmail.com')->send(new OrderAccepted($payment));

        return back();
    }


    public function bayar(Request $request, $id) {
        $this->validate($request, [
            'bukti' => "image|mimes:png,jpg,svg,jpeg,gif|max:5000"
        ]);

        $payment = Payment::find($id);
        if($request->hasFile('bukti')) {
            $gambar = $request->file('bukti');
            $filename = time().'-'.$gambar->getClientOriginalName();
            $gambar->move(public_path('images/evidence'), $filename);
        }
        $payment->update([
            'bukti' => $filename
        ]);

        return back();
    }

    public function accbayar($id) {
        $payment = Payment::find($id);

        $payment->update([
            'status' => 3
        ]);

        Mail::to($payment->user->email)->send(new OrderPaid($payment));

        return redirect()->route('penyewaan.index')->with('message', 'Pembayaran berhasil diverifikasi.');
    }

    
    public function alatkembali($id) {
        $payment = Payment::find($id);

        // Ambil semua order yang terkait payment ini
        $orders = Order::where('payment_id', $id)->get();

        // Tambahkan kembali stok alat
        foreach ($orders as $order) {
            $alat = $order->alat;
            $alat->stok += 1;
            $alat->save();
        }

        // Update status payment jadi "4" (dikembalikan)
        $payment->update(['status' => 4]);

        return back()->with('message', 'Alat berhasil dikembalikan dan stok diperbarui.');
    }

    public function cetak() {
        $dari = request('dari');
        $sampai = request('sampai');
        $laporan = DB::table('orders')
            ->join('payments','payments.id','orders.payment_id')
            ->join('alats','alats.id','orders.alat_id')
            ->join('users','users.id','orders.user_id')
            ->whereBetween('orders.created_at',[$dari, $sampai])
            ->where('orders.status',2)
            ->where('payments.status','>',2)
            ->get(['*','orders.created_at AS tanggal']);

        return view('admin.laporan',[
            'laporan' => $laporan,
            'total' => $laporan->sum('harga')
        ]);
    }

    public function cancelReservation($id)
        {
            Log::info('Cancel reservation initiated for Payment ID: ' . $id);
        
            // Temukan reservasi menggunakan Eloquent
            $payment = Payment::find($id);
        
            if (!$payment) {
                Log::error('Reservation not found for Payment ID: ' . $id);
                return redirect()->route('penyewaan.index')->with('error', 'Reservasi tidak ditemukan.');
            }
        
            // Ambil user terkait reservasi
            $user = $payment->user;
        
            if (!$user) {
                Log::error('User not found for Payment ID: ' . $id);
                return redirect()->route('penyewaan.index')->with('error', 'User tidak ditemukan.');
            }
        
            try {
                // Hapus reservasi
                $payment->delete();
                Log::info('Reservation deleted for Payment ID: ' . $id);
        
                // Kirim email pembatalan reservasi ke pengguna
                Mail::to($user->email)->send(new ReservationCancelled($user));
                Log::info('Cancellation email sent to user: ' . $user->email);
        
                // // Kirim email pembatalan reservasi ke alifvimanto69@gmail.com
                // Mail::to('alifvimanto69@gmail.com')->send(new ReservationCancelled($user));
                // Log::info('Cancellation email sent to admin: alifvimanto69@gmail.com');
            } catch (\Exception $e) {
                // Log jika ada error saat mengirim email
                Log::error('Failed to send cancellation email for Payment ID: ' . $id . ' Error: ' . $e->getMessage());
                return redirect()->route('penyewaan.index')->with('error', 'Gagal mengirim email pembatalan.');
            }
        
            return redirect()->route('penyewaan.index')->with('message', 'Reservasi berhasil dibatalkan dan email telah dikirim ke pengguna.');
        }
}
