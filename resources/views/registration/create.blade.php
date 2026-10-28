@extends('layouts.public')
@section('title', 'Formulir Pendaftaran')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <span class="text-uppercase small" style="letter-spacing:.15em;color:var(--azhar-gold);">Portal Minat</span>
        <h1 class="section-title mb-0">Formulir Penerimaan Murid Baru</h1>
        <p class="text-muted">Al-Azhar Cairo Palembang · Isi data dengan lengkap & benar</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('registration.store') }}" id="regForm" class="row g-4">
        @csrf

        <div class="col-12">
            <div class="p-4 bg-white rounded-4 shadow-sm">
                <h5 class="section-title mb-3"><i class="bi bi-1-circle me-2" style="color:var(--azhar-gold);"></i>Pilih Tahun Ajaran & Jenjang</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label required">Tahun Ajaran</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select" required data-testid="input-academic-year">
                            <option value="">— Pilih —</option>
                            @foreach($years as $y)
                                <option value="{{ $y->id }}" @selected(old('academic_year_id', $preselectedYear) == $y->id)>{{ $y->name }} @if($y->is_active) (Aktif) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">Jenjang / Grade</label>
                        <select name="grade_id" id="grade_id" class="form-select" required data-testid="input-grade">
                            <option value="">— Pilih —</option>
                            @foreach($grades as $g)
                                <option value="{{ $g->id }}" @selected(old('grade_id', $preselectedGrade) == $g->id)>{{ $g->code }} — {{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Persyaratan Usia</label>
                        <div id="ageRuleInfo" class="p-2 rounded" style="background:var(--azhar-cream);min-height:44px;font-size:.92rem;">Pilih tahun & grade dulu.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                <h5 class="section-title mb-3"><i class="bi bi-2-circle me-2" style="color:var(--azhar-gold);"></i>Data Calon Murid</h5>
                <div class="mb-3"><label class="form-label">No. Urut</label><input class="form-control" value="Otomatis (dibuat sistem setelah submit)" readonly data-testid="input-no-urut"></div>
                <div class="mb-3"><label class="form-label required">Nama Lengkap</label><input name="full_name" id="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="Sesuai Akte Kelahiran / Ijazah" required data-testid="input-full-name"><small class="text-muted">Sesuai Akte Kelahiran / ijazah sebelumnya.</small></div>
                <div class="mb-3"><label class="form-label required">Nama Panggilan</label><input name="nickname" class="form-control" value="{{ old('nickname') }}" required data-testid="input-nickname"></div>
                <div class="mb-3"><label class="form-label required d-block">Jenis Kelamin</label>
                    <div class="form-check form-check-inline"><input type="radio" class="form-check-input" name="gender" value="L" id="gL" @checked(old('gender')==='L') required data-testid="radio-gender-L"><label class="form-check-label" for="gL">Laki-Laki</label></div>
                    <div class="form-check form-check-inline"><input type="radio" class="form-check-input" name="gender" value="P" id="gP" @checked(old('gender')==='P') data-testid="radio-gender-P"><label class="form-check-label" for="gP">Perempuan</label></div>
                </div>
                <div class="mb-3"><label class="form-label">NISN</label><input name="nisn" class="form-control" value="{{ old('nisn') }}" placeholder="Nomor Induk Siswa Nasional" data-testid="input-nisn"></div>
                <div class="mb-3"><label class="form-label">Kelas (untuk pindahan)</label><input name="class" class="form-control" value="{{ old('class') }}" data-testid="input-class"></div>
                <div class="mb-3"><label class="form-label required">Tempat Lahir</label><input name="birth_place" class="form-control" value="{{ old('birth_place') }}" required data-testid="input-birth-place"></div>
                <div class="mb-3"><label class="form-label required">Tanggal Lahir</label><input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date') }}" required data-testid="input-birth-date">
                    <div id="ageResult" class="mt-2"></div>
                </div>
                <div class="mb-3"><label class="form-label required">Status Keluarga</label>
                    <select name="family_status" class="form-select" required data-testid="input-family-status">
                        @foreach(['Anak Kandung','Anak Tiri','Anak Angkat','Lainnya'] as $s)
                            <option value="{{ $s }}" @selected(old('family_status')===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label required">Anak Ke-</label><input type="number" min="1" name="child_order" class="form-control" value="{{ old('child_order') }}" required data-testid="input-child-order"></div>
                <div class="mb-3"><label class="form-label required">Alamat Lengkap</label><textarea name="address" rows="3" class="form-control" required data-testid="input-address">{{ old('address') }}</textarea></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                <h5 class="section-title mb-3"><i class="bi bi-3-circle me-2" style="color:var(--azhar-gold);"></i>Kontak & Data Orang Tua</h5>
                <div class="mb-3"><label class="form-label required">No. Telp / HP</label><input name="phone" class="form-control" value="{{ old('phone') }}" required data-testid="input-phone"></div>
                <div class="mb-3"><label class="form-label">Asal Sekolah</label><input name="previous_school" class="form-control" value="{{ old('previous_school') }}" data-testid="input-previous-school"></div>
                <hr>
                <h6 class="mb-3" style="color:var(--azhar-green-dark);font-weight:700;">Nama Orang Tua / Wali</h6>
                <div class="mb-3"><label class="form-label required">Ayah</label><input name="father_name" class="form-control" value="{{ old('father_name') }}" required data-testid="input-father-name"></div>
                <div class="mb-3"><label class="form-label required">Ibu</label><input name="mother_name" class="form-control" value="{{ old('mother_name') }}" required data-testid="input-mother-name"></div>
                <div class="mb-3"><label class="form-label required">Kakek Dari Ayah</label><input name="grandfather_name" class="form-control" value="{{ old('grandfather_name') }}" required data-testid="input-grandfather-name"></div>
                <h6 class="mb-3 mt-3" style="color:var(--azhar-green-dark);font-weight:700;">Pekerjaan Orang Tua</h6>
                <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Pekerjaan Ayah</label><input name="father_job" class="form-control" value="{{ old('father_job') }}" data-testid="input-father-job"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Pekerjaan Ibu</label><input name="mother_job" class="form-control" value="{{ old('mother_job') }}" data-testid="input-mother-job"></div></div>
                <h6 class="mb-3" style="color:var(--azhar-green-dark);font-weight:700;">Nomor HP Orang Tua</h6>
                <div class="row"><div class="col-md-6 mb-3"><label class="form-label">HP Ayah</label><input name="father_phone" class="form-control" value="{{ old('father_phone') }}" data-testid="input-father-phone"></div>
                <div class="col-md-6 mb-3"><label class="form-label">HP Ibu</label><input name="mother_phone" class="form-control" value="{{ old('mother_phone') }}" data-testid="input-mother-phone"></div></div>
            </div>
        </div>

        <div class="col-12">
            <div class="p-4 bg-white rounded-4 shadow-sm">
                <h5 class="section-title mb-3"><i class="bi bi-4-circle me-2" style="color:var(--azhar-gold);"></i>Data Pendaftaran</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label required">Kampus</label>
                        <select name="campus_id" class="form-select" required data-testid="input-campus">
                            @foreach($campuses as $c)<option value="{{ $c->id }}" @selected(old('campus_id')==$c->id)>{{ $c->name }} — {{ $c->address }}</option>@endforeach
                        </select></div>
                    <div class="col-md-6"><label class="form-label required">Kategori Siswa</label>
                        <select name="student_category_id" class="form-select" required data-testid="input-category">
                            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('student_category_id')==$c->id)>{{ $c->name }}</option>@endforeach
                        </select></div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="p-4 bg-white rounded-4 shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="agreement" id="agreement" value="1" required data-testid="input-agreement">
                    <label class="form-check-label" for="agreement">Saya menyatakan bahwa data yang saya masukkan adalah <strong>benar</strong> dan dapat dipertanggungjawabkan.</label>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('landing') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="button" class="btn btn-azhar px-4" id="btnReview" data-testid="btn-review">Konfirmasi & Kirim <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Konfirmasi Data</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="text-muted">Pastikan seluruh data yang Anda masukkan sudah benar.</p>
                <div id="reviewContent"></div>
                <div id="duplicateWarning" class="alert alert-warning mt-3 d-none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Ubah Data</button>
                <button class="btn btn-azhar" id="btnSubmit" data-testid="btn-submit-confirmed">Kirim Pendaftaran</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $gradesInfo = $grades->map(function($g){ return ['id'=>$g->id,'code'=>$g->code,'name'=>$g->name,'rule'=>$g->ageRule]; })->values();
    $yearsInfo = $years->map(function($y){ return ['id'=>$y->id,'name'=>$y->name,'start_year'=>$y->start_year]; })->values();
@endphp
<script>
const $ = (s) => document.querySelector(s);
const gradesInfo = {!! json_encode($gradesInfo) !!};
const yearsInfo = {!! json_encode($yearsInfo) !!};

function fmtRule(r){ if(!r) return 'Aturan usia belum ditetapkan.'; let min=[],max=[]; if(r.minimum_age_years||r.minimum_age_months||r.minimum_age_days){ if(r.minimum_age_years)min.push(r.minimum_age_years+' tahun'); if(r.minimum_age_months)min.push(r.minimum_age_months+' bulan'); if(r.minimum_age_days)min.push(r.minimum_age_days+' hari');} if(r.maximum_age_years||r.maximum_age_months||r.maximum_age_days){ if(r.maximum_age_years)max.push(r.maximum_age_years+' tahun'); if(r.maximum_age_months)max.push(r.maximum_age_months+' bulan'); if(r.maximum_age_days)max.push(r.maximum_age_days+' hari');} let s=''; if(min.length)s+='<div><b>Min:</b> '+min.join(' ')+'</div>'; if(max.length)s+='<div><b>Max:</b> '+max.join(' ')+'</div>'; s+='<small class="text-muted">Acuan: '+String(r.reference_day).padStart(2,'0')+'/'+String(r.reference_month).padStart(2,'0')+'</small>'; return s||'Aturan usia belum ditetapkan.'; }

function updateRuleInfo(){
    const gid = $('#grade_id').value, yid=$('#academic_year_id').value;
    const g = gradesInfo.find(x=>x.id==gid); const y=yearsInfo.find(x=>x.id==yid);
    if(!g||!y){ $('#ageRuleInfo').textContent='Pilih tahun & grade dulu.'; return; }
    $('#ageRuleInfo').innerHTML = `<b>${g.code}</b> · Acuan 1 Juli ${y.start_year}<br>${fmtRule(g.rule)}`;
    triggerAge();
}
async function triggerAge(){
    const gid=$('#grade_id').value, yid=$('#academic_year_id').value, bd=$('#birth_date').value;
    if(!gid||!yid||!bd){ $('#ageResult').innerHTML=''; return; }
    const res = await fetch(@json(route('api.validate-age')), {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window._token,'Accept':'application/json'},body:JSON.stringify({grade_id:gid,academic_year_id:yid,birth_date:bd})});
    const d = await res.json();
    const cls = d.is_eligible?'alert-success':'alert-danger';
    const ico = d.is_eligible?'✓':'✗';
    $('#ageResult').innerHTML = `<div class="alert ${cls} py-2 mb-0" data-testid="age-result">${ico} Usia pada ${d.reference_date}: <b>${d.age_years} tahun ${d.age_months} bulan ${d.age_days} hari</b><br>${d.message}</div>`;
}
['#grade_id','#academic_year_id'].forEach(s=>$(s).addEventListener('change', updateRuleInfo));
$('#birth_date').addEventListener('change', triggerAge);
updateRuleInfo();

$('#btnReview').addEventListener('click', async ()=>{
    const f = $('#regForm');
    if(!f.reportValidity()) return;
    const fd = new FormData(f);
    const rows = [
        ['Nama Anak', fd.get('full_name')],
        ['Tanggal Lahir', fd.get('birth_date')],
        ['Grade', gradesInfo.find(g=>g.id==fd.get('grade_id'))?.name],
        ['Tahun Ajaran', yearsInfo.find(y=>y.id==fd.get('academic_year_id'))?.name],
        ['Kampus', document.querySelector('select[name=campus_id] option:checked')?.textContent],
        ['Kategori', document.querySelector('select[name=student_category_id] option:checked')?.textContent],
        ['Nama Ayah', fd.get('father_name')],
        ['Nama Ibu', fd.get('mother_name')],
        ['HP', fd.get('phone')],
    ];
    $('#reviewContent').innerHTML = '<div class="table-responsive"><table class="table table-sm"><tbody>'+rows.map(r=>`<tr><th style="width:30%">${r[0]}</th><td>${r[1]||'-'}</td></tr>`).join('')+'</tbody></table></div>';

    // Check duplicate first
    const dup = await fetch(@json(route('api.check-duplicate')), {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window._token,'Accept':'application/json'},body:JSON.stringify({full_name:fd.get('full_name'),birth_date:fd.get('birth_date'),grade_id:fd.get('grade_id'),academic_year_id:fd.get('academic_year_id')})}).then(r=>r.json());
    const dw = $('#duplicateWarning');
    if(dup.exists){ dw.classList.remove('d-none'); dw.innerHTML = `<strong>Pendaftaran Sudah Ditemukan!</strong><br>No: <b>${dup.registration_number}</b> · Status: ${dup.status}<br><a class="btn btn-sm btn-azhar mt-2" href="${dup.view_url}">Lihat Pendaftaran</a>`; $('#btnSubmit').disabled=true; }
    else{ dw.classList.add('d-none'); $('#btnSubmit').disabled=false; }
    new bootstrap.Modal($('#confirmModal')).show();
});
$('#btnSubmit').addEventListener('click', ()=> $('#regForm').submit());
</script>
@endpush
