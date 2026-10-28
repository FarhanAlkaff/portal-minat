@extends('layouts.admin')
@section('title', 'Kampus')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h1 class="section-title mb-0">Manajemen Kampus</h1>
        <small class="text-muted">Kelola data kampus cabang, lokasi pendaftaran, dan status penerimaan</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-buildings text-primary"></i> Total: <b>{{ $items->count() }}</b>
        </span>
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-check-circle-fill text-success"></i> Aktif: <b>{{ $items->where('is_active', true)->count() }}</b>
        </span>
        <button type="button" class="btn btn-azhar px-3 py-2 rounded-3 shadow-xs d-inline-flex align-items-center gap-2" onclick="openAddCampusModal()">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Tambah Kampus</span>
        </button>
    </div>
</div>

<!-- Tabel Kampus Full Width -->
<div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
        <span class="fw-bold text-dark"><i class="bi bi-buildings me-2 text-primary"></i>Daftar Lokasi Kampus Al-Azhar Cairo</span>
        <small class="text-muted">{{ $items->count() }} kampus terdaftar</small>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:5%;" class="text-center">No</th>
                    <th style="width:35%;">Nama Kampus</th>
                    <th style="width:30%;">Alamat Lokasi</th>
                    <th class="text-center" style="width:12%;">Pendaftar</th>
                    <th class="text-center" style="width:10%;">Status</th>
                    <th class="text-end" style="width:8%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $idx => $i)
                <tr>
                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:10px;background:{{ $i->is_active ? '#EDE9FE' : '#F1F5F9' }};color:{{ $i->is_active ? '#6B2D8C' : '#94A3B8' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-buildings"></i>
                            </div>
                            <div>
                                <b class="text-dark">{{ $i->name }}</b>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-muted small d-flex align-items-center gap-1">
                            <i class="bi bi-geo-alt text-danger" style="font-size:0.85rem;"></i>
                            <span>{{ $i->address }}</span>
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
                            <button class="btn btn-sm btn-outline-primary rounded-2 px-2" title="Edit Kampus" onclick='openEditCampusModal(@json($i))'>
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            @if($i->registrations_count == 0)
                                <form method="POST" action="{{ route('admin.campuses.destroy', $i) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kampus \'{{ addslashes($i->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Kampus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-light border text-muted rounded-2 px-2" title="Kampus ini tidak dapat dihapus karena masih memiliki data pendaftaran ({{ $i->registrations_count }} siswa)." onclick="alert('Kampus \'{{ addslashes($i->name) }}\' tidak dapat dihapus karena masih digunakan oleh {{ $i->registrations_count }} data pendaftaran. Anda dapat menonaktifkan statusnya.')">
                                    <i class="bi bi-trash text-muted"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-buildings display-5 d-block text-muted opacity-50 mb-2"></i>
                        Belum ada data kampus. Klik tombol <b>Tambah Kampus</b> untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 bg-light border-top small text-muted d-flex align-items-center gap-2">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Kampus yang memiliki data pendaftaran dilindungi dari penghapusan demi keamanan data. Anda dapat mengubah statusnya menjadi <b>Nonaktif</b> jika lokasi tersebut sudah ditutup.</span>
    </div>
</div>

<!-- Modal Pop-Up Tambah / Edit Kampus -->
<div class="modal fade" id="campusModal" tabindex="-1" aria-labelledby="campusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom p-3 px-4" style="background:#FAF7FC;">
                <div class="d-flex align-items-center gap-2">
                    <div id="modal-icon-box" style="width:34px;height:34px;border-radius:10px;background:#EDE9FE;color:#6B2D8C;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-building-add" id="modal-icon"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="campusModalLabel">Tambah Kampus</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.campuses.save') }}" id="campusForm">
                @csrf
                <input type="hidden" name="id" id="modal_c_id">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kampus <span class="text-danger">*</span></label>
                        <input name="name" id="modal_c_name" class="form-control" placeholder="Contoh: Al-Azhar Cairo Palembang KM 3" required>
                        <small class="text-muted" style="font-size:0.75rem;">Nama resmi lokasi kampus yang tampil di formulir pendaftaran.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="address" id="modal_c_addr" rows="3" class="form-control" placeholder="Jl. Jend. Sudirman KM. 3, Palembang" required></textarea>
                    </div>

                    <div class="form-check form-switch p-3 rounded-3" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="modal_c_act" value="1" checked role="switch">
                        <label class="form-check-label fw-semibold" for="modal_c_act" style="cursor:pointer;">
                            Status Aktif
                        </label>
                        <div class="text-muted" style="font-size:0.72rem;">Jika dinonaktifkan, kampus ini tidak dapat dipilih saat calon siswa mendaftar baru.</div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 px-4 bg-light">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-azhar px-4 rounded-3 shadow-xs" id="modal-submit-btn">
                        <i class="bi bi-save me-1"></i> <span id="modal-submit-text">Simpan Kampus</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let campusModalInstance = null;
document.addEventListener('DOMContentLoaded', function() {
    campusModalInstance = new bootstrap.Modal(document.getElementById('campusModal'));
});

function openAddCampusModal() {
    document.getElementById('modal_c_id').value = '';
    document.getElementById('modal_c_name').value = '';
    document.getElementById('modal_c_addr').value = '';
    document.getElementById('modal_c_act').checked = true;

    document.getElementById('campusModalLabel').innerText = 'Tambah Kampus Baru';
    document.getElementById('modal-icon').className = 'bi bi-building-add';
    document.getElementById('modal-submit-text').innerText = 'Simpan Kampus';

    campusModalInstance.show();
    setTimeout(() => document.getElementById('modal_c_name').focus(), 300);
}

function openEditCampusModal(item) {
    document.getElementById('modal_c_id').value = item.id;
    document.getElementById('modal_c_name').value = item.name;
    document.getElementById('modal_c_addr').value = item.address;
    document.getElementById('modal_c_act').checked = !!item.is_active;

    document.getElementById('campusModalLabel').innerText = 'Edit Kampus: ' + item.name;
    document.getElementById('modal-icon').className = 'bi bi-pencil-square';
    document.getElementById('modal-submit-text').innerText = 'Perbarui Kampus';

    campusModalInstance.show();
    setTimeout(() => document.getElementById('modal_c_name').focus(), 300);
}
</script>
@endpush
@endsection
