<?php

declare(strict_types=1);

namespace App\Filament\Resources\RequirementResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\RequirementResource;

final class EditRequirement extends EditRecord
{
    protected static string $resource = RequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
