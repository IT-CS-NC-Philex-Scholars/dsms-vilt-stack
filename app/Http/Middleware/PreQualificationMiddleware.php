<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
class PreQualificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next)
         {
             // Check if user is pre-qualified
             if (!session('is_pre_qualified')) {
                 return redirect()->route('pre-qualification.create')
                     ->with('error', 'You need to complete the pre-qualification form first.');
             }

             // Share pre-qualification data with the view
             Inertia::share([
                 'preQualificationData' => session('pre_qualification_data'),
                 'flashSuccessMessage' => session('flash_success_message')
             ]);

             return $next($request);
         }
}
