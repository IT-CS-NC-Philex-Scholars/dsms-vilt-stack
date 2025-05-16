<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'province',
        'region',
        'type',
        'level',
        'contact_number',
        'email',
        'website',
        'description',
        'is_active',
        'additional_info',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'additional_info' => 'array',
        'type' => 'string',
    ];
}
