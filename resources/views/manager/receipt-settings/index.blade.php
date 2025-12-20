@extends('layouts.manager')

@section('title', 'Pengaturan Struk')

@section('content')
<div class="p-4 lg:p-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-receipt text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Pengaturan Struk</h2>
                    <p class="text-slate-400 text-sm">Kustomisasi tampilan struk penjualan</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="openPreviewModal()" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 border border-white/20">
                    <i class="fas fa-eye"></i>
                    Preview Struk
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('manager.receipt-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Main Settings -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Store Information -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-store text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Informasi Toko</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Toko *</label>
                            <input type="text" name="store_name" value="{{ old('store_name', $settings->store_name) }}" 
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                            @error('store_name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Toko</label>
                            <textarea name="store_address" rows="3" 
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('store_address', $settings->store_address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">No. Telepon</label>
                                <input type="text" name="store_phone" value="{{ old('store_phone', $settings->store_phone) }}" 
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                                <input type="email" name="store_email" value="{{ old('store_email', $settings->store_email) }}" 
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Content -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-align-left text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Konten Struk</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Header (Opsional)</label>
                            <textarea name="header_text" rows="2" placeholder="Teks tambahan di bagian atas struk..."
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('header_text', $settings->header_text) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pesan Terima Kasih</label>
                            <input type="text" name="thank_you_text" value="{{ old('thank_you_text', $settings->thank_you_text) }}" 
                                placeholder="TERIMAKASIH TELAH BERBELANJA"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Footer</label>
                            <textarea name="footer_text" rows="2" placeholder="Teks di bagian bawah struk..."
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('footer_text', $settings->footer_text) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Display Options -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-sliders-h text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Opsi Tampilan</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <input type="checkbox" name="show_logo" value="1" {{ $settings->show_logo ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-slate-700">Tampilkan Logo</span>
                            </label>

                            <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <input type="checkbox" name="show_address" value="1" {{ $settings->show_address ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-slate-700">Tampilkan Alamat</span>
                            </label>

                            <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <input type="checkbox" name="show_phone" value="1" {{ $settings->show_phone ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-slate-700">Tampilkan No. Telp</span>
                            </label>

                            <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <input type="checkbox" name="show_cashier_name" value="1" {{ $settings->show_cashier_name ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-slate-700">Tampilkan Nama Kasir</span>
                            </label>

                            <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <input type="checkbox" name="show_thank_you" value="1" {{ $settings->show_thank_you ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-slate-700">Tampilkan Pesan Terima Kasih</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Logo & Preview -->
            <div class="space-y-6">
                <!-- Logo Upload -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-image text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Logo Toko</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            @if($settings->logo)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($settings->logo) }}" alt="Logo" class="max-w-[150px] max-h-[100px] mx-auto rounded-lg border border-slate-200">
                                </div>
                                <button type="button" onclick="deleteLogo()" class="text-sm text-red-500 hover:text-red-700 mb-4">
                                    <i class="fas fa-trash mr-1"></i> Hapus Logo
                                </button>
                            @else
                                <div class="w-24 h-24 mx-auto mb-4 bg-slate-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-image text-3xl text-slate-400"></i>
                                </div>
                            @endif

                            <div class="mt-4">
                                <label class="block">
                                    <span class="sr-only">Pilih logo</span>
                                    <input type="file" name="logo" accept="image/*"
                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                </label>
                                <p class="mt-2 text-xs text-slate-500">Format: JPG, PNG, GIF. Maks 2MB.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Width -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-slate-500 to-slate-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-ruler-horizontal text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Ukuran Struk</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Lebar Struk</label>
                        <div class="relative">
                            <select name="receipt_width" class="custom-select w-full appearance-none px-4 py-3 pr-10 border-2 border-slate-200 rounded-xl bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all cursor-pointer hover:border-slate-300">
                                <option value="280px" {{ $settings->receipt_width == '280px' ? 'selected' : '' }}>58mm (280px)</option>
                                <option value="320px" {{ $settings->receipt_width == '320px' ? 'selected' : '' }}>80mm (320px) - Default</option>
                                <option value="384px" {{ $settings->receipt_width == '384px' ? 'selected' : '' }}>80mm Wide (384px)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Sesuaikan dengan ukuran printer thermal Anda.</p>
                    </div>
                </div>

                <!-- Save Button -->
                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Hidden form for delete logo (outside main form) -->
<form id="deleteLogoForm" action="{{ route('manager.receipt-settings.remove-logo') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 my-8 overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Preview Struk</h3>
                    <p class="text-emerald-100 text-xs">Contoh tampilan struk penjualan</p>
                </div>
            </div>
            <button type="button" onclick="closePreviewModal()" class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 bg-slate-100">
            <!-- Receipt Preview -->
            <div class="bg-white shadow-lg mx-auto p-4" id="receiptPreview" style="width: {{ $settings->receipt_width }}; font-family: 'Courier New', Courier, monospace; font-size:12px;">
                <!-- Logo -->
                @if($settings->show_logo && $settings->logo)
                <div style="text-align:center; margin-bottom: 8px;">
                    <img src="{{ Storage::url($settings->logo) }}" alt="Logo" style="max-height: 60px; max-width: 150px; margin: 0 auto;">
                </div>
                @endif

                <!-- Store Header -->
                <div style="text-align:center;">
                    <div style="font-size:18px; font-weight:700;">{{ $settings->store_name }}</div>
                    @if($settings->show_address && $settings->store_address)
                        <div>{{ $settings->store_address }}</div>
                    @endif
                    @if($settings->show_phone && $settings->store_phone)
                        <div>NO.TELP: {{ $settings->store_phone }}</div>
                    @endif
                    @if($settings->store_email)
                        <div>{{ $settings->store_email }}</div>
                    @endif
                    @if($settings->header_text)
                        <div style="margin-top:4px;">{{ $settings->header_text }}</div>
                    @endif
                    <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>
                </div>

                <!-- Transaction Info -->
                <div style="margin-top:6px;">
                    <div>INVOICE: INV-SAMPLE-001</div>
                    <div>TANGGAL: {{ now()->format('d/m/Y H:i') }}</div>
                    @if($settings->show_cashier_name)
                        <div>KASIR: {{ auth()->user()->name }}</div>
                    @endif
                </div>

                <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>

                <!-- Sample Items -->
                <div style="margin-top:6px;">
                    <div style="display:flex; justify-content:space-between;">
                        <div style="width:60%;">Produk Contoh 1</div>
                        <div style="width:10%; text-align:right;">2</div>
                        <div style="width:30%; text-align:right;">20.000</div>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <div style="width:60%;">Produk Contoh 2</div>
                        <div style="width:10%; text-align:right;">1</div>
                        <div style="width:30%; text-align:right;">15.000</div>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <div style="width:60%;">Produk Contoh 3</div>
                        <div style="width:10%; text-align:right;">3</div>
                        <div style="width:30%; text-align:right;">45.000</div>
                    </div>
                </div>

                <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>

                <!-- Totals -->
                <div style="margin-top:6px;">
                    <div style="display:flex; justify-content:space-between;">
                        <div>HARGA JUAL</div>
                        <div style="text-align:right;">Rp 80.000</div>
                    </div>

                    <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:6px;">
                        <div>TOTAL</div>
                        <div style="text-align:right;">Rp 80.000</div>
                    </div>
                </div>

                <div style="margin-top:8px; border-top:1px dashed #000; padding-top:6px;"></div>

                <!-- Payment -->
                <div style="margin-top:6px;">
                    <div style="display:flex; justify-content:space-between;">
                        <div>BAYAR (TUNAI)</div>
                        <div style="text-align:right;">Rp 100.000</div>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <div>KEMBALI</div>
                        <div style="text-align:right;">Rp 20.000</div>
                    </div>
                </div>

                <!-- Footer -->
                <div style="margin-top:10px; text-align:center;">
                    @if($settings->show_thank_you && $settings->thank_you_text)
                        <div>{{ $settings->thank_you_text }}</div>
                    @endif
                    @if($settings->footer_text)
                        <div style="margin-top:8px; font-size:10px;">{{ $settings->footer_text }}</div>
                    @endif
                </div>
            </div>

            <!-- Info Note -->
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-start gap-2">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-xs text-blue-700">Ini adalah preview dengan data contoh. Tampilan sebenarnya akan menggunakan data transaksi real.</p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
            <button type="button" onclick="closePreviewModal()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-medium transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function deleteLogo() {
    confirmAction('Apakah Anda yakin ingin menghapus logo?', function() {
        document.getElementById('deleteLogoForm').submit();
    }, { title: 'Hapus Logo', confirmText: 'Ya, Hapus' });
}

function openPreviewModal() {
    document.getElementById('previewModal').classList.remove('hidden');
    document.getElementById('previewModal').classList.add('flex');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
    document.getElementById('previewModal').classList.remove('flex');
}

// Close modal on backdrop click
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreviewModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreviewModal();
    }
});
</script>
@endsection
