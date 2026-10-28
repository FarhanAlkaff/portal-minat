@extends('layouts.admin')
@section('title', 'Aturan Usia '.$grade->code)
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.grades') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Grade
    </a>
    <div class="d-flex align-items-center gap-3">
        <x-grade-badge :code="$grade->code" size="lg" />
        <div>
            <h1 class="section-title mb-0">Aturan Usia · {{ $grade->name }}</h1>
            <small class="text-muted">Kriteria batas umur calon peserta didik saat mendaftar pada jenjang ini</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 rounded-4 shadow-sm p-4">
            <form method="POST" action="{{ route('admin.grades.age-rule.save', $grade) }}">
                @csrf

                <!-- Usia Minimum -->
                <div class="p-3 rounded-3 mb-4" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-up-right-circle text-primary"></i>
                            <span>Batas Usia Minimum</span>
                        </div>
                        <small class="text-muted">Syarat minimal umur calon siswa</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tahun</label>
                            <input type="number" min="0" max="30" name="minimum_age_years" id="min_years" class="form-control" value="{{ $rule->minimum_age_years }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Bulan</label>
                            <input type="number" min="0" max="12" name="minimum_age_months" id="min_months" class="form-control" value="{{ $rule->minimum_age_months }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Hari</label>
                            <input type="number" min="0" max="31" name="minimum_age_days" id="min_days" class="form-control" value="{{ $rule->minimum_age_days }}">
                        </div>
                    </div>
                </div>

                <!-- Usia Maksimum -->
                <div class="p-3 rounded-3 mb-4" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-down-right-circle text-danger"></i>
                            <span>Batas Usia Maksimum</span>
                        </div>
                        <small class="text-muted">Opsional (kosongkan jika tanpa batas)</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tahun</label>
                            <input type="number" min="0" max="30" name="maximum_age_years" id="max_years" class="form-control" value="{{ $rule->maximum_age_years }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Bulan</label>
                            <input type="number" min="0" max="12" name="maximum_age_months" id="max_months" class="form-control" value="{{ $rule->maximum_age_months }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Hari</label>
                            <input type="number" min="0" max="31" name="maximum_age_days" id="max_days" class="form-control" value="{{ $rule->maximum_age_days }}">
                        </div>
                    </div>
                </div>

                <!-- Tanggal Acuan -->
                <div class="p-3 rounded-3 mb-4" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-event text-warning"></i>
                            <span>Tanggal Acuan Perhitungan</span>
                        </div>
                        <small class="text-muted">Default nasional: 1 Juli</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tanggal (Hari)</label>
                            <input type="number" min="1" max="31" name="reference_day" class="form-control" value="{{ $rule->reference_day ?? 1 }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Bulan (1 - 12)</label>
                            <input type="number" min="1" max="12" name="reference_month" class="form-control" value="{{ $rule->reference_month ?? 7 }}" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2 border-top">
                    <button class="btn btn-azhar px-4 py-2 rounded-3 shadow-xs" data-testid="btn-save-age-rule">
                        <i class="bi bi-save me-1"></i> Simpan Aturan Usia
                    </button>
                    <a href="{{ route('admin.grades') }}" class="btn btn-light border px-4 py-2 rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Informasi & Panduan -->
    <div class="col-lg-5">
        <div class="card border-0 rounded-4 shadow-sm p-4 mb-3" style="background:#FAF7FC;border:1px solid #ECE3F5 !important;">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Cara Perhitungan Usia</h6>
            <p class="text-muted small" style="line-height:1.6;">
                Sistem menghitung umur calon siswa berdasarkan <b>Tanggal Lahir</b> terhadap <b>Tanggal Acuan</b> pada tahun ajaran yang dipilih saat mendaftar.
            </p>
            <div class="p-3 rounded-3 bg-white border small mb-3">
                <div class="fw-semibold text-dark mb-1">Contoh:</div>
                <div class="text-muted">Jika acuan adalah <b>1 Juli 2026</b> dan anak lahir pada <b>15 Januari 2020</b>, maka umur anak saat acuan adalah <b>6 tahun, 5 bulan, 16 hari</b>.</div>
            </div>
            <div class="small text-muted">
                <i class="bi bi-check2-circle text-success me-1"></i> Jika memenuhi batas minimum & maksimum &rarr; Status <b>Memenuhi</b>.<br>
                <i class="bi bi-exclamation-circle text-warning me-1"></i> Jika kurang dari batas minimum &rarr; Status <b>Belum Memenuhi</b>.
            </div>
        </div>
    </div>
</div>
@endsection
