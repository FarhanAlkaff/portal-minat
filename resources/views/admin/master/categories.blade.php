@extends('layouts.admin')
@section('title', 'Kategori Siswa')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h1 class="section-title mb-0">Kategori Siswa</h1>
        <small class="text-muted">Kelola jenis pendaftaran (misal: Siswa Baru, Pindahan, Internal/Alumni)</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-tags-fill text-primary"></i> Total: <b>{{ $items->count() }}</b>
        </span>
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-check-circle-fill text-success"></i> Aktif: <b>{{ $items->where('is_active', true)->count() }}</b>
        </span>
        <button type="button" class="btn btn-azhar px-3 py-2 rounded-3 shadow-xs d-inline-flex align-items-center gap-2" onclick="openAddCategoryModal()">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>
</div>

<!-- Tabel Kategori Full Width -->
<div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
        <span class="fw-bold text-dark"><i class="bi bi-tags-fill me-2 text-primary"></i>Daftar Kategori Pendaftaran</span>
        <small class="text-muted">{{ $items->count() }} kategori tercatat</small>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:5%;" class="text-center">No</th>
                    <th style="width:50%;">Nama Kategori Siswa</th>
                    <th class="text-center" style="width:20%;">Pendaftar</th>
                    <th class="text-center" style="width:15%;">Status</th>
                    <th class="text-end" style="width:10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $idx => $i)
                <tr>
                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:10px;background:{{ $i->is_active ? '#EDE9FE' : '#F1F5F9' }};color:{{ $i->is_active ? '#6B2D8C' : '#94A3B8' }};display:flex;align-items:center;justify-content:center;font-size:1rem;">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                            <b class="text-dark fs-6">{{ $i->name }}</b>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-2 py-1 {{ $i->registrations_count > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border' }}">
                            <i class="bi bi-people me-1"></i>{{ $i->registrations_count }} siswa
                        </span>
                    </td>
                    <td class="text-center">
                        @if($i->is_active)
                            <span class="badge rounded-pill px-2 py-1" style="background:#DCFCE7;color:#15803D;border:1px solid #BBF7D0;">
                                <i class="bi bi-check-circle-fill me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge rounded-pill px-2 py-1 bg-light text-muted border">
                                <i class="bi bi-slash-circle me-1"></i>Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary rounded-2 px-2" title="Edit Kategori" onclick='openEditCategoryModal(@json($i))'>
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            @if($i->registrations_count == 0)
                                <form method="POST" action="{{ route('admin.categories.destroy', $i) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ addslashes($i->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Kategori">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-light border text-muted rounded-2 px-2" title="Kategori masih digunakan oleh {{ $i->registrations_count }} pendaftar" onclick="alert('Kategori \'{{ addslashes($i->name) }}\' tidak dapat dihapus karena masih digunakan oleh {{ $i->registrations_count }} data pendaftaran.')">
                                    <i class="bi bi-trash text-muted"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        Belum ada kategori siswa. Klik tombol <b>Tambah Kategori</b> untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pop-Up Tambah / Edit Kategori -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom p-3 px-4" style="background:#FAF7FC;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;border-radius:10px;background:#EDE9FE;color:#6B2D8C;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-tag" id="category-modal-icon"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="categoryModalLabel">Tambah Kategori</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.categories.save') }}" id="categoryForm">
                @csrf
                <input type="hidden" name="id" id="modal_k_id">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input name="name" id="modal_k_name" class="form-control" placeholder="Contoh: Siswa Baru" required>
                        <small class="text-muted" style="font-size:0.75rem;">Nama kategori yang akan dipilih calon siswa saat mengisi formulir.</small>
                    </div>

                    <div class="form-check form-switch p-3 rounded-3" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="modal_k_act" value="1" checked role="switch">
                        <label class="form-check-label fw-semibold" for="modal_k_act" style="cursor:pointer;">
                            Status Aktif
                        </label>
                        <div class="text-muted" style="font-size:0.72rem;">Jika aktif, kategori ini akan muncul sebagai opsi pada halaman pendaftaran.</div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 px-4 bg-light">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-azhar px-4 rounded-3 shadow-xs">
                        <i class="bi bi-save me-1"></i> <span id="category-modal-submit-text">Simpan Kategori</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let categoryModalInstance = null;
document.addEventListener('DOMContentLoaded', function() {
    categoryModalInstance = new bootstrap.Modal(document.getElementById('categoryModal'));
});

function openAddCategoryModal() {
    document.getElementById('modal_k_id').value = '';
    document.getElementById('modal_k_name').value = '';
    document.getElementById('modal_k_act').checked = true;

    document.getElementById('categoryModalLabel').innerText = 'Tambah Kategori Baru';
    document.getElementById('category-modal-icon').className = 'bi bi-tag';
    document.getElementById('category-modal-submit-text').innerText = 'Simpan Kategori';

    categoryModalInstance.show();
    setTimeout(() => document.getElementById('modal_k_name').focus(), 300);
}

function openEditCategoryModal(item) {
    document.getElementById('modal_k_id').value = item.id;
    document.getElementById('modal_k_name').value = item.name;
    document.getElementById('modal_k_act').checked = !!item.is_active;

    document.getElementById('categoryModalLabel').innerText = 'Edit Kategori: ' + item.name;
    document.getElementById('category-modal-icon').className = 'bi bi-pencil-square';
    document.getElementById('category-modal-submit-text').innerText = 'Perbarui Kategori';

    categoryModalInstance.show();
    setTimeout(() => document.getElementById('modal_k_name').focus(), 300);
}
</script>
@endpush
@endsection
