@extends('layouts.app')
@section('title', 'Laporan Hasil')

@section('content')
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex shadow-sm rounded-lg overflow-hidden border border-gray-200 bg-white">
                    <div class="pl-3 flex items-center">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" placeholder="Cari Nama Laporan..." class="px-3 py-2 text-sm focus:outline-none w-48 lg:w-64 text-gray-700">
                    <button class="bg-[#0066FF] text-white px-5 py-2 text-sm font-semibold hover:bg-blue-700 transition-colors">Cari</button>
                </div>
                
                <select class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none text-gray-600 shadow-sm">
                    <option>Urutkan: Terbaru</option>
                    <option>Urutkan: Terlama</option>
                </select>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] text-gray-400 font-bold uppercase tracking-widest">
                                <th class="px-8 py-4">Nama Laporan</th>
                                <th class="px-8 py-4">Tanggal Dibuat</th>
                                <th class="px-8 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-600 divide-y divide-gray-50">
                            
                            <tr class="hover:bg-blue-50/10 transition-colors">
                                <td class="px-8 py-4 font-semibold text-gray-800">Laporan Hasil Klasterisasi Kelas 11 MIPA 1</td>
                                <td class="px-8 py-4 text-gray-500">20 Mei 2026</td>
                                <td class="px-8 py-4 flex justify-center space-x-3">
                                    <button class="p-1.5 text-green-600 hover:bg-green-50 rounded-md border border-gray-100 shadow-sm" title="Download Excel"><i data-lucide="download" class="w-4 h-4"></i></button>
                                    <button class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-md border border-gray-100 shadow-sm" title="Lihat Detail"><i data-lucide="eye" class="w-4 h-4"></i></button>
                                    <button class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-gray-100 shadow-sm" title="Hapus"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </td>
                            </tr>

                            <tr class="hover:bg-blue-50/10 transition-colors">
                                <td class="px-8 py-4 font-semibold text-gray-800">Pemetaan Skrining Awal Tahun Ajaran 2025/2026</td>
                                <td class="px-8 py-4 text-gray-500">12 Januari 2025</td>
                                <td class="px-8 py-4 flex justify-center space-x-3">
                                    <button class="p-1.5 text-green-600 hover:bg-green-50 rounded-md border border-gray-100 shadow-sm" title="Download Excel"><i data-lucide="download" class="w-4 h-4"></i></button>
                                    <button class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-md border border-gray-100 shadow-sm" title="Lihat Detail"><i data-lucide="eye" class="w-4 h-4"></i></button>
                                    <button class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-gray-100 shadow-sm" title="Hapus"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-4 border-t border-gray-50 flex items-center justify-between bg-white text-xs text-gray-400">
                    <span>Menampilkan 1 - 2 dari 100 hasil laporan</span>
                    <div class="flex items-center space-x-1">
                        <button class="p-1.5 border border-gray-100 rounded hover:bg-gray-50"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i></button>
                        <button class="px-2.5 py-1 bg-blue-50 text-[#0066FF] font-bold rounded">1</button>
                        <button class="p-1.5 border border-gray-100 rounded hover:bg-gray-50"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></button>
                    </div>
                </div>
            </div>

@endsection