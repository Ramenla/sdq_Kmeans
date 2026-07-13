@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')
    {{-- ===== MODAL: TAMBAH DATA SISWA ===== --}}
    <div id="modal-tambah" class="modal-overlay bg-black/60 backdrop-blur-none flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-xl shadow-xl border-0 w-full flex flex-col" style="max-width: 48rem; max-height: 90vh;">
            <div class="px-8 py-6 flex items-center justify-between border-b border-gray-100 shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Data Siswa</h2>
                <button type="button" onclick="closeModal('modal-tambah')" class="text-gray-400 hover:text-gray-900 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('siswa.store') }}" method="POST" class="p-5 overflow-y-auto space-y-2">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="nb-input" placeholder="Nama siswa" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="nb-label">No HP <span class="text-red-500">*</span></label>
                        <input type="text" name="no_hp" class="nb-input" placeholder="Contoh: 0812345678" value="{{ old('no_hp') }}" required>
                    </div>
                </div>
                <div>
                    <label class="nb-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="nb-input" placeholder="siswa@email.com" value="{{ old('email') }}" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="kelas" class="nb-input" placeholder="Contoh: X-IPA-1" value="{{ old('kelas') }}" required>
                    </div>
                    <div>
                        <label class="nb-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" class="nb-select" required>
                            <option value="">— Pilih —</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">Umur</label>
                        <input type="number" name="umur" class="nb-input" value="{{ old('umur') }}">
                    </div>
                    <div>
                        <label class="nb-label">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan" class="nb-input" value="{{ old('tanggal_pemeriksaan') }}">
                    </div>
                </div>
                <div class="pt-2 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Input Skor SDQ Mentah (Opsional)</h3>
                    <div class="grid grid-cols-5 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">E (Emosi)</label>
                            <input type="number" name="skor_e" min="0" max="10" class="nb-input text-center px-1" value="{{ old('skor_e') }}">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">C (Perilaku)</label>
                            <input type="number" name="skor_c" min="0" max="10" class="nb-input text-center px-1" value="{{ old('skor_c') }}">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">H (Hiperaktif)</label>
                            <input type="number" name="skor_h" min="0" max="10" class="nb-input text-center px-1" value="{{ old('skor_h') }}">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">P (Teman)</label>
                            <input type="number" name="skor_p" min="0" max="10" class="nb-input text-center px-1" value="{{ old('skor_p') }}">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">Pr (Prososial)</label>
                            <input type="number" name="skor_pr" min="0" max="10" class="nb-input text-center px-1" value="{{ old('skor_pr') }}">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-4">
                    <button type="button" onclick="closeModal('modal-tambah')" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors focus:outline-none">Batal</button>
                    <button type="submit" class="bg-[#0066FF] hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow-sm transition-colors text-sm focus:outline-none">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: EDIT DATA SISWA ===== --}}
    <div id="modal-edit" class="modal-overlay bg-black/60 backdrop-blur-none flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-xl shadow-xl border-0 w-full flex flex-col" style="max-width: 48rem; max-height: 90vh;">
            <div class="px-8 py-6 flex items-center justify-between border-b border-gray-100 shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Data Siswa</h2>
                <button type="button" onclick="closeModal('modal-edit')" class="text-gray-400 hover:text-gray-900 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="form-edit" action="" method="POST" class="p-5 overflow-y-auto space-y-2">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit-name" class="nb-input" required>
                    </div>
                    <div>
                        <label class="nb-label">No HP <span class="text-red-500">*</span></label>
                        <input type="text" name="no_hp" id="edit-no_hp" class="nb-input" required>
                    </div>
                </div>
                <div>
                    <label class="nb-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="edit-email" class="nb-input" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="kelas" id="edit-kelas" class="nb-input" required>
                    </div>
                    <div>
                        <label class="nb-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" id="edit-jk" class="nb-select" required>
                            <option value="">— Pilih —</option>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">Umur</label>
                        <input type="number" name="umur" id="edit-umur" class="nb-input">
                    </div>
                    <div>
                        <label class="nb-label">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan" id="edit-tgl" class="nb-input">
                    </div>
                </div>
                <div class="pt-2 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Input Skor SDQ Mentah (Opsional)</h3>
                    <div class="grid grid-cols-5 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">E (Emosi)</label>
                            <input type="number" name="skor_e" id="edit-skor-e" min="0" max="10" class="nb-input text-center px-1">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">C (Perilaku)</label>
                            <input type="number" name="skor_c" id="edit-skor-c" min="0" max="10" class="nb-input text-center px-1">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">H (Hiperaktif)</label>
                            <input type="number" name="skor_h" id="edit-skor-h" min="0" max="10" class="nb-input text-center px-1">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">P (Teman)</label>
                            <input type="number" name="skor_p" id="edit-skor-p" min="0" max="10" class="nb-input text-center px-1">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">Pr (Prososial)</label>
                            <input type="number" name="skor_pr" id="edit-skor-pr" min="0" max="10" class="nb-input text-center px-1">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-4">
                    <button type="button" onclick="closeModal('modal-edit')" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors focus:outline-none">Batal</button>
                    <button type="submit" class="bg-[#0066FF] hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow-sm transition-colors text-sm focus:outline-none">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: IMPORT DATA ===== --}}
    <div id="modal-import" class="modal-overlay bg-black/60 backdrop-blur-none flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-xl shadow-xl border-0 w-full flex flex-col" style="max-width: 28rem; max-height: 90vh;">
            <div class="px-8 py-6 flex items-center justify-between border-b border-gray-100 shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Import Data SDQ</h2>
                <button type="button" onclick="closeModal('modal-import')" class="text-gray-400 hover:text-gray-900 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih File (CSV / XLSX) <span class="text-red-500">*</span></label>
                    <input type="file" name="file_import" accept=".csv, .xls, .xlsx" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors border border-gray-200 rounded-lg p-1">
                    <p class="text-xs text-gray-500 mt-2">Pastikan format kolom Excel sesuai dengan template yang ditentukan.</p>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                    <button type="button" onclick="closeModal('modal-import')" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200 shadow-sm">Batal</button>
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-colors text-sm flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: EXPORT DATA ===== --}}
    <div id="modal-export" class="modal-overlay bg-black/60 backdrop-blur-none flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-xl shadow-xl border-0 w-full flex flex-col" style="max-width: 42rem; max-height: 90vh;">
            <div class="px-8 py-6 flex items-center justify-between border-b border-gray-100 shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Export Data Siswa</h2>
                <button type="button" onclick="closeModal('modal-export')" class="text-gray-400 hover:text-gray-900 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('siswa.export') }}" method="GET" class="p-6 space-y-5">
                
                {{-- Ruang Lingkup (Filter) --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-800 border-b pb-2 mb-3">Filter Ruang Lingkup</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Kelas</label>
                            <select name="kelas" class="nb-select bg-white">
                                <option value="">Semua Kelas</option>
                                @foreach($listKelas as $kls)
                                    <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="nb-select bg-white">
                                <option value="">Semua</option>
                                <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Umur</label>
                            <select name="umur" class="nb-select bg-white">
                                <option value="">Semua Umur</option>
                                @foreach($listUmur as $umr)
                                    <option value="{{ $umr }}" {{ request('umur') == $umr ? 'selected' : '' }}>{{ $umr }} Tahun</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Kategori</label>
                            <select name="kategori" class="nb-select bg-white">
                                <option value="">Semua Kategori</option>
                                <option value="Normal" {{ request('kategori') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Borderline" {{ request('kategori') == 'Borderline' ? 'selected' : '' }}>Borderline</option>
                                <option value="Abnormal" {{ request('kategori') == 'Abnormal' ? 'selected' : '' }}>Abnormal</option>
                            </select>
                        </div>
                        <div class="flex flex-col col-span-2">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Tanggal Pemeriksaan</label>
                            <select name="date" class="nb-select bg-white">
                                <option value="">Semua Waktu</option>
                                @foreach($daftarTanggal as $tgl)
                                    <option value="{{ $tgl }}" {{ request('date') == $tgl ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($tgl)->isoFormat('D MMMM YYYY') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Variabel yang diunduh --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-800 border-b pb-2 mb-3">Pilih Variabel Kolom</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @php
                            $availableColumns = [
                                'id_siswa' => 'ID Siswa',
                                'nama_siswa' => 'Nama Siswa',
                                'kelas' => 'Kelas',
                                'jenis_kelamin' => 'Jenis Kelamin',
                                'umur' => 'Umur',
                                'email' => 'Email',
                                'no_hp' => 'No HP',
                                'tanggal_pemeriksaan' => 'Tanggal Pemeriksaan',
                                'skor_e' => 'Skor E (Emosi)',
                                'skor_c' => 'Skor C (Perilaku)',
                                'skor_h' => 'Skor H (Hiperaktif)',
                                'skor_p' => 'Skor P (Teman)',
                                'skor_diff' => 'Total Kesulitan',
                                'skor_pr' => 'Skor Pr (Prososial)',
                                'kategori' => 'Kategori'
                            ];
                        @endphp
                        @foreach($availableColumns as $key => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="columns[]" value="{{ $key }}" class="rounded text-emerald-500 focus:ring-emerald-500 border-gray-300" checked>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                    <button type="button" onclick="closeModal('modal-export')" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200 shadow-sm">Batal</button>
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-colors text-sm flex items-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Unduh Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: DETAIL SISWA (FLAT DESIGN) ===== --}}
    <x-modal-detail-siswa />            <form action="{{ route('dashboard.guru') }}" method="GET" class="space-y-4 mb-1">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex shadow-sm rounded-lg overflow-hidden border border-gray-200 bg-white">
                        <div class="pl-3 flex items-center">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, ID, Email..." class="px-3 py-2 text-sm focus:outline-none w-48 lg:w-64 text-gray-700">
                        <button type="submit" class="bg-[#0066FF] text-white px-5 py-2 text-sm font-semibold hover:bg-blue-700 transition-colors">Cari</button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="if(confirm('Yakin ingin menghapus data terpilih?')) document.getElementById('bulk-delete-form').submit();" id="btn-hapus-terpilih" class="hidden items-center px-4 py-2 bg-white border border-red-500 text-red-500 text-sm font-semibold rounded-lg hover:bg-red-50 transition-all shadow-sm">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus Terpilih (<span id="count-terpilih">0</span>)
                        </button>
                        <button type="button" onclick="recalculateKategori()" id="btn-recalculate" class="flex items-center px-2 py-2 bg-white border border-emerald-500 text-emerald-600 text-sm font-semibold rounded-lg hover:bg-emerald-50 transition-all shadow-sm">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Recalculate Kategori
                        </button>
                        <button type="button" onclick="openModal('modal-export')" class="flex items-center px-4 py-2 bg-white border border-emerald-500 text-emerald-600 text-sm font-semibold rounded-lg hover:bg-emerald-50 transition-all shadow-sm">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i> Export Data
                        </button>
                        <button type="button" onclick="openModal('modal-import')" class="flex items-center px-4 py-2 bg-white border border-[#0066FF] text-[#0066FF] text-sm font-semibold rounded-lg hover:bg-blue-50 transition-all shadow-sm">
                            <i data-lucide="upload" class="w-4 h-4 mr-2"></i> Import Data
                        </button>
                        <button type="button" onclick="openModal('modal-tambah')" class="bg-[#0066FF] hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Data
                        </button>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <!-- Filter Kelas -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Filter Kelas</label>
                            <select name="kelas" onchange="this.form.submit()" class="nb-select bg-white">
                                <option value="">Semua</option>
                                @foreach($listKelas as $kls)
                                    <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Tanggal Tes -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Tanggal Pemeriksaan</label>
                            <select name="date" onchange="this.form.submit()" class="nb-select bg-white">
                                <option value="">Semua</option>
                                @foreach($daftarTanggal as $tgl)
                                    <option value="{{ $tgl }}" {{ request('date') == $tgl ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($tgl)->isoFormat('D MMMM YYYY') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Jenis Kelamin -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" onchange="this.form.submit()" class="nb-select bg-white">
                                <option value="">Semua</option>
                                <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                        </div>

                        <!-- Filter Umur -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Filter Umur</label>
                            <select name="umur" onchange="this.form.submit()" class="nb-select bg-white">
                                <option value="">Semua</option>
                                @foreach($listUmur as $umr)
                                    <option value="{{ $umr }}" {{ request('umur') == $umr ? 'selected' : '' }}>{{ $umr }} Tahun</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filter Kategori -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Kategori</label>
                            <select name="kategori" onchange="this.form.submit()" class="nb-select bg-white">
                                <option value="">Semua</option>
                                <option value="Normal" {{ request('kategori') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Borderline" {{ request('kategori') == 'Borderline' ? 'selected' : '' }}>Borderline</option>
                                <option value="Abnormal" {{ request('kategori') == 'Abnormal' ? 'selected' : '' }}>Abnormal</option>
                            </select>
                        </div>
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

            <form id="bulk-delete-form" action="{{ route('siswa.bulkDestroy') }}" method="POST">
                @csrf
                @method('DELETE')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="table-container overflow-x-auto">
                    <table class="w-full text-left border-collapse ">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                <th class="px-2 py-2.5 text-center">
                                    <input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" onclick="toggleAllCheckboxes(this)">
                                </th>
                                <th class="px-2 py-2.5 text-center">ID Siswa</th>
                                <th class="px-3 py-2.5">
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
                                <th class="px-2 py-2.5 text-center">Kelas</th>
                                <th class="px-2 py-2.5 text-center">Jenis Kelamin</th>
                                <th class="px-2 py-2.5 text-center">Umur</th>
                                <th class="px-2 py-2.5 text-center">Email</th>
                                <th class="px-2 py-2.5 text-center">No HP</th>
                                <th class="px-3 py-2.5 text-center">Tanggal Pemeriksaan</th>
                                <th class="px-1 py-2.5 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'skor_e', 'order' => (request('sort_by') === 'skor_e' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>E</span>
                                        @if(request('sort_by') === 'skor_e')
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
                                <th class="px-1 py-2.5 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'skor_c', 'order' => (request('sort_by') === 'skor_c' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>C</span>
                                        @if(request('sort_by') === 'skor_c')
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
                                <th class="px-1 py-2.5 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'skor_h', 'order' => (request('sort_by') === 'skor_h' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>H</span>
                                        @if(request('sort_by') === 'skor_h')
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
                                <th class="px-1 py-2.5 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'skor_p', 'order' => (request('sort_by') === 'skor_p' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>P</span>
                                        @if(request('sort_by') === 'skor_p')
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
                                <th class="px-2 py-2.5 text-center text-[#0066FF] bg-blue-50/50">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'skor_diff', 'order' => (request('sort_by') === 'skor_diff' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>Total Kesulitan</span>
                                        @if(request('sort_by') === 'skor_diff')
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
                                <th class="px-1 py-2.5 text-center">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'skor_pr', 'order' => (request('sort_by') === 'skor_pr' && request('order') === 'asc') ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-0.5 justify-center hover:text-[#0066FF] transition-colors cursor-pointer normal-case w-full">
                                        <span>Pr</span>
                                        @if(request('sort_by') === 'skor_pr')
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
                                <th class="px-2 py-2.5 text-center">Kategori</th>
                                <th class="px-3 py-2.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs text-gray-600 divide-y divide-gray-50">
                            @forelse ($dataSiswa as $data)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="px-2 py-2.5 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $data->id }}" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td class="px-2 py-2.5 text-center font-medium text-gray-400">{{ $data->id }}</td>
                                    <td class="px-3 py-2.5 font-semibold text-gray-800">{{ $data->nama_siswa ?: '-' }}</td>
                                    <td class="px-2 py-2.5 text-center">{{ $data->kelas ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-center font-medium text-gray-700">{{ $data->jenis_kelamin ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-center">{{ $data->umur }}</td>
                                    <td class="px-2 py-2.5 text-center">{{ $data->email ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-center">{{ $data->no_hp ?? '-' }}</td>
                                    
                                    @php
                                        $skor = $data->skorSdq->first();
                                    @endphp
                                    
                                    <td class="px-3 py-2.5 text-center">{{ $skor && $skor->tanggal_pemeriksaan ? \Carbon\Carbon::parse($skor->tanggal_pemeriksaan)->isoFormat('dddd, D MMMM YYYY') : '-' }}</td>
                                    <td class="px-1 py-2.5 text-center">{{ $skor->skor_e ?? '-' }}</td>
                                    <td class="px-1 py-2.5 text-center">{{ $skor->skor_c ?? '-' }}</td>
                                    <td class="px-1 py-2.5 text-center">{{ $skor->skor_h ?? '-' }}</td>
                                    <td class="px-1 py-2.5 text-center">{{ $skor->skor_p ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-center font-bold text-gray-900 bg-blue-50/30 text-sm">{{ $skor->skor_diff ?? '-' }}</td>
                                    <td class="px-1 py-2.5 text-center italic">{{ $skor->skor_pr ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-center font-semibold text-sm">
                                        {{ $skor->kategori ?? '-' }}
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Tombol Detail --}}
                                            <button type="button" 
                                                data-detail="{{ json_encode([
                                                    'nama' => $data->nama_siswa ?? '-',
                                                    'kelas' => $data->kelas ?? '-',
                                                    'umur' => $data->umur ? $data->umur . ' Tahun' : '-',
                                                    'jk' => $data->jenis_kelamin == 'L' ? 'Laki-laki' : ($data->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
                                                    'hp' => $data->no_hp ?? '-',
                                                    'email' => $data->email ?? '-',
                                                    'skor_e' => $skor->skor_e ?? '-', 'kategori_e' => $skor->kategori_e ?? '-',
                                                    'skor_c' => $skor->skor_c ?? '-', 'kategori_c' => $skor->kategori_c ?? '-',
                                                    'skor_h' => $skor->skor_h ?? '-', 'kategori_h' => $skor->kategori_h ?? '-',
                                                    'skor_p' => $skor->skor_p ?? '-', 'kategori_p' => $skor->kategori_p ?? '-',
                                                    'skor_pr' => $skor->skor_pr ?? '-', 'kategori_pr' => $skor->kategori_pr ?? '-',
                                                    'tanggal' => $skor && $skor->tanggal_pemeriksaan ? \Carbon\Carbon::parse($skor->tanggal_pemeriksaan)->isoFormat('D MMMM YYYY') : '-',
                                                    'total' => $skor->skor_diff ?? '-',
                                                    'kategori' => $skor->kategori ?? '-'
                                                ]) }}"
                                                onclick="openDetailModal(this)"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium px-2 py-1 rounded-md shadow-sm transition-colors duration-200 flex items-center gap-1 text-sm focus:outline-none"
                                                title="Lihat Detail">
                                                <i data-lucide="eye" class="w-3 h-3"></i>
                                                Detail
                                            </button>

                                            {{-- Tombol Edit --}}
                                            <button type="button"
                                                onclick="openEditModal({{ $data->id }}, '{{ addslashes($data->no_hp ?? '') }}', '{{ addslashes($data->nama_siswa ?? '') }}', '{{ addslashes($data->email ?? '') }}', '{{ addslashes($data->kelas ?? '') }}', '{{ $data->jenis_kelamin ?? '' }}', '{{ $data->umur ?? '' }}', '{{ $skor->tanggal_pemeriksaan ?? '' }}', '{{ $skor->skor_e ?? '' }}', '{{ $skor->skor_c ?? '' }}', '{{ $skor->skor_h ?? '' }}', '{{ $skor->skor_p ?? '' }}', '{{ $skor->skor_pr ?? '' }}')"
                                                class="bg-amber-400 hover:bg-amber-500 text-gray-800 font-medium px-2 py-1 rounded-md shadow-sm transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Edit Data">
                                                <i data-lucide="pencil" class="w-3 h-3"></i>
                                                Edit
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <button type="button" 
                                                onclick="deleteSiswa({{ $data->id }}, '{{ addslashes($data->nama_siswa ?? '') }}')"
                                                class="bg-red-500 hover:bg-red-600 text-white font-medium px-2 py-1 rounded-md shadow-sm transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Hapus Data">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data kuesioner siswa.</td>
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
            </form>

            {{-- Form Hapus Tunggal (Hidden) --}}
            <form id="form-delete-single" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>

@endsection

@push('scripts')
<script>
    // ===== MODAL FUNCTIONS =====
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }

    function openEditModal(userId, noHp, name, email, kelas, jk, umur, tglPemeriksaan, skorE, skorC, skorH, skorP, skorPr) {
        document.getElementById('form-edit').action = '/siswa/' + userId;
        document.getElementById('edit-no_hp').value = noHp;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-kelas').value = kelas;
        document.getElementById('edit-jk').value = jk;
        document.getElementById('edit-umur').value = umur;
        document.getElementById('edit-tgl').value = tglPemeriksaan;
        document.getElementById('edit-skor-e').value = skorE;
        document.getElementById('edit-skor-c').value = skorC;
        document.getElementById('edit-skor-h').value = skorH;
        document.getElementById('edit-skor-p').value = skorP;
        document.getElementById('edit-skor-pr').value = skorPr;
        openModal('modal-edit');
    }

    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(function(m) {
                m.classList.remove('active');
            });
            document.body.style.overflow = '';
        }
    });

    @if($errors->any() && old('_method') === 'PUT')
        openModal('modal-edit');
    @elseif($errors->any() && !old('_method'))
        openModal('modal-tambah');
    @endif

    // ===== SINGLE DELETE FUNCTION =====
    function deleteSiswa(id, name) {
        if (confirm('Yakin ingin menghapus data siswa ' + name + '?')) {
            const form = document.getElementById('form-delete-single');
            form.action = '/siswa/' + id;
            form.submit();
        }
    }

    // ===== BULK DELETE FUNCTIONS =====
    function toggleAllCheckboxes(source) {
        checkboxes = document.querySelectorAll('.row-checkbox');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
        updateBulkDeleteButton();
    }

    function updateBulkDeleteButton() {
        var checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        var btn = document.getElementById('btn-hapus-terpilih');
        var countSpan = document.getElementById('count-terpilih');
        
        if (checkedCount > 0) {
            btn.classList.remove('hidden');
            btn.classList.add('flex');
            countSpan.innerText = checkedCount;
        } else {
            btn.classList.add('hidden');
            btn.classList.remove('flex');
        }
        
        // Update master checkbox
        var allCheckboxes = document.querySelectorAll('.row-checkbox');
        var masterCheckbox = document.getElementById('check-all');
        if(allCheckboxes.length > 0 && checkedCount === allCheckboxes.length) {
            masterCheckbox.checked = true;
        } else if (masterCheckbox) {
            masterCheckbox.checked = false;
        }
    }

    // ===== RECALCULATE KATEGORI =====
    function recalculateKategori() {
        const btn = document.getElementById('btn-recalculate');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Memproses...';
        btn.disabled = true;

        fetch('{{ route("api.recalculateKategori") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                btn.innerHTML = originalText;
                btn.disabled = false;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
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
        if (status === '-' || !status) return 'text-gray-500';
        return 'text-blue-600';
    }

    function openDetailModal(btn) {
        let data = JSON.parse(btn.getAttribute('data-detail'));
        
        // Biodata
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

        // Tabel Riwayat
        let tbody = document.getElementById('md-history-body');
        if (data.total !== '-') {
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
        } else {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-gray-400 italic">Belum ada tes</td></tr>`;
        }

        openModal('modal-detail');
    }
</script>
@endpush