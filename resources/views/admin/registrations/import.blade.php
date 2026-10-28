@extends('layouts.admin')
@section('title', 'Import Batch Pendaftaran')
@section('content')
<div class="d-flex justify-content-between mb-3"><h1 class="section-title mb-0">Import Batch Pendaftar</h1><a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary">← Kembali</a></div>
<p class="text-muted">Upload file CSV berisi data pendaftar lama. Sistem akan menjalankan validasi usia, deteksi duplikat, dan generate nomor pendaftaran otomatis untuk setiap baris.</p>

@php $result = session('import_result'); @endphp
@if($result)
    <div class="alert alert-{{ $result['failed']==0 ? 'success' : 'warning' }} shadow-sm" data-testid="import-result">
        <b><i class="bi bi-check-circle me-1"></i>{{ $result['success'] }} baris berhasil diimport</b>
        @if($result['failed']>0) · <b><i class="bi bi-x-circle me-1"></i>{{ $result['failed'] }} baris gagal</b>@endif
        @if(!empty($result['created']))<div class="mt-2 small">Nomor yang dibuat: {{ implode(', ', array_slice($result['created'],0,20)) }}@if(count($result['created'])>20) ...@endif</div>@endif
        @if(!empty($result['errors']))
            <details class="mt-2"><summary>Lihat detail error ({{ count($result['errors']) }})</summary>
            <ul class="mb-0 mt-2 small">@foreach($result['errors'] as $e)<li>{{ $e }}</li>@endforeach</ul>
            </details>
        @endif
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('admin.registrations.import.store') }}" enctype="multipart/form-data" class="p-4 bg-white rounded-4 shadow-sm">
            @csrf
            <h5 class="section-title mb-3"><i class="bi bi-file-earmark-spreadsheet me-2" style="color:var(--azhar-gold);"></i>Upload File CSV</h5>
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <div class="mb-3">
                <label class="form-label required">Pilih File CSV</label>
                <input type="file" name="csv" accept=".csv,text/csv" required class="form-control" data-testid="import-file">
                <small class="text-muted">Max 5MB · Format UTF-8 · Pemisah koma</small>
            </div>
            <button class="btn btn-azhar" data-testid="btn-import-submit"><i class="bi bi-upload me-1"></i>Import Sekarang</button>
            <a href="{{ route('admin.registrations.import.template') }}" class="btn btn-outline-primary" data-testid="btn-import-template"><i class="bi bi-download me-1"></i>Download Template</a>
        </form>
    </div>
    <div class="col-lg-5">
        <div class="p-4 bg-white rounded-4 shadow-sm">
            <h6 class="section-title mb-3">Panduan Kolom CSV</h6>
            <ol class="small mb-3" style="padding-left:1.1rem;">
                <li>Download template terlebih dahulu untuk contoh format</li>
                <li>Kolom wajib: <code>academic_year_name, grade_code, full_name, birth_place, birth_date, father_name, mother_name, grandfather_name</code></li>
                <li>Format tanggal: <code>YYYY-MM-DD</code></li>
                <li>Grade code: <code>PG, TK-A, TK-B, SD, SMP, SMA</code></li>
                <li>Gender: <code>L</code> atau <code>P</code></li>
                <li>Baris duplikat akan otomatis di-skip (tidak error, tapi tercatat)</li>
                <li>Nomor pendaftaran dibuat otomatis mengikuti sequence tahun+grade</li>
            </ol>
            <div class="alert alert-info small mb-0"><i class="bi bi-info-circle me-1"></i>Setiap baris tetap melalui validasi usia terhadap tanggal 1 Juli tahun ajaran yang bersangkutan.</div>
        </div>
    </div>
</div>
@endsection
