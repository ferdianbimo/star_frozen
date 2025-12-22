<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Star Frozen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <!-- Login Card -->
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-slate-100">

            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-2xl mb-4">
                    <i class="fas fa-snowflake text-2xl text-white"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-800 mb-1">Star Frozen</h1>
                <p class="text-sm text-slate-500">POS & Inventory System</p>
            </div>

            <!-- Login Title -->
            <h2 class="text-2xl font-bold text-center text-slate-800 mb-6">Login</h2>

            <!-- Role Tabs -->
            <div class="flex gap-2 mb-6">
                <button
                    type="button"
                    onclick="switchRole('manager')"
                    id="tab-manager"
                    class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors bg-blue-500 text-white">
                    Owner
                </button>
                <button
                    type="button"
                    onclick="switchRole('cashier')"
                    id="tab-cashier"
                    class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors bg-slate-100 text-slate-600 hover:bg-slate-200">
                    Karyawan
                </button>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- ID Field -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">ID</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', 'manager@starfrozen.com') }}"
                        placeholder="StarAdmin"
                        class="w-full px-4 py-3 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                        required
                        autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="************"
                            class="w-full px-4 py-3 pr-12 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                            required>
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 rounded-lg transition-colors duration-200 mt-6">
                    Masuk
                </button>
            </form>

        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-slate-500 mt-6">
            Version 1.2.0 - © 2024 Star Frozen
        </p>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-6 right-6 z-50 flex flex-col gap-3"></div>

    <script>
        // Role Switch
        function switchRole(role) {
            const managerTab = document.getElementById('tab-manager');
            const cashierTab = document.getElementById('tab-cashier');
            const emailInput = document.getElementById('email');

            if (role === 'manager') {
                managerTab.className = 'flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors bg-blue-500 text-white';
                cashierTab.className = 'flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors bg-slate-100 text-slate-600 hover:bg-slate-200';
                emailInput.value = 'manager@starfrozen.com';
            } else {
                cashierTab.className = 'flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors bg-blue-500 text-white';
                managerTab.className = 'flex-1 py-2.5 text-sm font-medium rounded-lg transition-colors bg-slate-100 text-slate-600 hover:bg-slate-200';
                emailInput.value = 'kasir@starfrozen.com';
            }
        }

        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Show Toast Messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif

        @if($errors->any())
            showToast('{{ $errors->first() }}', 'error');
        @endif

        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

            toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
