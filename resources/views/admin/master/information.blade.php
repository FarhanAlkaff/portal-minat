@extends('layouts.admin')
@section('title', 'Pengaturan Website')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h1 class="section-title mb-0">Pengaturan Website & PPDB</h1>
        <small class="text-muted">Kelola konten landing page, teks informasi, pengumuman berjalan, dan integrasi WhatsApp</small>
    </div>
    <div>
        <a href="{{ route('landing') }}" target="_blank" class="btn btn-outline-secondary rounded-3 px-3 shadow-xs">
            <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Landing Page
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.information.save') }}" enctype="multipart/form-data" id="settingsForm">
    @csrf

    <!-- Static Top Navigation & Save Bar -->
    <div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <ul class="nav nav-pills gap-1" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-3 py-2 fw-semibold" id="branding-tab" data-bs-toggle="pill" data-bs-target="#branding" type="button" role="tab">
                        <i class="bi bi-palette me-1"></i> Branding & Hero
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="content-tab" data-bs-toggle="pill" data-bs-target="#content-info" type="button" role="tab">
                        <i class="bi bi-file-text me-1"></i> Konten PPDB
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="announcement-tab" data-bs-toggle="pill" data-bs-target="#announcement" type="button" role="tab">
                        <i class="bi bi-megaphone me-1"></i> Pengumuman
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="whatsapp-tab" data-bs-toggle="pill" data-bs-target="#whatsapp" type="button" role="tab">
                        <i class="bi bi-whatsapp me-1 text-success"></i> WhatsApp
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact" type="button" role="tab">
                        <i class="bi bi-telephone me-1"></i> Kontak
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="slider-tab" data-bs-toggle="pill" data-bs-target="#slider-content" type="button" role="tab">
                        <i class="bi bi-images me-1 text-azhar"></i> Slider Foto <span class="badge bg-secondary rounded-pill ms-1" style="font-size:0.7rem;">{{ $sliders->count() }}</span>
                    </button>
                </li>
            </ul>

            <button type="submit" class="btn btn-azhar px-4 py-2 rounded-3 shadow-xs" data-testid="btn-save-info">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="settingsTabContent">
        <!-- TAB 1: BRANDING & HERO -->
        <div class="tab-pane fade show active" id="branding" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-stars text-warning"></i> Bagian Hero Landing Page
                        </h5>
                        <p class="text-muted small">Teks utama yang pertama kali dilihat pengunjung saat membuka halaman depan.</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Hero (Headline) <span class="text-danger">*</span></label>
                            <input class="form-control" name="landing_hero_title" value="{{ $settings['landing_hero_title'] ?? '' }}" placeholder="Penerimaan Peserta Didik Baru">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Subjudul Hero</label>
                            <input class="form-control" name="landing_hero_subtitle" value="{{ $settings['landing_hero_subtitle'] ?? '' }}" placeholder="Tahun Ajaran 2026/2027">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Deskripsi Singkat Hero</label>
                            <textarea class="form-control" rows="3" name="landing_hero_description" placeholder="Selamat datang di portal resmi pendaftaran Al-Azhar Cairo Palembang...">{{ $settings['landing_hero_description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4">
                        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-image text-primary"></i> Logo Sekolah
                        </h5>
                        @php $logo = $settings['logo_path'] ?? null; @endphp
                        <div class="text-center p-3 mb-3 rounded-4" style="background:#FAF7FC;border:1px dashed #D8B4FE;">
                            @if($logo && file_exists(public_path($logo)))
                                <img src="{{ asset($logo) }}?v={{ time() }}" style="max-width:140px;max-height:140px;object-fit:contain;" data-testid="current-logo" class="img-fluid rounded-2">
                                <div class="mt-2 small text-muted">Logo saat ini aktif</div>
                            @else
                                <div class="text-muted py-4">
                                    <i class="bi bi-image display-5 d-block text-muted opacity-50 mb-2"></i>
                                    <span>Belum ada logo terunggah</span>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Unggah File Logo Baru</label>
                            <input type="file" name="logo" accept="image/*" class="form-control" data-testid="input-logo">
                            <small class="text-muted">Format: PNG, SVG, JPG, WebP (Maks. 2MB)</small>
                        </div>

                        @if($logo)
                        <div class="form-check form-switch p-2 rounded-3" style="background:#FFF1F2;border:1px solid #FECDD3;">
                            <input type="checkbox" class="form-check-input ms-0 me-2" name="logo_remove" value="1" id="rm" role="switch">
                            <label for="rm" class="form-check-label text-danger fw-semibold" style="cursor:pointer;font-size:0.85rem;">
                                Hapus logo saat ini
                            </label>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: KONTEN PPDB -->
        <div class="tab-pane fade" id="content-info" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-journal-text text-primary"></i> Konten Halaman Informasi PPDB
                        </h5>
                        <small class="text-muted">Teks panduan dan rincian yang ditampilkan pada menu informasi pendaftaran calon siswa</small>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 h-100" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle text-primary"></i> Informasi Umum Pendaftaran
                            </label>
                            <textarea class="form-control" rows="5" name="info_pendaftaran" placeholder="Rangkuman umum mekanisme pendaftaran...">{{ $settings['info_pendaftaran'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 h-100" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-check2-square text-success"></i> Persyaratan Pendaftaran
                            </label>
                            <textarea class="form-control" rows="5" name="info_persyaratan" placeholder="Syarat berkas: Akta Kelahiran, Kartu Keluarga, Pasfoto...">{{ $settings['info_persyaratan'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 h-100" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-diagram-3 text-info"></i> Alur Pendaftaran
                            </label>
                            <textarea class="form-control" rows="5" name="info_alur" placeholder="Langkah 1: Mengisi formulir online, Langkah 2: Verifikasi...">{{ $settings['info_alur'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 h-100" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-check text-warning"></i> Jadwal & Gelombang
                            </label>
                            <textarea class="form-control" rows="5" name="info_jadwal" placeholder="Gelombang 1: Oktober - Desember, Tes Observasi: Setiap Sabtu...">{{ $settings['info_jadwal'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                            <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-question-circle text-secondary"></i> Tanya Jawab (FAQ)
                            </label>
                            <textarea class="form-control" rows="5" name="info_faq" placeholder="Pertanyaan yang sering ditanyakan orang tua siswa...">{{ $settings['info_faq'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: PENGUMUMAN -->
        <div class="tab-pane fade" id="announcement" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-megaphone-fill text-warning"></i> Papan Pengumuman Berjalan (Marquee)
                        </h5>
                        <small class="text-muted">Muncul sebagai pita pengumuman di bagian paling atas halaman publik</small>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Judul Pengumuman</label>
                        <input class="form-control" name="announcement_title" value="{{ $settings['announcement_title'] ?? '' }}" placeholder="Pengumuman Penting PPDB">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status Pengumuman</label>
                        <select class="form-select" name="announcement_active" data-testid="announcement-active">
                            <option value="1" @selected(($settings['announcement_active'] ?? '1')==='1')>🟢 Aktif (Tampilkan)</option>
                            <option value="0" @selected(($settings['announcement_active'] ?? '1')==='0')>⚪ Nonaktif (Sembunyikan)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Daftar Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="5" name="announcement_items" data-testid="announcement-items" placeholder="Gelombang 1 dibuka sampai 30 November&#10;Diskon uang pangkal 10% bagi pendaftar awal">{{ $settings['announcement_items'] ?? '' }}</textarea>
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Tulis <b>satu pengumuman per baris</b> (gunakan tombol Enter untuk baris berikutnya).</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: WHATSAPP -->
        <div class="tab-pane fade" id="whatsapp" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp text-success"></i> Integrasi Notifikasi WhatsApp
                        </h5>
                        <small class="text-muted">Konfigurasi pesan konfirmasi pendaftaran dan panggilan observasi ke nomor orang tua</small>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kode Negara Default</label>
                        <div class="input-group">
                            <span class="input-group-text">+</span>
                            <input class="form-control" name="wa_country_code" value="{{ $settings['wa_country_code'] ?? '62' }}">
                        </div>
                        <small class="text-muted">Contoh: 62 untuk Indonesia</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Metode Pengiriman (Driver)</label>
                        <select class="form-select" name="wa_driver" data-testid="wa-driver" onchange="toggleWaToken(this.value)">
                            <option value="link" @selected(($settings['wa_driver'] ?? 'link')==='link')>🔗 Link Manual (wa.me) — Gratis & Fleksibel</option>
                            <option value="fonnte" @selected(($settings['wa_driver'] ?? 'link')==='fonnte')>⚡ Fonnte API — Otomatis & Terjadwal</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Token API Fonnte</label>
                        <input type="password" class="form-control" name="wa_fonnte_token" id="wa_fonnte_token" placeholder="Masukkan token fonnte.com" value="{{ $settings['wa_fonnte_token'] ?? '' }}">
                        <small class="text-muted">Diperlukan jika memilih metode Fonnte API</small>
                    </div>
                </div>

                <div class="p-3 rounded-3" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check text-success me-1"></i>Perbedaan Metode:</h6>
                    <ul class="text-muted small mb-0 ps-3 d-flex flex-column gap-1">
                        <li><b>Link Manual (wa.me):</b> Admin cukup mengklik tombol WA di dashboard pendaftaran untuk langsung membuka WhatsApp Web / Aplikasi WA dengan pesan yang sudah terisi otomatis. Sangat aman dan tanpa biaya langganan API.</li>
                        <li><b>Fonnte API:</b> Membutuhkan akun berbayar di fonnte.com untuk mengirimkan pesan otomatis langsung di latar belakang ke nomor orang tua.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- TAB 5: KONTAK -->
        <div class="tab-pane fade" id="contact" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-inbound text-primary"></i> Kontak & Lokasi Sekretariat PPDB
                        </h5>
                        <small class="text-muted">Kontak ini akan ditampilkan pada footer halaman publik dan lembar bukti pendaftaran</small>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nomor Telepon / WhatsApp CS</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input class="form-control" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" placeholder="0812-3456-7890">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Alamat Email Resmi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input class="form-control" type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" placeholder="info@alazharcairoplg.sch.id">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Lengkap Kantor Sekretariat</label>
                        <textarea class="form-control" rows="3" name="contact_address" placeholder="Jl. Jend. Sudirman KM. 3, Palembang, Sumatera Selatan">{{ $settings['contact_address'] ?? '' }}</textarea>
                        <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>Tekan <strong>Enter</strong> untuk membuat baris baru jika memiliki lebih dari 1 cabang/kampus.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 6: SLIDER FOTO HERO -->
        <div class="tab-pane fade" id="slider-content" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-images text-azhar"></i> Kelola Foto Slider Landing Page
                        </h5>
                        <p class="text-muted small mb-0">Foto-foto ini akan berganti secara otomatis di bagian Hero halaman depan. Anda dapat mengunggah foto baru, mengatur urutan, dan menambahkan keterangan (caption).</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-azhar rounded-3 px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalAddSlider">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Foto Slider
                        </button>
                    </div>
                </div>

                @if($sliders->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-image fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="mb-2">Belum ada foto slider yang ditambahkan.</p>
                        <button type="button" class="btn btn-sm btn-outline-azhar rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddSlider">
                            <i class="bi bi-plus-circle me-1"></i> Unggah Foto Pertama
                        </button>
                    </div>
                @else
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">Urutan</th>
                                    <th style="width: 140px;">Foto</th>
                                    <th>Keterangan (Caption)</th>
                                    <th style="width: 120px;" class="text-center">Status</th>
                                    <th style="width: 130px;" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sliders as $s)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace fs-6">#{{ $s->sort_order }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ asset($s->image_path) }}" target="_blank" title="Klik untuk perbesar">
                                                <img src="{{ asset($s->image_path) }}" alt="{{ $s->caption ?? 'Slide' }}" class="rounded-3 shadow-2xs border" style="width: 110px; height: 68px; object-fit: cover;">
                                            </a>
                                        </td>
                                        <td>
                                            @if($s->caption)
                                                <div class="fw-semibold text-dark">{{ $s->caption }}</div>
                                                <small class="text-muted"><i class="bi bi-card-text me-1"></i>Tampil sebagai label kecil di sudut foto</small>
                                            @else
                                                <span class="text-muted fst-italic small"><i class="bi bi-dash-circle me-1"></i>Tanpa keterangan foto</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($s->is_active)
                                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                                    <i class="bi bi-eye-slash me-1"></i>Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 px-2" title="Edit Slide"
                                                    onclick="editSlider({{ json_encode($s) }})">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Slide"
                                                    onclick="deleteSlider('{{ route('admin.sliders.destroy', $s) }}', '{{ addslashes($s->caption ?? 'Foto Slide #' . $s->sort_order) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4 mb-4">
        <button type="submit" class="btn btn-azhar px-4 py-2 rounded-3 shadow-xs">
            <i class="bi bi-save me-1"></i> Simpan Semua Pengaturan
        </button>
    </div>
</form>

<!-- Form Hapus Tersembunyi (Anti Form Nesting) -->
<form id="deleteSliderForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal Tambah Slider -->
<div class="modal fade" id="modalAddSlider" tabindex="-1" aria-labelledby="modalAddSliderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.sliders.save') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalAddSliderLabel"><i class="bi bi-plus-circle text-azhar me-1"></i> Tambah Foto Slider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih File Foto <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                    <small class="text-muted d-block mt-1">Disarankan format landscape (rasio 16:9 atau sekitar 800x500px). Maksimal 3 MB.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan Foto (Caption)</label>
                    <input type="text" name="caption" class="form-control" placeholder="Contoh: Gedung Kampus Sudirman" maxlength="150">
                    <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>Keterangan akan tampil kecil & rapi di sudut foto pada landing page.</small>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ ($sliders->max('sort_order') ?? 0) + 1 }}" min="0" max="999">
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="add_slider_active" value="1" checked>
                            <label class="form-check-label fw-semibold" for="add_slider_active">Tampilkan di Web</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-azhar rounded-3 px-4"><i class="bi bi-upload me-1"></i> Unggah Foto</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Slider -->
<div class="modal fade" id="modalEditSlider" tabindex="-1" aria-labelledby="modalEditSliderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.sliders.save') }}" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <input type="hidden" name="id" id="edit_slider_id">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalEditSliderLabel"><i class="bi bi-pencil-square text-azhar me-1"></i> Edit Foto Slider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3 text-center">
                    <img id="edit_slider_preview" src="" alt="Preview" class="rounded-3 border shadow-2xs w-100" style="max-height: 180px; object-fit: cover;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ganti Foto (Opsional)</label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengganti file foto.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan Foto (Caption)</label>
                    <input type="text" name="caption" id="edit_slider_caption" class="form-control" placeholder="Contoh: Gedung Kampus Sudirman" maxlength="150">
                    <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>Keterangan akan tampil kecil & rapi di sudut foto pada landing page.</small>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="sort_order" id="edit_slider_sort" class="form-control" min="0" max="999">
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_slider_active" value="1">
                            <label class="form-check-label fw-semibold" for="edit_slider_active">Tampilkan di Web</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-azhar rounded-3 px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleWaToken(driver) {
    const input = document.getElementById('wa_fonnte_token');
    if (driver === 'fonnte') {
        input.removeAttribute('disabled');
        input.focus();
    }
}

function editSlider(item) {
    document.getElementById('edit_slider_id').value = item.id;
    document.getElementById('edit_slider_preview').src = '{{ asset('') }}' + item.image_path.replace(/^\/+/, '');
    document.getElementById('edit_slider_caption').value = item.caption || '';
    document.getElementById('edit_slider_sort').value = item.sort_order;
    document.getElementById('edit_slider_active').checked = !!item.is_active;

    const modal = new bootstrap.Modal(document.getElementById('modalEditSlider'));
    modal.show();
}

function deleteSlider(url, caption) {
    if (confirm('Apakah Anda yakin ingin menghapus foto slider "' + caption + '"?')) {
        const form = document.getElementById('deleteSliderForm');
        form.action = url;
        form.submit();
    }
}

// Persist active tab across page reloads
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;
    if (hash) {
        const triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    }
    const tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('data-bs-target');
            if (target) history.replaceState(null, null, target);
        });
    });
});
</script>
@endpush
@endsection
