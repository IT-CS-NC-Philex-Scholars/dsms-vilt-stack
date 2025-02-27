<div class="">
    <!-- Scholar Dashboard -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Scholarship Requirements</h1>
                <span class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">
                    Scholar ID: {{ $scholar->id }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Scholar Details</h2>
                    <div class="space-y-2 text-sm">
                        <p class="text-gray-600 dark:text-gray-400">Name: <span class="font-medium text-gray-900 dark:text-white">{{ $scholar->full_name }}</span></p>
                        <p class="text-gray-600 dark:text-gray-400">School: <span class="font-medium text-gray-900 dark:text-white">{{ $scholar->school->name }}</span></p>
                        <p class="text-gray-600 dark:text-gray-400">Course: <span class="font-medium text-gray-900 dark:text-white">{{ $scholar->course }}</span></p>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Scholarship Details</h2>
                    <div class="space-y-2 text-sm">
                        <p class="text-gray-600 dark:text-gray-400">Program: <span class="font-medium text-gray-900 dark:text-white">{{ $scholarship->name }}</span></p>
                        <p class="text-gray-600 dark:text-gray-400">Amount: <span class="font-medium text-gray-900 dark:text-white">₱{{ number_format($scholarship->amount, 2) }}</span></p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($requirements as $requirement)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <div class="@if($requirement->status === 'approved') text-green-500 @elseif($requirement->status === 'rejected') text-red-500 @else text-yellow-500 @endif">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $requirement->document_type }}</h3>
                            @if($requirement->file_path)
                            <p class="text-sm text-gray-500">{{ basename($requirement->file_path) }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            @if($requirement->status === 'approved')
                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($requirement->status === 'rejected')
                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @else
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @endif">
                            {{ ucfirst($requirement->status) }}
                        </span>

                        <x-filament::button
                            type="button"
                            color="primary"
                            tag="a"
                            href="{{ App\Filament\Resources\RequirementResource::getUrl('edit', ['record' => $requirement]) }}"
                            target="_blank"
                            size="sm"
                            icon="heroicon-o-pencil"
                        >
                            Edit
                        </x-filament::button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
