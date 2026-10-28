@extends('layouts.base')

@section('body')
<div class="d-flex">
    <!-- Backdrop for mobile/tablet -->
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

    <!-- Admin Sidebar -->
    <aside id="adminSidebar" class="sidebar p-3 d-flex flex-column">
        <!-- Sidebar Brand & Toggle Button -->
        <div class="d-flex align-items-center justify-content-between mb-3 brand-wrap">
            <a href="{{ route('landing') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none rounded-3 flex-grow-1 overflow-hidden" title="PORMIN Admin">
                @php $logo = \App\Models\InformationSetting::get('logo_path'); @endphp
                @if($logo && file_exists(public_path($logo)))
                    <img src="{{ asset($logo) }}" alt="Logo" style="width:38px;height:38px;object-fit:contain;background:#fff;border-radius:9px;padding:2px;flex-shrink:0;">
                @else
                    <span class="brand-mark" style="width:38px;height:38px;font-size:1.05rem;flex-shrink:0;box-shadow:0 4px 12px rgba(147,51,234,0.35);">AZ</span>
                @endif
                <div class="overflow-hidden sidebar-hide-on-collapse">
                    <div style="font-family:'Fraunces',serif;font-weight:700;font-size:1.02rem;line-height:1.2;">PORMIN Admin</div>
                    <small class="text-truncate d-block" style="color:#C4B5FD;font-size:0.72rem;">Al-Azhar Cairo Plg</small>
                </div>
            </a>
            <!-- Desktop Toggle Button IN SIDEBAR -->
            <button type="button" id="sidebarToggleInsideBtn" class="btn btn-sm text-white-50 p-1 border-0 rounded-2 ms-1 d-none d-lg-flex align-items-center justify-content-center" title="Buka / Tutup Sidebar" style="background:rgba(255,255,255,0.08);width:32px;height:32px;flex-shrink:0;cursor:pointer;">
                <i class="bi bi-chevron-left text-white fs-6"></i>
            </button>
            <!-- Mobile Close Button -->
            <button type="button" id="sidebarCloseBtn" class="btn btn-sm btn-link text-white-50 p-1 ms-1 d-lg-none" title="Tutup Menu">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>

        <nav class="d-flex flex-column gap-1 flex-grow-1">
            <div class="text-uppercase small px-2 mt-2 mb-1 sidebar-hide-on-collapse" style="color:#A78BFA;font-size:0.7rem;font-weight:700;letter-spacing:0.08em;">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard" data-testid="sidebar-dashboard">
                <i class="bi bi-grid-1x2-fill me-2 fs-6"></i>
                <span class="sidebar-hide-on-collapse">Dashboard</span>
            </a>
            <a href="{{ route('admin.registrations.index') }}" class="nav-link-item {{ request()->routeIs('admin.registrations.index') || request()->routeIs('admin.registrations.show') ? 'active' : '' }}" title="Data Pendaftaran" data-testid="sidebar-registrations">
                <i class="bi bi-people-fill me-2 fs-6"></i>
                <span class="sidebar-hide-on-collapse">Data Pendaftaran</span>
            </a>
            <a href="{{ route('admin.registrations.create') }}" class="nav-link-item {{ request()->routeIs('admin.registrations.create') ? 'active' : '' }}" title="Input Pendaftaran" data-testid="sidebar-input-lama">
                <i class="bi bi-person-plus-fill me-2 fs-6"></i>
                <span class="sidebar-hide-on-collapse">Input Pendaftaran</span>
            </a>
            <a href="{{ route('admin.registrations.import') }}" class="nav-link-item {{ request()->routeIs('admin.registrations.import*') ? 'active' : '' }}" title="Import Batch CSV" data-testid="sidebar-import">
                <i class="bi bi-file-earmark-arrow-up-fill me-2 fs-6"></i>
                <span class="sidebar-hide-on-collapse">Import Batch CSV</span>
            </a>

            @if(auth()->user()->isSuperAdmin())
                <div class="text-uppercase small px-2 mt-3 mb-1 sidebar-hide-on-collapse" style="color:#A78BFA;font-size:0.7rem;font-weight:700;letter-spacing:0.08em;">Master Data</div>
                <a href="{{ route('admin.years') }}" class="nav-link-item {{ request()->routeIs('admin.years') ? 'active' : '' }}" title="Tahun Ajaran" data-testid="sidebar-years">
                    <i class="bi bi-calendar3 me-2 fs-6"></i>
                    <span class="sidebar-hide-on-collapse">Tahun Ajaran</span>
                </a>
                <a href="{{ route('admin.grades') }}" class="nav-link-item {{ request()->routeIs('admin.grades*') ? 'active' : '' }}" title="Grade & Aturan Usia" data-testid="sidebar-grades">
                    <i class="bi bi-mortarboard-fill me-2 fs-6"></i>
                    <span class="sidebar-hide-on-collapse">Grade & Aturan Usia</span>
                </a>
                <a href="{{ route('admin.campuses') }}" class="nav-link-item {{ request()->routeIs('admin.campuses') ? 'active' : '' }}" title="Kampus" data-testid="sidebar-campuses">
                    <i class="bi bi-buildings-fill me-2 fs-6"></i>
                    <span class="sidebar-hide-on-collapse">Kampus</span>
                </a>
                <a href="{{ route('admin.categories') }}" class="nav-link-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}" title="Kategori Siswa" data-testid="sidebar-categories">
                    <i class="bi bi-tags-fill me-2 fs-6"></i>
                    <span class="sidebar-hide-on-collapse">Kategori Siswa</span>
                </a>
                <a href="{{ route('admin.information') }}" class="nav-link-item {{ request()->routeIs('admin.information') ? 'active' : '' }}" title="Pengaturan Website" data-testid="sidebar-info">
                    <i class="bi bi-sliders me-2 fs-6"></i>
                    <span class="sidebar-hide-on-collapse">Pengaturan Website</span>
                </a>

                <div class="text-uppercase small px-2 mt-3 mb-1 sidebar-hide-on-collapse" style="color:#A78BFA;font-size:0.7rem;font-weight:700;letter-spacing:0.08em;">Manajemen Akun</div>
                <a href="{{ route('admin.users.index') }}" class="nav-link-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" title="Kelola Akun Admin" data-testid="sidebar-users">
                    <i class="bi bi-person-gear me-2 fs-6"></i>
                    <span class="sidebar-hide-on-collapse">Kelola Akun Admin</span>
                </a>
            @endif

            <div class="text-uppercase small px-2 mt-3 mb-1 sidebar-hide-on-collapse" style="color:#A78BFA;font-size:0.7rem;font-weight:700;letter-spacing:0.08em;">Akun Saya</div>
            <a href="{{ route('admin.password.edit') }}" class="nav-link-item {{ request()->routeIs('admin.password*') ? 'active' : '' }}" title="Ubah Password" data-testid="sidebar-password">
                <i class="bi bi-key-fill me-2 fs-6"></i>
                <span class="sidebar-hide-on-collapse">Ubah Password</span>
            </a>
        </nav>

        <div class="pt-3 mt-auto border-top" style="border-color:rgba(233,216,253,0.12) !important;">
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 user-card-wrap" style="background:rgba(255,255,255,0.05);">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div style="width:34px;height:34px;border-radius:9px;background:{{ auth()->user()->isSuperAdmin() ? '#9333EA' : '#3B82F6' }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden user-info-wrap sidebar-hide-on-collapse">
                        <div class="text-white small fw-bold text-truncate">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <div class="text-truncate" style="font-size:0.7rem;color:#C4B5FD !important;">
                            @if(auth()->user()->isSuperAdmin())
                                Super Admin
                            @else
                                Jenjang: {{ auth()->user()->assignedGradesLabel() }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 sidebar-hide-on-collapse">
                    <a href="{{ route('admin.password.edit') }}" class="btn btn-sm btn-outline-light border-0 text-white p-1 px-2 rounded-2" title="Ubah Password" style="background:rgba(255,255,255,0.1);">
                        <i class="bi bi-key"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 text-white p-1 px-2 rounded-2" title="Logout" data-testid="sidebar-logout" style="background:rgba(239,68,68,0.2);">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="flex-grow-1 admin-main d-flex flex-column" style="min-width:0;">
        <header class="admin-topbar py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <!-- Mobile only toggle -->
                <button id="sidebarMobileToggle" type="button" class="btn btn-sm btn-light border rounded-3 p-1 px-2 d-lg-none d-flex align-items-center justify-content-center shadow-xs" title="Menu" style="width:38px;height:38px;color:#3A154F;cursor:pointer;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-family:'Fraunces',serif;">Panel Kendali</h5>
                    <small class="text-muted">
                        <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->isSuperAdmin())
                    <span class="badge d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill d-none d-md-inline-flex" style="background:#EDE9FE;color:#6B2D8C;font-size:0.8rem;font-weight:600;">
                        <i class="bi bi-shield-check"></i> Super Admin
                    </span>
                @else
                    <span class="badge d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill d-none d-md-inline-flex" style="background:#EFF6FF;color:#1D4ED8;font-size:0.8rem;font-weight:600;">
                        <i class="bi bi-mortarboard-fill"></i> Jenjang: {{ auth()->user()->assignedGradesLabel() }}
                    </span>
                @endif

                @php $activeYear = \App\Models\AcademicYear::active(); @endphp
                @if($activeYear)
                    <span class="badge d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill" style="background:#EDE9FE;color:#6B2D8C;font-size:0.8rem;font-weight:600;">
                        <span style="width:7px;height:7px;border-radius:50%;background:#10B981;display:inline-block;"></span>
                        TA Aktif: {{ $activeYear->name }}
                    </span>
                @endif
                <a href="{{ route('admin.password.edit') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" style="font-size:0.8rem;" title="Ubah Password">
                    <i class="bi bi-key-fill text-muted"></i>
                    <span class="d-none d-sm-inline">Ubah Password</span>
                </a>
                <a href="{{ route('landing') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" style="font-size:0.8rem;">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Web Publik
                </a>
            </div>
        </header>

        <div class="p-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert" data-testid="flash-success">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert" data-testid="flash-info">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<!-- Modal Peringatan AFK / Inactivity -->
<div class="modal fade" id="afkWarningModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="afkWarningModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #FEF3C7; color: #D97706; flex-shrink: 0;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="afkWarningModalLabel" style="font-family:'Fraunces',serif; font-size: 1.2rem;">Peringatan Inaktivitas</h5>
                        <small class="text-muted">Keamanan Akun Admin</small>
                    </div>
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-secondary mb-2" style="font-size: 0.95rem; line-height: 1.5;">
                    Anda terdeteksi tidak aktif selama <strong>28 menit</strong>. Demi keamanan data pendaftaran, sesi Anda akan otomatis di-logout dalam:
                </p>
                <div class="text-center py-2 px-3 my-2 rounded-3" style="background: #FBF7FF; border: 1px dashed #D8B4FE;">
                    <span id="afkCountdown" class="display-6 fw-bold" style="color: #6B2D8C; font-family: monospace;">02:00</span>
                    <div class="small text-muted">menit sebelum sesi terkunci</div>
                </div>
                <p class="text-muted small mb-0">Klik tombol di bawah jika Anda masih ingin melanjutkan aktivitas.</p>
            </div>
            <div class="modal-footer border-0 pt-1 pb-4 px-4 d-flex gap-2">
                <form method="POST" action="{{ route('admin.logout') }}" class="m-0 flex-grow-1">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-semibold" style="font-size: 0.9rem;">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
                </form>
                <button type="button" id="afkKeepAliveBtn" class="btn btn-azhar flex-grow-1 rounded-pill py-2 fw-bold shadow-sm" style="font-size: 0.9rem;">
                    <i class="bi bi-check2-circle me-1"></i>Saya Masih Di Sini
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('adminSidebar');
        const insideToggleBtn = document.getElementById('sidebarToggleInsideBtn');
        const mobileToggleBtn = document.getElementById('sidebarMobileToggle');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const backdrop = document.getElementById('sidebarBackdrop');

        const isMobile = () => window.innerWidth < 992;

        // Pulihkan status sidebar di desktop dari localStorage
        if (!isMobile() && localStorage.getItem('pormin_sidebar_collapsed') === 'true') {
            sidebar.classList.add('is-collapsed');
        }

        // Toggle di desktop via tombol di dalam sidebar
        if (insideToggleBtn) {
            insideToggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('is-collapsed');
                localStorage.setItem('pormin_sidebar_collapsed', sidebar.classList.contains('is-collapsed'));
            });
        }

        // Toggle mobile drawer
        if (mobileToggleBtn) {
            mobileToggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('is-open');
                if (backdrop) backdrop.classList.toggle('show');
            });
        }

        function closeSidebar() {
            sidebar.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('show');
        }

        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);

        // AFK / Inactivity Timeout (30 menit = 1800 detik)
        (function() {
            const INACTIVITY_LIMIT_SEC = 1800; // 30 menit
            const WARNING_BEFORE_SEC = 120;     // Tampilkan peringatan 2 menit sebelum logout
            const WARNING_AT_SEC = INACTIVITY_LIMIT_SEC - WARNING_BEFORE_SEC; // 1680 detik

            let idleSeconds = 0;
            let warningShown = false;
            let countdownInterval = null;
            let afkModalInstance = null;

            const modalEl = document.getElementById('afkWarningModal');
            const countdownEl = document.getElementById('afkCountdown');
            const keepAliveBtn = document.getElementById('afkKeepAliveBtn');

            function formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }

            function getModal() {
                if (!afkModalInstance && modalEl && typeof bootstrap !== 'undefined') {
                    afkModalInstance = new bootstrap.Modal(modalEl, {
                        backdrop: 'static',
                        keyboard: false
                    });
                }
                return afkModalInstance;
            }

            function showWarning() {
                warningShown = true;
                const modal = getModal();
                if (modal) modal.show();

                let remainingSec = WARNING_BEFORE_SEC;
                if (countdownEl) countdownEl.textContent = formatTime(remainingSec);

                if (countdownInterval) clearInterval(countdownInterval);
                countdownInterval = setInterval(() => {
                    remainingSec--;
                    if (remainingSec <= 0) {
                        clearInterval(countdownInterval);
                        forceLogout();
                    } else if (countdownEl) {
                        countdownEl.textContent = formatTime(remainingSec);
                    }
                }, 1000);
            }

            function hideWarning() {
                warningShown = false;
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                }
                const modal = getModal();
                if (modal) modal.hide();
            }

            function forceLogout() {
                window.location.href = "{{ route('admin.login') }}?timeout=1";
            }

            function pingServer() {
                fetch("{{ route('admin.keep-alive') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                }).catch(() => {});
            }

            function resetActivity(triggerPing = false) {
                idleSeconds = 0;
                if (warningShown) {
                    hideWarning();
                    pingServer();
                } else if (triggerPing) {
                    pingServer();
                }
            }

            // Interval pengecekan setiap detik
            setInterval(() => {
                idleSeconds++;
                if (!warningShown && idleSeconds >= WARNING_AT_SEC) {
                    showWarning();
                } else if (idleSeconds >= INACTIVITY_LIMIT_SEC) {
                    forceLogout();
                }
            }, 1000);

            // Pantau event aktivitas pengguna
            let lastUserAction = Date.now();
            const activityEvents = ['mousemove', 'keydown', 'mousedown', 'scroll', 'touchstart'];
            activityEvents.forEach(evt => {
                window.addEventListener(evt, () => {
                    if (!warningShown) {
                        const now = Date.now();
                        if (now - lastUserAction > 10000) {
                            idleSeconds = 0;
                            lastUserAction = now;
                        }
                    }
                }, { passive: true });
            });

            if (keepAliveBtn) {
                keepAliveBtn.addEventListener('click', () => {
                    resetActivity(true);
                });
            }
        })();
    });
</script>
@endpush
@endsection
