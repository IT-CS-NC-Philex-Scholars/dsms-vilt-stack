<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requirement extends Model
{

    use HasFactory, SoftDeletes;


        protected $fillable = [
            'scholar_id',
            'scholarship_id',
            'document_type',
            'file_path',
            'status',
            'remarks',
            'submitted_at',
            'reviewed_at'
        ];

        protected $casts = [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime'
        ];

        public function scholar()
        {
            return $this->belongsTo(Scholar::class);
        }

        public function scholarship(): BelongsTo // Changed from belongsToMany, added return type hint
               {
                   // Assumes your foreign key column is named 'scholarship_id'
                   return $this->belongsTo(Scholarship::class);
               }
}
