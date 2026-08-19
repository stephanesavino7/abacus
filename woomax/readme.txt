=== WooMax ===
Contributors: woomax
Tags: woocommerce, e-commerce, custom-background, custom-colors, custom-header, custom-logo, custom-menu, featured-images, footer-widgets, full-width-template, theme-options, threaded-comments, translation-ready, block-patterns, wide-blocks
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.2.1
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

= 1.2.1 =
* Correction d'une erreur critique (fatale) sur les pages boutique et fiche produit : appel à une fonction inexistante `woomax_get_option()` dans le gabarit du fil d'Ariane, remplacé par `get_theme_mod( 'woomax_show_breadcrumbs' )`.
* Correction de l'affichage des icônes : le thème enregistrait Font Awesome sous le handle « font-awesome », déjà utilisé par Elementor pour sa version 4.x. WordPress ignorait alors la version 6 du thème et les icônes (fa-solid, fa-truck-fast…) ne s'affichaient plus. Font Awesome 6 est désormais chargé sous un handle dédié « woomax-fontawesome ».
* Les badges de réassurance et le bloc d'information de la fiche produit s'affichent désormais avec leurs valeurs par défaut, sans configuration préalable dans le Customizer.
* Import de démo Elementor entièrement revu : importe désormais 3 pages en allemand (Startseite, Über uns, Kontakt) prêtes pour une boutique de bois de chauffage, crée automatiquement le menu de navigation principal (Startseite, Shop, Über uns, Kontakt) et définit la page d'accueil. Icônes des widgets Elementor adaptées à la nomenclature compatible (FA5/FA6) pour un rendu correct dans l'éditeur.

= 1.2.0 =
* Icônes vectorielles à la place des emojis : les badges de réassurance de la fiche produit acceptent désormais des classes Font Awesome (ex. « fa-solid fa-truck-fast ») ou un nom d'icône court, avec repli automatique sur l'emoji si besoin. L'interface d'administration (installateur de plugins, statuts, titres de sections) utilise les icônes Dashicons de WordPress.
* Nouveau module « Modèles Elementor » (menu WooMax) : import en un clic d'une page d'accueil pré-conçue et entièrement modifiable dans Elementor (bannière héro, arguments de réassurance, produits populaires, catégories, appel à l'action). Le modèle n'utilise que des widgets du noyau d'Elementor et des shortcodes WooCommerce — aucune extension Pro requise. Option pour définir automatiquement la page comme page d'accueil du site.
* Correction du style de la fiche produit : mise en page galerie / résumé côte à côte fiabilisée, onglets (description, avis...) désormais correctement stylés, section « produits associés » et bloc bas de page affichés en pleine largeur avec une grille responsive.

= 1.1.0 =
* Compatibilité Elementor complète : support natif, largeur de contenu synchronisée avec le Customizer, catégorie de widgets « WooMax », et deux gabarits de page (Elementor pleine largeur avec en-tête/pied de page, et Elementor canevas vierge). Prise en charge des emplacements du Theme Builder d'Elementor Pro (en-tête, pied de page, single, archive).
* Nouveau menu d'administration dédié « WooMax » avec tableau de bord et installateur de plugins en un clic (installation + activation via AJAX sécurisé, depuis WordPress.org).
* Installateur proposant WooCommerce et Elementor (requis), des extensions Elementor pour WooCommerce (WooLentor, Essential Addons, Happy Elementor Addons) et des utilitaires recommandés (Contact Form 7, Yoast SEO). Notice d'administration lorsque des plugins requis manquent.
* Personnalisation avancée de la fiche produit (Customizer ▸ Boutique ▸ Infos fiche produit) : affichage/masquage du titre, de la notation, du prix, de la description, du formulaire d'achat, de la référence (SKU), des catégories, des étiquettes, de la disponibilité et des boutons de partage ; réorganisation de l'ordre des éléments ; badges de réassurance personnalisables et bloc d'information additionnel (livraison, garantie…).

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
