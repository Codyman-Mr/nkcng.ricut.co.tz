@props([
    'color' => 'gray',
    'icon' => null,
])

@php
    $base = "px-3 py-1 text-xs font-medium border rounded flex items-center transition";
    $colors = [
        'gray' => 'text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700',
        'green' => 'text-green-700 dark:text-green-200 border-green-500 dark:border-green-600 hover:bg-green-100 dark:hover:bg-green-700',
        'blue' => 'text-blue-700 dark:text-blue-200 border-blue-500 dark:border-blue-600 hover:bg-blue-100 dark:hover:bg-blue-700',
        'red' => 'text-red-700 dark:text-red-200 border-red-500 dark:border-red-600 hover:bg-red-100 dark:hover:bg-red-700',
    ];
@endphp

<button {{ $attributes->merge(['class' => "$base {$colors[$color]}"]) }}>
    @if($icon)
        <x-dynamic-component :component="$icon" class="w-4 h-4 mr-1" />
    @endif
    {{ $slot }}
</button>
