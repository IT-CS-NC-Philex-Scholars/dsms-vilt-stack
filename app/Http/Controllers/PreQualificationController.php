<?php

namespace App\Http\Controllers;

use App\Models\PreQualification;
use App\Models\Scholar;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\School;

class PreQualificationController extends Controller
{
    public function create()
    {
        $schools = School::select('id', 'name')->get();
        return Inertia::render('PreQualification/Form', [
            'canLogin' => true,
            'canRegister' => true,
            'schools' => $schools
        ]);
    }

    public function store(Request $request)
        {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'contact_number' => ['required', 'string', 'max:20'],
                'address' => ['required', 'string'],
                'birth_date' => ['required', 'date'],
                'gender' => ['required', 'string', 'in:male,female,other'],
                'current_grade' => ['required', 'numeric', 'min:0', 'max:100'],
                'enrollment_intent' => ['required', 'string'],
                'year_level' => ['required', 'integer', 'min:1', 'max:6'],
                'course' => ['required', 'string', 'max:255'],
                'school_id' => ['required', 'exists:schools,id'],
            ]);

            if ($validated['current_grade'] >= 80) {
                // Store in session for registration
                session([
                    'pre_qualification_data' => $validated,
                    'is_pre_qualified' => true,
                    'flash_success_message' => 'Congratulations! You are eligible for the scholarship. Please create an account to continue your application.'
                ]);

                return redirect()->route('register');
            }

            return back()
                ->with('error', 'Unfortunately, you do not meet the minimum grade requirement of 80%.')
                ->withInput();
        }
}
