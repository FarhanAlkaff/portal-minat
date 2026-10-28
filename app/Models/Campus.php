<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = ['name', 'address', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function registrations()
    {
        return $this->hasMany(StudentRegistration::class);
    }
}
