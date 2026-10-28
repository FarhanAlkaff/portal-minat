<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['code', 'name', 'description', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function ageRule()
    {
        return $this->hasOne(GradeAgeRule::class)->where('is_active', true);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'grade_user');
    }

    public function registrations()
    {
        return $this->hasMany(StudentRegistration::class);
    }

    public function getColorAttribute(): array
    {
        return self::colorFor($this->code);
    }

    public static function colorFor(?string $code): array
    {
        return match (strtoupper(trim((string)$code))) {
            'PG'   => ['bg' => '#F3E8FF', 'text' => '#7E22CE', 'border' => '#D8B4FE', 'bar' => '#9333EA'], // Ungu
            'TK-A' => ['bg' => '#E0E7FF', 'text' => '#4338CA', 'border' => '#C7D2FE', 'bar' => '#6366F1'], // Indigo
            'TK-B' => ['bg' => '#E0F2FE', 'text' => '#0284C7', 'border' => '#BAE6FD', 'bar' => '#0284C7'], // Biru Muda / Sky
            'SD'   => ['bg' => '#FEE2E2', 'text' => '#B91C1C', 'border' => '#FECACA', 'bar' => '#DC2626'], // Merah Khas SD
            'SMP'  => ['bg' => '#DBEAFE', 'text' => '#1D4ED8', 'border' => '#BFDBFE', 'bar' => '#2563EB'], // Biru Khas SMP
            'SMA'  => ['bg' => '#F1F5F9', 'text' => '#334155', 'border' => '#CBD5E1', 'bar' => '#64748B'], // Abu-Abu Khas SMA
            default => ['bg' => '#F3F4F6', 'text' => '#374151', 'border' => '#E5E7EB', 'bar' => '#6B7280'],
        };
    }
}
