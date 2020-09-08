(function (root, CP_Customizer, $) {

    CP_Customizer.__shortcodesPopupControls = {};

    CP_Customizer.registerShortcodePopupControls = function (tag, controls) {
        CP_Customizer.__shortcodesPopupControls[tag] = controls;
    };


    CP_Customizer.hooks.addAction('dynamic_columns_handle', function (cols, node) {
        if (CP_Customizer.isShortcodeContent(node)) {
            var shortcode = CP_Customizer.getNodeShortcode(node);
            var device = root.CP_Customizer.preview.currentDevice();
            var prop = "columns";

            if (device === "tablet") {
                prop = "columns_tablet";

            }

            if (device === "mobile") {
                prop = "columns_mobile";
            }

            shortcode.attrs = shortcode.attrs || {};
            shortcode.attrs[prop] = cols;
            CP_Customizer.updateNodeFromShortcodeObject(node, shortcode);
        }
    });
    CP_Customizer.hooks.addFilter('is_shortcode_editable', function (value, shortcode) {
        var controls = CP_Customizer.hooks.applyFilters('filter_shortcode_popup_controls', CP_Customizer.utils.deepClone(CP_Customizer.__shortcodesPopupControls));

        // console.log(controls, shortcode);
        value = value || _.has(controls, shortcode.tag);

        return value;
    });


    CP_Customizer.getShortcodePopupFields = function (shortcodeData) {
        var controls = CP_Customizer.hooks.applyFilters('filter_shortcode_popup_controls', CP_Customizer.utils.deepClone(CP_Customizer.__shortcodesPopupControls));
        var tag = _.isObject(shortcodeData) ? shortcodeData.tag : shortcodeData;
        var attrs = controls[tag] || [];
        var controls = [];
        for (var attrName in attrs) {
            if (attrs.hasOwnProperty(attrName)) {
                var control = CP_Customizer.utils.deepClone(attrs[attrName].control);

                if (shortcodeData.attrs && shortcodeData.attrs.hasOwnProperty(attrName)) {
                    control.value = shortcodeData.attrs[attrName];
                } else {
                    control.value = !_.isUndefined(control.default) ? control.default : "";
                }

                if (control.getValue) {
                    control.value = control.getValue(attrName, shortcodeData.tag);
                }

                control.name = attrName;
                control.id = attrName;
                if (control.getParse) {
                    control.value = control.getParse(control.value);
                }
                controls.push(control);
            }
        }
        return controls;
    };

    CP_Customizer.openShortcodePopupEditor = function (callback, $node, shortcode) {
        var popupContainer = $('#cp-container-editor');

        var fields = CP_Customizer.getShortcodePopupFields(shortcode);

        var fallback = CP_Customizer.shortcodesAttrs && CP_Customizer.shortcodesAttrs[shortcode];

        if (!fields.length || fallback) {
            return;
        }

        shortcode = {
            tag: _.isObject(shortcode) ? shortcode.tag : shortcode,
            attrs: _.isObject(shortcode) ? shortcode.attrs : {},
        };

        function setContent() {
            for (var i = 0; i < fields.length; i++) {
                var field = fields[i],
                    node = field.node;
                var value = field.val();

                if (field.setValue) {
                    field.setValue(field.id, value, shortcode.tag);
                } else {
                    shortcode.attrs[field.id] = field.setParse ? field.setParse(value) : value;
                }
            }

            callback(shortcode.attrs);
            CP_Customizer.closePopUps();
            CP_Customizer.updateState();
        }


        popupContainer.find('[id="cp-item-ok"]').off().click(setContent);
        popupContainer.find('[id="cp-item-cancel"]').off().click(function () {
            CP_Customizer.closePopUps();
        });

        popupContainer.find('#cp-items').empty();
        for (var i = 0; i < fields.length; i++) {
            var field = fields[i],
                type = field.type || 'text',
                content = (CP_Customizer.jsTPL[type] ? CP_Customizer.jsTPL[type](field) : '');

            var $fieldContent = $(content);

            $fieldContent.attr('data-field', field.name);


            field.$wrapper = $fieldContent;
            field.$panel = popupContainer.find('#cp-items');
            field.$node = $node;

            field.val = function () {
                var value = {};
                var field = this;
                var _values = this.$wrapper.find('[id^="' + field.id + '"]').filter('input,textarea,select').map(function (index, elem) {
                    return {
                        key: $(this).attr('id').replace(field.id + "__", ''),
                        value: $(this).is('[type=checkbox]') ? this.checked : $(this).val()
                    };
                }).toArray();

                _(_values).each(function (v) {
                    value[v.key] = v.value;
                });

                if (_values.length === 1 && value.hasOwnProperty(field.id)) {
                    value = value[field.id];
                }

                return value;
            };

            if (field.ready && _.isFunction(field.ready)) {
                $fieldContent.data('field', field);
                $fieldContent.bind('shortcode-popup-ready', function () {
                    var $fieldContent = $(this);
                    var field = $fieldContent.data('field');
                    field.ready($fieldContent, $fieldContent.closest('#cp-items'));
                });


            }

            popupContainer.find('#cp-items').append($fieldContent);

        }

        popupContainer.find('#cp-items').children().trigger('shortcode-popup-ready');
        CP_Customizer.popUp(window.CP_Customizer.translateCompanionString('Manage Options'), "cp-container-editor", {
            width: "600",
            class: "data-edit-popup"
        });
    };


    CP_Customizer.editEscapedShortcodeAtts = function ($node, shortcode) {
        CP_Customizer.openShortcodePopupEditor(function (attrs) {
            shortcode.attrs = attrs;

            var shortcodeText = '[' + shortcode.tag;
            var attrs = [];
            for (var attr in shortcode.attrs) {
                if ((shortcode.attrs[attr] + "").trim() != "") {

                    attrs.push(attr + '="' + htmlEscape(htmlEscape(shortcode.attrs[attr])) + '"');
                }
            }

            if (attrs.length) {
                shortcodeText += ' ' + attrs.join(' ');
            }

            shortcodeText += ']';

            CP_Customizer.updateNodeShortcode($node, shortcodeText);

        }, $node, shortcode);
    };

    function htmlEscape(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
})(window, CP_Customizer, jQuery);

