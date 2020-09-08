(function (root, CP_Customizer, $) {

    CP_Customizer.__customPopupControls = {};

    CP_Customizer.registerCustomPopupControls = function (tag, controls) {
        CP_Customizer.__customPopupControls[tag] = controls;
    };


    CP_Customizer.getCustomPopupFields = function (tag, $node) {
        var controls = CP_Customizer.hooks.applyFilters('filter_custom_popup_controls', CP_Customizer.utils.deepClone(CP_Customizer.__customPopupControls));
        var attrs = controls[tag] || [];
        var controls = [];
        for (var attrName in attrs) {
            if (attrs.hasOwnProperty(attrName)) {
                var control = CP_Customizer.utils.deepClone(attrs[attrName].control);


                if (_.isFunction(control.active)) {
                    if (!control.active($node)) {
                        continue;
                    }
                }

                if (control.attr) {
                    control.value = $node.attr(control.attr);
                }

                if (control.getValue) {
                    control.value = control.getValue(attrName, $node, control.default);
                }

                if (!control.value && control.default) {
                    control.value = control.default;
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

    CP_Customizer.openCustomPopupEditor = function ($node, tag, callback) {
        var popupContainer = $('#cp-container-editor');

        var fields = CP_Customizer.getCustomPopupFields(tag, $node);


        function setContent() {
            var values = {};
            for (var i = 0; i < fields.length; i++) {
                var field = fields[i],
                    value = field.val();

                values[field.id] = {
                    oldValue: field.value,
                    value: value
                };

                if (field.attr) {
                    $node.attr(field.attr, value);
                }

            }

            if (callback) {
                callback(values, $node);
            }

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


    function htmlEscape(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
})(window, CP_Customizer, jQuery);

