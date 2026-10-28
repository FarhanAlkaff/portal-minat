@extends('layouts.admin')
@section('title', 'Input Data Pendaftaran')
@section('content')
<div class="d-flex justify-content-between mb-3"><h1 class="section-title mb-0">Input Data Pendaftar</h1><a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary">← Kembali</a></div>
<p class="text-muted">Gunakan halaman ini untuk mendata pendaftar yang berasal dari sistem lama / offline. Semua validasi (usia, duplicate, nomor pendaftaran) tetap dijalankan otomatis.</p>

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ route('admin.registrations.store') }}" class="row g-3">
    @csrf
    <div class="col-md-4"><label class="form-label required">Tahun Ajaran</label><select name="academic_year_id" class="form-select" required>@foreach($years as $y)<option value="{{ $y->id }}" @selected(old('academic_year_id')==$y->id)>{{ $y->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label required">Grade</label><select name="grade_id" class="form-select" required>@foreach($grades as $g)<option value="{{ $g->id }}" @selected(old('grade_id')==$g->id)>{{ $g->code }} — {{ $g->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label required">Kategori</label><select name="student_category_id" class="form-select" required>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('student_category_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
    <div class="col-md-12"><label class="form-label required">Kampus</label><select name="campus_id" class="form-select" required>@foreach($campuses as $c)<option value="{{ $c->id }}" @selected(old('campus_id')==$c->id)>{{ $c->name }} — {{ $c->address }}</option>@endforeach</select></div>

    <div class="col-md-6 p-3 bg-white rounded-4 shadow-sm">
        <h6 class="section-title mb-2">Data Anak</h6>
        <div class="mb-2"><label class="form-label required">Nama Lengkap</label><input name="full_name" class="form-control" value="{{ old('full_name') }}" required></div>
        <div class="mb-2"><label class="form-label required">Nama Panggilan</label><input name="nickname" class="form-control" value="{{ old('nickname') }}" required></div>
        <div class="row"><div class="col-md-6 mb-2"><label class="form-label required">Jenis Kelamin</label><select name="gender" class="form-select" required><option value="L" @selected(old('gender')=='L')>Laki-Laki</option><option value="P" @selected(old('gender')=='P')>Perempuan</option></select></div>
        <div class="col-md-6 mb-2"><label class="form-label">NISN</label><input name="nisn" class="form-control" value="{{ old('nisn') }}"></div></div>
        <div class="mb-2"><label class="form-label">Kelas</label><input name="class" class="form-control" value="{{ old('class') }}"></div>
        <div class="row"><div class="col-md-6 mb-2"><label class="form-label required">Tempat Lahir</label><input name="birth_place" class="form-control" value="{{ old('birth_place') }}" required></div>
        <div class="col-md-6 mb-2"><label class="form-label required">Tanggal Lahir</label><input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required></div></div>
        <div class="row"><div class="col-md-8 mb-2"><label class="form-label required">Status Keluarga</label><select name="family_status" class="form-select" required>@foreach(['Anak Kandung','Anak Tiri','Anak Angkat','Lainnya'] as $s)<option @selected(old('family_status')===$s)>{{ $s }}</option>@endforeach</select></div>
        <div class="col-md-4 mb-2"><label class="form-label required">Anak Ke-</label><input type="number" min="1" name="child_order" class="form-control" value="{{ old('child_order') }}" required></div></div>
        <div class="mb-2"><label class="form-label required">Alamat</label><textarea name="address" rows="2" class="form-control" required>{{ old('address') }}</textarea></div>
        <div class="mb-2"><label class="form-label required">No. HP</label><input name="phone" class="form-control" value="{{ old('phone') }}" required></div>
        <div class="mb-2"><label class="form-label">Asal Sekolah</label><input name="previous_school" class="form-control" value="{{ old('previous_school') }}"></div>
    </div>

    <div class="col-md-6 p-3 bg-white rounded-4 shadow-sm">
        <h6 class="section-title mb-2">Data Orang Tua / Wali</h6>
        <div class="mb-2"><label class="form-label required">Ayah</label><input name="father_name" class="form-control" value="{{ old('father_name') }}" required></div>
        <div class="mb-2"><label class="form-label required">Ibu</label><input name="mother_name" class="form-control" value="{{ old('mother_name') }}" required></div>
        <div class="mb-2"><label class="form-label required">Kakek Dari Ayah</label><input name="grandfather_name" class="form-control" value="{{ old('grandfather_name') }}" required></div>
        <div class="row"><div class="col-md-6 mb-2"><label class="form-label">Pekerjaan Ayah</label><input name="father_job" class="form-control" value="{{ old('father_job') }}"></div>
        <div class="col-md-6 mb-2"><label class="form-label">Pekerjaan Ibu</label><input name="mother_job" class="form-control" value="{{ old('mother_job') }}"></div></div>
        <div class="row"><div class="col-md-6 mb-2"><label class="form-label">HP Ayah</label><input name="father_phone" class="form-control" value="{{ old('father_phone') }}"></div>
        <div class="col-md-6 mb-2"><label class="form-label">HP Ibu</label><input name="mother_phone" class="form-control" value="{{ old('mother_phone') }}"></div></div>
        <hr>
        <div class="form-check"><input type="checkbox" name="agreement" value="1" class="form-check-input" id="agr" required checked><label for="agr" class="form-check-label">Data yang dimasukkan sudah diverifikasi admin.</label></div>
    </div>

    <div class="col-12 text-end"><button class="btn btn-azhar" data-testid="admin-create-submit">Simpan Pendaftaran</button></div>
</form>
@endsection
