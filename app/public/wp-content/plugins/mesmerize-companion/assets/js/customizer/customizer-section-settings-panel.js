(function (root, CP_Customizer, $) {
    CP_Customizer.addModule(function (CP_Customizer) {
        CP_Customizer.hooks.addFilter('section_fixed_overlay_options', function (options, type) {

            var item = {

                name: "section_more_settings_button",
                title: window.CP_Customizer.translateCompanionString("Section Settings"),

                on_click: function (node) {
                    var section = node;

                    if (!section.parent().is(CP_Customizer.preview.getRootNode())) {
                        section = node.parentsUntil(top.CP_Customizer.preview.getRootNode()).last();
                    }

                    section = CP_Customizer.hooks.applyFilters('filter_cog_item_section_element', section, node);

                    CP_Customizer.wpApi.panel('page_content_panel').focus()
                    CP_Customizer.openRightSidebar("cp-section-setting", {
                        section: section
                    });
                }

            };

            if (type === "section" && options && !itemExists(options, item.name)) {
                options.items.push(item);
            }

            return options;
        });


        CP_Customizer.hooks.addFilter('section_fixed_overlay', function (options, key) {

            var tempOptions = _.clone(options);

            if (key === "items") {
                if (_.isArray(tempOptions)) {
                    tempOptions.forEach(function (item, index, optionsList) {
                        if (item.name === "section_color_changer" || item.name === "columns_reorder") {
                            if (item.on_hover) {
                                delete item.on_hover;
                            }

                            item.classes = "";

                            item.on_click = function (node) {
                                var section = node;

                                if (!section.parent().is(CP_Customizer.preview.getRootNode())) {
                                    section = node.parentsUntil(top.CP_Customizer.preview.getRootNode()).last();
                                }

                                section = CP_Customizer.hooks.applyFilters('filter_cog_item_section_element', section, node);

                                CP_Customizer.wpApi.panel('page_content_panel').focus()
                                CP_Customizer.openRightSidebar("cp-section-setting", {
                                    section: section
                                });
                            }
                        }

                        if (item.name === "page_background_image") {
                            delete optionsList[index];
                        }
                    });
                }

                // do return empty array slots
                var result = tempOptions.filter(function (item) {
                    return item;
                });
            } else {
                result = options;
            }


            return result;
        });

        function itemExists(options, name) {

            if (options && options.items) {
                for (var i = 0; i < options.items.length; i++) {
                    var item = options.items[i];

                    if (item.name === name) {
                        return true;
                    }
                }
            }

            return false;
        }


        CP_Customizer.hooks.addFilter('section_fixed_overlay_options', function (options, type) {

            var item = {

                name: "section_more_settings_button",
                title: window.CP_Customizer.translateCompanionString("Section Settings"),

                on_click: function (node) {
                    var section = node;

                    if (!section.parent().is(CP_Customizer.preview.getRootNode())) {
                        section = node.parentsUntil(top.CP_Customizer.preview.getRootNode()).last();
                    }

                    section = CP_Customizer.hooks.applyFilters('filter_cog_item_section_element', section, node);

                    CP_Customizer.wpApi.panel('page_content_panel').focus()
                    CP_Customizer.openRightSidebar("cp-section-setting", {
                        section: section
                    });
                }

            };

            if (type === "section" && options && !itemExists(options, item.name)) {
                options.items.push(item);
            }

            return options;
        });
    });

})(window, CP_Customizer, jQuery);


(function (root, CP_Customizer, $) {
    CP_Customizer.addModule(function (CP_Customizer) {
        var sectionPanel = CP_Customizer.panels.sectionPanel;
        sectionPanel.registerArea('background_color', {
            init: function ($container) {

                var separator = CP_Customizer.createControl.sectionSeparator(
                    this.getPrefixed('separator'),
                    $container,
                    window.CP_Customizer.translateCompanionString('Background Color')
                );

                this.addToControlsList(separator);

                var palette = CP_Customizer.createControl.palette(
                    this.getPrefixed('background-color'),
                    $container,
                    {
                        value: '#ffffff',
                        label: window.CP_Customizer.translateCompanionString('Background Color'),
                        palette: ['#ffffff', '#F5FAFD']
                    });

                this.addToControlsList(palette);

                CP_Customizer.createControl.infoPRO(
                    this.getPrefixed('section-bg-pro-info'),
                    $container,
                    '<span>' + window.CP_Customizer.translateCompanionString("More section design options available in PRO") + '</span><br/> ' +
                    '<a href="' + window.mesmerize_customize_settings.upgrade_url + '" class="button button-small button-orange upgrade-to-pro" target="_blank">' +
                    '' + window.CP_Customizer.translateCompanionString("Check all PRO features") + '</a>'
                )
            },
            update: function (data) {
                var image = CP_Customizer.utils.normalizeBackgroundImageValue((getComputedStyle(data.section[0]).backgroundImage || "")) || false;
                image = (image && image !== "none" && !image.endsWith('/none')) ? image : false;
                var color = getComputedStyle(data.section[0]).backgroundColor;

                if (image) {

                    this.disable();
                    return;
                }

                this.enable();

                var palette = this.getControl('background-color');

                palette.attachWithSetter(color, function (value) {

                    data.section.css({
                        'background-image': 'none',
                        'background-color': value
                    });

                    CP_Customizer.updateState();
                });
            }
        });

        sectionPanel.registerArea('background_image', {
            init: function ($container) {

                var separator = CP_Customizer.createControl.sectionSeparator(
                    this.getPrefixed('separator'),
                    $container,
                    window.CP_Customizer.translateCompanionString('Background Image')
                );

                this.addToControlsList(separator);

                var bgImage = CP_Customizer.createControl.image(
                    this.getPrefixed('image'),
                    $container,
                    {
                        value: '',
                        label: window.CP_Customizer.translateCompanionString('Background Image')
                    });

                this.addToControlsList(bgImage);
            },

            update: function (data) {
                var image = CP_Customizer.utils.normalizeBackgroundImageValue((getComputedStyle(data.section[0]).backgroundImage || "")) || false;
                image = (image && image !== "none" && !image.endsWith('/none')) ? image : false;


                var bgImage = this.getControl('image');

                if (!image) {
                    this.disable();
                    return;
                }

                this.enable();

                bgImage.attachWithSetter(image, function (value) {

                    if (value) {
                        value = 'url("' + value + '")';
                    } else {
                        value = "";
                    }
                    data.section.css({
                        'background-color': 'none',
                        'background-image': value,
                        'background-size': 'cover',
                        'background-position': 'center top'
                    });


                    CP_Customizer.updateState();
                });
            }
        });


        sectionPanel.registerArea('content_align', {
            init: function ($container) {

                var separator = CP_Customizer.createControl.sectionSeparator(
                    this.getPrefixed('separator'),
                    $container,
                    window.CP_Customizer.translateCompanionString('Content Align')
                );

                this.addToControlsList(separator);

                var textAlign = CP_Customizer.createControl.select(
                    this.getPrefixed('text_align'),
                    $container,
                    {
                        value: '',
                        label: window.CP_Customizer.translateCompanionString('Content align'),
                        choices: {
                            'content-align-default': window.CP_Customizer.translateCompanionString('Default'),
                            'content-left-sm': window.CP_Customizer.translateCompanionString('Left'),
                            'content-center-sm': window.CP_Customizer.translateCompanionString('Center'),
                            'content-right-sm': window.CP_Customizer.translateCompanionString('Right')
                        }
                    });

                this.addToControlsList(textAlign);
            },

            update: function (data) {
                var section = data.section;
                var sectionExports = CP_Customizer.getSectionExports(section);

                if (!sectionExports.contentAligns) {
                    this.disable();
                    return;
                }

                this.enable();

                var selector = sectionExports.contentAligns.join(' , '),
                    $alignHolders = section.find(selector);

                if (section.is(selector)) {
                    $alignHolders = $alignHolders.add(section);
                }

                var textAlign = this.getControl('text_align');
                var textAlignClasses = ['content-left-sm', 'content-center-sm', 'content-right-sm'];
                var currentTextAlign = CP_Customizer.utils.nodeMatchingClasses($alignHolders, textAlignClasses, true);

                textAlign.attachWithSetter(
                    currentTextAlign || 'content-align-default',
                    function (value, oldValue) {
                        if (oldValue.trim()) {
                            $alignHolders.each(function () {
                                $(this).removeClass(oldValue);
                            })
                        }
                        if (value.trim() && value !== 'content-align-default') {
                            $alignHolders.each(function () {
                                $(this).addClass(value);
                            })
                        }
                    }
                );
            }
        });


        // frame box
        sectionPanel.registerArea('frame', {
            init: function ($container) {
                var frameSeparator = CP_Customizer.createControl.sectionSeparator(this.getPrefixed('separator'),
                    $container, window.CP_Customizer.translateCompanionString('Frame Settings'));
                this.addToControlsList(frameSeparator);
                $groupEl = $container;
                var frameType = CP_Customizer.createControl.select(this.getPrefixed('type'), $groupEl, {
                    value: '',
                    label: window.CP_Customizer.translateCompanionString('Type'),
                    choices: {
                        "background": window.CP_Customizer.translateCompanionString("background"),
                        "border": window.CP_Customizer.translateCompanionString("border")
                    }
                });
                this.addToControlsList(frameType);
                var frameColor = CP_Customizer.createControl.color(this.getPrefixed('color'), $groupEl, {
                    value: '#ffffff',
                    label: window.CP_Customizer.translateCompanionString('Color')
                });

                this.addToControlsList(frameColor);

                var frameOffsetX = CP_Customizer.createControl.slider(this.getPrefixed('offs-x'), $groupEl, {
                    label: window.CP_Customizer.translateCompanionString('Offset X'),
                    choices: {
                        min: -100,
                        max: 100,
                        step: 1
                    }
                });

                this.addToControlsList(frameOffsetX);
                var frameOffsetY = CP_Customizer.createControl.slider(this.getPrefixed('offs-y'), $groupEl, {
                    label: window.CP_Customizer.translateCompanionString('Offset Y'),
                    choices: {
                        min: -100,
                        max: 100,
                        step: 1
                    }
                });

                this.addToControlsList(frameOffsetY);
                var frameWidth = CP_Customizer.createControl.slider(this.getPrefixed('width'), $groupEl, {
                    label: window.CP_Customizer.translateCompanionString('Width'),
                    choices: {
                        min: 1,
                        max: 200,
                        step: 1
                    }
                });

                this.addToControlsList(frameWidth);
                var frameHeight = CP_Customizer.createControl.slider(this.getPrefixed('height'), $groupEl, {
                    label: window.CP_Customizer.translateCompanionString('Height'),
                    choices: {
                        min: 1,
                        max: 200,
                        step: 1
                    }
                });
                this.addToControlsList(frameHeight);

                var frameHideOnMobile = CP_Customizer.createControl.checkbox(
                    this.getPrefixed('hide-on-mobile'),
                    $groupEl,
                    window.CP_Customizer.translateCompanionString('Hide on mobile')
                );

                this.addToControlsList(frameHideOnMobile);


            },
            update: function (data) {
                var section = data.section;
                var hasFrame = section.find('.overlay-box').length > 0;
                if (!hasFrame) {
                    this.disable();
                    return;
                }
                this.enable();
                if (hasFrame) {
                    var frameType = this.getControl('type');
                    var frameWidth = this.getControl('width');
                    var frameHeight = this.getControl('height');
                    var frameOffsetX = this.getControl('offs-x');
                    var frameOffsetY = this.getControl('offs-y');
                    var frameColor = this.getControl('color');
                    var frameHideOnMobile = this.getControl('hide-on-mobile');

                    var nodeSel = ".overlay-box .overlay-box-offset";


                    var selector = '[data-id="' + section.attr('data-id') + '"] ' + nodeSel;
                    var node = section.find(nodeSel);

                    frameHideOnMobile.attachWithSetter(node.hasClass("hide-xs"), function (value) {
                        if (value === true) {
                            node.addClass("hide-xs");
                        } else {
                            node.removeClass("hide-xs");
                        }
                    });

                    function getProp(name) {
                        return (CP_Customizer.contentStyle.getNodeProp(node, selector, null, name));
                    }

                    function setProp(name, value) {
                        CP_Customizer.contentStyle.setProp(selector, null, name, value);
                    }

                    function getFrameType() {
                        var type = "background";
                        if (node.hasClass("offset-border")) {
                            type = "border";
                        }
                        return type;
                    }

                    frameType.attachWithSetter(getFrameType(), function (value) {
                        if (value == "border") {
                            node.removeClass("offset-background");
                            node.addClass("offset-border");
                            setProp(value + '-color', getProp('background-color'));
                            setProp('background-color', 'transparent');
                        }
                        if (value == "background") {
                            node.addClass("offset-background");
                            node.removeClass("offset-border");
                            setProp(value + '-color', getProp('border-color'));
                            setProp('border-color', 'transparent');
                        }
                    });

                    frameColor.attachWithSetter(getProp(getFrameType() + '-color'), function (value) {
                        setProp(getFrameType() + '-color', value);
                    });

                    frameHeight.attachWithSetter(parseInt(getProp('height')), function (value) {
                        setProp('height', value + "%");
                    });

                    frameWidth.attachWithSetter(parseInt(getProp('width')), function (value) {
                        setProp('width', value + "%");
                    });

                    function getTranslate() {
                        var transform = getProp('transform');
                        var translate = transform.match(/translate\(([^,]+),([^,]+)\)/) || [];
                        var translateX = translate.length ? translate[1] : 0;
                        var translateY = translate.length ? translate[2] : 0;
                        return {
                            'x': parseInt(translateX),
                            'y': parseInt(translateY),
                        }
                    }

                    function setTranslate(x, y) {
                        var translate = getTranslate();
                        if (!_.isUndefined(x)) {
                            translate.x = x;
                        }
                        if (!_.isUndefined(y)) {
                            translate.y = y;
                        }
                        setProp('transform', 'translate(' + translate.x + '%,' + translate.y + '%)');
                    }

                    var translate = getTranslate();
                    frameOffsetX.attachWithSetter(parseInt(translate.x), function (value) {
                        setTranslate(value, undefined);
                    });

                    frameOffsetY.attachWithSetter(parseInt(translate.y), function (value) {
                        setTranslate(undefined, value);
                    });
                }
            }
        }); // #####
        sectionPanel.registerArea('list_items', {

            itemsListControlTemplate: '' +
                '<div class="section-list-item">' +
                '   <div class="handle reorder-handler"></div>' +
                '   <div class="text">' +
                '           <span title="color item" class="featured-item color"></span>' +
                '           <span class="label"><%= text %></span>' +
                '   </div>' +
                '</div>' +
                '',

            init: function ($container) {
                this.controls = [];

                var itemOptionsControl = CP_Customizer.createControl.sectionSeparator(
                    this.getPrefixed('title_separator'),
                    $container,
                    window.CP_Customizer.translateCompanionString('Items Options')
                );

                this.addToControlsList(itemOptionsControl);

                var itemsAlign = CP_Customizer.createControl.select(
                    this.getPrefixed('items_align'),
                    $container,
                    {
                        value: '',
                        label: window.CP_Customizer.translateCompanionString('Items align'),
                        choices: {
                            'items-align-default': window.CP_Customizer.translateCompanionString('Default'),
                            'start-sm': window.CP_Customizer.translateCompanionString('Left'),
                            'center-sm': window.CP_Customizer.translateCompanionString('Center'),
                            'end-sm': window.CP_Customizer.translateCompanionString('Right')
                        }
                    });
                this.addToControlsList(itemsAlign);

                var textAlign = CP_Customizer.createControl.select(
                    this.getPrefixed('text_align'),
                    $container,
                    {
                        value: '',
                        label: window.CP_Customizer.translateCompanionString('Item content align'),
                        choices: {
                            'content-align-default': window.CP_Customizer.translateCompanionString('Default'),
                            'content-left-sm': window.CP_Customizer.translateCompanionString('Left'),
                            'content-center-sm': window.CP_Customizer.translateCompanionString('Center'),
                            'content-right-sm': window.CP_Customizer.translateCompanionString('Right')
                        }
                    });

                this.addToControlsList(textAlign);

                listItemsOrder = CP_Customizer.createControl.sortable(
                    this.getPrefixed('order'),
                    $container,
                    _.template(this.itemsListControlTemplate),
                    window.CP_Customizer.translateCompanionString('Items position')
                );

                listItemsOrder.control.container.append('<a class="add-item button-primary">' + window.CP_Customizer.translateCompanionString("Add Item") + '</a>');

                this.addToControlsList(listItemsOrder);
            },

            update: function (data) {
                var section = data.section;
                var row = section.find('[data-type="row"]');

                if (!row.length || row.is('[data-content-shortcode]')) {
                    this.disable();
                    return;
                }

                this.enable();


                var textAlign = this.getControl('text_align'),
                    textAlignClasses = ['content-left-sm', 'content-center-sm', 'content-right-sm'],
                    currentTextAlign = CP_Customizer.utils.nodeMatchingClasses(row, textAlignClasses, true),

                    itemsAlign = this.getControl('items_align'),
                    itemsAlignClasses = ['start-sm', 'center-sm', 'end-sm'],
                    currentItemsAlign = CP_Customizer.utils.nodeMatchingClasses(row, itemsAlignClasses, true);

                var sectionExports = CP_Customizer.getSectionExports(section);
                var canAlignItems = _.isUndefined(sectionExports.canAlignRowItemsContent) || sectionExports.canAlignRowItemsContent;

                if (canAlignItems) {
                    textAlign.show();
                    itemsAlign.show();
                } else {
                    textAlign.hide();
                    itemsAlign.hide();
                }

                textAlign.attachWithSetter(
                    currentTextAlign || 'content-align-default',
                    function (value, oldValue) {
                        if (oldValue.trim()) {
                            row.removeClass(oldValue);
                        }
                        if (value.trim() && value !== 'content-align-default') {
                            row.addClass(value);
                        }
                    }
                );

                itemsAlign.attachWithSetter(
                    currentItemsAlign || 'items-align-default',
                    function (value, oldValue) {
                        if (oldValue.trim()) {
                            row.removeClass(oldValue);
                        }
                        if (value.trim() && value !== 'items-align-default') {
                            row.addClass(value);
                        }
                    }
                );


                this.setItems(section, row);

                var listItemsOrder = this.getControl('order');

                listItemsOrder.control.onStop = function (event, ui) {
                    var index = ui.item.index();
                    var node = jQuery(ui.item).data().original;
                    root.CP_Customizer.preview.insertNode(node, node.parent(), index);
                };

                var self = this;
                listItemsOrder.control.container.find('.add-item').unbind('click').click(function () {
                    var $content = row.children('div').first().clone(false, false);

                    $content = root.CP_Customizer.preview.cleanNode($content);
                    root.CP_Customizer.preview.insertNode($content, row);

                    self.setItems(section, row);
                });


                root.CP_Customizer.hooks.removeAction('section_list_item_refresh', function () {
                    self.setItems(section, row);
                });

                root.CP_Customizer.hooks.addAction('section_list_item_refresh', function () {
                    self.setItems(section, row);
                });
            },

            partialUpdate: function (data) {
                var section = data.section;
            },

            getItemOptions: function (section, item) {

                return {};
            },

            afterItemCreation: function (sortableItem, data) {
                sortableItem.data('original', data.original);
            },

            setItems: function (section, row) {
                var listItemsOrder = this.getControl('order');
                listItemsOrder.control.free();

                var items = row.children();
                var self = this;

                items = items.map(function () {
                    var item = $(this);
                    var title = $(this).text().replace(/\s\s+/g, " ").trim();

                    var headingText = $(this).find('h1,h2,h3,h4,h5,h6').eq(0);

                    if (headingText.length) {
                        title = headingText.text().replace(/\s\s+/g, " ").trim();
                    }

                    title = title.trim().length ? title : "";

                    if (!title && item.find('img').length) {
                        title = CP_Customizer.utils.getFileInfo(item.find('img').eq(0).attr('src')).filename;
                    }

                    if (!title) {
                        title = '[ ITEM ]';
                    }

                    title = title.replace(/(^.{1,15})(.*)$/igm, function (matches, firstMatch, secondMatch) {
                        return (firstMatch || "").trim() + (secondMatch && secondMatch.length ? "[...]" : "");
                    });

                    return {
                        text: title,
                        original: $(this),
                        options: self.getItemOptions(section, item)
                    };

                }).toArray();

                listItemsOrder.control.setItems(items, function () {
                    self.afterItemCreation.apply(self, arguments);
                });

            }
        });

        sectionPanel.registerArea('contact_form_options', {
            init: function ($container) {
                var separator = CP_Customizer.createControl.sectionSeparator(
                    this.getPrefixed('separator'),
                    $container,
                    window.CP_Customizer.translateCompanionString('Contact Form 7 Options')
                );

                this.addToControlsList(separator);

                var showInfosInOneColumn = CP_Customizer.createControl.checkbox(
                    this.getPrefixed('show-inline-info'),
                    $container,
                    window.CP_Customizer.translateCompanionString('Show form controls on one column')
                );

                this.addToControlsList(showInfosInOneColumn);

            },

            update: function (data) {
                var section = data.section;

                if (section.attr('data-export-id').match(/contact/) && section.find('[data-content-shortcode*="contact-form-7"]').length > 0) {
                    this.enable();
                } else {
                    this.disable();
                    return;
                }

                var showInfosInOneColumn = this.getControl('show-inline-info');
                var contactFormWrapper = section.find('.contact-form-wrapper');
                var hasInlineInfo = contactFormWrapper.is('.inline-info');

                showInfosInOneColumn.attachWithSetter(!hasInlineInfo, function (value) {
                    if (value) {
                        contactFormWrapper.removeClass('inline-info');
                    } else {
                        contactFormWrapper.addClass('inline-info');
                    }
                    CP_Customizer.updateState();
                })

            }
        })

    });

})(window, CP_Customizer, jQuery);
