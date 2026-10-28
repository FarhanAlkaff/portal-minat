@extends('layouts.admin')
@section('title', 'Grade & Aturan Usia')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h1 class="section-title mb-0">Grade & Aturan Usia</h1>
        <small class="text-muted">Kelola jenjang pendidikan, urutan tampilan formulir, dan ketentuan batas usia calon siswa</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-mortarboard-fill text-primary"></i> Total: <b>{{ $items->count() }}</b>
        </span>
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-check-circle-fill text-success"></i> Aktif: <b>{{ $items->where('is_active', true)->count() }}</b>
        </span>
        <button type="button" class="btn btn-azhar px-3 py-2 rounded-3 shadow-xs d-inline-flex align-items-center gap-2" onclick="openAddGradeModal()">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Tambah Jenjang</span>
        </button>
    </div>
</div>

<!-- Tabel Grade Full Width -->
<div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
        <span class="fw-bold text-dark"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Daftar Jenjang & Batasan Usia</span>
        <small class="text-muted">{{ $items->count() }} jenjang terdaftar</small>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:10%;">Kode</th>
                    <th style="width:30%;">Nama Jenjang</th>
                    <th style="width:28%;">Aturan Usia</th>
                    <th class="text-center" style="width:12%;">Pendaftar</th>
                    <th class="text-center" style="width:10%;">Status</th>
                    <th class="text-end" style="width:10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $g)
                <tr>
                    <td>
                        <x-grade-badge :code="$g->code" size="md" />
                    </td>
                    <td>
                        <b class="text-dark">{{ $g->name }}</b>
                        @if($g->description)
                            <div class="text-muted small text-truncate" style="max-width:280px;">{{ $g->description }}</div>
                        @endif
                        <div class="text-muted" style="font-size:0.7rem;">Urutan tampil: #{{ $g->sort_order }}</div>
                    </td>
                    <td>
                        @if($g->ageRule)
                            <div class="d-flex flex-column gap-1" style="font-size:0.75rem;">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-light text-dark border px-1">Min</span>
                                    <span>{{ $g->ageRule->minimum_age_years ?? 0 }} th {{ $g->ageRule->minimum_age_months ?? 0 }} bl</span>
                                </div>
                                @if($g->ageRule->maximum_age_years)
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-light text-dark border px-1">Max</span>
                                    <span>{{ $g->ageRule->maximum_age_years }} th {{ $g->ageRule->maximum_age_months ?? 0 }} bl</span>
                                </div>
                                @endif
                                <span class="text-muted" style="font-size:0.7rem;">Acuan: {{ str_pad($g->ageRule->reference_day, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($g->ageRule->reference_month, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:0.7rem;">
                                <i class="bi bi-exclamation-triangle me-1"></i>Belum Diatur
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-2 py-1 {{ $g->registrations_count > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border' }}">
                            <i class="bi bi-people me-1"></i>{{ $g->registrations_count }} siswa
                        </span>
                    </td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.grades.save') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="id" value="{{ $g->id }}">
                            <input type="hidden" name="code" value="{{ $g->code }}">
                            <input type="hidden" name="name" value="{{ $g->name }}">
                            <input type="hidden" name="description" value="{{ $g->description }}">
                            <input type="hidden" name="sort_order" value="{{ $g->sort_order }}">
                            <input type="hidden" name="is_active" value="{{ $g->is_active ? 0 : 1 }}">
                            @if($g->is_active)
                                <button type="submit" class="btn btn-sm p-1 px-2 rounded-pill" style="background:#DCFCE7;color:#15803D;font-size:0.75rem;border:1px solid #BBF7D0;" title="Klik untuk nonaktifkan">
                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm btn-light border text-muted p-1 px-2 rounded-pill" style="font-size:0.75rem;" title="Klik untuk aktifkan">
                                    <i class="bi bi-slash-circle me-1"></i>Nonaktif
                                </button>
                            @endif
                        </form>
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('admin.grades.age-rule', $g) }}" class="btn btn-sm btn-outline-success rounded-2 px-2" title="Kelola Batasan Usia">
                                <i class="bi bi-sliders me-1"></i>Usia
                            </a>
                            <button class="btn btn-sm btn-outline-primary rounded-2 px-2" title="Edit Jenjang" onclick='openEditGradeModal(@json($g))'>
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            @if($g->registrations_count == 0)
                                <form method="POST" action="{{ route('admin.grades.destroy', $g) }}" class="d-inline" onsubmit="return confirm('Hapus jenjang \'{{ addslashes($g->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Jenjang">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-light border text-muted rounded-2 px-2" title="Jenjang memiliki {{ $g->registrations_count }} data pendaftaran" onclick="alert('Jenjang \'{{ addslashes($g->name) }}\' tidak dapat dihapus karena masih digunakan oleh {{ $g->registrations_count }} data pendaftaran.')">
                                    <i class="bi bi-trash text-muted"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        Belum ada data jenjang. Klik tombol <b>Tambah Jenjang</b> untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 bg-light border-top small text-muted d-flex align-items-center gap-2">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Aturan usia digunakan oleh sistem saat calon siswa mendaftar untuk menentukan status kelayakan usia secara otomatis.</span>
    </div>
</div>

<!-- Modal Pop-Up Tambah / Edit Grade -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom p-3 px-4" style="background:#FAF7FC;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;border-radius:10px;background:#EDE9FE;color:#6B2D8C;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-mortarboard" id="grade-modal-icon"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="gradeModalLabel">Tambah Jenjang</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.grades.save') }}" id="gradeForm">
                @csrf
                <input type="hidden" name="id" id="modal_g_id">

                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-5">
                            <label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                            <input name="code" id="modal_g_code" class="form-control text-uppercase" placeholder="SD / SMP" required maxlength="10">
                        </div>
                        <div class="col-7">
                            <label class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" name="sort_order" id="modal_g_sort" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Jenjang <span class="text-danger">*</span></label>
                        <input name="name" id="modal_g_name" class="form-control" placeholder="Sekolah Dasar (SD)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Singkat</label>
                        <textarea name="description" id="modal_g_desc" rows="2" class="form-control" placeholder="Keterangan singkat tentang jenjang ini..."></textarea>
                    </div>

                    <div class="form-check form-switch p-3 rounded-3" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="modal_g_act" value="1" checked role="switch">
                        <label class="form-check-label fw-semibold" for="modal_g_act" style="cursor:pointer;">
                            Status Aktif
                        </label>
                        <div class="text-muted" style="font-size:0.72rem;">Jika aktif, jenjang akan tampil dan dapat dipilih pada form pendaftaran publik.</div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 px-4 bg-light">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-azhar px-4 rounded-3 shadow-xs">
                        <i class="bi bi-save me-1"></i> <span id="grade-modal-submit-text">Simpan Jenjang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let gradeModalInstance = null;
document.addEventListener('DOMContentLoaded', function() {
    gradeModalInstance = new bootstrap.Modal(document.getElementById('gradeModal'));
});

function openAddGradeModal() {
    document.getElementById('modal_g_id').value = '';
    document.getElementById('modal_g_code').value = '';
    document.getElementById('modal_g_name').value = '';
    document.getElementById('modal_g_desc').value = '';
    document.getElementById('modal_g_sort').value = 0;
    document.getElementById('modal_g_act').checked = true;

    document.getElementById('gradeModalLabel').innerText = 'Tambah Jenjang Baru';
    document.getElementById('grade-modal-icon').className = 'bi bi-mortarboard';
    document.getElementById('grade-modal-submit-text').innerText = 'Simpan Jenjang';

    gradeModalInstance.show();
    setTimeout(() => document.getElementById('modal_g_code').focus(), 300);
}

function openEditGradeModal(g) {
    document.getElementById('modal_g_id').value = g.id;
    document.getElementById('modal_g_code').value = g.code;
    document.getElementById('modal_g_name').value = g.name;
    document.getElementById('modal_g_desc').value = g.description || '';
    document.getElementById('modal_g_sort').value = g.sort_order || 0;
    document.getElementById('modal_g_act').checked = !!g.is_active;

    document.getElementById('gradeModalLabel').innerText = 'Edit Jenjang: ' + g.code;
    document.getElementById('grade-modal-icon').className = 'bi bi-pencil-square';
    document.getElementById('grade-modal-submit-text').innerText = 'Perbarui Jenjang';

    gradeModalInstance.show();
    setTimeout(() => document.getElementById('modal_g_name').focus(), 300);
}
</script>
@endpush
@endsection
