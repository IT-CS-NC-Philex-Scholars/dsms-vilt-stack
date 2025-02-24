<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class ScholarshipRequirement extends Model
{
    use SoftDeletes;

        protected $table = 'scholarship_requirement';

        protected $fillable = [
            'scholarship_id',
            'requirement_id',
            'is_mandatory',
            'submission_order',
            'description'
        ];

        public function scholarship(): BelongsTo
        {
            return $this->belongsTo(Scholarship::class);
        }

        public function requirement(): BelongsTo
        {
            return $this->belongsTo(Requirement::class);
        }
}
