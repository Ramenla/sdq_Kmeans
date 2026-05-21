<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Analisis K Terbaik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #F4F7FF; }
        /* Custom scrollbar untuk tabel agar tinggi card terkunci rapi */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-64 bg-white shadow-sm flex flex-col h-full border-r border-gray-100 hidden md:flex z-20">
        <div class="p-6">
            <h1 class="text-gray-800 font-bold text-xl tracking-tight">Dashboard Admin</h1>
        </div>
        <nav class="flex-1 px-4 space-y-1 mt-4 text-sm font-medium">
            <a href="#" class="block px-4 py-3 text-gray-400 hover:bg-gray-50 rounded-lg transition-colors italic">Dashboard</a>
            <a href="{{ route('dashboard.guru') }}" class="block px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-lg transition-colors">Data Siswa</a>
            <a href="{{ route('admin.analisis') }}" class="block px-4 py-3 text-[#0066FF] bg-blue-50 rounded-lg transition-colors">Analisis K Terbaik</a>
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

        <div class="flex-1 overflow-auto p-8 flex flex-col lg:flex-row gap-6">
            
            <div class="flex-1 space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Preview Data</h2>
                    <div class="custom-scrollbar overflow-x-auto max-h-60 overflow-y-auto border border-gray-50 rounded-lg">
                        <table class="w-full text-left border-collapse min-w-[800px] text-xs">
                            <thead class="bg-gray-50/70 sticky top-0 border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id_siswa', 'order' => (request('sort_by') === 'id_siswa' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 hover:text-[#0066FF] transition-colors cursor-pointer normal-case">
                                            <span>ID Siswa</span>
                                            @if(request('sort_by') === 'id_siswa')
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
                                    <th class="px-4 py-3">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nama_siswa', 'order' => (request('sort_by') === 'nama_siswa' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 hover:text-[#0066FF] transition-colors cursor-pointer normal-case">
                                            <span>Nama Siswa</span>
                                            @if(request('sort_by') === 'nama_siswa')
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
                                    <th class="px-3 py-3 text-center">Kelas</th>
                                    <th class="px-2 py-3 text-center">JK</th>
                                    <th class="px-2 py-3 text-center">Umur</th>
                                    <th class="px-4 py-3 text-center">Screening</th>
                                    <th class="px-2 py-3 text-center">E</th>
                                    <th class="px-2 py-3 text-center">C</th>
                                    <th class="px-2 py-3 text-center">H</th>
                                    <th class="px-2 py-3 text-center">P</th>
                                    <th class="px-3 py-3 text-center text-[#0066FF] bg-blue-50/30">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'diff', 'order' => (request('sort_by') === 'diff' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                            <span>Diff</span>
                                            @if(request('sort_by') === 'diff')
                                                @if(request('order') === 'desc')
                                                    <i data-lucide="arrow-down" class="w-3 h-3 text-[#0066FF]"></i>
                                                @else
                                                    <i data-lucide="arrow-up" class="w-3 h-3 text-[#0066FF]"></i>
                                                @endif
                                            @else
                                                <i data-lucide="chevrons-up-down" class="w-3 h-3 text-blue-300"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-2 py-3 text-center">Pr</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 divide-y divide-gray-50">
                                @forelse ($dataSiswa as $data)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-3 font-medium text-gray-400">{{ $data->user->nis ?? '-' }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $data->user->name ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center">{{ $data->user->kelas ?? '-' }}</td>
                                        <td class="px-2 py-3 text-center">{{ $data->user->jenis_kelamin ?? '-' }}</td>
                                        <td class="px-2 py-3 text-center">{{ $data->umur_saat_tes }}</td>
                                        <td class="px-4 py-3 text-center">{{ $data->created_at ? $data->created_at->format('d M Y') : '-' }}</td>
                                        <td class="px-2 py-3 text-center">{{ $data->e_score }}</td>
                                        <td class="px-2 py-3 text-center">{{ $data->c_score }}</td>
                                        <td class="px-2 py-3 text-center">{{ $data->h_score }}</td>
                                        <td class="px-2 py-3 text-center">{{ $data->p_score }}</td>
                                        <td class="px-3 py-3 text-center font-bold text-gray-900 bg-blue-50/20">{{ $data->skor_kesulitan }}</td>
                                        <td class="px-2 py-3 text-center italic">{{ $data->pro_score }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="px-4 py-10 text-center text-gray-400 italic">Belum ada data siswa siap proses.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-between mt-4 border-t border-gray-50 pt-3 w-full gap-4">
                        <span class="text-[11px] text-gray-400 font-medium">
                            Menampilkan {{ $dataSiswa->isEmpty() ? '0' : $dataSiswa->firstItem() }} - {{ $dataSiswa->lastItem() }} dari {{ $dataSiswa->total() }} hasil siswa siap proses
                        </span>
                        <div class="flex items-center">
                            {{ $dataSiswa->appends(request()->query())->links('partials.pagination') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Preview Preprocessing Data</h2>
                        <div class="flex gap-2">
                            <button class="px-3 py-1.5 border border-[#0066FF] text-[#0066FF] hover:bg-blue-50 font-semibold rounded-lg text-xs transition-all shadow-sm">Normalisasi</button>
                            <button class="px-3 py-1.5 bg-[#0066FF] text-white hover:bg-blue-700 font-semibold rounded-lg text-xs transition-all shadow-md">Standarisasi</button>
                        </div>
                    </div>
                    <div class="custom-scrollbar overflow-x-auto max-h-60 overflow-y-auto border border-gray-50 rounded-lg">
                        <table class="w-full text-left border-collapse min-w-[800px] text-xs">
                            <thead class="bg-gray-50/70 sticky top-0 border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">ID Siswa</th>
                                    <th class="px-4 py-3">Nama Siswa</th>
                                    <th class="px-3 py-3 text-center">Kelas</th>
                                    <th class="px-2 py-3 text-center">JK</th>
                                    <th class="px-2 py-3 text-center">Umur</th>
                                    <th class="px-2 py-3 text-center">E</th>
                                    <th class="px-2 py-3 text-center">C</th>
                                    <th class="px-2 py-3 text-center">H</th>
                                    <th class="px-2 py-3 text-center">P</th>
                                    <th class="px-3 py-3 text-center text-[#0066FF] bg-blue-50/30">Diff</th>
                                    <th class="px-2 py-3 text-center">Pr</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 divide-y divide-gray-50 font-mono">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 font-sans font-medium text-gray-400">123456</td>
                                    <td class="px-4 py-3 font-sans font-semibold text-gray-800">Baim Musyaffa</td>
                                    <td class="px-3 py-3 text-center font-sans">11 MIPA 1</td>
                                    <td class="px-2 py-3 text-center font-sans">L</td>
                                    <td class="px-2 py-3 text-center font-sans">17</td>
                                    <td class="px-2 py-3 text-center">0.85</td>
                                    <td class="px-2 py-3 text-center">0.40</td>
                                    <td class="px-2 py-3 text-center">0.70</td>
                                    <td class="px-2 py-3 text-center">0.20</td>
                                    <td class="px-3 py-3 text-center font-bold text-gray-900 bg-blue-50/20">0.91</td>
                                    <td class="px-2 py-3 text-center italic">0.90</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Metode Elbow</h2>
                        <button class="flex items-center px-4 py-1.5 bg-[#0066FF] text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md">
                            <i data-lucide="play" class="w-3.5 h-3.5 mr-1.5"></i> Proses Grafik
                        </button>
                    </div>
                    
                    <div class="w-full h-64 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 space-y-2">
                        <i data-lucide="line-chart" class="w-10 h-10 text-gray-300"></i>
                        <span class="text-xs font-medium">Grafik Evaluasi WCSS (Elbow Method) Akan Tampil Di Sini</span>
                    </div>
                </div>

            </div>

            <form action="{{ route('admin.analisis') }}" method="GET" class="w-full lg:w-80 bg-white p-6 rounded-xl border border-gray-100 shadow-sm h-fit space-y-6">
                <div>
                    <h3 class="text-gray-800 font-bold text-base tracking-tight">Filter dan Metriks</h3>
                    <p class="text-xs text-gray-400 mt-1">Konfigurasi data & pilihan variabel kuesioner SDQ.</p>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Variabel Klaster</label>
                    
                    <div class="space-y-2.5 text-sm text-gray-600 font-medium">
                        <label class="flex items-center space-x-3 cursor-pointer p-1 rounded hover:bg-gray-50">
                            <input type="checkbox" id="cb-e" name="cb_e" value="1" {{ request('cb_e', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0066FF] border-gray-300 focus:ring-blue-500">
                            <span>(E) Gejala Emosi</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer p-1 rounded hover:bg-gray-50">
                            <input type="checkbox" id="cb-c" name="cb_c" value="1" {{ request('cb_c', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0066FF] border-gray-300 focus:ring-blue-500">
                            <span>(C) Masalah Perilaku</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer p-1 rounded hover:bg-gray-50">
                            <input type="checkbox" id="cb-h" name="cb_h" value="1" {{ request('cb_h', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0066FF] border-gray-300 focus:ring-blue-500">
                            <span>(H) Hiperaktivitas</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer p-1 rounded hover:bg-gray-50">
                            <input type="checkbox" id="cb-p" name="cb_p" value="1" {{ request('cb_p', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0066FF] border-gray-300 focus:ring-blue-500">
                            <span>(P) Masalah Teman Sebaya</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer p-2 bg-blue-50/50 rounded-lg text-gray-700 border border-blue-100/30">
                            <input type="checkbox" id="cb-diff" name="cb_diff" value="1" {{ request('cb_diff') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0066FF] border-gray-300 focus:ring-blue-500">
                            <span class="font-bold text-[#0066FF]">(Diff) Total Kesulitan</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer p-1 rounded hover:bg-gray-50">
                            <input type="checkbox" id="cb-pr" name="cb_pr" value="1" {{ request('cb_pr', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0066FF] border-gray-300 focus:ring-blue-500">
                            <span>(Pr) Prososial</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-gray-50">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Batasan Ruang Lingkup</label>
                    
                    <div class="space-y-3">
                        <div class="space-y-1.5">
                            <span class="text-xs font-semibold text-gray-500">Kelas</span>
                            <select name="kelas" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                                <option value="">Semua Kelas</option>
                                @foreach($listKelas as $kls)
                                    <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-xs font-semibold text-gray-500">Screening Terakhir</span>
                            <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-xs font-semibold text-gray-500">Jenis Kelamin</span>
                            <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                                <option value="">Semua</option>
                                <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-xs font-semibold text-gray-500">Umur</span>
                            <select name="umur" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                                <option value="">Semua</option>
                                @foreach($listUmur as $umr)
                                    <option value="{{ $umr }}" {{ request('umur') == $umr ? 'selected' : '' }}>{{ $umr }} Tahun</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#0066FF] text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md flex items-center justify-center">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Load Data Siswa
                </button>
            </form>

        </div>
    </main>

    <script>
        lucide.createIcons();

        // Ambil elemen checkbox
        const cbE = document.getElementById('cb-e');
        const cbC = document.getElementById('cb-c');
        const cbH = document.getElementById('cb-h');
        const cbP = document.getElementById('cb-p');
        const cbDiff = document.getElementById('cb-diff');

        // Satukan sub-gejala ke dalam array
        const subGejala = [cbE, cbC, cbH, cbP];

        // JIKA Total Kesulitan (Diff) DI-CENTANG
        cbDiff.addEventListener('change', function() {
            if (this.checked) {
                // Matikan centang semua sub-gejala pembentuknya
                subGejala.forEach(cb => cb.checked = false);
            }
        });

        // JIKA salah satu sub-gejala (E, C, H, P) DI-CENTANG
        subGejala.forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    // Matikan centang pada Total Kesulitan otomatis
                    cbDiff.checked = false;
                }
            });
        });
    </script>
</body>
</html>