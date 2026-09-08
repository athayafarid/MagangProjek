<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - PT Besmindo Materi Sawatama Inventory</title>
    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md space-y-6">
        <!-- Logo Branding Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-emerald-600 shadow-xl shadow-emerald-600/30 border border-emerald-400/40">
                <img src="{{ asset('images/logo.png') }}" alt="PT Besmindo Materi Sawatama" class="max-h-12 w-auto object-contain filter drop-shadow">
            </div>
            <h1 class="text-xl font-extrabold text-white tracking-wide">PT Besmindo Materi Sawatama</h1>
            <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Inventory Management System</p>
        </div>

        @yield('content')
    </div>

</body>
</html>
