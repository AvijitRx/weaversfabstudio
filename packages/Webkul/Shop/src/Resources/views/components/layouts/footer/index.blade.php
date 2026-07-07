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
                Handloom cloth, Handwoven by Charka sold by metre.
            </p>

            <!-- Social links -->
            <div class="mt-2 flex items-center gap-4">
                <a
                    href="https://www.instagram.com/weaversfabstudio"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Instagram"
                    class="text-white/70 transition-colors hover:text-goldSoft"
                >
                    <svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22" aria-hidden="true"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63c-.79.31-1.46.72-2.12 1.38C1.36 2.67.95 3.34.63 4.14.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.8.72 1.47 1.38 2.13.66.66 1.33 1.07 2.12 1.38.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56.8-.31 1.47-.72 2.13-1.38.66-.66 1.07-1.33 1.38-2.13.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91-.31-.79-.72-1.46-1.38-2.12-.66-.66-1.33-1.07-2.13-1.38-.76-.3-1.64-.5-2.91-.56C15.67.01 15.26 0 12 0m0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84M12 16a4 4 0 1 1 4-4 4 4 0 0 1-4 4m6.41-10.85a1.44 1.44 0 1 0 1.44 1.44 1.44 1.44 0 0 0-1.44-1.44"/></svg>
                </a>

                <a
                    href="https://www.facebook.com/weaversfabstudio"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Facebook"
                    class="text-white/70 transition-colors hover:text-goldSoft"
                >
                    <svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22" aria-hidden="true"><path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.25h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07"/></svg>
                </a>

                <a
                    href="https://www.youtube.com/weaversfabstudio"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="YouTube"
                    class="text-white/70 transition-colors hover:text-goldSoft"
                >
                    <svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22" aria-hidden="true"><path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81M9.55 15.57V8.43L15.82 12l-6.27 3.57"/></svg>
                </a>

                <a
                    href="https://www.pinterest.com/weaversfabstudio"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Pinterest"
                    class="text-white/70 transition-colors hover:text-goldSoft"
                >
                    <svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.08 3.16 9.42 7.62 11.17-.11-.95-.2-2.4.04-3.44.22-.93 1.4-5.94 1.4-5.94s-.36-.72-.36-1.78c0-1.67.97-2.92 2.17-2.92 1.02 0 1.52.77 1.52 1.69 0 1.03-.66 2.57-1 4-.28 1.2.6 2.18 1.79 2.18 2.15 0 3.8-2.27 3.8-5.54 0-2.9-2.08-4.92-5.05-4.92-3.44 0-5.46 2.58-5.46 5.25 0 1.04.4 2.16.9 2.76.1.12.11.23.08.35-.09.38-.29 1.2-.33 1.36-.05.22-.17.27-.4.16-1.5-.7-2.43-2.89-2.43-4.65 0-3.79 2.75-7.27 7.93-7.27 4.17 0 7.4 2.97 7.4 6.94 0 4.14-2.61 7.47-6.23 7.47-1.22 0-2.36-.63-2.75-1.38l-.75 2.85c-.27 1.04-1 2.35-1.49 3.15C9.57 23.82 10.76 24 12 24c6.63 0 12-5.37 12-12S18.63 0 12 0"/></svg>
                </a>
            </div>
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
