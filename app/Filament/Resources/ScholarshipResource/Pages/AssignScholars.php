<?php

declare(strict_types=1);

namespace App\Filament\Resources\ScholarshipResource\Pages;

use Filament\Tables;
use App\Models\Scholar;
use App\Models\Requirement;
use App\Models\Scholarship;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ScholarshipResource;

final class AssignScholars extends Page
{
    public $scholarship;

    protected static string $resource = ScholarshipResource::class;

    protected static string $view = 'filament.resources.scholarship-resource.pages.assign-scholars';

    public function mount($record): void
    {
        $data = \App\Models\Scholarship::query()->find($record);
        $this->scholarship = $data;
    }

    private function getTableQuery(): Builder
    {
        return Scholar::query();
    }

    private function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('full_name')
                ->searchable(['first_name', 'middle_name', 'last_name']),
            Tables\Columns\TextColumn::make('school.name')
                ->searchable(),
            Tables\Columns\TextColumn::make('course'),
            Tables\Columns\TextColumn::make('year_level'),
        ];
    }

    private function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('assign')
                ->action(function (Model $record): void {
                    $requirement = new Requirement([
                        'scholar_id' => $record->id,
                        'scholarship_id' => $this->scholarship->id,
                        'status' => 'pending',
                    ]);
                    $requirement->save();

                    $this->notify('success', 'Scholar assigned successfully');
                })
                ->requiresConfirmation()
                ->hidden(fn (Model $record) => $record->scholarships->contains($this->scholarship->id)),
        ];
    }

    private function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('school')
                ->relationship('school', 'name'),
            Tables\Filters\SelectFilter::make('year_level')
                ->options([
                    1 => '1st Year',
                    2 => '2nd Year',
                    3 => '3rd Year',
                    4 => '4th Year',
                    5 => '5th Year',
                ]),
        ];
    }
}
