<?php

namespace App\Filament\Resources\ScholarshipResource\Pages;


use App\Filament\Resources\ScholarshipResource;
use App\Models\Scholar;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scholarship;

class AssignScholars extends Page
{
    protected static string $resource = ScholarshipResource::class;

    protected static string $view = 'filament.resources.scholarship-resource.pages.assign-scholars';

      public $scholarship;

      public function mount($record): void
          {
              $data = Scholarship::find($record);
              $this->scholarship = $data;
          }

          protected function getTableQuery(): Builder
              {
                  return Scholar::query();
              }
              protected function getTableColumns(): array
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

                  protected function getTableActions(): array
                  {
                      return [
                          Tables\Actions\Action::make('assign')
                              ->action(function (Model $record): void {
                                  $requirement = new \App\Models\Requirement([
                                      'scholar_id' => $record->id,
                                      'scholarship_id' => $this->scholarship->id,
                                      'status' => 'pending'
                                  ]);
                                  $requirement->save();

                                  $this->notify('success', 'Scholar assigned successfully');
                              })
                              ->requiresConfirmation()
                              ->hidden(fn (Model $record) => $record->scholarships->contains($this->scholarship->id)),
                      ];
                  }

                  protected function getTableFilters(): array
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
