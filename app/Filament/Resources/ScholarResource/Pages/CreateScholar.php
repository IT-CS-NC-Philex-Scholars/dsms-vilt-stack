<?php

declare(strict_types=1);

namespace App\Filament\Resources\ScholarResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ScholarResource;

final class CreateScholar extends CreateRecord
{
    protected static string $resource = ScholarResource::class;
}
