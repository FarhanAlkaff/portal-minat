@extends('layouts.admin')
@section('title', 'Tahun Ajaran')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h1 class="section-title mb-0">Tahun Ajaran</h1>
        <small class="text-muted">Kelola periode penerimaan peserta didik baru (PPDB) dan tentukan tahun aktif</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-calendar-range text-primary"></i> Total: <b>{{ $items->count() }}</b>
        </span>
        @php $activeYear = $items->firstWhere('is_active', true); @endphp
        <span class="badge rounded-pill px-3 py-2 bg-white text-dark border shadow-xs d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-check-circle-fill text-success"></i> Aktif: <b>{{ $activeYear ? $activeYear->name : 'Belum Dipilih' }}</b>
        </span>
        <button type="button" class="btn btn-azhar px-3 py-2 rounded-3 shadow-xs d-inline-flex align-items-center gap-2" onclick="openAddYearModal()">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Tambah Tahun Ajaran</span>
        </button>
    </div>
</div>

<!-- Tabel Tahun Ajaran Full Width -->
<div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
        <span class="fw-bold text-dark"><i class="bi bi-calendar-check me-2 text-primary"></i>Daftar Periode Akademik</span>
        <small class="text-muted">{{ $items->count() }} periode tercatat</small>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:5%;" class="text-center">No</th>
                    <th style="width:35%;">Nama Tahun Ajaran</th>
                    <th class="text-center" style="width:20%;">Periode Kalender</th>
                    <th class="text-center" style="width:15%;">Pendaftar</th>
                    <th class="text-center" style="width:13%;">Status</th>
                    <th class="text-end" style="width:12%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $idx => $it)
                <tr class="{{ $it->is_active ? 'bg-success-subtle bg-opacity-25' : '' }}">
                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:10px;background:{{ $it->is_active ? '#DCFCE7' : '#F1F5F9' }};color:{{ $it->is_active ? '#15803D' : '#64748B' }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem;">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <b class="text-dark fs-6">{{ $it->name }}</b>
                                @if($it->is_active)
                                    <span class="badge rounded-pill bg-success ms-1" style="font-size:0.68rem;">Sedang Berjalan</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="text-muted small fw-semibold">{{ $it->start_year }} &rarr; {{ $it->end_year }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-2 py-1 {{ $it->registrations_count > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border' }}">
                            <i class="bi bi-people me-1"></i>{{ $it->registrations_count }} siswa
                        </span>
                    </td>
                    <td class="text-center">
                        @if($it->is_active)
                            <span class="badge rounded-pill px-2 py-1" style="background:#DCFCE7;color:#15803D;border:1px solid #BBF7D0;">
                                <i class="bi bi-check-circle-fill me-1"></i>Aktif
                            </span>
                        @else
                            <form method="POST" action="{{ route('admin.years.save') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $it->id }}">
                                <input type="hidden" name="name" value="{{ $it->name }}">
                                <input type="hidden" name="start_year" value="{{ $it->start_year }}">
                                <input type="hidden" name="end_year" value="{{ $it->end_year }}">
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size:0.75rem;" title="Klik untuk mengaktifkan tahun ajaran ini">
                                    Jadikan Aktif
                                </button>
                            </form>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary rounded-2 px-2" title="Edit Tahun Ajaran" onclick='openEditYearModal(@json($it))'>
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            @if($it->registrations_count == 0 && !$it->is_active)
                                <form method="POST" action="{{ route('admin.years.destroy', $it) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran \'{{ addslashes($it->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Tahun Ajaran">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-light border text-muted rounded-2 px-2" title="{{ $it->is_active ? 'Tahun ajaran aktif tidak dapat dihapus.' : 'Masih memiliki data pendaftaran (' . $it->registrations_count . ' siswa).' }}" onclick="alert('{{ $it->is_active ? 'Tahun ajaran yang sedang aktif tidak dapat dihapus.' : 'Tahun ajaran \''.addslashes($it->name).'\' tidak dapat dihapus karena masih digunakan oleh '.$it->registrations_count.' data pendaftaran.' }}')">
                                    <i class="bi bi-trash text-muted"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        Belum ada data tahun ajaran. Klik tombol <b>Tambah Tahun Ajaran</b> untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 bg-light border-top small text-muted d-flex align-items-center gap-2">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Hanya satu tahun ajaran yang dapat berstatus <b>Aktif</b> pada satu waktu. Tahun aktif otomatis dijadikan acuan penerimaan pendaftaran baru.</span>
    </div>
</div>

<!-- Modal Pop-Up Tambah / Edit Tahun Ajaran -->
<div class="modal fade" id="yearModal" tabindex="-1" aria-labelledby="yearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom p-3 px-4" style="background:#FAF7FC;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;border-radius:10px;background:#EDE9FE;color:#6B2D8C;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-calendar-plus" id="year-modal-icon"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="yearModalLabel">Tambah Tahun Ajaran</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.years.save') }}" id="yearForm">
                @csrf
                <input type="hidden" name="id" id="modal_y_id">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tahun Ajaran <span class="text-danger">*</span></label>
                        <input name="name" id="modal_y_name" class="form-control" placeholder="Contoh: 2027/2028" required>
                        <small class="text-muted" style="font-size:0.75rem;">Format standar: YYYY/YYYY (misal: 2027/2028).</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tahun Awal <span class="text-danger">*</span></label>
                            <input type="number" name="start_year" id="modal_y_start" class="form-control" min="2020" max="2099" placeholder="2027" required oninput="syncModalYearName()">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tahun Akhir <span class="text-danger">*</span></label>
                            <input type="number" name="end_year" id="modal_y_end" class="form-control" min="2020" max="2100" placeholder="2028" required oninput="syncModalYearName()">
                        </div>
                    </div>

                    <div class="form-check form-switch p-3 rounded-3" style="background:#FAF7FC;border:1px solid #ECE3F5;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="modal_y_act" value="1" role="switch">
                        <label class="form-check-label fw-semibold" for="modal_y_act" style="cursor:pointer;">
                            Jadikan Tahun Ajaran Aktif
                        </label>
                        <div class="text-muted" style="font-size:0.72rem;">Mengaktifkan tahun ini akan otomatis menonaktifkan tahun ajaran sebelumnya.</div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 px-4 bg-light">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-azhar px-4 rounded-3 shadow-xs">
                        <i class="bi bi-save me-1"></i> <span id="year-modal-submit-text">Simpan Tahun Ajaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let yearModalInstance = null;
document.addEventListener('DOMContentLoaded', function() {
    yearModalInstance = new bootstrap.Modal(document.getElementById('yearModal'));
});

function syncModalYearName() {
    const s = document.getElementById('modal_y_start').value;
    const e = document.getElementById('modal_y_end').value;
    const n = document.getElementById('modal_y_name');
    if (s && !e) {
        document.getElementById('modal_y_end').value = parseInt(s) + 1;
    }
    if (s && document.getElementById('modal_y_end').value && !n.value) {
        n.value = s + '/' + document.getElementById('modal_y_end').value;
    }
}

function openAddYearModal() {
    document.getElementById('modal_y_id').value = '';
    document.getElementById('modal_y_name').value = '';
    document.getElementById('modal_y_start').value = '';
    document.getElementById('modal_y_end').value = '';
    document.getElementById('modal_y_act').checked = false;

    document.getElementById('yearModalLabel').innerText = 'Tambah Tahun Ajaran Baru';
    document.getElementById('year-modal-icon').className = 'bi bi-calendar-plus';
    document.getElementById('year-modal-submit-text').innerText = 'Simpan Tahun Ajaran';

    yearModalInstance.show();
    setTimeout(() => document.getElementById('modal_y_start').focus(), 300);
}

function openEditYearModal(item) {
    document.getElementById('modal_y_id').value = item.id;
    document.getElementById('modal_y_name').value = item.name;
    document.getElementById('modal_y_start').value = item.start_year;
    document.getElementById('modal_y_end').value = item.end_year;
    document.getElementById('modal_y_act').checked = !!item.is_active;

    document.getElementById('yearModalLabel').innerText = 'Edit Tahun Ajaran: ' + item.name;
    document.getElementById('year-modal-icon').className = 'bi bi-pencil-square';
    document.getElementById('year-modal-submit-text').innerText = 'Perbarui Tahun Ajaran';

    yearModalInstance.show();
    setTimeout(() => document.getElementById('modal_y_name').focus(), 300);
}
</script>
@endpush
@endsection
