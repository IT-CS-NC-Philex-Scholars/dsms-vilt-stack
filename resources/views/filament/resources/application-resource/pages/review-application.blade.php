<x-filament::page class="!max-w-full">
    <div class="space-y-6">

        <!-- Key Information Section -->
        <x-filament::section :collapsible="false">
            <x-slot name="heading" class="sr-only">
                Key Application Information for #{{ $record->id }}
            </x-slot>
            
            <div class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                    <!-- Applicant Info -->
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $record->user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $record->user->email }}</p>
                    </div>

                    <!-- Application & Scholarship Info -->
                    <div class="lg:col-span-1 space-y-1">
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Application ID:</span>
                            <span class="ml-1 text-sm text-gray-700 dark:text-gray-200">#{{ $record->id }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Scholarship:</span>
                            <span class="ml-1 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $record->scholarship->name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Status & Submission Info -->
                    <div class="lg:col-span-1 space-y-1">
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Submitted:</span>
                            <span class="ml-1 text-sm text-gray-700 dark:text-gray-200">{{ $record->submitted_at?->format('M d, Y, h:i A') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mr-2">Status:</span>
                            <x-filament::badge
                                size="md"
                                :color="match($record->status) {
                                    'approved' => 'success',
                                    'submitted', 'pending_review', 'pending' => 'info',
                                    'rejected' => 'danger',
                                    default => 'warning',
                                }"
                            >
                                {{ ucwords(str_replace('_', ' ', $record->status)) }}
                            </x-filament::badge>
                        </div>
                    </div>
                </div>

                @if($record->rejection_reason)
                    <div class="mt-4 p-3 border-l-4 border-danger-400 bg-danger-50 dark:bg-danger-500/10">
                        <h4 class="text-sm font-semibold text-danger-700 dark:text-danger-300">Rejection Reason:</h4>
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-200">
                            {{ $record->rejection_reason }}
                        </p>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Academic Information Section (Collapsible) -->
        @if ($record->user && $record->user->scholar)
            <x-filament::section :collapsible="true" :collapsed="true">
                <x-slot name="heading">
                    <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" />
                    Academic Information
                </x-slot>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-2 text-sm">
                        <div>
                            <span class="font-medium text-gray-500 dark:text-gray-400">School:</span>
                            <p class="text-gray-700 dark:text-gray-200">{{ $record->user->scholar->school->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500 dark:text-gray-400">Course:</span>
                            <p class="text-gray-700 dark:text-gray-200">{{ $record->user->scholar->course ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500 dark:text-gray-400">Year Level:</span>
                            <p class="text-gray-700 dark:text-gray-200">{{ $record->user->scholar->year_level ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        <!-- Submitted Documents Section -->
        <x-filament::section :collapsible="false" class="shadow-sm">
            <x-slot name="heading">
                <x-heroicon-m-document-text class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"/>
                Submitted Documents
            </x-slot>
            <x-slot name="description">
                Review and manage documents submitted by the applicant. You can approve, reject, or view each document.
            </x-slot>
            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament::page> 