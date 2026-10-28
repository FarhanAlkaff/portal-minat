@extends('layouts.public')
@section('title', 'Beranda')

@section('content')
@php
    $anActive = \App\Models\InformationSetting::get('announcement_active') === '1';
    $anTitle = \App\Models\InformationSetting::get('announcement_title', 'Pengumuman');
    $anItems = collect(explode("\n", (string)\App\Models\InformationSetting::get('announcement_items', '')))->map(fn($x)=>trim($x))->filter()->values();
@endphp
@if($anActive && $anItems->count())
<div class="announcement-strip py-1.5 py-md-2 px-2 px-md-3">
    <div class="container d-flex align-items-center gap-2 gap-md-3">
        <span class="badge rounded-pill fw-bold flex-shrink-0" style="background:var(--azhar-gold);color:var(--azhar-green-dark);letter-spacing:.08em;font-size:clamp(0.65rem, 1.8vw, 0.72rem);padding:.4rem .65rem;"><i class="bi bi-megaphone-fill me-1"></i>{{ $anTitle }}</span>
        <div class="marquee flex-grow-1" data-testid="announcement-marquee">
            <div class="marquee-track">
                @foreach($anItems as $item)<span class="marquee-item">{{ $item }}</span><span class="marquee-sep">◆</span>@endforeach
                @foreach($anItems as $item)<span class="marquee-item">{{ $item }}</span><span class="marquee-sep">◆</span>@endforeach
            </div>
        </div>
    </div>
</div>
@endif

@push('head')
<style>
    .announcement-strip{background:linear-gradient(90deg,var(--azhar-green-dark),var(--azhar-green));color:#fff;border-bottom:2px solid var(--azhar-gold);}
    .marquee{overflow:hidden;white-space:nowrap;position:relative;mask-image:linear-gradient(to right,transparent,#000 5%,#000 95%,transparent);}
    .marquee-track{display:inline-flex;gap:1.5rem;animation:pormin-marquee 32s linear infinite;padding-left:100%;}
    .marquee-item{font-weight:600;font-size:clamp(0.8rem, 1.8vw, 0.92rem);}
    .marquee-sep{color:var(--azhar-gold);opacity:.7;}
    @keyframes pormin-marquee{from{transform:translateX(0);}to{transform:translateX(-50%);}}

    /* Slider Responsive Image Height */
    .hero-slider-img {
        height: 220px;
        object-fit: cover;
        width: 100%;
        display: block;
    }
    @media (min-width: 576px) {
        .hero-slider-img {
            height: 280px;
        }
    }
    @media (min-width: 768px) {
        .hero-slider-img {
            height: 330px;
        }
    }
    @media (min-width: 992px) {
        .hero-slider-img {
            height: 375px;
        }
    }

    /* Subtle Floating Caption */
    .carousel-caption-subtle {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(15, 23, 42, 0.78);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        color: #ffffff;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 500;
        line-height: 1.3;
        letter-spacing: 0.01em;
        border: 1px solid rgba(255, 255, 255, 0.22);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        max-width: calc(100% - 60px);
        pointer-events: none;
        z-index: 5;
        text-shadow: 0 1px 2px rgba(0,0,0,0.6);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    @media (min-width: 768px) {
        .carousel-caption-subtle {
            bottom: 14px;
            left: 14px;
            padding: 5px 12px;
            font-size: 0.78rem;
            max-width: calc(100% - 100px);
            white-space: normal;
        }
    }

    /* Compact Circular Carousel Nav Controls */
    .hero-carousel-btn {
        width: 32px !important;
        height: 32px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        border-radius: 50% !important;
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
        border: 1px solid rgba(255, 255, 255, 0.22) !important;
        opacity: 0.8 !important;
        transition: all 0.2s ease !important;
    }
    .hero-carousel-btn:hover {
        opacity: 1 !important;
        background: rgba(15, 23, 42, 0.8) !important;
    }
    .hero-carousel-btn.carousel-control-prev {
        left: 8px !important;
    }
    .hero-carousel-btn.carousel-control-next {
        right: 8px !important;
    }
    .hero-carousel-btn .carousel-control-prev-icon,
    .hero-carousel-btn .carousel-control-next-icon {
        width: 14px !important;
        height: 14px !important;
    }
    @media (min-width: 768px) {
        .hero-carousel-btn {
            width: 38px !important;
            height: 38px !important;
        }
        .hero-carousel-btn.carousel-control-prev {
            left: 12px !important;
        }
        .hero-carousel-btn.carousel-control-next {
            right: 12px !important;
        }
        .hero-carousel-btn .carousel-control-prev-icon,
        .hero-carousel-btn .carousel-control-next-icon {
            width: 16px !important;
            height: 16px !important;
        }
    }

    /* Info Feature Card */
    .info-feature-card {
        border: 1px solid #E9D8FD;
        background: #ffffff;
        transition: all 0.22s ease;
        border-radius: 18px;
    }
    .info-feature-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(107, 45, 140, 0.09);
        border-color: #C084FC;
    }
    .info-icon-badge {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #F3E8FF;
        color: var(--azhar-green-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Grade Badge Mobile */
    @media (max-width: 575.98px) {
        .grade-badge {
            width: 48px !important;
            height: 48px !important;
            font-size: 1rem !important;
            border-radius: 12px !important;
        }
    }
</style>
@endpush

<section class="hero py-4 py-md-5">
    <div class="container hero-inner py-2 py-md-4">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-7">
                <span class="badge rounded-pill px-3 py-1.5 mb-2" style="background:rgba(167,139,250,.22);color:#E9D8FD;letter-spacing:.12em;font-size:clamp(0.68rem, 1.8vw, 0.78rem);">PORTAL MINAT · PMB {{ optional($activeYear)->name }}</span>
                <h1 class="hero-title mt-2 mb-2" style="font-size:clamp(1.85rem, 4.5vw, 3.4rem);line-height:1.15;">{{ $settings['landing_hero_title'] ?? 'Pendaftaran Murid Baru' }}</h1>
                <h2 class="h5 mb-3" style="color:var(--azhar-gold);font-family:'Fraunces',serif;font-size:clamp(1.05rem, 2.2vw, 1.35rem);">{{ $settings['landing_hero_subtitle'] ?? 'Al-Azhar Cairo Palembang' }}</h2>
                <p class="lead mb-4 opacity-90" style="max-width:620px;font-size:clamp(0.92rem, 1.8vw, 1.1rem);line-height:1.6;">{{ $settings['landing_hero_description'] ?? 'Selamat datang di portal resmi pendaftaran Al-Azhar Cairo Palembang.' }}</p>
                <div class="d-flex flex-column flex-sm-row gap-2 pt-1">
                    <a href="{{ route('registration.create') }}" class="btn btn-gold btn-lg px-4 py-2.5 rounded-pill text-center shadow-xs" data-testid="hero-daftar-btn">
                        Daftar Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('registration.check') }}" class="btn btn-outline-light btn-lg px-4 py-2.5 rounded-pill text-center" data-testid="hero-cek-btn">
                        <i class="bi bi-search me-1"></i> Cek Pendaftaran
                    </a>
                </div>
            </div>
            <!-- SLIDER GAMBAR DINAMIS -->
            <div class="col-lg-5 col-md-10 mx-auto mx-lg-0">
                <div id="heroImageCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden position-relative" data-bs-ride="carousel" style="border:1px solid rgba(255,255,255,.18);">
                    @if(isset($sliders) && $sliders->count() > 1)
                        <div class="carousel-indicators mb-2">
                            @foreach($sliders as $idx => $s)
                                <button type="button" data-bs-target="#heroImageCarousel" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}" aria-current="{{ $idx === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $idx + 1 }}"></button>
                            @endforeach
                        </div>
                    @endif
                    <div class="carousel-inner">
                        @forelse($sliders ?? [] as $idx => $s)
                            <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}" data-bs-interval="3500">
                                <div class="position-relative">
                                    <img src="{{ asset($s->image_path) }}" class="hero-slider-img" alt="{{ $s->caption ?? 'Slide '.($idx+1) }}">
                                    @if(!empty($s->caption))
                                        <div class="carousel-caption-subtle">
                                            <i class="bi bi-camera me-1 opacity-75"></i><span>{{ $s->caption }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="carousel-item active">
                                <img src="{{ asset('images/pgnew1.jpeg') }}" class="hero-slider-img" alt="Slide">
                            </div>
                        @endforelse
                    </div>
                    @if(isset($sliders) && $sliders->count() > 1)
                        <button class="carousel-control-prev hero-carousel-btn" type="button" data-bs-target="#heroImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next hero-carousel-btn" type="button" data-bs-target="#heroImageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            </div>
            <!-- AKHIR PERUBAHAN SLIDER -->
        </div>
    </div>
</section>

<!-- PILIH JENJANG -->
<section class="py-4 py-md-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-md-end flex-column flex-md-row mb-3 mb-md-4 gap-1 gap-md-2">
            <div>
                <span class="text-uppercase small fw-bold" style="letter-spacing:.15em;color:var(--azhar-green);">Jenjang Pendidikan</span>
                <h2 class="section-title mb-0" style="font-size:clamp(1.4rem, 3vw, 2rem);">Pilih Jenjang Pendaftaran</h2>
            </div>
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Aturan usia dihitung per 1 Juli tahun ajaran</small>
        </div>
        <div class="row g-2 g-sm-3 justify-content-center">
            @foreach($grades as $g)
                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                    <a href="{{ route('registration.create', ['grade' => $g->id, 'year' => optional($activeYear)->id]) }}" class="text-decoration-none h-100 d-block" data-testid="grade-card-{{ strtolower($g->code) }}">
                        <div class="grade-card p-3 text-center h-100 d-flex flex-column align-items-center justify-content-between">
                            <div>
                                <div class="grade-badge mx-auto mb-2">{{ $g->code }}</div>
                                <div class="fw-bold" style="color:var(--azhar-green-dark);font-size:0.95rem;">{{ $g->name }}</div>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size:0.75rem;line-height:1.4;">{{ Str::limit($g->description, 50) }}</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- INFORMASI PPDB (PERSYARATAN, ALUR, JADWAL) -->
<section class="py-4 py-md-5" style="background:#fff;border-top:1px solid #F1E9FA;">
    <div class="container">
        <div class="row g-3 g-md-4">
            <div class="col-12 col-md-4">
                <div class="info-feature-card h-100 p-3 p-md-4 rounded-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="info-icon-badge"><i class="bi bi-clipboard-check fs-5"></i></span>
                            <h4 class="section-title h5 mb-0 fw-bold" style="color:var(--azhar-green-dark);">Persyaratan</h4>
                        </div>
                        <p class="text-muted small mb-3" style="line-height:1.6;white-space:pre-line;">
                            {{ Str::limit($settings['info_persyaratan'] ?? 'Informasi persyaratan pendaftaran santri baru.', 190) }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('info.persyaratan') }}" class="btn btn-sm btn-outline-azhar rounded-pill px-3 py-1.5 w-100 w-sm-auto text-center">
                            Lihat Persyaratan <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="info-feature-card h-100 p-3 p-md-4 rounded-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="info-icon-badge"><i class="bi bi-diagram-3 fs-5"></i></span>
                            <h4 class="section-title h5 mb-0 fw-bold" style="color:var(--azhar-green-dark);">Alur Pendaftaran</h4>
                        </div>
                        <p class="text-muted small mb-3" style="line-height:1.6;white-space:pre-line;">
                            {{ Str::limit($settings['info_alur'] ?? 'Informasi tahapan pendaftaran murid baru.', 190) }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('info.alur') }}" class="btn btn-sm btn-outline-azhar rounded-pill px-3 py-1.5 w-100 w-sm-auto text-center">
                            Lihat Alur <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="info-feature-card h-100 p-3 p-md-4 rounded-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="info-icon-badge"><i class="bi bi-calendar-event fs-5"></i></span>
                            <h4 class="section-title h5 mb-0 fw-bold" style="color:var(--azhar-green-dark);">Jadwal</h4>
                        </div>
                        <p class="text-muted small mb-3" style="line-height:1.6;white-space:pre-line;">
                            {{ Str::limit($settings['info_jadwal'] ?? 'Jadwal pembukaan dan pelaksanaan tes observasi.', 190) }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('info.jadwal') }}" class="btn btn-sm btn-outline-azhar rounded-pill px-3 py-1.5 w-100 w-sm-auto text-center">
                            Lihat Jadwal <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
