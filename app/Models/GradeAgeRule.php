<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeAgeRule extends Model
{
    protected $fillable = [
        'grade_id',
        'minimum_age_years', 'minimum_age_months', 'minimum_age_days',
        'maximum_age_years', 'maximum_age_months', 'maximum_age_days',
        'reference_month', 'reference_day', 'is_active',
    ];
    protected $casts = ['is_active' => 'boolean'];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
