@extends('layouts.app')
@section('title', 'Laporan Hasil Klasterisasi')

@section('content')
    <div class="space-y-6">
        {{-- Search & Sort Bar --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.laporan') }}" method="GET" class="flex shadow-sm rounded-lg overflow-hidden border border-gray-200 bg-white">
                    <div class="pl-3 flex items-center">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Laporan..." class="px-3 py-2 text-sm focus:outline-none w-48 lg:w-64 text-gray-700">
                    @if(request('order'))
                        <input type="hidden" name="order" value="{{ request('order') }}">
                    @endif
                    <button type="submit" class="bg-[#0066FF] text-white px-5 py-2 text-sm font-semibold hover:bg-blue-700 transition-colors">Cari</button>
                </form>

                {{-- Tombol Hapus Terpilih --}}
                <button type="button" onclick="bulkDeleteLaporan()" id="btn-hapus-terpilih" class="hidden items-center px-4 py-2 bg-white border border-red-500 text-red-500 text-sm font-semibold rounded-lg hover:bg-red-50 transition-all shadow-sm">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Hapus Terpilih (<span id="count-terpilih">0</span>)
                </button>
            </div>

            <form action="{{ route('admin.laporan') }}" method="GET">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="order" onchange="this.form.submit()" class="nb-select bg-white !w-auto">
                    <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Urutkan: Terlama</option>
                </select>
            </form>
        </div>

        {{-- Form Bulk Delete (Hidden) --}}
        <form id="bulk-delete-form" action="{{ route('admin.laporan.bulkDestroy') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Tabel Laporan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] text-gray-400 font-bold uppercase tracking-widest">
                            <th class="px-4 py-4 w-10">
                                <input type="checkbox" id="check-all" onchange="toggleAllCheckboxes(this)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="px-4 py-4 w-12">No</th>
                            <th class="px-6 py-4">Nama Laporan</th>
                            <th class="px-6 py-4 text-center">Jumlah K</th>
                            <th class="px-6 py-4 text-center">Jumlah Data</th>
                            <th class="px-6 py-4">Tanggal Dibuat</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-600 divide-y divide-gray-50">
                        @forelse ($laporanList as $index => $laporan)
                            <tr class="hover:bg-blue-50/10 transition-colors">
                                <td class="px-4 py-4">
                                    <input type="checkbox" name="ids[]" value="{{ $laporan->id }}" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" onchange="updateBulkDeleteButton()">
                                </td>
                                <td class="px-4 py-4 text-gray-400 font-medium">{{ $laporanList->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $laporan->nama_klastering }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                        K={{ $laporan->jumlah_k }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-gray-700">{{ $laporan->results_count }} Siswa</td>
                                <td class="px-6 py-4 text-gray-500">{{ $laporan->created_at->isoFormat('dddd, D MMMM YYYY — HH:mm') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center items-center gap-2">
                                        {{-- Download Excel --}}
                                        <a href="{{ route('admin.laporan.export', $laporan->id) }}"
                                           class="p-1.5 text-green-600 hover:bg-green-50 rounded-md border border-gray-100 shadow-sm transition-colors"
                                           title="Download Excel">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>

                                        {{-- Hapus --}}
                                        <button type="button"
                                            onclick="deleteLaporan({{ $laporan->id }}, '{{ addslashes($laporan->nama_klastering) }}')"
                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-gray-100 shadow-sm transition-colors"
                                            title="Hapus Laporan">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center space-y-3 text-gray-400">
                                        <i data-lucide="file-x-2" class="w-12 h-12 text-gray-300"></i>
                                        <span class="text-sm font-medium">Belum ada laporan klasterisasi.</span>
                                        <span class="text-xs">Jalankan proses K-Means di halaman <a href="{{ route('admin.klasterisasi') }}" class="text-[#0066FF] font-semibold hover:underline">Klasterisasi</a> untuk membuat laporan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($laporanList->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 flex items-center justify-between bg-white text-xs text-gray-400">
                    <span>Menampilkan {{ $laporanList->firstItem() }} - {{ $laporanList->lastItem() }} dari {{ $laporanList->total() }} hasil laporan</span>
                    <div>
                        {{ $laporanList->appends(request()->query())->links('partials.pagination') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Hidden form for single delete --}}
    <form id="form-delete-laporan" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
    // ===== SINGLE DELETE =====
    function deleteLaporan(id, name) {
        if (confirm('Yakin ingin menghapus laporan "' + name + '"? Semua data hasil klasterisasi terkait akan ikut terhapus.')) {
            const form = document.getElementById('form-delete-laporan');
            form.action = '/admin/laporan-hasil/' + id;
            form.submit();
        }
    }

    // ===== CHECKBOX FUNCTIONS =====
    function toggleAllCheckboxes(source) {
        var checkboxes = document.querySelectorAll('.row-checkbox');
        for (var i = 0; i < checkboxes.length; i++) {
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
        if (allCheckboxes.length > 0 && checkedCount === allCheckboxes.length) {
            masterCheckbox.checked = true;
        } else if (masterCheckbox) {
            masterCheckbox.checked = false;
        }
    }

    // ===== BULK DELETE =====
    function bulkDeleteLaporan() {
        var checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length === 0) return;

        if (confirm('Yakin ingin menghapus ' + checked.length + ' laporan terpilih? Semua data hasil klasterisasi terkait akan ikut terhapus.')) {
            var form = document.getElementById('bulk-delete-form');
            // Hapus input ids lama jika ada
            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            // Tambahkan semua id yang dicentang
            checked.forEach(function(cb) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });
            form.submit();
        }
    }
</script>
@endpush