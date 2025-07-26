@props([
    'name',
    'label',
    'required' => false,
    'placeholder',
    'type' => 'text',
    'mask' => '',
    'errorMessage' => null,
    'disableInput' => false
])

<div class="text-black flex flex-col gap-0.5">
    <p class="text-gray-700 text-[13px] font-medium">{{ $label }} {{ $required ? '*' : '' }}</p>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? '' }}"
        {{ $disableInput ? 'disabled' : '' }}
        @if(!empty($mask) && $mask === 'CEP')
            x-mask="99999-999"
        @endif
        {{ $attributes }}
        @class([
            'border outline-none p-1.5 pl-3 rounded focus:border-blue-link invalid:border-red-500 text-sm',
            'border-gray-300' => !$errorMessage,
            'border-red-300' => $errorMessage,
            'bg-gray-200' => $disableInput
        ])>
    @if($errorMessage)
        <span class="text-red-500 text-[13px]">{{ $errorMessage }}</span>
    @endif
</div>
