<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin')</title>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <script src="{{ asset('js/lucide.js') }}"></script>
    <script src="{{ asset('js/chart.js') }}"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #F4F7FF; }
        .table-container::-webkit-scrollbar, .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .table-container::-webkit-scrollbar-thumb, .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        /* ===== MINIMALIST FORM STYLES ===== */
        .nb-input, .nb-select {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: #f9fafb;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            outline: none;
            width: 100%;
            color: #374151;
        }
        .nb-input:focus, .nb-select:focus {
            border-color: #3b82f6;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .nb-input::placeholder {
            color: #9ca3af;
        }
        .nb-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
            padding-right: 2.75rem;
        }
        .nb-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.375rem;
            display: block;
        }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            max-width: 540px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(20px);
            transition: transform 0.2s ease;
        }
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 100;
            border: 1px solid #f3f4f6;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            max-width: 400px;
            background: #fff;
            animation: slideInToast 0.3s ease forwards;
        }
        @keyframes slideInToast {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutToast {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(120%); opacity: 0; }
        }

        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        
        @stack('styles')
    </style>
</head>
<body class="bg-[#F4F7FF] font-sans text-gray-900">

    {{-- ===== TOAST NOTIFICATIONS ===== --}}
    @if(session('success'))
        <div id="toast-success" class="toast-notification bg-green-400 text-black">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div id="toast-error" class="toast-notification bg-red-400 text-white">
            <div class="flex items-start gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 shrink-0"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div id="toast-error-session" class="toast-notification bg-red-500 text-white">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Overlay untuk Sidebar di Mobile --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-30 hidden lg:hidden transition-opacity opacity-0 duration-300"></div>

    @include('layouts.sidebar')

    <div class="lg:ml-64 transition-all duration-300 flex flex-col min-h-screen" id="main-content">
        <script>
            // Mencegah animasi penutupan sidebar saat halaman dimuat (FOUC)
            if (window.innerWidth >= 1024 && localStorage.getItem('sidebarClosed') === 'true') {
                var sb = document.getElementById('logo-sidebar');
                var mc = document.getElementById('main-content');
                if (sb) {
                    sb.style.transition = 'none';
                    sb.classList.remove('lg:translate-x-0');
                }
                if (mc) {
                    mc.style.transition = 'none';
                    mc.classList.remove('lg:ml-64');
                }
            }
        </script>
        @include('layouts.header')
        
        <main class="flex-1 p-6 @yield('main_class')">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();

        // Toast auto-dismiss globally if they exist
        ['toast-success', 'toast-error'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                setTimeout(function() {
                    el.style.animation = 'slideOutToast 0.3s ease forwards';
                    setTimeout(function() { el.remove(); }, 300);
                }, 4000);
            }
        });

        // Sidebar Toggle logic
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('logo-sidebar');
            const mainContent = document.getElementById('main-content');
            const overlay = document.getElementById('sidebar-overlay');
            
            // Kembalikan transisi CSS setelah render awal selesai
            setTimeout(() => {
                if (sidebar) sidebar.style.transition = '';
                if (mainContent) mainContent.style.transition = '';
            }, 50);

            function toggleMobileSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                if (overlay) {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        overlay.classList.remove('opacity-100');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    } else {
                        overlay.classList.remove('hidden');
                        setTimeout(() => overlay.classList.add('opacity-100'), 10);
                    }
                }
            }

            if (overlay) {
                overlay.addEventListener('click', toggleMobileSidebar);
            }

            if (sidebar && toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const currentIsDesktop = window.innerWidth >= 1024;
                    
                    if (currentIsDesktop) {
                        // Toggle untuk Desktop
                        sidebar.classList.toggle('lg:translate-x-0');
                        if (mainContent) mainContent.classList.toggle('lg:ml-64');
                        
                        // Save state to localStorage
                        const sidebarClosed = !sidebar.classList.contains('lg:translate-x-0');
                        localStorage.setItem('sidebarClosed', sidebarClosed);
                    } else {
                        // Toggle untuk Mobile
                        toggleMobileSidebar();
                    }
                });
            }

            // Fix bug ketika di-resize (memastikan sidebar tidak nyangkut menutupi header)
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    // Kembalikan class -translate-x-full agar state mobile tidak bocor ke desktop
                    if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                    }
                    if (overlay) {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('opacity-100');
                    }
                }
            });
        });

        // ==========================================
        // GLOBAL URL STATE PERSISTENCE
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const currentSearch = window.location.search;
            const storageKey = 'last_url_' + currentPath;

            // Jangan terapkan pada halaman login atau root jika tidak diinginkan
            if(currentPath.includes('login') || currentPath === '/') return;

            // Jika URL memiliki parameter, simpan state tersebut
            if (currentSearch) {
                sessionStorage.setItem(storageKey, currentSearch);
            }

            // Ganti URL pada link sidebar secara langsung, sehingga saat diklik 
            // browser tidak perlu memuat halaman kosong lalu redirect (mencegah kedipan/reload 2 kali).
            const sidebarLinks = document.querySelectorAll('aside nav a');
            sidebarLinks.forEach(link => {
                try {
                    const url = new URL(link.href);
                    if (url.origin === window.location.origin) {
                        const savedQuery = sessionStorage.getItem('last_url_' + url.pathname);
                        if (savedQuery && savedQuery !== '?') {
                            link.href = url.pathname + savedQuery;
                        }
                    }
                } catch (e) {
                    // Abaikan jika href bukan URL valid
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
