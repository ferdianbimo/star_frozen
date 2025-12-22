{{-- Select/Dropdown Component with Error Handling --}}
{{-- Usage: <x-form-select name="role_id" label="Role" :options="$roles" /> --}}

@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => '',
    'required' => false,
    'placeholder' => 'Pilih...',
    'icon' => null,
    'valueKey' => 'id',
    'labelKey' => 'name'
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

    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full appearance-none bg-white border-2 rounded-xl px-4 py-2.5 pr-10 text-sm font-medium text-slate-700 focus:ring-4 transition-all cursor-pointer hover:border-slate-300 ' .
                          ($errors->has($name) ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10')
            ]) }}
        >
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $option)
                @php
                    $value = is_array($option) ? $option[$valueKey] : $option->$valueKey;
                    $label = is_array($option) ? $option[$labelKey] : $option->$labelKey;
                    $isSelected = old($name, $selected) == $value;
                @endphp
                <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
        </div>
    </div>

    <x-form-error :field="$name" />
</div>
