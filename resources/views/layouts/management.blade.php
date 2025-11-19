<!DOCTYPE html>
<html lang="tr">
@php
    $managementUser = auth()->user();
    $managementData = [
        'user' => [
            'id' => $managementUser?->id,
            'name' => $managementUser?->name,
            'email' => $managementUser?->email,
        ],
        'routes' => [
            'logout' => route('logout'),
        ],
    ];
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Yönetim Paneli' }}</title>
    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/css/app.css', 'resources/js/management/app.js'])
    @stack('styles')
</head>
<body class="bg-neutral-100 font-body antialiased text-secondary-900">
    <div id="management-root" class="min-h-screen"></div>

    <script>
        window.__MANAGEMENT_DATA__ = @json($managementData);
    </script>
    @stack('scripts')
</body>
</html>
