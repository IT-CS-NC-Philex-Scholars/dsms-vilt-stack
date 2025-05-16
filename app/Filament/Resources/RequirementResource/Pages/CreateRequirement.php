<?php

declare(strict_types=1);

namespace App\Filament\Resources\RequirementResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RequirementResource;

final class CreateRequirement extends CreateRecord
{
    protected static string $resource = RequirementResource::class;
}
