<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'title',
            'content',
            'priority',
            'published_at'
        ];

        protected $casts = [
            'published_at' => 'datetime'
        ];
}
