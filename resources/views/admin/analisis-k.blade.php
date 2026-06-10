@extends('layouts.app')
@section('title', 'Analisis K Terbaik')

@section('content')
<div class="flex flex-col lg:flex-row gap-6 items-start">
    
    <!-- Main Content (Grafik & Tabel) -->
    <div class="w-full lg:w-3/4 space-y-6">
                {{-- ============================================================= --}}
                {{-- CARD 1: PREVIEW DATA --}}
                {{-- ============================================================= --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Preview Data</h2>

                    @if($loaded && $dataSiswa)
                        {{-- STATE: Data sudah di-load --}}
                        <div class="custom-scrollbar overflow-x-auto max-h-60 overflow-y-auto border border-gray-50 rounded-lg">
                            <table class="w-full text-left border-collapse min-w-[600px] text-xs">
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
                                        <th class="px-4 py-3 text-center">Tanggal Pemeriksaan</th>
                                        @if(in_array('skor_e', $activeColumns))
                                            <th class="px-2 py-3 text-center">E</th>
                                        @endif
                                        @if(in_array('skor_c', $activeColumns))
                                            <th class="px-2 py-3 text-center">C</th>
                                        @endif
                                        @if(in_array('skor_h', $activeColumns))
                                            <th class="px-2 py-3 text-center">H</th>
                                        @endif
                                        @if(in_array('skor_p', $activeColumns))
                                            <th class="px-2 py-3 text-center">P</th>
                                        @endif
                                        @if($showDiff)
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
                                        @endif
                                        @if(in_array('skor_pr', $activeColumns))
                                            <th class="px-2 py-3 text-center">Pr</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 divide-y divide-gray-50">
                                    @forelse ($dataSiswa as $data)
                                        @php
                                            $skor = $data->skorSdq->first();
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-4 py-3 font-medium text-gray-400">{{ $data->id }}</td>
                                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $data->nama_siswa ?? '-' }}</td>
                                            <td class="px-3 py-3 text-center">{{ $data->kelas ?? '-' }}</td>
                                            <td class="px-2 py-3 text-center">{{ $data->jenis_kelamin ?? '-' }}</td>
                                            <td class="px-2 py-3 text-center">{{ $data->umur }}</td>
                                            <td class="px-4 py-3 text-center">{{ $skor && $skor->tanggal_pemeriksaan ? \Carbon\Carbon::parse($skor->tanggal_pemeriksaan)->isoFormat('dddd, D MMMM YYYY') : '-' }}</td>
                                            @if(in_array('skor_e', $activeColumns))
                                                <td class="px-2 py-3 text-center">{{ $skor->skor_e ?? '-' }}</td>
                                            @endif
                                            @if(in_array('skor_c', $activeColumns))
                                                <td class="px-2 py-3 text-center">{{ $skor->skor_c ?? '-' }}</td>
                                            @endif
                                            @if(in_array('skor_h', $activeColumns))
                                                <td class="px-2 py-3 text-center">{{ $skor->skor_h ?? '-' }}</td>
                                            @endif
                                            @if(in_array('skor_p', $activeColumns))
                                                <td class="px-2 py-3 text-center">{{ $skor->skor_p ?? '-' }}</td>
                                            @endif
                                            @if($showDiff)
                                                <td class="px-3 py-3 text-center font-bold text-gray-900 bg-blue-50/20">{{ $skor->skor_diff ?? '-' }}</td>
                                            @endif
                                            @if(in_array('skor_pr', $activeColumns))
                                                <td class="px-2 py-3 text-center italic">{{ $skor->skor_pr ?? '-' }}</td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="20" class="px-4 py-10 text-center text-gray-400 italic">Tidak ada data siswa yang cocok dengan filter.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="w-full overflow-x-auto mt-4 border-t border-gray-50 pt-3 flex justify-end">
                            <div class="flex items-center">
                                {{ $dataSiswa->appends(request()->query())->links('partials.pagination') }}
                            </div>
                        </div>
                    @else
                        {{-- STATE DEFAULT: Belum ada data di-load --}}
                        <div class="w-full h-48 bg-gray-50/50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 space-y-2">
                            <i data-lucide="database" class="w-10 h-10 text-gray-300"></i>
                            <span class="text-xs font-medium text-center px-4">Silakan konfigurasi filter dan klik "Load Data Siswa" terlebih dahulu.</span>
                        </div>
                    @endif
                </div>

                {{-- ============================================================= --}}
                {{-- CARD 2: PREVIEW PREPROCESSING DATA --}}
                {{-- ============================================================= --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Preview Preprocessing Data</h2>
                        <div class="flex gap-2">
                            <button id="btn-normalisasi" class="px-3 py-1.5 bg-[#0066FF] text-white hover:bg-blue-700 font-semibold rounded-lg text-xs transition-all shadow-md {{ !$loaded ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$loaded ? 'disabled' : '' }}>Normalisasi</button>
                        </div>
                    </div>

                    @if($loaded)
                        <div class="custom-scrollbar overflow-x-auto max-h-60 overflow-y-auto border border-gray-50 rounded-lg">
                            <table class="w-full text-left border-collapse min-w-[400px] text-xs">
                                <thead id="thead-preprocessing" class="bg-gray-50/70 sticky top-0 border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3">No</th>
                                        <th class="px-4 py-3">ID Siswa</th>
                                        <th class="px-4 py-3">Nama Siswa</th>
                                        @if(in_array('skor_e', $activeColumns))
                                            <th class="px-2 py-3 text-center">E</th>
                                        @endif
                                        @if(in_array('skor_c', $activeColumns))
                                            <th class="px-2 py-3 text-center">C</th>
                                        @endif
                                        @if(in_array('skor_h', $activeColumns))
                                            <th class="px-2 py-3 text-center">H</th>
                                        @endif
                                        @if(in_array('skor_p', $activeColumns))
                                            <th class="px-2 py-3 text-center">P</th>
                                        @endif
                                        @if($showDiff)
                                            <th class="px-3 py-3 text-center text-[#0066FF] bg-blue-50/30">Diff</th>
                                        @endif
                                        @if(in_array('skor_pr', $activeColumns))
                                            <th class="px-2 py-3 text-center">Pr</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="tbody-preprocessing" class="text-gray-600 divide-y divide-gray-50 font-mono">
                                    @if($dataSiswa)
                                        @foreach ($dataSiswa as $index => $data)
                                            @php
                                                $skor = $data->skorSdq->first();
                                            @endphp
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-4 py-3 font-sans font-medium text-gray-400">{{ $dataSiswa->firstItem() + $index }}</td>
                                                <td class="px-4 py-3 font-sans font-medium text-gray-400">{{ $data->id }}</td>
                                                <td class="px-4 py-3 font-sans font-semibold text-gray-800">{{ $data->nama_siswa ?? '-' }}</td>
                                                @if(in_array('skor_e', $activeColumns))
                                                    <td class="px-2 py-3 text-center">{{ $skor->skor_e ?? 0 }}</td>
                                                @endif
                                                @if(in_array('skor_c', $activeColumns))
                                                    <td class="px-2 py-3 text-center">{{ $skor->skor_c ?? 0 }}</td>
                                                @endif
                                                @if(in_array('skor_h', $activeColumns))
                                                    <td class="px-2 py-3 text-center">{{ $skor->skor_h ?? 0 }}</td>
                                                @endif
                                                @if(in_array('skor_p', $activeColumns))
                                                    <td class="px-2 py-3 text-center">{{ $skor->skor_p ?? 0 }}</td>
                                                @endif
                                                @if($showDiff)
                                                    <td class="px-3 py-3 text-center font-bold text-gray-900 bg-blue-50/20">{{ $skor->skor_diff ?? 0 }}</td>
                                                @endif
                                                @if(in_array('skor_pr', $activeColumns))
                                                    <td class="px-2 py-3 text-center italic">{{ $skor->skor_pr ?? 0 }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- STATE DEFAULT: Belum di-load --}}
                        <div class="w-full h-36 bg-gray-50/50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 space-y-2">
                            <i data-lucide="table" class="w-8 h-8 text-gray-300"></i>
                            <span class="text-xs font-medium text-center px-4">Silakan konfigurasi filter dan klik "Load Data Siswa" terlebih dahulu.</span>
                        </div>
                    @endif
                </div>

                {{-- ============================================================= --}}
                {{-- CARD 3: METODE ELBOW --}}
                {{-- ============================================================= --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#0066FF]">Metode Elbow</h2>
                        <button id="btn-proses-elbow" class="flex items-center px-4 py-1.5 bg-[#0066FF] text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md {{ !$loaded ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$loaded ? 'disabled' : '' }}>
                            <i data-lucide="play" class="w-3.5 h-3.5 mr-1.5"></i> Proses Grafik
                        </button>
                    </div>
                    
                    <div id="chart-elbow-wrapper" class="w-full h-72 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 space-y-2">
                        <i data-lucide="line-chart" class="w-10 h-10 text-gray-300" id="elbow-placeholder-icon"></i>
                        <span class="text-xs font-medium" id="elbow-placeholder-text">Grafik Evaluasi WCSS (Elbow Method) Akan Tampil Di Sini</span>
                        <canvas id="chartElbow" style="display:none;"></canvas>
                    </div>
                </div>

                {{-- ============================================================= --}}
                {{-- CARD 4: METODE SILHOUETTE SCORE --}}
                {{-- ============================================================= --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-gray-800 font-bold text-base tracking-tight text-[#10B981]">Metode Silhouette Score</h2>
                    </div>
                    
                    <div id="chart-silhouette-wrapper" class="w-full h-72 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 space-y-2">
                        <i data-lucide="bar-chart-2" class="w-10 h-10 text-gray-300" id="silhouette-placeholder-icon"></i>
                        <span class="text-xs font-medium" id="silhouette-placeholder-text">Grafik Evaluasi Silhouette Score Akan Tampil Di Sini</span>
                        <canvas id="chartSilhouette" style="display:none;"></canvas>
                    </div>

                    <div id="rekomendasi-silhouette" class="mt-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold rounded-lg hidden">
                        <!-- Teks rekomendasi dinamis -->
                    </div>
                </div>

    </div>

    {{-- ============================================================= --}}
    {{-- SIDEBAR: FILTER DAN METRIKS (Kanan) --}}
    {{-- ============================================================= --}}
    <form action="{{ route('admin.analisis') }}" method="GET" class="w-full lg:w-1/4 bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-6">
        {{-- Hidden input penanda bahwa form sudah di-submit --}}
        <input type="hidden" name="load" value="1">

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
                    <input type="checkbox" id="cb-diff" name="cb_diff" value="1" {{ request('cb_diff', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0066FF] border-gray-300 focus:ring-blue-500">
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
                    <span class="text-xs font-semibold text-gray-500">Tanggal Pemeriksaan</span>
                    <select name="date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                        <option value="">Semua</option>
                        @foreach($daftarTanggal as $tgl)
                            <option value="{{ $tgl }}" {{ request('date') == $tgl ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($tgl)->isoFormat('D MMMM YYYY') }}</option>
                        @endforeach
                    </select>
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
@endsection

@push('scripts')
    <script>
        // Vertical line plugin to draw dashed optimal K line
        const verticalLinePlugin = {
            id: 'verticalLinePlugin',
            afterDraw: (chart) => {
                if (chart.options.plugins.verticalLinePlugin && chart.options.plugins.verticalLinePlugin.xValue) {
                    const xVal = chart.options.plugins.verticalLinePlugin.xValue;
                    const xAxis = chart.scales.x;
                    const xPixel = xAxis.getPixelForValue(xVal);
                    
                    if (xPixel === undefined || isNaN(xPixel)) return;
                    
                    const ctx = chart.ctx;
                    ctx.save();
                    ctx.beginPath();
                    ctx.setLineDash([5, 5]);
                    ctx.strokeStyle = '#EF4444'; // Red
                    ctx.lineWidth = 1.5;
                    ctx.moveTo(xPixel, chart.chartArea.top);
                    ctx.lineTo(xPixel, chart.chartArea.bottom);
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        // ===================================================================
        // CHECKBOX — Semua bebas diklik, tidak ada mutual exclusion
        // Default: E, C, H, P, Pr = checked. Diff = unchecked tapi aktif.
        // ===================================================================
        // (Tidak ada logika saling mengunci — user bebas centang apapun)

        // ===================================================================
        // HELPER: Ambil query string URL saat ini (berisi filter aktif)
        // ===================================================================
        const currentSearch = window.location.search;

        // ===================================================================
        // VARIABEL KOLOM AKTIF (dari Blade/PHP ke JavaScript)
        // ===================================================================
        const activeColumns = @json($activeColumns ?? []);

        // Mapping nama kolom DB → label singkat untuk header tabel
        const columnLabels = {
            'e_score': 'E',
            'c_score': 'C',
            'h_score': 'H',
            'p_score': 'P',
            'pro_score': 'Pr',
            'skor_kesulitan': 'Diff'
        };

        // ===================================================================
        // FETCH API: TOMBOL NORMALISASI → /api/preprocess + filter
        // ===================================================================
        const btnNormalisasi = document.getElementById('btn-normalisasi');
        const tbodyPreprocessing = document.getElementById('tbody-preprocessing');
        const theadPreprocessing = document.getElementById('thead-preprocessing');

        if (btnNormalisasi && !btnNormalisasi.disabled) {
            btnNormalisasi.addEventListener('click', async function() {
                const originalText = this.textContent;
                this.textContent = 'Memproses...';
                this.disabled = true;
                this.classList.add('opacity-60', 'cursor-not-allowed');

                try {
                    const response = await fetch('/api/preprocess' + currentSearch);
                    const result = await response.json();

                    if (result.status === 'success' && result.scaled_data) {
                        const cols = result.active_columns || activeColumns;
                        let headerHTML = '<tr><th class="px-4 py-3">No</th><th class="px-4 py-3">ID Siswa</th><th class="px-4 py-3">Nama Siswa</th>';
                        cols.forEach(col => {
                            const label = columnLabels[col] || col;
                            headerHTML += `<th class="px-2 py-3 text-center">${label} (Norm)</th>`;
                        });
                        headerHTML += '</tr>';
                        theadPreprocessing.innerHTML = headerHTML;

                        let rows = '';
                        result.scaled_data.forEach((item, index) => {
                            rows += `<tr class="hover:bg-gray-50/50 transition-colors">`;
                            rows += `<td class="px-4 py-3 font-sans font-medium text-gray-400">${index + 1}</td>`;
                            rows += `<td class="px-4 py-3 font-sans font-medium text-gray-400">${item.id}</td>`;
                            rows += `<td class="px-4 py-3 font-sans font-semibold text-gray-800">${item.nama}</td>`;
                            cols.forEach(col => {
                                const val = item[col + '_zscore'];
                                rows += `<td class="px-2 py-3 text-center">${val !== null && val !== undefined ? val.toFixed(4) : '-'}</td>`;
                            });
                            rows += `</tr>`;
                        });
                        tbodyPreprocessing.innerHTML = rows;
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Tidak dapat terhubung ke server. Pastikan Laravel dan Python API aktif.');
                } finally {
                    this.textContent = originalText;
                    this.disabled = false;
                    this.classList.remove('opacity-60', 'cursor-not-allowed');
                }
            });
        }

        // ===================================================================
        // FETCH API: TOMBOL PROSES GRAFIK → /api/elbow + filter + Chart.js
        // Gaya: Clean seperti Matplotlib / Google Colab
        // ===================================================================
        const btnElbow = document.getElementById('btn-proses-elbow');
        const chartCanvas = document.getElementById('chartElbow');
        const chartWrapper = document.getElementById('chart-elbow-wrapper');
        const placeholderIcon = document.getElementById('elbow-placeholder-icon');
        const placeholderText = document.getElementById('elbow-placeholder-text');
        window.chartElbow = null;

        const chartSilhouette = document.getElementById('chartSilhouette');
        const chartSilhouetteWrapper = document.getElementById('chart-silhouette-wrapper');
        const silhouettePlaceholderIcon = document.getElementById('silhouette-placeholder-icon');
        const silhouettePlaceholderText = document.getElementById('silhouette-placeholder-text');
        const rekomendasiSilhouette = document.getElementById('rekomendasi-silhouette');
        window.chartSilhouette = null;

        if (btnElbow && !btnElbow.disabled) {
            btnElbow.addEventListener('click', async function() {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<svg class="animate-spin w-3.5 h-3.5 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses...';
                this.disabled = true;
                this.classList.add('opacity-60', 'cursor-not-allowed');

                try {
                    const response = await fetch('/api/elbow' + currentSearch);
                    const result = await response.json();

                    if (result.status === 'success' || response.ok) {
                        const inertias = result.inertia;
                        const maxK = result.max_k || inertias.length;
                        const labels = Array.from({ length: maxK }, (_, i) => i + 1);

                        // 1. Cari K dengan Silhouette tertinggi terlebih dahulu (dari k_values_silhouette)
                        let maxScore = -1;
                        let bestK = 2;
                        if (result.silhouette) {
                            const silhouettes = result.silhouette;
                            const kValuesSil = result.k_values_silhouette || [2, 3, 4, 5, 6, 7, 8, 9, 10];
                            for (let i = 0; i < silhouettes.length; i++) {
                                if (silhouettes[i] > maxScore) {
                                    maxScore = silhouettes[i];
                                    bestK = kValuesSil[i];
                                }
                            }
                        }

                        // Sembunyikan placeholder, tampilkan canvas
                        if (placeholderIcon) placeholderIcon.style.display = 'none';
                        if (placeholderText) placeholderText.style.display = 'none';
                        chartCanvas.style.display = 'block';

                        // Ubah wrapper agar canvas bisa mengisi penuh
                        chartWrapper.classList.remove('flex', 'flex-col', 'items-center', 'justify-center', 'text-gray-400', 'space-y-2', 'border-dashed');
                        chartWrapper.classList.add('border-solid', 'p-3');

                        // Destroy chart lama jika ada (re-render) — Penanganan Destroy Chart (Glitch Prevention)
                        if (window.chartElbow) {
                            window.chartElbow.destroy();
                        }

                        // Konfigurasi Chart.js — gaya Matplotlib / Google Colab (Elbow Method)
                        const ctx = chartCanvas.getContext('2d');
                        window.chartElbow = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Within-Cluster Sum Square',
                                        data: inertias,
                                        borderColor: '#1f77b4',
                                        backgroundColor: '#1f77b4',
                                        borderWidth: 1.5,
                                        pointStyle: 'circle',
                                        pointRadius: 5,
                                        pointBackgroundColor: '#1f77b4',
                                        pointBorderColor: '#1f77b4',
                                        pointBorderWidth: 1,
                                        pointHoverRadius: 7,
                                        tension: 0,
                                        fill: false
                                    }
                                ]
                            },
                            options: {
                                animation: false,
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: false
                                    },
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: '#1F2937',
                                        titleFont: { size: 12, family: 'Inter, sans-serif' },
                                        bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                        cornerRadius: 6,
                                        padding: 10,
                                        callbacks: {
                                            title: (items) => `K = ${items[0].label}`,
                                            label: (item) => `Inertia: ${item.raw.toFixed(4)}`
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: { display: true, text: 'Number of Cluster', font: { size: 11, family: 'Inter, sans-serif' }, color: '#000000' },
                                        ticks: { font: { size: 10, family: 'Inter, sans-serif' }, color: '#000000' },
                                        border: { display: true, color: '#000000' },
                                        grid: { color: '#E5E5E5', drawOnChartArea: true }
                                    },
                                    y: {
                                        title: { display: true, text: 'Within-Cluster Sum Square', font: { size: 11, family: 'Inter, sans-serif' }, color: '#000000' },
                                        ticks: { font: { size: 10, family: 'Inter, sans-serif' }, color: '#000000' },
                                        border: { display: true, color: '#000000' },
                                        grid: { color: '#E5E5E5', drawOnChartArea: true },
                                        beginAtZero: false
                                    }
                                }
                            }
                        });

                        // ==============================================================
                        // GRAFIK 2: SILHOUETTE SCORE
                        // ==============================================================
                        if (result.silhouette) {
                            const silhouettes = result.silhouette;
                            const kValuesSil = result.k_values_silhouette || [2, 3, 4, 5, 6, 7, 8, 9, 10];

                            // Sembunyikan placeholder Silhouette
                            if (silhouettePlaceholderIcon) silhouettePlaceholderIcon.style.display = 'none';
                            if (silhouettePlaceholderText) silhouettePlaceholderText.style.display = 'none';
                            chartSilhouette.style.display = 'block';

                            chartSilhouetteWrapper.classList.remove('flex', 'flex-col', 'items-center', 'justify-center', 'text-gray-400', 'space-y-2', 'border-dashed');
                            chartSilhouetteWrapper.classList.add('border-solid', 'p-3');

                            if (window.chartSilhouette) {
                                window.chartSilhouette.destroy();
                            }

                            const ctxSilhouette = chartSilhouette.getContext('2d');
                            window.chartSilhouette = new Chart(ctxSilhouette, {
                                type: 'line',
                                data: {
                                    labels: kValuesSil,
                                    datasets: [
                                        {
                                            label: 'Silhouette Score',
                                            data: silhouettes,
                                            borderColor: '#1f77b4',
                                            backgroundColor: '#1f77b4',
                                            borderWidth: 1.5,
                                            pointStyle: 'circle',
                                            pointRadius: 5,
                                            pointBackgroundColor: '#1f77b4',
                                            pointBorderColor: '#1f77b4',
                                            pointBorderWidth: 1,
                                            pointHoverRadius: 7,
                                            tension: 0,
                                            fill: false
                                        }
                                    ]
                                },
                                options: {
                                    animation: false,
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        title: {
                                            display: false
                                        },
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            backgroundColor: '#1F2937',
                                            titleFont: { size: 12, family: 'Inter, sans-serif' },
                                            bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                            cornerRadius: 6,
                                            padding: 10,
                                            callbacks: {
                                                title: (items) => `K = ${items[0].label}`,
                                                label: (item) => `Silhouette: ${item.raw.toFixed(4)}`
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            title: { display: true, text: 'Jumlah Cluster', font: { size: 11, family: 'Inter, sans-serif' }, color: '#000000' },
                                            ticks: { font: { size: 10, family: 'Inter, sans-serif' }, color: '#000000' },
                                            border: { display: true, color: '#000000' },
                                            grid: { color: '#E5E5E5', drawOnChartArea: true }
                                        },
                                        y: {
                                            title: { display: true, text: 'Silhouette Score', font: { size: 11, family: 'Inter, sans-serif' }, color: '#000000' },
                                            ticks: { font: { size: 10, family: 'Inter, sans-serif' }, color: '#000000' },
                                            border: { display: true, color: '#000000' },
                                            grid: { color: '#E5E5E5', drawOnChartArea: true },
                                            beginAtZero: false
                                        }
                                    }
                                }
                            });

                            if (maxScore > -1) {
                                rekomendasiSilhouette.textContent = `Rekomendasi K Terbaik berdasarkan Silhouette Score adalah K=${bestK} dengan skor ${maxScore.toFixed(4)}.`;
                                rekomendasiSilhouette.classList.remove('hidden');
                            }
                        }
                    } else {
                        throw new Error(result.message || 'Terjadi kesalahan saat memproses data.');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Gagal: ' + error.message);
                } finally {
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                    this.classList.remove('opacity-60', 'cursor-not-allowed');
                    lucide.createIcons();
                }
            });
        }

    </script>
@endpush