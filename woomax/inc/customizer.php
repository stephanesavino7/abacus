<?php
/**
 * WooMax Customizer – Panneau de personnalisation complet
 *
 * @package WooMax
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', 'woomax_customize_register' );

function woomax_customize_register( $wp_customize ) {

    // ── Helpers ──────────────────────────────────────────────────────────────
    $add_section = function( $id, $title, $panel = '', $priority = 10 ) use ( $wp_customize ) {
        $args = [ 'title' => $title, 'priority' => $priority ];
        if ( $panel ) $args['panel'] = $panel;
        $wp_customize->add_section( $id, $args );
    };
    $add_color = function( $id, $label, $default, $section, $desc = '', $selector = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, [
            'label'       => $label,
            'description' => $desc,
            'section'     => $section,
        ] ) );
    };
    $add_select = function( $id, $label, $default, $section, $choices, $desc = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( $id, [
            'label'       => $label,
            'description' => $desc,
            'section'     => $section,
            'type'        => 'select',
            'choices'     => $choices,
        ] );
    };
    $add_range = function( $id, $label, $default, $section, $min, $max, $step = 1, $desc = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( $id, [
            'label'       => $label,
            'description' => $desc,
            'section'     => $section,
            'type'        => 'range',
            'input_attrs' => [ 'min' => $min, 'max' => $max, 'step' => $step ],
        ] );
    };
    $add_text = function( $id, $label, $default, $section, $desc = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'       => $label,
            'description' => $desc,
            'section'     => $section,
            'type'        => 'text',
        ] );
    };
    $add_textarea = function( $id, $label, $default, $section, $desc = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'wp_kses_post',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'       => $label,
            'description' => $desc,
            'section'     => $section,
            'type'        => 'textarea',
        ] );
    };
    $add_checkbox = function( $id, $label, $default, $section, $desc = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => function( $v ) { return (bool) $v; },
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'       => $label,
            'description' => $desc,
            'section'     => $section,
            'type'        => 'checkbox',
        ] );
    };
    $add_image = function( $id, $label, $default, $section, $desc = '' ) use ( $wp_customize ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, [
            'label'       => $label,
            'description' => $desc,
            'section'     => $section,
        ] ) );
    };

    // ════════════════════════════════════════════════════════════════════════
    // PANELS
    // ════════════════════════════════════════════════════════════════════════
    $panels = [
        'woomax_panel_general'    => [ 'title' => '⚙️ WooMax – Général',        'priority' => 1  ],
        'woomax_panel_colors'     => [ 'title' => '🎨 WooMax – Couleurs',        'priority' => 2  ],
        'woomax_panel_typography' => [ 'title' => '🔤 WooMax – Typographie',     'priority' => 3  ],
        'woomax_panel_header'     => [ 'title' => '🔝 WooMax – En-tête',         'priority' => 4  ],
        'woomax_panel_homepage'   => [ 'title' => '🏠 WooMax – Page d\'accueil', 'priority' => 5  ],
        'woomax_panel_shop'       => [ 'title' => '🛒 WooMax – Boutique',        'priority' => 6  ],
        'woomax_panel_footer'     => [ 'title' => '📋 WooMax – Pied de page',    'priority' => 7  ],
        'woomax_panel_advanced'   => [ 'title' => '🚀 WooMax – Avancé',          'priority' => 8  ],
    ];
    foreach ( $panels as $id => $args ) {
        $wp_customize->add_panel( $id, $args );
    }

    // ════════════════════════════════════════════════════════════════════════
    // 1. GÉNÉRAL
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_site',       '🌐 Identité du site',   'woomax_panel_general', 10 );
    $add_section( 'woomax_section_layout',     '📐 Mise en page',        'woomax_panel_general', 20 );
    $add_section( 'woomax_section_top_bar',    '📢 Barre d\'annonce',    'woomax_panel_general', 30 );
    $add_section( 'woomax_section_preloader',  '⏳ Préchargement',        'woomax_panel_general', 40 );

    // Site
    $add_checkbox( 'woomax_show_breadcrumbs', 'Afficher le fil d\'Ariane',       true,  'woomax_section_site' );
    $add_checkbox( 'woomax_show_back_to_top', 'Bouton « Retour en haut »',       true,  'woomax_section_site' );

    // Layout
    $add_range( 'woomax_container_width', 'Largeur du conteneur (px)', 1280, 'woomax_section_layout', 900, 1600, 20, 'Entre 900 et 1600 px.' );
    $add_range( 'woomax_section_padding', 'Espacement des sections (px)', 80, 'woomax_section_layout', 40, 160, 8, 'Espacement vertical des sections.' );
    $add_range( 'woomax_border_radius', 'Rayon des bordures (px)', 6, 'woomax_section_layout', 0, 30, 1, '0 = carré ; 20+ = arrondi.' );

    // Top bar
    $add_checkbox( 'woomax_show_top_bar',   'Afficher la barre d\'annonce',      false, 'woomax_section_top_bar' );
    $add_textarea( 'woomax_top_bar_text',   'Texte de l\'annonce',
        '🚚 Livraison gratuite dès 50€ — Code : BIENVENUE10', 'woomax_section_top_bar' );
    $add_color( 'woomax_top_bar_bg',    'Fond de la barre',  '#1e1b18', 'woomax_section_top_bar' );
    $add_color( 'woomax_top_bar_color', 'Texte de la barre', '#ffffff', 'woomax_section_top_bar' );

    // Preloader
    $add_checkbox( 'woomax_show_preloader',   'Activer le préchargement', false, 'woomax_section_preloader' );
    $add_color(    'woomax_preloader_bg',     'Couleur de fond',  '#ffffff', 'woomax_section_preloader' );
    $add_color(    'woomax_preloader_color',  'Couleur du loader','#8B5E3C', 'woomax_section_preloader' );

    // ════════════════════════════════════════════════════════════════════════
    // 2. COULEURS
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_main_colors',   '🎨 Couleurs principales', 'woomax_panel_colors', 10 );
    $add_section( 'woomax_section_text_colors',   '✏️ Couleurs du texte',    'woomax_panel_colors', 20 );
    $add_section( 'woomax_section_bg_colors',     '🖼️ Arrière-plans',        'woomax_panel_colors', 30 );
    $add_section( 'woomax_section_button_colors', '🔘 Boutons',              'woomax_panel_colors', 40 );

    // Main colors
    $add_color( 'woomax_primary_color',   'Couleur primaire',   '#8B5E3C', 'woomax_section_main_colors', 'Couleur principale du thème (boutons, liens, accents).' );
    $add_color( 'woomax_secondary_color', 'Couleur secondaire', '#4A7C59', 'woomax_section_main_colors', 'Couleur secondaire (badges, éléments alternatifs).' );
    $add_color( 'woomax_accent_color',    'Couleur d\'accent',  '#D4A853', 'woomax_section_main_colors', 'Couleur d\'accentuation (étoiles, prix spéciaux).' );

    // Presets
    $wp_customize->add_setting( 'woomax_color_preset', [
        'default'           => 'natural-wood',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'woomax_color_preset', [
        'label'   => '🎨 Présets de couleurs rapides',
        'section' => 'woomax_section_main_colors',
        'type'    => 'select',
        'choices' => [
            'natural-wood'  => '🌿 Bois Naturel (défaut)',
            'midnight-blue' => '🌙 Bleu Nuit Élégant',
            'rose-gold'     => '🌸 Rose Doré',
            'forest-green'  => '🌲 Vert Forêt',
            'slate-modern'  => '🪨 Ardoise Moderne',
            'burgundy-wine' => '🍷 Bordeaux Classique',
            'ocean-breeze'  => '🌊 Bleu Océan',
            'golden-luxury' => '✨ Or Luxe',
        ],
        'description' => 'Choisissez un préset pour appliquer un jeu de couleurs harmonisé.',
    ] );

    // Text colors
    $add_color( 'woomax_text_color',        'Texte principal',       '#2c2c2c', 'woomax_section_text_colors' );
    $add_color( 'woomax_text_muted_color',  'Texte secondaire',      '#777777', 'woomax_section_text_colors' );
    $add_color( 'woomax_border_color',      'Couleur des bordures',  '#e8e0d5', 'woomax_section_text_colors' );

    // Background
    $add_color( 'woomax_bg_color',          'Fond principal',        '#ffffff', 'woomax_section_bg_colors' );
    $add_color( 'woomax_bg_secondary_color','Fond secondaire',        '#f9f6f1', 'woomax_section_bg_colors', 'Fond des sections alternées.' );
    $add_color( 'woomax_bg_dark_color',     'Fond sombre',           '#1e1b18', 'woomax_section_bg_colors', 'Fond du footer et sections sombres.' );

    // Buttons
    $add_select( 'woomax_btn_style', 'Style du texte des boutons', 'uppercase', 'woomax_section_button_colors', [
        'uppercase'  => 'MAJUSCULES',
        'capitalize' => 'Première Lettre',
        'normal'     => 'Normal',
    ] );
    $add_range( 'woomax_btn_border_radius', 'Rayon des boutons (px)', 4, 'woomax_section_button_colors', 0, 50, 1 );

    // ════════════════════════════════════════════════════════════════════════
    // 3. TYPOGRAPHIE
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_typo_heading', '📌 Titres',          'woomax_panel_typography', 10 );
    $add_section( 'woomax_section_typo_body',    '📝 Corps de texte',  'woomax_panel_typography', 20 );

    $google_fonts = [
        'Playfair Display' => 'Playfair Display (Serif élégant)',
        'Cormorant Garamond'=> 'Cormorant Garamond (Serif classique)',
        'Libre Baskerville'=> 'Libre Baskerville (Serif traditionnel)',
        'Merriweather'     => 'Merriweather (Serif lisible)',
        'Josefin Sans'     => 'Josefin Sans (Sans-serif géométrique)',
        'Montserrat'       => 'Montserrat (Sans-serif moderne)',
        'Raleway'          => 'Raleway (Sans-serif élégant)',
        'Poppins'          => 'Poppins (Sans-serif arrondi)',
        'Nunito'           => 'Nunito (Sans-serif doux)',
        'Inter'            => 'Inter (Sans-serif neutre)',
        'Roboto'           => 'Roboto (Sans-serif standard)',
        'Open Sans'        => 'Open Sans (Sans-serif lisible)',
        'Oswald'           => 'Oswald (Condensé impactant)',
        'Bebas Neue'       => 'Bebas Neue (Display bold)',
        'Cinzel'           => 'Cinzel (Romain majestueux)',
        'EB Garamond'      => 'EB Garamond (Classique raffiné)',
        'Lora'             => 'Lora (Serif doux)',
        'DM Serif Display' => 'DM Serif Display (Display moderne)',
        'Cardo'            => 'Cardo (Serif académique)',
        'Spectral'         => 'Spectral (Serif digital)',
    ];

    $body_fonts = [
        'Lato'        => 'Lato (Standard)',
        'Open Sans'   => 'Open Sans (Classique)',
        'Roboto'      => 'Roboto (Google)',
        'Inter'       => 'Inter (Moderne)',
        'Poppins'     => 'Poppins (Arrondi)',
        'Nunito'      => 'Nunito (Doux)',
        'Montserrat'  => 'Montserrat (Premium)',
        'Raleway'     => 'Raleway (Élégant)',
        'Source Sans Pro' => 'Source Sans Pro (Adobe)',
        'Mulish'      => 'Mulish (Géométrique)',
        'DM Sans'     => 'DM Sans (Contemporain)',
        'Karla'       => 'Karla (Humaniste)',
    ];

    // Headings
    $add_select( 'woomax_heading_font', 'Police des titres', 'Playfair Display', 'woomax_section_typo_heading', $google_fonts );
    $add_select( 'woomax_heading_weight', 'Graisse des titres', '700', 'woomax_section_typo_heading', [
        '300' => 'Léger (300)', '400' => 'Normal (400)', '600' => 'Semi-gras (600)',
        '700' => 'Gras (700)', '800' => 'Extra-gras (800)',
    ] );
    $add_select( 'woomax_heading_transform', 'Transformation', 'none', 'woomax_section_typo_heading', [
        'none'       => 'Aucune',
        'uppercase'  => 'MAJUSCULES',
        'capitalize' => 'Première Lettre',
        'lowercase'  => 'minuscules',
    ] );

    // Body
    $add_select( 'woomax_body_font',       'Police du corps',        'Lato', 'woomax_section_typo_body', $body_fonts );
    $add_range(  'woomax_body_font_size',  'Taille de base (px)',     16,    'woomax_section_typo_body', 13, 22, 1 );
    $add_select( 'woomax_body_font_weight','Graisse du corps',        '400', 'woomax_section_typo_body', [
        '300' => 'Léger (300)', '400' => 'Normal (400)', '500' => 'Médium (500)', '600' => 'Semi-gras (600)',
    ] );
    $add_select( 'woomax_body_line_height','Hauteur de ligne',        '1.7', 'woomax_section_typo_body', [
        '1.4' => '1.4 (Compact)', '1.5' => '1.5', '1.6' => '1.6', '1.7' => '1.7 (Défaut)', '1.8' => '1.8', '2' => '2 (Aéré)',
    ] );

    // ════════════════════════════════════════════════════════════════════════
    // 4. EN-TÊTE
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_header_style',  '🎨 Style de l\'en-tête',  'woomax_panel_header', 10 );
    $add_section( 'woomax_section_header_colors', '🎨 Couleurs en-tête',      'woomax_panel_header', 20 );
    $add_section( 'woomax_section_header_logo',   '🏷️ Logo',                 'woomax_panel_header', 30 );

    // Style
    $add_select( 'woomax_header_style', 'Style de l\'en-tête', 'default', 'woomax_section_header_style', [
        'default'  => 'Défaut (logo à gauche)',
        'centered' => 'Centré (logo au centre)',
        'minimal'  => 'Minimal (épuré)',
        'bold'     => 'Bold (fond plein)',
    ] );
    $add_checkbox( 'woomax_header_sticky',      'En-tête fixe (sticky)',           true,  'woomax_section_header_style' );
    $add_checkbox( 'woomax_header_transparent', 'En-tête transparent (page d\'accueil)', false, 'woomax_section_header_style' );
    $add_range(    'woomax_header_height',      'Hauteur de l\'en-tête (px)',       80, 'woomax_section_header_style', 50, 140, 4 );
    $add_checkbox( 'woomax_show_search',        'Afficher la recherche',            true,  'woomax_section_header_style' );
    $add_checkbox( 'woomax_show_wishlist_icon', 'Afficher la liste de souhaits',   true,  'woomax_section_header_style' );
    $add_checkbox( 'woomax_show_account_icon',  'Afficher le lien compte',         true,  'woomax_section_header_style' );
    $add_checkbox( 'woomax_show_cart_icon',     'Afficher le panier',              true,  'woomax_section_header_style' );

    // Colors
    $add_color( 'woomax_header_bg',          'Fond de l\'en-tête',      '#ffffff', 'woomax_section_header_colors' );
    $add_color( 'woomax_header_text_color',  'Texte de l\'en-tête',     '#2c2c2c', 'woomax_section_header_colors' );
    $add_color( 'woomax_header_sticky_bg',   'Fond sticky (après scroll)','#ffffff', 'woomax_section_header_colors' );

    // Logo
    $add_image( 'woomax_logo_light', 'Logo pour fond sombre (transparent header)', '', 'woomax_section_header_logo' );
    $add_range( 'woomax_logo_max_height', 'Hauteur max du logo (px)', 50, 'woomax_section_header_logo', 20, 120, 4 );
    $add_text(  'woomax_logo_text', 'Texte si pas de logo image', get_bloginfo('name'), 'woomax_section_header_logo' );

    // ════════════════════════════════════════════════════════════════════════
    // 5. PAGE D'ACCUEIL
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_hero',        '🦸 Section Hero',             'woomax_panel_homepage', 10 );
    $add_section( 'woomax_section_features',    '✅ Bande de caractéristiques', 'woomax_panel_homepage', 20 );
    $add_section( 'woomax_section_home_prods',  '🛍️ Produits en vedette',      'woomax_panel_homepage', 30 );
    $add_section( 'woomax_section_home_cats',   '📂 Catégories vedettes',       'woomax_panel_homepage', 40 );
    $add_section( 'woomax_section_home_banner', '🖼️ Bannière promo',           'woomax_panel_homepage', 50 );
    $add_section( 'woomax_section_home_tests',  '⭐ Témoignages',              'woomax_panel_homepage', 60 );
    $add_section( 'woomax_section_newsletter',  '📧 Newsletter',               'woomax_panel_homepage', 70 );

    // Hero
    $add_select( 'woomax_hero_style', 'Style du hero', 'full-screen', 'woomax_section_hero', [
        'full-screen' => 'Plein écran',
        'half-screen' => 'Mi-écran',
        'slider'      => 'Diaporama (slider)',
        'split'       => 'Divisé (image + texte)',
        'video'       => 'Fond vidéo',
    ] );
    $add_image(    'woomax_hero_image',    'Image de fond',        '', 'woomax_section_hero' );
    $add_text(     'woomax_hero_tag',      'Tag / surtitre',       __( 'Nouveau · Collection 2025', 'woomax' ), 'woomax_section_hero' );
    $add_textarea( 'woomax_hero_title',    'Titre principal',      __( 'Des produits naturels,<br>pour une vie meilleure', 'woomax' ), 'woomax_section_hero' );
    $add_textarea( 'woomax_hero_subtitle', 'Sous-titre',           __( 'Découvrez notre sélection de produits artisanaux, issus de matières premières durables et certifiées.', 'woomax' ), 'woomax_section_hero' );
    $add_text(     'woomax_hero_btn1_text','Texte bouton 1',       __( 'Découvrir la boutique', 'woomax' ), 'woomax_section_hero' );
    $add_text(     'woomax_hero_btn1_url', 'URL bouton 1',         '/boutique', 'woomax_section_hero' );
    $add_text(     'woomax_hero_btn2_text','Texte bouton 2',       __( 'Notre histoire', 'woomax' ), 'woomax_section_hero' );
    $add_text(     'woomax_hero_btn2_url', 'URL bouton 2',         '/a-propos', 'woomax_section_hero' );
    $add_color(    'woomax_hero_overlay',  'Couleur de l\'overlay','#000000', 'woomax_section_hero' );
    $add_range(    'woomax_hero_overlay_opacity', 'Opacité overlay (%)', 50, 'woomax_section_hero', 0, 90, 5 );

    // Features
    $add_checkbox( 'woomax_show_features', 'Afficher la bande caractéristiques', true, 'woomax_section_features' );
    $add_color(    'woomax_features_bg',   'Couleur de fond',   '#8B5E3C',  'woomax_section_features' );
    $add_color(    'woomax_features_text', 'Couleur du texte',  '#ffffff',  'woomax_section_features' );
    for ( $i = 1; $i <= 4; $i++ ) {
        $add_text( "woomax_feature_{$i}_icon",  "Icône {$i} (FontAwesome)", [ 'fa-truck', 'fa-shield-halved', 'fa-rotate-left', 'fa-headset' ][$i-1], 'woomax_section_features' );
        $add_text( "woomax_feature_{$i}_title", "Titre {$i}", [ 'Livraison gratuite', 'Paiement sécurisé', 'Retours 30 jours', 'Support 7j/7' ][$i-1], 'woomax_section_features' );
        $add_text( "woomax_feature_{$i}_text",  "Description {$i}", [ 'Dès 50€ d\'achat', 'SSL · Stripe · PayPal', 'Satisfait ou remboursé', 'Par chat et téléphone' ][$i-1], 'woomax_section_features' );
    }

    // Products
    $add_checkbox( 'woomax_show_home_products', 'Afficher les produits vedette', true, 'woomax_section_home_prods' );
    $add_text( 'woomax_home_products_title', 'Titre de la section', __( 'Nos Meilleures Ventes', 'woomax' ), 'woomax_section_home_prods' );
    $add_select( 'woomax_home_products_type', 'Type de produits', 'best_selling', 'woomax_section_home_prods', [
        'featured'     => 'Produits en vedette',
        'best_selling' => 'Meilleures ventes',
        'newest'       => 'Derniers arrivages',
        'on_sale'      => 'En promotion',
    ] );
    $add_range( 'woomax_home_products_count', 'Nombre de produits', 8, 'woomax_section_home_prods', 4, 20, 2 );
    $add_select( 'woomax_home_products_cols', 'Colonnes', '4', 'woomax_section_home_prods', [
        '2' => '2 colonnes', '3' => '3 colonnes', '4' => '4 colonnes', '5' => '5 colonnes',
    ] );

    // Categories
    $add_checkbox( 'woomax_show_home_cats', 'Afficher les catégories', true, 'woomax_section_home_cats' );
    $add_text( 'woomax_home_cats_title', 'Titre de la section', __( 'Explorez nos univers', 'woomax' ), 'woomax_section_home_cats' );

    // Banner
    $add_checkbox( 'woomax_show_home_banner', 'Afficher la bannière promo', true, 'woomax_section_home_banner' );
    $add_image(    'woomax_banner_image',  'Image de la bannière', '', 'woomax_section_home_banner' );
    $add_text(     'woomax_banner_tag',    'Surtitre',   __( 'Offre spéciale', 'woomax' ), 'woomax_section_home_banner' );
    $add_textarea( 'woomax_banner_title',  'Titre',      __( 'Jusqu\'à <span style="color:var(--woomax-accent)">-40%</span><br>sur nos essentiels', 'woomax' ), 'woomax_section_home_banner' );
    $add_textarea( 'woomax_banner_text',   'Description',__( 'Profitez de nos offres exclusives sur une sélection de produits.', 'woomax' ), 'woomax_section_home_banner' );
    $add_text(     'woomax_banner_btn',    'Texte bouton',__( 'Voir les promotions', 'woomax' ), 'woomax_section_home_banner' );
    $add_text(     'woomax_banner_url',    'URL du bouton','/boutique?sale', 'woomax_section_home_banner' );

    // Testimonials
    $add_checkbox( 'woomax_show_testimonials', 'Afficher les témoignages', true, 'woomax_section_home_tests' );
    $add_text(     'woomax_testimonials_title', 'Titre', __( 'Ce que disent nos clients', 'woomax' ), 'woomax_section_home_tests' );

    // Newsletter
    $add_checkbox( 'woomax_show_newsletter',      'Afficher la newsletter',   true,  'woomax_section_newsletter' );
    $add_text(     'woomax_newsletter_title',      'Titre',   __( 'Restez informé', 'woomax' ), 'woomax_section_newsletter' );
    $add_textarea( 'woomax_newsletter_subtitle',   'Sous-titre', __( 'Inscrivez-vous pour recevoir nos offres exclusives, nouveautés et conseils directement dans votre boîte mail.', 'woomax' ), 'woomax_section_newsletter' );
    $add_text(     'woomax_newsletter_placeholder','Placeholder champ email', __( 'Votre adresse e-mail…', 'woomax' ), 'woomax_section_newsletter' );
    $add_text(     'woomax_newsletter_btn',        'Texte bouton',           __( "M'inscrire", 'woomax' ), 'woomax_section_newsletter' );

    // ════════════════════════════════════════════════════════════════════════
    // 6. BOUTIQUE
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_shop_layout',  '📐 Mise en page boutique', 'woomax_panel_shop', 10 );
    $add_section( 'woomax_section_product_card', '🃏 Cartes produits',       'woomax_panel_shop', 20 );
    $add_section( 'woomax_section_single_prod',  '📄 Page produit',          'woomax_panel_shop', 30 );

    // Shop layout
    $add_select( 'woomax_shop_sidebar', 'Position de la sidebar', 'left', 'woomax_section_shop_layout', [
        'left'  => 'Gauche',
        'right' => 'Droite',
        'none'  => 'Sans sidebar',
    ] );
    $add_select( 'woomax_shop_columns', 'Colonnes produits', '4', 'woomax_section_shop_layout', [
        '2' => '2 colonnes', '3' => '3 colonnes', '4' => '4 colonnes', '5' => '5 colonnes',
    ] );
    $add_range( 'woomax_shop_per_page', 'Produits par page', 12, 'woomax_section_shop_layout', 6, 60, 6 );
    $add_checkbox( 'woomax_show_shop_toolbar', 'Afficher la barre d\'outils', true, 'woomax_section_shop_layout' );

    // Product card
    $add_select( 'woomax_card_hover', 'Effet survol de la carte', 'slide-up', 'woomax_section_product_card', [
        'slide-up' => 'Bouton glisse vers le haut',
        'zoom'     => 'Zoom image seul',
        'fade'     => 'Apparition des actions',
    ] );
    $add_checkbox( 'woomax_show_rating',       'Afficher les étoiles',         true, 'woomax_section_product_card' );
    $add_checkbox( 'woomax_show_wishlist_btn', 'Bouton liste de souhaits',     true, 'woomax_section_product_card' );
    $add_checkbox( 'woomax_show_compare_btn',  'Bouton comparaison produits',  false,'woomax_section_product_card' );
    $add_checkbox( 'woomax_show_quickview',    'Vue rapide (Quick View)',       true, 'woomax_section_product_card' );
    $add_select( 'woomax_card_image_ratio', 'Ratio d\'image', 'square', 'woomax_section_product_card', [
        'square'    => 'Carré (1:1)',
        'portrait'  => 'Portrait (3:4)',
        'landscape' => 'Paysage (4:3)',
        'wide'      => 'Large (16:9)',
    ] );

    // Single product
    $add_select( 'woomax_single_layout', 'Disposition page produit', 'left', 'woomax_section_single_prod', [
        'left'   => 'Image à gauche',
        'right'  => 'Image à droite',
        'full'   => 'Image pleine largeur',
    ] );
    $add_checkbox( 'woomax_show_sticky_atc',       'Barre « Ajouter au panier » fixe',     true, 'woomax_section_single_prod' );
    $add_checkbox( 'woomax_show_related_products', 'Produits associés',                    true, 'woomax_section_single_prod' );
    $add_range(    'woomax_related_products_count','Nb de produits associés',              4, 'woomax_section_single_prod', 2, 8, 2 );
    $add_checkbox( 'woomax_show_upsells',          'Upsells',                              true, 'woomax_section_single_prod' );

    // ════════════════════════════════════════════════════════════════════════
    // 7. PIED DE PAGE
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_footer_style',   '🎨 Style footer',      'woomax_panel_footer', 10 );
    $add_section( 'woomax_section_footer_content', '📝 Contenu footer',    'woomax_panel_footer', 20 );
    $add_section( 'woomax_section_footer_bottom',  '📋 Barre du bas',      'woomax_panel_footer', 30 );

    // Style
    $add_color( 'woomax_footer_bg',            'Fond du footer',           '#1e1b18', 'woomax_section_footer_style' );
    $add_color( 'woomax_footer_text_color',    'Texte du footer',          '#c8bfb0', 'woomax_section_footer_style' );
    $add_color( 'woomax_footer_heading_color', 'Titres du footer',         '#ffffff', 'woomax_section_footer_style' );
    $add_select( 'woomax_footer_columns', 'Nombre de colonnes', '4', 'woomax_section_footer_style', [
        '2' => '2 colonnes', '3' => '3 colonnes', '4' => '4 colonnes',
    ] );

    // Content
    $add_textarea( 'woomax_footer_about', 'Description de la marque', __( 'WooMax est votre boutique en ligne dédiée à des produits artisanaux et naturels de qualité premium.', 'woomax' ), 'woomax_section_footer_content' );
    $add_text(     'woomax_footer_col2_title', 'Titre colonne 2', __( 'Boutique', 'woomax' ),   'woomax_section_footer_content' );
    $add_text(     'woomax_footer_col3_title', 'Titre colonne 3', __( 'Informations', 'woomax' ),'woomax_section_footer_content' );
    $add_text(     'woomax_footer_col4_title', 'Titre colonne 4', __( 'Contact', 'woomax' ),    'woomax_section_footer_content' );
    $add_text(     'woomax_footer_phone',  'Téléphone',    '+33 1 23 45 67 89', 'woomax_section_footer_content' );
    $add_text(     'woomax_footer_email',  'E-mail',       'contact@woomax.com','woomax_section_footer_content' );
    $add_textarea( 'woomax_footer_address','Adresse',      '12 rue de l\'Artisan, 75001 Paris', 'woomax_section_footer_content' );

    // Social
    $add_text( 'woomax_social_facebook',  'Facebook URL',  '', 'woomax_section_footer_content' );
    $add_text( 'woomax_social_instagram', 'Instagram URL', '', 'woomax_section_footer_content' );
    $add_text( 'woomax_social_twitter',   'Twitter/X URL', '', 'woomax_section_footer_content' );
    $add_text( 'woomax_social_youtube',   'YouTube URL',   '', 'woomax_section_footer_content' );
    $add_text( 'woomax_social_pinterest', 'Pinterest URL', '', 'woomax_section_footer_content' );
    $add_text( 'woomax_social_linkedin',  'LinkedIn URL',  '', 'woomax_section_footer_content' );
    $add_text( 'woomax_social_tiktok',    'TikTok URL',    '', 'woomax_section_footer_content' );

    // Bottom bar
    $add_textarea( 'woomax_footer_copyright', 'Texte de copyright',
        sprintf( '© %d %s – Tous droits réservés.', date('Y'), get_bloginfo('name') ),
        'woomax_section_footer_bottom'
    );
    $add_checkbox( 'woomax_show_payment_icons', 'Afficher les icônes de paiement', true, 'woomax_section_footer_bottom' );
    $add_checkbox( 'woomax_show_footer_menu',   'Afficher le menu du bas',         true, 'woomax_section_footer_bottom' );

    // ════════════════════════════════════════════════════════════════════════
    // 8. AVANCÉ
    // ════════════════════════════════════════════════════════════════════════
    $add_section( 'woomax_section_perf',       '⚡ Performance',     'woomax_panel_advanced', 10 );
    $add_section( 'woomax_section_custom_css', '🎨 CSS personnalisé', 'woomax_panel_advanced', 20 );
    $add_section( 'woomax_section_custom_js',  '⚙️ JS personnalisé',  'woomax_panel_advanced', 30 );

    // Performance
    $add_checkbox( 'woomax_lazy_load',   'Chargement différé des images',   true,  'woomax_section_perf' );
    $add_checkbox( 'woomax_defer_js',    'Différer le JS non critique',      false, 'woomax_section_perf' );
    $add_checkbox( 'woomax_animations',  'Animations d\'apparition (scroll)',true,  'woomax_section_perf' );

    // Custom CSS
    $wp_customize->add_setting( 'woomax_custom_css', [
        'default'           => '',
        'sanitize_callback' => 'wp_strip_all_tags',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'woomax_custom_css', [
        'label'       => 'CSS personnalisé',
        'description' => 'Ce CSS sera appliqué après les styles du thème.',
        'section'     => 'woomax_section_custom_css',
        'type'        => 'textarea',
    ] );

    // Custom JS
    $add_textarea( 'woomax_custom_js', 'JavaScript personnalisé',
        '', 'woomax_section_custom_js', 'Ajouté avant </body>.' );
    $add_textarea( 'woomax_custom_js_head', 'JS dans <head>',
        '', 'woomax_section_custom_js', 'Ajouté dans <head>.' );

    // Google Analytics / GTM
    $add_text( 'woomax_ga_id',  'Google Analytics ID (GA4)', '', 'woomax_section_perf', 'Ex : G-XXXXXXXXXX' );
    $add_text( 'woomax_gtm_id', 'Google Tag Manager ID',     '', 'woomax_section_perf', 'Ex : GTM-XXXXXXX' );
    $add_text( 'woomax_pixel_id','Facebook Pixel ID',         '', 'woomax_section_perf', 'Ex : 1234567890' );
}

// ─── Output custom JS head ────────────────────────────────────────────────────
add_action( 'wp_head', function() {
    // Google Tag Manager
    $gtm = get_theme_mod( 'woomax_gtm_id', '' );
    if ( $gtm ) {
        echo "<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $gtm ) . "');</script>\n<!-- End Google Tag Manager -->\n";
    }
    // GA4
    $ga = get_theme_mod( 'woomax_ga_id', '' );
    if ( $ga ) {
        echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $ga ) . "\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js( $ga ) . "');</script>\n";
    }
    // Custom JS head
    $js_head = get_theme_mod( 'woomax_custom_js_head', '' );
    if ( $js_head ) echo "\n<script>\n" . wp_strip_all_tags( $js_head ) . "\n</script>\n";
}, 99 );

// ─── Output custom JS footer ──────────────────────────────────────────────────
add_action( 'wp_footer', function() {
    $js = get_theme_mod( 'woomax_custom_js', '' );
    if ( $js ) echo "\n<script>\n" . wp_strip_all_tags( $js ) . "\n</script>\n";
    // Facebook Pixel
    $pixel = get_theme_mod( 'woomax_pixel_id', '' );
    if ( $pixel ) {
        echo "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','" . esc_js( $pixel ) . "');fbq('track','PageView');</script>\n";
    }
    // GTM noscript
    $gtm = get_theme_mod( 'woomax_gtm_id', '' );
    if ( $gtm ) {
        echo "<!-- Google Tag Manager (noscript) -->\n<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=" . esc_attr( $gtm ) . "\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>\n<!-- End Google Tag Manager (noscript) -->\n";
    }
}, 99 );

// ─── Color preset applicator ──────────────────────────────────────────────────
add_action( 'customize_save_after', function( $wp_customize ) {
    $preset = get_theme_mod( 'woomax_color_preset', '' );
    $presets = [
        'midnight-blue' => [ '#1a2d5a', '#c0392b', '#f1c40f' ],
        'rose-gold'     => [ '#c9748a', '#8a5674', '#f4c89f' ],
        'forest-green'  => [ '#2d6a4f', '#95d5b2', '#e9c46a' ],
        'slate-modern'  => [ '#4a4e69', '#9a8c98', '#c9ada7' ],
        'burgundy-wine' => [ '#722f37', '#c17767', '#e8c99a' ],
        'ocean-breeze'  => [ '#0077b6', '#00b4d8', '#90e0ef' ],
        'golden-luxury' => [ '#b5860d', '#8b6914', '#ffe066' ],
    ];
    if ( isset( $presets[$preset] ) ) {
        $wp_customize->set_post_value( 'woomax_primary_color',   $presets[$preset][0] );
        $wp_customize->set_post_value( 'woomax_secondary_color', $presets[$preset][1] );
        $wp_customize->set_post_value( 'woomax_accent_color',    $presets[$preset][2] );
    }
} );
