<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id', // ** Added **
        'status', // e.g., 'pending', 'submitted', 'reviewing', 'approved', 'rejected'
        'rejection_reason',
        'address_verified', // Verification flags specific to this application attempt
        'grade_verified',
        'enrollment_verified',
        'submitted_at', // ** Optional: Track submission date **
        // Add any other fields specific to an application instance
    ];

    protected $casts = [
        'address_verified' => 'boolean',
        'grade_verified' => 'boolean',
        'enrollment_verified' => 'boolean',
        'submitted_at' => 'datetime', // ** Optional **
        'scholarship_id' => 'integer', // ** Added **
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function isComplete(): bool
    {
        return $this->documents()->count() >= 3;
    }

    /**
     * Get the scholarship this application is for.
     */
    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function hasSufficientDocuments(): bool
    {
        // Example: Check against requirements defined on the Scholarship
        $requiredCount = $this->scholarship?->requirements()->count() ?? 0;
        // Or maybe a fixed number if requirements aren't dynamically linked yet
        // $requiredCount = 3; // Example fixed number

        return $this->documents()->count() >= $requiredCount && $requiredCount > 0;
    }
}
