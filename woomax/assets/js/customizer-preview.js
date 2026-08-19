/**
 * WooMax – Customizer Live Preview
 * Met à jour les variables CSS et les styles en temps réel.
 */
(function($) {
    'use strict';

    // Helper: set CSS variable
    function setCSSVar(name, value) {
        document.documentElement.style.setProperty(name, value);
    }

    // Helper: inject/update inline style
    function setStyle(selector, prop, value) {
        $(selector).css(prop, value);
    }

    // ── Colors ────────────────────────────────────────────────────────────────
    wp.customize('woomax_primary_color', val => val.bind(v => setCSSVar('--woomax-primary', v)));
    wp.customize('woomax_secondary_color', val => val.bind(v => setCSSVar('--woomax-secondary', v)));
    wp.customize('woomax_accent_color', val => val.bind(v => setCSSVar('--woomax-accent', v)));
    wp.customize('woomax_text_color', val => val.bind(v => { setCSSVar('--woomax-text-color', v); }));
    wp.customize('woomax_text_muted_color', val => val.bind(v => setCSSVar('--woomax-text-muted', v)));
    wp.customize('woomax_bg_color', val => val.bind(v => setCSSVar('--woomax-bg-color', v)));
    wp.customize('woomax_bg_secondary_color', val => val.bind(v => setCSSVar('--woomax-bg-secondary', v)));
    wp.customize('woomax_bg_dark_color', val => val.bind(v => setCSSVar('--woomax-bg-dark', v)));
    wp.customize('woomax_border_color', val => val.bind(v => setCSSVar('--woomax-border-color', v)));

    // ── Header ────────────────────────────────────────────────────────────────
    wp.customize('woomax_header_bg', val => val.bind(v => { setCSSVar('--woomax-header-bg', v); setStyle('.woomax-header', 'background-color', v); }));
    wp.customize('woomax_header_text_color', val => val.bind(v => setCSSVar('--woomax-header-text', v)));
    wp.customize('woomax_header_height', val => val.bind(v => setCSSVar('--woomax-header-height', v + 'px')));

    // ── Footer ────────────────────────────────────────────────────────────────
    wp.customize('woomax_footer_bg', val => val.bind(v => { setCSSVar('--woomax-footer-bg', v); setStyle('.woomax-footer', 'background-color', v); }));
    wp.customize('woomax_footer_text_color', val => val.bind(v => setCSSVar('--woomax-footer-text', v)));
    wp.customize('woomax_footer_heading_color', val => val.bind(v => setCSSVar('--woomax-footer-heading', v)));

    // ── Typography ────────────────────────────────────────────────────────────
    wp.customize('woomax_heading_font', val => val.bind(v => {
        setCSSVar('--woomax-heading-font', '"' + v + '", serif');
        // Dynamically load the font
        const fontId = 'woomax-dyn-font-heading';
        $('#' + fontId).remove();
        $('<link>', {
            id: fontId,
            rel: 'stylesheet',
            href: 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(v) + ':wght@300;400;600;700;800&display=swap'
        }).appendTo('head');
    }));
    wp.customize('woomax_body_font', val => val.bind(v => {
        setCSSVar('--woomax-body-font', '"' + v + '", sans-serif');
        const fontId = 'woomax-dyn-font-body';
        $('#' + fontId).remove();
        $('<link>', {
            id: fontId,
            rel: 'stylesheet',
            href: 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(v) + ':wght@300;400;600;700&display=swap'
        }).appendTo('head');
    }));
    wp.customize('woomax_body_font_size', val => val.bind(v => setCSSVar('--woomax-body-font-size', v + 'px')));
    wp.customize('woomax_heading_weight', val => val.bind(v => setCSSVar('--woomax-heading-weight', v)));
    wp.customize('woomax_body_line_height', val => val.bind(v => setCSSVar('--woomax-body-line-height', v)));

    // ── Layout ────────────────────────────────────────────────────────────────
    wp.customize('woomax_container_width', val => val.bind(v => setCSSVar('--woomax-container-width', v + 'px')));
    wp.customize('woomax_border_radius', val => val.bind(v => setCSSVar('--woomax-border-radius', v + 'px')));
    wp.customize('woomax_section_padding', val => val.bind(v => setCSSVar('--woomax-section-padding', v + 'px')));
    wp.customize('woomax_btn_border_radius', val => val.bind(v => setCSSVar('--woomax-btn-radius', v + 'px')));

    // ── Custom CSS ─────────────────────────────────────────────────────────────
    wp.customize('woomax_custom_css', val => val.bind(v => {
        $('#woomax-custom-css-live').remove();
        $('<style>', { id: 'woomax-custom-css-live' }).text(v).appendTo('head');
    }));

    // ── Top bar ───────────────────────────────────────────────────────────────
    wp.customize('woomax_top_bar_bg', val => val.bind(v => setStyle('.woomax-top-bar', 'background-color', v)));
    wp.customize('woomax_top_bar_color', val => val.bind(v => setStyle('.woomax-top-bar', 'color', v)));

})(jQuery);
