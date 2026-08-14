<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


      <!-- SEO Meta Tags -->
    @php
        use App\Helpers\MetaTags;
        $meta = MetaTags::getMeta();
        $shouldIndex = MetaTags::shouldIndex();
    @endphp
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    @if (!$shouldIndex)
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    @endif


    <meta name="author" content="GetVeraSign">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="publisher" content="GetVeraSign">
    <link rel="canonical" href="{{ url()->current() }}">



    <link rel="image_src" href="{{ asset('images/getverasign.webp') }}" />
    <meta property="og:type" content="image/png" />
    <meta property="og:title" content="{{ $meta['title'] }}" />
    <meta property="og:description"
        content="{{ $meta['description'] }}" />
    <meta property="og:image" content="{{ asset('images/getverasign.webp') }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:alt"
        content="GetVeraSign, plataforma de firma digital" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="GetVeraSign" />
    <meta property="og:locale" content="es_MX" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:image" content=\"{{ asset('images/getverasign.webp') }}\">
    <meta property=\"og:site_name\" content=\"GetVeraSign\">

    <!-- Twitter -->
    <meta property=\"twitter:card\" content=\"summary_large_image\">
    <meta property=\"twitter:url\" content=\"{{ url()->current() }}\">
    <meta property=\"twitter:title\" content=\"{{ $meta['title'] }}\">
    <meta property=\"twitter:description\"
        content=\"{{ $meta['description'] }}\">
    <meta property=\"twitter:image\" content=\"{{ asset('images/getverasign.webp') }}\">

    <!-- WhatsApp -->
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:secure_url" content="{{ asset('images/getverasign.webp') }}" />
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#d4af37">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app"></div>
</body>

</html>