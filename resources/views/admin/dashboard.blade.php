@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Bagian Atas: 4 Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Kartu 1: Total Siswa --}}
        <div class="bg-white rounded-xl border-t-8 border-t-gray-400 p-6 flex flex-col justify-center shadow-sm">
            <h3 class="text-gray-800 font-bold text-base mb-2">Total Siswa</h3>
            <p class="text-4xl font-extrabold text-gray-900 text-center mt-2 mb-4">{{ number_format($totalSiswa) }}</p>
        </div>

        {{-- Kartu 2: Normal --}}
        <div class="bg-white rounded-xl border-t-8 border-t-[#00C49A] p-6 flex flex-col justify-center shadow-sm">
            <h3 class="text-gray-800 font-bold text-base mb-2">Normal</h3>
            <p class="text-4xl font-extrabold text-gray-900 text-center mt-2 mb-4">{{ number_format($globalNormalCount) }}</p>
        </div>

        {{-- Kartu 3: Borderline --}}
        <div class="bg-white rounded-xl border-t-8 border-t-[#FFD166] p-6 flex flex-col justify-center shadow-sm">
            <h3 class="text-gray-800 font-bold text-base mb-2">Borderline</h3>
            <p class="text-4xl font-extrabold text-gray-900 text-center mt-2 mb-4">{{ number_format($globalBorderlineCount) }}</p>
        </div>

        {{-- Kartu 4: Abnormal --}}
        <div class="bg-white rounded-xl border-t-8 border-t-[#EF476F] p-6 flex flex-col justify-center shadow-sm">
            <h3 class="text-gray-800 font-bold text-base mb-2">Abnormal</h3>
            <p class="text-4xl font-extrabold text-gray-900 text-center mt-2 mb-4">{{ number_format($globalAbnormalCount) }}</p>
        </div>
    </div>

    {{-- Bagian Tengah: Grafik Analisis (Flat Design) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kolom Kiri: Distribusi Status Mental --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col min-h-[350px]">
            <div class="flex items-center justify-between mb-6 shrink-0">
                <h2 class="text-lg font-bold text-gray-900">Distribusi Status Mental</h2>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="m-0">
                    <input type="hidden" name="jk_gejala" value="{{ $jkGejala }}">
                    <select name="jk_mental" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-700 py-1 pl-3 pr-8 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-medium cursor-pointer appearance-none bg-no-repeat bg-[right_0.5rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 20 20\' fill=\'%236B7280\'><path fill-rule=\'evenodd\' d=\'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z\' clip-rule=\'evenodd\'/></svg>');">
                        <option value="Semua" {{ $jkMental === 'Semua' ? 'selected' : '' }}>Semua</option>
                        <option value="L" {{ $jkMental === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $jkMental === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </form>
            </div>
            <div class="flex-grow flex flex-col md:flex-row items-center justify-center w-full gap-6">
                {{-- Donut Chart Container --}}
                <div class="relative w-full md:w-1/2 flex justify-center" style="height: 240px;">
                    <canvas id="mentalStatusChart"></canvas>
                </div>
                
                {{-- Custom Legend Container --}}
                <div class="w-full md:w-1/2 flex flex-col justify-center">
                    {{-- Legend 1: Normal --}}
                    <div class="p-3 rounded-lg border flex items-center justify-between shadow-sm hover:shadow transition-shadow mb-3"
                         style="border-color: rgba(0, 196, 154, 0.3); background: rgba(0, 196, 154, 0.08);">
                        <div class="flex items-center space-x-3">
                            <div class="w-3.5 h-3.5 rounded-full shadow-sm" style="background: #00C49A;"></div>
                            <h4 class="text-sm font-bold" style="color: #00896b;">Normal</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-gray-800">{{ $normalCount }}</span>
                            <span class="text-[10px] font-medium text-gray-500 ml-1">Siswa</span>
                        </div>
                    </div>

                    {{-- Legend 2: Borderline --}}
                    <div class="p-3 rounded-lg border flex items-center justify-between shadow-sm hover:shadow transition-shadow mb-3"
                         style="border-color: rgba(255, 209, 102, 0.4); background: rgba(255, 209, 102, 0.15);">
                        <div class="flex items-center space-x-3">
                            <div class="w-3.5 h-3.5 rounded-full shadow-sm" style="background: #FFD166;"></div>
                            <h4 class="text-sm font-bold" style="color: #cc9f20;">Borderline</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-gray-800">{{ $borderlineCount }}</span>
                            <span class="text-[10px] font-medium text-gray-500 ml-1">Siswa</span>
                        </div>
                    </div>

                    {{-- Legend 3: Abnormal --}}
                    <div class="p-3 rounded-lg border flex items-center justify-between shadow-sm hover:shadow transition-shadow"
                         style="border-color: rgba(239, 71, 111, 0.3); background: rgba(239, 71, 111, 0.08);">
                        <div class="flex items-center space-x-3">
                            <div class="w-3.5 h-3.5 rounded-full shadow-sm" style="background: #EF476F;"></div>
                            <h4 class="text-sm font-bold" style="color: #c03959;">Abnormal</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-gray-800">{{ $abnormalCount }}</span>
                            <span class="text-[10px] font-medium text-gray-500 ml-1">Siswa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Rata-Rata Gejala Tertinggi --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col min-h-[350px]">
            <div class="flex items-center justify-between mb-6 shrink-0">
                <h2 class="text-lg font-bold text-gray-900">Rata-Rata Gejala Tertinggi</h2>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="m-0">
                    <input type="hidden" name="jk_mental" value="{{ $jkMental }}">
                    <select name="jk_gejala" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-700 py-1 pl-3 pr-8 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-medium cursor-pointer appearance-none bg-no-repeat bg-[right_0.5rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 20 20\' fill=\'%236B7280\'><path fill-rule=\'evenodd\' d=\'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z\' clip-rule=\'evenodd\'/></svg>');">
                        <option value="Semua" {{ $jkGejala === 'Semua' ? 'selected' : '' }}>Semua</option>
                        <option value="L" {{ $jkGejala === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $jkGejala === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </form>
            </div>
            <div class="flex-grow flex items-center justify-center w-full">
                <div class="relative w-full" style="height: 260px;">
                    <canvas id="gejalaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian Bawah: Tabel Prioritas Penanganan --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Prioritas Penanganan</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                        <th class="px-4 py-3 text-center rounded-l-lg">Nama Siswa</th>
                        <th class="px-4 py-3 text-center">Kelas</th>
                        <th class="px-4 py-3 text-center">Skor Kesulitan</th>
                        <th class="px-4 py-3 text-center">Gejala Tertinggi</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center rounded-r-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-gray-600 divide-y divide-gray-50">
                    @forelse ($prioritas as $skor)
                        <tr class="hover:bg-blue-50/20 transition-colors">
                            <td class="px-4 py-3 text-center font-bold text-gray-800">{{ $skor->siswa->nama_siswa ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $skor->siswa->kelas ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-bold text-gray-900 bg-blue-50/30">{{ $skor->skor_diff }}</td>
                            <td class="px-4 py-3 text-center italic text-gray-500">{{ $skor->gejala_tertinggi }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-sm {{ $skor->kategori == 'Abnormal' ? 'text-[#EF476F]' : ($skor->kategori == 'Borderline' ? 'text-[#FFD166]' : 'text-[#00C49A]') }}">
                                {{ $skor->kategori }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" 
                                    data-detail="{{ json_encode([
                                        'nama' => $skor->siswa->nama_siswa ?? '-',
                                        'kelas' => $skor->siswa->kelas ?? '-',
                                        'umur' => $skor->siswa->umur ? $skor->siswa->umur . ' Tahun' : '-',
                                        'jk' => $skor->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($skor->siswa->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
                                        'hp' => $skor->siswa->no_hp ?? '-',
                                        'email' => $skor->siswa->email ?? '-',
                                        'skor_e' => $skor->skor_e, 'kategori_e' => $skor->kategori_e,
                                        'skor_c' => $skor->skor_c, 'kategori_c' => $skor->kategori_c,
                                        'skor_h' => $skor->skor_h, 'kategori_h' => $skor->kategori_h,
                                        'skor_p' => $skor->skor_p, 'kategori_p' => $skor->kategori_p,
                                        'skor_pr' => $skor->skor_pr, 'kategori_pr' => $skor->kategori_pr,
                                        'tanggal' => \Carbon\Carbon::parse($skor->tanggal_pemeriksaan)->isoFormat('D MMMM YYYY'),
                                        'total' => $skor->skor_diff,
                                        'kategori' => $skor->kategori
                                    ]) }}"
                                    onclick="openDetailModal(this)"
                                    class="text-[#0066FF] hover:underline font-semibold focus:outline-none">Lihat Detail</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== MODAL: DETAIL SISWA (FLAT DESIGN) ===== --}}
<x-modal-detail-siswa />

@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }

    function getStatusColor(status) {
        let base = 'w-1/3 text-right text-xs font-bold uppercase ';
        if (status === 'Abnormal') return base + 'text-red-600';
        if (status === 'Borderline') return base + 'text-yellow-600';
        return base + 'text-blue-600';
    }

    function getStatusColorTable(status) {
        if (status === 'Abnormal') return 'text-red-600';
        if (status === 'Borderline') return 'text-yellow-600';
        return 'text-blue-600';
    }

    function openDetailModal(btn) {
        let data = JSON.parse(btn.getAttribute('data-detail'));
        
        // Biodata (no colon prefixes because we added colons in HTML grid)
        document.getElementById('md-nama').innerText = data.nama;
        document.getElementById('md-kelas').innerText = data.kelas;
        document.getElementById('md-umur').innerText = data.umur;
        document.getElementById('md-jk').innerText = data.jk;
        document.getElementById('md-hp').innerText = data.hp;
        document.getElementById('md-email').innerText = data.email;
        
        // Skor Terakhir & Status
        document.getElementById('md-e').innerText = data.skor_e;
        document.getElementById('md-lbl-e').innerText = data.kategori_e;
        document.getElementById('md-lbl-e').className = getStatusColor(data.kategori_e);

        document.getElementById('md-c').innerText = data.skor_c;
        document.getElementById('md-lbl-c').innerText = data.kategori_c;
        document.getElementById('md-lbl-c').className = getStatusColor(data.kategori_c);

        document.getElementById('md-h').innerText = data.skor_h;
        document.getElementById('md-lbl-h').innerText = data.kategori_h;
        document.getElementById('md-lbl-h').className = getStatusColor(data.kategori_h);

        document.getElementById('md-p').innerText = data.skor_p;
        document.getElementById('md-lbl-p').innerText = data.kategori_p;
        document.getElementById('md-lbl-p').className = getStatusColor(data.kategori_p);

        document.getElementById('md-pr').innerText = data.skor_pr;
        document.getElementById('md-lbl-pr').innerText = data.kategori_pr;
        document.getElementById('md-lbl-pr').className = getStatusColor(data.kategori_pr);

        // Tabel Riwayat (Just 1 row for now since dashboard only gives top record)
        let tbody = document.getElementById('md-history-body');
        tbody.innerHTML = `
            <tr class="border-b border-gray-100">
                <td class="px-4 py-3 text-gray-700">${data.tanggal}</td>
                <td class="px-2 py-3 text-center text-gray-600">${data.skor_e}</td>
                <td class="px-2 py-3 text-center text-gray-600">${data.skor_c}</td>
                <td class="px-2 py-3 text-center text-gray-600">${data.skor_h}</td>
                <td class="px-2 py-3 text-center text-gray-600">${data.skor_p}</td>
                <td class="px-2 py-3 text-center text-gray-600">${data.skor_pr}</td>
                <td class="px-4 py-3 text-center font-bold text-gray-900">${data.total}</td>
                <td class="px-4 py-3 text-right font-bold ${getStatusColorTable(data.kategori)}">${data.kategori}</td>
            </tr>
        `;

        openModal('modal-detail');
    }

    // ========== INISIALISASI GRAFIK ==========
    document.addEventListener('DOMContentLoaded', function() {
        // Plugin untuk teks di tengah donut chart
        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw: function(chart) {
                if (chart.config.type !== 'doughnut') return;
                const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                ctx.save();
                
                const centerX = left + width / 2;
                const centerY = top + height / 2;
                
                // Angka Total
                ctx.font = "bold 2rem 'Inter', sans-serif";
                ctx.fillStyle = "#111827"; // text-gray-900
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText("{{ number_format($chartTotalSiswa) }}", centerX, centerY - 8);
                
                // Label "TOTAL SISWA"
                ctx.font = "600 0.65rem 'Inter', sans-serif";
                ctx.fillStyle = "#6B7280"; // text-gray-500
                ctx.fillText("TOTAL SISWA", centerX, centerY + 18);
                
                ctx.restore();
            }
        };

        // 1. Donut Chart (Distribusi Status Mental)
        const ctxMental = document.getElementById('mentalStatusChart').getContext('2d');
        new Chart(ctxMental, {
            type: 'doughnut',
            plugins: [centerTextPlugin],
            data: {
                labels: [
                    'Normal ({{ number_format($normalCount) }})', 
                    'Borderline ({{ number_format($borderlineCount) }})', 
                    'Abnormal ({{ number_format($abnormalCount) }})'
                ],
                datasets: [{
                    data: [{{ $normalCount }}, {{ $borderlineCount }}, {{ $abnormalCount }}],
                    backgroundColor: [
                        '#00C49A', // Normal - Hijau
                        '#FFD166', // Borderline - Kuning
                        '#EF476F'  // Abnormal - Merah
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                layout: {
                    padding: { left: 0, right: 0, top: 10, bottom: 10 }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1F2937',
                        padding: 12,
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 13 },
                        cornerRadius: 8,
                        displayColors: true
                    }
                }
            }
        });

        // 2. Horizontal Bar Chart (Rata-Rata Gejala Tertinggi)
        const ctxGejala = document.getElementById('gejalaChart').getContext('2d');
        new Chart(ctxGejala, {
            type: 'bar',
            data: {
                labels: ['Emosional', 'Perilaku', 'Hiperaktivitas', 'Teman Sebaya', 'Prososial'],
                datasets: [{
                    label: 'Rata-rata Skor',
                    data: [{{ $avgE }}, {{ $avgC }}, {{ $avgH }}, {{ $avgP }}, {{ $avgPr }}],
                    backgroundColor: '#60A5FA', // Biru solid lembut (Tailwind blue-400)
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y', // Membuatnya horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1F2937',
                        padding: 12,
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 13, weight: 'bold' },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#F3F4F6', drawBorder: false },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 11 },
                            color: '#6B7280',
                            stepSize: 2
                        },
                        beginAtZero: true
                    },
                    y: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                            color: '#4B5563'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
