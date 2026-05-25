<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0 bg-white border-r border-gray-200 flex flex-col">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100">
        <span class="text-xl font-bold text-gray-800 tracking-tight text-[#0066FF]">Sistem SDQ</span>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-400 hover:bg-gray-50 italic">
            <span>Dashboard</span>
        </a>
        <a href="{{ route('dashboard.guru') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard.guru') ? 'bg-blue-50 text-[#0066FF]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Data Siswa</span>
        </a>
        <a href="{{ route('admin.analisis') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analisis') ? 'bg-blue-50 text-[#0066FF]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Analisis K-Terbaik</span>
        </a>
        <a href="{{ route('admin.klasterisasi') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.klasterisasi') ? 'bg-blue-50 text-[#0066FF]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Klasterisasi K-Means</span>
        </a>
        <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.laporan') ? 'bg-blue-50 text-[#0066FF]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Laporan Hasil</span>
        </a>
    </nav>
</aside>