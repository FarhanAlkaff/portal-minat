<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationSequence extends Model
{
    protected $fillable = ['academic_year_id', 'grade_id', 'campus_id', 'last_sequence'];
}
