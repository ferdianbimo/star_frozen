<!-- Global Action Modal Component -->
<div id="actionModal" class="fixed inset-0 z-[9999] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeActionModal()"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div id="actionModalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0" 
             style="animation: modalIn 0.3s ease-out forwards;">
            <!-- Modal will be populated by JavaScript -->
        </div>
    </div>
</div>

@if(session('success') || session('error') || session('warning') || session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showActionModal('success', 'Berhasil!', {!! json_encode(session('success')) !!});
        @endif
        
        @if(session('error'))
            showActionModal('error', 'Gagal!', {!! json_encode(session('error')) !!});
        @endif
        
        @if(session('warning'))
            showActionModal('warning', 'Perhatian!', {!! json_encode(session('warning')) !!});
        @endif
        
        @if(session('info'))
            showActionModal('info', 'Informasi', {!! json_encode(session('info')) !!});
        @endif
    });
</script>
@endif

<style>
    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    @keyframes modalOut {
        from {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        to {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
    }
</style>

<script>
    function showActionModal(type, title, message, options = {}) {
        const modal = document.getElementById('actionModal');
        const content = document.getElementById('actionModalContent');
        if (!modal || !content) return;
        
        const configs = {
            success: {
                gradient: 'from-emerald-500 to-teal-600',
                iconBg: 'bg-emerald-100',
                iconColor: 'text-emerald-600',
                icon: 'fa-check-circle',
                btnClass: 'bg-gradient-to-r from-emerald-500 to-teal-600 hover:shadow-emerald-500/30'
            },
            error: {
                gradient: 'from-red-500 to-rose-600',
                iconBg: 'bg-red-100',
                iconColor: 'text-red-600',
                icon: 'fa-times-circle',
                btnClass: 'bg-gradient-to-r from-red-500 to-rose-600 hover:shadow-red-500/30'
            },
            warning: {
                gradient: 'from-amber-500 to-orange-600',
                iconBg: 'bg-amber-100',
                iconColor: 'text-amber-600',
                icon: 'fa-exclamation-triangle',
                btnClass: 'bg-gradient-to-r from-amber-500 to-orange-600 hover:shadow-amber-500/30'
            },
            info: {
                gradient: 'from-blue-500 to-indigo-600',
                iconBg: 'bg-blue-100',
                iconColor: 'text-blue-600',
                icon: 'fa-info-circle',
                btnClass: 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:shadow-blue-500/30'
            },
            confirm: {
                gradient: 'from-slate-600 to-slate-700',
                iconBg: 'bg-slate-100',
                iconColor: 'text-slate-600',
                icon: 'fa-question-circle',
                btnClass: 'bg-gradient-to-r from-red-500 to-rose-600 hover:shadow-red-500/30'
            }
        };
        
        const config = configs[type] || configs.info;
        
        let buttonsHtml = '';
        if (type === 'confirm' && options.onConfirm) {
            buttonsHtml = `
                <div class="flex gap-3 mt-6">
                    <button onclick="closeActionModal()" class="flex-1 px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                        Batal
                    </button>
                    <button onclick="handleConfirmAction()" class="flex-1 px-5 py-3 rounded-xl ${config.btnClass} text-white font-semibold hover:shadow-lg transition-all">
                        ${options.confirmText || 'Ya, Lanjutkan'}
                    </button>
                </div>
            `;
            window._confirmCallback = options.onConfirm;
        } else {
            buttonsHtml = `
                <button onclick="closeActionModal()" class="w-full px-5 py-3 rounded-xl ${config.btnClass} text-white font-semibold hover:shadow-lg transition-all mt-6">
                    OK
                </button>
            `;
        }
        
        content.innerHTML = `
            <div class="p-6 text-center">
                <div class="w-20 h-20 ${config.iconBg} rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas ${config.icon} text-4xl ${config.iconColor}"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">${title}</h3>
                <p class="text-slate-600">${message}</p>
                ${buttonsHtml}
            </div>
        `;
        
        modal.classList.remove('hidden');
        content.style.animation = 'modalIn 0.3s ease-out forwards';
        
        // Auto close after 3 seconds for success/info (not for confirm)
        if (type !== 'confirm' && !options.persistent) {
            setTimeout(() => {
                closeActionModal();
            }, 3000);
        }
    }
    
    function closeActionModal() {
        const modal = document.getElementById('actionModal');
        const content = document.getElementById('actionModalContent');
        if (!modal || !content) return;
        
        content.style.animation = 'modalOut 0.2s ease-in forwards';
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
    
    function handleConfirmAction() {
        if (window._confirmCallback) {
            window._confirmCallback();
        }
        closeActionModal();
    }
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeActionModal();
        }
    });
    
    // Helper function to show confirm dialog
    function confirmAction(message, onConfirm, options = {}) {
        showActionModal('confirm', options.title || 'Konfirmasi', message, {
            onConfirm: onConfirm,
            confirmText: options.confirmText || 'Ya, Lanjutkan'
        });
    }
</script>
