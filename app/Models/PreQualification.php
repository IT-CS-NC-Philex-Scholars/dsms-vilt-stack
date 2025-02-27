<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreQualification extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_number',
        'address',
        'birth_date',
        'gender',
        'current_grade',
        'enrollment_intent',
        'is_eligible',
    ];

    protected $casts = [
        'current_grade' => 'decimal:2',
        'is_eligible' => 'boolean',
        'birth_date' => 'date',
    ];

    public function checkEligibility()
    {
        return $this->current_grade >= 80;
    }
}
