<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuisioner SDQ - {{ $siswa->nama_siswa }}</title>
    <meta name="description" content="Hasil screening kuisioner SDQ untuk {{ $siswa->nama_siswa }}.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up {
            opacity: 0;
            animation: fadeUp 0.6s ease forwards;
        }
        .fade-up-1 { animation-delay: 0.1s; }
        .fade-up-2 { animation-delay: 0.2s; }
        .fade-up-3 { animation-delay: 0.3s; }
        .fade-up-4 { animation-delay: 0.4s; }

        /* Donut ring animation */
        .donut-ring {
            transition: stroke-dashoffset 1.2s cubic-bezier(0.22,1,0.36,1);
        }

        /* Score bar animation */
        .score-bar-fill {
            transition: width 1s cubic-bezier(0.22,1,0.36,1);
        }
    </style>
</head>
<body class="bg-[#F4F7FE] min-h-screen">

    @php
        $kategoriLower = strtolower($skor->kategori ?? 'normal');

        // Card background colors per category
        $catBgClass = match($kategoriLower) {
            'normal'     => 'bg-gradient-to-r from-emerald-500 to-emerald-600',
            'borderline' => 'bg-gradient-to-r from-amber-400 to-amber-500',
            'abnormal'   => 'bg-gradient-to-r from-red-500 to-red-600',
            default      => 'bg-gradient-to-r from-emerald-500 to-emerald-600',
        };

        // SVG ring color (white on colored card)
        $ringColor = '#ffffff';
        $ringBgColor = 'rgba(255,255,255,0.2)';

        // Score percentage for donut (out of 40)
        $scorePct = min(round(($skor->skor_diff / 40) * 100), 100);
        $circumference = 2 * M_PI * 50; // ~314.16
        $dashoffset = $circumference * (1 - ($skor->skor_diff / 40));

        // Explanation text
        $explanation = match($kategoriLower) {
            'normal'     => 'Skor kamu berada dalam rentang normal. Terus jaga kesehatanmu ya!',
            'borderline' => 'Skor kamu berada di area borderline. Disarankan untuk evaluasi lanjutan.',
            'abnormal'   => 'Skor kamu menunjukkan indikasi adanya kesulitan. Sangat disarankan untuk rujukan klinis ke fasilitas kesehatan/psikolog.',
            default      => '',
        };
    @endphp

    <div class="max-w-2xl mx-auto px-4 py-8 md:py-12">

        {{-- Header --}}
        <div class="text-center mb-8 fade-up">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                Terimakasih, <span class="text-[#0066FF]">{{ $siswa->nama_siswa }}</span>
            </h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed max-w-md mx-auto">
                Kuesioner berhasil diselesaikan. Berikut ringkasan hasil screening SDQ kamu.
            </p>
        </div>

        {{-- ============================================================ --}}
        {{--  KARTU ATAS: TOTAL SKOR KESULITAN                             --}}
        {{-- ============================================================ --}}
        <div class="rounded-xl overflow-hidden shadow-sm mb-6 fade-up fade-up-1">
            {{-- Colored top section --}}
            <div class="{{ $catBgClass }} p-6 md:p-8">
                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8">

                    {{-- Donut Chart --}}
                    <div class="flex-shrink-0">
                        <svg width="140" height="140" viewBox="0 0 120 120">
                            {{-- Background ring --}}
                            <circle cx="60" cy="60" r="50" fill="none"
                                stroke="{{ $ringBgColor }}" stroke-width="10"/>
                            {{-- Progress ring --}}
                            <circle cx="60" cy="60" r="50" fill="none"
                                stroke="{{ $ringColor }}" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $circumference }}"
                                transform="rotate(-90 60 60)"
                                class="donut-ring"
                                id="donutRing"
                                data-target="{{ $dashoffset }}"/>
                            {{-- Center text --}}
                            <text x="60" y="54" text-anchor="middle" fill="white" font-size="30" font-weight="800" id="donutNumber">0</text>
                            <text x="60" y="72" text-anchor="middle" fill="rgba(255,255,255,0.7)" font-size="11" font-weight="500">dari 40</text>
                        </svg>
                    </div>

                    {{-- Score Info --}}
                    <div class="text-center sm:text-left">
                        <p class="text-sm font-medium text-white/80 mb-1">Total Skor Kesulitan</p>
                        <div class="flex items-center gap-3 justify-center sm:justify-start flex-wrap">
                            <span class="text-4xl md:text-5xl font-black text-white">{{ $skor->skor_diff }}</span>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                                {{ $skor->kategori }}
                            </span>
                        </div>
                        <p class="text-xs text-white/70 mt-3 leading-relaxed max-w-xs">
                            {{ $explanation }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- White footer --}}
            <div class="bg-white px-6 md:px-8 py-3.5 flex items-center justify-between text-xs text-gray-500">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ \Carbon\Carbon::parse($skor->tanggal_pemeriksaan)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ $siswa->kelas }} &bull; {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $siswa->umur }} tahun</span>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{--  KARTU BAWAH: DETAIL SKOR PER SUBSKALA                        --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 mb-6 fade-up fade-up-2">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">Detail Skor per Subskala</h2>
                <p class="text-sm text-gray-400 mt-0.5">Breakdown skor per area gejala</p>
            </div>

            @php
                $subscales = [
                    ['label' => 'Emosional',               'desc' => 'Gejala emosi: kecemasan, ketakutan, kesedihan',         'skor' => $skor->skor_e,  'max' => 10],
                    ['label' => 'Perilaku (Conduct)',       'desc' => 'Masalah perilaku: marah, berkelahi, curang',            'skor' => $skor->skor_c,  'max' => 10],
                    ['label' => 'Hiperaktivitas',           'desc' => 'Hiperaktivitas dan kurang konsentrasi',                 'skor' => $skor->skor_h,  'max' => 10],
                    ['label' => 'Teman Sebaya (Peer)',      'desc' => 'Hubungan dengan teman sebaya',                          'skor' => $skor->skor_p,  'max' => 10],
                    ['label' => 'Prososial',                'desc' => 'Perilaku peduli, berbagi, menolong',                    'skor' => $skor->skor_pr, 'max' => 10],
                ];
            @endphp

            <div class="space-y-6">
                @foreach ($subscales as $idx => $sub)
                    @php
                        $barPct = round(($sub['skor'] / $sub['max']) * 100);
                    @endphp
                    <div class="fade-up" style="animation-delay: {{ 0.3 + ($idx * 0.08) }}s">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-sm font-bold text-gray-800">{{ $sub['label'] }}</span>
                                <p class="text-[11px] text-gray-400 leading-tight mt-0.5">{{ $sub['desc'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="score-bar-fill h-full bg-[#0066FF] rounded-full" style="width: 0%" data-target="{{ $barPct }}"></div>
                            </div>
                            <span class="text-sm font-bold text-[#0066FF] w-6 text-right">{{ $sub['skor'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ============================================================ --}}
        {{--  NAVIGASI AKHIR                                               --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 fade-up fade-up-4">
            <a href="{{ route('kuesioner.form') }}"
                class="w-full sm:w-auto text-center px-6 py-3 bg-white text-[#0066FF] font-semibold text-sm rounded-lg hover:bg-blue-50 transition-colors">
                Mulai Kuisioner
            </a>
            <a href="{{ route('landing') }}"
                class="w-full sm:w-auto text-center px-6 py-3 bg-[#0066FF] hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition-colors">
                Kembali Ke Beranda
            </a>
        </div>

        <div class="text-center mt-8">
            <p class="text-[10px] text-gray-400">SDQ &copy; Robert Goodman</p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        const targetScore = {{ $skor->skor_diff }};

        // Animate donut ring
        const ring = document.getElementById('donutRing');
        if (ring) {
            setTimeout(() => {
                ring.style.strokeDashoffset = ring.dataset.target;
            }, 400);
        }

        // Animate donut number
        const numberEl = document.getElementById('donutNumber');
        if (numberEl && targetScore > 0) {
            const duration = 1000;
            const start = performance.now();
            function animate(now) {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                numberEl.textContent = Math.round(eased * targetScore);
                if (progress < 1) requestAnimationFrame(animate);
            }
            setTimeout(() => requestAnimationFrame(animate), 500);
        }

        // Animate score bars
        setTimeout(() => {
            document.querySelectorAll('.score-bar-fill').forEach((bar, idx) => {
                setTimeout(() => {
                    bar.style.width = bar.dataset.target + '%';
                }, idx * 100);
            });
        }, 600);

    });
    </script>
</body>
</html>
