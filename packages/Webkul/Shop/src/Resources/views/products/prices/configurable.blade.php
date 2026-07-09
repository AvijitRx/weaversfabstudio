{{-- price-label kept (hidden) so the configurable JS that toggles it doesn't break --}}
<p class="price-label" hidden></p>

@if ($prices['final']['price'] < $prices['regular']['price'])
    <p class="regular-price text-lg font-semibold text-gray-500 line-through">
        {{ $prices['regular']['formatted_price'] }}
    </p>

    <p class="final-price font-semibold">
        {{ $prices['final']['formatted_price'] }}
    </p>
@else
    <p class="regular-price text-lg font-semibold text-gray-500 line-through"></p>

    <p class="final-price font-semibold">
        {{ $prices['regular']['formatted_price'] }}
    </p>
@endif