<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

 // Optional: if using factories

final class PreQualification extends Model
{
    // use HasFactory; // Optional

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
        'educational_level', // Added
        'school_id',         // Added
        'strand',            // Added (nullable)
        'shs_grade_level',   // Added (nullable)
        'course',            // Added (nullable)
        'year_level',        // Added (nullable)
        'enrollment_intent',
        'is_eligible',
    ];

    protected $casts = [
        'current_grade' => 'decimal:2',
        'is_eligible' => 'boolean',
        'birth_date' => 'date',
        'school_id' => 'integer',        // Added
        'shs_grade_level' => 'integer',  // Added
        'year_level' => 'integer',       // Added
    ];

    // The eligibility check logic is now primarily in the Controller for the session flow.
    // This could be used if saving the model instance first.
    public function checkEligibility(): bool
    {
        return $this->current_grade >= 80;
    }

    // Optional Relationship: If you want to link to the School model
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
