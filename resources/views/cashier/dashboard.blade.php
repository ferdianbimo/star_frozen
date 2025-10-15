<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
                <a href="{{ route('cashier.dashboard') }}" class="block py-2 px-4 bg-green-900 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('cashier.pos.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-cash-register mr-2"></i> Point of Sale
                </a>
                
                <a href="{{ route('cashier.inventory.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-boxes mr-2"></i> Inventory
                </a>
                
                <a href="{{ route('cashier.transactions.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-receipt mr-2"></i> My Transactions
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
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">Cashier Dashboard</h1>
                </div>
            </header>

            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <!-- Quick Access -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <a href="{{ route('cashier.pos.index') }}" class="block bg-white rounded-lg shadow p-6 hover:bg-green-50 transition">
                        <div class="flex items-center">
                            <div class="bg-green-500 rounded-full p-3 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-medium text-gray-900">New Transaction</h2>
                                <p class="mt-1 text-sm text-gray-600">Start a new sales transaction</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('cashier.inventory.index') }}" class="block bg-white rounded-lg shadow p-6 hover:bg-yellow-50 transition">
                        <div class="flex items-center">
                            <div class="bg-yellow-500 rounded-full p-3 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-medium text-gray-900">Inventory</h2>
                                <p class="mt-1 text-sm text-gray-600">Manage product stock</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('cashier.transactions.index') }}" class="block bg-white rounded-lg shadow p-6 hover:bg-blue-50 transition">
                        <div class="flex items-center">
                            <div class="bg-blue-500 rounded-full p-3 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-medium text-gray-900">Today's Transactions</h2>
                                <p class="mt-1 text-sm text-gray-600">View your sales history</p>
                            </div>
                        </div>
                    </a>
                </div>
                
                <!-- Stats Overview -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Today's Summary</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600 mb-1">Total Sales</p>
                            <p class="text-2xl font-bold text-gray-900">Rp 0</p>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 mb-1">Transactions</p>
                            <p class="text-2xl font-bold text-gray-900">0</p>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-purple-600 mb-1">Items Sold</p>
                            <p class="text-2xl font-bold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
                    </div>
                    <div class="bg-white overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" colspan="5">No transactions yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
