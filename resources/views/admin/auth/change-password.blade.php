@extends('layouts.admin')
@section('title', 'Ubah Password')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1 class="section-title mb-1">Ubah Kata Sandi Akun</h1>
    <p class="text-muted small mb-0">Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan data pendaftaran.</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 rounded-4 shadow-sm p-4">
            <!-- Profil Singkat Akun -->
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background:#FAF7FC;border:1px solid #E9D8FD;">
                <div style="width:48px;height:48px;border-radius:12px;background:{{ auth()->user()->isSuperAdmin() ? '#6B2D8C' : '#3B82F6' }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.2rem;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</div>
                    <div class="text-muted small text-truncate">{{ auth()->user()->email }}</div>
                    <div class="mt-1 d-flex align-items-center gap-2">
                        @if(auth()->user()->isSuperAdmin())
                            <span class="badge rounded-pill px-2 py-1" style="background:#EDE9FE;color:#6B2D8C;font-size:0.72rem;font-weight:600;">
                                <i class="bi bi-shield-check me-1"></i>Super Admin
                            </span>
                        @else
                            <span class="badge rounded-pill px-2 py-1" style="background:#EFF6FF;color:#1D4ED8;font-size:0.72rem;font-weight:600;">
                                <i class="bi bi-person-badge me-1"></i>Admin Grade
                            </span>
                            <span class="text-muted" style="font-size:0.75rem;">
                                Akses: <b>{{ auth()->user()->assignedGradesLabel() }}</b>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                @method('PUT')

                <!-- Password Saat Ini -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Saat Ini <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password yang sedang digunakan" required autofocus>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('current_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password Baru -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="text-muted">Gunakan kombinasi huruf, angka, atau simbol agar lebih aman.</small>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang password baru Anda" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2 border-top">
                    <button type="submit" class="btn btn-azhar px-4 py-2 rounded-3 shadow-xs">
                        <i class="bi bi-key-fill me-1"></i> Perbarui Kata Sandi
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light border px-4 py-2 rounded-3">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tips Keamanan -->
    <div class="col-lg-5">
        <div class="card border-0 rounded-4 shadow-sm p-4" style="background:#FAF7FC;border:1px solid #ECE3F5 !important;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div style="width:36px;height:36px;border-radius:10px;background:#EDE9FE;color:#6B2D8C;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h6 class="fw-bold mb-0 text-dark" style="font-family:'Fraunces',serif;">Tips Keamanan Akun</h6>
            </div>
            <ul class="text-muted small ps-3 mb-0 d-flex flex-column gap-2" style="line-height:1.6;">
                <li>Jangan membagikan kata sandi akun Anda kepada siapapun.</li>
                <li>Gunakan minimal 6 karakter dengan variasi huruf besar, kecil, angka, atau simbol.</li>
                <li>Pastikan selalu logout setelah selesai menggunakan komputer bersama.</li>
                <li>Setiap aktivitas perubahan kata sandi tercatat secara otomatis di log audit sistem.</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endpush
@endsection
