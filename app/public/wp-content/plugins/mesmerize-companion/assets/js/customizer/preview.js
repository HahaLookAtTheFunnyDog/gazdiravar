(function ($) {
    if (!NodeList.prototype.forEach) {
        NodeList.prototype.forEach = Array.prototype.forEach;
        HTMLCollection.prototype.forEach = Array.prototype.forEach; // Because of https://bugzilla.mozilla.org/show_bug.cgi?id=14869

    }

    if (!Array.from) {
        Array.from = function (object) {
            return [].slice.call(object);
        };
    }

    $(function () {

        // WebKit contentEditable focus bug workaround:
        if (/AppleWebKit\/([\d.]+)/.exec(navigator.userAgent)) {
            var editableFix = $('<input style="position:fixed;z-index-1;width:1px;height:1px;border:none;margin:0;padding:0;" tabIndex="-1">').appendTo('html');

            $('body').on('blur', "i.fa[contenteditable]", function () {
                editableFix[0].setSelectionRange(0, 0);
                editableFix.blur();
            });
        }
    });


})(jQuery);


jQuery.fn.addFixedOverlay = function (options) {

    if (!parent.CP_Customizer) {
        return;
    }

    var $ = jQuery;
    var root = parent;
    options = $.extend(true, {
        'types': [],
        'classes': [],
        'callback': function () {
        }
    }, options);

    this.each(function (index, el) {
        var $node = $(el);
        var ovItems = ($node.data('type') || "").trim().replace(/\s\s+/g, ' ').split(' ');
        var ov = $('<div class="node-overlay"><div class="overlay-top overlay-border"></div><div class="overlay-left overlay-border"></div><div class="overlay-right overlay-border"></div><div class="overlay-bottom overlay-border"></div></div>');
        var overlay = root.jQuery('#toolbar-template').html();
        var toolbar = $(overlay);
        var overlaysContainer = root.CP_Customizer.overlays.overlaysContainer();
        var optionsGroupOnHoverIN = function (event) {
            $(this).find('.options-group.cog').addClass('active');

            if (this.ownerDocument.defaultView.innerHeight - this.getClientRects()[0].top < 400) {
                $(this).find('.options-group.cog').addClass('reverse');
            } else {
                $(this).find('.options-group.cog').removeClass('reverse');
            }


        };
        var optionsGroupOnHoverOUT = function (event) {
            $(this).find('.options-group.cog').removeClass('active');
        };

        $.each(options.classes, function (index, val) {
            ov.addClass(val);
        });

        ovItems = ovItems.concat(options.types);
        ovItems = ovItems.filter(function (type) {
            return type.length;
        });

        if (ovItems.length === 0) {
            return;
        }
        root.CP_Customizer.overlays.assignNode(ov, $node);
        toolbar.find('.overlay-toolbar-element-type').html('Settings');
        toolbar.find('.edit-group').hide();
        ov.append(toolbar);
        $node.data('overlay', ov);
        $(ov).data('node', this);
        overlaysContainer.append(ov);
        $('.overlay-toolbar').hover(optionsGroupOnHoverIN, optionsGroupOnHoverOUT);

        // look for overlay cog items

        function addCogCallback(itemData) {
            if (itemData.toolbarTitle) {
                var title = itemData.toolbarTitle;
                if (typeof itemData.toolbarTitle === "function") {
                    title = itemData.toolbarTitle($node);
                }

                toolbar.find('.overlay-toolbar-element-type').html(title);
            }
            options.callback.apply($node, itemData);
        }

        for (var i = 0; i < ovItems.length; i++) {
            var itemType = ovItems[i];
            root.CP_Customizer.overlays.addOptionsToFixedOverlay(toolbar.find('.overlay-contextual-menu'), itemType, $node, addCogCallback);
        }

        if ($(this).width() < $(this).closest('body').width()) {
            ov.addClass('cog-outside');
        }
    });
    return this;
};


var cpHeaderOverlaySS = false;

jQuery(document).ready(function ($) {
    var style = jQuery('style[data-name="overlay-opacity"]')[0];
    cpHeaderOverlaySS = Array.from(document.styleSheets).filter(function (ss) {
        return ss.ownerNode === style;
    }).pop();
});

function setOverlayProperty(prop, value) {
    var rule = (cpHeaderOverlaySS.cssRules || cpHeaderOverlaySS.rules)[0];
    rule.style.setProperty(prop, value);
}

function liveUpdate(setting, callback) {
    var cb = function (value) {
        value.bind(callback);
    };
    var _setting = setting;
    var _prefixedSetting = parent.CP_Customizer.slugPrefix() + "_" + setting;
    wp.customize(_setting, cb);
    wp.customize(_prefixedSetting, cb);


}

var __addedSettings = [];

function liveUpdateAutoSetting(setting, callback) {

    if (!parent.CP_Customizer) {
        return;
    }

    if (__addedSettings.indexOf(setting) !== -1) {
        return;
    }

    __addedSettings.push(setting);

    var cb = function (value) {
        value.bind(callback);
    };

    var _setting = 'CP_AUTO_SETTING[' + setting + ']';
    var _prefixedSetting = 'CP_AUTO_SETTING[' + parent.CP_Customizer.slugPrefix() + "_" + setting + ']';
    wp.customize(_setting, cb);
    wp.customize(_prefixedSetting, cb);

    if (parent.wp.customize(_setting)) {
        callback(parent.wp.customize(_setting).get());
    } else {
        if (parent.wp.customize(_prefixedSetting)) {
            callback(parent.wp.customize(_prefixedSetting).get());
        }
    }
}

function bindLiveUpdates(root, $) {

    $('[data-theme-href]').each(function () {
        var option = $(this).attr('data-theme-href');
        var self = $(this);
        liveUpdateAutoSetting(option, function (to) {
            self.attr('href', to);
        });
    });

    $('[data-theme-src]').each(function () {
        var option = $(this).attr('data-theme-src');
        var self = $(this);
        liveUpdateAutoSetting(option, function (to) {
            self.attr('src', to);
        });
    });

    $('[data-theme-target]').each(function () {
        var option = $(this).attr('data-theme-target');
        var self = $(this);
        liveUpdateAutoSetting(option, function (to) {
            var classAttr = self.attr('class');
            var newClassAttr = classAttr.replace(/fa\-[a-z0-9\-]+/ig, "").replace(/\s[\s]+/ig, " ").trim() + " " + to;
            self.attr('class', newClassAttr);
        });
    });

    $('[data-theme-fa]').each(function () {
        var option = $(this).attr('data-theme');
        var self = $(this);
        liveUpdateAutoSetting(option, function (to) {
            self.html(to);
        });
    });
}

function doPreviewDecoration(root, $) {

    $(document).find('[data-theme]').find(root.CP_Customizer.CONTENT_ELEMENTS).each(function (index, el) {
        $(this).attr('data-content-editable', true);
        $(this).attr('data-content-code-editable', true);
        $(this).attr('contenteditable', true);
    });
}


jQuery(document).ready(function ($) {

    if (wp.customize && wp.customize.mutationObserver) {
        wp.customize.mutationObserver.disconnect();
    }
    // if page is not maintainable with companion do not decorate
    if (parent.CP_Customizer && !parent.CP_Customizer.preview.data().maintainable) {
        return;
    }

    // bindLiveUpdates(parent, jQuery);
    doPreviewDecoration(parent, $);
});
