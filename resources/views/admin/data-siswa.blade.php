<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Data Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #F4F7FF; }
        .table-container::-webkit-scrollbar { height: 6px; }
        .table-container::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-64 bg-white shadow-sm flex flex-col h-full border-r border-gray-100 hidden md:flex z-20">
        <div class="p-6">
            <h1 class="text-gray-800 font-bold text-xl tracking-tight">Dashboard Admin</h1>
        </div>
        <nav class="flex-1 px-4 space-y-1 mt-4 text-sm font-medium">
            <a href="#" class="block px-4 py-3 text-gray-400 hover:bg-gray-50 rounded-lg transition-colors italic">Dashboard</a>
            <a href="{{ route('dashboard.guru') }}" class="block px-4 py-3 text-[#0066FF] bg-blue-50 rounded-lg transition-colors">Data Siswa</a>
            <a href="{{ route('admin.analisis') }}" class="block px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-lg transition-colors">Analisis K Terbaik</a>
            <a href="{{ route('admin.klasterisasi') }}" class="block px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-lg transition-colors">Klasterisasi</a>
            <a href="{{ route('admin.laporan') }}" class="block px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-lg transition-colors">Laporan Hasil</a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden">
        
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10">
            <button class="text-gray-600 hover:text-gray-900">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="flex items-center space-x-3">
                <span class="text-sm font-semibold text-gray-700">Fauzan (Guru BK)</span>
                <div class="w-9 h-9 rounded-full bg-[#0066FF] text-white flex items-center justify-center shadow-sm">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-auto p-8">
            
            <form action="{{ route('dashboard.guru') }}" method="GET" class="space-y-4 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex shadow-sm rounded-lg overflow-hidden border border-gray-200 bg-white">
                        <div class="pl-3 flex items-center">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/NIS Siswa..." class="px-3 py-2 text-sm focus:outline-none w-48 lg:w-64 text-gray-700">
                        <button type="submit" class="bg-[#0066FF] text-white px-5 py-2 text-sm font-semibold hover:bg-blue-700 transition-colors">Cari</button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" class="flex items-center px-4 py-2 bg-white border border-[#0066FF] text-[#0066FF] text-sm font-semibold rounded-lg hover:bg-blue-50 transition-all shadow-sm">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i> Import data
                        </button>
                        <button type="button" class="flex items-center px-4 py-2 bg-[#0066FF] text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Data
                        </button>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Filter Kelas -->
                        <select name="kelas" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                            <option value="">Kelas</option>
                            @foreach($listKelas as $kls)
                                <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                            @endforeach
                        </select>

                        <!-- Filter Screening Terakhir (Exact Date Match) -->
                        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm" title="Screening Terakhir">

                        <!-- Filter Jenis Kelamin -->
                        <select name="jenis_kelamin" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                            <option value="">Jenis Kelamin</option>
                            <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>

                        <!-- Filter Umur -->
                        <select name="umur" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                            <option value="">Umur</option>
                            @foreach($listUmur as $umr)
                                <option value="{{ $umr }}" {{ request('umur') == $umr ? 'selected' : '' }}>{{ $umr }} Tahun</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dropdown Jumlah Data (per_page) di pojok kanan atas tabel -->
                    <div class="flex items-center gap-2 self-end md:self-auto">
                        <label for="per_page" class="text-xs font-semibold text-gray-500">Tampilkan:</label>
                        <select id="per_page" name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                            <option value="10" {{ request('per_page') == '10' || !request('per_page') ? 'selected' : '' }}>10</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="table-container overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1100px]">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] text-gray-400 font-bold uppercase tracking-widest">
                                <th class="px-6 py-4">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id_siswa', 'order' => (request('sort_by') === 'id_siswa' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 hover:text-[#0066FF] transition-colors cursor-pointer normal-case">
                                        <span>ID Siswa</span>
                                        @if(request('sort_by') === 'id_siswa')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nama_siswa', 'order' => (request('sort_by') === 'nama_siswa' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 hover:text-[#0066FF] transition-colors cursor-pointer normal-case">
                                        <span>Nama Siswa</span>
                                        @if(request('sort_by') === 'nama_siswa')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-4 text-center">Kelas</th>
                                <th class="px-4 py-4 text-center">Jenis Kelamin</th>
                                <th class="px-4 py-4 text-center">Umur</th>
                                <th class="px-6 py-4 text-center">Screening Terakhir</th>
                                <th class="px-3 py-4 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'e_score', 'order' => (request('sort_by') === 'e_score' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>E</span>
                                        @if(request('sort_by') === 'e_score')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3 h-3 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3 h-3 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3 h-3 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-3 py-4 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'c_score', 'order' => (request('sort_by') === 'c_score' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>C</span>
                                        @if(request('sort_by') === 'c_score')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3 h-3 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3 h-3 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3 h-3 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-3 py-4 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'h_score', 'order' => (request('sort_by') === 'h_score' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>H</span>
                                        @if(request('sort_by') === 'h_score')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3 h-3 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3 h-3 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3 h-3 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-3 py-4 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'p_score', 'order' => (request('sort_by') === 'p_score' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>P</span>
                                        @if(request('sort_by') === 'p_score')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3 h-3 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3 h-3 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3 h-3 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-4 text-center text-[#0066FF] bg-blue-50/50">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'diff', 'order' => (request('sort_by') === 'diff' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>Total Kesulitan</span>
                                        @if(request('sort_by') === 'diff')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-blue-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-3 py-4 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'pro_score', 'order' => (request('sort_by') === 'pro_score' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>Pr</span>
                                        @if(request('sort_by') === 'pro_score')
                                            @if(request('order') === 'desc')
                                                <i data-lucide="arrow-down" class="w-3 h-3 text-[#0066FF]"></i>
                                            @else
                                                <i data-lucide="arrow-up" class="w-3 h-3 text-[#0066FF]"></i>
                                            @endif
                                        @else
                                            <i data-lucide="chevrons-up-down" class="w-3 h-3 text-gray-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-600 divide-y divide-gray-50">
                            @forelse ($dataSiswa as $data)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-400">{{ $data->user->nis ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $data->user->name ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $data->user->kelas ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center font-medium text-gray-700">{{ $data->user->jenis_kelamin ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $data->umur_saat_tes }}</td>
                                    <td class="px-6 py-4 text-center">{{ $data->created_at ? $data->created_at->format('d M Y') : '-' }}</td>
                                    <td class="px-3 py-4 text-center">{{ $data->e_score }}</td>
                                    <td class="px-3 py-4 text-center">{{ $data->c_score }}</td>
                                    <td class="px-3 py-4 text-center">{{ $data->h_score }}</td>
                                    <td class="px-3 py-4 text-center">{{ $data->p_score }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-gray-900 bg-blue-50/30 text-base">{{ $data->skor_kesulitan }}</td>
                                    <td class="px-3 py-4 text-center italic">{{ $data->pro_score }}</td>
                                    <td class="px-6 py-4 flex justify-center space-x-2">
                                        <button class="p-1.5 text-blue-500 hover:bg-blue-100 rounded-md transition-colors shadow-sm bg-white border border-gray-100" title="Detail Siswa"><i data-lucide="eye" class="w-4 h-4"></i></button>
                                        <button class="p-1.5 text-amber-500 hover:bg-amber-100 rounded-md transition-colors shadow-sm bg-white border border-gray-100" title="Edit Data"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                                        <button class="p-1.5 text-red-500 hover:bg-red-100 rounded-md transition-colors shadow-sm bg-white border border-gray-100" title="Hapus Data"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data kuesioner siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-8 py-5 border-t border-gray-50 flex items-center justify-between bg-white w-full gap-4">
                    <span class="text-xs text-gray-400 font-medium tracking-wide">
                        Menampilkan {{ $dataSiswa->isEmpty() ? '0' : $dataSiswa->firstItem() }} - {{ $dataSiswa->lastItem() }} dari {{ $dataSiswa->total() }} hasil
                    </span>
                    <div class="flex items-center">
                        {{ $dataSiswa->appends(request()->query())->links('partials.pagination') }}
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script> lucide.createIcons(); </script>
</body>
</html>