<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Document extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'application_id',
        'type',
        'path',
        'file_path',
        'original_name',
        'disk',
        'mime_type',
        'file_size',
        'status',
        'verification_date',
        'notes',
        'verified',
        'semester_type',
        'semester_number',
        'academic_year',
    ];

    protected $casts = [
        'status' => 'string',
        'semester_number' => 'integer',
        'academic_year' => 'integer',
        'verified' => 'boolean',
        'verification_date' => 'datetime',
    ];
    
    /**
     * Handle the case where field may be named either file_path or path
     */
    public function getPathAttribute()
    {
        return $this->attributes['path'] ?? $this->attributes['file_path'] ?? null;
    }
    
    /**
     * Set both path fields to maintain compatibility
     */
    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value;
        
        // Also set file_path if that column exists
        if (array_key_exists('file_path', $this->attributes)) {
            $this->attributes['file_path'] = $value;
        }
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeDisplayAttribute(): string
    {
        $types = [
            'grade_slip' => 'Grade Slip',
            'id_card' => 'ID Card',
            'enrollment_certificate' => 'Proof of Enrollment',
            'income_certificate' => 'Income Certificate',
            'recommendation_letter' => 'Recommendation Letter',
            'residency' => 'Proof of Residency',
            'enrollment' => 'Enrollment Certificate',
            'grades' => 'Grade Report',
        ];

        return $types[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }
    
    public function getSemesterDisplayAttribute(): string
    {
        if (!$this->semester_type || !$this->semester_number) {
            return 'N/A';
        }
        
        $year = $this->academic_year ? $this->academic_year . '-' . ($this->academic_year + 1) : '';
        
        if ($this->semester_type === 'semestral') {
            $semester = $this->semester_number === 1 ? '1st Semester' : '2nd Semester';
        } else { // trimesteral
            $semester = match ($this->semester_number) {
                1 => '1st Trimester',
                2 => '2nd Trimester',
                3 => '3rd Trimester',
                default => $this->semester_number . 'th Trimester'
            };
        }
        
        return $year ? "$semester ($year)" : $semester;
    }
}
