<!DOCTYPE html>
<html>
<head>
    <title></title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Si usas Vite --}}
    <link href="{{ asset('css/subscription-stats.css') }}" rel="stylesheet">
</head>
<body>
    <div class="filament-app-layout"> {{-- Contenedor principal de Filament --}}
        <div class="filament-main-content"> {{-- Contenido principal --}}
            <div class="container mx-auto px-4 py-8"> {{-- Contenedor opcional para centrar el contenido --}}
                @yield('content')
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>