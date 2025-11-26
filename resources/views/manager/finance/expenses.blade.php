<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengeluaran - Star Frozen POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar (sama seperti sebelumnya) -->
        <aside class="w-64 bg-blue-900 text-white flex flex-col">
            <div class="p-6 bg-blue-950">
                <h1 class="text-2xl font-bold">Star Frozen</h1>
                <p class="text-blue-300 text-sm">POS Manager</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-800 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('manager.inventory.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-800 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Inventory
                </a>
                <a href="{{ route('manager.finance.index') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-800 rounded-lg font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/></svg>
                    Keuangan
                </a>
                <a href="{{ route('manager.access.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-800 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                    Hak Akses
                </a>
            </nav>
            <div class="p-4 border-t border-blue-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-300 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium transition">Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-8 py-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Kelola Pengeluaran</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('manager.finance.index') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition">Kembali</a>
                        <button onclick="openModal('add')" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">+ Tambah Pengeluaran</button>
                    </div>
                </div>
            </header>

            <div class="p-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
                @endif

                <!-- Filter & Search -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori atau deskripsi..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">Filter</button>
                            <a href="{{ route('manager.finance.expenses') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition">Reset</a>
                            <button type="submit" name="export" value="pdf" class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition ml-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Export PDF
                            </button>
                            <button type="submit" name="export" value="excel" class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Excel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Expenses Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Kategori</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Deskripsi</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Jumlah</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Dicatat Oleh</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($expenses as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->category }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($expense->description, 50) }}</td>
                                <td class="px-6 py-4 text-sm text-right font-semibold text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->user->name }}</td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick='editExpense(@json($expense))' class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                                    <button onclick='deleteExpense({{ $expense->id }})' class="text-red-600 hover:text-red-800">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">Tidak ada data pengeluaran</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $expenses->links() }}
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Add/Edit -->
    <div id="expenseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-lg">
            <h2 id="modalTitle" class="text-2xl font-bold mb-6">Tambah Pengeluaran</h2>
            <form id="expenseForm" method="POST">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <input type="text" name="category" id="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., Gaji Karyawan, Listrik, Sewa">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" id="description" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Detail pengeluaran..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Rp)</label>
                        <input type="number" name="amount" id="amount" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="expense_date" id="expense_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">Simpan</button>
                    <button type="button" onclick="closeModal()" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openModal(mode) {
            document.getElementById('expenseModal').classList.remove('hidden');
            if (mode === 'add') {
                document.getElementById('modalTitle').textContent = 'Tambah Pengeluaran';
                document.getElementById('expenseForm').action = '{{ route("manager.finance.expenses.store") }}';
                document.getElementById('methodField').value = 'POST';
                document.getElementById('expenseForm').reset();
                document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
            }
        }

        function editExpense(expense) {
            document.getElementById('expenseModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Pengeluaran';
            document.getElementById('expenseForm').action = '/manager/finance/expenses/' + expense.id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('category').value = expense.category;
            document.getElementById('description').value = expense.description;
            document.getElementById('amount').value = expense.amount;
            document.getElementById('expense_date').value = expense.expense_date;
        }

        function closeModal() {
            document.getElementById('expenseModal').classList.add('hidden');
        }

        function deleteExpense(id) {
            if (confirm('Yakin ingin menghapus pengeluaran ini?')) {
                const form = document.getElementById('deleteForm');
                form.action = '/manager/finance/expenses/' + id;
                form.submit();
            }
        }
    </script>
</body>
</html>
