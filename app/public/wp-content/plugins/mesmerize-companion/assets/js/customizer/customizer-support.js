(function ($, CP_Customizer) {
    CP_Customizer.addModule(function () {
        var used = false;
        CP_Customizer.bind(CP_Customizer.events.PREVIEW_LOADED, function () {

            if (used) {
                return;
            }
            used = true;

            var $activatePageCard = $('.reiki-needed-container[data-type="activate"]');
            var $openPageCard = $('.reiki-needed-container[data-type="select"]');
            var $makeEditable = $('.reiki-needed-container[data-type="edit-this-page"]');
            var $makeProductEditable = $('.reiki-needed-container[data-type="edit-this-product"]');

            var data = CP_Customizer.preview.data();
            var toAppend;

            var canMaintainThis = CP_Customizer.preview.data('canEditInCustomizer') //CP_Customizer.options('isMultipage', false) && (data.pageID !== false);

            if (data.maintainable) {

            } else {
                if (canMaintainThis) {

                    if (CP_Customizer.preview.data('queryVars:post_type', 'page') === 'page') {
                        toAppend = $makeEditable.clone().show();
                    } else {
                        toAppend = $makeProductEditable.clone().show();
                    }

                    wp.customize.panel('page_content_panel').container.eq(0).find('.sections-list-reorder').empty().append(toAppend);

                } else {
                    wp.customize.panel('page_content_panel').container.eq(0).find('.accordion-section-title > .add-section-plus').remove();
                    if (!data.hasFrontPage) {
                        toAppend = $activatePageCard.clone().show();
                        wp.customize.panel('page_content_panel').container.eq(0).find('.sections-list-reorder').empty().append(toAppend);
                    } else {
                        if (!data.isFrontPage) {
                            toAppend = $openPageCard.clone().show();
                            wp.customize.panel('page_content_panel').container.eq(0).find('.sections-list-reorder').empty().append(toAppend);

                        }
                    }
                }
            }

        });
    });
})(jQuery, CP_Customizer);
