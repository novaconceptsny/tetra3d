<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('redesign/css/style.css') }}"/>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
            <!-- Add your navigation items here -->
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Verification Reminder - Shows only for unverified users -->
            @include('components.verification-reminder')
            
            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
</body>
</html> 