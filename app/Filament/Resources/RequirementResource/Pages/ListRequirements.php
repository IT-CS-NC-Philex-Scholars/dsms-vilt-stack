<?php

declare(strict_types=1);

namespace App\Filament\Resources\RequirementResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RequirementResource;

final class ListRequirements extends ListRecords
{
    protected static string $resource = RequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
