@php
    $channel = core()->getCurrentChannel();
@endphp

{{-- ─────── SEO META ─────── --}}
@push ('meta')
    <meta name="title" content="Weavers Fab Studio — Atelier of Handloom" />
    <meta name="description" content="Cloth that remembers the hand that made it. A documented atelier of handloom textiles from across the subcontinent." />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Karla:ital,wght@0,300..800;1,300..800&family=DM+Mono:ital,wght@0,300;0,400;0,500;1,400&display=swap">
@endpush

@push ('styles')
    <link rel="stylesheet" href="{{ asset('themes/shop/default/css/wfs.css') }}">
@endpush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        Weavers Fab Studio — An Atelier of Handloom
    </x-slot>

    <div class="wfs-page" id="wfs-home">

        {{-- ═════ UTILITY BAR ═════ --}}
        <div class="utility">
            <span>Complimentary shipping across India <span class="dot">·</span> Worldwide ₹ ९९९ flat</span>
            <ul>
                <li>EN / हि / FR</li>
                <li>INR ₹</li>
                <li>Atelier · Mon–Sat by appointment</li>
                <li><a href="{{ route('shop.customer.session.index') }}" style="color:inherit;">Sign In</a></li>
            </ul>
        </div>

        {{-- ═════ HEADER ═════ --}}
        <header class="site">
            <div class="row">
                <nav class="primary" aria-label="Primary">
                    <ul>
                        <li><a href="{{ route('shop.home.index') }}">Collections</a></li>
                        <li><a href="#">Heritage</a></li>
                        <li><a href="#">Lookbook</a></li>
                        <li><a href="#">Journal</a></li>
                        <li class="chapter-num">इ.०७</li>
                    </ul>
                </nav>

                <a class="wordmark" href="{{ route('shop.home.index') }}" aria-label="Weavers Fab Studio Home">
                    <span>Weavers</span><span class="mark">Fab</span><span>Studio</span><span class="dot">.</span>
                    <span class="sub">est. २०२४ · Atelier of Handloom</span>
                </a>

                <div class="actions">
                    <a href="{{ route('shop.search.index') }}" aria-label="Search">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <span class="label-text">Search</span>
                    </a>
                    <a href="{{ route('shop.customers.account.profile.index') }}" aria-label="Account">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                        <span class="label-text">Account</span>
                    </a>
                    <a class="bag" href="{{ route('shop.checkout.cart.index') }}" aria-label="Atelier Bag">
                        Atelier <span class="count">०२</span>
                    </a>
                </div>
            </div>
        </header>

        {{-- ═════ MARQUEE STRIP ═════ --}}
        <div class="marquee" aria-hidden="true">
            <div class="marquee-track">
                @for ($i = 0; $i < 2; $i++)
                    <span>हस्तनिर्मित</span><span class="glyph">❋</span><span><em>handloom</em></span><span class="glyph">·</span>
                    <span>खादी</span><span class="glyph">❋</span><span><em>khadi</em></span><span class="glyph">·</span>
                    <span>चरखा</span><span class="glyph">❋</span><span><em>charkha</em></span><span class="glyph">·</span>
                    <span>नील</span><span class="glyph">❋</span><span><em>indigo</em></span><span class="glyph">·</span>
                    <span>मjeeth</span><span class="glyph">❋</span><span><em>madder</em></span><span class="glyph">·</span>
                    <span>रेशम</span><span class="glyph">❋</span><span><em>silk</em></span><span class="glyph">·</span>
                    <span>सूत</span><span class="glyph">❋</span><span><em>cotton</em></span><span class="glyph">·</span>
                    <span>बनारस</span><span class="glyph">❋</span><span><em>banaras</em></span><span class="glyph">·</span>
                    <span>बागरू</span><span class="glyph">❋</span><span><em>bagru</em></span><span class="glyph">·</span>
                    <span>कांचीपुरम</span><span class="glyph">❋</span><span><em>kanchipuram</em></span><span class="glyph">·</span>
                @endfor
            </div>
        </div>

        {{-- ═════ HERO ═════ --}}
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-text">
                    <div class="hero-meta">
                        <span>Spring Edition · No. ०७</span>
                        <span class="line"></span>
                        <span>February २०२६</span>
                    </div>

                    <h1 class="display">
                        <span class="word">Cloth</span>
                        <span class="word">that</span><br>
                        <span class="word"><em>remembers</em></span><br>
                        <span class="word">the</span>
                        <span class="word">hand</span>
                        <span class="word">that</span>
                        <span class="word">made&nbsp;it.</span>
                    </h1>

                    <p class="lede">
                        We commission, document, and shepherd handloom textiles from fourteen weaving villages across the subcontinent — each metre traceable from spindle to tag, dyed with mineral and plant pigments, and signed by the artisan whose loom held it.
                    </p>

                    <div class="hero-ctas">
                        <a class="btn" href="{{ route('shop.home.index') }}#collection">Discover the Collection</a>
                        <a class="btn ghost" href="#loom">Visit the Atelier</a>
                    </div>
                </div>

                <div class="hero-art">
                    <div class="hero-annotation">a fragment of <em>Banarasi</em> brocade <br>woven Spring &apos;२६</div>
                    <div class="swatch-big" role="img" aria-label="A close-up of madder-dyed Banarasi brocade with gold zari"></div>
                    <div class="stamp">हस्त<span class="big">M.A</span>निर्मित</div>
                    <div class="label-card">
                        <span class="cat-no">WFS<br>२६·०७</span>
                        <span class="name"><em>Madder &amp;</em><br>Mughal Gold</span>
                        <span class="price">₹ २८,४००</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═════ CHAPTER 01 · PROMISE ═════ --}}
        <section class="promise">
            <div class="wrap">
                <div class="chapter reveal">
                    <div class="num"><span class="small">Chapter</span>इ.०१</div>
                    <h2>A <em>first principle</em>:<br>cloth must remember.</h2>
                    <p class="aside">Three commitments behind every metre we put into the world. The atelier's standing terms with its weavers, dyers, and clients.</p>
                </div>

                <div class="promise-grid reveal-stagger">
                    <div class="promise-card">
                        <span class="num">०१</span>
                        <svg class="icon" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <circle cx="28" cy="28" r="22"/>
                            <path d="M28 6 v44 M6 28 h44 M12 12 L 44 44 M44 12 L 12 44"/>
                        </svg>
                        <h3>Loomed by hand<br>in <em>fourteen villages</em>.</h3>
                        <p>Every metre is woven on a wooden loom or a charkha in one of fourteen workshops we work with directly. No power, no machine, no anonymity.</p>
                        <div class="footnote">No. ०१ · Provenance</div>
                    </div>
                    <div class="promise-card">
                        <span class="num">०२</span>
                        <svg class="icon" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M28 6 C 14 22, 14 36, 28 50 C 42 36, 42 22, 28 6 Z"/>
                            <circle cx="28" cy="30" r="6"/>
                        </svg>
                        <h3>Dyed with <em>mineral &amp; plant</em> pigments only.</h3>
                        <p>Indigo, madder, turmeric, pomegranate rind, iron-mordanted gallnut. Recipes recorded in our atelier's dye-book, batch by batch.</p>
                        <div class="footnote">No. ०२ · Materia</div>
                    </div>
                    <div class="promise-card">
                        <span class="num">०३</span>
                        <svg class="icon" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <rect x="8" y="8" width="40" height="40"/>
                            <path d="M8 18 H 48 M8 28 H 48 M8 38 H 48 M18 8 V 48 M28 8 V 48 M38 8 V 48"/>
                        </svg>
                        <h3>Documented <em>thread to tag</em>.</h3>
                        <p>Each piece carries a stamped provenance label noting weaver, region, loom number, materials, dye lot and the runtime hours that produced it.</p>
                        <div class="footnote">No. ०३ · Record</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═════ CHAPTER 02 · COLLECTION ═════ --}}
        <section class="collection" id="collection">
            <div class="wrap">
                <div class="chapter reveal">
                    <div class="num"><span class="small">Chapter</span>इ.०२</div>
                    <h2>The Spring <em>Catalogue</em>,<br>chosen by hand.</h2>
                    <p class="aside">Five pieces from our current edition. Open the cards for the full provenance: loom, weaver, materials, runtime.</p>
                </div>

                <div class="product-grid reveal-stagger">
                    <article class="product">
                        <div class="product-img fabric-banarasi">
                            <div class="frame-inner"></div>
                            <div class="product-stamp"><span class="new">New</span> · Spring '२६</div>
                            <div class="product-cursor">view<br>piece</div>
                        </div>
                        <div class="provenance">
                            <div>
                                <div class="cat-no">WFS·२६·०७·M</div>
                                <h4><em>Madder &amp;</em> Mughal Gold</h4>
                                <div class="meta">
                                    <span>Banarasi Brocade · २.८ m</span>
                                    <span>Pure Mulberry Silk · Real Zari</span>
                                    <span class="weaver">Wvr. Mohammad Anwar · Varanasi</span>
                                    <span>१८० hrs · २२० thr/in</span>
                                </div>
                            </div>
                            <div class="price">₹ २८,४००<small>incl. GST</small></div>
                        </div>
                    </article>

                    <article class="product">
                        <div class="product-img fabric-khadi">
                            <div class="frame-inner"></div>
                            <div class="product-stamp">Atelier</div>
                            <div class="product-cursor">view<br>piece</div>
                        </div>
                        <div class="provenance">
                            <div>
                                <div class="cat-no">WFS·२६·१२·K</div>
                                <h4>Khadi <em>au Naturel</em></h4>
                                <div class="meta">
                                    <span>Hand-Spun Cotton · ३.२ m</span>
                                    <span>Undyed · Loom-finished</span>
                                    <span class="weaver">Wvr. Sushila Devi · Wardha</span>
                                    <span>९२ hrs · १४० thr/in</span>
                                </div>
                            </div>
                            <div class="price">₹ ९,६००<small>incl. GST</small></div>
                        </div>
                    </article>

                    <article class="product">
                        <div class="product-img fabric-indigo">
                            <div class="frame-inner"></div>
                            <div class="product-stamp"><span class="new">Limited</span> · ०७ pieces</div>
                            <div class="product-cursor">view<br>piece</div>
                        </div>
                        <div class="provenance">
                            <div>
                                <div class="cat-no">WFS·२६·१९·B</div>
                                <h4>Bagru <em>Resist</em>, Indigo</h4>
                                <div class="meta">
                                    <span>Hand-Block Print · २.५ m</span>
                                    <span>Cold-Vat Indigo · Iron Mordant</span>
                                    <span class="weaver">Atlr. Chhipa Family · Bagru</span>
                                    <span>७ vat dips · ३४ blocks</span>
                                </div>
                            </div>
                            <div class="price">₹ १४,२००<small>incl. GST</small></div>
                        </div>
                    </article>

                    <article class="product">
                        <div class="product-img fabric-madras">
                            <div class="frame-inner"></div>
                            <div class="product-stamp">Edition</div>
                            <div class="product-cursor">view<br>piece</div>
                        </div>
                        <div class="provenance">
                            <div>
                                <div class="cat-no">WFS·२६·२४·M</div>
                                <h4>Madras <em>Check</em>, Madder</h4>
                                <div class="meta">
                                    <span>Pit-Loom Cotton · २.८ m</span>
                                    <span>Madder &amp; Turmeric · Pre-Mordant</span>
                                    <span class="weaver">Wvr. K. Velusamy · Madurai</span>
                                    <span>१४८ hrs · १८० thr/in</span>
                                </div>
                            </div>
                            <div class="price">₹ १८,९००<small>incl. GST</small></div>
                        </div>
                    </article>

                    <article class="product">
                        <div class="product-img fabric-jamdani">
                            <div class="frame-inner"></div>
                            <div class="product-stamp"><span class="new">Reserved</span> · Spring '२६</div>
                            <div class="product-cursor">view<br>piece</div>
                        </div>
                        <div class="provenance">
                            <div>
                                <div class="cat-no">WFS·२६·३१·J</div>
                                <h4>Jamdani <em>in Mist</em></h4>
                                <div class="meta">
                                    <span>Discontinuous Weft · ३.० m</span>
                                    <span>Khadi Cotton · Loom-Inlaid</span>
                                    <span class="weaver">Wvr. Mukunda Pal · Phulia</span>
                                    <span>२६० hrs · २८० thr/in</span>
                                </div>
                            </div>
                            <div class="price">₹ ३४,२००<small>incl. GST</small></div>
                        </div>
                    </article>
                </div>

                <div class="asterism" aria-hidden="true">❋ &nbsp; ❋ &nbsp; ❋</div>
                <div class="browse-all">
                    <a class="btn ghost" href="{{ route('shop.search.index') }}">Browse the Full Catalogue</a>
                </div>
            </div>
        </section>

        {{-- ═════ CHAPTER 03 · FROM THE LOOM ═════ --}}
        <section class="loom" id="loom">
            <div class="wrap">
                <div class="chapter reveal">
                    <div class="num"><span class="small">Chapter</span>इ.०३</div>
                    <h2>From the loom,<br><em>a voice</em>.</h2>
                    <p class="aside">A new conversation, every season. Spring '२६ : Mohammad Anwar, third-generation Banarasi weaver, on the discipline of zari.</p>
                </div>

                <div class="loom-spread reveal-stagger">
                    <div>
                        <p class="loom-quote">
                            <span class="openq">&ldquo;</span>The brocade does not bend to the loom. It bends to the <em>hand</em> behind it. Forty years and my father&apos;s forty before me &mdash; the loom keeps the count, but the cloth remembers <em>only the hand</em>.<span class="closeq">&rdquo;</span>
                        </p>
                        <div class="loom-attrib">M. Anwar · Varanasi · est. १९५३</div>
                    </div>

                    <aside class="weaver-card">
                        <div class="weaver-portrait" aria-hidden="true"></div>
                        <div class="weaver-meta">
                            <strong>Mohammad Anwar</strong>
                            <div class="row"><span class="k">Region</span><span>Varanasi, उ.प्र.</span></div>
                            <div class="row"><span class="k">Tradition</span><span>Banarasi Brocade</span></div>
                            <div class="row"><span class="k">Loom №</span><span>१४ · Pit-Loom</span></div>
                            <div class="row"><span class="k">Since</span><span>१९७९</span></div>
                            <div class="row"><span class="k">Pieces with WFS</span><span>१२</span></div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        {{-- ═════ CHAPTER 04 · PROCESS ═════ --}}
        <section class="process">
            <div class="wrap">
                <div class="chapter reveal">
                    <div class="num"><span class="small">Chapter</span>इ.०४</div>
                    <h2>Seven slow steps,<br><em>spindle</em> to selvedge.</h2>
                    <p class="aside">A simplified record of what is, in practice, a four-month commitment from raw cotton to finished bolt. Hover any step.</p>
                </div>

                <div class="process-track reveal">
                    <div class="process-step">
                        <span class="step-num">०१ · Spin</span>
                        <svg viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <circle cx="28" cy="28" r="22"/>
                            <circle cx="28" cy="28" r="4" fill="currentColor"/>
                            <path d="M28 6 v 18 M50 28 h -18 M28 50 v -18 M6 28 h 18"/>
                        </svg>
                        <h4 class="step-name">Charkha</h4>
                        <p class="step-blurb">Raw cotton drawn into single-ply yarn on the spinning wheel.</p>
                    </div>
                    <div class="process-step">
                        <span class="step-num">०२ · Dye</span>
                        <svg viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M12 8 h32 v40 H12 z"/>
                            <path d="M12 20 q8 -4 16 0 t16 0"/>
                            <path d="M12 30 q8 -4 16 0 t16 0"/>
                            <path d="M12 40 q8 -4 16 0 t16 0"/>
                        </svg>
                        <h4 class="step-name">Vat &amp; <em>Mordant</em></h4>
                        <p class="step-blurb">Indigo, madder, turmeric. Iron and alum to fix.</p>
                    </div>
                    <div class="process-step">
                        <span class="step-num">०३ · Warp</span>
                        <svg viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M4 8 V 48 M14 8 V 48 M24 8 V 48 M34 8 V 48 M44 8 V 48 M54 8 V 48"/>
                            <path d="M4 14 H 54 M4 42 H 54"/>
                        </svg>
                        <h4 class="step-name">Warping</h4>
                        <p class="step-blurb">Yarn laid lengthwise — the loom's spine.</p>
                    </div>
                    <div class="process-step">
                        <span class="step-num">०४ · Set</span>
                        <svg viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <rect x="6" y="6" width="44" height="44"/>
                            <path d="M14 6 V 50 M28 6 V 50 M42 6 V 50"/>
                            <path d="M6 18 H 50 M6 32 H 50 M6 46 H 50"/>
                        </svg>
                        <h4 class="step-name">Setting</h4>
                        <p class="step-blurb">Reed, heddle, shuttle — the loom prepared.</p>
                    </div>
                    <div class="process-step">
                        <span class="step-num">०५ · Weave</span>
                        <svg viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M4 12 q 8 6 16 0 t 16 0 t 16 0"/>
                            <path d="M4 22 q 8 6 16 0 t 16 0 t 16 0"/>
                            <path d="M4 32 q 8 6 16 0 t 16 0 t 16 0"/>
                            <path d="M4 42 q 8 6 16 0 t 16 0 t 16 0"/>
                        </svg>
                        <h4 class="step-name">The <em>Weave</em></h4>
                        <p class="step-blurb">Weft and warp meet, hand-throw by hand-throw.</p>
                    </div>
                    <div class="process-step">
                        <span class="step-num">०६ · Wash</span>
                        <svg viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M8 36 c 6 -8 12 -8 20 0 s 14 8 20 0"/>
                            <path d="M8 26 c 6 -8 12 -8 20 0 s 14 8 20 0"/>
                            <path d="M8 16 c 6 -8 12 -8 20 0 s 14 8 20 0"/>
                        </svg>
                        <h4 class="step-name">Washing</h4>
                        <p class="step-blurb">River-water rinse to soften the hand.</p>
                    </div>
                    <div class="process-step">
                        <span class="step-num">०७ · Tag</span>
                        <svg viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M8 8 H 30 L 48 26 V 48 H 8 z"/>
                            <circle cx="22" cy="22" r="3"/>
                            <path d="M16 36 H 40 M16 42 H 32"/>
                        </svg>
                        <h4 class="step-name">Provenance</h4>
                        <p class="step-blurb">Stamped, signed, recorded in the dye-book.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═════ PULL QUOTE BAND ═════ --}}
        <section class="pullquote">
            <div class="pullquote-inner reveal">
                <span class="marker">&para;</span>
                <div>
                    <p>
                        A handloom cloth is not slow because it is <em>antique</em>. It is slow because every metre is a hundred small decisions held in human attention &mdash; how tight the warp, how heavy the weft, when to stop, when to dye again, when to begin.
                    </p>
                    <footer>WFS Manifesto · Note ०२</footer>
                </div>
            </div>
        </section>

        {{-- ═════ NEWSLETTER ═════ --}}
        <section class="newsletter">
            <div class="newsletter-inner reveal">
                <div class="left">
                    <span class="eyebrow">The Seasonal Journal · Quarterly</span>
                    <h2>Subscribe to <em>The Atelier Notes</em>.</h2>
                    <p>A printed booklet, four times a year. Weaver profiles, new editions, recipes from the dye-book, atelier visits, and the occasional letter from the loom. Free with any purchase; ₹ ४८० a year on its own.</p>
                </div>
                <div>
                    <form class="nl-form" action="{{ route('shop.subscription.store') }}" method="POST" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Subscribed ✓';">
                        @csrf
                        <input type="email" name="email" placeholder="your.address@elsewhere" aria-label="email" required>
                        <button type="submit">Subscribe</button>
                    </form>
                    <div class="nl-meta">
                        <span>No tracking · no resale</span>
                        <span>Unsubscribe anytime</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═════ FOOTER ═════ --}}
        <footer class="site">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a class="wordmark" href="{{ route('shop.home.index') }}">
                        <span>Weavers</span><span class="mark">Fab</span><span>Studio</span><span class="dot">.</span>
                        <span class="sub">est. २०२४</span>
                    </a>
                    <p>An atelier of <em>handloom</em>, documented thread to tag.</p>
                </div>

                <div class="footer-col">
                    <h5>Atelier</h5>
                    <ul>
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">The Weavers</a></li>
                        <li><a href="#">The Dye-Book</a></li>
                        <li><a href="#">Visit · By Appointment</a></li>
                        <li><a href="#">Press &amp; Editorial</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Catalogue</h5>
                    <ul>
                        <li><a href="{{ route('shop.search.index') }}">Spring Edition '२६</a></li>
                        <li><a href="#">Banarasi Brocade</a></li>
                        <li><a href="#">Khadi &amp; Cotton</a></li>
                        <li><a href="#">Indigo Resist</a></li>
                        <li><a href="#">Jamdani &amp; Tangail</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Counsel</h5>
                    <ul>
                        <li><a href="#">Care &amp; Keeping</a></li>
                        <li><a href="#">Shipping · India</a></li>
                        <li><a href="#">Shipping · World</a></li>
                        <li><a href="#">Returns &amp; Restoration</a></li>
                        <li><a href="#">Bespoke Commissions</a></li>
                    </ul>
                </div>

                <p class="footer-acknowledge">
                    Weavers Fab Studio acknowledges the master weavers, dyers, and printers of <em>Varanasi, Wardha, Bagru, Madurai, Phulia, Pochampally, Bhuj, Maheshwar, and ten other villages</em> whose hands hold our entire catalogue. We work in their company, under their counsel, and in their debt.
                </p>
            </div>

            <div class="footer-bottom">
                <span>© २०२६ Weavers Fab Studio · Mumbai &middot; Varanasi</span>
                <span class="credit">Designed in the manner of a notebook. <em>Set in Fraunces &amp; Karla.</em></span>
            </div>
        </footer>

    </div>
</x-shop::layouts>

@push('scripts')
<script>
(function(){
  /* Reveal-on-scroll */
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
  document.querySelectorAll('.wfs-page .reveal, .wfs-page .reveal-stagger').forEach(function(el){ io.observe(el); });

  /* Atelier bag count (demo): clicking a product card bumps count */
  document.querySelectorAll('.wfs-page .product').forEach(function(p){
    p.addEventListener('click', function(e){
      e.preventDefault();
      var bag = document.querySelector('.wfs-page .actions .bag .count');
      var dev = ['०','१','२','३','४','५','६','७','८','९'];
      var map = { '०':0,'१':1,'२':2,'३':3,'४':4,'५':5,'६':6,'७':7,'८':8,'९':9 };
      var cur = bag.textContent.trim();
      var n = [].reduce.call(cur, function(s,c){ return s*10 + (map[c]||0); }, 0) + 1;
      bag.textContent = String(n).split('').map(function(d){ return dev[+d]; }).join('').padStart(2,'०');
      bag.animate(
        [{transform:'scale(1.4)', color:'var(--turmeric)'}, {transform:'scale(1)', color:'var(--madder)'}],
        {duration: 520, easing: 'cubic-bezier(.22,1,.36,1)'}
      );
    });
  });

  /* Header shadow on scroll */
  var header = document.querySelector('.wfs-page header.site');
  if (header) {
    window.addEventListener('scroll', function(){
      header.style.boxShadow = window.scrollY > 8 ? '0 8px 24px -16px rgba(28,20,16,0.20)' : 'none';
    }, { passive: true });
  }
})();
</script>
@endpush
