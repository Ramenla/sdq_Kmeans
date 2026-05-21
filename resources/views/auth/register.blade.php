<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SDQ App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F4F7FF] min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white p-8 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">Daftar dulu yu</h2>

        @if ($errors->any())
            <div class="mb-5 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/register" method="POST" class="space-y-4">
            @csrf <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama" class="w-full px-4 py-3 bg-[#F0F4FA] text-blue-900 placeholder-blue-300 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition-all" required>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="w-full px-4 py-3 bg-[#F0F4FA] text-blue-900 placeholder-blue-300 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition-all" required>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 bg-[#F0F4FA] text-blue-900 placeholder-blue-300 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition-all" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full px-4 py-3 bg-[#F0F4FA] text-blue-900 placeholder-blue-300 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition-all" required>
            </div>

            <button type="submit" class="w-full bg-[#0066FF] hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition duration-200 mt-4 shadow-sm hover:shadow-md">
                Daftar
            </button>
        </form>

        <p class="text-center text-xs text-gray-600 mt-6 font-semibold">
            Sudah punya akun? <a href="{{ route('login.siswa') }}" class="text-[#0066FF] hover:underline">Masuk</a>
        </p>
    </div>

</body>
</html>