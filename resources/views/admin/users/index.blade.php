@extends('layouts.admin')
@section('title', 'Manajemen Akun Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="section-title mb-1">Manajemen Akun Admin</h1>
        <p class="text-muted small mb-0">Kelola akun administrator portal pendaftaran serta pembagian hak akses jenjang/grade.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-azhar px-3 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill"></i>
        <span>Tambah Akun Baru</span>
    </a>
</div>

<div class="card border-0 rounded-4 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-vcenter mb-0 align-middle">
                <thead style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;">
                    <tr>
                        <th class="ps-4 py-3" style="width:60px;">#</th>
                        <th class="py-3">Nama & Email</th>
                        <th class="py-3">Peran (Role)</th>
                        <th class="py-3">Akses Jenjang / Grade</th>
                        <th class="py-3">Terdaftar</th>
                        <th class="pe-4 py-3 text-end" style="width:130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:38px;height:38px;border-radius:10px;background:{{ $u->isSuperAdmin() ? '#6B2D8C' : '#3B82F6' }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem;flex-shrink:0;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                                        <div class="text-muted small">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($u->isSuperAdmin())
                                    <span class="badge rounded-pill px-3 py-2" style="background:#EDE9FE;color:#6B2D8C;font-size:0.75rem;font-weight:600;">
                                        <i class="bi bi-shield-lock-fill me-1"></i>Super Admin
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2" style="background:#EFF6FF;color:#1D4ED8;font-size:0.75rem;font-weight:600;">
                                        <i class="bi bi-person-badge-fill me-1"></i>Admin Grade
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($u->isSuperAdmin())
                                    <span class="text-muted small fst-italic">Semua Jenjang (Akses Penuh)</span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($u->grades as $g)
                                            <x-grade-badge :code="$g->code" size="sm" />
                                        @empty
                                            <span class="text-danger small fst-italic">Belum ada grade</span>
                                        @endforelse
                                    </div>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1" title="Edit Akun">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }} ({{ $u->email }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1" title="Hapus Akun">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada akun admin terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
