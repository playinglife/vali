# ShirtHouse SEO plan

ShirtHouse is a public storefront (catalog, cart, checkout) with an admin behind login. SEO is effectively off today: every page sends `noindex,nofollow`. Product meta fields already exist in the database but are never rendered. Work these items in order; skip P3 until the site is indexing.

- **29** work items
- **8** blocking (P0)
- **14** should-do (P1)
- **0 / 29** implemented

> **Indexing is disabled.** `layouts/app.blade.php` line 8: `meta name="robots" content="noindex, nofollow"`. Until that is scoped, nothing else on this list can rank.

Source: ShirtHouse codebase audit · public layout, `routes/web.php`, Product model, `lang/en` + `lang/ro` · Aug 27, 2026

---

## What is already in place

| Piece | State | Note |
| --- | --- | --- |
| HTML lang | Done | Follows app locale (en / ro) |
| Product slugs | Done | `/products/{slug}` is crawl-friendly |
| Product meta columns | Stored only | `meta_title` / `meta_description` in DB + admin, not in `<head>` |
| robots.txt (public/) | Wrong policy | Allows all, including `/admin`; no Sitemap line |
| Page titles | Broken | Several pages still titled Dashboard |
| noindex | Global | Blocks the entire storefront |
| en / ro copy | UI only | Locale is cookie-based — Google sees one language |

---

## Implementation order

Implement top to bottom inside each phase.

### 1. Crawl & index

#### P0 — Stop blocking Google on the storefront

- **Why:** `layouts/app.blade.php` sets `noindex,nofollow` on every public page. Search engines will not list ShirtHouse at all until this is gone.
- **Do:** Remove the global robots meta from the public layout. Keep noindex on admin, login, cart, checkout, thank-you, and contact-success. Default public pages to `index,follow`.
- **Files:** `layouts/app.blade.php`, `layouts/admin.blade.php`

#### P0 — Fix robots.txt and point it at the sitemap

- **Why:** `public/robots.txt` currently allows everything, including `/admin`. A second `robots.txt` at the repo root is unused (Apache serves `public/`).
- **Do:** Disallow `/admin`, `/cart`, `/checkout`, `/thankyou`, `/contact-success`. Allow the rest. Add a `Sitemap:` line once the XML sitemap exists.
- **Files:** `public/robots.txt`

#### P0 — Add a shared SEO head partial

- **Why:** There is no `meta.blade.php`. Titles, descriptions, canonical, OG, and JSON-LD will be copy-pasted unless they live in one include.
- **Do:** Create a Blade partial (or a small view composer) that accepts title, description, canonical, robots, og image, and extra JSON-LD. Yield it from both layouts.
- **Files:** `resources/views/partials/seo.blade.php`, `layouts/app.blade.php`

#### P0 — Explicitly noindex private and transactional URLs

- **Why:** Cart, checkout, thank-you, contact-success, and admin must never rank. Thank-you pages especially create thin duplicate results.
- **Do:** Set robots `noindex,nofollow` on those routes via the SEO partial. Confirm `/up` (health) is also noindexed.
- **Files:** pages/cart, checkout, thankyou, contact-success, admin layout

#### P0 — Set production APP_NAME and APP_URL

- **Why:** Canonicals, sitemaps, and OG urls are built from config. `.env.example` still has `APP_NAME=Laravel` and `APP_URL=http://localhost`.
- **Do:** Set `APP_NAME=ShirtHouse` and the real HTTPS origin. Use `config('app.name')` and `url()` instead of hardcoding ShirtHouse in layouts.
- **Files:** `.env`, `.env.example`, `config/app.php`, layouts

### 2. On-page

#### P0 — Unique titles on every public page

- **Why:** Home, products, product, cart, and leftover Clef Play pages all use the section title Dashboard. Google will treat them as duplicates.
- **Do:** Replace Dashboard. Pattern: `{Page} | ShirtHouse`. Home can be `ShirtHouse | Custom corporate shirts` (or similar). Product pages use `meta_title` or product name.
- **Files:** pages/home, products, product, and lang files

#### P0 — Meta descriptions (default + per page)

- **Why:** No description tag exists. Google will invent snippets from random body copy.
- **Do:** Add translated descriptions in `lang/en` and `lang/ro`. Unique copy for home, products, about us, custom, size chart, contact. 150–160 characters, with a call to action.
- **Files:** `lang/en`, `lang/ro`, SEO partial

#### P0 — Render product meta_title and meta_description

- **Why:** The products table already stores these fields and admin can edit them. The storefront never outputs them.
- **Do:** On `/products/{slug}`, pass `meta_title` / `meta_description` into the SEO partial, falling back to the translated product name and a generated description.
- **Files:** `pages/product.blade.php`, Product model, admin is already wired

#### P0 — Canonical URLs

- **Why:** Without `rel=canonical`, query strings, trailing slashes, and http vs https can split ranking signals.
- **Do:** Output `<link rel=canonical>` from `APP_URL` + current path (no query string) on every indexable page. Self-canonical is enough for now.
- **Files:** SEO partial

#### P1 — One H1 per page, product name as H1

- **Why:** Home has three H1s. Product listing H1 is commented out. Product detail uses H2 for the product name. Size chart has no H1.
- **Do:** Exactly one H1 that matches the topic of the page. Product cards stay H2/H3. Footer labels should not be headings if they are not section titles.
- **Files:** pages/home, products, product, size-chart, components/product-detail, footer

### 3. Rich results

#### P1 — Open Graph and Twitter cards

- **Why:** Shared links on WhatsApp, LinkedIn, Facebook, and X currently have no title, description, or image.
- **Do:** `og:type`, `og:title`, `og:description`, `og:url`, `og:image`, `og:locale`, `og:site_name`. `twitter:card` = `summary_large_image`. Product pages: `og:type=product` plus the product image.
- **Files:** SEO partial, a default 1200x630 share image in `public/`

#### P1 — XML sitemap of indexable URLs

- **Why:** No `sitemap.xml`. Google has to discover products only through crawl of `/products`.
- **Do:** Serve `/sitemap.xml` with home, products, aboutus, custom, size-chart, contact, plus every active product slug. `lastmod` from `updated_at`. Exclude admin, cart, checkout, thank-you, and leftover Clef Play pages. Link it from `robots.txt`.
- **Files:** new route + controller, or a generator command

#### P1 — JSON-LD Organization (and WebSite)

- **Why:** Helps sitelinks, brand knowledge, and search appearance. Cheap to add once the SEO partial exists.
- **Do:** Organization with name, url, logo. WebSite with name and url. Add SearchAction only if a public search exists (it does not today).
- **Files:** SEO partial

#### P1 — JSON-LD Product and Offer on product pages

- **Why:** Product rich results need schema. Pricing already exists on variants.
- **Do:** Product name, description, image, sku/slug, brand ShirtHouse, Offer with price, currency, and availability. Do not invent review ratings.
- **Files:** `pages/product.blade.php` or product-detail component

#### P2 — Visible breadcrumbs plus BreadcrumbList schema

- **Why:** Product URLs are `/products/{slug}` with no trail. Breadcrumbs help crawl and SERP display.
- **Do:** Home > Products > {Product}. Same structure in JSON-LD. Keep it on product and listing pages first.
- **Files:** product and products views, new breadcrumb component

### 4. International

#### P1 — Decide locale URL strategy (en / ro)

- **Why:** English and Romanian exist, but locale is cookie/session only. Google cannot crawl both languages; hreflang is impossible without distinct URLs.
- **Do:** Pick one: prefix `/en` and `/ro` (best for SEO), or `?lang=` (weaker). Then emit hreflang + x-default and translated canonicals. This is the one architectural decision on the list.
- **Files:** `routes/web.php`, SetLocale middleware, language-switcher, SEO partial

#### P1 — hreflang alternate links

- **Why:** Without them, Google may rank the wrong language or treat en/ro as duplicates.
- **Do:** On every indexable URL, output en, ro, and x-default. Product pages need both language URLs even if the slug stays the same.
- **Files:** SEO partial, depends on locale-urls

#### P1 — Translated titles and descriptions

- **Why:** Static copy is already in `lang/en` and `lang/ro`. Product names translate via the translations table. Meta must follow the same pattern.
- **Do:** Store page meta in lang files. Confirm product `meta_title`/`description` are locale-aware or fall back to translated name.
- **Files:** `lang/en`, `lang/ro`, HasLocaleText / products

### 5. Content & IA

#### P0 — Remove or noindex leftover Clef Play pages

- **Why:** `/about`, `/app`, `/teachers`, `/book-a-private-conversation` are orphan music-education pages. Footer still links to `/about`. `/school` is a 500 (view missing).
- **Do:** Delete the routes and views, or 301 to ShirtHouse equivalents. Fix footer About to `/aboutus`. Do not leave them indexable.
- **Files:** `routes/web.php`, pages/about, app, teachers, bookacall, `layouts/footer.blade.php`

#### P1 — Privacy policy (and terms) pages

- **Why:** Footer links to `/privacy-policy`, which has no route. Needed for ads, analytics consent, and trust. Google also crawls legal pages.
- **Do:** Add real privacy (and terms if checkout collects personal data) in en/ro. Point the footer at the live route.
- **Files:** new views + routes, footer

#### P1 — Image alts, dimensions, and lazy-load leftovers

- **Why:** Product cards already have alts and lazy load. Main product image is eager (correct). Other marketing images may lack alt or width/height (CLS).
- **Do:** Meaningful alts on remaining images. width/height or aspect-ratio on all. Decorative icons `aria-hidden`. Default OG image 1200x630.
- **Files:** storefront Blade components, public images

#### P1 — Internal linking and footer cleanup

- **Why:** Footer Quicklinks go to Home, the wrong About, and Contact. Custom, products, and size chart are underlinked.
- **Do:** Footer and home CTAs should hit `/products`, `/custom`, `/aboutus`, `/size-chart`, `/contact`. Social icons need real hrefs or should be removed.
- **Files:** `layouts/footer.blade.php`, `pages/home.blade.php`

#### P2 — Custom 404 that links back into the catalog

- **Why:** Soft 404s and dead Clef Play URLs will appear once those pages are removed. A useful 404 keeps crawl equity.
- **Do:** Branded 404 with links to home and products. Return a real HTTP 404. noindex the 404 template.
- **Files:** `resources/views/errors/404.blade.php`

#### P1 — One host, HTTPS, trailing-slash policy

- **Why:** Duplicate hosts (www vs apex, http vs https) split ranking. Canonicals cannot fix this alone.
- **Do:** Force HTTPS and a single host in Apache/Laravel. Pick trailing slash or not and 301 the other. `APP_URL` must match the chosen origin.
- **Files:** Apache/docker vhost, `APP_URL`, optional ForceHttps middleware

### 6. Measure & launch

#### P1 — Google Search Console and sitemap submit

- **Why:** Without GSC you cannot see coverage errors, inspect URLs, or confirm hreflang.
- **Do:** Verify the production domain (DNS or HTML file). Submit `/sitemap.xml`. Set the preferred domain. Repeat for the other language if using separate URL prefixes.
- **Files:** public verification file or DNS; no app change required besides sitemap

#### P2 — Analytics (GA4 or GTM) with consent

- **Why:** Not ranking-related, but you need it to see organic landing pages and conversions after launch.
- **Do:** GTM in the public layout only. Do not load on admin. Pair with the privacy policy and a cookie banner if required for RO/EU.
- **Files:** `layouts/app.blade.php`, privacy page

#### P2 — Core Web Vitals pass on key templates

- **Why:** Page experience is a ranking factor. Home, `/products`, and product detail are the money pages.
- **Do:** LCP: preload hero/product image. CLS: reserved image space. INP: keep click handlers light. Compress images; avoid loading AG Grid on the storefront.
- **Files:** layouts, product images, Vite build

#### P3 — Favicon, apple-touch-icon, share image sizes

- **Why:** Favicon.ico exists. No apple-touch-icon, no SVG icon, no dedicated OG image. Minor but visible in SERPs and iOS shares.
- **Do:** 32/180 apple-touch-icon, optional SVG favicon, 1200x630 `og-default.jpg`.
- **Files:** `public/`

#### P3 — Later: FAQ, size-guide content, optional blog

- **Why:** The site is thin on unique copy beyond product names. Content is how a shirt shop wins queries like corporate shirts Romania.
- **Do:** Expand size chart and custom page copy. FAQPage schema only if a real FAQ is on the page. Do not add a blog until there is a publishing plan.
- **Files:** pages/size-chart, custom, aboutus — later

---

## Phase notes

Details that do not fit a single ticket. Read before starting that phase.

### 1. Crawl and index

Index: home, `/products`, `/products/{slug}`, `/aboutus`, `/custom`, `/size-chart`, `/contact`.

Never index: `/admin/*`, `/cart`, `/checkout`, `/thankyou`, `/contact-success`, `/admin/login`, `/up`.

Do not add an SEO composer package unless the partial gets messy. A Blade include is enough for this site.

### 2. On-page

Title pattern: `{Page} | ShirtHouse`. Home is the exception — lead with the brand plus the primary keyword (custom corporate shirts).

Product fallback if `meta_title` is empty: translated product name + ` | ShirtHouse`. Truncate titles near 60 characters and descriptions near 155.

### 3. Rich results

JSON-LD only for facts on the page. No fake AggregateRating. Offer price should match what the customer actually sees for a default variant.

Sitemap should regenerate when products are created, updated, or unpublished. A cached route or a file written on save is fine; no need for a cron at this catalog size.

### 4. International — decision required

Cookie-only locale means crawlers get one language (usually English from Accept-Language). Romanian pages will not rank until each language has its own URL.

Recommended: prefix every public route with `/en` and `/ro`, default `/` to one of them via 302/301, and switcher links to the other prefix. hreflang comes after that, not before.

### 5. Content and information architecture

Footer About currently goes to the leftover Clef Play `/about` page, not `/aboutus`. Social icons have no links. `/privacy-policy` 404s. `/school` 500s.

Clean this before submitting a sitemap so Google is not invited to crawl dead or off-brand URLs.

### 6. Measure and launch

Search Console after the sitemap exists and noindex is gone. Analytics after privacy copy exists. Core Web Vitals after templates stabilize — do not optimize AG Grid; it is admin-only.

Skip a blog until there is someone to write it. Thin posts will not help a product catalog.

---

## Full backlog

| # | Priority | Phase | Item |
| ---: | --- | --- | --- |
| 1 | P0 | Crawl & index | Stop blocking Google on the storefront |
| 2 | P0 | Crawl & index | Fix robots.txt and point it at the sitemap |
| 3 | P0 | Crawl & index | Add a shared SEO head partial |
| 4 | P0 | Crawl & index | Explicitly noindex private and transactional URLs |
| 5 | P0 | Crawl & index | Set production APP_NAME and APP_URL |
| 6 | P0 | On-page | Unique titles on every public page |
| 7 | P0 | On-page | Meta descriptions (default + per page) |
| 8 | P0 | On-page | Render product meta_title and meta_description |
| 9 | P0 | On-page | Canonical URLs |
| 10 | P1 | On-page | One H1 per page, product name as H1 |
| 11 | P1 | Rich results | Open Graph and Twitter cards |
| 12 | P1 | Rich results | XML sitemap of indexable URLs |
| 13 | P1 | Rich results | JSON-LD Organization (and WebSite) |
| 14 | P1 | Rich results | JSON-LD Product and Offer on product pages |
| 15 | P2 | Rich results | Visible breadcrumbs plus BreadcrumbList schema |
| 16 | P1 | International | Decide locale URL strategy (en / ro) |
| 17 | P1 | International | hreflang alternate links |
| 18 | P1 | International | Translated titles and descriptions |
| 19 | P0 | Content & IA | Remove or noindex leftover Clef Play pages |
| 20 | P1 | Content & IA | Privacy policy (and terms) pages |
| 21 | P1 | Content & IA | Image alts, dimensions, and lazy-load leftovers |
| 22 | P1 | Content & IA | Internal linking and footer cleanup |
| 23 | P2 | Content & IA | Custom 404 that links back into the catalog |
| 24 | P1 | Content & IA | One host, HTTPS, trailing-slash policy |
| 25 | P1 | Measure & launch | Google Search Console and sitemap submit |
| 26 | P2 | Measure & launch | Analytics (GA4 or GTM) with consent |
| 27 | P2 | Measure & launch | Core Web Vitals pass on key templates |
| 28 | P3 | Measure & launch | Favicon, apple-touch-icon, share image sizes |
| 29 | P3 | Measure & launch | Later: FAQ, size-guide content, optional blog |
