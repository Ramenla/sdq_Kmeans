<header class="sticky top-0 h-16 bg-white z-20 flex items-center justify-between px-4 sm:px-8 shrink-0 w-full shadow-sm">
    <div class="flex items-center">
        <button id="sidebarToggle" class="p-2 rounded-lg text-gray-800 hover:bg-gray-100 transition-colors focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="flex flex-col text-right hidden sm:flex">
                <span class="text-sm font-bold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</span>
            </div>
            <div class="w-8 h-8 rounded-full bg-[#0066FF] text-white flex items-center justify-center text-sm font-semibold shadow-sm">
                <i data-lucide="user" class="w-4 h-4"></i>
            </div>
        </div>

        <div class="h-6 w-px bg-gray-200"></div>

        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-gray-500 hover:text-red-600 transition-colors text-sm font-medium focus:outline-none bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Keluar</span>
            </button>
        </form>
    </div>
</header>