<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
{{--# @lang('Olá!')--}}
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{--{{ $line }}--}}
Solicitação de redefinição de senha da sua conta.
@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{--{{ $actionText }}--}}
    Resetar Senha
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{--{{ $line }}--}}

@endforeach

{{-- Salutation --}}
{{--@if (! empty($salutation))--}}
{{--{{ $salutation }}--}}
{{--@else--}}
{{--Antenciosamente,<br>--}}
{{--{{ config('app.name') }}--}}
{{--@endif--}}

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
//    "Se você estiver com problemas para clicar no botão \":actionText\", copie e cole a URL abaixo.\n".
//    'Em seu navegador:',
"Caso não consiga acessar, utilize o link:",
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
