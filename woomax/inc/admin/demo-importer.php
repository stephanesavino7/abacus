<?php
/**
 * WooMax – Importateur de démo Elementor (contenu en allemand)
 *
 * Importe en un clic un ensemble de pages pré-conçues et entièrement modifiables
 * dans Elementor : page d'accueil, « Über uns » (à propos) et « Kontakt » (contact).
 * Le contenu est rédigé en allemand (boutique de bois de chauffage). Un menu de
 * navigation principal est également créé et assigné automatiquement.
 *
 * Les modèles n'utilisent que des widgets du noyau d'Elementor (titre, texte,
 * bouton, icon-box, séparateur) et des shortcodes WooCommerce ([products],
 * [product_categories]) : aucune extension Pro requise.
 *
 * @package WooMax
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class WooMax_Demo_Importer {

    /**
     * Initialise les hooks.
     */
    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'register_menu' ], 20 );
        add_action( 'wp_ajax_woomax_import_demo', [ __CLASS__, 'ajax_import_demo' ] );
    }

    /**
     * Ajoute la sous-page « Démo Elementor » au menu WooMax.
     */
    public static function register_menu() {
        add_submenu_page(
            'woomax',
            __( 'Démo Elementor', 'woomax' ),
            __( 'Démo Elementor', 'woomax' ),
            'edit_pages',
            'woomax-templates',
            [ __CLASS__, 'render_page' ]
        );
    }

    /**
     * Génère un identifiant court unique (format Elementor).
     */
    private static function eid() {
        return dechex( wp_rand( 0x1000000, 0xfffffff ) );
    }

    /* ═══════════════════════════════════════════════════════════════════════
     *  PAGE D'ADMINISTRATION
     * ═══════════════════════════════════════════════════════════════════════ */

    public static function render_page() {
        $elementor_active = function_exists( 'woomax_is_elementor_active' ) && woomax_is_elementor_active();
        $woo_active       = class_exists( 'WooCommerce' );
        $imported         = get_option( 'woomax_demo_pages', [] );
        ?>
        <div class="wrap woomax-admin">
            <div class="woomax-admin__hero">
                <h1><?php esc_html_e( 'Démo Elementor', 'woomax' ); ?></h1>
                <p><?php esc_html_e( 'Importez un ensemble de pages pré-conçues en allemand (accueil, à propos, contact), entièrement modifiables dans Elementor. Un menu de navigation est créé automatiquement.', 'woomax' ); ?></p>
            </div>

            <?php if ( ! $elementor_active ) : ?>
                <div class="notice notice-warning" style="margin:0 20px 20px 0;">
                    <p>
                        <?php esc_html_e( 'Elementor doit être installé et activé pour importer et modifier ces modèles.', 'woomax' ); ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=woomax-plugins' ) ); ?>"><?php esc_html_e( 'Installer Elementor', 'woomax' ); ?></a>
                    </p>
                </div>
            <?php endif; ?>
            <?php if ( ! $woo_active ) : ?>
                <div class="notice notice-info" style="margin:0 20px 20px 0;">
                    <p><?php esc_html_e( 'WooCommerce est recommandé : les sections « produits » et « catégories » de la page d\'accueil s\'afficheront une fois WooCommerce actif.', 'woomax' ); ?></p>
                </div>
            <?php endif; ?>

            <div class="woomax-plugin-grid" style="grid-template-columns:repeat(auto-fill,minmax(360px,1fr));">
                <div class="woomax-plugin-card" data-template="full">
                    <div class="woomax-plugin-card__head">
                        <span class="dashicons dashicons-images-alt2"></span>
                        <div>
                            <h3><?php esc_html_e( 'Démo complète « Brennholz »', 'woomax' ); ?></h3>
                        </div>
                    </div>
                    <p class="woomax-plugin-card__desc">
                        <?php esc_html_e( 'Importe 3 pages en allemand : Startseite (accueil), Über uns (à propos) et Kontakt (contact). Crée le menu principal et définit la page d\'accueil.', 'woomax' ); ?>
                    </p>
                    <ul style="margin:0 0 14px;padding-left:18px;font-size:12.5px;color:#555;line-height:1.8;">
                        <li><?php esc_html_e( 'Startseite — bannière, réassurance, produits, catégories, CTA', 'woomax' ); ?></li>
                        <li><?php esc_html_e( 'Über uns — présentation de l\'entreprise et valeurs', 'woomax' ); ?></li>
                        <li><?php esc_html_e( 'Kontakt — coordonnées et horaires', 'woomax' ); ?></li>
                    </ul>
                    <div class="woomax-plugin-card__footer" style="flex-wrap:wrap;gap:10px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;">
                            <input type="checkbox" id="woomax-set-front" checked>
                            <?php esc_html_e( 'Définir « Startseite » comme page d\'accueil', 'woomax' ); ?>
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;">
                            <input type="checkbox" id="woomax-build-menu" checked>
                            <?php esc_html_e( 'Créer le menu de navigation', 'woomax' ); ?>
                        </label>
                        <button class="button button-primary woomax-import-demo" <?php disabled( ! $elementor_active ); ?>>
                            <span class="dashicons dashicons-download" style="margin-top:3px;"></span>
                            <?php esc_html_e( 'Importer la démo', 'woomax' ); ?>
                        </button>
                    </div>
                    <div class="woomax-notice-inline"></div>
                    <?php if ( ! empty( $imported ) ) : ?>
                        <div class="woomax-imported-list" style="margin-top:12px;font-size:12.5px;">
                            <strong><?php esc_html_e( 'Pages déjà importées :', 'woomax' ); ?></strong>
                            <ul style="margin:6px 0 0;padding-left:18px;line-height:1.9;">
                                <?php foreach ( $imported as $pid ) :
                                    if ( ! get_post( $pid ) ) continue; ?>
                                    <li>
                                        <?php echo esc_html( get_the_title( $pid ) ); ?> —
                                        <a href="<?php echo esc_url( get_edit_post_link( $pid ) ); ?>"><?php esc_html_e( 'Modifier', 'woomax' ); ?></a> ·
                                        <a href="<?php echo esc_url( get_permalink( $pid ) ); ?>" target="_blank"><?php esc_html_e( 'Voir', 'woomax' ); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /* ═══════════════════════════════════════════════════════════════════════
     *  TRAITEMENT AJAX
     * ═══════════════════════════════════════════════════════════════════════ */

    public static function ajax_import_demo() {
        check_ajax_referer( 'woomax-admin-nonce', 'nonce' );

        if ( ! current_user_can( 'edit_pages' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission refusée.', 'woomax' ) ] );
        }
        if ( ! ( function_exists( 'woomax_is_elementor_active' ) && woomax_is_elementor_active() ) ) {
            wp_send_json_error( [ 'message' => __( 'Elementor n\'est pas actif.', 'woomax' ) ] );
        }

        $set_front  = ! empty( $_POST['set_front'] )  && 'true' === sanitize_text_field( wp_unslash( $_POST['set_front'] ) );
        $build_menu = ! empty( $_POST['build_menu'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['build_menu'] ) );

        $templates = self::get_templates();
        $map       = get_option( 'woomax_demo_pages', [] );
        if ( ! is_array( $map ) ) {
            $map = [];
        }
        $results = [];

        foreach ( $templates as $key => $tpl ) {
            $page_id = self::import_page( $key, $tpl, $map );
            if ( $page_id ) {
                $map[ $key ] = $page_id;
                $results[]   = [
                    'title'     => $tpl['title'],
                    'edit_link' => get_edit_post_link( $page_id, 'raw' ),
                    'view_link' => get_permalink( $page_id ),
                ];
            }
        }

        update_option( 'woomax_demo_pages', $map );

        // Page d'accueil statique.
        if ( $set_front && ! empty( $map['home'] ) ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', (int) $map['home'] );
        }

        // Menu de navigation.
        if ( $build_menu ) {
            self::build_menu( $map );
        }

        // Régénère le CSS Elementor.
        if ( class_exists( '\Elementor\Plugin' ) ) {
            try {
                \Elementor\Plugin::$instance->files_manager->clear_cache();
            } catch ( \Throwable $e ) {
                // Sans gravité.
            }
        }

        wp_send_json_success( [
            'message' => __( 'Démo importée avec succès.', 'woomax' ),
            'pages'   => $results,
        ] );
    }

    /**
     * Crée ou met à jour une page et y injecte les données Elementor.
     *
     * @param string $key  Clé du modèle.
     * @param array  $tpl  Définition du modèle.
     * @param array  $map  Correspondance clé => post_id déjà importés.
     * @return int|false   ID de la page, ou false.
     */
    private static function import_page( $key, $tpl, $map ) {
        $existing = ! empty( $map[ $key ] ) ? (int) $map[ $key ] : 0;
        $page_id  = ( $existing && get_post( $existing ) ) ? $existing : 0;

        $postarr = [
            'post_title'   => $tpl['title'],
            'post_name'    => $tpl['slug'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ];
        if ( $page_id ) {
            $postarr['ID'] = $page_id;
            wp_update_post( $postarr );
        } else {
            $page_id = wp_insert_post( $postarr );
        }

        if ( ! $page_id || is_wp_error( $page_id ) ) {
            return false;
        }

        $data = call_user_func( $tpl['builder'] );

        update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
        update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
        update_post_meta( $page_id, '_wp_page_template', 'template-elementor-full.php' );
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
        }
        // _elementor_data : chaîne JSON échappée par des slashes.
        update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );

        return $page_id;
    }

    /**
     * Crée (ou complète) le menu principal et l'assigne à l'emplacement « primary ».
     */
    private static function build_menu( $map ) {
        $menu_name = __( 'Hauptmenü', 'woomax' );
        $menu      = wp_get_nav_menu_object( $menu_name );
        $menu_id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $menu_name );
        if ( ! $menu_id || is_wp_error( $menu_id ) ) {
            return;
        }

        // Évite les doublons : on repart d'un menu vide.
        $items = wp_get_nav_menu_items( $menu_id );
        if ( $items ) {
            foreach ( $items as $item ) {
                wp_delete_post( $item->ID, true );
            }
        }

        // 1. Startseite
        if ( ! empty( $map['home'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, [
                'menu-item-title'     => __( 'Startseite', 'woomax' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => (int) $map['home'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ] );
        }

        // 2. Shop (boutique WooCommerce)
        if ( function_exists( 'wc_get_page_id' ) ) {
            $shop_id = wc_get_page_id( 'shop' );
            if ( $shop_id && $shop_id > 0 ) {
                wp_update_nav_menu_item( $menu_id, 0, [
                    'menu-item-title'     => __( 'Shop', 'woomax' ),
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => (int) $shop_id,
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                ] );
            }
        }

        // 3. Über uns  4. Kontakt
        foreach ( [ 'about' => __( 'Über uns', 'woomax' ), 'contact' => __( 'Kontakt', 'woomax' ) ] as $k => $label ) {
            if ( ! empty( $map[ $k ] ) ) {
                wp_update_nav_menu_item( $menu_id, 0, [
                    'menu-item-title'     => $label,
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => (int) $map[ $k ],
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                ] );
            }
        }

        // Assignation à l'emplacement « primary ».
        $locations = get_theme_mod( 'nav_menu_locations', [] );
        if ( ! is_array( $locations ) ) {
            $locations = [];
        }
        $locations['primary'] = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }

    /* ═══════════════════════════════════════════════════════════════════════
     *  CONSTRUCTEURS DE WIDGETS ELEMENTOR
     * ═══════════════════════════════════════════════════════════════════════ */

    private static function heading( $text, $size = 'xl', $tag = 'h2', $color = '', $align = 'center' ) {
        $s = [ 'title' => $text, 'align' => $align, 'size' => $size, 'header_size' => $tag, 'title_color' => $color ];
        return [
            'id'         => self::eid(),
            'elType'     => 'widget',
            'widgetType' => 'heading',
            'settings'   => array_filter( $s, function ( $v ) { return '' !== $v; } ),
            'elements'   => [],
        ];
    }

    private static function text( $html, $align = 'center', $color = '' ) {
        $s = [ 'editor' => wpautop( $html ), 'align' => $align, 'text_color' => $color ];
        return [
            'id'         => self::eid(),
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => array_filter( $s, function ( $v ) { return '' !== $v; } ),
            'elements'   => [],
        ];
    }

    private static function button( $label, $url, $align = 'center' ) {
        return [
            'id'         => self::eid(),
            'elType'     => 'widget',
            'widgetType' => 'button',
            'settings'   => [
                'text'  => $label,
                'link'  => [ 'url' => $url, 'is_external' => '', 'nofollow' => '' ],
                'align' => $align,
                'size'  => 'lg',
            ],
            'elements'   => [],
        ];
    }

    private static function icon_box( $icon, $title, $desc ) {
        return [
            'id'         => self::eid(),
            'elType'     => 'widget',
            'widgetType' => 'icon-box',
            'settings'   => [
                'selected_icon'    => [ 'value' => $icon, 'library' => 'fa-solid' ],
                'title_text'       => $title,
                'description_text' => $desc,
                'position'         => 'top',
                'title_size'       => 'h3',
            ],
            'elements'   => [],
        ];
    }

    private static function shortcode( $code ) {
        return [
            'id'         => self::eid(),
            'elType'     => 'widget',
            'widgetType' => 'shortcode',
            'settings'   => [ 'shortcode' => $code ],
            'elements'   => [],
        ];
    }

    private static function column( $widgets, $size = 100 ) {
        return [
            'id'       => self::eid(),
            'elType'   => 'column',
            'settings' => [ '_column_size' => $size, '_inline_size' => null ],
            'elements' => $widgets,
            'isInner'  => false,
        ];
    }

    private static function section( $columns, $settings = [] ) {
        return [
            'id'       => self::eid(),
            'elType'   => 'section',
            'settings' => $settings,
            'elements' => $columns,
            'isInner'  => false,
        ];
    }

    /** Raccourci : rembourrage vertical d'une section. */
    private static function pad( $top, $bottom ) {
        return [ 'padding' => [ 'unit' => 'px', 'top' => $top, 'bottom' => $bottom, 'left' => 20, 'right' => 20, 'isLinked' => false ] ];
    }

    /** Raccourci : section avec fond coloré + rembourrage. */
    private static function bg( $color, $top, $bottom ) {
        return array_merge(
            [ 'background_background' => 'classic', 'background_color' => $color ],
            self::pad( $top, $bottom )
        );
    }

    /* ═══════════════════════════════════════════════════════════════════════
     *  DÉFINITION DES MODÈLES (contenu en allemand)
     * ═══════════════════════════════════════════════════════════════════════ */

    /**
     * @return array Liste des modèles [ clé => [ title, slug, builder ] ].
     */
    private static function get_templates() {
        return [
            'home'    => [
                'title'   => __( 'Startseite', 'woomax' ),
                'slug'    => 'startseite',
                'builder' => [ __CLASS__, 'tpl_home' ],
            ],
            'about'   => [
                'title'   => __( 'Über uns', 'woomax' ),
                'slug'    => 'ueber-uns',
                'builder' => [ __CLASS__, 'tpl_about' ],
            ],
            'contact' => [
                'title'   => __( 'Kontakt', 'woomax' ),
                'slug'    => 'kontakt',
                'builder' => [ __CLASS__, 'tpl_contact' ],
            ],
        ];
    }

    /** Modèle : page d'accueil (allemand). */
    public static function tpl_home() {
        $shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
        $woo      = class_exists( 'WooCommerce' );
        $data     = [];

        // 1. HÉRO
        $data[] = self::section(
            [ self::column( [
                self::heading( 'Premium Brennholz – ofenfertig geliefert', 'xxl', 'h1', '#ffffff' ),
                self::text( 'Kammergetrocknetes Kaminholz aus nachhaltiger Forstwirtschaft. Beste Qualität, faire Preise und schnelle Lieferung bis vor Ihre Haustür.', 'center', '#f2ede7' ),
                self::button( 'Jetzt Brennholz kaufen', $shop_url ),
            ] ) ],
            self::bg( '#1e1b18', 120, 120 )
        );

        // 2. RÉASSURANCE
        $data[] = self::section(
            [
                self::column( [ self::icon_box( 'fas fa-shipping-fast', 'Schnelle Lieferung', 'Versand innerhalb von 24–48 Stunden, deutschlandweit.' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-tree', 'Nachhaltige Qualität', 'Holz aus regionaler, nachhaltiger Forstwirtschaft.' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-fire', 'Ofenfertig & trocken', 'Kammergetrocknet mit unter 20 % Restfeuchte.' ) ], 34 ),
            ],
            self::pad( 70, 40 )
        );

        // 3. PRODUITS POPULAIRES
        if ( $woo ) {
            $data[] = self::section(
                [ self::column( [
                    self::heading( 'Beliebte Produkte', 'xl' ),
                    self::text( 'Unsere meistgekauften Brennholz-Sorten – frisch verfügbar.' ),
                    self::shortcode( '[products limit="8" columns="4" orderby="popularity" class="woomax-el-products"]' ),
                ] ) ],
                self::pad( 40, 60 )
            );

            // 4. CATÉGORIES
            $data[] = self::section(
                [ self::column( [
                    self::heading( 'Unsere Holzsorten', 'xl' ),
                    self::shortcode( '[product_categories number="4" columns="4" orderby="count"]' ),
                ] ) ],
                self::bg( '#f7f5f2', 60, 60 )
            );
        }

        // 5. AVANTAGES (texte)
        $data[] = self::section(
            [
                self::column( [ self::icon_box( 'fas fa-shield-alt', 'Sichere Zahlung', 'Alle gängigen Zahlarten, SSL-verschlüsselt.' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-undo', 'Zufriedenheitsgarantie', '30 Tage Rückgaberecht ohne Wenn und Aber.' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-headset', 'Persönlicher Service', 'Wir beraten Sie gern – telefonisch und per E-Mail.' ) ], 34 ),
            ],
            self::pad( 20, 60 )
        );

        // 6. CTA
        $data[] = self::section(
            [ self::column( [
                self::heading( 'Bereit für gemütliche Stunden am Kamin?', 'xl', 'h2', '#ffffff' ),
                self::text( 'Bestellen Sie noch heute Ihr Premium-Brennholz und sparen Sie beim ersten Kauf.', 'center', '#f2ede7' ),
                self::button( 'Zum Shop', $shop_url ),
            ] ) ],
            self::bg( '#8a5a2b', 80, 80 )
        );

        return $data;
    }

    /** Modèle : « Über uns » (allemand). */
    public static function tpl_about() {
        $shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
        $data     = [];

        // Titre
        $data[] = self::section(
            [ self::column( [
                self::heading( 'Über uns', 'xxl', 'h1', '#ffffff' ),
                self::text( 'Ihr Fachhändler für hochwertiges Brennholz – seit über 20 Jahren.', 'center', '#f2ede7' ),
            ] ) ],
            self::bg( '#1e1b18', 90, 90 )
        );

        // Présentation
        $data[] = self::section(
            [ self::column( [
                self::heading( 'Holz ist unsere Leidenschaft', 'xl', 'h2', '', 'left' ),
                self::text(
                    'Als familiengeführter Betrieb liefern wir seit über zwei Jahrzehnten erstklassiges Brennholz an Privathaushalte und Gewerbe. Unser Holz stammt ausschließlich aus nachhaltiger, regionaler Forstwirtschaft. Jede Lieferung wird sorgfältig geprüft, damit Sie sich auf gleichbleibend hohe Qualität verlassen können.',
                    'left'
                ),
                self::text(
                    'Ob Eiche, Buche oder Mischholz – wir trocknen unser Kaminholz kammergetrocknet auf unter 20 % Restfeuchte. So brennt es sauber, effizient und mit angenehmer Wärme.',
                    'left'
                ),
            ] ) ],
            self::pad( 70, 40 )
        );

        // Valeurs
        $data[] = self::section(
            [
                self::column( [ self::icon_box( 'fas fa-leaf', 'Nachhaltigkeit', 'Für jeden gefällten Baum pflanzen wir nach.' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-award', 'Geprüfte Qualität', 'Kontrollierte Restfeuchte in jeder Charge.' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-handshake', 'Fairer Handel', 'Transparente Preise, ehrliche Beratung.' ) ], 34 ),
            ],
            self::bg( '#f7f5f2', 60, 60 )
        );

        // CTA
        $data[] = self::section(
            [ self::column( [
                self::heading( 'Überzeugen Sie sich selbst', 'xl', 'h2', '#ffffff' ),
                self::button( 'Unsere Produkte entdecken', $shop_url ),
            ] ) ],
            self::bg( '#8a5a2b', 70, 70 )
        );

        return $data;
    }

    /** Modèle : « Kontakt » (allemand). */
    public static function tpl_contact() {
        $data = [];

        // Titre
        $data[] = self::section(
            [ self::column( [
                self::heading( 'Kontakt', 'xxl', 'h1', '#ffffff' ),
                self::text( 'Wir sind für Sie da – nehmen Sie Kontakt mit uns auf.', 'center', '#f2ede7' ),
            ] ) ],
            self::bg( '#1e1b18', 90, 90 )
        );

        // Coordonnées (3 colonnes)
        $data[] = self::section(
            [
                self::column( [ self::icon_box( 'fas fa-map-marker-alt', 'Adresse', 'Musterstraße 12<br>12345 Musterstadt<br>Deutschland' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-phone', 'Telefon', '+49 (0) 123 456 789<br>Mo–Fr: 8:00–18:00 Uhr' ) ], 33 ),
                self::column( [ self::icon_box( 'fas fa-envelope', 'E-Mail', 'info@ihre-domain.de<br>Antwort innerhalb von 24 Std.' ) ], 34 ),
            ],
            self::pad( 70, 40 )
        );

        // Horaires
        $data[] = self::section(
            [ self::column( [
                self::heading( 'Öffnungszeiten', 'lg', 'h2' ),
                self::text(
                    'Montag – Freitag: 8:00 – 18:00 Uhr<br>Samstag: 9:00 – 13:00 Uhr<br>Sonntag & Feiertage: geschlossen'
                ),
                self::text(
                    'Hinweis: Fügen Sie hier mit dem Plugin « Contact Form 7 » ein Kontaktformular ein, indem Sie das Text-Widget durch ein Shortcode-Widget ersetzen.',
                    'center', '#888888'
                ),
            ] ) ],
            self::bg( '#f7f5f2', 60, 70 )
        );

        return $data;
    }
}

WooMax_Demo_Importer::init();
