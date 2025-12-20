<!-- Toast Container - Fixed Position Right Side -->
<div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none">
    <!-- Toasts will be inserted here -->
</div>

@if(session('success') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast('success', 'Berhasil!', {!! json_encode(session('success')) !!});
        @endif
        
        @if(session('error'))
            showToast('error', 'Error!', {!! json_encode(session('error')) !!});
        @endif
    });
</script>
@endif

<script>
    function showToast(type, title, message, duration = 4000) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        
        const toast = document.createElement('div');
        toast.className = `toast-item pointer-events-auto transform translate-x-full opacity-0 transition-all duration-300 ease-out`;
        
        const isSuccess = type === 'success';
        const bgGradient = isSuccess 
            ? 'from-emerald-500 to-teal-600' 
            : 'from-red-500 to-rose-600';
        const iconBg = isSuccess ? 'bg-white/20' : 'bg-white/20';
        const icon = isSuccess 
            ? '<i class="fas fa-check"></i>' 
            : '<i class="fas fa-exclamation"></i>';
        
        toast.innerHTML = `
            <div class="bg-gradient-to-r ${bgGradient} text-white px-5 py-4 rounded-xl shadow-2xl min-w-[320px] max-w-[420px] flex items-start gap-4 backdrop-blur-sm border border-white/10">
                <div class="w-10 h-10 ${iconBg} rounded-full flex items-center justify-center flex-shrink-0">
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
            <div class="toast-progress h-1 bg-white/30 rounded-full mt-1 mx-2 overflow-hidden">
                <div class="toast-progress-bar h-full bg-white/70 rounded-full" style="width: 100%; transition: width ${duration}ms linear;"></div>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Trigger animation
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
            
            // Start progress bar animation
            const progressBar = toast.querySelector('.toast-progress-bar');
            if (progressBar) {
                requestAnimationFrame(() => {
                    progressBar.style.width = '0%';
                });
            }
        });
        
        // Auto dismiss
        setTimeout(() => {
            dismissToast(toast);
        }, duration);
    }
    
    function closeToast(button) {
        const toast = button.closest('.toast-item');
        if (toast) {
            dismissToast(toast);
        }
    }
    
    function dismissToast(toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
</script>
