<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PORMIN') · Al-Azhar Cairo Palembang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root{
            /* Kombinasi Ungu Elegan & Modern (Tanpa Kuning/Emas) */
            --azhar-green:#6B2D8C;         /* Ungu Utama (Deep Violet) */
            --azhar-green-dark:#3A154F;    /* Ungu Gelap Mewah (Plum Dark) */
            --azhar-gold:#A78BFA;          /* Aksen Ungu Pastel Cerah (Lavender) pengganti emas */
            --azhar-cream:#F8F6FB;         /* Background Putih Bersih bernuansa ungu sangat lembut */
            --azhar-ink:#1F1626;           /* Teks Gelap Kontras Tinggi & Nyaman */
        }
        html,body{font-family:'Instrument Sans',system-ui,sans-serif;color:var(--azhar-ink);background:var(--azhar-cream);}
        h1,h2,h3,.display-1,.display-2,.display-3,.display-4,.display-5,.display-6,.hero-title{font-family:'Fraunces',Georgia,serif;letter-spacing:-.01em;}
        a{color:var(--azhar-green);}
        
        .navbar-azhar{background:linear-gradient(90deg,var(--azhar-green-dark),var(--azhar-green));}
        .navbar-azhar .navbar-brand,.navbar-azhar .nav-link{color:#ffffff !important;}
        .navbar-azhar .nav-link:hover{color:#E9D8FD !important;}
        
        .brand-mark{width:44px;height:44px;background:#9333EA;color:#ffffff;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-family:'Fraunces',serif;font-size:1.35rem;}
        
        .btn-azhar{background:var(--azhar-green);color:#fff;border:none;font-weight:600;}
        .btn-azhar:hover{background:var(--azhar-green-dark);color:#fff;}
        .btn-outline-azhar{background:transparent;color:var(--azhar-green);border:1.5px solid var(--azhar-green);font-weight:600;}
        .btn-outline-azhar:hover{background:var(--azhar-green);color:#fff;}
        
        /* Tombol Aksen Utama (Putih bersih/terang agar kontras dan elegan di atas ungu) */
        .btn-gold{background:#ffffff;color:var(--azhar-green-dark);border:none;font-weight:700;}
        .btn-gold:hover{background:#F3E8FF;color:var(--azhar-green-dark);}
        
        /* Hero Section Ungu Mewah & Elegan */
        .hero{background:linear-gradient(135deg,#240A34 0%,var(--azhar-green-dark) 55%,var(--azhar-green) 100%);color:#fff;position:relative;overflow:hidden;}
        .hero::after{content:"";position:absolute;inset:0;background-image:radial-gradient(circle at 15% 20%, rgba(167,139,250,.18), transparent 40%),radial-gradient(circle at 85% 80%, rgba(255,255,255,.06), transparent 40%);}
        .hero-inner{position:relative;z-index:2;}
        
        .grade-card{border:1px solid #E9D8FD;border-radius:16px;background:#fff;transition:all .2s ease;height:100%;}
        .grade-card:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(107,45,140,.15);border-color:#9333EA;}
        .grade-badge{width:56px;height:56px;border-radius:14px;background:#F3E8FF;color:var(--azhar-green-dark);display:flex;align-items:center;justify-content:center;font-weight:700;font-family:'Fraunces',serif;font-size:1.15rem;border:1px solid #E9D8FD;}
        
        .section-title{font-family:'Fraunces',serif;font-weight:700;letter-spacing:-.01em;}
        .footer{background:var(--azhar-green-dark);color:#E9D8FD;}
        .form-label{font-weight:600;color:var(--azhar-green-dark);}
        .required::after{content:" *";color:#E056FD;}
        
        .status-pill{padding:.3rem .7rem;border-radius:999px;font-size:.78rem;font-weight:600;letter-spacing:.02em;}
        .status-terdaftar{background:#F3E8FF;color:#6B2D8C;}
        .status-menunggu{background:#FEF3C7;color:#92400E;}
        .status-lolos{background:#E0E7FF;color:#3730A3;}
        .status-layak{background:#F3E8FF;color:#6B2D8C;}
        .status-dipanggil{background:#FCE7F3;color:#831843;}
        .status-diterima{background:#6B2D8C;color:#fff;}
        .status-ditolak,.status-tidak{background:#FEE2E2;color:#991B1B;}
        
        .sidebar{background:linear-gradient(180deg,#240835 0%,#150221 100%);width:270px;min-width:270px;min-height:100vh;position:sticky;top:0;max-height:100vh;overflow-y:auto;overflow-x:hidden;color:#E9D8FD;border-right:1px solid rgba(233,216,253,0.12);transition:width .24s cubic-bezier(0.4,0,0.2,1),min-width .24s cubic-bezier(0.4,0,0.2,1),margin-left .24s cubic-bezier(0.4,0,0.2,1);z-index:1040;}
        .sidebar .nav-link-item{color:#D8B4FE;text-decoration:none;display:flex;align-items:center;padding:.65rem .9rem;border-radius:10px;font-weight:500;font-size:.9rem;transition:all .18s ease;white-space:nowrap;}
        .sidebar .nav-link-item:hover{background:rgba(167,139,250,.15);color:#fff;transform:translateX(3px);}
        .sidebar .nav-link-item.active{background:linear-gradient(90deg,rgba(147,51,234,.45),rgba(147,51,234,.2));color:#fff;border-left:3.5px solid #C084FC;font-weight:600;}
        .sidebar a{color:#E9D8FD;text-decoration:none;}

        /* Desktop Mini-Sidebar Collapse */
        @media (min-width:992px){
            .sidebar.is-collapsed{width:76px !important;min-width:76px !important;padding-left:.5rem !important;padding-right:.5rem !important;}
            .sidebar.is-collapsed .sidebar-hide-on-collapse{display:none !important;}
            .sidebar.is-collapsed .nav-link-item{justify-content:center !important;padding:.7rem 0 !important;text-align:center;}
            .sidebar.is-collapsed .nav-link-item i{margin-right:0 !important;font-size:1.25rem;}
            .sidebar.is-collapsed .brand-wrap{justify-content:center !important;flex-direction:column !important;gap:.4rem !important;}
            .sidebar.is-collapsed .user-info-wrap{display:none !important;}
            .sidebar.is-collapsed .user-card-wrap{justify-content:center !important;padding:.3rem !important;}
            .sidebar.is-collapsed #sidebarToggleInsideBtn i{transform:rotate(180deg);}
        }

        .sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(15,6,23,0.55);backdrop-filter:blur(3px);z-index:1030;}
        .sidebar-backdrop.show{display:block;}

        /* Mobile Drawer */
        @media (max-width:991.98px){
            .sidebar{position:fixed;left:0;top:0;bottom:0;margin-left:-270px;box-shadow:0 0 50px rgba(0,0,0,0.5);}
            .sidebar.is-open{margin-left:0 !important;}
        }

        .admin-main{background:#F8FAFD;min-height:100vh;transition:all .24s ease;}
        .admin-topbar{background:#fff;border-bottom:1px solid #EBE6F3;}
        .admin-card{background:#fff;border-radius:16px;border:1px solid #ECE7F5;box-shadow:0 4px 20px rgba(107,45,140,.04);transition:transform .2s ease,box-shadow .2s ease;}
        .admin-card:hover{box-shadow:0 8px 28px rgba(107,45,140,.08);}

        .kpi-card{background:#fff;border-radius:16px;border:1px solid #ECE7F5;padding:1.25rem;transition:all .2s ease;box-shadow:0 2px 12px rgba(107,45,140,.04);}
        .kpi-card:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(107,45,140,.12);border-color:#D8B4FE;}
        .kpi-card .kpi-num{font-family:'Fraunces',serif;font-size:2.25rem;font-weight:700;color:var(--azhar-green-dark);line-height:1.1;}
        .kpi-icon-wrap{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;}
        
        .timeline{border-left:2px solid #9333EA;padding-left:1rem;}
        .timeline-item{position:relative;padding-bottom:.9rem;}
        .timeline-item::before{content:"";position:absolute;left:-1.4rem;top:.35rem;width:.8rem;height:.8rem;background:#9333EA;border-radius:50%;}
        .table-vcenter td,.table-vcenter th{vertical-align:middle;}
        .progress-slim{height:8px;border-radius:999px;background:#EDE9FE;}
    </style>
    @stack('head')
</head>
<body>
@yield('body')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window._token = document.querySelector('meta[name=csrf-token]').content;
</script>
@stack('scripts')
</body>
</html>