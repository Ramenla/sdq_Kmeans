<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kuisioner SDQ - Screening Kesehatan Mental</title>
    <meta name="description" content="Kuisioner Strengths and Difficulties Questionnaire (SDQ) untuk screening kesehatan mental siswa.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Step Panels */
        .step-panel { display: none; }
        .step-panel.active { display: block; animation: fadeIn 0.4s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Question transition */
        .q-card {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Answer card hover */
        .answer-card {
            transition: all 0.2s ease;
        }
        .answer-card:hover {
            transform: scale(1.04);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        /* Error shake */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .shake { animation: shake 0.35s ease; }

        /* Spinner */
        .spinner {
            border: 3px solid #E5E7EB;
            border-top-color: #0066FF;
            border-radius: 50%;
            width: 36px; height: 36px;
            animation: spin 0.65s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Custom select arrow */
        .select-field {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
            padding-right: 2.5rem;
        }
    </style>
</head>
<body class="bg-[#F4F7FE] min-h-screen">

    {{-- Hidden form for final submission --}}
    <form id="sdqForm" method="POST" action="{{ route('kuesioner.submit') }}" class="hidden">
        @csrf
        <input type="hidden" name="nama_siswa" id="h_nama_siswa">
        <input type="hidden" name="kelas" id="h_kelas">
        <input type="hidden" name="jenis_kelamin" id="h_jenis_kelamin">
        <input type="hidden" name="umur" id="h_umur">
        <input type="hidden" name="email" id="h_email">
        <input type="hidden" name="no_hp" id="h_no_hp">
        @for ($i = 1; $i <= 25; $i++)
            <input type="hidden" name="jawaban[{{ $i }}]" id="h_jawaban_{{ $i }}">
        @endfor
    </form>

    <div class="w-full">

        {{-- Server Validation Errors --}}
        @if($errors->any())
            <div class="max-w-2xl mx-auto px-4 pt-8 md:pt-12">
                <div class="bg-red-50 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <div class="text-sm text-red-700">
                            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{--  STEP 1: DATA DIRI SISWA                                      --}}
        {{-- ============================================================ --}}
        <div id="step-identity" class="step-panel active">
            <div class="max-w-2xl mx-auto px-4 py-8 md:py-12">

            {{-- Page Header (outside card) --}}
            <div id="pageHeader" class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Kuisioner <span class="text-[#0066FF]">SDQ</span>
                </h1>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    Strengths and Difficulties Questionnaire<br>
                    Alat screening kesehatan mental siswa
                </p>
            </div>

            {{-- White Card --}}
            <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">

                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Data Diri Siswa</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Isi identitasmu sebelum memulai kuisioner</p>
                </div>

                {{-- Error Banner (client-side) --}}
                <div id="identityError" class="hidden bg-red-50 rounded-lg p-3 mb-5">
                    <div class="flex items-center gap-2 text-sm text-red-700 font-medium">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span id="identityErrorText"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Nama Lengkap (full width) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="inp_nama">Nama Lengkap</label>
                        <input type="text" id="inp_nama" autocomplete="name"
                            class="w-full bg-[#EFF3FF] rounded-lg px-4 py-3 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-blue-200 transition"
                            placeholder="Masukkan nama lengkap">
                    </div>

                    {{-- Kelas & Jenis Kelamin (2 columns) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="inp_kelas">Kelas</label>
                            <input type="text" id="inp_kelas"
                                class="w-full bg-[#EFF3FF] rounded-lg px-4 py-3 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-blue-200 transition"
                                placeholder="Contoh: XII IPA 2">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="inp_jk">Jenis Kelamin</label>
                            <select id="inp_jk"
                                class="select-field w-full bg-[#EFF3FF] rounded-lg px-4 py-3 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-200 transition">
                                <option value="">Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    {{-- Umur & No. HP (2 columns) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="inp_umur">Umur</label>
                            <input type="number" id="inp_umur" min="4" max="25"
                                class="w-full bg-[#EFF3FF] rounded-lg px-4 py-3 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-blue-200 transition"
                                placeholder="Contoh: 16">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="inp_hp">No. HP</label>
                            <input type="text" id="inp_hp"
                                class="w-full bg-[#EFF3FF] rounded-lg px-4 py-3 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-blue-200 transition"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    {{-- Email (full width) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="inp_email">Email</label>
                        <input type="email" id="inp_email"
                            class="w-full bg-[#EFF3FF] rounded-lg px-4 py-3 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-blue-200 transition"
                            placeholder="siswa@email.com">
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="button" id="btnStartQuiz"
                    class="w-full mt-8 bg-[#0066FF] hover:bg-blue-700 text-white font-semibold py-3.5 rounded-lg transition-colors text-sm">
                    Mulai Kuisioner
                </button>
            </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{--  STEP 2: KUESIONER PER SOAL                                   --}}
        {{-- ============================================================ --}}
        <div id="step-quiz" class="step-panel">
            <div class="max-w-4xl mx-auto px-4 min-h-screen flex flex-col justify-center pb-20 md:pb-32 pt-10">

            {{-- Progress Bar --}}
            <div id="progressArea" class="mb-10">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-700" id="progressLabel">Pertanyaan 1 dari 25</span>
                    <span class="text-sm font-semibold text-gray-400" id="progressPercent">0%</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0066FF] rounded-full transition-all duration-500 ease-out" id="progressBar" style="width: 4%"></div>
                </div>
            </div>

            {{-- Question Container (rendered by JS) --}}
            <div id="questionContainer" class="mb-10"></div>

            {{-- Navigation --}}
            <div class="flex items-center justify-between mt-8">
                <button type="button" id="btnPrev"
                    class="hidden items-center gap-2 px-6 py-3 bg-white text-[#0066FF] font-semibold text-base rounded-xl hover:bg-blue-50 transition-colors shadow-sm border border-gray-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Sebelumnya
                </button>
                <div class="ml-auto">
                    <button type="button" id="btnNext"
                        class="hidden items-center gap-2 px-6 py-3 bg-[#0066FF] hover:bg-blue-700 text-white font-semibold text-base rounded-xl transition-colors shadow-md">
                        Selanjutnya
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button type="button" id="btnFinish"
                        class="hidden items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-base rounded-xl transition-colors shadow-md">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Selesai
                    </button>
                </div>
            </div>
            </div>
        </div>

        {{-- Loading Overlay --}}
        <div id="loadingOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm hidden">
            <div class="text-center">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-sm font-semibold text-gray-700">Memproses jawaban kamu...</p>
                <p class="text-xs text-gray-400 mt-1">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        const questions = @json($questions);
        const totalQuestions = 25;

        let answers = {};
        let currentQ = 1;
        let isSubmitting = false;

        // DOM refs
        const stepIdentity      = document.getElementById('step-identity');
        const stepQuiz           = document.getElementById('step-quiz');
        const pageHeader         = document.getElementById('pageHeader');
        const progressLabel      = document.getElementById('progressLabel');
        const progressPercent    = document.getElementById('progressPercent');
        const progressBar        = document.getElementById('progressBar');
        const questionContainer  = document.getElementById('questionContainer');
        const btnPrev            = document.getElementById('btnPrev');
        const btnNext            = document.getElementById('btnNext');
        const btnFinish          = document.getElementById('btnFinish');
        const btnStartQuiz       = document.getElementById('btnStartQuiz');
        const loadingOverlay     = document.getElementById('loadingOverlay');
        const identityError      = document.getElementById('identityError');
        const identityErrorText  = document.getElementById('identityErrorText');

        // SVG icon templates
        const iconX     = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="7" y1="7" x2="17" y2="17"/><line x1="17" y1="7" x2="7" y2="17"/></svg>`;
        const iconMinus = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="7" y1="12" x2="17" y2="12"/></svg>`;
        const iconCheck = `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 12 10 16 18 8"/></svg>`;

        // =========================
        //  STEP 1 -> STEP 2
        // =========================
        btnStartQuiz.addEventListener('click', function() {
            const nama  = document.getElementById('inp_nama').value.trim();
            const kelas = document.getElementById('inp_kelas').value.trim();
            const jk    = document.getElementById('inp_jk').value;
            const umur  = document.getElementById('inp_umur').value;

            if (!nama) return showError('Nama lengkap wajib diisi.');
            if (!kelas) return showError('Kelas wajib diisi.');
            if (!jk) return showError('Jenis kelamin wajib dipilih.');
            if (!umur || umur < 4 || umur > 25) return showError('Umur wajib diisi (4-25 tahun).');

            hideError();
            stepIdentity.classList.remove('active');
            stepQuiz.classList.add('active');
            renderQuestion(1);
        });

        function showError(msg) {
            identityErrorText.textContent = msg;
            identityError.classList.remove('hidden');
            identityError.classList.add('shake');
            setTimeout(() => identityError.classList.remove('shake'), 350);
        }
        function hideError() {
            identityError.classList.add('hidden');
        }

        // =========================
        //  RENDER QUESTION
        // =========================
        function renderQuestion(num) {
            currentQ = num;
            const q = questions[num];
            const sel = answers[num];

            const opts = [
                { label: 'Tidak Benar', value: 0, iconDefault: iconX },
                { label: 'Agak Benar',  value: 1, iconDefault: iconMinus },
                { label: 'Benar',       value: 2, iconDefault: iconCheck },
            ];

            questionContainer.innerHTML = `
                <div class="q-card">
                    <p class="text-3xl md:text-5xl font-bold text-gray-900 text-center leading-tight mb-12">${q.text}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6" id="answersGrid">
                        ${opts.map(o => {
                            const isSelected = sel === o.value;
                            const cardBg     = isSelected ? 'bg-blue-100 border-blue-200 shadow-md scale-105' : 'bg-white border-gray-200 shadow-sm';
                            const circleStyle = isSelected
                                ? 'bg-[#0066FF] text-white'
                                : 'border-2 border-gray-300 text-gray-400';
                            const icon        = isSelected ? iconCheck : o.iconDefault;
                            const textColor   = isSelected ? 'text-[#0066FF] font-bold' : 'text-gray-700';

                            return `
                                <button type="button"
                                    class="answer-card cursor-pointer border rounded-2xl p-8 flex flex-col items-center gap-5 transition-all duration-300 ${cardBg}"
                                    data-value="${o.value}"
                                    onclick="window.__selectAnswer(${num}, ${o.value})">
                                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center ${circleStyle}">
                                        ${icon}
                                    </div>
                                    <span class="text-lg md:text-xl font-semibold ${textColor}">${o.label}</span>
                                </button>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;

            updateProgress();
            updateNavButtons();
        }

        // =========================
        //  SELECT ANSWER
        // =========================
        window.__selectAnswer = function(num, value) {
            answers[num] = value;
            renderQuestion(num); // re-render to update visual state

            // Auto-advance (except last question)
            if (num < totalQuestions) {
                setTimeout(() => goToQuestion(num + 1), 350);
            }
        };

        // =========================
        //  NAVIGATION
        // =========================
        function goToQuestion(num) {
            if (num < 1 || num > totalQuestions) return;
            const card = questionContainer.querySelector('.q-card');
            if (card) {
                card.style.opacity = '0';
                card.style.transform = num > currentQ ? 'translateX(24px)' : 'translateX(-24px)';
            }
            setTimeout(() => renderQuestion(num), 180);
        }

        btnPrev.addEventListener('click', () => goToQuestion(currentQ - 1));
        btnNext.addEventListener('click', () => goToQuestion(currentQ + 1));

        btnFinish.addEventListener('click', function() {
            if (isSubmitting) return;
            for (let i = 1; i <= totalQuestions; i++) {
                if (answers[i] === undefined) {
                    goToQuestion(i);
                    return;
                }
            }
            submitForm();
        });

        // =========================
        //  KEYBOARD SHORTCUTS
        // =========================
        document.addEventListener('keydown', function(e) {
            if (!stepQuiz.classList.contains('active') || isSubmitting) return;

            if (e.key === '1') { window.__selectAnswer(currentQ, 0); e.preventDefault(); }
            if (e.key === '2') { window.__selectAnswer(currentQ, 1); e.preventDefault(); }
            if (e.key === '3') { window.__selectAnswer(currentQ, 2); e.preventDefault(); }
            if (e.key === 'Enter' && answers[currentQ] !== undefined) {
                e.preventDefault();
                currentQ < totalQuestions ? goToQuestion(currentQ + 1) : btnFinish.click();
            }
            if (e.key === 'ArrowLeft' && currentQ > 1) { goToQuestion(currentQ - 1); e.preventDefault(); }
            if (e.key === 'ArrowRight' && currentQ < totalQuestions && answers[currentQ] !== undefined) { goToQuestion(currentQ + 1); e.preventDefault(); }
        });

        // =========================
        //  UI HELPERS
        // =========================
        function updateProgress() {
            const answered = Object.keys(answers).length;
            const pct = Math.round((currentQ / totalQuestions) * 100);
            const answeredPct = Math.round((answered / totalQuestions) * 100);
            progressLabel.textContent = `Pertanyaan ${currentQ} dari ${totalQuestions}`;
            progressPercent.textContent = `${answeredPct}%`;
            progressBar.style.width = `${pct}%`;
        }

        function updateNavButtons() {
            const hasAnswer = answers[currentQ] !== undefined;

            // Prev
            if (currentQ > 1) {
                btnPrev.classList.remove('hidden');
                btnPrev.classList.add('inline-flex');
            } else {
                btnPrev.classList.add('hidden');
                btnPrev.classList.remove('inline-flex');
            }

            // Next vs Finish
            if (currentQ < totalQuestions) {
                btnFinish.classList.add('hidden');
                btnFinish.classList.remove('inline-flex');
                if (hasAnswer) {
                    btnNext.classList.remove('hidden');
                    btnNext.classList.add('inline-flex');
                } else {
                    btnNext.classList.add('hidden');
                    btnNext.classList.remove('inline-flex');
                }
            } else {
                btnNext.classList.add('hidden');
                btnNext.classList.remove('inline-flex');
                if (hasAnswer) {
                    btnFinish.classList.remove('hidden');
                    btnFinish.classList.add('inline-flex');
                } else {
                    btnFinish.classList.add('hidden');
                    btnFinish.classList.remove('inline-flex');
                }
            }
        }

        // =========================
        //  SUBMIT
        // =========================
        function submitForm() {
            isSubmitting = true;
            loadingOverlay.classList.remove('hidden');

            document.getElementById('h_nama_siswa').value    = document.getElementById('inp_nama').value.trim();
            document.getElementById('h_kelas').value         = document.getElementById('inp_kelas').value.trim();
            document.getElementById('h_jenis_kelamin').value = document.getElementById('inp_jk').value;
            document.getElementById('h_umur').value          = document.getElementById('inp_umur').value;
            document.getElementById('h_email').value         = document.getElementById('inp_email').value.trim();
            document.getElementById('h_no_hp').value         = document.getElementById('inp_hp').value.trim();

            for (let i = 1; i <= totalQuestions; i++) {
                document.getElementById(`h_jawaban_${i}`).value = answers[i];
            }

            document.getElementById('sdqForm').submit();
        }

    });
    </script>
</body>
</html>
