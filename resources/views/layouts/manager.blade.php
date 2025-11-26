<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manager') - Star Frozen POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="bg-blue-800 text-white w-64 flex-shrink-0 overflow-y-auto">
            <!-- Logo -->
            <div class="p-6 border-b border-blue-700">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-blue-200 mt-1">Manager Panel</p>
            </div>

            <!-- Navigation -->
            <nav class="p-4">
                <a href="{{ route('manager.dashboard') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors {{ request()->routeIs('manager.dashboard') ? 'bg-blue-900 text-white' : 'hover:bg-blue-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('manager.inventory.index') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors {{ request()->routeIs('manager.inventory.*') ? 'bg-blue-900 text-white' : 'hover:bg-blue-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Inventory
                </a>

                <a href="{{ route('manager.finance.index') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors {{ request()->routeIs('manager.finance.*') ? 'bg-blue-900 text-white' : 'hover:bg-blue-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Keuangan
                </a>

                <a href="{{ route('manager.access.index') }}" 
                   class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors {{ request()->routeIs('manager.access.*') || request()->routeIs('manager.users.*') || request()->routeIs('manager.roles.*') ? 'bg-blue-900 text-white' : 'hover:bg-blue-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Hak Akses
                </a>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 w-64 p-4 border-t border-blue-700 bg-blue-800">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3">
                        <span class="text-lg font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-200 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-sm bg-blue-700 hover:bg-blue-600 rounded-lg transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                        <div class="text-sm text-gray-600">
                            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-6">
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

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
