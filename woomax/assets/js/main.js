/**
 * WooMax – JavaScript principal
 * @version 1.0.0
 */
(function($) {
    'use strict';

    const WooMax = {

        init() {
            this.header();
            this.mobileNav();
            this.searchOverlay();
            this.cartSidebar();
            this.backToTop();
            this.fadeInObserver();
            this.productTabs();
            this.addToCart();
            this.quickView();
            this.wishlist();
            this.qtyInput();
            this.productGallery();
            this.stickyATC();
            this.newsletter();
            this.productTabs();
        },

        // ── Header sticky & transparent ──────────────────────────────────────
        header() {
            const $header = $('#woomax-header');
            if (!$header.length) return;
            const stickyEnabled = $header.data('sticky') !== false;
            if (!stickyEnabled) return;
            let lastScroll = 0;
            $(window).on('scroll.woomax', function() {
                const scroll = $(this).scrollTop();
                if (scroll > 80) {
                    $header.addClass('scrolled');
                } else {
                    $header.removeClass('scrolled');
                }
                lastScroll = scroll;
            });
        },

        // ── Mobile navigation ─────────────────────────────────────────────────
        mobileNav() {
            const $nav     = $('#woomax-mobile-nav');
            const $toggle  = $('#woomax-mobile-toggle');
            const $close   = $('#woomax-mobile-close');
            const $overlay = $('#woomax-mobile-overlay');
            const open  = () => $nav.addClass('active');
            const close = () => $nav.removeClass('active');
            $toggle.on('click', open);
            $close.on('click', close);
            $overlay.on('click', close);
            $(document).on('keydown', e => { if (e.key === 'Escape') close(); });
        },

        // ── Search overlay ────────────────────────────────────────────────────
        searchOverlay() {
            const $overlay = $('#woomax-search-overlay');
            const $triggers= $('.woomax-search-trigger');
            const $close   = $('#woomax-search-close');
            const $input   = $overlay.find('.woomax-search-overlay__input');
            const open  = () => { $overlay.addClass('active'); setTimeout(() => $input.trigger('focus'), 300); };
            const close = () => $overlay.removeClass('active');
            $triggers.on('click', open);
            $close.on('click', close);
            $overlay.on('click', e => { if ($(e.target).is($overlay)) close(); });
            $(document).on('keydown', e => { if (e.key === 'Escape') close(); });
        },

        // ── Cart sidebar ──────────────────────────────────────────────────────
        cartSidebar() {
            const $sidebar  = $('#woomax-cart-sidebar');
            const $overlay  = $('#woomax-cart-overlay');
            const $triggers = $('.woomax-cart-trigger');
            const $close    = $('#woomax-cart-close');
            const open  = () => { $sidebar.addClass('open'); $overlay.addClass('active'); $('body').css('overflow','hidden'); };
            const close = () => { $sidebar.removeClass('open'); $overlay.removeClass('active'); $('body').css('overflow',''); };
            $triggers.on('click', open);
            $close.on('click', close);
            $overlay.on('click', close);
            $(document).on('keydown', e => { if (e.key === 'Escape') close(); });
            // Update on add to cart
            $(document.body).on('wc_fragments_refreshed added_to_cart', function() {
                $.post(WooMaxData.ajaxUrl, { action: 'woomax_mini_cart', nonce: WooMaxData.nonce }, res => {
                    if (res.success) {
                        $('.woomax-cart-count').text(res.data.count || 0);
                        $('.woomax-cart-total').html(res.data.total || '');
                        $('.woomax-mini-cart-content').html(res.data.html || '');
                    }
                });
            });
        },

        // ── Back to top ───────────────────────────────────────────────────────
        backToTop() {
            const $btn = $('#woomax-back-to-top');
            if (!$btn.length) return;
            $(window).on('scroll.woomax-top', function() {
                if ($(this).scrollTop() > 400) $btn.addClass('visible');
                else $btn.removeClass('visible');
            });
            $btn.on('click', () => $('html, body').animate({ scrollTop: 0 }, 600));
        },

        // ── Intersection observer – fade in ───────────────────────────────────
        fadeInObserver() {
            if (!window.IntersectionObserver) {
                $('.woomax-fade-in').addClass('visible');
                return;
            }
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.woomax-fade-in').forEach(el => obs.observe(el));
        },

        // ── Product tabs (home) ───────────────────────────────────────────────
        productTabs() {
            $(document).on('click', '.woomax-tab-filter', function() {
                const $btn  = $(this);
                const type  = $btn.data('type');
                const $grid = $('#woomax-products-container');
                if (!$grid.length || $btn.hasClass('loading')) return;
                // Update button states
                $('.woomax-tab-filter').removeClass('woomax-btn--primary').addClass('woomax-btn--secondary');
                $btn.removeClass('woomax-btn--secondary').addClass('woomax-btn--primary loading');
                $grid.css('opacity', 0.5);
                $.post(WooMaxData.ajaxUrl, {
                    action: 'woomax_get_products',
                    nonce:  WooMaxData.nonce,
                    type:   type,
                    count:  $grid.data('count') || 8,
                    cols:   $grid.data('cols') || 4,
                }, res => {
                    $btn.removeClass('loading');
                    $grid.css('opacity', 1);
                    if (res.success) $grid.html(res.data.html);
                });
            });
        },

        // ── Add to cart (AJAX) ─────────────────────────────────────────────────
        addToCart() {
            $(document).on('click', '.woomax-atc-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const id   = $btn.data('product-id');
                if (!id || $btn.hasClass('loading')) return;
                const qty = $btn.closest('.woomax-product-card, .woomax-sticky-atc__inner').find('.woomax-qty').val() || 1;
                $btn.addClass('loading').text(WooMaxData.strings.addingToCart);
                $.post(WooMaxData.ajaxUrl, {
                    action:     'woocommerce_ajax_add_to_cart',
                    product_id: id,
                    quantity:   qty,
                    nonce:      WooMaxData.nonce,
                }, res => {
                    $btn.removeClass('loading').text(WooMaxData.strings.addedToCart);
                    $btn.prepend('<i class="fa-solid fa-check" style="margin-right:6px;"></i>');
                    setTimeout(() => {
                        $btn.html('<i class="fa-solid fa-bag-shopping" style="margin-right:8px;"></i>' + (window.WooMaxData.strings.addToCart || 'Ajouter au panier'));
                    }, 2000);
                    $(document.body).trigger('wc_fragment_refresh');
                    // Open cart sidebar
                    setTimeout(() => { $('#woomax-cart-sidebar').addClass('open'); $('#woomax-cart-overlay').addClass('active'); $('body').css('overflow','hidden'); }, 300);
                }).fail(() => $btn.removeClass('loading').text(WooMaxData.strings.error));
            });
        },

        // ── Quick view ────────────────────────────────────────────────────────
        quickView() {
            $(document).on('click', '.woomax-quickview-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (!id) return;
                const $modal = $('#woomax-quick-view');
                $modal.css('display','flex');
                $modal.find('.woomax-quick-view__inner').html('<div style="display:flex;justify-content:center;padding:40px;"><div class="woomax-spinner"></div></div>');
                $.post(WooMaxData.ajaxUrl, { action: 'woomax_quick_view', product_id: id, nonce: WooMaxData.nonce }, res => {
                    if (res.success) $modal.find('.woomax-quick-view__inner').html(res.data.html);
                });
            });
        },

        // ── Wishlist ──────────────────────────────────────────────────────────
        wishlist() {
            $(document).on('click', '.woomax-wishlist-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const id   = $btn.data('id');
                if (!id) return;
                $.post(WooMaxData.ajaxUrl, { action: 'woomax_wishlist', product_id: id, nonce: WooMaxData.nonce }, res => {
                    if (res.success) {
                        $btn.toggleClass('active', res.data.added);
                        $('.woomax-wishlist-count').text(res.data.count || 0);
                        WooMax.showNotice(res.data.added ? WooMaxData.strings.addToWishlist : '✕ Retiré de la liste de souhaits', 'success');
                    }
                });
            });
        },

        // ── Qty input ─────────────────────────────────────────────────────────
        qtyInput() {
            $(document).on('click', '.woomax-qty-minus, .woomax-qty-plus', function() {
                const $input = $(this).siblings('.woomax-qty, input[name="quantity"]');
                let val = parseInt($input.val()) || 1;
                const min = parseInt($input.attr('min')) || 1;
                const max = parseInt($input.attr('max')) || 9999;
                if ($(this).hasClass('woomax-qty-minus')) val = Math.max(min, val - 1);
                else val = Math.min(max, val + 1);
                $input.val(val).trigger('change');
            });
        },

        // ── Product gallery (single product) ──────────────────────────────────
        productGallery() {
            $(document).on('click', '.woomax-product-gallery__thumb', function() {
                const src = $(this).data('src') || $(this).find('img').attr('src');
                if (!src) return;
                $('.woomax-product-gallery__thumb').removeClass('active');
                $(this).addClass('active');
                const $main = $('.woomax-product-gallery__main img');
                $main.css('opacity', 0);
                setTimeout(() => { $main.attr('src', src).css('opacity', 1); }, 200);
            });
        },

        // ── Sticky add to cart (single product) ───────────────────────────────
        stickyATC() {
            const $sticky = $('.woomax-sticky-atc');
            if (!$sticky.length) return;
            const $atc = $('.woomax-single-product__add-to-cart');
            if (!$atc.length) return;
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    $sticky.toggleClass('visible', !e.isIntersecting);
                });
            }, { threshold: 0 });
            obs.observe($atc[0]);
        },

        // ── Newsletter form ───────────────────────────────────────────────────
        newsletter() {
            $('#woomax-newsletter-form, .woomax-newsletter-form').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const email = $form.find('input[name="email"]').val();
                const $msg  = $('#woomax-newsletter-msg, .woomax-newsletter-msg').first();
                const $btn  = $form.find('button[type="submit"]');
                if (!email) return;
                $btn.prop('disabled', true).text(WooMaxData.strings.loading);
                $.post(WooMaxData.ajaxUrl, { action: 'woomax_newsletter', email, nonce: WooMaxData.nonce }, res => {
                    $btn.prop('disabled', false).text($btn.data('original-text') || WooMaxData.strings.addingToCart);
                    if (res.success) {
                        $msg.text(res.data.message).css('display','block');
                        $form[0].reset();
                    } else {
                        $msg.text((res.data && res.data.message) || WooMaxData.strings.error).css('display','block');
                    }
                });
            });
        },

        // ── Notice / toast ────────────────────────────────────────────────────
        showNotice(msg, type = 'success') {
            const $notice = $(`<div class="woomax-notice woomax-notice--${type}" style="
                position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
                background:${type === 'success' ? '#333' : '#e74c3c'};color:#fff;
                padding:14px 28px;border-radius:6px;font-size:14px;font-weight:600;
                z-index:99999;box-shadow:0 4px 20px rgba(0,0,0,.2);
                animation:slideUp .3s ease forwards;white-space:nowrap;
            ">${msg}</div>`);
            $('body').append($notice);
            setTimeout(() => $notice.fadeOut(400, function() { $(this).remove(); }), 3000);
        },

        // ── Product tabs ──────────────────────────────────────────────────────
        tabs() {
            $(document).on('click', '.woomax-tab-btn', function() {
                const $btn     = $(this);
                const panelId  = $btn.data('panel');
                const $wrapper = $btn.closest('.woomax-product-tabs, .woomax-tabs');
                $wrapper.find('.woomax-tab-btn').removeClass('active');
                $wrapper.find('.woomax-tab-panel').removeClass('active');
                $btn.addClass('active');
                $wrapper.find('#' + panelId).addClass('active');
            });
        },
    };

    // ── Init on DOM ready ──────────────────────────────────────────────────────
    $(function() {
        WooMax.init();
        WooMax.tabs();
    });

    // ── Expose globally ────────────────────────────────────────────────────────
    window.WooMax = WooMax;

})(jQuery);

/* Slide up keyframe */
const s = document.createElement('style');
s.textContent = '@keyframes slideUp{from{transform:translateX(-50%) translateY(20px);opacity:0;}to{transform:translateX(-50%) translateY(0);opacity:1;}}';
document.head.appendChild(s);
