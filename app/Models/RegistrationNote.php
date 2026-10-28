<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationNote extends Model
{
    protected $fillable = ['registration_id', 'admin_id', 'note', 'note_type'];

    public function registration() { return $this->belongsTo(StudentRegistration::class, 'registration_id'); }
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
}
