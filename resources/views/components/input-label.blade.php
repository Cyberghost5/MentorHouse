@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm', 'style' => 'color:#1a3327;']) }}>
    {{ $value ?? $slot }}
</label>
