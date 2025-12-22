{{-- Error Validation Component --}}
{{-- Usage: <x-form-error field="email" /> --}}

@props(['field'])

@error($field)
    <div class="mt-1.5 flex items-start gap-2">
        <i class="fas fa-exclamation-circle text-red-500 text-sm mt-0.5 flex-shrink-0"></i>
        <p class="text-red-600 text-sm font-medium">{{ $message }}</p>
    </div>
@enderror
