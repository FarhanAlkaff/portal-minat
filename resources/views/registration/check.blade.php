@extends('layouts.public')
@section('title', 'Cek Pendaftaran')
@section('content')
<div class="container py-5" style="max-width:640px;">
    <h1 class="section-title mb-1 text-center">Cek Pendaftaran</h1>
    <p class="text-center text-muted mb-4">Masukkan nomor pendaftaran dan tanggal lahir calon murid.</p>
    @if($error)<div class="alert alert-danger">{{ $error }}</div>@endif
    <form method="POST" action="{{ route('registration.check') }}" class="p-4 bg-white rounded-4 shadow-sm">
        @csrf
        <div class="mb-3"><label class="form-label required">Nomor Pendaftaran</label><input name="registration_number" class="form-control" required value="{{ old('registration_number') }}" placeholder="Contoh: SD26-3-0001" data-testid="cek-input-number"></div>
        <div class="mb-3"><label class="form-label required">Tanggal Lahir</label><input type="date" name="birth_date" class="form-control" required value="{{ old('birth_date') }}" data-testid="cek-input-birth-date"></div>
        <button class="btn btn-azhar w-100" data-testid="cek-submit">Cek Sekarang</button>
    </form>
</div>
@endsection
