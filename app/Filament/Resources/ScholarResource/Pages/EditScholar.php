<?php

declare(strict_types=1);

namespace App\Filament\Resources\ScholarResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ScholarResource;

final class EditScholar extends EditRecord
{
    protected static string $resource = ScholarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
