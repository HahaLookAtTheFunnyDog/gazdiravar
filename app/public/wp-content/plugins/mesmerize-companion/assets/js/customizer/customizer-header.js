(function($) {
    window.CP_Customizer.addModule(function(CP_Customizer) {
        var control = wp.customize.panel('header_panel');
        var $headerLi = control.container.eq(0);
        // remove default events
        $headerLi.children('h3').andSelf().off();

        $headerLi.find('[data-name="change"]').andSelf().click(function(event) {
            event.preventDefault();
            event.stopPropagation();

            var sectionID = 'header_layout';
            if (CP_Customizer.isRightSidebarVisible(sectionID)) {
                CP_Customizer.hideRightSidebar();
            } else {
                CP_Customizer.openRightSidebar(sectionID);
            }
        });


        $headerLi.find('[data-name="edit"]').unbind('click').click(function(event) {
            event.preventDefault();
            event.stopPropagation();

            if (CP_Customizer.isRightSidebarVisible('header_background_chooser')) {
                CP_Customizer.hideRightSidebar();
            } else {
                CP_Customizer.openRightSidebar('header_background_chooser');
            }

            $(this).toggleClass('active');
        });

    });
})(jQuery);