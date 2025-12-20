<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manager') - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/30">
                            <i class="fas fa-store text-white text-lg"></i>
                        </div>
                        <div>
                            <div class="text-base font-bold text-white">Star Frozen</div>
                            <div class="text-xs text-indigo-400 font-medium flex items-center gap-1">
                                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>
                                Manager Panel
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
                
                <a href="{{ route('manager.dashboard') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('manager.dashboard') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('manager.dashboard') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-chart-pie {{ request()->routeIs('manager.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>

                <a href="{{ route('manager.inventory.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('manager.inventory.*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('manager.inventory.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-boxes {{ request()->routeIs('manager.inventory.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Inventaris</span>
                </a>

                <a href="{{ route('manager.finance.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('manager.finance.*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('manager.finance.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-wallet {{ request()->routeIs('manager.finance.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Keuangan</span>
                </a>

                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-3 px-3">Administrasi</div>

                <a href="{{ route('manager.access.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('manager.access.*') || request()->routeIs('manager.users.*') || request()->routeIs('manager.roles.*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('manager.access.*') || request()->routeIs('manager.users.*') || request()->routeIs('manager.roles.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-users-cog {{ request()->routeIs('manager.access.*') || request()->routeIs('manager.users.*') || request()->routeIs('manager.roles.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Hak Akses</span>
                </a>

                <a href="{{ route('manager.activity-logs.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('manager.activity-logs.*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('manager.activity-logs.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-clipboard-list {{ request()->routeIs('manager.activity-logs.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Log Aktivitas</span>
                </a>

                <a href="{{ route('manager.receipt-settings.index') }}" class="nav-link group flex items-center px-4 py-3 rounded-xl mb-1 {{ request()->routeIs('manager.receipt-settings.*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg {{ request()->routeIs('manager.receipt-settings.*') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-slate-600/50' }} flex items-center justify-center mr-3 transition-colors">
                        <i class="fas fa-receipt {{ request()->routeIs('manager.receipt-settings.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"></i>
                    </div>
                    <span class="text-sm font-medium">Pengaturan Struk</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="border-t border-slate-700/50 p-4">
                <div class="bg-slate-800/50 rounded-xl p-3 mb-3">
                    <div class="flex items-center">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-xl object-cover mr-3 shadow-md">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 text-white font-bold shadow-md">
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
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-store text-white text-sm"></i>
                    </div>
                    <span class="font-semibold text-slate-800">Star Frozen</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
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
