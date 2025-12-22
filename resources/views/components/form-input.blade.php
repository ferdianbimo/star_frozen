{{-- Input Field Component with Error Handling --}}
{{-- Usage: <x-form-input name="email" label="Email Address" type="email" :required="true" /> --}}

@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'icon' => null
])

<div>
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-2">
        @if($icon)
        <i class="{{ $icon }} mr-1 text-slate-400"></i>
        @endif
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full px-4 py-2.5 bg-white border-2 rounded-xl text-sm font-medium text-slate-700 focus:ring-4 transition-all hover:border-slate-300 ' .
                      ($errors->has($name) ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10'),
            'placeholder' => $placeholder
        ]) }}
    >

    <x-form-error :field="$name" />
</div>
