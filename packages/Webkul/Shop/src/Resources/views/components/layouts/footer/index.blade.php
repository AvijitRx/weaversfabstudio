{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    /*
     * Footer link URLs are entered by hand in the admin, and several were
     * saved with a duplicated scheme (e.g. "https:https://example.com/page/..")
     * which the browser treats as invalid, so the links won't open. Strip a
     * leading scheme that is immediately followed by another scheme.
     */
    $footerLinkSections = collect($customization?->options ?? [])
        ->map(function ($section) {
            return collect($section)
                ->map(function ($link) {
                    $link['url'] = preg_replace('#^https?:(?=https?://)#i', '', $link['url'] ?? '');

                    return $link;
                })
                ->values()
                ->all();
        })
        ->values()
        ->all();
@endphp

<footer class="mt-9 bg-navyBlue text-white max-sm:mt-10">
    <div class="flex justify-between gap-x-16 gap-y-10 px-[60px] py-16 max-1060:flex-col-reverse max-md:gap-8 max-md:px-8 max-md:py-10 max-sm:px-4 max-sm:py-8">
        <!-- Brand Column -->
        <div class="grid max-w-[320px] content-start gap-4 max-1060:max-w-full">
            <p
                class="font-dmserif text-3xl text-white"
                role="heading"
                aria-level="2"
            >
                Weavers Fab Studio
            </p>

            <span class="h-px w-12 bg-gold"></span>

            <p class="text-sm leading-6 text-white/70">
                Handwoven cloth and garments, made slowly on wooden looms with
                natural dyes — shirts, kurtas, tees and fabric by the metre.
            </p>
        </div>

        <!-- For Desktop View -->
        <div
            class="flex flex-wrap items-start gap-24 max-1180:gap-10 max-1060:hidden"
            v-pre
        >
            @if (! empty($footerLinkSections))
                @foreach ($footerLinkSections as $footerLinkSection)
                    <ul class="grid gap-4 text-sm">
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        @foreach ($footerLinkSection as $link)
                            <li>
                                <a
                                    href="{{ $link['url'] }}"
                                    class="text-white/70 transition-colors hover:text-goldSoft"
                                >
                                    {{ $link['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>

        <!-- For Mobile view -->
        <x-shop::accordion
            :is-active="false"
            class="hidden !w-full !border-b !border-t !border-white/15 !text-goldSoft max-1060:block"
        >
            <x-slot:header class="!px-0 !py-4 text-xs font-bold uppercase tracking-[0.24em] text-goldSoft">
                Quick Links
            </x-slot>

            <x-slot:content class="grid gap-4 !bg-transparent !p-0 !pb-5">
                @if (! empty($footerLinkSections))
                    @foreach ($footerLinkSections as $footerLinkSection)
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        @foreach ($footerLinkSection as $link)
                            <a
                                href="{{ $link['url'] }}"
                                class="text-sm text-white/75 transition-colors hover:text-goldSoft"
                                v-pre
                            >
                                {{ $link['title'] }}
                            </a>
                        @endforeach
                    @endforeach
                @endif
            </x-slot>
        </x-shop::accordion>

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.before') !!}

        <!-- News Letter subscription -->
        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
            <div class="grid content-start gap-2.5">
                <p
                    class="max-w-[320px] font-dmserif text-3xl italic leading-[42px] text-goldSoft max-md:text-2xl max-sm:text-lg"
                    role="heading"
                    aria-level="2"
                >
                    @lang('shop::app.components.layouts.footer.newsletter-text')
                </p>

                <p class="text-xs text-white/70">
                    @lang('shop::app.components.layouts.footer.subscribe-stay-touch')
                </p>

                <div>
                    <x-shop::form
                        :action="route('shop.subscription.store')"
                        class="mt-2.5 rounded max-sm:mt-0"
                    >
                        <div class="relative w-full">
                            <x-shop::form.control-group.control
                                type="email"
                                class="block w-[420px] max-w-full rounded-sm border border-white/25 bg-white/10 px-5 py-4 text-base text-white placeholder:text-white/50 focus:border-goldSoft max-1060:w-full max-md:p-3.5 max-sm:mb-0 max-sm:p-2 max-sm:text-sm"
                                name="email"
                                rules="required|email"
                                label="Email"
                                :aria-label="trans('shop::app.components.layouts.footer.email')"
                                placeholder="email@example.com"
                            />

                            <x-shop::form.control-group.error control-name="email" />

                            <button
                                type="submit"
                                class="absolute top-1.5 flex w-max items-center rounded-sm bg-madder px-7 py-2.5 text-sm font-semibold uppercase tracking-[0.12em] text-white transition-colors hover:bg-madderDeep ltr:right-2 rtl:left-2 max-md:top-1 max-md:px-5 max-md:text-xs max-sm:static max-sm:mt-2.5 max-sm:w-full max-sm:justify-center max-sm:py-2.5"
                            >
                                @lang('shop::app.components.layouts.footer.subscribe')
                            </button>
                        </div>
                    </x-shop::form>
                </div>
            </div>
        @endif

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.after') !!}
    </div>

    <div class="flex justify-between border-t border-white/10 px-[60px] py-4 max-md:justify-center max-sm:px-5">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="text-sm text-white/60 max-md:text-center">
            @if (core()->getConfigData('general.content.footer.copyright_content'))
                {!! core()->getConfigData('general.content.footer.copyright_content') !!}
            @else
                @lang('shop::app.components.layouts.footer.footer-text', ['current_year'=> date('Y') ])
            @endif
        </p>

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
