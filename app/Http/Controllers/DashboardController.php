<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Scholar;
use App\Models\Document;
use App\Models\Application;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $application = Application::query()->where('user_id', $user->id)->first();

        if (!$application) {
            $application = Application::create([
                'user_id' => $user->id,
                'status' => 'incomplete',
            ]);
        }

        $documents = Document::query()->where('user_id', $user->id)->get();
        $scholar = Scholar::query()->where('user_id', $user->id)->first();

        $activeScholarships = $scholar ? $scholar->scholarships()->with('pivot')->get() : collect();

        $availableScholarships = Scholarship::query()
            ->where('status', 'active')
            ->whereDate('application_period_end', '>=', now())
            ->orderBy('application_period_end', 'asc')
            ->get();
            
        // Get current academic year
        $currentYear = Carbon::now()->month >= 6 
            ? Carbon::now()->year 
            : Carbon::now()->year - 1;

        return Inertia::render('Dashboard', [
            'application' => $application,
            'documents' => $documents,
            'scholar' => $scholar,
            'documentTypes' => $this->getRequiredDocumentTypes(),
            'availableScholarships' => $availableScholarships,
            'activeScholarships' => $activeScholarships,
            'currentAcademicYear' => $currentYear,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ],
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $user = $request->user();

        try {
            $validated = $request->validate([
                'document_type' => 'required|string|in:grade_slip,id_card,enrollment_certificate,income_certificate,recommendation_letter',
                'file' => ['required', File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(10 * 1024)],
                'semester_type' => 'nullable|string|in:semestral,trimesteral',
                'semester_number' => 'nullable|integer|min:1|max:3',
                'academic_year' => 'nullable|integer|min:2000|max:2100',
            ]);

            $application = Application::query()->where('user_id', $user->id)->first();
            
            if (!$application) {
                $application = Application::create([
                    'user_id' => $user->id,
                    'status' => 'incomplete',
                ]);
            }

            // Find existing document of the same type and semester information
            $query = Document::query()
                ->where('user_id', $user->id)
                ->where('type', $validated['document_type']);
                
            // If semester info is provided, include it in the search
            if (!empty($validated['semester_type']) && !empty($validated['semester_number']) && !empty($validated['academic_year'])) {
                $query->where('semester_type', $validated['semester_type'])
                      ->where('semester_number', $validated['semester_number'])
                      ->where('academic_year', $validated['academic_year']);
            }
            
            $existingDocument = $query->first();

            // Delete old file if it exists
            if ($existingDocument) {
                if (Storage::disk('public')->exists($existingDocument->file_path)) {
                    Storage::disk('public')->delete($existingDocument->file_path);
                }
                // Don't delete the record yet, we'll update it
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $sanitizedOriginalName = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
            $baseName = pathinfo($sanitizedOriginalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $maxBaseNameLength = 100;
            $truncatedBaseName = substr($baseName, 0, $maxBaseNameLength);
            
            // Include semester info in the filename if provided
            $semesterInfo = '';
            if (!empty($validated['semester_type']) && !empty($validated['semester_number']) && !empty($validated['academic_year'])) {
                $semType = substr($validated['semester_type'], 0, 3); // sem or tri
                $semesterInfo = "_{$semType}{$validated['semester_number']}_{$validated['academic_year']}";
            }
            
            $fileName = "{$user->id}_{$validated['document_type']}{$semesterInfo}_" . time() . '.' . $extension;
            
            // Use public disk for easier access
            $path = $file->storeAs(
                "documents/user_{$user->id}",
                $fileName,
                'public'
            );

            // Create or update document record
            $documentData = [
                'file_path' => $path,
                'original_name' => $originalName,
                'disk' => 'public',
                'mime_type' => $file->getMimeType(),
                'status' => 'pending',
                'file_size' => $file->getSize(),
            ];
            
            // Only add semester fields if they're provided
            if (!empty($validated['semester_type'])) {
                $documentData['semester_type'] = $validated['semester_type'];
            }
            if (!empty($validated['semester_number'])) {
                $documentData['semester_number'] = $validated['semester_number'];
            }
            if (!empty($validated['academic_year'])) {
                $documentData['academic_year'] = $validated['academic_year'];
            }
            
            if ($existingDocument) {
                $existingDocument->update($documentData);
                $document = $existingDocument;
            } else {
                $documentData['user_id'] = $user->id;
                $documentData['application_id'] = $application->id;
                $documentData['type'] = $validated['document_type'];
                
                $document = Document::create($documentData);
            }

            // Get fresh data for the response
            $documents = Document::query()->where('user_id', $user->id)->get();
            $documentType = Str::title(str_replace('_', ' ', $validated['document_type']));
            
            // Return response for Inertia
            return back()->with('success', "{$documentType} uploaded successfully.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Inertia
            throw $e;
        } catch (\Exception $e) {
            Log::error('Document upload failed for user: ' . $user->id, [
                'document_type' => $request->document_type,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'An unexpected error occurred during file upload: ' . $e->getMessage()
            ]);
        }
    }

    public function submitApplication(Request $request)
    {
        $user = $request->user();
        $application = Application::query()->where('user_id', $user->id)->firstOrFail();
        
        $requiredDocTypes = collect($this->getRequiredDocumentTypes())->where('required', true)->pluck('type');
        $uploadedRequiredDocsCount = Document::query()
            ->where('user_id', $user->id)
            ->whereIn('type', $requiredDocTypes)
            ->count();

        if ($uploadedRequiredDocsCount < $requiredDocTypes->count()) {
            return redirect()->back()->with('error', 'Please upload all required documents before submitting.');
        }

        $application->update(['status' => 'submitted']);

        return redirect()->back()->with('success', 'Application submitted successfully');
    }

    public function applyScholarship(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
        ]);

        $scholar = Scholar::query()->where('user_id', $user->id)->firstOrFail();

        $application = Application::query()->where('user_id', $user->id)->firstOrFail();
        if($application->status !== 'approved') {
            return redirect()->back()->with('error', 'Your main application must be approved before applying for scholarships.');
        }

        $requiredDocumentTypes = collect($this->getRequiredDocumentTypes())->where('required', true)->pluck('type');
        $uploadedRequiredDocs = Document::query()
            ->where('user_id', $user->id)
            ->whereIn('type', $requiredDocumentTypes)
            ->count();

        if ($uploadedRequiredDocs < $requiredDocumentTypes->count()) {
            return redirect()->back()->with('error', 'Please upload all required documents for your main application first.');
        }

        $scholarship = Scholarship::findOrFail($validated['scholarship_id']);
        if ($scholarship->status !== 'active' || now()->isAfter($scholarship->application_period_end)) {
            return redirect()->back()->with('error', 'This scholarship is no longer accepting applications.');
        }

        if ($scholar->scholarships()->where('scholarship_id', $validated['scholarship_id'])->exists()) {
            return redirect()->back()->with('error', 'You have already applied for this scholarship.');
        }

        $scholar->scholarships()->attach($validated['scholarship_id'], [
            'status' => 'pending',
            'start_date' => null,
            'end_date' => null,
            'remarks' => 'Scholarship application submitted by user.',
        ]);

        return redirect()->back()->with('success', 'You have successfully applied for the scholarship.');
    }

    private function getRequiredDocumentTypes(): array
    {
        return [
            [
                'type' => 'grade_slip',
                'label' => 'Grade Slip',
                'description' => 'Upload your grade slip showing your academic performance. For college students, upload one for each semester/trimester.',
                'required' => true,
                'requires_semester' => true,
            ],
            [
                'type' => 'id_card',
                'label' => 'ID Card / Proof of Identity',
                'description' => 'Upload your school ID, national ID, or any government-issued ID.',
                'required' => true,
                'requires_semester' => false,
            ],
            [
                'type' => 'enrollment_certificate',
                'label' => 'Proof of Enrollment',
                'description' => 'Upload a certificate of registration or any document proving your current enrollment. For college students, upload one for each semester/trimester.',
                'required' => true,
                'requires_semester' => true,
            ],
            [
                'type' => 'income_certificate',
                'label' => 'Proof of Income (Optional)',
                'description' => 'Upload documents showing family income (e.g., ITR, Certificate of Indigency). Not required for all scholarships.',
                'required' => false,
                'requires_semester' => false,
            ],
            [
                'type' => 'recommendation_letter',
                'label' => 'Recommendation Letter (Optional)',
                'description' => 'Upload a recommendation letter from a teacher, mentor, or community leader.',
                'required' => false,
                'requires_semester' => false,
            ],
        ];
    }
}
