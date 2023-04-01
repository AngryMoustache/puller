@props([
    'text' => $slot,
])

<h1 {{ $attributes->merge([
    'class' => 'font-semibold text-lg flex gap-8 items-center',
]) }}>
    <span>{{ $text }}</span>
    <div class="opacity-50 border-b border-border grow"></div>
</h1>
