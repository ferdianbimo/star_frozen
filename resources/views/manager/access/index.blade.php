<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
                <a href="{{ route('manager.dashboard') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('manager.inventory.index') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-boxes mr-2"></i> Inventory
                </a>
                
                <a href="{{ route('manager.finance.index') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-dollar-sign mr-2"></i> Keuangan
                </a>
                
                <a href="{{ route('manager.access.index') }}" class="block py-2 px-4 bg-blue-900 text-white">
                    <i class="fas fa-user-lock mr-2"></i> Hak Akses
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
            <div class="p-8">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Hak Akses</h2>
                    <p class="text-gray-600">Kelola users dan roles</p>
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

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Total Users</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Total Roles</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_roles'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Managers</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['manager_count'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-gray-500 text-sm">Cashiers</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['cashier_count'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Users Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold">Users</h3>
                            <a href="{{ route('manager.users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                + Add User
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Name</th>
                                        <th class="text-left py-2">Email</th>
                                        <th class="text-left py-2">Role</th>
                                        <th class="text-left py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2">{{ $user->name }}</td>
                                        <td class="py-2">{{ $user->email }}</td>
                                        <td class="py-2">
                                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                                {{ $user->role->display_name ?? ucfirst($user->role->name) }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <a href="{{ route('manager.users.edit', $user) }}" class="text-blue-600 hover:underline text-sm mr-2">Edit</a>
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('manager.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>

                    <!-- Roles Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold">Roles</h3>
                            <a href="{{ route('manager.roles.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                + Add Role
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($roles as $role)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-lg">{{ $role->display_name ?? ucfirst($role->name) }}</h4>
                                        <p class="text-sm text-gray-600">{{ $role->users_count }} users</p>
                                        @if($role->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('manager.roles.edit', $role) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                                        @if(!in_array($role->name, ['manager', 'kasir']))
                                        <form action="{{ route('manager.roles.destroy', $role) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
