@extends('layouts.base')

@section('body')
<div class="site-wrapper">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-azhar sticky-top shadow-sm py-2 py-lg-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
                @php $logo = \App\Models\InformationSetting::get('logo_path'); @endphp
                @if($logo && file_exists(public_path($logo)))
                    <img src="{{ asset($logo) }}" alt="Logo" style="width:40px;height:40px;object-fit:contain;">
                @else
                    <span class="brand-mark" style="width:40px;height:40px;font-size:1.2rem;">AZ</span>
                @endif
                <div class="d-flex flex-column lh-1">
                    <span style="font-family:'Fraunces',serif;font-weight:700;font-size:1.05rem;">PORMIN</span>
                    <small style="opacity:.85;font-size:.7rem;">Al-Azhar Cairo Palembang</small>
                </div>
            </a>
            <button class="navbar-toggler border-0 p-2 rounded-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#nav" data-testid="navbar-toggle" aria-label="Toggle navigation" style="background:rgba(255,255,255,0.12);">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1 gap-1 pt-2 pt-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('landing') }}" data-testid="nav-home">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('info.pendaftaran') }}" data-testid="nav-info">Informasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('info.persyaratan') }}" data-testid="nav-persyaratan">Persyaratan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('info.alur') }}" data-testid="nav-alur">Alur</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('info.jadwal') }}" data-testid="nav-jadwal">Jadwal</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('info.faq') }}" data-testid="nav-faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('registration.check') }}" data-testid="nav-cek">Cek Pendaftaran</a></li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0"><a class="btn btn-gold px-3 py-2 rounded-pill shadow-xs" href="{{ route('registration.create') }}" data-testid="nav-daftar-cta">Daftar Sekarang <i class="bi bi-arrow-right ms-1"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama (Akan otomatis melar mengisi ruang kosong) -->
    <main class="site-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-4 py-md-5 mt-auto">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
                        @if($logo && file_exists(public_path($logo)))
                            <img src="{{ asset($logo) }}" alt="Logo" style="width:44px;height:44px;object-fit:contain;">
                        @else
                            <span class="brand-mark">AZ</span>
                        @endif
                    </a>
                    <div>
                        <div style="font-family:'Fraunces',serif;font-weight:700;">PORMIN</div>
                        <small style="opacity:.75;">Portal Minat · Al-Azhar Cairo Palembang</small>
                    </div>
                </div>
                <small class="d-block text-white-50">© {{ date('Y') }} Al-Azhar Cairo Palembang. Hak cipta dilindungi.</small>
            </div>
            <div class="small">
                @php
                    $rawAddress = \App\Models\InformationSetting::get('contact_address') ?? '';
                    $cleanAddress = preg_replace('/<br\s*\/?>/i', "\n", $rawAddress);
                    $addressLines = array_filter(array_map('trim', explode("\n", $cleanAddress)));
                    $phone = \App\Models\InformationSetting::get('contact_phone');
                    $cleanPhone = preg_replace('/[^0-9+]/', '', (string)$phone);
                    $email = \App\Models\InformationSetting::get('contact_email');
                @endphp
                <div class="d-flex align-items-start mb-2">
                    <i class="bi bi-geo-alt me-2 mt-1 flex-shrink-0" style="color:var(--azhar-gold);"></i>
                    <div>
                        @forelse($addressLines as $line)
                            <div class="mb-1">{{ $line }}</div>
                        @empty
                            <div>{{ $rawAddress }}</div>
                        @endforelse
                    </div>
                </div>
                @if($phone)
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-telephone me-2 flex-shrink-0" style="color:var(--azhar-gold);"></i>
                        <a href="tel:{{ $cleanPhone }}" class="text-decoration-none text-light opacity-90">{{ $phone }}</a>
                    </div>
                @endif
                @if($email)
                    <div class="d-flex align-items-center">
                        <i class="bi bi-envelope me-2 flex-shrink-0" style="color:var(--azhar-gold);"></i>
                        <a href="mailto:{{ $email }}" class="text-decoration-none text-light opacity-90">{{ $email }}</a>
                    </div>
                @endif
            </div>
        </div>
    </footer>
</div>

<!-- Tambahan Style CSS Responsif untuk HP & iPad -->
@push('head')
<style>
    html, body {
        height: 100%;
        margin: 0;
    }
    .site-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .site-content {
        flex: 1 0 auto;
    }
    @media (max-width: 991.98px) {
        .navbar-azhar .navbar-collapse {
            background: linear-gradient(180deg, rgba(36, 10, 52, 0.98) 0%, rgba(21, 2, 33, 0.98) 100%);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            padding: 1.25rem;
            margin-top: 0.85rem;
            border-radius: 18px;
            border: 1px solid rgba(233, 216, 253, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.45);
        }
        .navbar-azhar .navbar-nav .nav-link {
            padding: 0.75rem 1rem !important;
            border-radius: 10px;
            font-weight: 500;
            color: #fff !important;
            transition: all 0.2s ease;
        }
        .navbar-azhar .navbar-nav .nav-link:hover,
        .navbar-azhar .navbar-nav .nav-link:focus {
            background: rgba(167, 139, 250, 0.18);
            color: #fff !important;
            padding-left: 1.25rem !important;
        }
        .navbar-azhar .btn-gold {
            width: 100%;
            text-align: center;
            margin-top: 0.5rem;
            padding: 0.75rem 1rem !important;
            border-radius: 12px;
        }
    }
</style>
@endpush
@endsection