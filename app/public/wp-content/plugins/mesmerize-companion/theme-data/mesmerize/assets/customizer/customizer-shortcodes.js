(function (root, CP_Customizer, $) {

    var countUpSelector = '[data-countup="true"]';

    var countupControls = {
        min: {
            control: {
                label: window.CP_Customizer.translateCompanionString('Start counter from'),
                type: 'text',
                attr: 'data-min',
                default: 0
            }
        },

        max: {
            control: {
                label: window.CP_Customizer.translateCompanionString('End counter to'),
                type: 'text',
                attr: 'data-max',
                default: 100
            }

        },

        stop: {
            control: {
                label: window.CP_Customizer.translateCompanionString('Stop circle at value'),
                type: 'text',
                attr: 'data-stop',
                active: function ($item) {
                    return $item.closest('.circle-counter').length > 0;
                },
                default: 50
            }

        },

        prefix: {
            control: {
                label: window.CP_Customizer.translateCompanionString('Prefix ( text in front of the number )'),
                type: 'text',
                attr: 'data-prefix',
                default: ""
            }

        },

        suffix: {
            control: {
                label: window.CP_Customizer.translateCompanionString('Suffix ( text after the number )'),
                type: 'text',
                attr: 'data-suffix',
                default: "%"
            }

        },

        duration: {
            control: {
                label: window.CP_Customizer.translateCompanionString('Counter duration ( in milliseconds )'),
                type: 'text',
                attr: 'data-duration',
                default: 2000
            }

        }


    };

    CP_Customizer.hooks.addFilter('filter_custom_popup_controls', function (controls) {
        var extendedControls = _.extend(_.clone(controls),
            {
                countup: countupControls
            }
        );
        return extendedControls;
    });

    CP_Customizer.preview.registerContainerDataHandler(countUpSelector, function ($item) {
        CP_Customizer.openCustomPopupEditor($item, 'countup', function (values, $item) {
            console.log(values, $item);
            CP_Customizer.preview.jQuery($item[0]).data().restartCountUp();
        });
    });

    CP_Customizer.hooks.addAction('clean_nodes', function ($nodes) {
        $nodes.find(countUpSelector).each(function () {
            this.innerHTML = "";
            this.removeAttribute('data-max-computed');
        });

        $nodes.find('.circle-counter svg.circle-bar').removeAttr('style');
    });


})(window, CP_Customizer, jQuery);


(function (root, CP_Customizer, $) {
    CP_Customizer.registerShortcodePopupControls(
        "mesmerize_contact_form",
        {
            "shortcode": {
                
                control: {
                    label: window.CP_Customizer.translateCompanionString("3rd party form shortcode"),
                    type: "text",
                    setParse: function (value) {
                        return value.trim().replace(/^\[+/, '').replace(/\]+$/, '');
                    },

                    getParse: function (value) {
                        var val = value.trim().replace(/^\[+/, '').replace(/\]+$/, '');
                        if (!val) return "";
                        return "[" + CP_Customizer.utils.htmlDecode(val) + "]";
                    }
                }
            }
        }
    );

    CP_Customizer.hooks.addAction('shortcode_edit_mesmerize_contact_form', CP_Customizer.editEscapedShortcodeAtts);

})(window, CP_Customizer, jQuery);
