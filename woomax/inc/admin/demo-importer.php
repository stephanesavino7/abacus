<?php
/**
 * WooMax – Importateur de pages Elementor (page d'accueil)
 *
 * Permet, depuis le menu WooMax, d'importer en un clic une page d'accueil
 * pré-conçue et modifiable dans Elementor. Le modèle n'utilise que des widgets
 * du noyau d'Elementor (titre, texte, bouton, icon-box) et des shortcodes
 * WooCommerce ([products], [product_categories]) : aucune extension Pro requise.
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
        add_action( 'wp_ajax_woomax_import_home', [ __CLASS__, 'ajax_import_home' ] );
    }

    /**
     * Ajoute la sous-page « Modèles Elementor » au menu WooMax.
     */
    public static function register_menu() {
        add_submenu_page(
            'woomax',
            __( 'Modèles Elementor', 'woomax' ),
            __( 'Modèles Elementor', 'woomax' ),
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

    /**
     * Page d'administration : import du modèle d'accueil.
     */
    public static function render_page() {
        $elementor_active = function_exists( 'woomax_is_elementor_active' ) && woomax_is_elementor_active();
        $existing         = get_option( 'woomax_home_page_id' );
        $existing_link    = $existing ? get_edit_post_link( $existing ) : '';
        ?>
        <div class="wrap woomax-admin">
            <div class="woomax-admin__hero">
                <h1><?php esc_html_e( 'Modèles Elementor', 'woomax' ); ?></h1>
                <p><?php esc_html_e( 'Importez une page d\'accueil pré-conçue, entièrement modifiable dans Elementor.', 'woomax' ); ?></p>
            </div>

            <?php if ( ! $elementor_active ) : ?>
                <div class="notice notice-warning" style="margin:0 20px 20px 0;">
                    <p>
                        <?php esc_html_e( 'Elementor doit être installé et activé pour importer et modifier ce modèle.', 'woomax' ); ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=woomax-plugins' ) ); ?>"><?php esc_html_e( 'Installer Elementor', 'woomax' ); ?></a>
                    </p>
                </div>
            <?php endif; ?>

            <div class="woomax-plugin-grid" style="grid-template-columns:repeat(auto-fill,minmax(340px,1fr));">
                <div class="woomax-plugin-card" data-template="home">
                    <div class="woomax-plugin-card__head">
                        <span class="dashicons dashicons-admin-home"></span>
                        <div>
                            <h3><?php esc_html_e( 'Page d\'accueil WooMax', 'woomax' ); ?></h3>
                        </div>
                    </div>
                    <p class="woomax-plugin-card__desc">
                        <?php esc_html_e( 'Bannière héro, arguments de réassurance, produits populaires, catégories et appel à l\'action. Construite avec Elementor + shortcodes WooCommerce.', 'woomax' ); ?>
                    </p>
                    <div class="woomax-plugin-card__footer">
                        <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;">
                            <input type="checkbox" id="woomax-set-front" checked>
                            <?php esc_html_e( 'Définir comme page d\'accueil', 'woomax' ); ?>
                        </label>
                        <button class="button button-primary woomax-import-home" <?php disabled( ! $elementor_active ); ?>>
                            <span class="dashicons dashicons-download" style="margin-top:3px;"></span>
                            <?php esc_html_e( 'Importer', 'woomax' ); ?>
                        </button>
                    </div>
                    <div class="woomax-notice-inline"></div>
                    <?php if ( $existing_link ) : ?>
                        <p style="font-size:12px;margin:8px 0 0;">
                            <?php esc_html_e( 'Déjà importée.', 'woomax' ); ?>
                            <a href="<?php echo esc_url( $existing_link ); ?>"><?php esc_html_e( 'Modifier dans Elementor', 'woomax' ); ?></a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX : crée la page d'accueil et injecte les données Elementor.
     */
    public static function ajax_import_home() {
        check_ajax_referer( 'woomax-admin-nonce', 'nonce' );

        if ( ! current_user_can( 'edit_pages' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission refusée.', 'woomax' ) ] );
        }
        if ( ! ( function_exists( 'woomax_is_elementor_active' ) && woomax_is_elementor_active() ) ) {
            wp_send_json_error( [ 'message' => __( 'Elementor n\'est pas actif.', 'woomax' ) ] );
        }

        $set_front = ! empty( $_POST['set_front'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['set_front'] ) );

        // Crée (ou réutilise) la page d'accueil.
        $existing = (int) get_option( 'woomax_home_page_id' );
        $page_id  = $existing && get_post( $existing ) ? $existing : 0;

        $postarr = [
            'post_title'   => __( 'Accueil', 'woomax' ),
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
            wp_send_json_error( [ 'message' => __( 'Impossible de créer la page.', 'woomax' ) ] );
        }

        // Injecte les données Elementor.
        $data = self::get_home_template();
        update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
        update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
        update_post_meta( $page_id, '_wp_page_template', 'template-elementor-full.php' );
        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
        }
        // _elementor_data doit être une chaîne JSON échappée par des slashes.
        update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );

        update_option( 'woomax_home_page_id', $page_id );

        // Définit comme page d'accueil statique.
        if ( $set_front ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page_id );
        }

        // Régénère le CSS Elementor pour la nouvelle page.
        if ( class_exists( '\Elementor\Plugin' ) ) {
            try {
                \Elementor\Plugin::$instance->files_manager->clear_cache();
            } catch ( \Throwable $e ) {
                // Sans gravité : le CSS sera régénéré au premier affichage.
            }
        }

        wp_send_json_success( [
            'message'   => __( 'Page d\'accueil importée avec succès.', 'woomax' ),
            'edit_link' => get_edit_post_link( $page_id, 'raw' ),
            'view_link' => get_permalink( $page_id ),
        ] );
    }

    /**
     * Construit les données Elementor de la page d'accueil.
     *
     * N'utilise que des widgets du noyau Elementor + shortcodes WooCommerce,
     * afin de rester compatible sans Elementor Pro.
     *
     * @return array
     */
    private static function get_home_template() {
        $shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/boutique' );

        // Widget: titre
        $heading = function( $text, $size = 'xl', $color = '', $align = 'center' ) {
            $s = [
                'title'      => $text,
                'align'      => $align,
                'size'       => $size,
                'title_color'=> $color,
            ];
            return [
                'id'         => self::eid(),
                'elType'     => 'widget',
                'widgetType' => 'heading',
                'settings'   => array_filter( $s, function( $v ) { return '' !== $v; } ),
                'elements'   => [],
            ];
        };
        // Widget: texte
        $text = function( $html, $align = 'center', $color = '' ) {
            $s = [ 'editor' => wpautop( $html ), 'align' => $align, 'text_color' => $color ];
            return [
                'id'         => self::eid(),
                'elType'     => 'widget',
                'widgetType' => 'text-editor',
                'settings'   => array_filter( $s, function( $v ) { return '' !== $v; } ),
                'elements'   => [],
            ];
        };
        // Widget: bouton
        $button = function( $label, $url, $align = 'center' ) {
            return [
                'id'         => self::eid(),
                'elType'     => 'widget',
                'widgetType' => 'button',
                'settings'   => [
                    'text'      => $label,
                    'link'      => [ 'url' => $url, 'is_external' => '', 'nofollow' => '' ],
                    'align'     => $align,
                    'size'      => 'lg',
                ],
                'elements'   => [],
            ];
        };
        // Widget: icon-box
        $icon_box = function( $icon, $title, $desc ) {
            return [
                'id'         => self::eid(),
                'elType'     => 'widget',
                'widgetType' => 'icon-box',
                'settings'   => [
                    'selected_icon'    => [ 'value' => $icon, 'library' => 'fa-solid' ],
                    'title_text'       => $title,
                    'description_text' => $desc,
                    'position'         => 'top',
                ],
                'elements'   => [],
            ];
        };
        // Widget: shortcode
        $shortcode = function( $code ) {
            return [
                'id'         => self::eid(),
                'elType'     => 'widget',
                'widgetType' => 'shortcode',
                'settings'   => [ 'shortcode' => $code ],
                'elements'   => [],
            ];
        };
        // Colonne
        $column = function( $widgets, $size = 100 ) {
            return [
                'id'       => self::eid(),
                'elType'   => 'column',
                'settings' => [ '_column_size' => $size, '_inline_size' => null ],
                'elements' => $widgets,
                'isInner'  => false,
            ];
        };
        // Section
        $section = function( $columns, $settings = [] ) {
            return [
                'id'       => self::eid(),
                'elType'   => 'section',
                'settings' => $settings,
                'elements' => $columns,
                'isInner'  => false,
            ];
        };

        $data = [];

        // ── 1. HÉRO ─────────────────────────────────────────────────────────
        $data[] = $section(
            [ $column( [
                $heading( __( 'Des produits d\'exception, livrés chez vous', 'woomax' ), 'xxl', '#ffffff' ),
                $text( __( 'Découvrez notre sélection premium, pensée pour la qualité et la durabilité.', 'woomax' ), 'center', '#f5f5f5' ),
                $button( __( 'Découvrir la boutique', 'woomax' ), $shop_url ),
            ] ) ],
            [
                'background_background' => 'classic',
                'background_color'      => '#1e1b18',
                'padding'               => [ 'unit' => 'px', 'top' => 110, 'bottom' => 110, 'left' => 20, 'right' => 20, 'isLinked' => false ],
            ]
        );

        // ── 2. RÉASSURANCE (3 icon-box) ─────────────────────────────────────
        $data[] = $section(
            [
                $column( [ $icon_box( 'fas fa-truck-fast', __( 'Livraison rapide', 'woomax' ), __( 'Expédition sous 24-48h partout en France.', 'woomax' ) ) ], 33 ),
                $column( [ $icon_box( 'fas fa-lock', __( 'Paiement sécurisé', 'woomax' ), __( 'Vos transactions sont 100% protégées.', 'woomax' ) ) ], 33 ),
                $column( [ $icon_box( 'fas fa-rotate-left', __( 'Retours gratuits', 'woomax' ), __( 'Satisfait ou remboursé sous 30 jours.', 'woomax' ) ) ], 34 ),
            ],
            [ 'padding' => [ 'unit' => 'px', 'top' => 60, 'bottom' => 40, 'left' => 20, 'right' => 20, 'isLinked' => false ] ]
        );

        // ── 3. PRODUITS POPULAIRES ──────────────────────────────────────────
        $data[] = $section(
            [ $column( [
                $heading( __( 'Produits populaires', 'woomax' ), 'xl' ),
                $shortcode( '[products limit="8" columns="4" orderby="popularity" class="woomax-el-products"]' ),
            ] ) ],
            [ 'padding' => [ 'unit' => 'px', 'top' => 40, 'bottom' => 60, 'left' => 20, 'right' => 20, 'isLinked' => false ] ]
        );

        // ── 4. CATÉGORIES ───────────────────────────────────────────────────
        $data[] = $section(
            [ $column( [
                $heading( __( 'Explorez nos catégories', 'woomax' ), 'xl' ),
                $shortcode( '[product_categories number="4" columns="4" orderby="count"]' ),
            ] ) ],
            [
                'background_background' => 'classic',
                'background_color'      => '#f7f5f2',
                'padding'               => [ 'unit' => 'px', 'top' => 60, 'bottom' => 60, 'left' => 20, 'right' => 20, 'isLinked' => false ],
            ]
        );

        // ── 5. APPEL À L'ACTION ─────────────────────────────────────────────
        $data[] = $section(
            [ $column( [
                $heading( __( 'Prêt à passer commande ?', 'woomax' ), 'xl', '#ffffff' ),
                $text( __( 'Rejoignez des milliers de clients satisfaits dès aujourd\'hui.', 'woomax' ), 'center', '#f5f5f5' ),
                $button( __( 'Voir tous les produits', 'woomax' ), $shop_url ),
            ] ) ],
            [
                'background_background' => 'classic',
                'background_color'      => '#8a5a2b',
                'padding'               => [ 'unit' => 'px', 'top' => 80, 'bottom' => 80, 'left' => 20, 'right' => 20, 'isLinked' => false ],
            ]
        );

        return $data;
    }
}

WooMax_Demo_Importer::init();
