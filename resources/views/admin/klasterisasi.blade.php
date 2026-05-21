<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Klasterisasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #F4F7FF; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        /* Animasi halus untuk Accordion HTML asli */
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
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
            <a href="{{ route('admin.analisis') }}" class="block px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-lg transition-colors">Analisis K Terbaik</a>
            <a href="{{ route('admin.klasterisasi') }}" class="block px-4 py-3 text-[#0066FF] bg-blue-50 rounded-lg transition-colors">Klasterisasi</a>
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
                
                <details class="bg-white rounded-xl shadow-sm border border-gray-100 group">
                    <summary class="p-4 font-bold text-gray-700 cursor-pointer flex justify-between items-center hover:bg-gray-50 rounded-xl transition-colors">
                        <div class="flex items-center gap-2 text-sm text-[#0066FF]">
                            <i data-lucide="database" class="w-4 h-4"></i>
                            <span>Lihat Data Mentah & Hasil Preprocessing (Opsional)</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="p-6 border-t border-gray-100 space-y-4">
                        <p class="text-xs text-gray-500">Tabel data ini telah melalui proses standarisasi (Z-Score) sebelum dimasukkan ke dalam algoritma K-Means.</p>
                        <div class="h-32 bg-gray-50 rounded border border-dashed flex items-center justify-center text-xs text-gray-400">
                            (Tabel Preview Data & Preprocessing Ditampilkan Di Sini)
                        </div>
                    </div>
                </details>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <input type="text" placeholder="Nama Klastering (Contoh: Pemetaan Ganjil 2026)" class="w-full sm:w-64 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-700 shadow-sm">
                        <input type="number" min="2" max="10" placeholder="Jml K (ex: 3)" class="w-full sm:w-32 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-700 text-center font-bold shadow-sm">
                    </div>
                    <button class="w-full sm:w-auto px-6 py-2.5 bg-[#0066FF] text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md flex items-center justify-center">
                        <i data-lucide="cpu" class="w-4 h-4 mr-2"></i> Proses K-Means
                    </button>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Visualisasi Model & Profil Klaster</h2>
                        <span class="text-xs bg-green-100 text-green-700 font-bold px-2 py-1 rounded">Status: Berhasil Diproses (K=3)</span>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 pt-2">
                        <div class="xl:col-span-2 flex flex-col space-y-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sebaran Data (PCA 2D)</span>
                            <div class="w-full h-72 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400">
                                <i data-lucide="scatter-chart" class="w-10 h-10 text-gray-300 mb-2"></i>
                                <span class="text-xs font-medium">Scatter Plot PCA Akan Ditampilkan Di Sini</span>
                            </div>
                        </div>

                        <div class="xl:col-span-1 flex flex-col space-y-3">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sebaran Data per Klaster</span>
                            
                            <div class="p-4 rounded-lg border border-red-100 bg-red-50/30 flex items-center justify-between shadow-sm hover:shadow transition-shadow">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3.5 h-3.5 rounded-full bg-red-500 shadow-sm"></div>
                                    <h4 class="text-sm font-bold text-red-700">Klaster 1</h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-black text-gray-800">15</span>
                                    <span class="text-xs font-medium text-gray-500 ml-1">Siswa</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg border border-blue-100 bg-blue-50/30 flex items-center justify-between shadow-sm hover:shadow transition-shadow">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3.5 h-3.5 rounded-full bg-blue-500 shadow-sm"></div>
                                    <h4 class="text-sm font-bold text-blue-700">Klaster 2</h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-black text-gray-800">45</span>
                                    <span class="text-xs font-medium text-gray-500 ml-1">Siswa</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg border border-emerald-100 bg-emerald-50/30 flex items-center justify-between shadow-sm hover:shadow transition-shadow">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-sm"></div>
                                    <h4 class="text-sm font-bold text-emerald-700">Klaster 3</h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-black text-gray-800">20</span>
                                    <span class="text-xs font-medium text-gray-500 ml-1">Siswa</span>
                                </div>
                            </div>

                            <div class="mt-2 pt-3 border-t border-gray-100 flex justify-between items-center px-1">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Data</span>
                                <span class="text-sm font-black text-gray-700">80 Siswa</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight">Tabel Hasil Pemetaan Siswa</h2>
                        <select class="px-3 py-1.5 border border-gray-200 rounded text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600">
                            <option>Semua Klaster</option>
                            <option>Klaster 1</option>
                            <option>Klaster 2</option>
                            <option>Klaster 3</option>
                        </select>
                    </div>

                    <div class="custom-scrollbar overflow-x-auto max-h-[400px] overflow-y-auto">
                        <table class="w-full text-left border-collapse min-w-[900px] text-xs">
                            <thead class="bg-gray-50/70 sticky top-0 border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">ID Siswa</th>
                                    <th class="px-4 py-3">Nama Siswa</th>
                                    <th class="px-3 py-3 text-center">Kelas</th>
                                    <th class="px-2 py-3 text-center">E</th>
                                    <th class="px-2 py-3 text-center">C</th>
                                    <th class="px-2 py-3 text-center">H</th>
                                    <th class="px-2 py-3 text-center">P</th>
                                    <th class="px-3 py-3 text-center text-[#0066FF]">Diff</th>
                                    <th class="px-2 py-3 text-center">Pr</th>
                                    <th class="px-4 py-3 text-center bg-gray-100 text-gray-700">Hasil Klaster</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 divide-y divide-gray-50">
                                
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-400">123456</td>
                                    <td class="px-4 py-3 font-semibold text-gray-800">Baim Musyaffa</td>
                                    <td class="px-3 py-3 text-center">11 MIPA 1</td>
                                    <td class="px-2 py-3 text-center">8</td>
                                    <td class="px-2 py-3 text-center">4</td>
                                    <td class="px-2 py-3 text-center">7</td>
                                    <td class="px-2 py-3 text-center">2</td>
                                    <td class="px-3 py-3 text-center font-bold text-gray-900">21</td>
                                    <td class="px-2 py-3 text-center italic">9</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 font-bold rounded text-[10px] tracking-wide border border-red-200">KLASTER 1</span>
                                    </td>
                                </tr>

                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-400">123457</td>
                                    <td class="px-4 py-3 font-semibold text-gray-800">Siti Rahma</td>
                                    <td class="px-3 py-3 text-center">11 MIPA 1</td>
                                    <td class="px-2 py-3 text-center">3</td>
                                    <td class="px-2 py-3 text-center">2</td>
                                    <td class="px-2 py-3 text-center">4</td>
                                    <td class="px-2 py-3 text-center">2</td>
                                    <td class="px-3 py-3 text-center font-bold text-gray-900">11</td>
                                    <td class="px-2 py-3 text-center italic">8</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 font-bold rounded text-[10px] tracking-wide border border-blue-200">KLASTER 2</span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                        <button class="px-5 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                            <i data-lucide="printer" class="w-4 h-4 inline mr-1"></i> Cetak PDF
                        </button>
                        <button class="px-6 py-2 bg-[#0066FF] text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md">
                            <i data-lucide="save" class="w-4 h-4 inline mr-1"></i> Simpan Hasil Klastering
                        </button>
                    </div>
                </div>

            </div>

            <div class="w-full lg:w-80 bg-white p-6 rounded-xl border border-gray-100 shadow-sm h-fit space-y-6">
                <div>
                    <h3 class="text-gray-800 font-bold text-base tracking-tight">Filter dan Metriks</h3>
                    <p class="text-xs text-gray-400 mt-1">Data dikunci berdasarkan hasil analisis sebelumnya.</p>
                </div>
                
                <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-lg text-sm text-blue-800 font-medium">
                    Variabel terpilih: <br><span class="font-bold">E, C, H, P, Pr</span>
                </div>

                <div class="space-y-1.5">
                    <span class="text-xs font-semibold text-gray-500">Kelas</span>
                    <input type="text" value="11 MIPA 1" disabled class="w-full px-3 py-2 border border-gray-100 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                </div>
            </div>

        </div>
    </main>

    <script> lucide.createIcons(); </script>
</body>
</html>