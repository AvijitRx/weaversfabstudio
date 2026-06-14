@php
    $customer = auth()->guard('customer')->user();
@endphp

<div class="panel-side journal-scroll grid max-h-[1320px] min-w-[342px] max-w-[380px] grid-cols-[1fr] gap-8 overflow-y-auto overflow-x-hidden max-xl:min-w-[270px] max-md:max-w-full max-md:gap-5">
    <!-- Account Profile Hero Section -->
    <div class="grid grid-cols-[auto_1fr] items-center gap-4 rounded-sm border border-navyBlue/10 bg-white px-5 py-[25px] shadow-[0_8px_20px_-12px_rgba(29,36,53,.18)] max-md:py-2.5">
        <div class="">
            <img
                src="{{ $customer->image_url ??  bagisto_asset('images/user-placeholder.png') }}"
                class="h-[60px] w-[60px] rounded-full"
                alt="Profile Image"
            >
        </div>

        <div
            class="flex flex-col justify-between gap-0.5"
            v-pre
        >
            <p class="break-all font-dmserif text-2xl max-md:text-xl">
                Hello, {{ $customer->first_name }}
            </p>

            <p class="text-sm text-inkSoft no-underline">
                {{ $customer->email }}
            </p>
        </div>
    </div>

    <!-- Account Navigation Menus -->
    @foreach (menu()->getItems('customer') as $menuItem)
        <div>
            <!-- Account Navigation Toggler -->
            <div class="select-none pb-4 max-md:pb-1.5">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-madder">
                    {{ $menuItem->getName() }}
                </p>
            </div>

            <!-- Account Navigation Content -->
            @if ($menuItem->haveChildren())
                <div class="grid overflow-hidden rounded-sm border border-navyBlue/10 bg-white max-md:border-none max-md:bg-transparent">
                    @foreach ($menuItem->getChildren() as $subMenuItem)
                        <a href="{{ $subMenuItem->getUrl() }}">
                            <div class="flex cursor-pointer justify-between border-t border-navyBlue/10 px-6 py-4 transition-colors first:border-t-0 hover:bg-cream max-md:border-0 max-md:px-0 max-md:py-3 {{ $subMenuItem->isActive() ? 'border-l-2 !border-l-madder bg-cream' : '' }}">
                                <p class="flex items-center gap-x-4 text-base font-medium max-sm:text-base {{ $subMenuItem->isActive() ? 'text-madder' : '' }}">
                                    <span class="{{ $subMenuItem->getIcon() }} text-2xl"></span>

                                    {{ $subMenuItem->getName() }}
                                </p>

                                <span class="icon-arrow-right rtl:icon-arrow-left text-2xl {{ $subMenuItem->isActive() ? 'text-madder' : 'text-inkSoft' }}"></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
