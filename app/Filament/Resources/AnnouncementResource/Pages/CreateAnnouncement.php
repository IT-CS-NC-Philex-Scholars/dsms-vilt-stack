<?php

declare(strict_types=1);

namespace App\Filament\Resources\AnnouncementResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AnnouncementResource;

final class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;
}
