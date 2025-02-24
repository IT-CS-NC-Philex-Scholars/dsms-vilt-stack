<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    use SoftDeletes;


        protected $fillable = [
            'name',
            'description',
            'requirements',
            'application_deadline',
            'status'
        ];

        protected $casts = [
            'requirements' => 'array',
            'application_deadline' => 'date'
        ];

        public function scholars()
        {
            return $this->belongsToMany(Scholar::class, 'scholar_scholarship')
                ->withPivot('status', 'start_date', 'end_date', 'remarks')
                ->withTimestamps();
        }

        public function requirements()
        {
            return $this->hasMany(Requirement::class);
        }
}
