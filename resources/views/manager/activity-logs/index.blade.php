@extends('layouts.manager')

@section('title','Log Aktivitas')

@section('content')
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Log Aktivitas</h2>
                <p class="text-gray-600">Pantau semua aktivitas sistem secara realtime</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Live
                </span>
                <button onclick="location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Total Aktivitas</div>
                <div class="text-2xl font-bold text-gray-800">{{ $logs->total() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Login Hari Ini</div>
                <div class="text-2xl font-bold text-teal-600">
                    {{ \App\Models\ActivityLog::where('action', 'login')->whereDate('created_at', today())->count() }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Transaksi Hari Ini</div>
                <div class="text-2xl font-bold text-purple-600">
                    {{ \App\Models\ActivityLog::where('action', 'checkout')->whereDate('created_at', today())->count() }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Stok Masuk</div>
                <div class="text-2xl font-bold text-green-600">
                    {{ \App\Models\ActivityLog::where('action', 'stock_in')->whereDate('created_at', today())->count() }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Stok Keluar</div>
                <div class="text-2xl font-bold text-orange-600">
                    {{ \App\Models\ActivityLog::where('action', 'stock_out')->whereDate('created_at', today())->count() }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Perubahan Data</div>
                <div class="text-2xl font-bold text-blue-600">
                    {{ \App\Models\ActivityLog::whereIn('action', ['create', 'update', 'delete'])->whereDate('created_at', today())->count() }}
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <form method="GET" action="{{ route('manager.activity-logs.index') }}" class="flex items-center space-x-4 flex-wrap gap-2">
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Modul</label>
                    <select name="module" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Modul</option>
                        <option value="auth" {{ request('module') == 'auth' ? 'selected' : '' }}>🔐 Autentikasi</option>
                        <option value="inventory" {{ request('module') == 'inventory' ? 'selected' : '' }}>📦 Inventory</option>
                        <option value="pos" {{ request('module') == 'pos' ? 'selected' : '' }}>🛒 POS</option>
                        <option value="product" {{ request('module') == 'product' ? 'selected' : '' }}>📋 Produk</option>
                        <option value="user" {{ request('module') == 'user' ? 'selected' : '' }}>👤 User</option>
                        <option value="finance" {{ request('module') == 'finance' ? 'selected' : '' }}>💰 Keuangan</option>
                        <option value="batch" {{ request('module') == 'batch' ? 'selected' : '' }}>📦 Batch</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Aksi</label>
                    <select name="action" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                </div>
                <div>
                    <label class="text-sm text-gray-600 block mb-1">User</label>
                    <select name="user_id" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua User</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                        class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                        class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                        Filter
                    </button>
                    @if(request()->hasAny(['module', 'action', 'user_id', 'date_from', 'date_to']))
                    <a href="{{ route('manager.activity-logs.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Activity Logs Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    @php
                        $actionColors = [
                            'create' => 'bg-green-100 text-green-800',
                            'update' => 'bg-blue-100 text-blue-800',
                            'delete' => 'bg-red-100 text-red-800',
                            'stock_in' => 'bg-indigo-100 text-indigo-800',
                            'stock_out' => 'bg-orange-100 text-orange-800',
                            'checkout' => 'bg-purple-100 text-purple-800',
                            'login' => 'bg-teal-100 text-teal-800',
                            'logout' => 'bg-gray-100 text-gray-800',
                            'export' => 'bg-yellow-100 text-yellow-800',
                        ];
                        $actionColor = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                        
                        $moduleIcons = [
                            'inventory' => '📦',
                            'pos' => '🛒',
                            'product' => '📋',
                            'user' => '👤',
                            'auth' => '🔐',
                            'report' => '📊',
                        ];
                        $moduleIcon = $moduleIcons[$log->module] ?? '📝';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="font-medium">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</div>
                            @if($log->ip_address)
                                <div class="text-xs text-gray-400">{{ $log->ip_address }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $actionColor }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <span class="mr-1">{{ $moduleIcon }}</span>
                            {{ ucfirst($log->module) }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            @if($log->old_values || $log->new_values || $log->metadata)
                                <button onclick="showLogDetail({{ $log->id }})" 
                                    class="text-blue-600 hover:underline text-sm">
                                    Lihat Detail
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <p class="text-lg">Belum ada log aktivitas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Log Detail Modal -->
    <div id="logDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Detail Log Aktivitas</h3>
                <button onclick="closeLogDetail()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="logDetailContent" class="p-4 overflow-y-auto max-h-[60vh]">
                <!-- Content will be loaded here -->
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
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Nilai Lama:</h4>
                        <pre class="bg-red-50 p-3 rounded text-sm overflow-x-auto">${JSON.stringify(log.old_values, null, 2)}</pre>
                    </div>
                `;
            }

            if (log.new_values && Object.keys(log.new_values).length > 0) {
                content += `
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Nilai Baru:</h4>
                        <pre class="bg-green-50 p-3 rounded text-sm overflow-x-auto">${JSON.stringify(log.new_values, null, 2)}</pre>
                    </div>
                `;
            }

            if (log.metadata && Object.keys(log.metadata).length > 0) {
                content += `
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Metadata:</h4>
                        <pre class="bg-blue-50 p-3 rounded text-sm overflow-x-auto">${JSON.stringify(log.metadata, null, 2)}</pre>
                    </div>
                `;
            }

            if (log.user_agent) {
                content += `
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">User Agent:</h4>
                        <p class="text-sm text-gray-600 break-all">${log.user_agent}</p>
                    </div>
                `;
            }

            if (!content) {
                content = '<p class="text-gray-500">Tidak ada detail tambahan</p>';
            }

            document.getElementById('logDetailContent').innerHTML = content;
            document.getElementById('logDetailModal').classList.remove('hidden');
        }

        function closeLogDetail() {
            document.getElementById('logDetailModal').classList.add('hidden');
        }

        // Close modal on backdrop click
        document.getElementById('logDetailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogDetail();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogDetail();
            }
        });

        // Auto-refresh every 30 seconds (optional - uncomment to enable)
        // setInterval(function() {
        //     location.reload();
        // }, 30000);
    </script>
@endsection
