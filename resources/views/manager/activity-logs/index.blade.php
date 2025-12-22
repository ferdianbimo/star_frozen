@extends('layouts.manager')

@section('title','Log Aktivitas')

@section('content')
<div class="spacing-page">
    <x-page-header
        icon="fa-clipboard-list"
        title="Log Aktivitas"
        subtitle="Pantau semua aktivitas sistem secara realtime"
        iconColor="primary">
        <x-slot name="actions">
            <span class="inline-flex items-center px-3 py-1.5 bg-green-500/20 border border-green-500/30 text-green-400 rounded-badge text-sm font-medium">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                Live
            </span>
            <button onclick="location.reload()" class="btn-outline btn-md">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
        </x-slot>
    </x-page-header>

    <!-- Summary Stats -->
    <div class="grid-stats-6 spacing-section">
        <x-stat-card
            icon="fa-list"
            iconColor="slate"
            label="Total"
            :value="$logs->total()" />

        <x-stat-card
            icon="fa-sign-in-alt"
            iconColor="info"
            label="Login"
            :value="\App\Models\ActivityLog::where('action', 'login')->whereDate('created_at', today())->count()" />

        <x-stat-card
            icon="fa-shopping-cart"
            iconColor="primary"
            label="Transaksi"
            :value="\App\Models\ActivityLog::where('action', 'checkout')->whereDate('created_at', today())->count()" />

        <x-stat-card
            icon="fa-arrow-down"
            iconColor="success"
            label="Stok Masuk"
            :value="\App\Models\ActivityLog::where('action', 'stock_in')->whereDate('created_at', today())->count()" />

        <x-stat-card
            icon="fa-arrow-up"
            iconColor="warning"
            label="Stok Keluar"
            :value="\App\Models\ActivityLog::where('action', 'stock_out')->whereDate('created_at', today())->count()" />

        <x-stat-card
            icon="fa-edit"
            iconColor="primary"
            label="Perubahan"
            :value="\App\Models\ActivityLog::whereIn('action', ['create', 'update', 'delete'])->whereDate('created_at', today())->count()" />
    </div>

    <!-- Filters -->
    <x-section-card icon="fa-filter" iconColor="primary" title="Filter Log">\n        <form method="GET" action="{{ route('manager.activity-logs.index') }}" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="relative">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Modul</label>
                <div class="relative">
                    <select name="module" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer hover:border-slate-300">
                        <option value="">Semua Modul</option>
                        <option value="auth" {{ request('module') == 'auth' ? 'selected' : '' }}>🔐 Autentikasi</option>
                        <option value="inventory" {{ request('module') == 'inventory' ? 'selected' : '' }}>📦 Inventory</option>
                        <option value="pos" {{ request('module') == 'pos' ? 'selected' : '' }}>🛒 POS</option>
                        <option value="product" {{ request('module') == 'product' ? 'selected' : '' }}>📋 Produk</option>
                        <option value="user" {{ request('module') == 'user' ? 'selected' : '' }}>👤 User</option>
                        <option value="finance" {{ request('module') == 'finance' ? 'selected' : '' }}>💰 Keuangan</option>
                        <option value="batch" {{ request('module') == 'batch' ? 'selected' : '' }}>📦 Batch</option>
                        <option value="settings" {{ request('module') == 'settings' ? 'selected' : '' }}>⚙️ Settings</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>
            <div class="relative">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Aksi</label>
                <div class="relative">
                    <select name="action" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer hover:border-slate-300">
                        <option value="">Semua Aksi</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Tambah</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Edit</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Hapus</option>
                        <option value="stock_in" {{ request('action') == 'stock_in' ? 'selected' : '' }}>Stok Masuk</option>
                        <option value="stock_out" {{ request('action') == 'stock_out' ? 'selected' : '' }}>Stok Keluar</option>
                        <option value="checkout" {{ request('action') == 'checkout' ? 'selected' : '' }}>Checkout</option>
                        <option value="export" {{ request('action') == 'export' ? 'selected' : '' }}>Export</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>
            <div class="relative">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">User</label>
                <div class="relative">
                    <select name="user_id" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer hover:border-slate-300">
                        <option value="">Semua User</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>
            <div class="relative">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Dari Tanggal</label>
                <div class="relative">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all hover:border-slate-300">
                </div>
            </div>
            <div class="relative">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Sampai Tanggal</label>
                <div class="relative">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all hover:border-slate-300">
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary btn-md flex-1">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                @if(request()->hasAny(['module', 'action', 'user_id', 'date_from', 'date_to']))
                <a href="{{ route('manager.activity-logs.index') }}" class="btn-outline btn-md" title="Reset Filter">
                    <i class="fas fa-undo"></i>
                </a>
                @endif
            </div>
        </form>
    </x-section-card>

    <!-- Activity Logs Table -->
    <x-section-card icon="fa-list" iconColor="primary" title="Riwayat Aktivitas">
        <div class="table-container">
            <table class="min-w-full">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Modul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    @php
                        $actionConfig = [
                            'create' => ['bg' => 'bg-gradient-to-r from-emerald-500 to-teal-500', 'text' => 'text-white', 'label' => 'Tambah'],
                            'update' => ['bg' => 'bg-gradient-to-r from-blue-500 to-indigo-500', 'text' => 'text-white', 'label' => 'Edit'],
                            'delete' => ['bg' => 'bg-gradient-to-r from-red-500 to-rose-500', 'text' => 'text-white', 'label' => 'Hapus'],
                            'stock_in' => ['bg' => 'bg-gradient-to-r from-green-500 to-emerald-500', 'text' => 'text-white', 'label' => 'Stok Masuk'],
                            'stock_out' => ['bg' => 'bg-gradient-to-r from-orange-500 to-amber-500', 'text' => 'text-white', 'label' => 'Stok Keluar'],
                            'checkout' => ['bg' => 'bg-gradient-to-r from-purple-500 to-violet-500', 'text' => 'text-white', 'label' => 'Checkout'],
                            'login' => ['bg' => 'bg-gradient-to-r from-teal-500 to-cyan-500', 'text' => 'text-white', 'label' => 'Login'],
                            'logout' => ['bg' => 'bg-gradient-to-r from-slate-400 to-slate-500', 'text' => 'text-white', 'label' => 'Logout'],
                            'export' => ['bg' => 'bg-gradient-to-r from-amber-500 to-yellow-500', 'text' => 'text-white', 'label' => 'Export'],
                        ];
                        $action = $actionConfig[$log->action] ?? ['bg' => 'bg-slate-200', 'text' => 'text-slate-700', 'label' => ucfirst($log->action)];

                        $moduleConfig = [
                            'inventory' => ['icon' => 'fa-boxes-stacked', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
                            'pos' => ['icon' => 'fa-cash-register', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                            'product' => ['icon' => 'fa-box', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                            'user' => ['icon' => 'fa-user', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                            'auth' => ['icon' => 'fa-shield-halved', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
                            'finance' => ['icon' => 'fa-wallet', 'color' => 'text-green-600', 'bg' => 'bg-green-50'],
                            'batch' => ['icon' => 'fa-layer-group', 'color' => 'text-cyan-600', 'bg' => 'bg-cyan-50'],
                            'settings' => ['icon' => 'fa-gear', 'color' => 'text-slate-600', 'bg' => 'bg-slate-50'],
                        ];
                        $module = $moduleConfig[$log->module] ?? ['icon' => 'fa-circle', 'color' => 'text-slate-600', 'bg' => 'bg-slate-50'];
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-slate-400"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-slate-500">{{ $log->created_at->format('H:i:s') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($log->user->name ?? 'S', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $log->user->name ?? 'System' }}</div>
                                    @if($log->ip_address)
                                        <div class="text-xs text-slate-400 font-mono">{{ $log->ip_address }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold {{ $action['bg'] }} {{ $action['text'] }} shadow-sm">
                                {{ $action['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 {{ $module['bg'] }} rounded-lg flex items-center justify-center">
                                    <i class="fas {{ $module['icon'] }} {{ $module['color'] }} text-sm"></i>
                                </div>
                                <span class="font-medium text-slate-700">{{ ucfirst($log->module) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600 max-w-xs truncate">{{ $log->description ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($log->old_values || $log->new_values || $log->metadata)
                                <button onclick="showLogDetail({{ $log->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition-colors">
                                    <i class="fas fa-eye"></i>
                                    Detail
                                </button>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-clipboard-list text-3xl text-slate-300"></i>
                                </div>
                                <p class="text-lg font-semibold text-slate-400">Belum ada log aktivitas</p>
                                <p class="text-sm text-slate-400 mt-1">Aktivitas sistem akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </x-section-card>

    <!-- Log Detail Modal -->
    <div id="logDetailModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeLogDetail()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden transform transition-all">
                <div class="px-6 py-4 bg-gradient-to-r from-slate-800 to-slate-900 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">Detail Log Aktivitas</h3>
                    </div>
                    <button onclick="closeLogDetail()" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="logDetailContent" class="p-6 overflow-y-auto max-h-[65vh]">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store log data for modal
        const logsData = @json($logs->keyBy('id'));

        function showLogDetail(logId) {
            const log = logsData[logId];
            if (!log) return;

            let content = '';

            if (log.old_values && Object.keys(log.old_values).length > 0) {
                content += `
                    <div class="mb-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-left text-red-600 text-sm"></i>
                            </div>
                            <h4 class="font-bold text-slate-800">Nilai Lama</h4>
                        </div>
                        <pre class="bg-red-50 border border-red-100 p-4 rounded-xl text-sm overflow-x-auto text-red-800 font-mono">${JSON.stringify(log.old_values, null, 2)}</pre>
                    </div>
                `;
            }

            if (log.new_values && Object.keys(log.new_values).length > 0) {
                content += `
                    <div class="mb-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-right text-emerald-600 text-sm"></i>
                            </div>
                            <h4 class="font-bold text-slate-800">Nilai Baru</h4>
                        </div>
                        <pre class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-sm overflow-x-auto text-emerald-800 font-mono">${JSON.stringify(log.new_values, null, 2)}</pre>
                    </div>
                `;
            }

            if (log.metadata && Object.keys(log.metadata).length > 0) {
                content += `
                    <div class="mb-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                            </div>
                            <h4 class="font-bold text-slate-800">Metadata</h4>
                        </div>
                        <pre class="bg-blue-50 border border-blue-100 p-4 rounded-xl text-sm overflow-x-auto text-blue-800 font-mono">${JSON.stringify(log.metadata, null, 2)}</pre>
                    </div>
                `;
            }

            if (log.user_agent) {
                content += `
                    <div class="mb-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-globe text-slate-600 text-sm"></i>
                            </div>
                            <h4 class="font-bold text-slate-800">User Agent</h4>
                        </div>
                        <p class="text-sm text-slate-600 break-all bg-slate-50 border border-slate-100 p-4 rounded-xl">${log.user_agent}</p>
                    </div>
                `;
            }

            if (!content) {
                content = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-info-circle text-2xl text-slate-400"></i>
                        </div>
                        <p class="text-slate-500">Tidak ada detail tambahan</p>
                    </div>
                `;
            }

            document.getElementById('logDetailContent').innerHTML = content;
            document.getElementById('logDetailModal').classList.remove('hidden');
        }

        function closeLogDetail() {
            document.getElementById('logDetailModal').classList.add('hidden');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogDetail();
            }
        });
    </script>
</div>
@endsection
