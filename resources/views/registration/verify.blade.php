@extends('layouts.public')
@section('title', 'Verifikasi Pendaftaran')
@section('content')
<div class="container py-5" style="max-width:640px;">
    <div class="p-4 bg-white rounded-4 shadow-sm text-center">
        <i class="bi bi-shield-check display-1" style="color:var(--azhar-green);"></i>
        <h1 class="section-title mt-2">Pendaftaran Valid</h1>
        <p class="text-muted">Data ini terverifikasi dari sistem PORMIN Al-Azhar Cairo Palembang.</p>
        <div class="text-start mt-3">
            <div class="d-flex justify-content-between mb-1"><span>No. Pendaftaran</span><b>{{ $reg->registration_number }}</b></div>
            <div class="d-flex justify-content-between mb-1"><span>Nama</span><b>{{ $reg->full_name }}</b></div>
            <div class="d-flex justify-content-between mb-1"><span>Grade</span><b>{{ $reg->grade->code }}</b></div>
            <div class="d-flex justify-content-between mb-1"><span>Tahun Ajaran</span><b>{{ $reg->academicYear->name }}</b></div>
            <div class="d-flex justify-content-between mb-1"><span>Status</span><b>{{ $reg->status }}</b></div>
        </div>
    </div>
</div>
@endsection
