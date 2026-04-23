@props(['active'])

@php
$classes = 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out';
$style = ($active ?? false)
            ? 'color:#c49a3c; border-color:#c49a3c; background:rgba(196,154,60,.08);'
            : 'color:#8aab97; border-color:transparent;';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $style]) }}>
    {{ $slot }}
</a>
