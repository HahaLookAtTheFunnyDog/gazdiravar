(function (root, CP_Customizer, $) {
    CP_Customizer.addModule(function (CP_Customizer) {
        var popups = CP_Customizer.options('featuresPopups', {}),
            startWithFeaturePopup = CP_Customizer.options('startWithFeaturePopup', null);
        // popup = popups[startWithFeaturePopup];

        CP_Customizer.featuresPopups = {
            openPopup: function (id) {

                var popup = popups[id];

                if (popup) {
                    popup.nonce = CP_Customizer.options('featurePopupsNonce', '');
                    var $container;
                    var containerID;
                    var popUpSelector = '[data-feature-popup-id="' + popup.id + '"]';
                    if (!$(popUpSelector).length) {
                        containerID = "cp_feauture_popups_" + Math.round(Math.random() * 10000);
                        $container = $("<div/>", {
                            id: containerID,
                            style: "display:none",
                            "data-popup-id": ""
                        });

                        $container.append(popup.content);
                        $container.attr('data-feature-popup-id', popup.id);
                        $('body').append($container);
                    } else {
                        $container = $(popUpSelector);
                        containerID = $container.attr('id');
                    }

                    var $tbWindow = CP_Customizer.popUp(popup.title || 'New Feature', containerID, popup.data || {
                        class: ""
                    });

                    CP_Customizer.hooks.doAction('features_popup_displayed', popup, $tbWindow);
                }
            }
        };


        if (startWithFeaturePopup) {
            CP_Customizer.one(CP_Customizer.events.PREVIEW_LOADED, function () {
                CP_Customizer.featuresPopups.openPopup(startWithFeaturePopup);
            });
        }

    });

})(window, CP_Customizer, jQuery);
