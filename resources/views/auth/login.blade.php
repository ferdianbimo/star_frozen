<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Branding (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern"></div>
            
            <!-- Decorative circles -->
            <div class="absolute -top-20 -left-20 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/4 w-48 h-48 bg-blue-400/10 rounded-full blur-2xl floating"></div>
            
            <div class="relative z-10 flex flex-col items-center justify-center w-full px-12">
                <!-- Logo -->
                <div class="mb-8 floating">
                    <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center shadow-2xl border border-white/20">
                     <i class="fas fa-store text-5xl text-white"></i>    
                    </div>
                </div>
                
                <h1 class="text-4xl xl:text-5xl font-bold text-white text-center mb-4">Star Frozen</h1>
                <p class="text-blue-100 text-lg text-center max-w-md mb-8">Sistem Point of Sale Modern untuk Bisnis Frozen Food Anda</p>
                
                <!-- Features -->
                <div class="space-y-4 w-full max-w-sm">
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-4 border border-white/10">
                        <div class="w-12 h-12 bg-blue-500/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bolt text-xl text-yellow-300"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">Transaksi Cepat</h3>
                            <p class="text-blue-200 text-sm">Proses checkout dalam hitungan detik</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-4 border border-white/10">
                        <div class="w-12 h-12 bg-emerald-500/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-pie text-xl text-emerald-300"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">Laporan Real-time</h3>
                            <p class="text-blue-200 text-sm">Pantau bisnis Anda kapan saja</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-4 border border-white/10">
                        <div class="w-12 h-12 bg-purple-500/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-boxes text-xl text-purple-300"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">Manajemen Stok</h3>
                            <p class="text-blue-200 text-sm">Kelola inventaris dengan mudah</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 lg:py-0">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl shadow-blue-500/30 mb-4">
                        <i class="fas fa-snowflake text-4xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Star Frozen POS</h1>
                    <p class="text-slate-400 text-sm mt-1">Sistem Point of Sale</p>
                </div>

                <!-- Login Card -->
                <div class="bg-white/5 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/10 p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <div class="hidden lg:inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg shadow-blue-500/30 mb-4">
                            <i class="fas fa-user text-2xl text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-white">Selamat Datang!</h2>
                        <p class="text-slate-400 mt-2">Silakan masuk ke akun Anda</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2" for="email">
                                <i class="fas fa-envelope mr-2 text-blue-400"></i>Email
                            </label>
                            <div class="relative">
                                <input 
                                    class="w-full bg-slate-800/50 border border-slate-600/50 text-white rounded-xl px-4 py-3.5 pl-12 focus:outline-none focus:border-blue-500 input-focus transition-all placeholder-slate-500 @error('email') border-red-500 @enderror" 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    placeholder="nama@email.com"
                                    required 
                                    autofocus>
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                    <i class="fas fa-at"></i>
                                </div>
                            </div>
                            @error('email')
                                <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2" for="password">
                                <i class="fas fa-lock mr-2 text-blue-400"></i>Password
                            </label>
                            <div class="relative">
                                <input 
                                    class="w-full bg-slate-800/50 border border-slate-600/50 text-white rounded-xl px-4 py-3.5 pl-12 pr-12 focus:outline-none focus:border-blue-500 input-focus transition-all placeholder-slate-500 @error('password') border-red-500 @enderror" 
                                    id="password" 
                                    type="password" 
                                    name="password"
                                    placeholder="••••••••"
                                    required>
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                    <i class="fas fa-key"></i>
                                </div>
                                <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember" 
                                    class="w-5 h-5 rounded-md bg-slate-800/50 border-slate-600 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <span class="ml-3 text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Ingat saya</span>
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-4 rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-300 flex items-center justify-center gap-2 group">
                            <span>Masuk</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-8 pt-6 border-t border-slate-700/50">
                        <p class="text-center text-slate-500 text-sm">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Koneksi aman dan terenkripsi
                        </p>
                    </div>
                </div>

                <!-- Copyright -->
                <p class="text-center text-slate-600 text-sm mt-6">
                    &copy; {{ date('Y') }} Star Frozen POS. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('error', 'Error!', {!! json_encode(session('error')) !!});
        });
    </script>
    @endif

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function showToast(type, title, message, duration = 4000) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = `toast-item pointer-events-auto transform translate-x-full opacity-0 transition-all duration-300 ease-out`;
            
            const isSuccess = type === 'success';
            const bgGradient = isSuccess 
                ? 'from-emerald-500 to-teal-600' 
                : 'from-red-500 to-rose-600';
            const icon = isSuccess 
                ? '<i class="fas fa-check"></i>' 
                : '<i class="fas fa-exclamation"></i>';
            
            toast.innerHTML = `
                <div class="bg-gradient-to-r ${bgGradient} text-white px-5 py-4 rounded-xl shadow-2xl min-w-[280px] sm:min-w-[320px] max-w-[90vw] sm:max-w-[420px] flex items-start gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                        ${icon}
                    </div>
                    <div class="flex-1 pt-0.5">
                        <p class="font-semibold text-sm">${title}</p>
                        <p class="text-sm text-white/90 mt-0.5">${message}</p>
                    </div>
                    <button onclick="closeToast(this)" class="text-white/70 hover:text-white transition-colors flex-shrink-0 mt-0.5">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="h-1 bg-white/30 rounded-full mt-1 mx-2 overflow-hidden">
                    <div class="h-full bg-white/70 rounded-full" style="width: 100%; transition: width ${duration}ms linear;" id="progress-bar"></div>
                </div>
            `;
            
            container.appendChild(toast);
            
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
                
                const progressBar = toast.querySelector('#progress-bar');
                if (progressBar) {
                    requestAnimationFrame(() => {
                        progressBar.style.width = '0%';
                    });
                }
            });
            
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
        
        function closeToast(button) {
            const toast = button.closest('.toast-item');
            if (toast) {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }
        }
    </script>
</body>
</html>
