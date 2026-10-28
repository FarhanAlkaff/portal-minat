<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRegistration extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'age_reference_date' => 'date',
        'observation_date' => 'date',
        'registered_at' => 'datetime',
    ];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function grade() { return $this->belongsTo(Grade::class); }
    public function campus() { return $this->belongsTo(Campus::class); }
    public function category() { return $this->belongsTo(StudentCategory::class, 'student_category_id'); }
    public function notes() { return $this->hasMany(RegistrationNote::class, 'registration_id')->latest(); }
    public function histories() { return $this->hasMany(RegistrationStatusHistory::class, 'registration_id')->latest('id'); }
}
