<v-product-card
    {{ $attributes }}
    :product="product"
>
</v-product-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-card-template"
    >
        <!-- Grid Card -->
        <div
            class="group w-full"
            v-if="mode != 'list'"
        >
            <!-- Product Image -->
            <div class="relative overflow-hidden rounded-sm bg-paper transition-shadow duration-300 group-hover:shadow-[0_18px_32px_-14px_rgba(29,36,53,.32)]">
                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <a
                    :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`"
                    :aria-label="product.name + ' '"
                >
                    <x-shop::media.images.lazy
                        class="aspect-[3/4] w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
                        ::src="product.base_image.medium_image_url"
                        ::srcset="`
                            ${product.base_image.small_image_url} 150w,
                            ${product.base_image.medium_image_url} 300w,
                        `"
                        sizes="(max-width: 768px) 150px, (max-width: 1200px) 300px, 600px"
                        ::key="product.id"
                        ::index="product.id"
                        width="300"
                        height="400"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <!-- Product Sale Badge -->
                <p
                    class="absolute top-2.5 z-[2] inline-block rounded-sm bg-madder px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-white ltr:left-2.5 rtl:right-2.5"
                    v-if="product.on_sale"
                >
                    @lang('shop::app.components.products.card.sale')
                </p>

                <!-- Product New Badge -->
                <p
                    class="absolute top-2.5 z-[2] inline-block rounded-sm bg-navyBlue px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-white ltr:left-2.5 rtl:right-2.5"
                    v-else-if="product.is_new"
                >
                    @lang('shop::app.components.products.card.new')
                </p>

                <!-- Product Ratings -->
                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                    <x-shop::products.ratings
                        class="absolute bottom-2.5 items-center !border-white bg-white/90 !px-2 !py-1 text-xs max-sm:!px-1.5 max-sm:!py-0.5 ltr:left-2.5 rtl:right-2.5"
                        ::average="product.ratings.average"
                        ::total="product.ratings.total"
                        ::rating="false"
                        v-if="product.ratings.total"
                    />
                @else
                    <x-shop::products.ratings
                        class="absolute bottom-2.5 items-center !border-white bg-white/90 !px-2 !py-1 text-xs max-sm:!px-1.5 max-sm:!py-0.5 ltr:left-2.5 rtl:right-2.5"
                        ::average="product.ratings.average"
                        ::total="product.reviews.total"
                        ::rating="false"
                        v-if="product.reviews.total"
                    />
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                <!-- Floating Actions (wishlist / compare) -->
                {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                    <span
                        class="absolute top-2.5 z-[2] flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-white/95 text-lg shadow-[0_2px_10px_rgba(29,36,53,.14)] transition-all duration-300 hover:text-madder md:translate-x-2 md:opacity-0 md:group-hover:translate-x-0 md:group-hover:opacity-100 ltr:right-2.5 rtl:left-2.5"
                        role="button"
                        aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                        tabindex="0"
                        :class="product.is_wishlist ? 'icon-heart-fill text-madder' : 'icon-heart'"
                        @click="addToWishlist()"
                    >
                    </span>
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                @if (core()->getConfigData('catalog.products.settings.compare_option'))
                    <span
                        class="icon-compare absolute top-[52px] z-[2] flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-white/95 text-lg shadow-[0_2px_10px_rgba(29,36,53,.14)] transition-all delay-75 duration-300 hover:text-madder md:translate-x-2 md:opacity-0 md:group-hover:translate-x-0 md:group-hover:opacity-100 ltr:right-2.5 rtl:left-2.5"
                        role="button"
                        aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                        tabindex="0"
                        @click="addToCompare(product.id)"
                    >
                    </span>
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}

                <!-- Add To Cart Overlay (desktop) -->
                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                    <button
                        class="absolute inset-x-0 bottom-0 z-[3] flex translate-y-full items-center justify-center gap-2 bg-navyBlue/95 py-3.5 text-xs font-semibold uppercase tracking-[0.16em] text-white transition-all duration-300 ease-out hover:bg-madder group-hover:translate-y-0 max-md:hidden"
                        :disabled="! product.is_saleable || isAddingToCart"
                        @click="addToCart()"
                    >
                        <span class="icon-cart text-base"></span>

                        @lang('shop::app.components.products.card.add-to-cart')
                    </button>

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                @endif
            </div>

            <!-- Product Information -->
            <div class="mt-3.5 grid content-start gap-1.5 px-0.5">
                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
                    <p class="line-clamp-2 break-words text-[15px] font-medium leading-5 text-navyBlue underline-offset-4 decoration-gold decoration-2 group-hover:underline max-md:whitespace-break-spaces">
                        @{{ product.name }}
                    </p>
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                <!-- Pricing -->
                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="flex items-center gap-2 text-[15px] font-bold max-sm:text-sm"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                <!-- Add To Cart (mobile) -->
                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                    <button
                        class="secondary-button mt-1.5 w-full max-w-full py-2 text-[11px] md:hidden"
                        :disabled="! product.is_saleable || isAddingToCart"
                        @click="addToCart()"
                    >
                        @lang('shop::app.components.products.card.add-to-cart')
                    </button>
                @endif
            </div>
        </div>

        <!-- List Card -->
        <div
            class="relative flex max-w-max grid-cols-2 gap-6 overflow-hidden rounded-sm max-sm:flex-wrap"
            v-else
        >
            <div class="group relative max-w-[250px] overflow-hidden rounded-sm bg-paper">

                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
                    <x-shop::media.images.lazy
                        class="aspect-[3/4] min-w-[250px] object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="300"
                        height="400"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <!-- Product Sale Badge -->
                <p
                    class="absolute top-2.5 z-[2] inline-block rounded-sm bg-madder px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-white ltr:left-2.5 rtl:right-2.5"
                    v-if="product.on_sale"
                >
                    @lang('shop::app.components.products.card.sale')
                </p>

                <!-- Product New Badge -->
                <p
                    class="absolute top-2.5 z-[2] inline-block rounded-sm bg-navyBlue px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-white ltr:left-2.5 rtl:right-2.5"
                    v-else-if="product.is_new"
                >
                    @lang('shop::app.components.products.card.new')
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                    <span
                        class="absolute top-2.5 z-[2] flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-white/95 text-lg shadow-[0_2px_10px_rgba(29,36,53,.14)] transition-all duration-300 hover:text-madder ltr:right-2.5 rtl:left-2.5"
                        role="button"
                        aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                        tabindex="0"
                        :class="product.is_wishlist ? 'icon-heart-fill text-madder' : 'icon-heart'"
                        @click="addToWishlist()"
                    >
                    </span>
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                @if (core()->getConfigData('catalog.products.settings.compare_option'))
                    <span
                        class="icon-compare absolute top-[52px] z-[2] flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-white/95 text-lg shadow-[0_2px_10px_rgba(29,36,53,.14)] transition-all duration-300 hover:text-madder ltr:right-2.5 rtl:left-2.5"
                        role="button"
                        aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                        tabindex="0"
                        @click="addToCompare(product.id)"
                    >
                    </span>
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}
            </div>

            <div class="grid content-start gap-2.5 py-1">

                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
                    <p class="font-dmserif text-xl leading-snug">
                        @{{ product.name }}
                    </p>
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="flex gap-2.5 text-lg font-semibold"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                <p class="text-sm text-inkSoft">
                    <template  v-if="! product.ratings.total">
                        <p class="text-sm text-inkSoft">
                            @lang('shop::app.components.products.card.review-description')
                        </p>
                    </template>

                    <template v-else>
                        @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.ratings.total"
                                ::rating="false"
                            />
                        @else
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.reviews.total"
                                ::rating="false"
                            />
                        @endif
                    </template>
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                    <x-shop::button
                        class="primary-button mt-1.5 whitespace-nowrap px-8 py-2.5"
                        :title="trans('shop::app.components.products.card.add-to-cart')"
                        ::loading="isAddingToCart"
                        ::disabled="! product.is_saleable || isAddingToCart"
                        @click="addToCart()"
                    />

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}

                @endif
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product-card', {
            template: '#v-product-card-template',

            props: ['mode', 'product'],

            data() {
                return {
                    isCustomer: '{{ auth()->guard('customer')->check() }}',

                    isAddingToCart: false,
                }
            },

            methods: {
                addToWishlist() {
                    if (this.isCustomer) {
                        this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                                product_id: this.product.id
                            })
                            .then(response => {
                                this.product.is_wishlist = ! this.product.is_wishlist;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                },

                addToCompare(productId) {
                    /**
                     * This will handle for customers.
                     */
                    if (this.isCustomer) {
                        this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                'product_id': productId
                            })
                            .then(response => {
                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                            })
                            .catch(error => {
                                if ([400, 422].includes(error.response.status)) {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });

                                    return;
                                }

                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                            });

                        return;
                    }

                    /**
                     * This will handle for guests.
                     */
                    let items = this.getStorageValue() ?? [];

                    if (items.length) {
                        if (! items.includes(productId)) {
                            items.push(productId);

                            localStorage.setItem('compare_items', JSON.stringify(items));

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                        } else {
                            this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.components.products.card.already-in-compare')" });
                        }
                    } else {
                        localStorage.setItem('compare_items', JSON.stringify([productId]));

                        this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });

                    }
                },

                getStorageValue(key) {
                    let value = localStorage.getItem('compare_items');

                    if (! value) {
                        return [];
                    }

                    return JSON.parse(value);
                },

                addToCart() {
                    this.isAddingToCart = true;

                    this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data );

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }

                            this.isAddingToCart = false;
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            if (error.response.data.redirect_uri) {
                                window.location.href = error.response.data.redirect_uri;
                            }

                            this.isAddingToCart = false;
                        });
                },
            },
        });
    </script>
@endpushOnce
