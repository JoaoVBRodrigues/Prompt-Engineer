@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-slate-400 font-mono mb-1']) }}>
    {{ $value ?? $slot }}
</label>
