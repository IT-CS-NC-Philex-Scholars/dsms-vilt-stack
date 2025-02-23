<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use SoftDeletes;

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

        public function scholarship()
        {
            return $this->belongsTo(Scholarship::class);
        }
}
