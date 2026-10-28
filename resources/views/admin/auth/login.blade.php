@extends('layouts.base')
@section('title', 'Login Petugas Admin · PORMIN')
@section('body')
<style>
    /* Styling Khusus Halaman Login */
    .login-wrapper {
        min-height: 100vh;
        background: radial-gradient(circle at 10% 20%, rgba(167, 139, 250, 0.25) 0%, transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(107, 45, 140, 0.35) 0%, transparent 45%),
                    radial-gradient(circle at 50% 50%, rgba(58, 21, 79, 0.6) 0%, transparent 60%),
                    linear-gradient(135deg, #1b0729 0%, #2e0e45 45%, #461463 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    /* Floating Ambient Glowing Orbs */
    .ambient-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.5;
        pointer-events: none;
        z-index: 1;
        animation: floatOrb 12s ease-in-out infinite alternate;
    }
    .orb-1 {
        width: 380px;
        height: 380px;
        background: #9333EA;
        top: -100px;
        left: -100px;
        animation-duration: 14s;
    }
    .orb-2 {
        width: 420px;
        height: 420px;
        background: #A78BFA;
        bottom: -120px;
        right: -100px;
        animation-duration: 16s;
    }
    .orb-3 {
        width: 260px;
        height: 260px;
        background: #6B2D8C;
        top: 40%;
        right: 25%;
        opacity: 0.3;
        animation-duration: 10s;
    }
    @keyframes floatOrb {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(30px, 40px) scale(1.08); }
        100% { transform: translate(-20px, -30px) scale(0.95); }
    }

    /* Login Master Card Container */
    .login-container {
        position: relative;
        z-index: 5;
        width: 100%;
        max-width: 980px;
    }

    .login-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 60px -15px rgba(22, 4, 36, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.15);
        border: none;
    }

    /* Left Side Branding Panel */
    .login-brand-panel {
        background: linear-gradient(145deg, #27093b 0%, #3e125c 50%, #581a7d 100%);
        color: #ffffff;
        padding: 3.5rem 3rem;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }
    .login-brand-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 24px 24px;
        opacity: 0.7;
        pointer-events: none;
    }

    .brand-logo-wrapper {
        width: 86px;
        height: 86px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25), 0 0 0 3px rgba(167, 139, 250, 0.35);
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }
    .brand-logo-wrapper:hover {
        transform: translateY(-3px) scale(1.03);
    }
    .brand-logo-wrapper img {
        width: 64px;
        height: 64px;
        object-fit: contain;
    }

    .brand-title {
        font-family: 'Fraunces', Georgia, serif;
        font-size: 2.1rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-bottom: 0.35rem;
        color: #ffffff;
    }
    .brand-subtitle {
        color: #E9D8FD;
        font-size: 0.95rem;
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    .feature-pill {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        transition: all 0.25s ease;
    }
    .feature-pill:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateX(4px);
        border-color: rgba(167, 139, 250, 0.4);
    }
    .feature-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(167, 139, 250, 0.22);
        color: #E9D8FD;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    /* Right Side Form Panel */
    .login-form-panel {
        padding: 3.5rem 3rem;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .login-form-header h2 {
        font-family: 'Fraunces', Georgia, serif;
        font-size: 1.85rem;
        font-weight: 700;
        color: var(--azhar-green-dark);
        letter-spacing: -0.01em;
        margin-bottom: 0.4rem;
    }
    .login-form-header p {
        color: #6B7280;
        font-size: 0.92rem;
    }

    /* Form Fields & Inputs */
    .custom-input-group {
        border-radius: 12px;
        border: 1.5px solid #E5E7EB;
        background: #F9FAFB;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    .custom-input-group:focus-within {
        border-color: #6B2D8C;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(107, 45, 140, 0.12);
    }
    .custom-input-group .input-group-text {
        background: transparent;
        border: none;
        color: #6B2D8C;
        font-size: 1.15rem;
        padding-left: 1rem;
        padding-right: 0.75rem;
    }
    .custom-input-group .form-control {
        border: none;
        background: transparent;
        padding: 0.8rem 0.85rem;
        font-size: 0.95rem;
        color: #1F2937;
    }
    .custom-input-group .form-control:focus {
        box-shadow: none;
        background: transparent;
    }
    .custom-input-group .btn-toggle-pw {
        background: transparent;
        border: none;
        color: #9CA3AF;
        padding-right: 1rem;
        transition: color 0.2s;
    }
    .custom-input-group .btn-toggle-pw:hover {
        color: #6B2D8C;
    }

    /* Submit Button with Gradient and Elevation */
    .btn-login-submit {
        background: linear-gradient(135deg, #6B2D8C 0%, #4A156D 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 0.85rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        box-shadow: 0 8px 20px rgba(107, 45, 140, 0.28);
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-login-submit:hover {
        background: linear-gradient(135deg, #581a7d 0%, #360c52 100%);
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(107, 45, 140, 0.38);
        color: #ffffff;
    }
    .btn-login-submit:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(107, 45, 140, 0.2);
    }

    .btn-back-home {
        color: #6B7280;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-back-home:hover {
        color: #6B2D8C;
        background: #F3E8FF;
    }

    /* Custom Checkbox */
    .form-check-input:checked {
        background-color: #6B2D8C;
        border-color: #6B2D8C;
    }
    .form-check-input:focus {
        border-color: #A78BFA;
        box-shadow: 0 0 0 0.25rem rgba(107, 45, 140, 0.15);
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .login-brand-panel {
            padding: 2.5rem 2rem;
            text-align: center;
            align-items: center;
        }
        .brand-logo-wrapper {
            margin-left: auto;
            margin-right: auto;
        }
        .feature-pill {
            text-align: left;
        }
        .login-form-panel {
            padding: 2.5rem 2rem;
        }
    }
    @media (max-width: 575.98px) {
        .login-wrapper {
            padding: 1rem 0.5rem;
        }
        .login-card {
            border-radius: 18px;
        }
        .login-form-panel {
            padding: 2rem 1.25rem;
        }
        .login-brand-panel {
            padding: 2rem 1.25rem;
        }
    }
</style>

<div class="login-wrapper">
    <!-- Ambient Background Lighting -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="ambient-orb orb-3"></div>

    <div class="login-container">
        <div class="card login-card">
            <div class="row g-0">
                <!-- Sisi Kiri: Branding & Identity Al-Azhar Cairo Palembang -->
                <div class="col-lg-5 login-brand-panel">
                    <div style="position:relative; z-index:2;">
                        @php $logo = \App\Models\InformationSetting::get('logo_path'); @endphp
                        <div class="brand-logo-wrapper">
                            @if($logo && file_exists(public_path($logo)))
                                <img src="{{ asset($logo) }}" alt="Logo Al-Azhar Cairo Palembang">
                            @else
                                <span class="fw-bold" style="font-family:'Fraunces',serif; font-size:1.8rem; color:var(--azhar-green);">AZ</span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <span class="badge rounded-pill mb-2 px-3 py-1 text-white fw-semibold" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); font-size:0.75rem;">
                                <i class="bi bi-shield-lock-fill me-1" style="color:#A78BFA;"></i> Portal Administrasi
                            </span>
                            <h1 class="brand-title">PORMIN</h1>
                            <p class="brand-subtitle mb-0">Al-Azhar Cairo Palembang</p>
                            <small class="text-white-50" style="font-size:0.82rem;">Sistem Penerimaan Murid Baru Terpadu</small>
                        </div>

                        <!-- Feature Highlight List -->
                        <div class="d-none d-md-block my-4">
                            <div class="feature-pill">
                                <div class="feature-icon-box">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-white" style="font-size:0.88rem;">Multi-Jenjang Pendidikan</div>
                                    <small class="text-white-50" style="font-size:0.75rem;">PG, TK-A, TK-B, SD, SMP, & SMA</small>
                                </div>
                            </div>

                            <div class="feature-pill">
                                <div class="feature-icon-box">
                                    <i class="bi bi-speedometer2"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-white" style="font-size:0.88rem;">Manajemen Kuota & Observasi</div>
                                    <small class="text-white-50" style="font-size:0.75rem;">Rekapitulasi target & berkas calon siswa</small>
                                </div>
                            </div>

                            <div class="feature-pill">
                                <div class="feature-icon-box">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-white" style="font-size:0.88rem;">Akses Aman & Terenkripsi</div>
                                    <small class="text-white-50" style="font-size:0.75rem;">Proteksi data pendaftaran keluarga murid</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Panel Kiri -->
                    <div style="position:relative; z-index:2;" class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center justify-content-between">
                        <small class="text-white-50" style="font-size:0.75rem;">
                            &copy; {{ date('Y') }} Al-Azhar Cairo Palembang
                        </small>
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2 py-1" style="font-size:0.68rem;">
                            <i class="bi bi-lock-fill me-1"></i> SSL 256-Bit
                        </span>
                    </div>
                </div>

                <!-- Sisi Kanan: Form Login Petugas -->
                <div class="col-lg-7 login-form-panel">
                    <div class="login-form-header mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge rounded-pill px-3 py-1" style="background:#F3E8FF; color:#6B2D8C; font-weight:600; font-size:0.75rem;">
                                <i class="bi bi-person-badge me-1"></i> Khusus Petugas & Admin
                            </span>
                        </div>
                        <h2>Selamat Datang 👋</h2>
                        <p>Silakan masukkan akun Anda untuk melanjutkan ke dashboard manajemen PMB.</p>
                    </div>

                    @if(session('warning') || request()->has('timeout'))
                        <div class="alert alert-warning d-flex align-items-center py-2 px-3 mb-4 rounded-3 border-0 shadow-sm" style="background:#FEF3C7; color:#92400E;">
                            <i class="bi bi-shield-exclamation fs-5 me-2" style="color:#D97706;"></i>
                            <div style="font-size:0.88rem;">{{ session('warning') ?? 'Sesi Anda telah berakhir karena tidak ada aktivitas selama 30 menit demi keamanan. Silakan login kembali.' }}</div>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-4 rounded-3 border-0 shadow-sm" style="background:#FEE2E2; color:#991B1B;">
                            <i class="bi bi-exclamation-circle-fill fs-5 me-2 text-danger"></i>
                            <div style="font-size:0.88rem;">{{ $errors->first() }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <!-- Field Email -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1 text-uppercase" style="letter-spacing:0.04em;">
                                Alamat Email
                            </label>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope-at"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       class="form-control" 
                                       placeholder="admin@alazharcairoplg.sch.id" 
                                       required 
                                       value="{{ old('email') }}" 
                                       data-testid="admin-login-email"
                                       autocomplete="email"
                                       autofocus>
                            </div>
                        </div>

                        <!-- Field Password -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-muted mb-0 text-uppercase" style="letter-spacing:0.04em;">
                                    Password
                                </label>
                            </div>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-shield-lock"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       id="adminPasswordInput"
                                       class="form-control" 
                                       placeholder="••••••••" 
                                       required 
                                       data-testid="admin-login-password"
                                       autocomplete="current-password">
                                <button type="button" 
                                        class="btn btn-toggle-pw" 
                                        id="togglePasswordBtn" 
                                        title="Tampilkan / Sembunyikan Password"
                                        tabindex="-1">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Ingat Saya -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label small text-secondary" for="remember" style="user-select:none;">
                                    Ingat saya di perangkat ini
                                </label>
                            </div>
                        </div>

                        <!-- Tombol Submit Masuk -->
                        <button type="submit" class="btn btn-login-submit w-100 mb-3" data-testid="admin-login-submit">
                            <span>Masuk ke Dashboard</span>
                            <i class="bi bi-arrow-right fs-5"></i>
                        </button>
                    </form>

                    <!-- Navigasi Balik & Info Tambahan -->
                    <div class="text-center pt-3 border-top mt-2">
                        <a href="{{ route('landing') }}" class="btn-back-home">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Beranda Utama</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Password Visibility
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const pwInput = document.getElementById('adminPasswordInput');
        const pwIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && pwInput && pwIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = pwInput.getAttribute('type') === 'password';
                pwInput.setAttribute('type', isPassword ? 'text' : 'password');
                pwIcon.classList.toggle('bi-eye', !isPassword);
                pwIcon.classList.toggle('bi-eye-slash', isPassword);
            });
        }
    });
</script>
@endsection
