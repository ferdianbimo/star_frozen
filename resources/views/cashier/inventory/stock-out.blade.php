<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Out Logs - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-green-800 text-white w-64 py-4 flex flex-col">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-green-200">Cashier Dashboard</p>
            </div>
            
            <nav class="flex-1">
                <a href="{{ route('cashier.dashboard') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('cashier.pos.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-cash-register mr-2"></i> Point of Sale
                </a>
                
                <a href="{{ route('cashier.inventory.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-boxes mr-2"></i> Inventory
                </a>
                
                <a href="{{ route('cashier.inventory.stock-out') }}" class="block py-2 px-4 bg-green-900 text-white">
                    <i class="fas fa-sign-out-alt mr-2"></i> Stok Keluar
                </a>
            </nav>
            
            <div class="px-4 py-2 mt-auto border-t border-green-700">
                <div class="flex items-center mb-2">
                    <span class="rounded-full bg-green-600 w-8 h-8 flex items-center justify-center mr-2">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-sm text-green-300 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow">
                <div class="py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div>
                        <a href="{{ route('cashier.inventory.index') }}" class="text-green-500 hover:text-green-700">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">Stok Keluar (Stock Out Logs)</h1>
                    </div>
                </div>
            </header>

            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="logsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prev Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="logsTbody">
                                @forelse($logs as $log)
                                <tr data-log-id="{{ $log->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                        @if($log->product && $log->product->image)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($log->product->image) }}" alt="{{ $log->product->name }}" class="h-8 w-8 rounded object-cover mr-3">
                                        @endif
                                        <div class="text-sm font-medium text-gray-900">{{ $log->product ? $log->product->name : '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ abs($log->change) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->previous_stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->new_stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->user ? $log->user->name : 'system' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->note }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No stock-out logs found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        (function(){
            const pollInterval = 5000; // 5s
            let lastId = null;
            const tbody = document.getElementById('logsTbody');

            function updateLastIdFromDOM(){
                const first = tbody.querySelector('tr[data-log-id]');
                if(first) lastId = parseInt(first.getAttribute('data-log-id'));
            }

            function renderRow(item){
                const tr = document.createElement('tr');
                tr.setAttribute('data-log-id', item.id);
                const productCellContent = item.image ?
                    `<div class="flex items-center"><img src="${escapeHtml(item.image)}" class="h-8 w-8 rounded object-cover mr-3"> <div class="text-sm font-medium text-gray-900">${escapeHtml(item.product)}</div></div>` :
                    `<div class="text-sm font-medium text-gray-900">${escapeHtml(item.product)}</div>`;

                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.created_human}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${productCellContent}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${Math.abs(item.change)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${item.previous_stock}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${item.new_stock}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(item.user)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHtml(item.note || '')}</td>
                `;
                return tr;
            }

            function escapeHtml(str){
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            async function poll(){
                try{
                    const url = new URL('{{ route('cashier.inventory.stock-out.updates') }}', window.location.origin);
                    if(lastId) url.searchParams.set('since_id', lastId);
                    const res = await fetch(url.toString(), {cache: 'no-store'});
                    if(!res.ok) return;
                    const json = await res.json();
                    if(json.logs && json.logs.length){
                        // prepend newest first
                        json.logs.reverse().forEach(item =>{
                            const row = renderRow(item);
                            tbody.insertBefore(row, tbody.firstChild);
                        });
                        updateLastIdFromDOM();
                        // keep table to reasonable size
                        while(tbody.children.length > 200){ tbody.removeChild(tbody.lastChild); }
                    }
                }catch(e){
                    console.error('Polling error', e);
                }
            }

            // Initialize lastId based on current DOM
            updateLastIdFromDOM();
            // Start polling loop
            setInterval(poll, pollInterval);
        })();
    </script>
</body>
</html>
