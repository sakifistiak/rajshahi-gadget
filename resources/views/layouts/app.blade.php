<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Khan Gadget Admin') }}</title>

        <!-- Site & Admin Favicon -->
        <link rel="icon" href="/favicon.png" type="image/png"/>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>

        <!-- Fonts (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Lucide Icons Library -->
        <script src="https://unpkg.com/lucide@latest"></script>
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            /* Custom scrollbar to look extremely neat */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full bg-[#f8fafc] text-slate-800 flex flex-col overflow-hidden">
        
        <!-- Top Navbar (Full Width) -->
        @include('layouts.navigation')

        <!-- Container below Top Navbar -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Left Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Scroll Area -->
            <main class="flex-1 overflow-y-auto p-5 md:p-6 bg-[#f1f5f9]/70">
                <div class="space-y-6">
                    @if (isset($header))
                        <header class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm">
                            {{ $header }}
                        </header>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        </script>
    </body>
</html>
