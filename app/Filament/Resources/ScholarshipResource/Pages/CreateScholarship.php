<?php

declare(strict_types=1);

namespace App\Filament\Resources\ScholarshipResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ScholarshipResource;

final class CreateScholarship extends CreateRecord
{
    protected static string $resource = ScholarshipResource::class;
}
