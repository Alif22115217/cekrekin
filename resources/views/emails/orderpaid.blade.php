@component('mail::message')
# ✅ Pembayaran Berhasil!

Halo **{{ $payment->user->name }}**,  
Pembayaran Anda telah kami konfirmasi. Silakan ambil alat sesuai jadwal yang tertera di bawah ini.

---

### 📅 Detail Reservasi

**👤 Nama:** {{ $payment->user->name }}  
**🧾 No Invoice:** {{ $payment->no_invoice }}  
**📦 Tanggal Pengambilan:** {{ \Carbon\Carbon::parse($payment->order->first()->starts)->format('d M Y H:i') }}

---

### 📋 Rincian Alat Disewa

@component('mail::table')
| Nama Alat           | Durasi Sewa | Harga          |
|---------------------|:------------:|---------------:|
@foreach ($payment->order as $item)
| {{ $item->alat->nama_alat }} | {{ $item->durasi }} Jam | @money($item->harga) |
@endforeach
@endcomponent

---

### 💳 Total Pembayaran  
**@money($payment->total)** telah berhasil dibayarkan.

---

@component('mail::button', ['url' => route('order.detail', ['id' => $payment->order->first()->id])])
Lihat Detail Reservasi
@endcomponent

Terima kasih atas kepercayaan Anda menggunakan layanan kami.

Salam hangat,  
**Tim Rental Kamera Online Amikom**
@endcomponent