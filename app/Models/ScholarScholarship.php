<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ScholarScholarship extends Model
{
    use SoftDeletes;

    protected $table = 'scholar_scholarship';

    protected $fillable = [
        'scholar_id',
        'scholarship_id',
        'status',
        'start_date',
        'end_date',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
