<?php

declare(strict_types=1);

namespace App\Filament\Resources\ScholarshipResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ScholarshipResource;

final class ListScholarships extends ListRecords
{
    protected static string $resource = ScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
