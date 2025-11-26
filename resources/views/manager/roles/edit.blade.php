<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role - Star Frozen POS</title>
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
                    <div class="flex items-center mb-2">
                        <a href="{{ route('manager.access.index') }}" class="text-blue-600 hover:underline mr-2">← Back</a>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800">Edit Role</h2>
                    <p class="text-gray-600">Update role information</p>
                </div>

                @if(in_array($role->name, ['manager', 'kasir']))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    <strong>Warning:</strong> This is a default system role. Changing it may affect system functionality.
                </div>
                @endif

                <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                    <form method="POST" action="{{ route('manager.roles.update', $role) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Role Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                placeholder="e.g., supervisor, admin"
                                {{ in_array($role->name, ['manager', 'kasir']) ? 'readonly' : '' }}>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @if(in_array($role->name, ['manager', 'kasir']))
                                <p class="text-gray-500 text-xs mt-1">Default role name cannot be changed</p>
                            @else
                                <p class="text-gray-500 text-xs mt-1">Use lowercase, no spaces (use underscore instead)</p>
                            @endif
                        </div>

                        <!-- Display Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="display_name">
                                Display Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role->display_name) }}" required
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-500 @enderror"
                                placeholder="e.g., Supervisor, Administrator">
                            @error('display_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Brief description of this role's responsibilities">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-bold">
                                Update Role
                            </button>
                            <a href="{{ route('manager.access.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
