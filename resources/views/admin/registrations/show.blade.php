@extends('layouts.admin')
@section('title', 'Detail Pendaftaran '.$reg->registration_number)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.registrations.index') }}" class="text-muted small">← Data Pendaftaran</a>
        <h1 class="section-title mb-0">{{ $reg->registration_number }} · {{ $reg->full_name }}</h1>
        <div><span class="status-pill status-{{ \Illuminate\Support\Str::slug($reg->status) }}" data-testid="detail-status">{{ $reg->status }}</span></div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-success" href="{{ route('registration.pdf', $reg->registration_number) }}"><i class="bi bi-file-pdf me-1"></i>Bukti PDF</a>
        @if($reg->observation_date)<a class="btn btn-outline-primary" href="{{ route('registration.observation-pdf', $reg->registration_number) }}"><i class="bi bi-envelope-paper me-1"></i>Surat Observasi</a>@endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="p-3 bg-white rounded-4 shadow-sm mb-3">
            <h5 class="section-title">Data Calon Murid</h5>
            <table class="table table-sm mb-0">
                <tr><td width="30%">Nama Lengkap</td><td><b>{{ $reg->full_name }}</b> ({{ $reg->nickname }})</td></tr>
                <tr><td>Jenis Kelamin</td><td>{{ $reg->gender==='L'?'Laki-Laki':'Perempuan' }}</td></tr>
                <tr><td>NISN</td><td>{{ $reg->nisn ?: '-' }}</td></tr>
                <tr><td>Tempat, Tgl Lahir</td><td>{{ $reg->birth_place }}, {{ $reg->birth_date?->translatedFormat('d F Y') }}</td></tr>
                <tr><td>Usia pada {{ \Carbon\Carbon::parse($reg->age_reference_date)->translatedFormat('d F Y') }}</td><td>{{ $reg->age_years }}th {{ $reg->age_months }}bl {{ $reg->age_days }}hr</td></tr>
                <tr><td>Status Keluarga</td><td>{{ $reg->family_status }} · Anak ke-{{ $reg->child_order }}</td></tr>
                <tr><td>Alamat</td><td>{{ $reg->address }}</td></tr>
                <tr><td>Asal Sekolah</td><td>{{ $reg->previous_school ?: '-' }}</td></tr>
                <tr><td>No. HP</td><td>{{ $reg->phone }}</td></tr>
                <tr><td>Grade / TA / Kampus</td><td><x-grade-badge :grade="$reg->grade" /> · {{ $reg->academicYear->name }} · {{ $reg->campus->name }}</td></tr>
            </table>
        </div>
        <div class="p-3 bg-white rounded-4 shadow-sm mb-3">
            <h5 class="section-title">Data Orang Tua</h5>
            <table class="table table-sm mb-0">
                <tr><td width="30%">Ayah</td><td>{{ $reg->father_name }} · {{ $reg->father_job ?: '-' }} · {{ $reg->father_phone ?: '-' }}</td></tr>
                <tr><td>Ibu</td><td>{{ $reg->mother_name }} · {{ $reg->mother_job ?: '-' }} · {{ $reg->mother_phone ?: '-' }}</td></tr>
                <tr><td>Kakek Dari Ayah</td><td>{{ $reg->grandfather_name }}</td></tr>
            </table>
        </div>

        <div class="p-3 bg-white rounded-4 shadow-sm mb-3">
            <h5 class="section-title">Validasi Administrasi</h5>
            <form method="POST" action="{{ route('admin.registrations.validate', $reg) }}" class="row g-2">
                @csrf
                <div class="col-md-6"><label class="form-label">Status Administrasi</label>
                    <select name="administrative_status" class="form-select" data-testid="input-admin-status">
                        @foreach(['Menunggu Validasi','Lolos Administrasi','Tidak Lolos Administrasi'] as $s)<option @selected($reg->administrative_status===$s)>{{ $s }}</option>@endforeach
                    </select></div>
                <div class="col-md-6"><label class="form-label">Kelayakan Observasi</label>
                    <select name="observation_eligibility" class="form-select" data-testid="input-eligibility">
                        @foreach(['Belum Ditentukan','Layak Observasi','Tidak Layak Observasi'] as $s)<option @selected($reg->observation_eligibility===$s)>{{ $s }}</option>@endforeach
                    </select></div>
                <div class="col-12"><label class="form-label">Catatan Admin</label><textarea name="note" class="form-control" rows="2" placeholder="Opsional"></textarea></div>
                <div class="col-12 text-end"><button class="btn btn-azhar" data-testid="btn-save-validation">Simpan Validasi</button></div>
            </form>
        </div>

        <div class="p-3 bg-white rounded-4 shadow-sm mb-3">
            <h5 class="section-title">Jadwal Observasi</h5>
            <form method="POST" action="{{ route('admin.registrations.observation', $reg) }}" class="row g-2">
                @csrf
                <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="observation_date" class="form-control" value="{{ $reg->observation_date?->format('Y-m-d') }}" required data-testid="input-obs-date"></div>
                <div class="col-md-3"><label class="form-label">Jam</label><input type="time" name="observation_time" class="form-control" value="{{ $reg->observation_time }}" required data-testid="input-obs-time"></div>
                <div class="col-md-5"><label class="form-label">Lokasi</label><input name="observation_location" class="form-control" value="{{ $reg->observation_location ?: 'Al-Azhar Cairo Palembang' }}" required></div>
                <div class="col-md-6"><label class="form-label">Ruang</label><input name="observation_room" class="form-control" value="{{ $reg->observation_room }}" placeholder="Contoh: Ruang Observasi TK"></div>
                <div class="col-md-6"><label class="form-label">Catatan</label><input name="observation_notes" class="form-control" value="{{ $reg->observation_notes }}"></div>
                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted"><i class="bi bi-whatsapp me-1" style="color:#25d366;"></i>Notifikasi WA otomatis dikirim ke orang tua setelah simpan</small>
                    <button class="btn btn-azhar" data-testid="btn-save-observation">Simpan Jadwal & Kirim WA</button>
                </div>
            </form>
            @php $wa = session('wa'); @endphp
            @if($wa)
                <div class="alert alert-{{ $wa['sent'] ? 'success' : ($wa['wa_link'] ? 'info' : 'warning') }} mt-3" data-testid="wa-alert">
                    @if($wa['sent'])
                        <i class="bi bi-check-circle me-1"></i>Pesan WhatsApp <b>terkirim otomatis</b> ke {{ $wa['phone'] }} (via Fonnte).
                    @elseif($wa['wa_link'])
                        <i class="bi bi-whatsapp me-1" style="color:#25d366;"></i>Pesan siap dikirim ke <b>{{ $wa['phone'] }}</b>. Klik tombol berikut untuk membuka WhatsApp:
                        <div class="mt-2"><a href="{{ $wa['wa_link'] }}" target="_blank" class="btn btn-success btn-sm" data-testid="btn-wa-open"><i class="bi bi-whatsapp me-1"></i>Buka WhatsApp & Kirim</a></div>
                    @else
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $wa['error'] ?? 'Tidak dapat mengirim WA.' }}
                    @endif
                </div>
            @endif
            @if($reg->observation_date)
                <form method="POST" action="{{ route('admin.registrations.resend-wa', $reg) }}" class="mt-2 d-inline">@csrf
                    <button class="btn btn-sm btn-outline-success" data-testid="btn-resend-wa"><i class="bi bi-arrow-repeat me-1"></i>Kirim Ulang WA</button>
                </form>
            @endif
        </div>

        <div class="p-3 bg-white rounded-4 shadow-sm">
            <h5 class="section-title">Hasil Observasi & Keputusan</h5>
            <form method="POST" action="{{ route('admin.registrations.observation-result', $reg) }}" class="row g-2">
                @csrf
                <div class="col-md-4"><label class="form-label">Hasil Observasi</label>
                    <select name="observation_result" class="form-select" data-testid="input-obs-result">
                        <option value="Lulus" @selected($reg->observation_result==='Lulus')>Lulus</option>
                        <option value="Tidak Lulus" @selected($reg->observation_result==='Tidak Lulus')>Tidak Lulus</option>
                    </select></div>
                <div class="col-md-4"><label class="form-label">Keputusan</label>
                    <select name="decision" class="form-select" data-testid="input-decision">
                        <option value="Belum">Belum</option>
                        <option value="Diterima" @selected($reg->status==='Diterima')>Diterima</option>
                        <option value="Ditolak" @selected($reg->status==='Ditolak')>Ditolak</option>
                    </select></div>
                <div class="col-md-4 d-flex align-items-end"><button class="btn btn-azhar w-100" data-testid="btn-save-result">Simpan Hasil</button></div>
                <div class="col-12"><label class="form-label">Catatan Hasil</label><textarea name="note" class="form-control" rows="2"></textarea></div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="p-3 bg-white rounded-4 shadow-sm mb-3">
            <h5 class="section-title">Timeline Status</h5>
            <ul class="timeline list-unstyled">
                @foreach($reg->histories->sortBy('id') as $h)
                    <li class="timeline-item"><b>{{ $h->new_status }}</b><small class="text-muted d-block">{{ $h->created_at?->format('d M Y H:i') }} · {{ $h->changer->name ?? 'Sistem' }}</small>@if($h->note)<div class="small">{{ $h->note }}</div>@endif</li>
                @endforeach
            </ul>
        </div>
        @if($reg->notes->count())
        <div class="p-3 bg-white rounded-4 shadow-sm">
            <h5 class="section-title">Catatan Admin</h5>
            @foreach($reg->notes as $n)
                <div class="mb-2 p-2 rounded" style="background:var(--azhar-cream);"><small class="text-muted">{{ $n->note_type }} · {{ $n->created_at?->format('d M Y H:i') }} · {{ $n->admin->name ?? '-' }}</small><div>{{ $n->note }}</div></div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
