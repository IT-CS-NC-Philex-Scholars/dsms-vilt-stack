<?php

namespace App\Models;

use App\Models\Requirement;
use App\Models\Scholar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'name',
            'description',
            'amount',
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
            return $this->belongsToMany(Scholar::class, 'requirements');
        }

        public function requirements()
        {
            return $this->hasMany(Requirement::class);
        }
}
