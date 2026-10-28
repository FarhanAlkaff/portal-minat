<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'activity', 'subject_type', 'subject_id', 'data', 'ip_address', 'created_at'];
    protected $casts = ['data' => 'array', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}
