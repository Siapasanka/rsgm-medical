@props([
    'label',
    'value' => 0,
    'hint' => null,
    'tone' => 'slate',
])

@php
    $tones = [
        'blue' => [
            'bg' => '#eff6ff',
            'border' => '#bfdbfe',
            'label' => '#1d4ed8',
            'value' => '#1e3a8a',
            'hint' => '#2563eb',
        ],
        'emerald' => [
            'bg' => '#ecfdf5',
            'border' => '#a7f3d0',
            'label' => '#047857',
            'value' => '#064e3b',
            'hint' => '#059669',
        ],
        'amber' => [
            'bg' => '#fffbeb',
            'border' => '#fde68a',
            'label' => '#b45309',
            'value' => '#78350f',
            'hint' => '#d97706',
        ],
        'violet' => [
            'bg' => '#f5f3ff',
            'border' => '#c4b5fd',
            'label' => '#6d28d9',
            'value' => '#4c1d95',
            'hint' => '#7c3aed',
        ],
        'rose' => [
            'bg' => '#fff1f2',
            'border' => '#fecdd3',
            'label' => '#be123c',
            'value' => '#881337',
            'hint' => '#e11d48',
        ],
        'slate' => [
            'bg' => '#ffffff',
            'border' => '#e5e7eb',
            'label' => '#6b7280',
            'value' => '#1f2937',
            'hint' => '#6b7280',
        ],
    ];

    $style = $tones[$tone] ?? $tones['slate'];
@endphp

<div class="overflow-hidden shadow-sm sm:rounded-lg border" style="background-color: {{ $style['bg'] }}; border-color: {{ $style['border'] }};">
    <div class="p-4">
        <p class="text-xs uppercase tracking-wide" style="color: {{ $style['label'] }};">{{ $label }}</p>
        <p class="mt-2 text-2xl font-bold" style="color: {{ $style['value'] }};">{{ number_format($value) }}</p>
        @if($hint)
            <p class="mt-1 text-xs" style="color: {{ $style['hint'] }};">{{ $hint }}</p>
        @endif
    </div>
</div>
