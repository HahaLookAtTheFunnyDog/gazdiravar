(function (root, CP_Customizer, $) {

    CP_Customizer.one(CP_Customizer.events.PREVIEW_LOADED, function () {

        var predefinedSitesSection = CP_Customizer.wpApi.section('extendthemes_start_from_demo_site').container;
        predefinedSitesSection.find('*').andSelf().off();

        predefinedSitesSection.on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            CP_Customizer.featuresPopups.openPopup('demo_import');
        });

    });


    CP_Customizer.hooks.addAction('features_popup_displayed', function (popup, $tbWindow) {

        function disablePopup() {
            jQuery.post(ajaxurl, {
                value: '1',
                action: "companion_disable_popup",
                option: 'feature_popup_' + popup.id + '_disabled',
                companion_disable_popup_wpnonce: popup.nonce
            });
        }

        $tbWindow.find('.close-button').off('click.feature-popups').on('click.feature-popups', function (event) {
            disablePopup();
            CP_Customizer.closePopUps();
        });

        $tbWindow.find('.js-ocdi-gl-import-data').click(function (event) {
            event.preventDefault();
            event.stopPropagation();
            disablePopup();
            var href = this.href;
            setTimeout(function () {
                window.location = href;
            }, 100);
        });


        $tbWindow.off('click.feature-popups').on('click.feature-popups', '.js-ocdi-nav-link', function (event) {
            event.preventDefault();
            event.stopPropagation();
            // Remove 'active' class from the previous nav list items.
            $(this).parent().siblings().removeClass('active');

            // Add the 'active' class to this nav list item.
            $(this).parent().addClass('active');

            var category = $(this).attr('href').replace('#', '');

            // show/hide the right items, based on category selected
            var $container = $tbWindow.find('.js-ocdi-gl-item-container');
            $container.css('min-width', $container.outerHeight());

            if (category.trim() === 'all') {
                $container.find('[data-categories]').fadeIn(250);
            } else {
                var $items = $container.find('[data-categories]');
                $items.fadeOut(0, function () {
                    setTimeout(function () {
                        $items.each(function () {
                            var $item = $(this);
                            if ($item.attr('data-categories').split(',').indexOf(category) !== -1) {
                                $item.fadeIn(50);
                            }
                        });
                    }, 0);
                });

            }

        });

    });

})(window, CP_Customizer, jQuery);
