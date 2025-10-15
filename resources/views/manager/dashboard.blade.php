<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 py-4 flex flex-col">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-blue-200">Manager Dashboard</p>
            </div>
            
            <nav class="flex-1">
                <a href="{{ route('manager.dashboard') }}" class="block py-2 px-4 bg-blue-900 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('manager.users.index') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-users mr-2"></i> Users
                </a>
                
                <a href="{{ route('manager.reports.sales') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-chart-line mr-2"></i> Reports
                </a>
            </nav>
            
            <div class="px-4 py-2 mt-auto border-t border-blue-700">
                <div class="flex items-center mb-2">
                    <span class="rounded-full bg-blue-600 w-8 h-8 flex items-center justify-center mr-2">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-sm text-blue-300 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow">
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                </div>
            </header>

            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="text-sm font-medium text-gray-500 mb-1">Total Users</h2>
                        <p class="text-2xl font-bold text-blue-600">2</p>
                        <p class="text-xs text-gray-500 mt-1">All users</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="text-sm font-medium text-gray-500 mb-1">Active Cashiers</h2>
                        <p class="text-2xl font-bold text-blue-600">1</p>
                        <p class="text-xs text-gray-500 mt-1">Logged in today</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="text-sm font-medium text-gray-500 mb-1">Reports Generated</h2>
                        <p class="text-2xl font-bold text-blue-600">0</p>
                        <p class="text-xs text-gray-500 mt-1">This month</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="text-sm font-medium text-gray-500 mb-1">System Health</h2>
                        <p class="text-2xl font-bold text-green-600">Good</p>
                        <p class="text-xs text-gray-500 mt-1">No issues detected</p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="font-medium text-gray-700 mb-3">User Activity</h2>
                        <canvas id="userActivityChart" height="200"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="font-medium text-gray-700 mb-3">System Resources</h2>
                        <canvas id="systemResourcesChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg font-medium text-gray-900">Recent User Activity</h3>
                    </div>
                    <div class="bg-white overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" colspan="3">No activity logged yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Sample charts
        const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
        const userActivityChart = new Chart(userActivityCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Login Activity',
                    data: [1, 2, 1, 3, 2, 0, 1],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const systemResourcesCtx = document.getElementById('systemResourcesChart').getContext('2d');
        const systemResourcesChart = new Chart(systemResourcesCtx, {
            type: 'bar',
            data: {
                labels: ['CPU', 'Memory', 'Disk', 'Network', 'Sessions'],
                datasets: [{
                    label: 'Usage %',
                    data: [25, 40, 30, 15, 10],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(139, 92, 246, 0.7)'
                    ]
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>
</html>
