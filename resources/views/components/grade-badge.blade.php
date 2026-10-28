@props(['grade' => null, 'code' => null, 'size' => 'normal'])
@php
    $c = $code ?? $grade?->code;
    $color = \App\Models\Grade::colorFor($c);
    $fontSize = match($size) {
        'sm' => '0.7rem',
        'lg' => '0.82rem',
        default => '0.75rem',
    };
    $padding = match($size) {
        'sm' => '0.15rem 0.45rem',
        'lg' => '0.35rem 0.65rem',
        default => '0.2rem 0.55rem',
    };
@endphp
@if($c)
    <span class="badge fw-bold align-middle d-inline-flex align-items-center" style="background:{{ $color['bg'] }};color:{{ $color['text'] }};border:1px solid {{ $color['border'] }};border-radius:6px;font-size:{{ $fontSize }};padding:{{ $padding }};letter-spacing:0.02em;">
        {{ $c }}
    </span>
@else
    <span class="badge bg-light text-muted border px-2 py-1" style="font-size:0.75rem;">-</span>
@endif
