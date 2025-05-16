<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Scholar extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
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
        'additional_details',
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
        'additional_details' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    /**
     * Get the scholarships *awarded* to this scholar.
     */
    public function scholarships(): BelongsToMany
    {
        return $this->belongsToMany(Scholarship::class, 'scholar_scholarship')
            ->withPivot('status', 'start_date', 'end_date', 'remarks')
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->middle_name.' '.$this->last_name;
    }

    public function getCompletedRequirementsAttribute()
    {
        return $this->requirements()->whereHas('scholarships', function ($query): void {
            $query->where('status', 'active');
        })
            ->where('status', 'approved')->count();
    }

    public function getTotalRequirementsAttribute()
    {
        return $this->requirements()->count();
    }
}
