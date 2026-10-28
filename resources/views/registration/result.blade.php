@extends('layouts.public')
@section('title', 'Hasil Pendaftaran')

@section('content')
<div class="container py-5">
    @if(session('duplicate'))
        <div class="alert alert-warning"><i class="bi bi-info-circle me-2"></i><strong>Pendaftaran Sudah Ditemukan.</strong> Calon murid dengan data ini sudah terdaftar pada sistem PORMIN.</div>
    @elseif(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><strong>Pendaftaran Berhasil!</strong> Simpan nomor pendaftaran Anda untuk cek status.</div>
    @endif

    <div class="p-4 p-md-5 bg-white rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <span class="text-uppercase small" style="letter-spacing:.15em;color:var(--azhar-gold);">Bukti Pendaftaran</span>
                <h1 class="section-title mb-1">{{ $reg->registration_number }}</h1>
                <div class="text-muted">{{ $reg->full_name }} · {{ $reg->grade->code }} · TA {{ $reg->academicYear->name }}</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('registration.pdf', $reg->registration_number) }}" class="btn btn-gold" data-testid="btn-download-pdf"><i class="bi bi-download me-1"></i>Download PDF</a>
                @if($reg->observation_date)
                    <a href="{{ route('registration.observation-pdf', $reg->registration_number) }}" class="btn btn-outline-success" data-testid="btn-download-observation"><i class="bi bi-file-earmark-text me-1"></i>Surat Observasi</a>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <h5 class="section-title mb-3">Timeline Status</h5>
                <ul class="timeline list-unstyled">
                    @foreach($reg->histories->sortBy('id') as $h)
                        <li class="timeline-item"><strong>{{ $h->new_status }}</strong> <small class="text-muted">· {{ $h->created_at?->format('d M Y H:i') }}</small>@if($h->note)<div class="small text-muted">{{ $h->note }}</div>@endif</li>
                    @endforeach
                    <li class="timeline-item" style="opacity:.6;"><em>Status saat ini: <strong>{{ $reg->status }}</strong></em></li>
                </ul>

                @if($reg->observation_date)
                    <div class="alert alert-info mt-3">
                        <h6 class="mb-2"><i class="bi bi-calendar-event me-1"></i>Jadwal Observasi</h6>
                        <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($reg->observation_date)->translatedFormat('l, d F Y') }}</div>
                        <div><strong>Jam:</strong> {{ substr($reg->observation_time,0,5) }} WIB</div>
                        <div><strong>Lokasi:</strong> {{ $reg->observation_location }}</div>
                        @if($reg->observation_room)<div><strong>Ruang:</strong> {{ $reg->observation_room }}</div>@endif
                        @if($reg->observation_notes)<div class="mt-1"><small>{{ $reg->observation_notes }}</small></div>@endif
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3" style="background:var(--azhar-cream);">
                    <h6 class="mb-3">Ringkasan Pendaftaran</h6>
                    <div class="d-flex justify-content-between mb-1"><span>No. Pendaftaran</span><b>{{ $reg->registration_number }}</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Grade</span><b>{{ $reg->grade->code }}</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Tahun Ajaran</span><b>{{ $reg->academicYear->name }}</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Kampus</span><b>{{ $reg->campus->name }}</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Kategori</span><b>{{ $reg->category->name }}</b></div>
                    <div class="d-flex justify-content-between mb-2"><span>Usia pada {{ \Carbon\Carbon::parse($reg->age_reference_date)->translatedFormat('d M Y') }}</span><b>{{ $reg->age_years }}th {{ $reg->age_months }}bl</b></div>
                    <div class="text-center mt-2"><span class="status-pill status-{{ Str::slug($reg->status) }}" data-testid="registration-status">{{ $reg->status }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
