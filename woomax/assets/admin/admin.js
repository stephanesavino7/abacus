/**
 * WooMax — Administration (installateur de plugins)
 *
 * Gère l'installation / activation des plugins via AJAX sécurisé.
 */
( function ( $ ) {
    'use strict';

    var cfg  = window.WooMaxAdmin || {};
    var i18n = cfg.i18n || {};

    /**
     * Envoie une requête AJAX pour installer ou activer un plugin.
     *
     * @param {string}   slug   Slug du plugin.
     * @param {string}   action install | activate.
     * @param {function} done   Callback( success, data ).
     */
    function request( slug, action, done ) {
        var ajaxAction = 'install' === action ? 'woomax_install_plugin' : 'woomax_activate_plugin';
        $.post( cfg.ajaxUrl, {
            action: ajaxAction,
            nonce:  cfg.nonce,
            slug:   slug
        } )
        .done( function ( response ) {
            done( !!( response && response.success ), response && response.data ? response.data : {} );
        } )
        .fail( function () {
            done( false, { message: i18n.error || 'Erreur' } );
        } );
    }

    /**
     * Met à jour l'affichage d'une carte plugin selon son statut.
     */
    function renderCard( $card, status ) {
        var $status = $card.find( '.woomax-plugin-status' );
        var $footer = $card.find( '.woomax-plugin-card__footer' );

        $status
            .removeClass( 'woomax-plugin-status--active woomax-plugin-status--inactive woomax-plugin-status--not_installed' )
            .addClass( 'woomax-plugin-status--' + status );

        if ( 'active' === status ) {
            $status.text( '✓ ' + ( i18n.active || 'Actif' ) );
            $footer.find( '.woomax-plugin-action' ).remove();
            if ( ! $footer.find( 'button[disabled]' ).length ) {
                $footer.append( '<button class="button" disabled>' + ( i18n.active || 'Actif' ) + '</button>' );
            }
        } else if ( 'inactive' === status ) {
            $status.text( i18n.installed || 'Installé' );
            $footer.find( '.woomax-plugin-action' )
                .attr( 'data-action', 'activate' )
                .removeClass( 'is-busy' )
                .prop( 'disabled', false )
                .text( i18n.activate || 'Activer' );
        }
    }

    /**
     * Traite le clic sur un bouton d'action (install / activate) d'une carte.
     */
    function handleAction( $btn, callback ) {
        var $card  = $btn.closest( '.woomax-plugin-card' );
        var slug   = $card.data( 'slug' );
        var action = $btn.data( 'action' );

        $btn.addClass( 'is-busy' ).prop( 'disabled', true )
            .text( 'install' === action ? ( i18n.installing || '…' ) : ( i18n.activating || '…' ) );

        request( slug, action, function ( success, data ) {
            if ( success ) {
                renderCard( $card, data.status || 'active' );
            } else {
                $btn.removeClass( 'is-busy' ).prop( 'disabled', false )
                    .text( 'install' === action ? ( i18n.install || 'Installer' ) : ( i18n.activate || 'Activer' ) );
                window.alert( ( data && data.message ) ? data.message : ( i18n.error || 'Erreur' ) );
            }
            if ( 'function' === typeof callback ) {
                callback( success );
            }
        } );
    }

    $( function () {
        // Bouton individuel
        $( document ).on( 'click', '.woomax-plugin-action', function ( e ) {
            e.preventDefault();
            handleAction( $( this ) );
        } );

        // Bouton « Installer tous les plugins requis »
        $( document ).on( 'click', '.woomax-install-all', function ( e ) {
            e.preventDefault();
            var $all   = $( this );
            var scope  = $all.data( 'scope' ) || 'required';
            var $cards = $( '.woomax-plugin-card' );

            if ( 'required' === scope ) {
                $cards = $cards.filter( '[data-required="1"]' );
            }

            var $buttons = $cards.find( '.woomax-plugin-action' );
            if ( ! $buttons.length ) {
                return;
            }

            $all.addClass( 'is-busy' ).prop( 'disabled', true );

            var index = 0;
            function next() {
                if ( index >= $buttons.length ) {
                    $all.removeClass( 'is-busy' ).prop( 'disabled', false )
                        .text( i18n.done || 'Terminé !' );
                    return;
                }
                var $btn = $( $buttons[ index ] );
                index++;
                // Le bouton peut avoir disparu (déjà traité) : on passe au suivant.
                if ( ! $btn.length || ! $btn.is( ':visible' ) ) {
                    next();
                    return;
                }
                handleAction( $btn, function () {
                    next();
                } );
            }
            next();
        } );
    } );

} )( jQuery );
