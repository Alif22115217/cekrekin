@component('mail::message')
# Reservasi Anda Telah Disetujui!

Reservasi Anda telah disetujui oleh Admin. Berikut adalah langkah selanjutnya untuk menyelesaikan proses:

### Pembayaran
Silakan melakukan pembayaran melalui transfer ATM ke rekening berikut:

## BCA xxxxxxxxxx a/n Alif Nursetyo Vimanto  
## Jumlah Pembayaran: @money($payment->total)

Setelah melakukan pembayaran, silakan upload bukti pembayaran pada website kami.

# Detail Reservasi
**Nama**: {{$payment->user->name}}  
**No Invoice**: {{ $payment->no_invoice }}  
**Tanggal Pengambilan**: {{ date('d M Y H:i', strtotime($payment->order->first()->starts)) }}

@component('mail::table')
| Alat             | Durasi       | Harga    |
| ---------------- |:------------:| --------:|
@foreach ($payment->order as $item)
| {{$item->alat->nama_alat}} | {{ $item->durasi }} Jam | @money($item->harga) |
@endforeach
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}  
Rental Kamera Online Amikom
@endcomponent
