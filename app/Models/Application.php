<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'rejection_reason',
        'address_verified',
        'grade_verified',
        'enrollment_verified',
    ];

    protected $casts = [
        'address_verified' => 'boolean',
        'grade_verified' => 'boolean',
        'enrollment_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function isComplete()
    {
        return $this->documents()->count() >= 3;
    }
}
