@component('mail::message')
# Reservasi Dibatalkan

Halo {{ $user->name }},

Kami menyesal menginformasikan bahwa reservasi Anda untuk **{{ $alat->nama_alat }}** dengan durasi **{{ $reservasi->durasi }} jam** telah dibatalkan.

**Nomor Reservasi**: {{ $reservasi->id }}  
**Tanggal Pemesanan**: {{ $reservasi->created_at->format('d M Y') }}  
**Tanggal Pengambilan**: {{ $reservasi->start_date->format('d M Y') }}

Terima kasih,<br>
**Rental Kamera Online**
@endcomponent
