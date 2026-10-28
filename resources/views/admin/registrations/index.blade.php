@extends('layouts.admin')
@section('title', 'Data Pendaftaran')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div><h1 class="section-title mb-0">Data Pendaftaran</h1><small class="text-muted">{{ $registrations->total() }} data</small></div>
    <div class="d-flex gap-2"><a class="btn btn-outline-success" href="{{ route('admin.registrations.export', request()->query()) }}" data-testid="btn-export"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV</a><a class="btn btn-azhar" href="{{ route('admin.registrations.create') }}" data-testid="btn-create"><i class="bi bi-plus-circle me-1"></i>Input Baru</a></div>
</div>

<form class="p-3 bg-white rounded-4 shadow-sm mb-3">
    <div class="row g-2 align-items-center">
        @php $hasMultiGrades = count($filters['grades']) > 1; @endphp
        <div class="{{ $hasMultiGrades ? 'col-md-3' : 'col-md-5' }}">
            <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nomor / nama / NISN / HP..." data-testid="filter-search">
        </div>
        <div class="col-md-2">
            <select name="academic_year_id" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($filters['years'] as $y)
                    <option value="{{ $y->id }}" @selected(request('academic_year_id')==$y->id)>{{ $y->name }}</option>
                @endforeach
            </select>
        </div>
        @if($hasMultiGrades)
        <div class="col-md-2">
            <select name="grade_id" class="form-select">
                <option value="">Semua Grade</option>
                @foreach($filters['grades'] as $g)
                    <option value="{{ $g->id }}" @selected(request('grade_id')==$g->id)>{{ $g->code }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-2">
            <select name="campus_id" class="form-select">
                <option value="">Semua Kampus</option>
                @foreach($filters['campuses'] as $c)
                    <option value="{{ $c->id }}" @selected(request('campus_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                @foreach($filters['statuses'] as $s)
                    <option value="{{ $s }}" @selected(request('status')==$s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button class="btn btn-azhar w-100" title="Terapkan Filter"><i class="bi bi-funnel-fill"></i></button>
            @if(request()->hasAny(['search', 'academic_year_id', 'grade_id', 'campus_id', 'status']))
                <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
            @endif
        </div>
    </div>
</form>

<div class="p-2 bg-white rounded-4 shadow-sm">
    <div class="table-responsive"><table class="table table-vcenter">
        <thead><tr><th>No.</th><th>Pendaftaran</th><th>Nama</th><th>Grade</th><th>Tahun</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        @forelse($registrations as $i => $r)
            <tr>
                <td>{{ $registrations->firstItem() + $i }}</td>
                <td><b>{{ $r->registration_number }}</b></td>
                <td>{{ $r->full_name }}<br><small class="text-muted">{{ $r->birth_date?->format('d M Y') }}</small></td>
                <td><x-grade-badge :grade="$r->grade" /></td>
                <td>{{ $r->academicYear->name }}</td>
                <td><span class="status-pill status-{{ \Illuminate\Support\Str::slug($r->status) }}">{{ $r->status }}</span></td>
                <td class="text-end">
                    <a href="{{ route('admin.registrations.show', $r) }}" class="btn btn-sm btn-outline-primary" data-testid="row-detail-{{ $r->id }}"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('registration.pdf', $r->registration_number) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-pdf"></i></a>
                    <form method="POST" action="{{ route('admin.registrations.destroy', $r) }}" class="d-inline" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data.</td></tr>
        @endforelse
        </tbody></table></div>
    <div class="p-3">{{ $registrations->links() }}</div>
</div>
@endsection
