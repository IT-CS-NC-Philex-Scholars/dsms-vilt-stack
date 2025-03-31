<?php
declare(strict_types=1);

namespace App\Http\Controllers;
use App\Models\Application;
use App\Models\Document;
use App\Models\Scholar;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;


final class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();
        $application = Application::where('user_id', $user->id)->firstOrFail();
        $documents = Document::where('application_id', $application->id)->get();
        $scholar = Scholar::where('user_id', $user->id)->first();

        // Get scholarships the scholar has already applied for
        $activeScholarships = $scholar->scholarships()->with('pivot')->get();

        // Get all available scholarships
        $availableScholarships = Scholarship::where('status', 'active')
            // ->where('application_deadline', '>=', now())
            ->get();
        // dd($availableScholarships)

        return Inertia::render('Dashboard', [
            'application' => $application,
            'documents' => $documents,
            'scholar' => $scholar,
            'documentTypes' => $this->getRequiredDocumentTypes(),
            'availableScholarships' => $availableScholarships,
            'activeScholarships' => $activeScholarships,
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|in:grade_slip,id_card,enrollment_certificate,income_certificate,recommendation_letter',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $user = auth()->user();
        $application = Application::where('user_id', $user->id)->firstOrFail();

        // Check if document of this type already exists
        $existingDocument = Document::where('application_id', $application->id)
            ->where('type', $request->document_type)
            ->first();

        if ($existingDocument) {
            // Delete the old file if it exists
            if (Storage::exists($existingDocument->file_path)) {
                Storage::delete($existingDocument->file_path);
            }
            $existingDocument->delete();
        }

        // Store the new file
        $path = $request->file('file')->store('documents/' . $user->id);

        Document::create([
            'application_id' => $application->id,
            'type' => $request->document_type,
            'file_path' => $path,
            'status' => 'pending',
        ]);

        // Update verification flags on application
        if ($request->document_type === 'grade_slip') {
            $application->update(['grade_verified' => true]);
        } elseif ($request->document_type === 'id_card') {
            $application->update(['address_verified' => true]);
        } elseif ($request->document_type === 'enrollment_certificate') {
            $application->update(['enrollment_verified' => true]);
        }

        return redirect()->back()->with('success', 'Document uploaded successfully');
    }

    public function submitApplication()
    {
        $user = auth()->user();
        $application = Application::where('user_id', $user->id)->firstOrFail();
        $documentCount = Document::where('application_id', $application->id)->count();

        if ($documentCount < 3) {
            return redirect()->back()->with('error', 'Please upload at least 3 required documents');
        }

        $application->update(['status' => 'submitted']);

        return redirect()->back()->with('success', 'Application submitted successfully');
    }

    public function applyScholarship(Request $request)
    {
        $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
        ]);

        $user = auth()->user();
        $scholar = Scholar::where('user_id', $user->id)->first();
        $scholarship = Scholarship::findOrFail($request->scholarship_id);

        // Check if scholar has already applied for this scholarship
        if ($scholar->scholarships()->where('scholarship_id', $scholarship->id)->exists()) {
            return redirect()->back()->with('error', 'You have already applied for this scholarship');
        }

        // Check if application is complete
        $application = Application::where('user_id', $user->id)->firstOrFail();
        $requiredDocumentTypes = array_column(
            array_filter($this->getRequiredDocumentTypes(), fn($doc) => $doc['required']),
            'type'
        );

        $uploadedRequiredDocs = Document::where('application_id', $application->id)
            ->whereIn('type', $requiredDocumentTypes)
            ->count();

        if ($uploadedRequiredDocs < count($requiredDocumentTypes)) {
            return redirect()->back()->with('error', 'Please upload all required documents before applying for scholarships');
        }

        // Attach scholar to scholarship
        $scholar->scholarships()->attach($scholarship->id, [
            'status' => 'pending',
            'start_date' => null,
            'end_date' => null,
            'remarks' => 'Application under review',
        ]);

        return redirect()->back()->with('success', 'You have successfully applied for the scholarship');
    }

    private function getRequiredDocumentTypes()
    {
        return [
            [
                'type' => 'grade_slip',
                'label' => 'Grade Slip',
                'description' => 'Upload your most recent grade slip showing your academic performance',
                'required' => true,
            ],
            [
                'type' => 'id_card',
                'label' => 'ID Card',
                'description' => 'Upload your school or government-issued ID card',
                'required' => true,
            ],
            [
                'type' => 'enrollment_certificate',
                'label' => 'Enrollment Certificate',
                'description' => 'Upload a certificate proving your current enrollment',
                'required' => true,
            ],
            [
                'type' => 'income_certificate',
                'label' => 'Income Certificate',
                'description' => 'Upload proof of family income (if applicable)',
                'required' => false,
            ],
            [
                'type' => 'recommendation_letter',
                'label' => 'Recommendation Letter',
                'description' => 'Upload a recommendation letter from a teacher or mentor',
                'required' => false,
            ],
        ];
    }
}
