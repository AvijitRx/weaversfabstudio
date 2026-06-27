<!-- SEO Meta Content -->
@push('meta')
    <meta name="title" content="{{ $page->meta_title }}" />

    <meta name="description" content="{{ $page->meta_description }}" />

    <meta name="keywords" content="{{ $page->meta_keywords }}" />
@endPush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ $page->meta_title }}
    </x-slot>

    <!-- Page Content -->
    <div class="cms-page">
        <article class="cms-prose">
            {!! $page->html_content !!}
        </article>
    </div>

    {{--
        The CMS content is raw HTML stored in the admin. Tailwind's preflight
        strips default styling from headings/lists, so the typography is
        defined here using the Weavers Fab Studio brand tokens (Fraunces for
        display, Karla for body, ink/gold/paper palette). All styling is kept
        self-contained so the page renders correctly without an asset rebuild.
    --}}
    <style>
        .cms-page {
            background: #FCFAF5;
            padding: 4rem 1.5rem;
        }

        .cms-prose {
            max-width: 800px;
            margin: 0 auto;
            color: #5A6072;
            font-family: 'Karla', 'Helvetica Neue', sans-serif;
            font-size: 1.0625rem;
            line-height: 1.85;
        }

        .cms-prose > *:first-child {
            margin-top: 0;
        }

        .cms-prose h1 {
            position: relative;
            margin: 0 0 2rem;
            padding-bottom: 1rem;
            color: #1D2435;
            font-family: 'Fraunces', 'Georgia', serif;
            font-size: 2.5rem;
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: -0.01em;
        }

        .cms-prose h1::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 3.5rem;
            height: 3px;
            border-radius: 2px;
            background: #BB8A36;
        }

        .cms-prose h2 {
            margin: 2.75rem 0 0.85rem;
            color: #1D2435;
            font-family: 'Fraunces', 'Georgia', serif;
            font-size: 1.55rem;
            font-weight: 600;
            line-height: 1.25;
        }

        .cms-prose h3 {
            margin: 2rem 0 0.75rem;
            color: #B23A26;
            font-family: 'Fraunces', 'Georgia', serif;
            font-size: 1.25rem;
            font-style: italic;
            font-weight: 500;
        }

        .cms-prose p {
            margin: 0 0 1.2rem;
        }

        .cms-prose a {
            color: #B23A26;
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color 0.15s ease;
        }

        .cms-prose a:hover {
            color: #8F2C1C;
        }

        .cms-prose strong {
            color: #1D2435;
            font-weight: 600;
        }

        .cms-prose ul,
        .cms-prose ol {
            margin: 0 0 1.4rem;
            padding-left: 1.4rem;
        }

        .cms-prose ul {
            list-style: disc;
        }

        .cms-prose ol {
            list-style: decimal;
        }

        .cms-prose li {
            margin: 0.45rem 0;
            padding-left: 0.35rem;
        }

        .cms-prose li::marker {
            color: #BB8A36;
        }

        .cms-prose blockquote {
            margin: 2.25rem 0;
            padding: 1.25rem 1.5rem;
            border-left: 3px solid #BB8A36;
            border-radius: 0 6px 6px 0;
            background: #FAF6EE;
            color: #1D2435;
            font-style: italic;
        }

        .cms-prose blockquote strong {
            font-style: normal;
        }

        @media (max-width: 768px) {
            .cms-page {
                padding: 2.5rem 1rem;
            }

            .cms-prose {
                font-size: 1rem;
            }

            .cms-prose h1 {
                font-size: 2rem;
            }

            .cms-prose h2 {
                font-size: 1.35rem;
            }
        }
    </style>
</x-shop::layouts>
