@extends('layouts.app')
@section('title', 'Klasterisasi K-Means')

@section('content')
<div class="flex flex-col lg:flex-row gap-6 items-start">
    
    <!-- Main Content (Grafik & Tabel) -->
    <div class="flex-1 w-full lg:w-3/4 space-y-6">
                {{-- ============================================================= --}}
                {{-- ACCORDION: Data Mentah & Preprocessing --}}
                {{-- ============================================================= --}}
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

                {{-- ============================================================= --}}
                {{-- FORM: Input K & Nama Klasterisasi --}}
                {{-- ============================================================= --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <input type="text" id="input-nama-klastering" placeholder="Nama Klastering (Contoh: Pemetaan Ganjil 2026)" class="w-full sm:w-64 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-700 shadow-sm">
                        <input type="number" id="input-jumlah-k" min="2" max="10" value="3" placeholder="Jml K (ex: 3)" class="w-full sm:w-32 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-700 text-center font-bold shadow-sm">
                    </div>
                    <button id="btn-proses-kmeans" class="w-full sm:w-auto px-5 py-2 bg-[#0066FF] text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md flex items-center justify-center">
                        Proses K-Means
                    </button>
                </div>

                {{-- ============================================================= --}}
                {{-- VISUALISASI: Scatter Plot + Sebaran Klaster --}}
                {{-- ============================================================= --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Visualisasi Model & Profil Klaster</h2>
                        <span id="status-badge" class="text-xs bg-gray-100 text-gray-500 font-bold px-2 py-1 rounded">Status: Menunggu Proses</span>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 pt-2">
                        <div class="xl:col-span-2 flex flex-col space-y-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sebaran Data (PCA 2D)</span>
                            <div id="scatter-wrapper" class="w-full h-72 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400">
                                <i data-lucide="scatter-chart" class="w-10 h-10 text-gray-300 mb-2" id="scatter-placeholder-icon"></i>
                                <span class="text-xs font-medium" id="scatter-placeholder-text">Scatter Plot PCA Akan Ditampilkan Di Sini</span>
                                <canvas id="chartScatter" style="display:none;"></canvas>
                            </div>
                        </div>

                        <div class="xl:col-span-1 flex flex-col space-y-3">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sebaran Data per Klaster</span>
                            
                            <div id="cluster-cards-container">
                                {{-- Cards akan di-render dinamis oleh JavaScript --}}
                                <div class="w-full h-40 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 space-y-2">
                                    <i data-lucide="pie-chart" class="w-8 h-8 text-gray-300"></i>
                                    <span class="text-xs font-medium">Sebaran klaster muncul setelah proses.</span>
                                </div>
                            </div>

                            <div id="total-data-container" class="mt-2 pt-3 border-t border-gray-100 flex justify-between items-center px-1" style="display:none;">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Data</span>
                                <span id="total-data-value" class="text-sm font-black text-gray-700">0 Siswa</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================================= --}}
                {{-- TABEL PROFIL KARAKTERISTIK KLASTER (Rata-Rata SDQ) --}}
                {{-- ============================================================= --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 bg-gray-50/30">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight">Profil Karakteristik Klaster (Rata-Rata Skor SDQ)</h2>
                        <p class="text-xs text-gray-400 mt-1">Rata-rata skor mentah SDQ untuk setiap klaster hasil K-Means.</p>
                    </div>
                    <div class="custom-scrollbar overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[600px] text-xs">
                            <thead class="bg-gray-50/70 sticky top-0 border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Klaster</th>
                                    <th class="px-3 py-3 text-center">Rata-rata E</th>
                                    <th class="px-3 py-3 text-center">Rata-rata C</th>
                                    <th class="px-3 py-3 text-center">Rata-rata H</th>
                                    <th class="px-3 py-3 text-center">Rata-rata P</th>
                                    <th class="px-3 py-3 text-center">Rata-rata Pr</th>
                                    <th class="px-3 py-3 text-center text-[#0066FF]">Rata-rata Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-profil-klaster" class="text-gray-600 divide-y divide-gray-50">
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400 italic">Data profil klaster akan muncul setelah proses K-Means.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ============================================================= --}}
                {{-- TABEL HASIL PEMETAAN SISWA --}}
                {{-- ============================================================= --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight">Tabel Hasil Pemetaan Siswa</h2>
                        <select id="filter-klaster-tabel" class="px-3 py-1.5 border border-gray-200 rounded text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600">
                            <option value="all">Semua Klaster</option>
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
                            <tbody id="tbody-hasil-klaster" class="text-gray-600 divide-y divide-gray-50">
                                <tr>
                                    <td colspan="10" class="px-4 py-10 text-center text-gray-400 italic">Klik tombol "Proses K-Means" untuk memulai klasterisasi.</td>
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

    {{-- ============================================================= --}}
    {{-- SIDEBAR: Informasi Filter (Kanan) --}}
    {{-- ============================================================= --}}
    <div class="w-full lg:w-1/4 bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-6 sticky top-20">
        <div>
            <h3 class="text-gray-800 font-bold text-base tracking-tight">Filter dan Metriks</h3>
            <p class="text-xs text-gray-400 mt-1">Data dikunci berdasarkan hasil analisis sebelumnya.</p>
        </div>
        
        <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-lg text-sm text-blue-800 font-medium">
            Variabel terpilih: <br><span class="font-bold">E, C, H, P, Pr</span>
        </div>

        <div class="space-y-3">
            <div class="space-y-1.5">
                <span class="text-xs font-semibold text-gray-500">Inertia (WCSS)</span>
                <div id="info-inertia" class="px-3 py-2 border border-gray-100 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono">-</div>
            </div>
            <div class="space-y-1.5">
                <span class="text-xs font-semibold text-gray-500">Iterasi Konvergensi</span>
                <div id="info-n-iter" class="px-3 py-2 border border-gray-100 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono">-</div>
            </div>
            <div class="space-y-1.5">
                <span class="text-xs font-semibold text-gray-500">PCA Explained Variance</span>
                <div id="info-pca-var" class="px-3 py-2 border border-gray-100 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono">-</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>

        // ===================================================================
        // PALET WARNA KLASTER — Hingga 10 klaster
        // ===================================================================
        const CLUSTER_COLORS = [
            { bg: '#EF4444', bgLight: 'rgba(239,68,68,0.15)', border: '#FCA5A5', text: '#B91C1C', label: 'red' },
            { bg: '#3B82F6', bgLight: 'rgba(59,130,246,0.15)', border: '#93C5FD', text: '#1D4ED8', label: 'blue' },
            { bg: '#10B981', bgLight: 'rgba(16,185,129,0.15)', border: '#6EE7B7', text: '#047857', label: 'emerald' },
            { bg: '#F59E0B', bgLight: 'rgba(245,158,11,0.15)', border: '#FCD34D', text: '#B45309', label: 'amber' },
            { bg: '#8B5CF6', bgLight: 'rgba(139,92,246,0.15)', border: '#C4B5FD', text: '#6D28D9', label: 'violet' },
            { bg: '#EC4899', bgLight: 'rgba(236,72,153,0.15)', border: '#F9A8D4', text: '#BE185D', label: 'pink' },
            { bg: '#14B8A6', bgLight: 'rgba(20,184,166,0.15)', border: '#5EEAD4', text: '#0F766E', label: 'teal' },
            { bg: '#F97316', bgLight: 'rgba(249,115,22,0.15)', border: '#FDBA74', text: '#C2410C', label: 'orange' },
            { bg: '#6366F1', bgLight: 'rgba(99,102,241,0.15)', border: '#A5B4FC', text: '#4338CA', label: 'indigo' },
            { bg: '#06B6D4', bgLight: 'rgba(6,182,212,0.15)', border: '#67E8F9', text: '#0E7490', label: 'cyan' },
        ];

        // ===================================================================
        // REFERENSI ELEMEN DOM
        // ===================================================================
        const btnProses         = document.getElementById('btn-proses-kmeans');
        const inputK            = document.getElementById('input-jumlah-k');
        const inputNama         = document.getElementById('input-nama-klastering');
        const statusBadge       = document.getElementById('status-badge');
        const scatterWrapper    = document.getElementById('scatter-wrapper');
        const scatterCanvas     = document.getElementById('chartScatter');
        const placeholderIcon   = document.getElementById('scatter-placeholder-icon');
        const placeholderText   = document.getElementById('scatter-placeholder-text');
        const clusterCardsDiv   = document.getElementById('cluster-cards-container');
        const totalDataDiv      = document.getElementById('total-data-container');
        const totalDataValue    = document.getElementById('total-data-value');
        const tbodyHasil        = document.getElementById('tbody-hasil-klaster');
        const filterKlaster     = document.getElementById('filter-klaster-tabel');
        const infoInertia       = document.getElementById('info-inertia');
        const infoNIter         = document.getElementById('info-n-iter');
        const infoPcaVar        = document.getElementById('info-pca-var');

        let scatterChart = null;
        let allHasilData = []; // Simpan semua data hasil untuk filter tabel

        // ===================================================================
        // FETCH API: TOMBOL PROSES K-MEANS
        // ===================================================================
        btnProses.addEventListener('click', async function() {
            const jumlahK = parseInt(inputK.value);
            if (!jumlahK || jumlahK < 2 || jumlahK > 10) {
                alert('Masukkan nilai K antara 2 sampai 10.');
                return;
            }

            const originalHTML = this.innerHTML;
            this.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses...';
            this.disabled = true;
            this.classList.add('opacity-60', 'cursor-not-allowed');

            statusBadge.textContent = 'Status: Memproses...';
            statusBadge.className = 'text-xs bg-yellow-100 text-yellow-700 font-bold px-2 py-1 rounded';

            try {
                const response = await fetch('/api/klasterisasi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        jumlah_k: jumlahK,
                        nama_klastering: inputNama.value || null,
                    }),
                });

                if (!response.ok) {
                    const errData = await response.json();
                    throw new Error(errData.message || 'Terjadi kesalahan di server Laravel.');
                }

                const result = await response.json();

                if (result.status === 'success') {
                    allHasilData = result.data_siswa || [];
                    const K = result.jumlah_klaster;

                    // Update status badge
                    statusBadge.textContent = `Status: Berhasil Diproses (K=${K})`;
                    statusBadge.className = 'text-xs bg-green-100 text-green-700 font-bold px-2 py-1 rounded';

                    // Update sidebar info
                    infoInertia.textContent = result.inertia !== null ? result.inertia.toFixed(4) : '-';
                    infoNIter.textContent = result.n_iter !== null ? result.n_iter + ' iterasi' : '-';
                    if (result.pca_explained_variance && result.pca_explained_variance.length >= 2) {
                        const pc1 = (result.pca_explained_variance[0] * 100).toFixed(1);
                        const pc2 = (result.pca_explained_variance[1] * 100).toFixed(1);
                        infoPcaVar.textContent = `PC1: ${pc1}% | PC2: ${pc2}%`;
                    }

                    // --- (A) SCATTER PLOT ---
                    renderScatterPlot(allHasilData, K);

                    // --- (B) CARDS SEBARAN ---
                    renderClusterCards(result.cluster_counts, result.total_data, K);

                    // --- (C) TABEL HASIL ---
                    renderTable(allHasilData, K);

                    // --- (D) TABEL PROFIL KLASTER ---
                    renderProfilKlaster(result.cluster_profiles, K);
                    updateFilterDropdown(K);

                } else {
                    statusBadge.textContent = 'Status: Gagal';
                    statusBadge.className = 'text-xs bg-red-100 text-red-700 font-bold px-2 py-1 rounded';
                    alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
                }
            } catch (error) {
                console.error('Fetch error:', error);
                statusBadge.textContent = 'Status: Error';
                statusBadge.className = 'text-xs bg-red-100 text-red-700 font-bold px-2 py-1 rounded';
                alert('Error: ' + error.message);
            } finally {
                this.innerHTML = originalHTML;
                this.disabled = false;
                this.classList.remove('opacity-60', 'cursor-not-allowed');
                lucide.createIcons();
            }
        });

        // ===================================================================
        // (A) RENDER SCATTER PLOT — Chart.js tipe 'scatter'
        // ===================================================================
        function renderScatterPlot(data, K) {
            // Sembunyikan placeholder
            if (placeholderIcon) placeholderIcon.style.display = 'none';
            if (placeholderText) placeholderText.style.display = 'none';
            scatterCanvas.style.display = 'block';

            scatterWrapper.classList.remove('flex', 'flex-col', 'items-center', 'justify-center', 'text-gray-400', 'border-dashed');
            scatterWrapper.classList.add('border-solid', 'p-2');

            if (scatterChart) {
                scatterChart.destroy();
                scatterChart = null;
            }

            // Kelompokkan data per klaster
            const datasets = [];
            for (let c = 0; c < K; c++) {
                const clusterData = data
                    .filter(item => item.cluster_number === c)
                    .map(item => ({ x: item.pca_x, y: item.pca_y }));

                const color = CLUSTER_COLORS[c % CLUSTER_COLORS.length];
                datasets.push({
                    label: `Klaster ${c + 1}`,
                    data: clusterData,
                    backgroundColor: color.bg,
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                });
            }

            const ctx = scatterCanvas.getContext('2d');
            scatterChart = new Chart(ctx, {
                type: 'scatter',
                data: { datasets },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: `Scatter Plot PCA — K-Means (K=${K})`,
                            font: { size: 13, weight: '700', family: 'Inter, sans-serif' },
                            color: '#1F2937',
                            padding: { bottom: 12 }
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11, family: 'Inter, sans-serif' },
                                padding: 16,
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            titleFont: { size: 11, family: 'Inter, sans-serif' },
                            bodyFont: { size: 11, family: 'Inter, sans-serif' },
                            cornerRadius: 6,
                            padding: 8,
                            callbacks: {
                                label: (ctx) => `PC1: ${ctx.raw.x.toFixed(3)}, PC2: ${ctx.raw.y.toFixed(3)}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Principal Component 1',
                                font: { size: 11, weight: '600', family: 'Inter, sans-serif' },
                                color: '#6B7280'
                            },
                            ticks: { font: { size: 10 }, color: '#9CA3AF' },
                            grid: { color: '#F3F4F6', borderColor: '#D1D5DB' }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Principal Component 2',
                                font: { size: 11, weight: '600', family: 'Inter, sans-serif' },
                                color: '#6B7280'
                            },
                            ticks: { font: { size: 10 }, color: '#9CA3AF' },
                            grid: { color: '#F3F4F6', borderColor: '#D1D5DB' }
                        }
                    }
                }
            });
        }

        // ===================================================================
        // (B) RENDER CARDS SEBARAN KLASTER
        // ===================================================================
        function renderClusterCards(counts, totalData, K) {
            let html = '';
            for (let c = 0; c < K; c++) {
                const count = counts[c] || 0;
                const color = CLUSTER_COLORS[c % CLUSTER_COLORS.length];
                html += `
                    <div class="p-4 rounded-lg border flex items-center justify-between shadow-sm hover:shadow transition-shadow mb-3"
                         style="border-color: ${color.border}; background: ${color.bgLight};">
                        <div class="flex items-center space-x-3">
                            <div class="w-3.5 h-3.5 rounded-full shadow-sm" style="background: ${color.bg};"></div>
                            <h4 class="text-sm font-bold" style="color: ${color.text};">Klaster ${c + 1}</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-gray-800">${count}</span>
                            <span class="text-xs font-medium text-gray-500 ml-1">Siswa</span>
                        </div>
                    </div>
                `;
            }
            clusterCardsDiv.innerHTML = html;

            totalDataDiv.style.display = 'flex';
            totalDataValue.textContent = `${totalData} Siswa`;
        }

        // ===================================================================
        // (C) RENDER TABEL HASIL PEMETAAN
        // ===================================================================
        function renderTable(data, K) {
            let rows = '';
            data.forEach(item => {
                const clusterIdx = item.cluster_number;
                const color = CLUSTER_COLORS[clusterIdx % CLUSTER_COLORS.length];
                rows += `
                    <tr class="hover:bg-gray-50/50 transition-colors" data-cluster="${clusterIdx}">
                        <td class="px-4 py-3 font-medium text-gray-400">${item.nis}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">${item.nama}</td>
                        <td class="px-3 py-3 text-center">${item.kelas}</td>
                        <td class="px-2 py-3 text-center">${item.e_score}</td>
                        <td class="px-2 py-3 text-center">${item.c_score}</td>
                        <td class="px-2 py-3 text-center">${item.h_score}</td>
                        <td class="px-2 py-3 text-center">${item.p_score}</td>
                        <td class="px-3 py-3 text-center font-bold text-gray-900">${item.skor_kesulitan}</td>
                        <td class="px-2 py-3 text-center italic">${item.pro_score}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-3 py-1 font-bold rounded text-[10px] tracking-wide border"
                                  style="background: ${color.bgLight}; color: ${color.text}; border-color: ${color.border};">
                                KLASTER ${clusterIdx + 1}
                            </span>
                        </td>
                    </tr>
                `;
            });
            tbodyHasil.innerHTML = rows;
        }

        // ===================================================================
        // DROPDOWN FILTER KLASTER TABEL
        // ===================================================================
        function updateFilterDropdown(K) {
            let opts = '<option value="all">Semua Klaster</option>';
            for (let c = 0; c < K; c++) {
                opts += `<option value="${c}">Klaster ${c + 1}</option>`;
            }
            filterKlaster.innerHTML = opts;
        }

        filterKlaster.addEventListener('change', function() {
            const val = this.value;
            const rows = tbodyHasil.querySelectorAll('tr[data-cluster]');
            rows.forEach(row => {
                if (val === 'all' || row.dataset.cluster === val) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // ===================================================================
        // (D) RENDER TABEL PROFIL KARAKTERISTIK KLASTER
        // ===================================================================
        function renderProfilKlaster(profiles, K) {
            const tbody = document.getElementById('tbody-profil-klaster');
            if (!profiles || profiles.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada data profil klaster.</td></tr>';
                return;
            }

            // Mapping kolom yang mungkin ada dari Python
            const colMap = [
                { key: 'e_score',        label: 'E' },
                { key: 'c_score',        label: 'C' },
                { key: 'h_score',        label: 'H' },
                { key: 'p_score',        label: 'P' },
                { key: 'pro_score',      label: 'Pr' },
                { key: 'skor_kesulitan', label: 'Diff' },
            ];

            let rows = '';
            profiles.forEach(profile => {
                const c = profile.cluster_label;
                const color = CLUSTER_COLORS[c % CLUSTER_COLORS.length];

                rows += '<tr class="hover:bg-gray-50/50 transition-colors">';

                // Kolom Klaster — dengan badge warna
                rows += `<td class="px-4 py-3">`;
                rows += `<span class="inline-flex items-center gap-2 px-3 py-1 font-bold rounded text-[11px] tracking-wide border"
                               style="background: ${color.bgLight}; color: ${color.text}; border-color: ${color.border};">`;
                rows += `<span class="w-2.5 h-2.5 rounded-full inline-block" style="background: ${color.bg};"></span>`;
                rows += `Klaster ${c + 1}`;
                rows += `</span></td>`;

                // Kolom Rata-rata SDQ
                colMap.forEach(col => {
                    const val = profile[col.key];
                    const isDiff = col.key === 'skor_kesulitan';
                    const extraClass = isDiff ? ' font-bold text-gray-900' : '';
                    rows += `<td class="px-3 py-3 text-center font-mono${extraClass}">`;
                    rows += val !== undefined && val !== null ? val.toFixed(2) : '-';
                    rows += '</td>';
                });

                rows += '</tr>';
            });

            tbody.innerHTML = rows;
        }

    </script>
@endpush