<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'start_year', 'end_year', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function registrations()
    {
        return $this->hasMany(StudentRegistration::class);
    }

    public static function active()
    {
        return static::where('is_active', true)->orderByDesc('start_year')->first();
    }
}
