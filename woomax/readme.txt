=== WooMax ===
Contributors: woomax
Tags: woocommerce, e-commerce, custom-background, custom-colors, custom-header, custom-logo, custom-menu, featured-images, footer-widgets, full-width-template, theme-options, threaded-comments, translation-ready, block-patterns, wide-blocks
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A premium WooCommerce theme inspired by natural and artisanal wood aesthetics.

== Description ==

WooMax is a fully customizable WooCommerce theme inspired by natural, artisanal aesthetics — perfect for premium wood products, handcrafted goods, and eco-friendly brands.

**Key Features:**
- 100% customizable via WordPress Customizer (no page builder required)
- WooCommerce native integration
- Responsive, mobile-first design
- Beautiful hero section with animated elements
- Featured products grid with hover effects
- Testimonials section
- Newsletter subscription section
- Custom widget areas
- Translation ready (POT file included)
- RTL support
- Accessibility compliant (WCAG 2.1 AA)
- Page speed optimized

**Customizer Panels:**
- Theme identity (Logo, favicon, site name)
- Colors & Typography (Primary color, fonts, font sizes)
- Header (Layout, top bar, sticky behavior)
- Homepage (Hero, sections visibility, CTAs)
- Shop (Grid columns, layout, product display options)
- Footer (Columns layout, widgets)
- Social networks (Links to all major platforms)
- Advanced options (Custom CSS, lazy loading, breadcrumbs)

== Installation ==

1. In your WordPress admin, go to Appearance → Themes → Add New → Upload Theme
2. Upload the woomax.zip file
3. Activate the theme
4. Go to Appearance → Customize to configure your theme
5. Make sure WooCommerce is installed and activated for full e-commerce functionality

== Frequently Asked Questions ==

= Does WooMax require WooCommerce? =
WooMax works without WooCommerce as a blog/portfolio theme, but its full feature set requires WooCommerce.

= How do I set up the homepage? =
After activating the theme, go to Settings → Reading and set "Your homepage displays" to "A static page". Then assign pages to Homepage and Blog. Configure the homepage in Appearance → Customize → Page d'accueil.

= Can I change the primary color? =
Yes! Go to Appearance → Customize → Couleurs & Typographie → Couleur principale.

= Is WooMax translation ready? =
Yes! A .pot file is included in the languages/ directory.

== Changelog ==

= 1.0.1 =
* Correction d'erreurs fatales : tous les appels de fonctions WooCommerce (is_woocommerce, wc_get_cart_url, wc_get_checkout_url, get_woocommerce_currency_symbol, woocommerce_mini_cart, woocommerce_breadcrumb, wc_get_product_ids_on_sale, etc.) sont désormais protégés par function_exists() / class_exists('WooCommerce'). Le thème ne plante plus lorsque WooCommerce est inactif.

= 1.0.0 =
* Initial release

== Resources ==

* normalize.css — https://necolas.github.io/normalize.css/ — MIT License
* Google Fonts — https://fonts.google.com — SIL Open Font License
* Playfair Display font — SIL Open Font License
* Lato font — SIL Open Font License
* WooCommerce — https://woocommerce.com — GPLv3
