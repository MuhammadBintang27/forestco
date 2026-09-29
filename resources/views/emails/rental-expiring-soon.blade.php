@component('mail::message')
# Masa Sewa Anda Akan Segera Berakhir

Halo {{ $rental->reservation->penyewa->nama }},

Masa sewa Anda untuk **{{ $rental->reservation->property->nama }}** akan berakhir pada
**{{ $rental->tanggal_selesai->translatedFormat('d F Y') }}** (7 hari lagi).

Jika Anda ingin melanjutkan sewa, silakan ajukan perpanjangan melalui tautan di bawah ini.

@component('mail::button', ['url' => route('sewa.index')])
Ajukan Perpanjangan
@endcomponent

Jika Anda tidak melakukan perpanjangan, masa sewa akan berakhir sesuai tanggal di atas.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
