<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran {{ $reg->registration_number }}</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1a2a22; line-height: 1.45; }
        .wrap { border: 1.5px solid #cfc3a0; padding: 18px 20px; border-radius: 4px; }

        /* HEADER */
        .header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .header-tbl td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 60px; }
        .logo-cell .logo-box { width: 52px; height: 52px; background: #faf6ec; border: 1.5px solid #c9a24b; border-radius: 50%; text-align: center; line-height: 52px; font-weight: bold; color: #0f5f3a; font-size: 15px; font-family: 'Fraunces', serif; }
        .title-cell h1 { color: #0f5f3a; margin: 0; font-size: 17px; letter-spacing: .03em; }
        .title-cell .subtitle { color: #666; font-size: 10.5px; margin-top: 2px; }
        .qr-cell { width: 90px; text-align: right; }
        .qr-cell img { width: 78px; height: 78px; border: 1px solid #eee; padding: 3px; background: #fff; }
        .divider { border: none; border-top: 2px solid #0f5f3a; margin: 8px 0 14px 0; }

        /* SUMMARY BOX */
        .summary { background: #faf6ec; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 3px 4px; vertical-align: top; font-size: 10.5px; }
        .summary .lbl { color: #666; width: 22%; }
        .summary .val { font-weight: bold; color: #0f5f3a; width: 28%; }
        .status-pill { display: inline-block; background: #0f5f3a; color: #fff; padding: 3px 12px; border-radius: 999px; font-size: 10px; font-weight: bold; }

        /* SECTIONS */
        h2.section { color: #0f5f3a; font-size: 12px; margin: 14px 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #c9a24b; letter-spacing: .02em; }
        .data-tbl { width: 100%; border-collapse: collapse; }
        .data-tbl td { padding: 4px 5px; vertical-align: top; font-size: 10.5px; }
        .data-tbl .lbl { color: #555; width: 20%; }
        .data-tbl .val { width: 30%; }

        /* SIGNATURE */
        .signature-tbl { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .signature-tbl td { width: 50%; text-align: left; vertical-align: top; padding: 0 10px; font-size: 10.5px; }
        .signature-tbl .space { height: 60px; }
        .signature-tbl .line { border-top: 1px solid #333; padding-top: 4px; font-weight: bold; }

        /* FOOTER */
        .foot { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #888; }
    </style>
</head>
<body>
<div class="wrap">
    @php $logo = \App\Models\InformationSetting::get('logo_path'); @endphp
    <table class="header-tbl">
        <tr>
            <td class="logo-cell">
                @if($logo && file_exists(public_path($logo)))
                    <img src="{{ public_path($logo) }}" style="width:52px;height:52px;object-fit:contain;">
                @else
                    <div class="logo-box">AZ</div>
                @endif
            </td>
            <td class="title-cell">
                <h1>AL-AZHAR CAIRO PALEMBANG</h1>
                <div class="subtitle">BUKTI PENDAFTARAN MURID BARU · PORMIN (Portal Minat)</div>
            </td>
            <td class="qr-cell"><img src="data:image/svg+xml;base64,{{ $qr }}"></td>
        </tr>
    </table>
    <hr class="divider">

    <div class="summary">
        <table>
            <tr>
                <td class="lbl">Nomor Pendaftaran</td><td class="val">{{ $reg->registration_number }}</td>
                <td class="lbl">Tahun Ajaran</td><td class="val">{{ $reg->academicYear->name }}</td>
            </tr>
            <tr>
                <td class="lbl">Tanggal Pendaftaran</td><td class="val">{{ $reg->registered_at?->translatedFormat('d F Y H:i') }}</td>
                <td class="lbl">Grade</td><td class="val">{{ $reg->grade->code }} — {{ $reg->grade->name }}</td>
            </tr>
            <tr>
                <td class="lbl">Kampus</td><td class="val" style="font-weight:normal;">{{ $reg->campus->name }}</td>
                <td class="lbl">Kategori</td><td class="val" style="font-weight:normal;">{{ $reg->category->name }}</td>
            </tr>
            <tr>
                <td class="lbl">Status</td>
                <td colspan="3"><span class="status-pill">{{ $reg->status }}</span></td>
            </tr>
        </table>
    </div>

    <h2 class="section">Data Calon Murid</h2>
    <table class="data-tbl">
        <tr>
            <td class="lbl">Nama Lengkap</td><td class="val"><b>{{ $reg->full_name }}</b></td>
            <td class="lbl">Nama Panggilan</td><td class="val">{{ $reg->nickname }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Kelamin</td><td class="val">{{ $reg->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
            <td class="lbl">NISN</td><td class="val">{{ $reg->nisn ?: '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat, Tgl Lahir</td>
            <td class="val">{{ $reg->birth_place }},<br>{{ \Carbon\Carbon::parse($reg->birth_date)->translatedFormat('d F Y') }}</td>
            <td class="lbl">Usia pada {{ \Carbon\Carbon::parse($reg->age_reference_date)->translatedFormat('d F Y') }}</td>
            <td class="val">{{ $reg->age_years }} tahun<br>{{ $reg->age_months }} bulan {{ $reg->age_days }} hari</td>
        </tr>
        <tr>
            <td class="lbl">Status Keluarga</td><td class="val">{{ $reg->family_status }}</td>
            <td class="lbl">Anak Ke-</td><td class="val">{{ $reg->child_order }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td><td colspan="3">{{ $reg->address }}</td>
        </tr>
        <tr>
            <td class="lbl">Asal Sekolah</td><td class="val">{{ $reg->previous_school ?: '-' }}</td>
            <td class="lbl">No. HP</td><td class="val">{{ $reg->phone }}</td>
        </tr>
    </table>

    <h2 class="section">Data Orang Tua / Wali</h2>
    <table class="data-tbl">
        <tr><td class="lbl">Ayah</td><td class="val">{{ $reg->father_name }}</td><td class="lbl">Pekerjaan Ayah</td><td class="val">{{ $reg->father_job ?: '-' }}</td></tr>
        <tr><td class="lbl">Ibu</td><td class="val">{{ $reg->mother_name }}</td><td class="lbl">Pekerjaan Ibu</td><td class="val">{{ $reg->mother_job ?: '-' }}</td></tr>
        <tr><td class="lbl">Kakek Dari Ayah</td><td colspan="3">{{ $reg->grandfather_name }}</td></tr>
        <tr><td class="lbl">HP Ayah</td><td class="val">{{ $reg->father_phone ?: '-' }}</td><td class="lbl">HP Ibu</td><td class="val">{{ $reg->mother_phone ?: '-' }}</td></tr>
    </table>

    <table class="signature-tbl">
        <tr>
            <td>Yang menerima pendaftaran,</td>
            <td style="text-align:right;">Palembang, {{ now()->translatedFormat('d M Y') }}<br>Yang Mendaftar,</td>
        </tr>
        <tr><td class="space"></td><td class="space"></td></tr>
        <tr>
            <td><div style="border-top:1px solid #333;width:220px;padding-top:4px;font-weight:bold;">Administrasi</div></td>
            <td style="text-align:right;">
                <table style="margin-left:auto;"><tr><td style="border-top:1px solid #333;width:220px;padding-top:4px;font-weight:bold;text-align:left;">Nama Orangtua/Wali</td></tr></table>
            </td>
        </tr>
    </table>
</div>

<div class="foot">
    Dokumen ini dihasilkan sistem PORMIN · {{ now()->translatedFormat('d F Y H:i') }} · Verifikasi: {{ url('/verify/'.$reg->verification_token) }}
</div>
</body>
</html>
