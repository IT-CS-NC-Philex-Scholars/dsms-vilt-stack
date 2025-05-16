<div class="space-y-4">
    <div class="p-4 bg-primary-50 dark:bg-primary-950 rounded-lg border border-primary-200 dark:border-primary-800">
        <h3 class="text-lg font-bold text-primary-700 dark:text-primary-300">
            {{ str_replace('_', ' ', ucwords($document->type)) }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Uploaded on {{ $document->created_at->format('M d, Y h:i A') }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">File Name</span>
            <p class="mt-1">{{ $document->original_name }}</p>
        </div>
        
        <div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
            <div class="mt-1">
                @php
                    $color = match($document->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning'
                    };
                @endphp
                <x-filament::badge :color="$color">
                    {{ ucfirst($document->status) }}
                </x-filament::badge>
            </div>
        </div>
        
        @if($document->semester_type && $document->semester_number)
        <div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Semester Information</span>
            <p class="mt-1">
                @php
                    $semesterLabel = $document->semester_type === 'semestral' ? 
                        ($document->semester_number === 1 ? '1st Semester' : '2nd Semester') : 
                        match($document->semester_number) {
                            1 => '1st Trimester',
                            2 => '2nd Trimester',
                            3 => '3rd Trimester',
                            default => $document->semester_number . 'th Trimester'
                        };
                    
                    $yearLabel = $document->academic_year ? 
                        ' (' . $document->academic_year . '-' . ($document->academic_year + 1) . ')' : 
                        '';
                @endphp
                {{ $semesterLabel . $yearLabel }}
            </p>
        </div>
        @endif
        
        <div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Verified</span>
            <p class="mt-1">{{ $document->verified ? 'Yes' : 'No' }}</p>
        </div>
        
        @if($document->verification_date)
        <div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Review Date</span>
            <p class="mt-1">{{ $document->verification_date->format('M d, Y h:i A') }}</p>
        </div>
        @endif
    </div>
    
    @if($document->notes)
    <div>
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</span>
        <div class="mt-1 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
            {{ $document->notes }}
        </div>
    </div>
    @endif
    
    @if($document->path || $document->file_path)
    <div class="text-center pt-4">
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">Document Preview</span>
        
        @php
            $filePath = $document->path ?? $document->file_path;
            $extension = pathinfo($document->original_name, PATHINFO_EXTENSION);
        @endphp
        
        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                <img src="{{ Storage::url($filePath) }}" alt="Document Preview" class="max-w-full h-auto mx-auto">
            </div>
        @elseif(strtolower($extension) === 'pdf')
            <div class="text-center">
                <p class="mb-2">PDF document preview not available in this view</p>
                <x-filament::button tag="a" href="{{ Storage::url($filePath) }}" target="_blank">
                    <x-slot:icon>
                        <x-heroicon-m-arrow-top-right-on-square class="w-5 h-5" />
                    </x-slot:icon>
                    Open PDF
                </x-filament::button>
            </div>
        @else
            <div class="text-center">
                <p>Document preview not available for this file type</p>
                <x-filament::button tag="a" href="{{ Storage::url($filePath) }}" target="_blank">
                    <x-slot:icon>
                        <x-heroicon-m-arrow-down-tray class="w-5 h-5" />
                    </x-slot:icon>
                    Download File
                </x-filament::button>
            </div>
        @endif
    </div>
    @endif
</div> 