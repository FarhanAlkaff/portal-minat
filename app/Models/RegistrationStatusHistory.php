<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationStatusHistory extends Model
{
    public $timestamps = false;
    protected $fillable = ['registration_id', 'old_status', 'new_status', 'changed_by', 'note', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function registration() { return $this->belongsTo(StudentRegistration::class, 'registration_id'); }
    public function changer() { return $this->belongsTo(User::class, 'changed_by'); }
}
