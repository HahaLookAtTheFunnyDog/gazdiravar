(function ($) {
    var contentSwap = {
        "contentswap-effect": {
            "effectType": "",
            "contentType": "overlay",
            "overflowEnabled": "false",
            "effectDelay": "800",
            "effectEasing": "Ease",
            "overlayColor": "490A3D",
            "innerColor": "ffffff",
            "openPage": "same",
            "name": "",
            "captionType": "490A3D",
            "operationType": "edit",
            "hasls": "true",
            "additionalWrapperClasses": "",
            "direction": "bottom",
            "useSameTemplate": "true"
        }
    };


    jQuery(document).ready(function () {

        var contentSwapTimeout = setTimeout(function () {
            if (window.initHoverFX) {
                initHoverFX(contentSwap);
            }
        }, 10);
        jQuery(window).resize(function (e) {
            clearTimeout(contentSwapTimeout);
            contentSwapTimeout = setTimeout(function () {
                if (window.initHoverFX) {
                    initHoverFX(contentSwap, null, e);
                }
            }, 150);

        });
    });


})(jQuery);
