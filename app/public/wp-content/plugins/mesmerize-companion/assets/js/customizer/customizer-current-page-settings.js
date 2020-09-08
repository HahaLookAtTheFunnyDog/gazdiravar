(function (root, CP_Customizer, $) {

    var containerTemplate = $('' +
        '<li id="cp-current-page-settings-popup" class="customizer-right-section">' +
        '    <span data-close-right-sidebar="true" title="' + window.CP_Customizer.translateCompanionString('Close Panel') + '" class="close-panel"></span>' +
        '    <ul class="current-page-settings-container section-settings-container accordion-section-content no-border"></ul>' +
        ' </li>');

    CP_Customizer.addModule(function (CP_Customizer) {

        var control = wp.customize.panel('page_content_panel');
        control.container.find('.sections-list-reorder').append(containerTemplate);

        var $container = $("#cp-current-page-settings-popup .current-page-settings-container");

        CP_Customizer.panels.pageContentPanel = CP_Customizer.panels.instantiate({
            init: function (data) {
                if (this._initialized) {
                    return;
                }

                this._initialized = true;

                var self = this;

                // sort asc by priority
                var areas = Object.getOwnPropertyNames(this._areas).sort(function (a, b) {
                    return self._areas[a].priority - self._areas[b].priority;
                });

                areas = CP_Customizer.hooks.applyFilters('page_settings_sidebar_sort_areas', areas);

                areas.forEach(function (area) {
                    if (self._areas[area] && self._areas[area].canInclude()) {
                        CP_Customizer.hooks.doAction('before_page_settings_sidebar_area_' + name, $container);

                        self._processExtendAreaQueue(self._areas[area]);
                        self._areas[area].initAreaTitle($container);
                        self._areas[area].init($container);

                        CP_Customizer.hooks.doAction('after_page_settings_sidebar_area_' + name, $container);
                    }
                });

                this.update(data);
            },
            update: function (data) {
                for (var area in this._areas) {

                    if (this._areas[area].canInclude()) {

                        CP_Customizer.hooks.doAction('update_before_page_settings_sidebar_area_' + name, data);
                        this._areas[area].update(data);
                        CP_Customizer.hooks.doAction('update_after_page_settings_sidebar_area_' + name, data);

                    }
                }
            }
        });

        CP_Customizer.hooks.addAction('right_sidebar_opened', function (sidebarId) {
            if (sidebarId !== 'cp-current-page-settings') {
                return;
            }

            CP_Customizer.panels.pageContentPanel.init({});
            CP_Customizer.panels.pageContentPanel.update({});
            CP_Customizer.hooks.doAction('section_sidebar_opened', {});
        });
    });

})(window, CP_Customizer, jQuery);
