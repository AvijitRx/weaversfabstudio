@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    use Illuminate\Support\Facades\Storage;
    use Webkul\Category\Models\Category;

    $channel = core()->getCurrentChannel();

    $rootCategoryId = $channel->root_category_id;

    $shopCategories = Category::where('parent_id', $rootCategoryId)
        ->where('status', 1)
        ->orderBy('position')
        ->get();

    /*
     * Hero slides come from the admin "Image Carousel" theme customization
     * (Settings → Themes) when it is enabled and has images; otherwise the
     * branded defaults below are used.
     */
    $heroCustomization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'image_carousel',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    $heroSlides = [];

    foreach ($heroCustomization?->options['images'] ?? [] as $adminSlide) {
        if (empty($adminSlide['image'])) {
            continue;
        }

        $heroSlides[] = [
            'img'     => url($adminSlide['image']),
            'eyebrow' => 'Weavers Fab Studio',
            'title'   => e($adminSlide['title'] ?? ''),
            'sub'     => null,
            'cta'     => [
                'label' => 'Shop Now',
                'url'   => ! empty($adminSlide['link']) ? $adminSlide['link'] : route('shop.search.index'),
            ],
            'cta2'    => null,
        ];
    }

    $defaultHeroSlides = [
        [
            'img'     => asset('storage/wfs/hero.jpg'),
            'eyebrow' => 'Weavers Fab Studio · Est. 2014',
            'title'   => 'Handwoven cloth,<br><em>tailored</em> for living.',
            'sub'     => 'Shirts, tees, kurtas and handloom fabric by the metre — naturally dyed and finished by hand.',
            'cta'     => ['label' => 'Shop the Collection', 'url' => route('shop.search.index')],
            'cta2'    => ['label' => 'New Arrivals', 'url' => route('shop.search.index', ['new' => 1])],
        ],
        [
            'img'     => asset('storage/category/4/kurtas.jpg'),
            'eyebrow' => 'The festive loom',
            'title'   => 'Kurtas woven slow,<br>worn for <em>years</em>.',
            'sub'     => 'Festive and everyday kurtas in handloom cotton and silk, dyed with indigo, madder and turmeric.',
            'cta'     => ['label' => 'Shop Kurtas', 'url' => route('shop.product_or_category.index', 'kurtas')],
            'cta2'    => null,
        ],
        [
            'img'     => asset('storage/wfs/promo.jpg'),
            'eyebrow' => 'Cut to order',
            'title'   => 'Cloth by the metre,<br>selvedge to <em>selvedge</em>.',
            'sub'     => 'Choose a weave, request a swatch, and we cut the exact length you need.',
            'cta'     => ['label' => 'Shop Fabrics', 'url' => route('shop.product_or_category.index', 'fabrics')],
            'cta2'    => null,
        ],
    ];

    if (empty($heroSlides)) {
        $heroSlides = $defaultHeroSlides;
    }
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta name="title" content="{{ $channel->home_seo['meta_title'] ?? '' }}" />
    <meta name="description" content="{{ $channel->home_seo['meta_description'] ?? '' }}" />
    <meta name="keywords" content="{{ $channel->home_seo['meta_keywords'] ?? '' }}" />
@endPush

@push('scripts')
    @if(! empty($categories))
        <script>
            localStorage.setItem('categories', JSON.stringify(@json($categories)));
        </script>
    @endif
@endpush

{{--
    ============================================================
    WEAVERS FAB STUDIO — e-commerce homepage
    Real storefront: hero slider, woven-values marquee, shop-by-
    category (real categories + images), and editorial product
    rows driven by real catalog data via Bagisto's own
    <x-shop::products.carousel> (images, price, add-to-cart,
    wishlist all functional). Renders inside <x-shop::layouts>,
    so header / search / cart / footer work. Custom styling is
    scoped under `.wfs` in @push('styles').
    ============================================================
--}}

@push ('styles')
    @verbatim
    <style>
    /* ===== WEAVERS FAB STUDIO storefront (scoped .wfs) ===== */
    .wfs, .wfs *, .wfs *::before, .wfs *::after { box-sizing:border-box }
    .wfs{
      --paper:#f4eee2; --cream:#faf6ee; --ink:#1d2435; --ink-soft:#5a6072;
      --madder:#b23a26; --madder-deep:#8f2c1c; --gold:#bb8a36; --gold-soft:#f0c98a; --line:rgba(29,36,53,.14);
      --serif:"Fraunces",Georgia,serif; --sans:"Karla","Helvetica Neue",sans-serif;
      font-family:var(--sans); color:var(--ink);
    }
    .wfs h1,.wfs h2,.wfs h3{font-family:var(--serif);font-weight:500;line-height:1.05;margin:0}
    .wfs p{margin:0}
    .wfs a{text-decoration:none;color:inherit}
    .wfs-wrap{max-width:1320px;margin:0 auto;padding:0 clamp(18px,4vw,56px)}
    .wfs-eyebrow{font-size:.74rem;letter-spacing:.26em;text-transform:uppercase;color:var(--madder);font-weight:600}
    .wfs-eyebrow.on-dark{color:var(--gold-soft)}

    .wfs-btn{display:inline-flex;align-items:center;gap:10px;font-weight:600;font-size:.82rem;letter-spacing:.12em;
      text-transform:uppercase;padding:15px 30px;border-radius:2px;transition:.3s;cursor:pointer}
    .wfs-btn.solid{background:var(--madder);color:#fff}
    .wfs-btn.solid:hover{background:var(--madder-deep)}
    .wfs-btn.light{background:#fff;color:var(--ink);border:1px solid #fff}
    .wfs-btn.light:hover{background:transparent;color:#fff;border-color:#fff}

    /* ---------- HERO SLIDER ---------- */
    .wfs-hero{position:relative;height:clamp(520px,78vh,700px);overflow:hidden;background:var(--ink)}
    .wfs-slide{position:absolute;inset:0;display:flex;align-items:center;opacity:0;visibility:hidden;
      transition:opacity 1.1s ease,visibility 0s linear 1.1s;pointer-events:none}
    .wfs-slide.active{opacity:1;visibility:visible;transition:opacity 1.1s ease;pointer-events:auto;z-index:2}
    .wfs-slide .bg{position:absolute;inset:0;background-size:cover;background-position:center 30%;transform:scale(1.12)}
    .wfs-slide.active .bg{transform:scale(1.02);transition:transform 7.5s cubic-bezier(.22,.61,.36,1)}
    .wfs-slide .bg::after{content:"";position:absolute;inset:0;
      background:linear-gradient(90deg,rgba(15,19,32,.84) 0%,rgba(15,19,32,.56) 42%,rgba(15,19,32,.16) 100%)}
    .wfs-slide .inner{position:relative;z-index:2;color:#fff;max-width:620px;padding:48px 0}
    .wfs-slide h1{color:#fff;font-size:clamp(2.6rem,5.6vw,4.8rem);letter-spacing:-.01em;margin:18px 0 0}
    .wfs-slide h1 em{font-style:italic;color:var(--gold-soft);font-weight:400}
    .wfs-slide p.sub{margin-top:20px;font-size:1.1rem;max-width:44ch;color:rgba(255,255,255,.85)}
    .wfs-slide .cta{display:flex;flex-wrap:wrap;gap:14px;margin-top:34px}
    .wfs-slide .inner > *{opacity:0;transform:translateY(22px);
      transition:opacity .7s ease,transform .7s cubic-bezier(.16,1,.3,1)}
    .wfs-slide.active .inner > *{opacity:1;transform:none}
    .wfs-slide.active .inner > *:nth-child(1){transition-delay:.25s}
    .wfs-slide.active .inner > *:nth-child(2){transition-delay:.38s}
    .wfs-slide.active .inner > *:nth-child(3){transition-delay:.52s}
    .wfs-slide.active .inner > *:nth-child(4){transition-delay:.66s}

    .wfs-hero-ui{position:absolute;left:0;right:0;bottom:26px;z-index:5;display:flex;align-items:center;justify-content:space-between}
    .wfs-hero-dots{display:flex;gap:10px}
    .wfs-hero-dot{width:34px;height:3px;border:0;padding:0;background:rgba(255,255,255,.32);cursor:pointer;transition:.35s;border-radius:2px}
    .wfs-hero-dot.on{background:var(--gold-soft)}
    .wfs-hero-arrows{display:flex;gap:10px}
    .wfs-hero-arrow{width:46px;height:46px;border-radius:999px;border:1px solid rgba(255,255,255,.45);background:transparent;
      color:#fff;font-size:1.05rem;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;justify-content:center}
    .wfs-hero-arrow:hover{background:#fff;color:var(--ink);border-color:#fff}
    @media (prefers-reduced-motion:reduce){
      .wfs-slide,.wfs-slide.active{transition:none}
      .wfs-slide .bg,.wfs-slide.active .bg{transform:none;transition:none}
      .wfs-slide .inner > *,.wfs-slide.active .inner > *{opacity:1;transform:none;transition:none}
    }

    /* ---------- VALUES MARQUEE ---------- */
    .wfs-ribbon{background:var(--ink);border-top:1px solid rgba(255,255,255,.08);overflow:hidden;padding:15px 0}
    .wfs-ribbon .track{display:flex;width:max-content;white-space:nowrap;animation:wfs-marquee 30s linear infinite}
    .wfs-ribbon:hover .track{animation-play-state:paused}
    .wfs-ribbon span{display:inline-flex;align-items:center;gap:42px;padding-right:42px;
      color:var(--gold-soft);font-size:.74rem;font-weight:600;letter-spacing:.3em;text-transform:uppercase}
    .wfs-ribbon i{font-style:normal;color:rgba(255,255,255,.45)}
    @keyframes wfs-marquee{to{transform:translateX(-50%)}}
    @media (prefers-reduced-motion:reduce){.wfs-ribbon .track{animation:none}}

    /* ---------- SECTION SHELL ---------- */
    .wfs-sec{padding:clamp(48px,7vh,92px) 0;background:var(--cream)}
    .wfs-sec.paper{background:var(--paper)}
    .wfs-sec.weave{background:var(--paper) repeating-linear-gradient(45deg,rgba(29,36,53,.022) 0 2px,transparent 2px 9px)}
    .wfs-sec-head{text-align:center;max-width:640px;margin:0 auto clamp(34px,5vh,52px)}
    .wfs-sec-head h2{font-size:clamp(2rem,4vw,3.2rem);margin-top:12px}
    .wfs-sec-head p{color:var(--ink-soft);margin-top:12px}

    /* ---------- EDITORIAL ROW HEAD (product carousels) ---------- */
    .wfs-edit-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;
      border-bottom:1px solid var(--line);padding-bottom:18px;margin-bottom:6px}
    .wfs-edit-head h2{font-size:clamp(2rem,3.8vw,3rem);margin-top:10px}
    .wfs-edit-head .note{margin-top:10px;color:var(--ink-soft);font-size:.95rem;max-width:46ch}
    .wfs-arrow-link{position:relative;display:inline-flex;align-items:center;gap:9px;margin-bottom:6px;white-space:nowrap;
      font-size:.78rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--ink)}
    .wfs-arrow-link b{font-weight:700}
    .wfs-arrow-link .ar{transition:transform .3s;color:var(--madder)}
    .wfs-arrow-link::after{content:"";position:absolute;left:0;right:24px;bottom:-7px;height:2px;background:var(--madder);
      transform:scaleX(0);transform-origin:left;transition:transform .35s cubic-bezier(.16,1,.3,1)}
    .wfs-arrow-link:hover .ar{transform:translateX(6px)}
    .wfs-arrow-link:hover::after{transform:scaleX(1)}

    /* ---------- CAROUSEL RESKIN (Bagisto component inside .wfs) ---------- */
    .wfs-carousel{position:relative}
    .wfs-carousel .container{margin:0 !important;padding:0 !important;max-width:none !important}
    .wfs-carousel .secondary-button{display:none} /* safety: bottom "View All" if a link is ever passed */
    /* the component's header row only holds the arrows now — collapse it */
    .wfs-carousel > div > .flex.justify-between{margin:0;min-height:0}
    /* arrows float over the rail edges, vertically centred */
    .wfs-carousel .icon-arrow-left-stylish,
    .wfs-carousel .icon-arrow-right-stylish{
      position:absolute;top:50%;z-index:6;transform:translateY(-50%);
      width:46px;height:46px;border-radius:999px;background:#fff;border:1px solid rgba(29,36,53,.12);
      box-shadow:0 8px 22px rgba(29,36,53,.18);
      display:inline-flex;align-items:center;justify-content:center;color:var(--ink);transition:background .3s,color .3s}
    .wfs-carousel .icon-arrow-left-stylish{left:-22px}
    .wfs-carousel .icon-arrow-right-stylish{right:-22px}
    .wfs-carousel .icon-arrow-left-stylish:hover,
    .wfs-carousel .icon-arrow-right-stylish:hover{background:var(--ink);color:#fff;border-color:var(--ink)}

    /* ---------- CATEGORY TILES ---------- */
    .wfs-cats{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(14px,1.6vw,22px)}
    .wfs-cat{position:relative;display:block;aspect-ratio:3/4;overflow:hidden;border-radius:3px;background:#e4d9c4}
    .wfs-cat img{width:100%;height:100%;object-fit:cover;transition:transform .7s cubic-bezier(.16,1,.3,1)}
    .wfs-cat:hover img{transform:scale(1.06)}
    .wfs-cat::after{content:"";position:absolute;inset:0;background:linear-gradient(transparent 40%,rgba(15,19,32,.72))}
    .wfs-cat .lab{position:absolute;left:0;right:0;bottom:0;z-index:2;padding:20px;color:#fff;text-align:center}
    .wfs-cat .lab h3{color:#fff;font-size:1.5rem}
    .wfs-cat .lab span{font-size:.72rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.8);display:inline-flex;align-items:center;gap:7px;margin-top:6px}
    .wfs-cat .lab span::after{content:"→";transition:transform .3s}
    .wfs-cat:hover .lab span::after{transform:translateX(5px)}

    /* ---------- STORY SPLIT ---------- */
    .wfs-story{background:var(--ink);color:#fff;overflow:hidden}
    .wfs-story .grid2{display:grid;grid-template-columns:1fr 1fr;gap:clamp(32px,5vw,72px);align-items:center;
      padding:clamp(56px,9vh,104px) 0}
    .wfs-story .pic{position:relative}
    .wfs-story .pic img{width:100%;aspect-ratio:4/5;object-fit:cover;border-radius:3px;display:block}
    .wfs-story .pic::after{content:"";position:absolute;inset:18px -18px -18px 18px;border:1px solid var(--gold);
      border-radius:3px;z-index:-1}
    .wfs-story h2{color:#fff;font-size:clamp(2rem,3.8vw,3.1rem);margin-top:14px}
    .wfs-story h2 em{font-style:italic;color:var(--gold-soft);font-weight:400}
    .wfs-story .txt p.body{margin-top:18px;color:rgba(255,255,255,.75);line-height:1.75;max-width:54ch}
    .wfs-story .stats{display:grid;grid-template-columns:repeat(3,auto);gap:clamp(24px,4vw,56px);margin-top:34px;
      border-top:1px solid rgba(255,255,255,.14);padding-top:26px;width:max-content}
    .wfs-story .stats b{display:block;font-family:var(--serif);font-weight:500;font-size:2rem;color:var(--gold-soft)}
    .wfs-story .stats span{display:block;margin-top:4px;font-size:.74rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.6)}
    .wfs-story .cta{margin-top:30px}
    @media (max-width:900px){
      .wfs-story .grid2{grid-template-columns:1fr;gap:40px}
      .wfs-story .pic{margin-right:18px}
      .wfs-story .stats{width:100%;grid-template-columns:repeat(3,1fr)}
    }

    /* ---------- TESTIMONIALS ---------- */
    .wfs-quotes{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(16px,2vw,28px)}
    .wfs-quote{background:#fff;border:1px solid var(--line);border-radius:3px;padding:30px 28px;position:relative}
    .wfs-quote::before{content:"“";position:absolute;top:6px;font-family:var(--serif);font-size:4.4rem;line-height:1;
      color:var(--madder);opacity:.85;left:22px}
    .wfs-quote p.q{margin-top:40px;font-size:.98rem;line-height:1.7;color:var(--ink)}
    .wfs-quote .who{margin-top:18px;display:flex;align-items:center;gap:10px}
    .wfs-quote .who i{font-style:normal;width:26px;height:1px;background:var(--gold)}
    .wfs-quote .who span{font-size:.76rem;letter-spacing:.16em;text-transform:uppercase;color:var(--ink-soft);font-weight:600}
    .wfs-quote .stars{margin-top:6px;color:var(--gold);letter-spacing:3px;font-size:.85rem}
    /* mobile: swipeable snap slider with the next card peeking */
    @media (max-width:900px){
      .wfs-quotes{display:flex;gap:14px;overflow-x:auto;scroll-snap-type:x mandatory;
        margin:0 calc(-1 * clamp(18px,4vw,56px));padding:4px clamp(18px,4vw,56px) 18px;
        -webkit-overflow-scrolling:touch;scrollbar-width:none}
      .wfs-quotes::-webkit-scrollbar{display:none}
      .wfs-quote{flex:0 0 82%;scroll-snap-align:center}
    }

    /* ---------- PROMO BANNER ---------- */
    .wfs-promo{position:relative;min-height:clamp(320px,48vh,440px);display:flex;align-items:center;overflow:hidden}
    .wfs-promo .bg{position:absolute;inset:0;background-size:cover;background-position:center}
    .wfs-promo .bg::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(178,58,38,.92),rgba(143,44,28,.55))}
    .wfs-promo .inner{position:relative;z-index:2;color:#fff;padding:48px 0;max-width:560px}
    .wfs-promo h2{color:#fff;font-size:clamp(2rem,4.4vw,3.4rem)}
    .wfs-promo p{margin-top:14px;color:rgba(255,255,255,.9);max-width:40ch}
    .wfs-promo .cta{margin-top:28px}

    @media (max-width:1024px){
      .wfs-cats{grid-template-columns:repeat(2,1fr)}
    }
    @media (max-width:640px){
      .wfs-edit-head{flex-direction:column;align-items:flex-start}
      .wfs-arrow-link{margin-bottom:0}
      .wfs-hero-arrows{display:none}
    }
    @media (max-width:560px){
      .wfs-slide .cta{flex-direction:column;align-items:stretch}
      .wfs-btn{justify-content:center}
    }

    /* reveal */
    .wfs-rise{opacity:0;transform:translateY(26px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
    .wfs-rise.in{opacity:1;transform:none}
    @media (prefers-reduced-motion:reduce){.wfs-rise{opacity:1;transform:none}}
    </style>
    @endverbatim
@endpush

<x-shop::layouts>
    <x-slot:title>
        {{ $channel->home_seo['meta_title'] ?? 'Weavers Fab Studio — Handwoven & Tailored' }}
    </x-slot>

    <div class="wfs">

        {{-- ===================== HERO SLIDER ===================== --}}
        <section
            class="wfs-hero"
            aria-roledescription="carousel"
            aria-label="Featured collections"
        >
            @foreach ($heroSlides as $i => $slide)
                <div
                    class="wfs-slide {{ $i === 0 ? 'active' : '' }}"
                    role="group"
                    aria-roledescription="slide"
                    aria-label="{{ $i + 1 }} of {{ count($heroSlides) }}"
                >
                    <div class="bg" style="background-image:url('{{ $slide['img'] }}')"></div>

                    <div class="wfs-wrap" style="width:100%">
                        <div class="inner">
                            <span class="wfs-eyebrow on-dark">{{ $slide['eyebrow'] }}</span>

                            <h1>{!! $slide['title'] !!}</h1>

                            @if ($slide['sub'])
                                <p class="sub">{{ $slide['sub'] }}</p>
                            @endif

                            <div class="cta">
                                <a href="{{ $slide['cta']['url'] }}" class="wfs-btn solid">{{ $slide['cta']['label'] }}</a>

                                @if ($slide['cta2'])
                                    <a href="{{ $slide['cta2']['url'] }}" class="wfs-btn light">{{ $slide['cta2']['label'] }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if (count($heroSlides) > 1)
            <div class="wfs-hero-ui wfs-wrap">
                <div class="wfs-hero-dots">
                    @foreach ($heroSlides as $i => $slide)
                        <button
                            type="button"
                            class="wfs-hero-dot {{ $i === 0 ? 'on' : '' }}"
                            aria-label="Go to slide {{ $i + 1 }}"
                        ></button>
                    @endforeach
                </div>

                <div class="wfs-hero-arrows">
                    <button type="button" class="wfs-hero-arrow wfs-hero-prev" aria-label="Previous slide">←</button>
                    <button type="button" class="wfs-hero-arrow wfs-hero-next" aria-label="Next slide">→</button>
                </div>
            </div>
            @endif
        </section>

        {{-- ===================== VALUES MARQUEE ===================== --}}
        <div class="wfs-ribbon" aria-hidden="true">
            <div class="track">
                @for ($r = 0; $r < 2; $r++)
                    <span>Handloom-woven <i>✦</i></span>
                    <span>Natural dyes — indigo · madder · turmeric <i>✦</i></span>
                    <span>Small batches <i>✦</i></span>
                    <span>Easy 7-day returns <i>✦</i></span>
                    <span>Free shipping over ₹1,499 <i>✦</i></span>
                    <span>Fabric cut to order <i>✦</i></span>
                @endfor
            </div>
        </div>

        {{-- ===================== SHOP BY CATEGORY ===================== --}}
        <section class="wfs-sec">
            <div class="wfs-wrap">
                <div class="wfs-sec-head wfs-rise">
                    <span class="wfs-eyebrow">Browse</span>
                    <h2>Shop by Category</h2>
                    <p>From everyday tees to festive kurtas and cloth by the metre.</p>
                </div>

                <div class="wfs-cats wfs-rise">
                    @foreach ($shopCategories as $cat)
                        <a href="{{ route('shop.product_or_category.index', $cat->slug) }}" class="wfs-cat">
                            @if ($cat->logo_url)
                                <img src="{{ $cat->logo_url }}" alt="{{ $cat->name }}" loading="lazy" />
                            @endif
                            <div class="lab">
                                <h3>{{ $cat->name }}</h3>
                                <span>Shop now</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===================== NEW ARRIVALS (real products) ===================== --}}
        <section class="wfs-sec paper">
            <div class="wfs-wrap">
                <div class="wfs-edit-head wfs-rise">
                    <div>
                        <span class="wfs-eyebrow">Just In</span>
                        <h2>New Arrivals</h2>
                        <p class="note">Fresh off the loom this week — small batches, never restocked the same way twice.</p>
                    </div>

                    <a href="{{ route('shop.search.index', ['new' => 1]) }}" class="wfs-arrow-link">
                        View all new <span class="ar">→</span>
                    </a>
                </div>

                <div class="wfs-carousel">
                    {{-- navigation-link intentionally empty: the section head above carries the "View all" link --}}
                    <x-shop::products.carousel
                        :title="''"
                        :src="route('shop.api.products.index', ['new' => 1, 'limit' => 12, 'sort' => 'created_at-desc'])"
                        :navigation-link="''"
                    />
                </div>
            </div>
        </section>

        {{-- ===================== STUDIO STORY ===================== --}}
        <section class="wfs-story">
            <div class="wfs-wrap">
                <div class="grid2">
                    <div class="pic wfs-rise">
                        <img src="{{ asset('storage/wfs/hero.jpg') }}" alt="Weavers at the loom" loading="lazy" />
                    </div>

                    <div class="txt wfs-rise">
                        <span class="wfs-eyebrow on-dark">The Studio</span>
                        <h2>Every thread has a <em>hand</em> behind it.</h2>
                        <p class="body">
                            Since 2014 we've worked with artisan families who weave on wooden
                            looms the way their grandparents did — slowly, in small batches,
                            with yarns dyed in indigo, madder and turmeric. No two bolts are
                            ever quite the same, and that's the point.
                        </p>

                        <div class="stats">
                            <div><b>10+</b><span>Years at the loom</span></div>
                            <div><b>40</b><span>Artisan families</span></div>
                            <div><b>100%</b><span>Natural dyes</span></div>
                        </div>

                        <div class="cta">
                            <a href="{{ url('page/about-us') }}" class="wfs-btn light">Our Story</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== THE STUDIO EDIT (real products) ===================== --}}
        <section class="wfs-sec weave">
            <div class="wfs-wrap">
                <div class="wfs-edit-head wfs-rise">
                    <div>
                        <span class="wfs-eyebrow">Hand-picked</span>
                        <h2>The Studio Edit</h2>
                        <p class="note">Our favourites from this season's looms, chosen by the weavers themselves.</p>
                    </div>

                    <a href="{{ route('shop.search.index') }}" class="wfs-arrow-link">
                        View the edit <span class="ar">→</span>
                    </a>
                </div>

                <div class="wfs-carousel">
                    {{-- navigation-link intentionally empty: the section head above carries the "View all" link --}}
                    <x-shop::products.carousel
                        :title="''"
                        :src="route('shop.api.products.index', ['featured' => 1, 'limit' => 12])"
                        :navigation-link="''"
                    />
                </div>
            </div>
        </section>

        {{-- ===================== PROMO BANNER ===================== --}}
        <section class="wfs-promo">
            <div class="bg" style="background-image:url('{{ asset('storage/wfs/promo.jpg') }}')"></div>
            <div class="wfs-wrap">
                <div class="inner wfs-rise">
                    <span class="wfs-eyebrow on-dark" style="color:#ffe6c2">Cut to order</span>
                    <h2>Handloom cloth,<br>sold by the metre.</h2>
                    <p>Choose a weave, request a swatch, and we cut the length you need — selvedge to selvedge.</p>
                    <div class="cta">
                        <a href="{{ route('shop.product_or_category.index', 'fabrics') }}" class="wfs-btn light">Shop Fabrics</a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== TESTIMONIALS ===================== --}}
        <section class="wfs-sec">
            <div class="wfs-wrap">
                <div class="wfs-sec-head wfs-rise">
                    <span class="wfs-eyebrow">Word of Mouth</span>
                    <h2>From Our Customers</h2>
                </div>

                <div class="wfs-quotes wfs-rise">
                    <figure class="wfs-quote">
                        <div class="stars">★★★★★</div>
                        <p class="q">The kurta feels nothing like store-bought cotton — you can
                            see the weave, and it gets softer every wash. Worth every rupee.</p>
                        <figcaption class="who"><i></i><span>Ananya · Bengaluru</span></figcaption>
                    </figure>

                    <figure class="wfs-quote">
                        <div class="stars">★★★★★</div>
                        <p class="q">Ordered three metres of the indigo handloom for a shirt.
                            The swatch came first, the cut was exact, and the colour is unreal.</p>
                        <figcaption class="who"><i></i><span>Imran · Hyderabad</span></figcaption>
                    </figure>

                    <figure class="wfs-quote">
                        <div class="stars">★★★★★</div>
                        <p class="q">Returns were painless when my size ran small — the
                            replacement arrived in four days with a handwritten note.</p>
                        <figcaption class="who"><i></i><span>Meera · Pune</span></figcaption>
                    </figure>
                </div>
            </div>
        </section>

    </div>

    {{--
        This push must stay inside <x-shop::layouts> — pushes after the
        component's closing tag run after @stack('scripts') has rendered
        and are silently dropped.
    --}}
    @push('scripts')
    <script>
        (function () {
            /*
             * Vue re-renders #app on window load, replacing the DOM these
             * handlers would have been attached to — so initialise after
             * load (and after the synchronous mount) instead of on parse.
             */
            function initReveal() {
                var els = document.querySelectorAll('.wfs-rise');
                if (! ('IntersectionObserver' in window)) { els.forEach(function (e){ e.classList.add('in'); }); return; }
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
                }, { threshold: .12, rootMargin: '0px 0px -6% 0px' });
                els.forEach(function (e){ io.observe(e); });

                /* Safety net: never leave content hidden. */
                setTimeout(function () {
                    document.querySelectorAll('.wfs-rise:not(.in)').forEach(function (e){ e.classList.add('in'); });
                }, 2500);
            }

            function initHero() {
                var hero = document.querySelector('.wfs-hero');
                if (! hero) return;

                var slides = hero.querySelectorAll('.wfs-slide');
                var dots = hero.querySelectorAll('.wfs-hero-dot');
                if (slides.length < 2) return;

                var idx = 0, timer = null;
                var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                function go(n) {
                    idx = (n + slides.length) % slides.length;
                    slides.forEach(function (s, i){ s.classList.toggle('active', i === idx); });
                    dots.forEach(function (d, i){ d.classList.toggle('on', i === idx); });
                }

                /* restart the countdown so a manual action gets a full interval */
                function play() {
                    if (reduced) return;
                    if (timer) clearInterval(timer);
                    timer = setInterval(function (){ go(idx + 1); }, 5500);
                }

                var prev = hero.querySelector('.wfs-hero-prev');
                var next = hero.querySelector('.wfs-hero-next');
                if (prev) prev.addEventListener('click', function (){ go(idx - 1); play(); });
                if (next) next.addEventListener('click', function (){ go(idx + 1); play(); });
                dots.forEach(function (d, i){ d.addEventListener('click', function (){ go(i); play(); }); });

                play();
            }

            function init() { initReveal(); initHero(); }

            if (document.readyState === 'complete') {
                setTimeout(init, 50);
            } else {
                window.addEventListener('load', function (){ setTimeout(init, 50); });
            }
        })();
    </script>
    @endpush
</x-shop::layouts>
