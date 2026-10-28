<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'grade_user')->orderBy('sort_order');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGradeAdmin(): bool
    {
        return $this->role === 'grade_admin';
    }

    /**
     * Get list of grade IDs accessible by this user.
     *
     * @return array<int>
     */
    public function accessibleGradeIds(): array
    {
        if ($this->isSuperAdmin()) {
            return Grade::pluck('id')->toArray();
        }

        return $this->grades()->pluck('grades.id')->toArray();
    }

    /**
     * Get Grade models accessible by this user.
     */
    public function accessibleGrades(bool $onlyActive = true)
    {
        if ($this->isSuperAdmin()) {
            $query = Grade::orderBy('sort_order');
            if ($onlyActive) {
                $query->where('is_active', true);
            }
            return $query->get();
        }

        $query = $this->grades()->orderBy('sort_order');
        if ($onlyActive) {
            $query->where('is_active', true);
        }
        return $query->get();
    }

    public function assignedGradesLabel(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Semua Grade (Super Admin)';
        }

        $codes = $this->grades->pluck('code')->toArray();
        return !empty($codes) ? implode(', ', $codes) : 'Belum Ditentukan';
    }
}
