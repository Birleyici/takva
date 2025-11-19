<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Takva Dergisi')</title>
    <meta name="description" content="@yield('description', 'Takva Dergisi - İslami bilim, kültür ve düşünce dünyasından en güncel makaleler ve sayılar')">
    <meta name="keywords" content="@yield('keywords', 'takva, dergi, islam, makaleler, bilim, kültür')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'Takva Dergisi')">
    <meta property="og:description" content="@yield('og_description', 'İslami bilim, kültür ve düşünce dünyasından en güncel makaleler ve sayılar')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Takva Dergisi')">
    <meta name="twitter:description" content="@yield('twitter_description', 'İslami bilim, kültür ve düşünce dünyasından en güncel makaleler ve sayılar')">
    
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="min-h-screen bg-white">
    <x-layout.navigation />
    
    @yield('content')
    
    <x-layout.footer />
    
    @stack('scripts')
</body>
</html>