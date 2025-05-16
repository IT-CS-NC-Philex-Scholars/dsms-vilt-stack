<?php

declare(strict_types=1);

namespace App\Http\Controllers;

// Keep if you plan to log attempts
use Inertia\Inertia;
use App\Models\School;
use Illuminate\Http\Request; // Make sure you have this model
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

 // Required for conditional validation

final class PreQualificationController extends Controller
{
    public function create()
    {
        // Fetch schools - Assuming 'School' model exists and has relevant schools
        // You might want to add a 'type' column (shs, college) to your schools
        // table later for more specific filtering if needed.
        $schools = \App\Models\School::query()->select('id', 'name', 'type')
                        // ->whereIn('type', ['shs', 'college']) // Example if you add types
            ->orderBy('name')
            ->get();

        return Inertia::render('PreQualification/Form', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'schools' => $schools,
            'flash' => [ // Pass flash messages explicitly if not using default middleware
                'success' => session('flash_success_message'),
                'error' => session('error'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal & Contact Info
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            // Ensure email uniqueness check excludes current user if editing later
            // For pre-qual, check users table AND maybe pending registrations if needed
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'contact_number' => ['required', 'string', 'max:20'], // Consider specific format validation
            'address' => ['required', 'string', 'max:1000'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'current_grade' => ['required', 'numeric', 'min:0', 'max:100'],

            // Educational Info
            'educational_level' => ['required', 'string', Rule::in(['shs', 'college'])],
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'enrollment_intent' => ['required', 'string', 'max:2000'],

            // Conditional SHS Fields
            'strand' => ['required_if:educational_level,shs', 'nullable', 'string', 'max:255'],
            'shs_grade_level' => ['required_if:educational_level,shs', 'nullable', 'integer', Rule::in([11, 12])],

            // Conditional College Fields
            'course' => ['required_if:educational_level,college', 'nullable', 'string', 'max:255'],
            'year_level' => ['required_if:educational_level,college', 'nullable', 'integer', 'min:1', 'max:6'], // Adjust max as needed
            'semester_system' => ['required_if:educational_level,college', 'nullable', 'string', Rule::in(['semestral', 'trimesteral'])], // Added semester_system validation
        ]);

        // Eligibility Check (Universal for now)
        if ($validated['current_grade'] >= 80) {
            // Store validated data in session for registration
            // Only store relevant fields based on educational_level
            $preQualData = $validated;
            if ($validated['educational_level'] === 'shs') {
                unset($preQualData['course'], $preQualData['year_level'], $preQualData['semester_system']); // Ensure semester_system is unset for SHS
            } else { // college
                unset($preQualData['strand'], $preQualData['shs_grade_level']);
                // semester_system is relevant for college, so it's kept
            }

            session([
                'pre_qualification_data' => $preQualData,
                'is_pre_qualified' => true,
                // Use dedicated session flash for success message on redirect
                // 'flash_success_message' => 'Congratulations! You are eligible...'
            ]);

            // Redirect to registration with a success flash message
            return redirect()->route('register')
                ->with('success', 'Congratulations! You are eligible for the scholarship. Please create an account to continue your application.');

        }

        // Not eligible - return back with error flash message and input
        return back()
            ->with('error', 'Unfortunately, you do not meet the minimum grade requirement of 80%.')
            ->withInput(); // Keep filled data
    }
}
