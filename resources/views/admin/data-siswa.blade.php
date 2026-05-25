@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')
    {{-- ===== MODAL: TAMBAH DATA SISWA ===== --}}
    <div id="modal-tambah" class="modal-overlay">
        <div class="modal-content bg-white rounded-xl shadow-xl border border-gray-100">
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-800">Tambah Data Siswa</h2>
                <button type="button" onclick="closeModal('modal-tambah')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="{{ route('siswa.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">No HP <span class="text-red-500">*</span></label>
                        <input type="text" name="no_hp" class="nb-input" placeholder="Contoh: 0812345678" value="{{ old('no_hp') }}" required>
                    </div>
                    <div>
                        <label class="nb-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="nb-input" placeholder="Nama siswa" value="{{ old('name') }}" required>
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
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                    <button type="button" onclick="closeModal('modal-tambah')" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-colors text-sm">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: EDIT DATA SISWA ===== --}}
    <div id="modal-edit" class="modal-overlay">
        <div class="modal-content bg-white rounded-xl shadow-xl border border-gray-100">
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-800">Edit Data Siswa</h2>
                <button type="button" onclick="closeModal('modal-edit')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-edit" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="nb-label">No HP <span class="text-red-500">*</span></label>
                        <input type="text" name="no_hp" id="edit-no_hp" class="nb-input" required>
                    </div>
                    <div>
                        <label class="nb-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit-name" class="nb-input" required>
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
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-2">
                    <button type="button" onclick="closeModal('modal-edit')" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-colors text-sm">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: IMPORT DATA ===== --}}
    <div id="modal-import" class="modal-overlay">
        <div class="modal-content bg-white rounded-xl shadow-xl border border-gray-100" style="max-w: 28rem;">
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-800">Import Data SDQ</h2>
                <button type="button" onclick="closeModal('modal-import')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
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
                        <button type="button" onclick="if(confirm('Yakin ingin menghapus data terpilih?')) document.getElementById('bulk-delete-form').submit();" id="btn-hapus-terpilih" class="hidden items-center px-4 py-2 bg-white border border-red-500 text-red-500 text-sm font-semibold rounded-lg hover:bg-red-50 transition-all shadow-sm">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus Terpilih (<span id="count-terpilih">0</span>)
                        </button>
                        <button type="button" onclick="openModal('modal-import')" class="flex items-center px-4 py-2 bg-white border border-[#0066FF] text-[#0066FF] text-sm font-semibold rounded-lg hover:bg-blue-50 transition-all shadow-sm">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i> Import data
                        </button>
                        <button type="button" onclick="openModal('modal-tambah')" class="bg-[#0066FF] hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Data
                        </button>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Filter Kelas -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Filter Kelas</label>
                            <select name="kelas" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                                <option value="">Semua</option>
                                @foreach($listKelas as $kls)
                                    <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Tanggal Tes -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Tanggal Pemeriksaan</label>
                            <select name="date" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                                <option value="">Semua</option>
                                @foreach($daftarTanggal as $tgl)
                                    <option value="{{ $tgl }}" {{ request('date') == $tgl ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($tgl)->isoFormat('D MMMM YYYY') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Jenis Kelamin -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                                <option value="">Semua</option>
                                <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                        </div>

                        <!-- Filter Umur -->
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold text-gray-500 mb-1">Filter Umur</label>
                            <select name="umur" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-gray-600 shadow-sm">
                                <option value="">Semua</option>
                                @foreach($listUmur as $umr)
                                    <option value="{{ $umr }}" {{ request('umur') == $umr ? 'selected' : '' }}>{{ $umr }} Tahun</option>
                                @endforeach
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
                    <table class="w-full text-left border-collapse min-w-[1200px]">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] text-gray-400 font-bold uppercase tracking-widest">
                                <th class="px-4 py-4 text-center">
                                    <input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" onclick="toggleAllCheckboxes(this)">
                                </th>
                                <th class="px-4 py-4 text-center">ID Siswa</th>
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
                                <th class="px-4 py-4 text-center">Email</th>
                                <th class="px-4 py-4 text-center">No HP</th>
                                <th class="px-6 py-4 text-center">Tanggal Pemeriksaan</th>
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
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $data->id }}" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td class="px-4 py-4 text-center font-medium text-gray-400">{{ $data->id }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $data->nama_siswa ?: '-' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $data->kelas ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center font-medium text-gray-700">{{ $data->jenis_kelamin ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $data->umur }}</td>
                                    <td class="px-4 py-4 text-center">{{ $data->email ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $data->no_hp ?? '-' }}</td>
                                    
                                    @php
                                        $skor = $data->skorSdq->first();
                                    @endphp
                                    
                                    <td class="px-6 py-4 text-center">{{ $skor && $skor->tanggal_pemeriksaan ? \Carbon\Carbon::parse($skor->tanggal_pemeriksaan)->isoFormat('dddd, D MMMM YYYY') : '-' }}</td>
                                    <td class="px-3 py-4 text-center">{{ $skor->skor_e ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center">{{ $skor->skor_c ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center">{{ $skor->skor_h ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center">{{ $skor->skor_p ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-gray-900 bg-blue-50/30 text-base">{{ $skor->skor_diff ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center italic">{{ $skor->skor_pr ?? '-' }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Tombol Edit --}}
                                            <button type="button"
                                                onclick="openEditModal({{ $data->id }}, '{{ addslashes($data->no_hp ?? '') }}', '{{ addslashes($data->nama_siswa ?? '') }}', '{{ addslashes($data->email ?? '') }}', '{{ addslashes($data->kelas ?? '') }}', '{{ $data->jenis_kelamin ?? '' }}', '{{ $data->umur ?? '' }}', '{{ $skor->tanggal_pemeriksaan ?? '' }}')"
                                                class="bg-amber-400 hover:bg-amber-500 text-gray-800 font-medium px-3 py-1.5 rounded-md shadow-sm transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Edit Data">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                Edit
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('siswa.destroy', $data->id) }}" method="POST" 
                                                  onsubmit="return confirm('Yakin ingin menghapus data siswa {{ addslashes($data->nama_siswa ?? '') }}?');" 
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="bg-red-500 hover:bg-red-600 text-white font-medium px-3 py-1.5 rounded-md shadow-sm transition-colors duration-200 flex items-center gap-1 text-sm"
                                                    title="Hapus Data">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data kuesioner siswa.</td>
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

    function openEditModal(userId, noHp, name, email, kelas, jk, umur, tglPemeriksaan) {
        document.getElementById('form-edit').action = '/siswa/' + userId;
        document.getElementById('edit-no_hp').value = noHp;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-kelas').value = kelas;
        document.getElementById('edit-jk').value = jk;
        document.getElementById('edit-umur').value = umur;
        document.getElementById('edit-tgl').value = tglPemeriksaan;
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
</script>
@endpush