<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0 bg-white flex flex-col">
    <div class="flex items-center h-16 px-6">
        <span class="text-lg font-bold tracking-tight text-[#0066FF]">Dashboard Admin</span>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors duration-200 text-gray-500 hover:bg-gray-50">
            <span>Dashboard</span>
        </a>
        <a href="{{ route('dashboard.guru') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard.guru') ? 'bg-[#D9EAFD] text-[#0066FF]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Data Siswa</span>
        </a>
        <a href="{{ route('admin.analisis') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.analisis') ? 'bg-[#D9EAFD] text-[#0066FF]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Analisis K Terbaik</span>
        </a>
        <a href="{{ route('admin.klasterisasi') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.klasterisasi') ? 'bg-[#D9EAFD] text-[#0066FF]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Klasterisasi</span>
        </a>
        <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.laporan') ? 'bg-[#D9EAFD] text-[#0066FF]' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            <span>Laporan Hasil</span>
        </a>
    </nav>
</aside>