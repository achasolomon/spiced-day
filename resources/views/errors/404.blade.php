<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .error-card {
            animation: fadeIn 0.35s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-950 min-h-screen flex items-center justify-center px-6">

    <div class="w-full max-w-2xl">

        <div class="error-card bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-8 md:p-10 text-center">

            <!-- Error Image -->
            <div class="flex justify-center mb-6">
                <img
                    src="{{ asset('assets/images/404_page_cover.jpg') }}"
                    alt="Page Not Found"
                    class="max-h-[320px] w-auto object-contain"
                >
            </div>

            <!-- Minimal text -->
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Page not found
            </p>

            <!-- Actions -->
            <div class="flex justify-center gap-3 flex-wrap">

                @php
                    $user = auth()->user();
                    $dashboardRoute = $user ? match($user->user_type) {
                        'applicant' => route('applicant.dashboard'),
                        'consultant' => route('consultant.dashboard'),
                        'admin' => route('admin.dashboard'),
                        default => route('dashboard'),
                    } : route('home');
                @endphp
                <a href="{{ $dashboardRoute }}"
                   class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition">
                    {{ $user ? 'Dashboard' : 'Home' }}
                </a>

                <button onclick="history.back()"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                    Go Back
                </button>

            </div>

            <!-- Error code -->
            <div class="mt-6 text-xs text-gray-400 dark:text-gray-600">
                Error 404
            </div>

        </div>

    </div>

</body>
</html>
