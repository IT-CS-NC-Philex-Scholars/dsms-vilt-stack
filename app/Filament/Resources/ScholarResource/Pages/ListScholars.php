<?php

declare(strict_types=1);

namespace App\Filament\Resources\ScholarResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ScholarResource;

final class ListScholars extends ListRecords
{
    protected static string $resource = ScholarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
