<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Primary Meta Tags -->
    <meta name="author" content="Philex Mines Technology Team">
    <meta name="theme-color" content="#000000">

    <!-- Favicon & App Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Default OG Image Fallback (in case Vue metadata doesn't load) -->
    <meta property="og:image" content="{{ asset('images/dashboard-image.png') }}">

    <!-- Structured Data (JSON-LD Schema.org) -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "PhilexScholar",
            "url": "{{ url('/') }}",
            "image": "{{ asset('images/dashboard-image.png') }}",
            "description": "PhilexScholar is a centralized platform for managing scholarships in the Philex Mines community, streamlining the entire scholarship lifecycle.",
            "applicationCategory": "Education",
            "operatingSystem": "All",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "PHP",
                "category": "Free"
            },
            "provider": {
                "@type": "Organization",
                "name": "Philex Mines Technology Team",
                "url": "{{ url('/') }}"
            }
        }
    </script>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
