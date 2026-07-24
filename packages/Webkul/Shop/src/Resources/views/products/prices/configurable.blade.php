{{-- price-label kept (hidden) so the configurable JS that toggles it doesn't break --}}
<p class="price-label" hidden></p>

@php
    // A configurable with no (or broken) variants can come back without these
    // keys — never let a missing key raise a fatal ErrorException here.
    $regularPrice = $prices['regular'] ?? null;
    $finalPrice   = $prices['final'] ?? null;

    $isOnSale = isset($finalPrice['price'], $regularPrice['price'])
        && $finalPrice['price'] < $regularPrice['price'];
@endphp

@if ($isOnSale)
    <p class="regular-price text-lg font-semibold text-gray-500 line-through">
        {{ $regularPrice['formatted_price'] ?? '' }}
    </p>

    <p class="final-price font-semibold">
        {{ $finalPrice['formatted_price'] ?? '' }}
    </p>
@else
    <p class="regular-price text-lg font-semibold text-gray-500 line-through"></p>

    <p class="final-price font-semibold">
        {{ $regularPrice['formatted_price'] ?? ($finalPrice['formatted_price'] ?? '') }}
    </p>
@endif