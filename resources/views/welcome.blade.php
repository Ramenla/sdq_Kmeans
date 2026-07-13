<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran - SDQ App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F4F7FF] min-h-screen flex flex-col items-center justify-center p-4">

    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
        <p class="text-sm text-gray-600 font-medium">Pilih peran anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-3xl">
        
        <a href="{{ route('login.guru') }}" class="group bg-white p-10 rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-transparent hover:border-blue-100 transition-all duration-300 flex flex-col items-center text-center">
            <div class="text-[#0066FF] mb-5 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path fill="#fff" d="M12 11.998h5.5c-.328 3.513-2.915 6.55-5.5 7.742V12H6.5V6.735l5.5-2.062v7.325z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900 mb-3">Admin/Guru</h2>
            <p class="text-xs text-gray-500 leading-relaxed max-w-[200px]">
                Akses dashboard manajemen dan analisis untuk memantau siswa secara berkala
            </p>
        </a>

        <a href="{{ route('kuesioner.form') }}" class="group bg-white p-10 rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-transparent hover:border-indigo-100 transition-all duration-300 flex flex-col items-center text-center">
            <div class="text-[#0066FF] mb-5 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900 mb-3">Siswa</h2>
            <p class="text-xs text-gray-500 leading-relaxed max-w-[200px]">
                Mulai screening &amp; pantau kesehatan mental anda dengan alat evaluasi mandiri yang mudah digunakan
            </p>
        </a>

    </div>

</body>
</html>