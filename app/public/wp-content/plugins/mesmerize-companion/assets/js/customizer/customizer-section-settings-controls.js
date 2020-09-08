(function (root, CP_Customizer, $) {

    var sectionSettingsContainer = $('' +
        '<li id="cp-section-setting-popup" class="customizer-right-section">' +
        '    <span data-close-right-sidebar="true" title="' + root.CP_Customizer.translateCompanionString("Close Panel") + '" class="close-panel"></span>' +
        '    <ul class="section-settings-container accordion-section-content no-border"></ul>' +
        ' </li>');


    CP_Customizer.addModule(function (CP_Customizer) {

        var control = wp.customize.panel('page_content_panel');
        control.container.find('.sections-list-reorder').append(sectionSettingsContainer);

        CP_Customizer.hooks.addFilter('content_section_setting_float', function () {
            return false;
        });

        CP_Customizer.hooks.addFilter('content_section_setting', function () {
            return "cp-section-setting";
        });


        var controlInstances = {};


        CP_Customizer.createControl = function (options) {

            options = _.extend({
                id: '',
                type: '',
                container: $('<div />'),
                params: {},
                value: '',
                updater: function (value) {
                    if (control.container.find('[data-cp-link]').is('[type=checkbox]')) {
                        control.container.find('[data-cp-link]').prop('checked', value);
                        return;
                    }
                    control.container.find('[data-cp-link]').val(value).trigger('change');
                }
            }, options);


            options.params.type = options.params.type || options.type;

            var settingID = options.id || _.uniqueId('control-setting-');
            var setting = new CP_Customizer.wpApi.Setting(settingID, options.value, {});


            setting.previewer = CP_Customizer.wpApi.previewer;

            setting.transport = 'postMessage';

            options.params.settings = [setting];
            options.params.value = options.value;
            options.params.link = ' data-cp-link';

            var controllerClass = CP_Customizer.wpApi.controlConstructor[options.type] || CP_Customizer.wpApi.Control;
            var control = new controllerClass(
                settingID + '-control',
                {
                    containerType: options.type,
                    params: options.params
                }
            );

            var container = $(options.container);

            control.container = container;
            control.setting = control.setting || setting;

            if (_.isFunction(options.renderContent)) {
                control.renderContent = options.renderContent;
            }

            control.section = function () {
                return 'page_content';
            };

            var updaterCallback = options.updater;

            var oldSet = setting.set;

            setting.bind(function (value) {
                control.params.value = value;
                updaterCallback.call(this, value);
            });

            setting.bind(function (value, oldValue) {
                if (_.isFunction(setting.onChange)) {
                    setting.onChange(value, oldValue);
                    CP_Customizer.markSave();
                }
            });

            setting.controlContainer = container;
            setting.renderControl = function () {
                control.renderContent();
                control.ready();

                return this;
            };

            setting.attachWithSetter = function (currentValue, onChange) {
                this.onChange = false;
                this._value = undefined;
                this.set(currentValue);

                if (options.onAttach) {
                    // ensure that the preview is loaded
                    if (CP_Customizer.preview.isLoaded()) {
                        options.onAttach.call(setting, currentValue);
                    }
                }

                var self = this;
                _.delay(function () {
                    self.onChange = onChange;
                }, 1);
            };

            setting.renderControl();
            setting.control = control;


            setting.hide = function () {
                this.control.container.hide();
            };

            setting.show = function () {
                this.control.container.show();
            };


            controlInstances[settingID] = setting;

            return setting;
        };

        CP_Customizer.createControl.getInstance = function (settingID) {
            var setting = controlInstances[settingID];

            return setting;
        }

        CP_Customizer.createControl.color = function (id, container, params) {

            var $container = $('<li class="customize-control customize-control-kirki customize-control-kirki-color" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                updater: function (value) {
                    var colorControl = this.control.container.find('input[data-cp-link]');
                    if (colorControl.data('spectrum.id') === undefined) {
                        colorControl.iris('color', value);
                    } else {
                        colorControl.spectrum("set", value);
                    }
                },
                type: 'kirki-color',
                container: $container,
                params: _.extend(
                    params,
                    {
                        choices: {
                            alpha: params.alpha !== undefined ? params.alpha : true,
                            instant: true
                        },

                        value: params.value || "#FFFFFF"
                    }
                ),
                value: params.value || "#FFFFFF"
            };

            return CP_Customizer.createControl(options);
        };


        CP_Customizer.createControl.palette = function (id, container, params) {

            var $container = $('<li class="customize-control customize-control-kirki customize-control-kirki-color" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                updater: function (value) {
                    var colorControl = this.control.container.find('input[data-cp-link]');
                    if (colorControl.data('spectrum.id') === undefined) {
                        colorControl.iris('color', value);
                    } else {
                        var cb = colorControl.spectrum("option", "move");
                        colorControl.spectrum("destroy");
                        colorControl.spectrum({
                            showPaletteOnly: true,
                            showPalette: true,
                            color: value,
                            instant: true,
                            palette: [params.palette],
                            move: cb,
                            change: cb
                        });
                        colorControl.spectrum("set", value);

                    }
                },
                type: 'kirki-color',
                container: $container,
                params: _.extend(
                    params,
                    {
                        choices: {
                            alpha: params.alpha || true,
                            instant: true
                        },
                        value: params.value || "#FFFFFF"
                    }
                ),
                value: params.value || "#FFFFFF"
            };

            var result = CP_Customizer.createControl(options);

            result.setPallete = function (palette) {
                var colorControl = this.control.container.find('input[data-cp-link]');
                colorControl.spectrum('option', 'palette', palette);
            };

            return result;
        };


        CP_Customizer.createControl.select = function (id, container, params) {
            var type = 'kirki-select',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                updater: function (value) {
                    if (this.controlContainer.find('[data-cp-link]').data().selectize) {
                        this.controlContainer.find('[data-cp-link]').data().selectize.setValue(value);
                    }
                },
                type: type,
                container: $container,
                params: _.extend(
                    params,
                    {
                        choices: params.choices || [],
                        value: params.value || "",
                        multiple: params.multiple || []
                    }
                ),
                value: params.value || ""
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.text = function (id, container, params) {
            var type = 'kirki-number',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />'),
                params = params || {};

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',

                type: type,
                onAttach: function (value) {
                    var thisInput = this.controlContainer.find('input');
                    thisInput.val(value);
                },
                container: $container,
                params: _.extend(
                    params,
                    {
                        value: params.value || ""
                    }
                ),
                value: params.value || ""
            };

            var setting = CP_Customizer.createControl(options);
            setting.control.container.find('input').spinner('destroy');

            return setting;
        };


        CP_Customizer.createControl.image = function (id, container, params) {
            var type = 'image',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                updater: function (value) {
                    if (value && "none" !== value && "/none" !== value.split('/none').pop()) {
                        this.control.params.attachment = {
                            id: Date.now(),
                            type: type,
                            sizes: {
                                full: {
                                    url: value
                                }
                            }
                        }
                    } else {
                        this.control.params.attachment = undefined;
                    }
                    this.control.renderContent();
                },
                params: _.extend(
                    params,
                    {

                        canUpload: true,
                        button_labels: {
                            remove: window.CP_Customizer.translateCompanionString("Remove"),
                            change: window.CP_Customizer.translateCompanionString("Change"),
                            select: window.CP_Customizer.translateCompanionString("Select")
                        },
                        attachment: {
                            type: type,
                            sizes: {
                                full: {
                                    url: params.url
                                }
                            }
                        }
                    }
                ),
                value: params.value || ""
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.radioButtonset = function (id, container, params) {
            var type = 'kirki-radio-buttonset',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                updater: function (value) {
                },
                type: type,
                container: $container,
                params: _.extend(
                    params,
                    {
                        choices: params.choices || [],
                        value: params.value || "",
                        id: id || ''
                    }
                ),
                value: params.value || ""
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.croppedImage = function (id, container, params) {
            var type = 'cropped_image',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                updater: function (value) {
                    if (_.isString(value) && "none" !== value && "/none" !== value.split('/none').pop()) {
                        this.control.params.attachment = {
                            id: Date.now(),
                            type: 'image',
                            sizes: {
                                full: {
                                    url: value
                                }
                            },
                            url: value
                        }
                    } else {
                        if (!value) {
                            this.control.params.attachment = undefined;
                        }
                    }
                    this.control.renderContent();
                },
                flex_height: true,
                flex_width: true,
                params: _.extend(
                    params,
                    {
                        canUpload: true,
                        flex_height: 1,
                        flex_width: 1,
                        button_labels: {
                            remove: window.CP_Customizer.translateCompanionString("Remove"),
                            change: window.CP_Customizer.translateCompanionString("Change"),
                            select: window.CP_Customizer.translateCompanionString("Select"),
                            placeholder: "No file selected",
                        },
                        attachment: {
                            id: Date.now(),
                            type: 'image',
                            sizes: {
                                full: {
                                    url: params.value
                                }
                            },
                            description: params.value,
                            url: params.value
                        }
                    }
                ),
                value: params.value || "",
                renderContent: function () {
                    var control = this, template, standardTypes, templateId, sectionId;

                    standardTypes = [
                        'button',
                        'checkbox',
                        'date',
                        'datetime-local',
                        'email',
                        'month',
                        'number',
                        'password',
                        'radio',
                        'range',
                        'search',
                        'select',
                        'tel',
                        'time',
                        'text',
                        'textarea',
                        'week',
                        'url'
                    ];

                    templateId = control.templateSelector;

                    if (!this.params.attachment) {
                        var value = this.setting.get();
                        if (_.isString(value) && "none" !== value && "/none" !== value.split('/none').pop()) {
                            this.params.attachment = {
                                id: Date.now(),
                                type: 'image',
                                sizes: {
                                    full: {
                                        url: value
                                    }
                                },
                                url: value
                            }
                        }
                    }

                    // Use default content template when a standard HTML type is used, there isn't a more specific template existing, and the control container is empty.
                    if (templateId === 'customize-control-' + control.params.type + '-content' &&
                        _.contains(standardTypes, control.params.type) &&
                        !document.getElementById('tmpl-' + templateId) &&
                        0 === control.container.children().length) {
                        templateId = 'customize-control-default-content';
                    }

                    // Replace the container element's content with the control.
                    if (document.getElementById('tmpl-' + templateId)) {
                        template = wp.template(templateId);
                        if (template && control.container) {
                            control.container.html(template(control.params));
                        }
                    }

                    // Re-render notifications after content has been re-rendered.
                    control.notifications.container = control.getNotificationsContainerElement();
                    sectionId = control.section();
                }
            };

            var result = CP_Customizer.createControl(options);


            result.attachWithSetter = function (currentValue, onChange) {
                this.onChange = false;
                this._value = undefined;
                this.set(currentValue);

                if (options.onAttach) {
                    // ensure that the preview is loaded
                    if (CP_Customizer.preview.isLoaded()) {
                        options.onAttach.call(setting, currentValue);
                    }
                }

                var self = this;
                _.delay(function () {
                    self.onChange = function (newValue, oldValue) {
                        onChange.call(this, this.control.params.attachment.url, oldValue)
                    }
                }, 1);
            };
            return result;
        };

        CP_Customizer.createControl.video = function (id, container, params) {
            var type = 'media',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                updater: function (value) {
                    if (value) {
                        this.control.params.attachment = value;
                    } else {
                        this.control.params.value = '';
                    }
                    this.control.renderContent();
                },
                params: _.extend(
                    params,
                    {
                        canUpload: true,
                        button_labels: {
                            remove: "Remove",
                            change: "Change",
                            select: "Select",
                            placeholder: "No file selected",
                        },
                        attachment: {
                            id: params.value
                        }
                    }
                ),
                value: params.value || ''
            };

            var result = CP_Customizer.createControl(options);

            result.onSelect = function () {
            };

            result.attachWithSetter = function (currentValue, onChange) {
                this.onChange = false;
                this._value = undefined;

                this.set(currentValue);

                if (options.onAttach) {
                    if (CP_Customizer.preview.isLoaded()) {
                        options.onAttach.call(setting, currentValue);
                    }
                }

                var self = this;
                _.delay(function () {
                    self.onSelect = onChange;
                }, 1);
            };

            result.control.select = function () {
                // Get the attachment from the modal frame.
                var node,
                    attachment = result.control.frame.state().get('selection').first().toJSON(),
                    mejsSettings = window._wpmejsSettings || {};

                result.control.params.attachment = attachment;

                // Set the Customizer setting; the callback takes care of rendering.
                result.control.setting(attachment.id);
                result.onSelect({
                    id: attachment.id,
                    url: attachment.url,
                    icon: attachment.icon,
                    type: attachment.type
                })
                node = result.control.container.find('audio, video').get(0);

                // Initialize audio/video previews.
                if (node) {
                    result.control.player = new MediaElementPlayer(node, mejsSettings);
                } else {
                    result.control.cleanupPlayer();
                }
            };

            result.control.container.on('click', 'button.remove-button', function () {
                result.control.params.attachment = {};
                result.set('');
                result.onSelect({
                    id: ''
                });
                result.control.renderContent(); // Not bound to setting change when emptying.

            });

            return result;
        };

        CP_Customizer.createControl.gradient = function (id, container, params) {
            var type = 'web-gradients',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                updater: function (value) {
                    this.control.params.value = value;
                    this.control.renderContent();
                },
                container: $container,
                params: _.extend(
                    params,
                    {
                        value: params.value || "",
                        button_label: window.CP_Customizer.translateCompanionString("Choose Gradient")
                    }
                ),
                value: params.value || ""
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.gradientPro = function (id, container, params) {
            var type = 'gradient-control-pro',
                selector = 'customize-control-' + id + '-control',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" id="' + selector + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                updater: function (value) {
                    this.control.setValue(value, true);
                },
                container: $container,
                params: _.extend(
                    params,
                    {
                        value: params.value || "",
                        button_label: "Select Gradient",
                        id: id || '',
                        settings: {
                            default: id || ''
                        }
                    }
                ),
                value: params.value || "{\"angle\":40.23635830927382,\"colors\":[{\"color\":\"#ffffff\",\"position\":\"0%\"},{\"color\":\"#000000\",\"position\":\"100%\"}]}",
                selector: selector
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.typography = function (id, container, params) {
            var type = 'kirki-typography',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" id="customize-control-' + id + '-control" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                updater: function (value) {
                },
                container: $container,
                params: _.extend(
                    params,
                    {
                        kirkiConfig: "global",
                        l10n: kirki.l10n.global,
                        default: params.value || {},
                        id: id || '',
                    }
                ),
                value: params.value || ""
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.sectionSeparator = function (id, container, label) {
            var type = 'sectionseparator',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    label: label
                }
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.textarea = function (id, container, params) {
            var type = 'kirki-generic',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: _.extend(
                    params,
                    {
                        value: params.value || "",
                        choices: {
                            element: 'textarea',
                            rows: '5'
                        }
                    }
                ),
                value: params.value || ""
            };

            return CP_Customizer.createControl(options);
        };

        CP_Customizer.createControl.controlsGroup = function (id, container, label) {
            var type = 'sectionseparator',
                $container = $('<li class="customize-control customize-control-kirki customize-controls-container customize-control-' + type + '" ></li>');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    label: label || ""
                }
            };

            var result = CP_Customizer.createControl(options);


            return (function (result, $el, parent) {

                if (!label) {
                    result.control.container.find('.mesmerize-separator').remove()
                }


                result.parent = container;

                result.free = function () {
                    $el.remove();
                };

                result.attach = function () {
                    parent.append($el);
                };

                result.el = function () {
                    if (this.control.container.find('ul.holder').length === 0) {
                        this.control.container.append('<ul class="holder"></ul>');
                    }
                    return this.control.container.find('ul.holder');
                };

                return result;
            })(result, $container, container);
        };


        CP_Customizer.createControl.controlHolder = function (id, container) {
            var type = 'sectionsetting',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container
            };

            return CP_Customizer.createControl(options);
        };


        CP_Customizer.createControl.checkbox = function (id, container, label) {
            var type = 'kirki-checkbox',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    label: label
                }
            };

            return CP_Customizer.createControl(options);
        };


        CP_Customizer.createControl.generic = function (id, container, params) {
            var type = 'kirki-generic',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    label: params.label,
                    choices: {
                        element: 'input',
                        type: 'text'
                    }
                },
                value: params.value
            };

            return CP_Customizer.createControl(options);


            return result;
        };

        CP_Customizer.createControl.repeater = function (id, container, params) {
            var type = 'repeater',
                $container = $('<li id="customize-"' + id + '"-control" class="customize-control customize-control-kirki customize-control-' + type + '" />');

            var repeaterContent = '';
            repeaterContent += '<label>';
            repeaterContent += '<span class="customize-control-title">' + params.label + '</span>';
            repeaterContent += '<input type="hidden" ' + params + ' value="" />';
            repeaterContent += '</label>';
            repeaterContent += '<ul class="repeater-fields"></ul>';
            // repeaterContent += '<p class="limit">' + params.choices.limit + '</p>';
            repeaterContent += '<button class="button-secondary repeater-add">Add new</button>';
            repeaterContent += jQuery('.customize-control-repeater-content').first().prop('outerHTML');
            $container.html(repeaterContent);

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: _.extend(
                    params,
                    {
                        choices: params.choices || {},
                        kirkiConfig: "global",
                        l10n: kirki.l10n.global,
                        label: params.label,
                        default: CP_Customizer.utils.normalizeValue(params.value, true),
                    }
                ),
                value: CP_Customizer.utils.normalizeValue(params.value, true)//params.value //encodeURI(JSON.stringify(params.value))
            };

            var result = CP_Customizer.createControl(options);

            if (_.isFunction(params.getValue)) {
                result.control.getValue = params.getValue;
            }

            result.control.setValue = function (newValue, refresh, filtering) {


                normalizedValue = CP_Customizer.utils.normalizeValue(newValue, true);
                newValue = [];

                normalizedValue.forEach(function (item) {
                    if (item) {
                        newValue.push(item);
                    }
                });

                // We need to filter the values after the first load to remove data requrired for diplay but that we don't want to save in DB
                var filteredValue = newValue,
                    filter = [];

                if (filtering) {
                    jQuery.each(this.params.fields, function (index, value) {
                        if ('image' === value.type || 'cropped_image' === value.type || 'upload' === value.type) {
                            filter.push(index);
                        }
                    });
                    jQuery.each(newValue, function (index, value) {
                        jQuery.each(filter, function (ind, field) {
                            if ('undefined' !== typeof value[field] && 'undefined' !== typeof value[field].id) {
                                filteredValue[index][field] = value[field].id;
                            }
                        });
                    });

                }

                // make sure that it sets the change
                this.setting._value = CP_Customizer.utils.deepClone(this.setting._value, true);
                this.setting.set(CP_Customizer.utils.deepClone(filteredValue, true));
                //
                // if (refresh) {
                //     CP_Customizer.preview.refresh()
                // }
            };


            result.set = function (to) {
                var from = this._value;

                to = this._setter.apply(this, arguments);
                to = this.validate(to);

                // Bail if the sanitized value is null or unchanged.
                if (null === to || _.isEqual(JSON.stringify(from), JSON.stringify(to))) {
                    return this;
                }

                this._value = to;
                this._dirty = true;

                this.callbacks.fireWith(this, [to, from]);

                return this;
            }

            return result;
        };


        CP_Customizer.createControl.button = function (id, container, label, callback, buttonOptions) {
            var type = 'kirki-checkbox',
                $container = $('<li class="customize-control customize-control-kirki customize-control-ope-button" />');

            if (container) {
                $(container).append($container);
            }

            buttonOptions = _.extend(
                {
                    class: "button full-width"
                },
                buttonOptions
            );

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    label: label
                }
            };

            var result = CP_Customizer.createControl(options);

            result.control.container.empty();

            var $button = $('<button class="' + buttonOptions.class + '" />');
            $button.text(label);
            $button.off('click').on('click', function (event) {
                event.stopPropagation();
                event.preventDefault();
                callback.call(this, event);
            });

            result.control.container.append($button);

            return result;
        };

        CP_Customizer.createControl.buttonGroup = function (id, container, params, callback, buttonOptions) {
            var type = 'sidebar-button-group',
                $container = $('<li class="customize-control customize-control-kirki customize-control-ope-button" />');

            if (container) {
                $(container).append($container);
            }

            buttonOptions = _.extend(
                {
                    class: "button full-width"
                },
                buttonOptions
            );

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: _.extend(
                    params,
                    {
                        popup: id
                    }
                ),
            };

            var result = CP_Customizer.createControl(options);

            return result;
        };


        CP_Customizer.createControl.info = function (id, container, content) {
            var type = 'kirki-checkbox',
                $container = $('<li class="customize-control customize-control-kirki customize-control-ope-info" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    label: ""
                }
            };

            var result = CP_Customizer.createControl(options);

            result.control.container.empty();

            result.control.container.append($('<p/>').append(content));


            return result;
        };

        CP_Customizer.createControl.infoPRO = function (id, container, content) {

            if (CP_Customizer.IS_PRO) {
                return;
            }

            var type = 'kirki-checkbox',
                $container = $('<li class="customize-control customize-control-kirki customize-control-ope-info-pro" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    label: ""
                }
            };

            var result = CP_Customizer.createControl(options);

            result.control.container.empty();

            result.control.container.append($('<p/>').append(content));


            return result;
        };


        CP_Customizer.createControl.sortable = function (id, container, itemTemplate, label) {
            var type = 'sectionsetting',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + '" />');

            if (container) {
                $(container).append($container);
            }

            var options = {
                id: id || '',
                type: type,
                container: $container,
                params: {
                    itemTemplate: itemTemplate,
                    label: label
                }
            };

            var result = CP_Customizer.createControl(options);

            result.control.attachControls = function () {
            };
            result.control.free = function () {
                this.container.find('.setting-control-container').empty();
                try {
                    this.container.sortable('destroy');
                } catch (e) {

                }
            };

            result.control.onStop = function () {
            };

            result.control.setItems = function (items, afterCreation) {
                var control = this;
                var itemContainer = this.container.find('.setting-control-container');


                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    var html = control.params.itemTemplate(item);

                    if (afterCreation) {
                        html = $(html);
                        afterCreation(html, item);
                    }

                    itemContainer.append(html);
                }

                itemContainer.sortable({
                    axis: "y",
                    handle: ".handle",
                    stop: function (event, ui) {
                        result.control.onStop(event, ui);
                    }
                });

            };


            return result;
        }

        CP_Customizer.createControl.number = function (id, container, options) {

            options = options || {};

            options.choices = _.extend({
                min: 0,
                max: 100,
                step: 1
            }, options.choices || {});

            var type = 'kirki-number',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + ' cp-control" />');

            if (container) {
                $(container).append($container);
            }


            var controlOptions = {
                id: id || '',
                updater: function (value) {
                    var thisInput = this.controlContainer.find('input');
                    thisInput.val(value);
                    // thisInput.change();
                },
                type: type,
                container: $container,
                params: {
                    label: options.label,
                    default: options.default,
                    choices: options.choices
                },
                value: options.default
            };

            return CP_Customizer.createControl(controlOptions);
        }

        CP_Customizer.createControl.spacing = function (id, container, options) {
            if (!options.sides) {
                options.sides = ['top', 'bottom'];
            }

            var type = 'kirki-spacing',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + ' cp-control" />');

            if (container) {
                $(container).append($container);
            }

            var sides = {};
            var controls = {};

            for (var i = 0; i < options.sides.length; i++) {
                sides[options.sides[i]] = "0px";
                controls[options.sides[i]] = true;
            }

            var controlOptions = {
                id: id || '',
                updater: function (value) {

                    for (var key in value) {
                        var $input = this.controlContainer.find('.' + key + '.input-wrapper > input');
                        $input.val(value[key]);
                    }

                },
                type: type,
                container: $container,
                params: {
                    kirkiConfig: "global",
                    l10n: kirki.l10n.global,
                    label: options.label,
                    default: sides,
                    choices: {
                        controls: controls
                    }
                },
                value: sides
            };

            kirkiNotifications(id, type, 'global');

            return CP_Customizer.createControl(controlOptions);
        };

        CP_Customizer.createControl.dimension = function (id, container, options) {

            options = options || {};

            var type = 'kirki-dimension',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + ' cp-control" />');

            if (container) {
                $(container).append($container);
            }


            var controlOptions = {
                id: id || '',
                updater: function (value) {
                    var $input = this.controlContainer.find('.input-wrapper > input');
                    $input.val(value);

                },
                type: type,
                container: $container,
                params: {
                    kirkiConfig: "global",
                    l10n: kirki.l10n.global,
                    label: options.label,
                    default: options.default || "0px"
                },
                value: options.default || "0px"
            };

            kirkiNotifications(id, type, 'global');

            return CP_Customizer.createControl(controlOptions);
        }

        CP_Customizer.createControl.slider = function (id, container, options) {

            options = options || {};

            options.choices = _.extend({
                min: 0,
                max: 100,
                step: 1,
                default: 0
            }, options.choices || {});

            var type = 'kirki-slider',
                $container = $('<li class="customize-control customize-control-kirki customize-control-' + type + ' cp-control" />');

            if (container) {
                $(container).append($container);
            }


            var controlOptions = {
                id: id || '',
                updater: function (value) {
                    var thisInput = this.controlContainer.find('input');
                    thisInput.val(value);
                    // thisInput.change();
                    thisInput.siblings('.kirki_range_value').find('.value').text(value);

                },
                onAttach: function (value) {
                    var thisInput = this.controlContainer.find('input');
                    thisInput.attr('data-reset_value', value);
                    thisInput.data('reset_value', value);
                },
                type: type,
                container: $container,
                params: {
                    kirkiConfig: "global",
                    l10n: kirki.l10n.global,
                    label: options.label,
                    default: options.default,
                    choices: options.choices
                },
                value: options.default
            };

            kirkiNotifications(id, type, 'global');

            return CP_Customizer.createControl(controlOptions);
        }
    });

})(window, CP_Customizer, jQuery);


(function (root, CP_Customizer, $) {
    CP_Customizer.addModule(function (CP_Customizer) {
        var $sectionSettingsContainer = $("#cp-section-setting-popup .section-settings-container");

        CP_Customizer.hooks.doAction('section_panel_before_dimensions', $sectionSettingsContainer); // #####

        CP_Customizer.hooks.addAction('right_sidebar_opened', function (sidebarId, data) {
            if (sidebarId !== 'cp-section-setting') {
                return;
            }

            CP_Customizer.panels.sectionPanel.init(data);
            CP_Customizer.panels.sectionPanel.update(data);
            CP_Customizer.hooks.doAction('section_sidebar_opened', data);
        });


        var baseArea = {

            __controls: {},

            priority: 100,

            extend: function (options) {
                return _.extend(this, options);
            },

            init: function () {
            },
            update: function () {
            },


            partialUpdate: function () {
            },

            disable: function () {
                for (var item in this.__controls) {
                    this.__controls[item].hide();
                }
                this.enabled = false;
            },
            enable: function () {
                for (var item in this.__controls) {
                    this.__controls[item].show();
                }
                this.enabled = true;
            },


            _exclude: false,

            exclude: function () {
                this._exclude = true;
            },

            include: function () {
                this._exclude = false;
            },

            canInclude: function () {
                return !this._exclude;
            },

            getPrefixed: function (data) {
                return this.name + '-' + data;
            },

            areaTitle: false,


            initAreaTitle: function ($container) {
                if (this.areaTitle) {
                    var separator = CP_Customizer.createControl.sectionSeparator(
                        this.getPrefixed('area-title'),
                        $container,
                        this.areaTitle
                    );
                    this.addToControlsList(separator);
                }
            },

            hideAll: function () {
                for (var control in this.__controls) {
                    this.__controls[control].hide();
                }
            },

            showAll: function () {
                for (var control in this.__controls) {
                    this.__controls[control].show();
                }
            },

            addToControlsList: function (control) {
                this.__controls[control.id] = control;
            },

            getControl: function (id) {
                if (this.__controls[id]) {
                    return this.__controls[id];
                }

                if (this.__controls[this.getPrefixed(id)]) {
                    return this.__controls[this.getPrefixed(id)];
                }

                return null;
            },

            wrapOriginal: function (originalName, newFunction) {
                if (_.isFunction(this[originalName])) {
                    this[originalName] = _.wrap(this[originalName], newFunction);
                }
            }
        };


        var panelBase = {
            _areas: {},
            _initialized: false,

            extend: function (options) {
                return _.extend(this, options);
            },

            registerArea: function (name, options, forceOverride) {

                var settings = _.extend(
                    CP_Customizer.utils.deepClone(baseArea),
                    options
                );

                if (!this._areas[name] || forceOverride === true) {
                    this._areas[name] = _.extend({
                        enabled: true,
                        name: name
                    }, settings);


                }

                return this._areas[name];
            },

            hasArea: function (name) {
                return !_.isUndefined(this._areas[name]);
            },

            getArea: function (name) {
                return this._areas[name];
            },

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

                areas = CP_Customizer.hooks.applyFilters('section_sidebar_sort_areas', areas);

                areas.forEach(function (area) {
                    if (self._areas[area] && self._areas[area].canInclude()) {
                        CP_Customizer.hooks.doAction('before_section_sidebar_area_' + name, $sectionSettingsContainer);

                        self._processExtendAreaQueue(self._areas[area]);

                        self._areas[area].initAreaTitle($sectionSettingsContainer);
                        self._areas[area].init($sectionSettingsContainer);

                        CP_Customizer.hooks.doAction('after_section_sidebar_area_' + name, $sectionSettingsContainer);
                    }
                });

                this.update(data);
            },

            update: function (data) {

                var self = this,
                    initData = CP_Customizer.utils.deepClone(data),
                    rebindCallback = _.throttle(function () {
                        self.partialUpdate(initData);
                    }, 50);

                CP_Customizer.rebind(CP_Customizer.events.PREVIEW_LOADED + ".settings-panel", _.debounce(function () {
                    var oldSectionSelector = CP_Customizer.preview.getNodeAbsSelector(initData.section);
                    initData.section = CP_Customizer.preview.jQuery(oldSectionSelector);
                    rebindCallback();
                }, 100));
                CP_Customizer.rebind(CP_Customizer.events.STATE_UPDATED + ".settings-panel", rebindCallback);

                data.sectionExports = CP_Customizer.getSectionExports(data.section);

                for (var area in this._areas) {

                    if (this._areas[area].canInclude()) {

                        CP_Customizer.hooks.doAction('update_before_section_sidebar_area_' + name, data);
                        this._areas[area].update(data);
                        CP_Customizer.hooks.doAction('update_after_section_sidebar_area_' + name, data);

                    }
                }
            },

            partialUpdate: function (data) {

                for (var area in this._areas) {

                    if (this._areas[area].canInclude()) {

                        CP_Customizer.hooks.doAction('partial_update_before_section_sidebar_area_' + name, data);
                        this._areas[area].partialUpdate(data);
                        CP_Customizer.hooks.doAction('partial_update_after_section_sidebar_area_' + name, data);

                    }
                }
            },

            disableArea: function (name) {
                if (this._areas[name] && this._areas[name].enabled) {
                    this._areas[name].enabled = false;
                    this._areas[name].disable();
                }
            },
            enableArea: function (name) {
                if (this._areas[name] && !this._areas[name].enabled) {
                    this._areas[name].enabled = false;
                    this._areas[name].enable();
                }
            },

            excludeArea: function (name) {
                if (this._areas[name]) {
                    this._areas[name].exclude();
                }
            },

            _areasToExtend: {},

            _processExtendAreaQueue: function (area) {
                var extendCallback = this._areasToExtend[area.name];

                if (extendCallback) {
                    extendCallback(area)
                }
            },

            extendArea: function (name, callback) {
                if (this._areas[name]) {
                    callback(this._areas[name]);
                } else {
                    if (!this._areasToExtend[name]) {
                        this._areasToExtend[name] = callback;
                    }
                }
            }
        };

        CP_Customizer.panels = CP_Customizer.panel || {};
        CP_Customizer.panels.panelBase = panelBase;
        CP_Customizer.panels.panelArea = baseArea;

        CP_Customizer.panels.instantiate = function (config) {
            return _.extend(CP_Customizer.utils.deepClone(panelBase), config || {});
        };

        CP_Customizer.panels.sectionPanel = _.extend(CP_Customizer.panels.sectionPanel || {}, panelBase);

    });

})(window, CP_Customizer, jQuery);
