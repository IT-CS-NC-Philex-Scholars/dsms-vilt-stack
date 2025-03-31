<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application; // Import Application

final class WelcomeController extends Controller
{
    public function home(Application $app): Response // Inject Application
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            // Pass app name and potentially PHP/Laravel versions if needed in layout/page
            'appName' => config('app.name', 'PhilexScholar'),
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => $app->version(),
            'seo' => [
                // Update SEO title and description based on README
                'title' => 'PhilexScholar | Your Digital Scholarship Hub',
                'description' => 'Transforming scholarship management for the Philex Mines community. Streamlining applications, tracking, and disbursements with our modern platform.',
            ],
        ]);
    }
}
