<header class="h-16 bg-white border-b border-gray-200 shadow-sm z-20 flex items-center justify-between px-4 sm:px-6 shrink-0">
    <div class="flex items-center gap-4">
        <button id="sidebarToggle" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-lg font-bold text-gray-800 tracking-tight">@yield('title', 'Halaman Utama')</h1>
    </div>

    <div class="flex items-center gap-3">
        <div class="flex flex-col text-right hidden sm:flex">
            <span class="text-sm font-semibold text-gray-700">Fauzan</span>
            <span class="text-xs text-gray-400 font-medium">Guru BK</span>
        </div>
        <div class="w-9 h-9 rounded-full bg-[#0066FF] text-white flex items-center justify-center text-sm font-semibold shadow-sm">
            <i data-lucide="user" class="w-5 h-5"></i>
        </div>
    </div>
</header>