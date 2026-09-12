<?php
/**
 * WooMax – Panneau d'administration & installateur de plugins
 *
 * Crée un menu dédié « WooMax » dans l'administration WordPress avec :
 *  - Un tableau de bord de bienvenue
 *  - Un installateur en un clic des plugins requis / recommandés
 *    (WooCommerce, Elementor, extensions Elementor pour WooCommerce…)
 *
 * L'installation utilise les classes natives de WordPress
 * (Plugin_Upgrader + plugins_api) via AJAX sécurisé par nonce et capacités.
 *
 * @package WooMax
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class WooMax_Admin {

    /**
     * Liste des plugins gérés par l'installateur.
     * Tous proviennent du dépôt officiel WordPress.org (installables par slug).
     */
    public static function get_plugins() {
        return [
            // ── Requis ──────────────────────────────────────────────
            'woocommerce' => [
                'name'     => 'WooCommerce',
                'slug'     => 'woocommerce',
                'file'     => 'woocommerce/woocommerce.php',
                'required' => true,
                'category' => 'core',
                'desc'     => __( 'La plateforme e-commerce indispensable au fonctionnement de la boutique WooMax.', 'woomax' ),
                'icon'     => 'dashicons-cart',
            ],
            'elementor' => [
                'name'     => 'Elementor',
                'slug'     => 'elementor',
                'file'     => 'elementor/elementor.php',
                'required' => true,
                'category' => 'core',
                'desc'     => __( 'Le constructeur de pages en glisser-déposer. WooMax est pleinement compatible.', 'woomax' ),
                'icon'     => 'dashicons-layout',
            ],

            // ── Extensions Elementor pour WooCommerce (recommandées) ──
            'woolentor-addons' => [
                'name'     => 'WooLentor – WooCommerce Elementor Addons',
                'slug'     => 'woolentor-addons',
                'file'     => 'woolentor-addons/woolentor_addons_elementor.php',
                'required' => false,
                'category' => 'woo-elementor',
                'desc'     => __( 'Plus de 100 widgets Elementor dédiés à WooCommerce : grille produits, panier, checkout, etc.', 'woomax' ),
                'icon'     => 'dashicons-screenoptions',
            ],
            'essential-addons-for-elementor-lite' => [
                'name'     => 'Essential Addons for Elementor',
                'slug'     => 'essential-addons-for-elementor-lite',
                'file'     => 'essential-addons-for-elementor-lite/essential_adons_elementor.php',
                'required' => false,
                'category' => 'woo-elementor',
                'desc'     => __( 'La bibliothèque de widgets Elementor la plus populaire, avec de nombreux éléments WooCommerce.', 'woomax' ),
                'icon'     => 'dashicons-screenoptions',
            ],
            'happy-elementor-addons' => [
                'name'     => 'Happy Elementor Addons',
                'slug'     => 'happy-elementor-addons',
                'file'     => 'happy-elementor-addons/happy-elementor-addons.php',
                'required' => false,
                'category' => 'woo-elementor',
                'desc'     => __( 'Widgets et fonctionnalités supplémentaires pour Elementor, incluant des modules boutique.', 'woomax' ),
                'icon'     => 'dashicons-smiley',
            ],

            // ── Utilitaires recommandés ──────────────────────────────
            'contact-form-7' => [
                'name'     => 'Contact Form 7',
                'slug'     => 'contact-form-7',
                'file'     => 'contact-form-7/wp-contact-form-7.php',
                'required' => false,
                'category' => 'utility',
                'desc'     => __( 'Gestion simple et flexible de formulaires de contact.', 'woomax' ),
                'icon'     => 'dashicons-email',
            ],
            'wordpress-seo' => [
                'name'     => 'Yoast SEO',
                'slug'     => 'wordpress-seo',
                'file'     => 'wordpress-seo/wp-seo.php',
                'required' => false,
                'category' => 'utility',
                'desc'     => __( 'Optimisez le référencement naturel de votre boutique.', 'woomax' ),
                'icon'     => 'dashicons-chart-line',
            ],
        ];
    }

    /**
     * Statut d'un plugin : not_installed | inactive | active.
     */
    public static function get_plugin_status( $file ) {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $installed = get_plugins();
        if ( ! isset( $installed[ $file ] ) ) {
            return 'not_installed';
        }
        return is_plugin_active( $file ) ? 'active' : 'inactive';
    }

    /**
     * Initialise les hooks.
     */
    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'wp_ajax_woomax_install_plugin', [ __CLASS__, 'ajax_install_plugin' ] );
        add_action( 'wp_ajax_woomax_activate_plugin', [ __CLASS__, 'ajax_activate_plugin' ] );
        add_action( 'admin_notices', [ __CLASS__, 'setup_notice' ] );
    }

    /**
     * Enregistre le menu WooMax et ses sous-pages.
     */
    public static function register_menu() {
        add_menu_page(
            __( 'WooMax', 'woomax' ),
            __( 'WooMax', 'woomax' ),
            'manage_options',
            'woomax',
            [ __CLASS__, 'render_dashboard' ],
            'dashicons-store',
            2
        );
        add_submenu_page(
            'woomax',
            __( 'Tableau de bord', 'woomax' ),
            __( 'Tableau de bord', 'woomax' ),
            'manage_options',
            'woomax',
            [ __CLASS__, 'render_dashboard' ]
        );
        add_submenu_page(
            'woomax',
            __( 'Plugins requis', 'woomax' ),
            __( 'Plugins requis', 'woomax' ),
            'install_plugins',
            'woomax-plugins',
            [ __CLASS__, 'render_plugins_page' ]
        );
        add_submenu_page(
            'woomax',
            __( 'Personnaliser', 'woomax' ),
            __( 'Personnaliser', 'woomax' ),
            'edit_theme_options',
            'customize.php',
            null
        );
    }

    /**
     * Charge le CSS/JS de l'admin uniquement sur nos pages.
     */
    public static function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'woomax' ) === false ) {
            return;
        }
        wp_enqueue_style(
            'woomax-admin',
            WOOMAX_URI . '/assets/admin/admin.css',
            [],
            WOOMAX_VERSION
        );
        wp_enqueue_script(
            'woomax-admin',
            WOOMAX_URI . '/assets/admin/admin.js',
            [ 'jquery' ],
            WOOMAX_VERSION,
            true
        );
        wp_localize_script( 'woomax-admin', 'WooMaxAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'woomax-admin-nonce' ),
            'i18n'    => [
                'installing' => __( 'Installation…', 'woomax' ),
                'activating' => __( 'Activation…', 'woomax' ),
                'installed'  => __( 'Installé', 'woomax' ),
                'active'     => __( 'Actif', 'woomax' ),
                'activate'   => __( 'Activer', 'woomax' ),
                'install'    => __( 'Installer', 'woomax' ),
                'error'      => __( 'Erreur — réessayez.', 'woomax' ),
                'done'       => __( 'Terminé !', 'woomax' ),
                'imported'      => __( 'Page d\'accueil importée.', 'woomax' ),
                'editElementor' => __( 'Modifier dans Elementor', 'woomax' ),
                'viewPage'      => __( 'Voir la page', 'woomax' ),
            ],
        ] );
    }

    /**
     * AJAX : installe un plugin depuis WordPress.org.
     */
    public static function ajax_install_plugin() {
        check_ajax_referer( 'woomax-admin-nonce', 'nonce' );

        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission refusée.', 'woomax' ) ] );
        }

        $slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
        $plugins = self::get_plugins();
        if ( ! $slug || ! isset( $plugins[ $slug ] ) ) {
            wp_send_json_error( [ 'message' => __( 'Plugin inconnu.', 'woomax' ) ] );
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

        // Déjà installé ? On active directement.
        $file = $plugins[ $slug ]['file'];
        if ( 'not_installed' !== self::get_plugin_status( $file ) ) {
            $activate = activate_plugin( $file );
            if ( is_wp_error( $activate ) ) {
                wp_send_json_error( [ 'message' => $activate->get_error_message() ] );
            }
            wp_send_json_success( [ 'status' => 'active', 'message' => __( 'Activé.', 'woomax' ) ] );
        }

        // Récupère les infos du plugin depuis l'API WordPress.org
        $api = plugins_api( 'plugin_information', [
            'slug'   => $slug,
            'fields' => [ 'sections' => false ],
        ] );

        if ( is_wp_error( $api ) ) {
            wp_send_json_error( [ 'message' => $api->get_error_message() ] );
        }

        $skin     = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $result   = $upgrader->install( $api->download_link );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( [ 'message' => $result->get_error_message() ] );
        }
        if ( is_wp_error( $skin->result ) ) {
            wp_send_json_error( [ 'message' => $skin->result->get_error_message() ] );
        }
        if ( ! $result ) {
            wp_send_json_error( [ 'message' => __( 'Échec de l\'installation.', 'woomax' ) ] );
        }

        // Activation immédiate après installation
        $activate = activate_plugin( $file );
        if ( is_wp_error( $activate ) ) {
            wp_send_json_success( [ 'status' => 'inactive', 'message' => __( 'Installé mais non activé.', 'woomax' ) ] );
        }

        wp_send_json_success( [ 'status' => 'active', 'message' => __( 'Installé et activé.', 'woomax' ) ] );
    }

    /**
     * AJAX : active un plugin déjà installé.
     */
    public static function ajax_activate_plugin() {
        check_ajax_referer( 'woomax-admin-nonce', 'nonce' );

        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission refusée.', 'woomax' ) ] );
        }

        $slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
        $plugins = self::get_plugins();
        if ( ! $slug || ! isset( $plugins[ $slug ] ) ) {
            wp_send_json_error( [ 'message' => __( 'Plugin inconnu.', 'woomax' ) ] );
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $activate = activate_plugin( $plugins[ $slug ]['file'] );
        if ( is_wp_error( $activate ) ) {
            wp_send_json_error( [ 'message' => $activate->get_error_message() ] );
        }
        wp_send_json_success( [ 'status' => 'active', 'message' => __( 'Activé.', 'woomax' ) ] );
    }

    /**
     * Notice d'administration invitant à installer les plugins requis.
     */
    public static function setup_notice() {
        if ( ! current_user_can( 'install_plugins' ) ) return;
        // Ne pas afficher sur nos propres pages
        $screen = get_current_screen();
        if ( $screen && strpos( $screen->id, 'woomax' ) !== false ) return;

        $missing = [];
        foreach ( self::get_plugins() as $slug => $p ) {
            if ( $p['required'] && 'active' !== self::get_plugin_status( $p['file'] ) ) {
                $missing[] = $p['name'];
            }
        }
        if ( empty( $missing ) ) return;

        $url = admin_url( 'admin.php?page=woomax-plugins' );
        echo '<div class="notice notice-warning is-dismissible"><p>';
        printf(
            /* translators: %1$s = liste des plugins, %2$s = URL */
            esc_html__( 'WooMax recommande d\'activer : %1$s. ', 'woomax' ),
            '<strong>' . esc_html( implode( ', ', $missing ) ) . '</strong>'
        );
        echo '<a href="' . esc_url( $url ) . '" class="button button-primary" style="margin-left:8px;">' . esc_html__( 'Installer les plugins', 'woomax' ) . '</a>';
        echo '</p></div>';
    }

    /**
     * Page : Tableau de bord.
     */
    public static function render_dashboard() {
        $required_ok = true;
        foreach ( self::get_plugins() as $p ) {
            if ( $p['required'] && 'active' !== self::get_plugin_status( $p['file'] ) ) {
                $required_ok = false;
                break;
            }
        }
        ?>
        <div class="wrap woomax-admin">
            <div class="woomax-admin__hero">
                <h1><?php esc_html_e( 'Bienvenue sur WooMax', 'woomax' ); ?></h1>
                <p><?php esc_html_e( 'Votre thème WooCommerce premium, compatible Elementor et entièrement personnalisable.', 'woomax' ); ?></p>
                <span class="woomax-admin__version">v<?php echo esc_html( WOOMAX_VERSION ); ?></span>
            </div>

            <div class="woomax-admin__cards">
                <div class="woomax-admin__card">
                    <span class="dashicons dashicons-admin-plugins"></span>
                    <h3><?php esc_html_e( 'Plugins requis', 'woomax' ); ?></h3>
                    <p><?php esc_html_e( 'Installez WooCommerce, Elementor et les extensions recommandées en un clic.', 'woomax' ); ?></p>
                    <?php if ( ! $required_ok ) : ?>
                        <span class="woomax-badge woomax-badge--warning"><?php esc_html_e( 'Action requise', 'woomax' ); ?></span>
                    <?php else : ?>
                        <span class="woomax-badge woomax-badge--ok"><?php esc_html_e( 'Tout est prêt', 'woomax' ); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=woomax-plugins' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Gérer les plugins', 'woomax' ); ?></a>
                </div>

                <div class="woomax-admin__card">
                    <span class="dashicons dashicons-admin-customizer"></span>
                    <h3><?php esc_html_e( 'Personnalisation', 'woomax' ); ?></h3>
                    <p><?php esc_html_e( 'Couleurs, polices, en-tête, boutique, page produit… tout se règle dans le Customizer.', 'woomax' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button"><?php esc_html_e( 'Ouvrir le Customizer', 'woomax' ); ?></a>
                </div>

                <div class="woomax-admin__card">
                    <span class="dashicons dashicons-layout"></span>
                    <h3><?php esc_html_e( 'Elementor', 'woomax' ); ?></h3>
                    <p>
                        <?php
                        if ( woomax_is_elementor_active() ) {
                            esc_html_e( 'Elementor est actif. Vous pouvez concevoir vos pages en glisser-déposer.', 'woomax' );
                        } else {
                            esc_html_e( 'Elementor n\'est pas encore actif. Installez-le pour débloquer le constructeur visuel.', 'woomax' );
                        }
                        ?>
                    </p>
                    <?php if ( woomax_is_elementor_active() ) : ?>
                        <span class="woomax-badge woomax-badge--ok"><?php esc_html_e( 'Actif', 'woomax' ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Page : Installateur de plugins.
     */
    public static function render_plugins_page() {
        $plugins = self::get_plugins();
        $groups  = [
            'core'          => [ 'icon' => 'dashicons-admin-tools',    'label' => __( 'Plugins requis', 'woomax' ) ],
            'woo-elementor' => [ 'icon' => 'dashicons-screenoptions',  'label' => __( 'Extensions Elementor pour WooCommerce', 'woomax' ) ],
            'utility'       => [ 'icon' => 'dashicons-superhero-alt',  'label' => __( 'Utilitaires recommandés', 'woomax' ) ],
        ];
        ?>
        <div class="wrap woomax-admin">
            <h1><?php esc_html_e( 'Plugins WooMax', 'woomax' ); ?></h1>
            <p class="woomax-admin__intro"><?php esc_html_e( 'Installez et activez en un clic les plugins nécessaires au bon fonctionnement du thème. Tous proviennent du dépôt officiel WordPress.org.', 'woomax' ); ?></p>

            <div class="woomax-admin__toolbar">
                <button class="button button-primary woomax-install-all" data-scope="required">
                    <?php esc_html_e( 'Installer tous les plugins requis', 'woomax' ); ?>
                </button>
            </div>

            <?php foreach ( $groups as $cat => $group ) : ?>
                <h2 class="woomax-admin__group-title"><span class="dashicons <?php echo esc_attr( $group['icon'] ); ?>"></span> <?php echo esc_html( $group['label'] ); ?></h2>
                <div class="woomax-plugin-grid">
                    <?php foreach ( $plugins as $slug => $p ) : ?>
                        <?php if ( $p['category'] !== $cat ) continue; ?>
                        <?php $status = self::get_plugin_status( $p['file'] ); ?>
                        <div class="woomax-plugin-card" data-slug="<?php echo esc_attr( $slug ); ?>" data-required="<?php echo $p['required'] ? '1' : '0'; ?>">
                            <div class="woomax-plugin-card__head">
                                <span class="dashicons <?php echo esc_attr( $p['icon'] ); ?>"></span>
                                <div>
                                    <h3><?php echo esc_html( $p['name'] ); ?>
                                        <?php if ( $p['required'] ) : ?>
                                            <span class="woomax-badge woomax-badge--req"><?php esc_html_e( 'Requis', 'woomax' ); ?></span>
                                        <?php endif; ?>
                                    </h3>
                                </div>
                            </div>
                            <p class="woomax-plugin-card__desc"><?php echo esc_html( $p['desc'] ); ?></p>
                            <div class="woomax-plugin-card__footer">
                                <span class="woomax-plugin-status woomax-plugin-status--<?php echo esc_attr( $status ); ?>">
                                    <?php
                                    if ( 'active' === $status ) {
                                        echo '<span class="dashicons dashicons-yes-alt"></span> ' . esc_html__( 'Actif', 'woomax' );
                                    } elseif ( 'inactive' === $status ) {
                                        echo '<span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Installé (inactif)', 'woomax' );
                                    } else {
                                        echo '<span class="dashicons dashicons-minus"></span> ' . esc_html__( 'Non installé', 'woomax' );
                                    }
                                    ?>
                                </span>
                                <?php if ( 'active' === $status ) : ?>
                                    <button class="button" disabled><?php esc_html_e( 'Actif', 'woomax' ); ?></button>
                                <?php elseif ( 'inactive' === $status ) : ?>
                                    <button class="button button-primary woomax-plugin-action" data-action="activate"><?php esc_html_e( 'Activer', 'woomax' ); ?></button>
                                <?php else : ?>
                                    <button class="button button-primary woomax-plugin-action" data-action="install"><?php esc_html_e( 'Installer', 'woomax' ); ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

WooMax_Admin::init();
