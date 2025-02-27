<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'application_id',
        'type',
        'file_path',
        'status',
        'verification_date',
        'notes',
        'original_name',
        'mime_type',
        'file_size',
        'verified',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verification_date' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
    public function getTypeDisplayAttribute()
        {
            $types = [
                'grade_slip' => 'Grade Slip',
                'id_card' => 'ID Card',
                'enrollment_certificate' => 'Enrollment Certificate',
                'income_certificate' => 'Income Certificate',
                'recommendation_letter' => 'Recommendation Letter'
            ];

            return $types[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
        }
}
