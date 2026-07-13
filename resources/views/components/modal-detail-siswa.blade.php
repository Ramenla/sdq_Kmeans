{{-- ===== MODAL: DETAIL SISWA (FLAT DESIGN) ===== --}}
<div id="modal-detail" class="modal-overlay bg-black/60 backdrop-blur-none flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-xl shadow-xl border-0 w-full flex flex-col" style="max-width: 48rem; max-height: 90vh;">
        {{-- Header (Fixed) --}}
        <div class="px-8 py-6 flex items-center justify-between border-b border-gray-100 shrink-0">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Siswa/i</h2>
            <button type="button" onclick="closeModal('modal-detail')" class="text-gray-400 hover:text-gray-900 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        {{-- Body (Scrollable) --}}
        <div class="p-8 overflow-y-auto space-y-10">
            {{-- Biodata --}}
            <div>
                <h3 class="text-xs font-bold text-gray-400 tracking-wider mb-3 uppercase">Biodata Siswa</h3>
                <div class="space-y-4 text-base">
                    <div class="grid grid-cols-[140px_15px_1fr] items-start">
                        <span class="text-gray-500">Nama</span>
                        <span class="text-gray-500">:</span>
                        <span id="md-nama" class="font-medium text-gray-900 break-words">-</span>
                    </div>
                    <div class="grid grid-cols-[140px_15px_1fr] items-start">
                        <span class="text-gray-500">Kelas</span>
                        <span class="text-gray-500">:</span>
                        <span id="md-kelas" class="font-medium text-gray-900 break-words">-</span>
                    </div>
                    <div class="grid grid-cols-[140px_15px_1fr] items-start">
                        <span class="text-gray-500">Umur</span>
                        <span class="text-gray-500">:</span>
                        <span id="md-umur" class="font-medium text-gray-900 break-words">-</span>
                    </div>
                    <div class="grid grid-cols-[140px_15px_1fr] items-start">
                        <span class="text-gray-500">Jenis Kelamin</span>
                        <span class="text-gray-500">:</span>
                        <span id="md-jk" class="font-medium text-gray-900 break-words">-</span>
                    </div>
                    <div class="grid grid-cols-[140px_15px_1fr] items-start">
                        <span class="text-gray-500">No HP</span>
                        <span class="text-gray-500">:</span>
                        <span id="md-hp" class="font-medium text-gray-900 break-words">-</span>
                    </div>
                    <div class="grid grid-cols-[140px_15px_1fr] items-start">
                        <span class="text-gray-500">Email</span>
                        <span class="text-gray-500">:</span>
                        <span id="md-email" class="font-medium text-gray-900 break-words">-</span>
                    </div>
                </div>
            </div>

            {{-- Skor Terakhir --}}
            <div>
                <h3 class="text-xs font-bold text-gray-400 tracking-wider mb-3 uppercase">Skor Terakhir Tes</h3>
                <div class="border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between p-3 border-b border-gray-100">
                        <span class="w-1/3 text-gray-700 font-medium">Emosional (E)</span>
                        <span id="md-e" class="w-1/3 text-center text-gray-900 font-bold">-</span>
                        <span id="md-lbl-e" class="w-1/3 text-right text-xs font-bold">-</span>
                    </div>
                    <div class="flex items-center justify-between p-3 border-b border-gray-100">
                        <span class="w-1/3 text-gray-700 font-medium">Perilaku (C)</span>
                        <span id="md-c" class="w-1/3 text-center text-gray-900 font-bold">-</span>
                        <span id="md-lbl-c" class="w-1/3 text-right text-xs font-bold">-</span>
                    </div>
                    <div class="flex items-center justify-between p-3 border-b border-gray-100">
                        <span class="w-1/3 text-gray-700 font-medium">Hiperaktivitas (H)</span>
                        <span id="md-h" class="w-1/3 text-center text-gray-900 font-bold">-</span>
                        <span id="md-lbl-h" class="w-1/3 text-right text-xs font-bold">-</span>
                    </div>
                    <div class="flex items-center justify-between p-3 border-b border-gray-100">
                        <span class="w-1/3 text-gray-700 font-medium">Teman Sebaya (P)</span>
                        <span id="md-p" class="w-1/3 text-center text-gray-900 font-bold">-</span>
                        <span id="md-lbl-p" class="w-1/3 text-right text-xs font-bold">-</span>
                    </div>
                    <div class="flex items-center justify-between p-3">
                        <span class="w-1/3 text-gray-700 font-medium">Prososial (Pr)</span>
                        <span id="md-pr" class="w-1/3 text-center text-gray-900 font-bold">-</span>
                        <span id="md-lbl-pr" class="w-1/3 text-right text-xs font-bold">-</span>
                    </div>
                </div>
            </div>

            {{-- Riwayat Tes (Tabel) --}}
            <div>
                <h3 class="text-xs font-bold text-gray-400 tracking-wider mb-3 uppercase">Riwayat Tes & Perkembangan Gejala</h3>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-indigo-50/50 text-[11px] text-gray-500 font-bold uppercase tracking-wider border-b border-gray-200">
                                <th class="px-4 py-3">Tanggal Tes</th>
                                <th class="px-2 py-3 text-center">E</th>
                                <th class="px-2 py-3 text-center">C</th>
                                <th class="px-2 py-3 text-center">H</th>
                                <th class="px-2 py-3 text-center">P</th>
                                <th class="px-2 py-3 text-center">Pr</th>
                                <th class="px-4 py-3 text-center">Total Skor</th>
                                <th class="px-4 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody id="md-history-body" class="text-sm">
                            <!-- JS content injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
