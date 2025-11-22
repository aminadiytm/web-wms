<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Document</title>
</head>
<body>
    @include('components.sidebar')
        <div class="flex-1 flex flex-col min-h-screen">

        <!-- Header  -->
        
        @include('components.navbar')

        <!-- Content -->
        <main class="p-6">
            @yield('content')
        </main>

    </div>
</body>
</html>