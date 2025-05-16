<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Document;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Notifications\NewApplicationSubmitted;

final class ScholarController extends Controller
{
    public function dashboard()
    {
        $application = auth()->user()->applications()
            ->with('documents')
            ->latest()
            ->first();

        if (! $application) {
            $application = \App\Models\Application::query()->create([
                'user_id' => auth()->id(),
                'status' => 'draft',
            ]);
        }

        return Inertia::render('Scholar/Dashboard', [
            'application' => $application,
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => ['required', 'file', 'max:10240'], // 10MB max
            'type' => ['required', 'in:residency,enrollment,grades'],
        ]);

        $application = auth()->user()->applications()->latest()->firstOrFail();

        // Delete existing document of same type if exists
        $application->documents()->where('type', $request->type)->delete();

        $path = $request->file('document')->store('documents', 'public');

        $document = new Document([
            'type' => $request->type,
            'file_path' => $path,
            'original_name' => $request->file('document')->getClientOriginalName(),
            'mime_type' => $request->file('document')->getMimeType(),
            'file_size' => $request->file('document')->getSize(),
        ]);

        $application->documents()->save($document);

        return back()->with('success', 'Document uploaded successfully');
    }

    public function submitApplication()
    {
        $application = auth()->user()->applications()->latest()->firstOrFail();

        if ($application->documents()->count() < 3) {
            return back()->with('error', 'Please upload all required documents before submitting');
        }

        $application->update([
            'status' => 'pending_review',
        ]);

        // Notify admins about new application
        User::role('admin')->each(function ($admin) use ($application): void {
            $admin->notify(new NewApplicationSubmitted($application));
        });

        return back()->with('success', 'Application submitted successfully');
    }
}
