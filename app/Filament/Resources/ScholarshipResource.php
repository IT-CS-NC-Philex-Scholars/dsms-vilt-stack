<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Scholarship;
use Filament\Resources\Resource;
use App\Filament\Resources\ScholarshipResource\Pages;
use App\Filament\Resources\ScholarshipResource\RelationManagers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Scholarships';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('1. Scholarship Program Overview')
                    ->description('Provide the fundamental details that define this scholarship program.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                Forms\Components\TextInput::make('name')
                            ->label('Official Name of the Scholarship Program')
                            ->placeholder('e.g., Bright Futures Scholarship, Academic Excellence Grant')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Brief Description & Purpose')
                            ->placeholder('Summarize the scholarship program, its goals, and who it aims to support. You can use bullet points for key objectives.')
                            ->helperText('A concise overview. Use formatting tools for readability.')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                            ->label('Current Program Status')
                            ->options([
                                'upcoming' => 'Upcoming/Announced (Not yet open for applications)',
                                'accepting_applications' => 'Active - Accepting Applications Now',
                                'screening' => 'Screening/Evaluation (Application period closed, reviewing applicants)',
                                'awarding' => 'Awarding (Scholars selected, notifying recipients)',
                                'ongoing' => 'Ongoing (Scholars are currently benefiting)',
                                'completed' => 'Completed/Closed (Program cycle finished, not currently active)',
                                'archived' => 'Archived (No longer offered, kept for records)',
                            ])
                            ->default('upcoming')
                            ->required()
                            ->helperText('Select the current operational phase of this scholarship program.'),
                        Forms\Components\Select::make('scholarship_program_type')
                            ->label('Type of Scholarship Program')
                            ->options([
                                'ched_cmsp' => 'CHED Merit Scholarship Program (CMSP)',
                                'ched_ssp' => 'CHED State Scholarship Program (SSP)',
                                'ched_pesfa' => 'CHED Private Education Student Financial Assistance (PESFA)',
                                'institutional' => 'Institutional / University-Specific Program',
                                'private_foundation' => 'Private Foundation / NGO Grant',
                                'lgu_funded' => 'Local Government Unit (LGU) Funded Program',
                                'corporate_sponsored' => 'Corporate Sponsored Scholarship',
                                'other' => 'Other (Please specify in description or notes if applicable)',
                            ])
                            ->searchable()
                            ->nullable()
                            ->helperText('Categorize the scholarship based on its primary source or nature.'),
                        Forms\Components\Select::make('financial_assistance_type')
                            ->label('Nature of Financial Assistance')
                            ->options([
                                'full' => 'Full Scholarship (Covers most/all major expenses)',
                                'partial' => 'Partial Scholarship / Half Merit (Covers specific portions)',
                                'tuition_only' => 'Tuition and Other School Fees Only',
                                'stipend_only' => 'Stipend / Living Allowance Only',
                                'allowance_only' => 'Book / Connectivity / Other Specific Allowance Only',
                                'mixed' => 'Mixed Benefits (Various components, describe in Benefits section)',
                            ])
                            ->searchable()
                            ->nullable()
                            ->helperText('Specify what kind of financial support is provided.'),
                        Forms\Components\TextInput::make('slots_available')
                            ->label('Number of Available Scholarship Slots')
                            ->placeholder('e.g., 10 or 0 for unspecified')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->nullable()
                            ->helperText('Enter the total number of scholars that can be accepted for this cycle. Leave blank or 0 if not fixed.'),
                    ]),

                Forms\Components\Section::make('2. Eligibility & Application Timeline')
                    ->description('Define who can apply and the key dates for the application process.')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('application_period_start')
                            ->label('Application Period - Start Date')
                            ->required()
                            ->helperText('The date when applications will begin to be accepted.'),
                        Forms\Components\DatePicker::make('application_period_end')
                            ->label('Application Period - End Date')
                            ->required()
                            ->helperText('The deadline for submitting applications.'),
                        Forms\Components\Select::make('target_student_group')
                            ->label('Target Student Group(s)')
                    ->options([
                                'jhs_g9_g10' => 'Junior High School (Grade 9-10)',
                                'shs_g11_g12' => 'Senior High School (Grade 11-12)',
                                'shs_graduating' => 'Graduating Senior High School (for upcoming college enrollment)',
                                'college_freshmen' => 'Incoming College Freshmen (First Year)',
                                'college_sophomore' => 'College Sophomore (Second Year)',
                                'college_junior' => 'College Junior (Third Year)',
                                'college_senior' => 'College Senior (Fourth/Fifth Year)',
                                'college_ongoing_any_level' => 'Ongoing Undergraduate College Students (Any Level)',
                                'graduate_studies_masters' => 'Graduate Studies (Masters Degree)',
                                'graduate_studies_phd' => 'Graduate Studies (Doctoral Degree)',
                                'vocational_technical' => 'Vocational/Technical Course Students',
                                'all_student_levels' => 'All Student Levels (JHS to Graduate)',
                                'other' => 'Other (If selecting, please specify details in \'Other Specific Eligibility Criteria\' below)',
                            ])
                            ->multiple()
                            ->searchable()
                            ->nullable()
                            ->helperText('Select all student categories eligible. If \'Other\' is chosen, ensure to provide details in the relevant eligibility field below.')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('gwa_requirement_description')
                            ->label('General Weighted Average (GWA) / Academic Requirements')
                            ->placeholder('e.g., Minimum GWA of 90% or B+ equivalent; Must maintain a GWA of 2.0 per semester.')
                            ->helperText('Specify academic criteria. Use lists for multiple requirements.')
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\RichEditor::make('income_bracket_requirement_description')
                            ->label('Financial Need / Income Bracket Requirements')
                            ->placeholder('e.g., For students from families with a total annual income not exceeding P400,000. Proof of income (ITR or Certificate of Indigence) required.')
                            ->helperText('Describe financial qualifications. Use formatting for clarity.')
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\RichEditor::make('eligibility_criteria_description')
                            ->label('Other Specific Eligibility Criteria')
                            ->placeholder('e.g., Must be a resident of City X; Must not be a recipient of other government scholarships; Details for \'Other\' target student group.')
                            ->helperText('List any other conditions (residency, course restrictions, etc.). If you selected \'Other\' for Target Student Group, specify here.')
                            ->columnSpanFull()
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('3. Scholarship Benefits & Application Procedure')
                    ->description('Detail what the scholarship provides and how applicants can apply.')
                    ->icon('heroicon-o-document-text')
                    ->columns(1)
                    ->schema([
                        Forms\Components\RichEditor::make('benefits_description')
                            ->label('Detailed Scholarship Benefits')
                            ->placeholder("Example:\n- Full tuition and other school fees coverage.\n- Monthly stipend of P5,000.\n- Annual book allowance of P3,000.\n- Thesis/Dissertation grant of P10,000.")
                            ->helperText('Clearly itemize all benefits. Use bullet points for easy reading.')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\RichEditor::make('documentary_requirements_description')
                            ->label('List of Required Documents for Application')
                            ->placeholder("Example:\n1. Certified True Copy of PSA Birth Certificate.\n2. Latest Income Tax Return (ITR) of parents/guardian or BIR Certificate of Tax Exemption.\n3. Form 138 (High School Report Card) for freshmen.\n4. Certificate of Good Moral Character.\n5. Proof of Enrollment (if applicable).")
                            ->helperText('Enumerate all required documents. Use numbered lists if possible.')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\RichEditor::make('application_process_description')
                            ->label('Step-by-Step Application Process & Instructions')
                            ->placeholder("Example:\n1. Download the application form from [Link].\n2. Fill out the form completely.\n3. Compile all required documents as listed above.\n4. Submit the application packet via email to [scholarships@example.com] or in person at [Office Address] by [Deadline Date].\n5. Wait for notification of interview schedule via email within [Timeframe, e.g., 2 weeks after deadline].")
                            ->helperText('Provide clear, step-by-step instructions. Use numbered lists and include any relevant links or contact points.')
                    ->columnSpanFull()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Program Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scholarship_program_type')
                    ->label('Program Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ched_cmsp' => 'CHED CMSP',
                        'ched_ssp' => 'CHED SSP',
                        'ched_pesfa' => 'CHED PESFA',
                        'institutional' => 'Institutional',
                        'private_foundation' => 'Private Foundation',
                        'lgu_funded' => 'LGU Funded',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('application_period_end')
                    ->label('Application End')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'primary' => 'upcoming',
                        'success' => 'accepting_applications',
                        'info' => 'screening',
                        'warning' => 'awarding',
                        'success' => 'ongoing', // You might want a different color for ongoing vs accepting
                        'danger' => 'completed',
                        'gray' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('slots_available')
                    ->label('Slots')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming/Announced',
                        'accepting_applications' => 'Accepting Applications',
                        'screening' => 'Screening/Evaluation',
                        'awarding' => 'Awarding',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed/Closed',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('scholarship_program_type')
                    ->label('Program Type')
                    ->options([
                        'ched_cmsp' => 'CHED CMSP',
                        'ched_ssp' => 'CHED SSP',
                        'ched_pesfa' => 'CHED PESFA',
                        'institutional' => 'Institutional',
                        'private_foundation' => 'Private Foundation',
                        'lgu_funded' => 'LGU Funded',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ApplicationsRelationManager::class,
            RelationManagers\ScholarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholarships::route('/'),
            'create' => Pages\CreateScholarship::route('/create'),
            'view' => Pages\ViewScholarship::route('/{record}'),
            'edit' => Pages\EditScholarship::route('/{record}/edit'),
        ];
    }
}
