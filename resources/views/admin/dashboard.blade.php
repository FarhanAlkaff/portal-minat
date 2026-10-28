@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<!-- Hero Welcome & Quick Actions -->
<div class="card border-0 rounded-4 text-white p-4 mb-4 position-relative overflow-hidden shadow-sm" style="background:linear-gradient(135deg, #4A156D 0%, #2A093D 60%, #170222 100%);">
    <div class="position-relative z-1">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge rounded-pill px-3 py-1 mb-2" style="background:rgba(255,255,255,0.15);backdrop-filter:blur(6px);color:#E9D8FD;font-weight:500;">
                    <i class="bi bi-stars me-1"></i>Portal Minat (PORMIN)
                </span>
                <h2 class="section-title mb-1 text-white" style="font-size:1.85rem;">
                    Dashboard Manajemen PMB
                    @if(!auth()->user()->isSuperAdmin())
                        <span class="badge rounded-pill align-middle fs-6 px-3 py-1 ms-2" style="background:#3B82F6;color:#FFFFFF;font-weight:600;">
                            <i class="bi bi-mortarboard-fill me-1"></i>Jenjang: {{ auth()->user()->assignedGradesLabel() }}
                        </span>
                    @endif
                </h2>
                <p class="mb-0 text-white-50 small" style="max-width:550px;">
                    Pantau statistik penerimaan calon murid baru Al-Azhar Cairo Palembang secara real-time, jadwal observasi, dan status verifikasi berkas.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.registrations.create') }}" class="btn btn-gold rounded-pill px-3 py-2 shadow-sm" data-testid="btn-input-lama">
                    <i class="bi bi-person-plus-fill me-1"></i>Input Pendaftaran
                </a>
                <a href="{{ route('admin.registrations.import') }}" class="btn btn-outline-light rounded-pill px-3 py-2">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i>Import CSV
                </a>
                <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-light rounded-pill px-3 py-2">
                    <i class="bi bi-table me-1"></i>Semua Data
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 6 Interactive KPI Cards -->
<div class="row g-3 mb-4">
    @php
        $kpi = [
            [
                'title' => 'Total Pendaftar',
                'key' => 'total',
                'icon' => 'people-fill',
                'bg' => '#F3E8FF',
                'color' => '#6B2D8C',
                'border' => '#D8B4FE',
                'url' => route('admin.registrations.index'),
                'desc' => 'Semua data masuk'
            ],
            [
                'title' => 'Menunggu Validasi',
                'key' => 'menunggu',
                'icon' => 'hourglass-split',
                'bg' => '#FEF3C7',
                'color' => '#D97706',
                'border' => '#FCD34D',
                'url' => route('admin.registrations.index', ['administrative_status' => 'Menunggu Validasi']),
                'desc' => 'Perlu ditinjau'
            ],
            [
                'title' => 'Lolos Administrasi',
                'key' => 'lolos_admin',
                'icon' => 'patch-check-fill',
                'bg' => '#EEF2FF',
                'color' => '#4F46E5',
                'border' => '#C7D2FE',
                'url' => route('admin.registrations.index', ['administrative_status' => 'Lolos Administrasi']),
                'desc' => 'Berkas valid'
            ],
            [
                'title' => 'Layak Observasi',
                'key' => 'layak',
                'icon' => 'clipboard-check-fill',
                'bg' => '#CCFBF1',
                'color' => '#0D9488',
                'border' => '#99F6E4',
                'url' => route('admin.registrations.index', ['observation_eligibility' => 'Layak Observasi']),
                'desc' => 'Siap dijadwalkan'
            ],
            [
                'title' => 'Dipanggil Observasi',
                'key' => 'dipanggil',
                'icon' => 'megaphone-fill',
                'bg' => '#FFE4E6',
                'color' => '#E11D48',
                'border' => '#FECDD3',
                'url' => route('admin.registrations.index', ['status' => 'Dipanggil Observasi']),
                'desc' => 'Jadwal telah dikirim'
            ],
            [
                'title' => 'Diterima',
                'key' => 'diterima',
                'icon' => 'check-circle-fill',
                'bg' => '#DCFCE7',
                'color' => '#059669',
                'border' => '#86EFAC',
                'url' => route('admin.registrations.index', ['status' => 'Diterima']),
                'desc' => 'Resmi bergabung'
            ],
        ];
    @endphp

    @foreach($kpi as $item)
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ $item['url'] }}" class="text-decoration-none">
                <div class="kpi-card h-100 d-flex flex-column justify-content-between" style="border-top:3.5px solid {{ $item['color'] }};">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-icon-wrap" style="background:{{ $item['bg'] }};color:{{ $item['color'] }};">
                            <i class="bi bi-{{ $item['icon'] }} fs-5"></i>
                        </div>
                        <i class="bi bi-arrow-up-right small text-muted"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-truncate mb-1">{{ $item['title'] }}</div>
                        <div class="kpi-num" data-testid="kpi-{{ $item['key'] }}">{{ $counts[$item['key']] }}</div>
                        <div class="text-muted mt-1" style="font-size:0.72rem;">{{ $item['desc'] }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<!-- FULL WIDTH: Distribusi Pendaftaran & Rekap Target per Kampus -->
<div class="admin-card p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
        <div>
            <h5 class="section-title mb-0" style="font-size:1.15rem;">Distribusi Pendaftaran & Target Kuota</h5>
            <small class="text-muted">Rekap pendaftaran per kampus untuk Tahun Ajaran <b>{{ $selectedYear->name }}</b></small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Dropdown Pilih Tahun -->
            <form method="GET" action="{{ route('admin.dashboard') }}" class="m-0" id="filterYearForm">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted small border-secondary-subtle">
                        <i class="bi bi-calendar3 me-1"></i>Tahun:
                    </span>
                    <select name="dist_year_id" class="form-select form-select-sm fw-semibold border-secondary-subtle" onchange="this.form.submit()" style="font-size:0.8rem;background-color:#FAF7FC;min-width:140px;">
                        @foreach($academicYears as $y)
                            <option value="{{ $y->id }}" {{ $selectedYear->id == $y->id ? 'selected' : '' }}>
                                TA {{ $y->name }} {{ $y->start_year > now()->year ? '(Akan Datang)' : ($y->is_active ? '(Aktif)' : '') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            <!-- Tombol Atur Target -->
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#targetModal" title="Atur Target Kuota Murid" style="border:1px solid #9333EA;color:#6B2D8C;font-size:0.78rem;font-weight:600;background:#FAF5FF;">
                <i class="bi bi-bullseye"></i> Atur Target
            </button>
        </div>
    </div>

    <!-- Banner Rekap Capaian Target -->
    <div class="p-3 rounded-3 mb-4" style="background:linear-gradient(135deg, #F9F5FF 0%, #F3E8FF 100%);border:1px solid #E9D8FD;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-1" style="background:#6B2D8C;color:#fff;font-size:0.75rem;">
                    TA {{ $selectedYear->name }}
                </span>
                @if($selectedYear->start_year > now()->year)
                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.7rem;">Periode Pendaftaran Baru (Akan Datang)</span>
                @endif
            </div>
            <div class="text-end">
                <span class="text-muted small">Total Terdata:</span>
                <span class="fw-bold text-dark fs-5 ms-1">{{ $totalRegisteredInYear }}</span>
                <span class="text-muted small">/ {{ $totalTargetInYear > 0 ? $totalTargetInYear : '-' }} Target Murid</span>
                <span class="badge rounded-pill ms-2" style="background:#EDE9FE;color:#6B2D8C;font-weight:700;">
                    {{ $totalTargetPercent }}% Tercapai
                </span>
            </div>
        </div>
        <div class="progress mt-2" style="height:10px;border-radius:999px;background:#EDE9FE;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $totalTargetPercent }}%; background:#6B2D8C;"></div>
        </div>
    </div>

    <!-- Kampus KM 3 & KM 7 Berdampingan (Side by Side) -->
    <div class="row g-3">
        @foreach($campusDistribution as $campDist)
            <div class="col-md-6">
                <div class="p-3 rounded-3 border h-100 d-flex flex-column" style="background:#fff;border-color:#ECE7F5 !important;">
                    <!-- Header Kampus -->
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:8px;background:#F3E8FF;color:#6B2D8C;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-buildings-fill fs-6"></i>
                            </div>
                            <span class="fw-bold text-dark" style="font-size:0.92rem;">{{ $campDist->campus->name }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark fs-6">{{ $campDist->total_registered }} Murid</span>
                            @if($campDist->total_target > 0)
                                <div class="text-muted" style="font-size:0.7rem;">Target: {{ $campDist->total_target }} ({{ $campDist->percent }}%)</div>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Bar Kampus -->
                    <div class="progress progress-slim my-2" style="background:#EDE9FE;">
                        <div class="progress-bar" style="width: {{ $campDist->percent }}%; background:#6B2D8C;"></div>
                    </div>

                    <!-- Sub-list Jenjang di bawah kampus (hanya yang aktif) -->
                    <div class="mt-2 pt-2 border-top flex-grow-1" style="border-color:#F5EEFC !important;">
                        <div class="d-flex align-items-center justify-content-between text-muted small fw-bold mb-2" style="font-size:0.7rem;letter-spacing:0.04em;">
                            <span>JENJANG AKTIF</span>
                            <span>PENDAFTAR / TARGET</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach($campDist->grades as $gData)
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background:#FAF7FC;">
                                    <div class="d-flex align-items-center gap-2" style="min-width:130px;">
                                        <span class="badge fw-bold" style="background:{{ $gData->color['bg'] }};color:{{ $gData->color['text'] }};border-radius:6px;font-size:0.74rem;">
                                            {{ $gData->grade->code }}
                                        </span>
                                        <span class="small fw-semibold text-dark text-truncate" style="max-width:120px;" title="{{ $gData->grade->name }}">
                                            {{ $gData->grade->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end">
                                        <div class="progress d-none d-sm-block flex-grow-1" style="height:6px;max-width:90px;background:#E9D8FD;border-radius:999px;">
                                            <div class="progress-bar" style="width: {{ $gData->percent }}%; background: {{ $gData->color['bar'] }};"></div>
                                        </div>
                                        <div class="text-end" style="min-width:85px;">
                                            <span class="fw-bold text-dark small">{{ $gData->count }}</span>
                                            @if($gData->target > 0)
                                                <span class="text-muted" style="font-size:0.7rem;">/ {{ $gData->target }} ({{ $gData->percent }}%)</span>
                                            @else
                                                <span class="text-muted" style="font-size:0.7rem;">(Target: -)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Balanced Lower Grid (7 : 5) -->
<div class="row g-4">
    <!-- Left Column: Pendaftaran Terbaru & Statistik Global per Jenjang -->
    <div class="col-lg-7 d-flex flex-column gap-4">
        <!-- Pendaftaran Terbaru -->
        <div class="admin-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="section-title mb-0" style="font-size:1.15rem;">Pendaftaran Terbaru</h5>
                    <small class="text-muted">Calon murid yang baru mendaftar di sistem</small>
                </div>
                <a href="{{ route('admin.registrations.index') }}" class="btn btn-sm btn-azhar rounded-pill px-3">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @if($recentRegistrations->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Belum ada data pendaftar baru yang masuk.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-uppercase" style="font-size:0.75rem;letter-spacing:0.05em;color:#6B7280;">
                                <th>No. Reg</th>
                                <th>Nama Calon Murid</th>
                                <th>Jenjang</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRegistrations as $reg)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark font-monospace" style="font-size:0.83rem;">
                                            {{ $reg->registration_number }}
                                        </span>
                                        <div class="text-muted" style="font-size:0.7rem;">
                                            {{ $reg->registered_at ? $reg->registered_at->diffForHumans() : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $reg->full_name }}</div>
                                        <div class="text-muted" style="font-size:0.72rem;">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $reg->campus->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <x-grade-badge :grade="$reg->grade" />
                                    </td>
                                    <td>
                                        @php
                                            $st = $reg->status;
                                            $cls = match($st) {
                                                'Terdaftar' => 'status-terdaftar',
                                                'Menunggu Validasi' => 'status-menunggu',
                                                'Lolos Administrasi' => 'status-lolos',
                                                'Layak Observasi' => 'status-layak',
                                                'Dipanggil Observasi' => 'status-dipanggil',
                                                'Diterima' => 'status-diterima',
                                                default => 'status-ditolak'
                                            };
                                        @endphp
                                        <span class="status-pill {{ $cls }}">{{ $st }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.registrations.show', $reg->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" title="Buka Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Statistik Global per Grade -->
        <div class="admin-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="section-title mb-0" style="font-size:1.15rem;">Statistik Keseluruhan per Jenjang</h5>
                    <small class="text-muted">Akumulasi pendaftar di tiap jenjang sekolah</small>
                </div>
                <a href="{{ route('admin.grades') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-gear-fill me-1"></i>Aturan Usia
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase" style="font-size:0.75rem;letter-spacing:0.05em;color:#6B7280;">
                            <th>Jenjang</th>
                            <th style="min-width:130px;">Sebaran</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Lolos Admin</th>
                            <th class="text-center">Layak Obs</th>
                            <th class="text-center">Diterima</th>
                            <th class="text-end">Filter</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perGrade as $g)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <x-grade-badge :code="$g->code" size="lg" />
                                        <span class="fw-semibold text-dark text-truncate" style="max-width:130px;" title="{{ $g->name }}">
                                            {{ $g->name }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1 progress-slim">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $g->percentage }}%; background: {{ $g->color['bar'] }};" aria-valuenow="{{ $g->percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-muted small" style="font-size:0.72rem;min-width:32px;">{{ $g->percentage }}%</span>
                                    </div>
                                </td>
                                <td class="text-center fw-bold text-dark">{{ $g->total }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
                                        {{ $g->lolos_admin }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
                                        {{ $g->layak }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.75rem;">
                                        {{ $g->diterima }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.registrations.index', ['grade_id' => $g->id]) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1" title="Lihat Pendaftar {{ $g->code }}">
                                        <i class="bi bi-funnel-fill text-muted"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Agenda Observasi, Kategori Siswa, & Pintasan Utilitas -->
    <div class="col-lg-5 d-flex flex-column gap-4">
        <!-- Jadwal Observasi Mendatang -->
        <div class="admin-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="section-title mb-0" style="font-size:1.15rem;">Agenda Observasi</h5>
                    <small class="text-muted">Panggilan yang telah dijadwalkan</small>
                </div>
                <span class="badge rounded-pill bg-purple-subtle text-purple px-2 py-1" style="background:#F3E8FF;color:#6B2D8C;font-size:0.75rem;">
                    {{ $upcomingObservations->count() }} Terjadwal
                </span>
            </div>

            @if($upcomingObservations->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar2-check fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Belum ada jadwal observasi yang ditetapkan.
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($upcomingObservations as $obs)
                        <div class="p-3 rounded-3 border d-flex flex-column gap-1" style="background:#FAF7FC;border-color:#E9D8FD !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark" style="font-size:0.88rem;">{{ $obs->full_name }}</span>
                                <x-grade-badge :grade="$obs->grade" size="sm" />
                            </div>
                            <div class="text-muted small d-flex flex-wrap gap-2" style="font-size:0.76rem;">
                                <span><i class="bi bi-calendar-event me-1 text-primary"></i>{{ \Carbon\Carbon::parse($obs->observation_date)->translatedFormat('d M Y') }}</span>
                                <span><i class="bi bi-clock me-1 text-primary"></i>{{ substr($obs->observation_time, 0, 5) }} WIB</span>
                                @if($obs->observation_room)
                                    <span><i class="bi bi-door-open me-1 text-primary"></i>{{ $obs->observation_room }}</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1 pt-1 border-top" style="border-color:#F3E8FF !important;">
                                <small class="text-muted font-monospace" style="font-size:0.7rem;">{{ $obs->registration_number }}</small>
                                <a href="{{ route('admin.registrations.show', $obs->id) }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold" style="font-size:0.75rem;color:#6B2D8C;">
                                    Detail / Notifikasi WA <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Kategori Siswa -->
        <div class="admin-card p-4">
            <h5 class="section-title mb-3 pb-2 border-bottom" style="font-size:1.15rem;">Kategori Calon Siswa (TA {{ $selectedYear->name }})</h5>
            <div class="row g-2">
                @php $totCat = max($categories->sum('count'), 1); @endphp
                @foreach($categories as $cat)
                    @php $pct = round(($cat->count / $totCat) * 100); @endphp
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center border" style="background:#F8F9FA;">
                            <div class="text-muted small mb-1" style="font-size:0.75rem;">{{ $cat->name }}</div>
                            <div class="fs-4 fw-bold" style="color:var(--azhar-green-dark);font-family:'Fraunces',serif;">
                                {{ $cat->count }}
                            </div>
                            <span class="badge rounded-pill bg-light text-muted border" style="font-size:0.7rem;">{{ $pct }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pintasan Cepat & Master Data -->
        <div class="admin-card p-4">
            <h5 class="section-title mb-3 pb-2 border-bottom" style="font-size:1.15rem;">Pintasan & Utilitas</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.registrations.export') }}" class="btn btn-outline-success d-flex align-items-center justify-content-between p-2 px-3 rounded-3">
                    <span class="small fw-semibold"><i class="bi bi-file-earmark-excel me-2 fs-6"></i>Ekspor Data Pendaftar (CSV)</span>
                    <i class="bi bi-download"></i>
                </a>
                <a href="{{ route('admin.registrations.import.template') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-2 px-3 rounded-3">
                    <span class="small fw-semibold"><i class="bi bi-file-earmark-arrow-down me-2 fs-6"></i>Unduh Template Import CSV</span>
                    <i class="bi bi-file-earmark-code"></i>
                </a>
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.information') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-2 px-3 rounded-3">
                        <span class="small fw-semibold"><i class="bi bi-megaphone me-2 fs-6"></i>Ubah Running Text / Logo</span>
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-between p-2 px-3 rounded-3">
                        <span class="small fw-semibold"><i class="bi bi-person-gear me-2 fs-6"></i>Kelola Akun Admin (CRUD)</span>
                        <i class="bi bi-arrow-right-short fs-5"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Atur Target Kuota Pendaftaran -->
<div class="modal fade" id="targetModal" tabindex="-1" aria-labelledby="targetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3" style="background:#FAF7FC;">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="targetModalLabel" style="font-family:'Fraunces',serif;">
                        <i class="bi bi-bullseye me-1 text-primary"></i> Atur Target Kuota Murid
                    </h5>
                    <small class="text-muted">Tahun Ajaran: <b>{{ $selectedYear->name }}</b></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.dashboard.save-targets') }}">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $selectedYear->id }}">
                <div class="modal-body p-4" style="max-height:70vh;overflow-y:auto;">
                    <p class="text-muted small mb-3">
                        Tentukan target kuota penerimaan murid baru untuk masing-masing jenjang di tiap kampus pada Tahun Ajaran <b>{{ $selectedYear->name }}</b>. Angka ini akan menjadi acuan rekap capaian pendaftaran tahunan.
                    </p>

                    <div class="d-flex flex-column gap-4">
                        @foreach($campuses as $camp)
                            <div class="p-3 rounded-3 border" style="background:#FDFBFF;border-color:#EDE5F5 !important;">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div style="width:28px;height:28px;border-radius:6px;background:#6B2D8C;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.8rem;">
                                        <i class="bi bi-buildings-fill"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $camp->name }}</h6>
                                </div>

                                <div class="row g-3">
                                    @foreach($activeGrades as $grd)
                                        @php
                                            $val = \App\Models\InformationSetting::get("target_{$selectedYear->id}_{$camp->id}_{$grd->id}", '');
                                        @endphp
                                        <div class="col-sm-6 col-md-4">
                                            <label class="form-label small fw-semibold text-muted mb-1" style="font-size:0.75rem;">
                                                Target {{ $grd->code }} ({{ $grd->name }})
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="targets[{{ $camp->id }}][{{ $grd->id }}]" class="form-control fw-bold" placeholder="0" min="0" value="{{ $val }}">
                                                <span class="input-group-text small text-muted" style="font-size:0.75rem;">Murid</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4" style="background:#FAF7FC;">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-azhar rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Target
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
