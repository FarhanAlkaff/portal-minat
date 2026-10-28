@extends('layouts.admin')
@section('title', 'Edit Akun Admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Akun
    </a>
    <h1 class="section-title mb-1">Edit Akun: {{ $user->name }}</h1>
    <p class="text-muted small mb-0">Perbarui profil, kata sandi, serta hak akses jenjang akun administrator.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 shadow-sm p-4">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah password">
                    <small class="text-muted">Biarkan kosong jika tetap menggunakan password lama.</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Peran / Role <span class="text-danger">*</span></label>
                    <div class="d-flex gap-4 p-3 rounded-3" style="background:#F9FAFB;border:1px solid #E5E7EB;">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_grade_admin" value="grade_admin" {{ old('role', $user->role) === 'grade_admin' ? 'checked' : '' }} onchange="toggleGradeSelection()">
                            <label class="form-check-label fw-semibold text-dark" for="role_grade_admin">
                                Admin Grade (Khusus Jenjang Tertentu)
                                <div class="text-muted small fw-normal">Hanya dapat melihat pendaftaran & dashboard pada jenjang yang dipilih.</div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_super_admin" value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'checked' : '' }} onchange="toggleGradeSelection()">
                            <label class="form-check-label fw-semibold text-dark" for="role_super_admin">
                                Super Admin (Akses Penuh)
                                <div class="text-muted small fw-normal">Dapat melihat semua jenjang, mengelola akun admin, dan mengatur master data.</div>
                            </label>
                        </div>
                    </div>
                    @error('role')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div id="gradeSelectionBox" class="mb-4">
                    <label class="form-label fw-semibold">Pilih Jenjang / Grade yang Dikelola <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-2">Admin ini hanya akan memiliki akses data pada jenjang yang dicentang di bawah ini:</p>
                    <div class="row g-2">
                        @foreach($grades as $g)
                            @php
                                $isChecked = is_array(old('grade_ids')) 
                                    ? in_array($g->id, old('grade_ids')) 
                                    : in_array($g->id, $userGradeIds);
                            @endphp
                            <div class="col-md-6">
                                <label class="d-flex align-items-center gap-2 p-3 rounded-3 border h-100" style="cursor:pointer;background:#fff;">
                                    <input type="checkbox" name="grade_ids[]" value="{{ $g->id }}" class="form-check-input mt-0" {{ $isChecked ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $g->code }} - {{ $g->name }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $g->description }}</div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('grade_ids')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center gap-2 pt-2 border-top">
                    <button type="submit" class="btn btn-azhar px-4 py-2 rounded-3 shadow-xs">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4 py-2 rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleGradeSelection() {
        const isGradeAdmin = document.getElementById('role_grade_admin').checked;
        const box = document.getElementById('gradeSelectionBox');
        if (box) {
            box.style.display = isGradeAdmin ? 'block' : 'none';
        }
    }
    document.addEventListener('DOMContentLoaded', toggleGradeSelection);
</script>
@endpush
@endsection
