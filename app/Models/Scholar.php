<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholar extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'contact_number',
            'address',
            'birth_date',
            'gender',
            'school_id',
            'type',
            'course',
            'year_level',
            'status',
            'additional_details'
        ];

        protected $casts = [
            'birth_date' => 'date',
            'contact_number' => 'string',
            'email' => 'string',
            'address' => 'string',
            'gender' => 'string',
            'school_id' => 'integer',
            'type' => 'string',
            'course' => 'string',
            'year_level' => 'integer',
            'status' => 'string',
            'additional_details' => 'json'
        ];

        public function school()
        {
            return $this->belongsTo(School::class, 'school_id', 'id');
        }

        public function requirements()
        {
            return $this->hasMany(Requirement::class);
        }

        public function scholarships()
        {
            return $this->belongsToMany(Scholarship::class, 'requirements');
        }

        public function getFullNameAttribute()
        {
            return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
        }
}
