<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if(isset($page['props']['seo']))
            <title inertia>{{ $page['props']['seo']['title'] ?? config('app.name', 'Laravel') }}</title>
            @if(isset($page['props']['seo']['description']))
                <meta name="description" content="{{ $page['props']['seo']['description'] }}">
            @endif
            @if(isset($page['props']['seo']['canonical']))
                <link rel="canonical" href="{{ $page['props']['seo']['canonical'] }}">
            @endif

            @if(isset($page['props']['seo']['open_graph']))
                @foreach($page['props']['seo']['open_graph'] as $key => $value)
                    <meta property="{{ $key }}" content="{{ $value }}">
                @endforeach
                {{-- Schema.org / WhatsApp fallback microdata --}}
                <meta itemprop="name" content="{{ $page['props']['seo']['title'] }}">
                <meta itemprop="description" content="{{ $page['props']['seo']['description'] }}">
                <meta itemprop="image" content="{{ $page['props']['seo']['open_graph']['og:image'] ?? '' }}">
            @endif

            @if(isset($page['props']['seo']['twitter']))
                @foreach($page['props']['seo']['twitter'] as $key => $value)
                    <meta name="{{ $key }}" content="{{ $value }}">
                @endforeach
            @endif

            @if(isset($page['props']['seo']['tracking']['gsc']))
                <meta name="google-site-verification" content="{{ $page['props']['seo']['tracking']['gsc'] }}">
            @endif
            
            @if(isset($page['props']['seo']['schema']))
                <script type="application/ld+json">
                    {!! is_string($page['props']['seo']['schema']) ? $page['props']['seo']['schema'] : json_encode($page['props']['seo']['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
                </script>
            @endif
        @else
            <title inertia>{{ config('app.name', 'Laravel') }}</title>
        @endif
        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="192x192" href="/favicon-192.png">
        <link rel="icon" type="image/png" sizes="512x512" href="/favicon-512.png">
        <meta name="theme-color" content="#059669">
        @routes
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased text-slate-900 bg-slate-50">
        @inertia
    </body>
</html>
