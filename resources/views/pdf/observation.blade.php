<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Surat Panggilan Observasi</title>
<style>
    body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1a2a22;}
    .header{text-align:center;border-bottom:3px double #0f5f3a;padding-bottom:8px;margin-bottom:16px;}
    .header h1{color:#0f5f3a;margin:0;font-size:15px;}
    h2{text-align:center;font-size:14px;letter-spacing:.1em;margin:16px 0 12px;color:#0f5f3a;text-transform:uppercase;}
    table{width:100%;border-collapse:collapse;margin:6px 0;}
    table td{padding:3px 6px;}
    .lbl{color:#555;width:35%;}
    .qr{float:right;text-align:center;font-size:8px;}
    .signature{margin-top:30px;text-align:right;}
</style></head>
<body>
    <div class="header">
        @php $logo = \App\Models\InformationSetting::get('logo_path'); @endphp
        @if($logo && file_exists(public_path($logo)))
            <img src="{{ public_path($logo) }}" style="width:60px;height:60px;object-fit:contain;">
        @endif
        <h1>AL-AZHAR CAIRO PALEMBANG</h1>
        <div>Jl. Jend. Sudirman KM. 3, Palembang</div>
    </div>
    <div class="qr"><img src="data:image/svg+xml;base64,{{ $qr }}" width="80"><br>Verifikasi</div>
    <h2>Surat Panggilan Observasi</h2>
    <p>Kepada Yth. Orang Tua / Wali dari:</p>
    <table>
        <tr><td class="lbl">Nama Calon Murid</td><td>: <b>{{ $reg->full_name }}</b></td></tr>
        <tr><td class="lbl">Nomor Pendaftaran</td><td>: <b>{{ $reg->registration_number }}</b></td></tr>
        <tr><td class="lbl">Grade / Tahun Ajaran</td><td>: {{ $reg->grade->code }} · {{ $reg->academicYear->name }}</td></tr>
    </table>
    <p>Dengan hormat, kami mengundang Bapak/Ibu untuk mengikuti sesi observasi calon murid pada:</p>
    <table>
        <tr><td class="lbl">Hari / Tanggal</td><td>: {{ \Carbon\Carbon::parse($reg->observation_date)->translatedFormat('l, d F Y') }}</td></tr>
        <tr><td class="lbl">Pukul</td><td>: {{ substr($reg->observation_time,0,5) }} WIB</td></tr>
        <tr><td class="lbl">Lokasi</td><td>: {{ $reg->observation_location }}</td></tr>
        @if($reg->observation_room)<tr><td class="lbl">Ruang</td><td>: {{ $reg->observation_room }}</td></tr>@endif
    </table>
    @if($reg->observation_notes)<p><b>Catatan:</b> {{ $reg->observation_notes }}</p>@endif
    <p>Mohon hadir tepat waktu. Terima kasih atas perhatian dan kerjasama Bapak/Ibu.</p>
    <div class="signature">
        <div>Palembang, {{ now()->translatedFormat('d F Y') }}</div>
        <br><br><br>
        <div><b>Panitia Penerimaan Murid Baru</b></div>
        <div>Al-Azhar Cairo Palembang</div>
    </div>
</body></html>
