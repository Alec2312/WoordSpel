@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-sm']) }}>
    {{ $value ?? $slot }}
</label>
