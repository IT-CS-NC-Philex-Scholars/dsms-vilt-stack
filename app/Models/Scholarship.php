<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Scholarship extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'status',
        'application_period_start',
        'application_period_end',
        'slots_available',
        'documentary_requirements_description',
        'application_process_description',
        'eligibility_criteria_description',
        'scholarship_program_type',
        'financial_assistance_type',
        'target_student_group',
        'gwa_requirement_description',
        'income_bracket_requirement_description',
        'benefits_description',
    ];

    protected $casts = [
        'application_period_start' => 'date',
        'application_period_end' => 'date',
        'slots_available' => 'integer',
    ];

    public function scholars(): BelongsToMany
    {
        return $this->belongsToMany(Scholar::class, 'scholar_scholarship')
            ->withPivot('status', 'start_date', 'end_date', 'remarks')
            ->withTimestamps();
    }

    /**
     * Get the requirements defined for this scholarship.
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    /**
     * Get all applications submitted for this scholarship.
     */
    public function applications(): HasMany // ** Added relationship **
    {
        return $this->hasMany(Application::class);
    }
}
