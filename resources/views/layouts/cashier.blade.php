<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier') - Star Frozen POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom sidebar scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.5);
            border-radius: 10px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.7);
        }
        /* Smooth transitions */
        .nav-link {
            transition: all 0.2s ease-in-out;
        }
        .nav-link:hover {
            transform: translateX(4px);
        }
        /* Mobile sidebar animation */
        .sidebar-overlay {
            transition: opacity 0.3s ease-in-out;
        }
        .sidebar-mobile {
            transition: transform 0.3s ease-in-out;
        }
        /* Hide scrollbar but keep functionality */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Custom Select Dropdown Styles */
        .custom-select {
            background-image: none;
        }
        .custom-select:focus {
            outline: none;
        }
        .custom-select option {
            padding: 12px 16px;
            background: white;
            color: #334155;
        }
        .custom-select option:checked {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            color: white;
        }
        .custom-select option:hover {
            background: #f1f5f9;
        }
        /* Modern input date styles */
        input[type="date"] {
            position: relative;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
        /* Custom scrollbar for tables and containers */
        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-mobile bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white w-64 flex-shrink-0 flex flex-col h-full shadow-xl fixed lg:relative z-50 -translate-x-full lg:translate-x-0">
            <!-- Logo Section -->
            <div class="px-5 py-6 border-b border-slate-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-3 shadow-lg shadow-blue-500/30">
                            <i class="fas fa-store text-white text-lg"></i>
                        </div>
                        <div>
                            <div class="text-base font-bold text-white">Star Frozen</div>
                            <div class="text-xs text-blue-400 font-medium flex items-center gap-1">
                                <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                                Cashier Panel
                            </div>
                        </div>
                    </div>
                    <!-- Close button for mobile -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white p-2">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 overflow-y-auto sidebar-scroll">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">Menu Utama</div>

                <a href="{{ route('cashier.dashboard') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('cashier.dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('cashier.dashboard') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-home {{ request()->routeIs('cashier.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>

                <a href="{{ route('cashier.pos.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('cashier.pos.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('cashier.pos.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-shopping-cart {{ request()->routeIs('cashier.pos.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Point of Sale</span>
                    @if(request()->routeIs('cashier.pos.*'))
                        <span class="ml-auto w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    @endif
                </a>

                <a href="{{ route('cashier.inventory.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('cashier.inventory.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('cashier.inventory.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-boxes {{ request()->routeIs('cashier.inventory.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Inventaris</span>
                </a>

                <a href="{{ route('cashier.transactions.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('cashier.transactions.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('cashier.transactions.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-receipt {{ request()->routeIs('cashier.transactions.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Riwayat Transaksi</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="border-t border-slate-700/50 p-4">
                <div class="bg-slate-800/50 rounded-xl p-3 mb-3">
                    <div class="flex items-center">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-xl object-cover mr-3 shadow-md">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-3 text-white font-bold shadow-md">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Action Modal Component -->
        <x-action-modal />

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-100 w-full">
            <!-- Mobile Header -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
                <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <i class="fas fa-store text-white text-sm"></i>
                    </div>
                    <span class="font-semibold text-slate-800">Star Frozen</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>

            <!-- Content -->
            <div class="p-4 sm:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // Close sidebar when clicking a link (mobile)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // Close sidebar on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar').classList.remove('-translate-x-full');
                document.getElementById('sidebarOverlay').classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
