<?php

namespace App\Models;

use App\Models\Requirement;
use App\Models\Scholar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    use HasFactory, SoftDeletes;


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
