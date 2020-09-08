/* global top */

(function (root, CP_Customizer, $) {

    function newColumnContent() {
        return '' +
            '<div data-cpid="new" class="column_28" reveal-fx="RevealFX115" data-scrollreveal="RevealFX115">' +
            '<img width="184" height="174" src="@@ROOT@@/wp-content/themes/reiki-dragdrop/images/icon1.png" data-content-code-editable="true" data-content-editable="true" contenteditable="true">' +
            '<h4 data-content-code-editable="true" data-content-editable="true" contenteditable="true">ON CANVAS TEXT EDITING</h4>' +
            '<p class="small_text1" data-content-code-editable="true" data-content-editable="true" contenteditable="true">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>' +
            '</div>';
    }

    CP_Customizer.addModule(function (CP_Customizer) {
        var $ = jQuery; // use global jquery until preview is ready

        CP_Customizer.bind(CP_Customizer.events.PREVIEW_LOADED, function () {
            // add preview jquery  spectrum plugin
            // use preview jQuery as the $ for the next elements
            $ = CP_Customizer.preview.jQuery();
            $.fn.spectrum = root.jQuery.fn.spectrum;
        });

        function getColorComponent() {
            if ($('#cp-spectrum-keeper').length) {
                return $('#cp-spectrum-keeper');
            }

            var $template = $(
                '   <div id="cp-spectrum-picker" class="picker">' +
                '       <div class="bg-picker">' +
                '            <h5 class="legend">' + window.CP_Customizer.translateCompanionString('Background Color') + '</h5>' +
                '           <input name="color" type="text" />' +
                '       </div>');

            var colorPalette = root.CP_Customizer.data().bgColorPalette;
            $template.find('[name=color]').spectrum({
                flat: true,
                preferredFormat: "hex",
                showPaletteOnly: true,
                color: '#ffffff',
                palette: [colorPalette],
                move: function (color) {
                    if (!color) {
                        return;
                    }
                    var node = $(this).closest('.node-overlay').data().node;
                    $(this).find('[name=color]').spectrum("set", color.toString());
                    $(node).css('background-color', color.toString());
                    $(this).closest('.cog.active').removeClass('active');
                    CP_Customizer.markSave();
                },
                change: function (color) {
                    if (!color || !$(this).closest('.node-overlay').data()) {
                        return;
                    }
                    $(this).find('[name=color]').spectrum("set", color.toString());
                    var node = $(this).closest('.node-overlay').data().node;
                    $(node).css('background-color', color.toString());
                    $(this).closest('.cog.active').removeClass('active');
                    CP_Customizer.markSave();
                }
            });

            $template.find('[name="addImage"]').click(function (event) {
                var node = $(this).closest('.node-overlay').data().node;
                CP_Customizer.openImageManager(function (image) {
                    $(node).css('background-image', "url(" + image + ")");
                    CP_Customizer.markSave();
                });
            });


            $template.find('[name="removeImage"]').click(function (event) {
                var node = $(this).closest('.node-overlay').data().node;
                $(node).css('background-image', "none");
                CP_Customizer.markSave();
            });


            $template.find('[name="coverImage"]').click(function (event) {
                var node = $(this).closest('.node-overlay').data().node;

                if ($(this).hasClass('active')) {
                    $(node).css('background-size', "auto");
                    $(node).css('background-repeat', "repeat");
                } else {
                    $(node).css('background-size', "cover");
                    $(node).css('background-repeat', "no-repeat");
                }
                CP_Customizer.markSave();
                $(this).toggleClass('active');
            });

            function update() {

                var node = $(this).closest('.node-overlay').data().node;

                var backgroundColor = "#" + tinycolor($(node).css('background-color')).toHex();

                if (colorPalette.indexOf(backgroundColor) !== -1) {
                    $(this).closest('.cog-subitems').removeClass('disabled');
                    $(this).find('[name=color]').spectrum("set", $(node).css('background-color'));

                } else {
                    $(this).closest('.cog-subitems').addClass('disabled');
                    var $reason = $(this).closest('.cog-subitems').children('p.disable-reason');

                    if ($reason.length === 0) {
                        $reason = $("<p class='disable-reason'> " + window.CP_Customizer.translateCompanionString('This section has a custom background color') +
                            "<br/>" + window.CP_Customizer.translateCompanionString('Background can be changed in PRO') + "</p>");
                        $(this).closest('.cog-subitems').prepend($reason);
                    }

                }

                // if ($(node).css('background-size') && $(node).css('background-size') === "cover") {
                //     $template.find('[name="coverImage"]').addClass('active');
                // } else {
                //     $template.find('[name="coverImage"]').removeClass('active');
                // }


            }

            $template.data('update', update.bind($template));
            return $template;
        }


        CP_Customizer.overlays.registerFixedOverlayOptions({
            'section': {
                'title': function (node) {
                    var label = $(node).attr('data-label') ? $(node).attr('data-label') + " [" +
                        window.CP_Customizer.translateCompanionString('Section') + "]" : window.CP_Customizer.translateCompanionString('SECTION');
                    return label;
                },
                'toolbarTitle': function (node) {
                    var label = $(node).attr('data-label') ? $(node).attr('data-label') + " [" + window.CP_Customizer.translateCompanionString('Section') + "]" :
                        window.CP_Customizer.translateCompanionString('SECTION');
                    return label;
                },
                toolbar_binds: {
                    hover: [
                        function (event, overlay) {

                            // layout toggle
                            var $changerItem = overlay.find('[data-name="section_layout_width_changer"]');

                            if (!$(this).closest(CP_Customizer.preview.getPageContainerSelector()).length) {
                                $changerItem.hide();
                            } else {
                                $changerItem.show();
                            }

                            if ($(this).children().is('.gridContainer')) {
                                $changerItem.text(window.CP_Customizer.translateCompanionString('Make full width'));
                                $changerItem.data('toMake', 'full');
                            } else {
                                $changerItem.text(window.CP_Customizer.translateCompanionString('Make Centered'));
                                $changerItem.data('toMake', 'centered');
                            }

                            var $bgChanger = overlay.find('[data-name="page_background_image"]');
                            var $isTransparent = tinycolor(getComputedStyle($(this)[0]).backgroundColor).getAlpha() === 0;

                            if ($isTransparent) {
                                $bgChanger.show();
                            } else {
                                $bgChanger.hide();
                            }
                        },
                        function () {
                        }
                    ]
                },
                'items': [{
                    'name': 'section_layout_width_changer',
                    'title': window.CP_Customizer.translateCompanionString('Make full width'),
                    'on_click': function (node) {
                        var toMake = $(this).data().toMake;
                        switch (toMake) {
                            case 'centered':
                                $(node).children().not('[class*="section-separator"]').addClass('gridContainer');
                                break;
                            case 'full':
                                $(node).children().not('[class*="section-separator"]').removeClass('gridContainer');
                                break;
                        }
                        $(this).trigger('reiki.update_overlays');

                        CP_Customizer.hooks.doAction('section_layout_changed', node, toMake);
                        $(this).closest('.overlay-toolbar').trigger('mouseover');

                        CP_Customizer.markSave();
                    }
                }, {
                    'name': 'section_color_changer',
                    'title': window.CP_Customizer.translateCompanionString('Change background'),
                    'classes': 'subitems-arrow',
                    'on_hover': [function () {
                        var $subitemsContainer = $(this).children('.cog-subitems').length ? $(this).children('.cog-subitems') : $("<div class='cog-subitems' />");
                        $brush = getColorComponent();

                        $subitemsContainer.empty();
                        $subitemsContainer.appendTo($(this));

                        $subitemsContainer.append($brush);
                        $brush.data('update')();
                    }, function () {
                        //console.log('hover out');
                    }]

                }, {
                    'name': 'page_background_image',
                    'title': window.CP_Customizer.translateCompanionString('Change background Image'),
                    'on_click': function (event) {
                        root.CP_Customizer.wpApi.control('background_image').focus();
                    }
                }]
            },
            'list': {
                'nodeOverrider': function (node) {
                    return $(node).is('[data-type="row"]') ? $(node) : $(node).find('[data-type="row"]').eq(0);
                },
                'title': function (node) {
                    var label = $(node).closest('[data-label]').length ? $(node).closest('[data-label]').attr('data-label') +
                        " [" + window.CP_Customizer.translateCompanionString("List") + "]" : window.CP_Customizer.translateCompanionString("List");
                    return label;
                },
                'toolbarTitle': function (node) {
                    var label = $(node).closest('[data-label]').length ? $(node).closest('[data-label]').attr('data-label') + " [" +
                        window.CP_Customizer.translateCompanionString("List") + "]" : window.CP_Customizer.translateCompanionString("List");
                    return label;
                },
                toolbar_binds: {
                    hover: [
                        function (event, overlay) {
                            if ($(this).find('[data-type="row"]').length || $(this).is('[data-type="row"]')) {
                                overlay.find('[data-category="list"]').show();

                                if ($(overlay.data('node')).find('[data-type="row"]').is('[data-content-shortcode]')) {
                                    overlay.find('[data-name="row_add_item"]').hide();
                                    overlay.find('[data-name="columns_reorder"]').hide();
                                } else {
                                    overlay.find('[data-name="row_add_item"]').show();
                                    overlay.find('[data-name="columns_reorder"]').show();

                                }
                            } else {
                                overlay.find('[data-category="list"]').hide();
                            }
                        },
                        function (event, overlay) {
                        }
                    ]
                },
                'items': [{
                    'name': 'row_add_item',
                    'title': window.CP_Customizer.translateCompanionString('Add item'),
                    'on_click': function (node) {
                        var content = newColumnContent();
                        var $content;
                        if (!$(node).children().length) {
                            content = content.split("@@ROOT@@").join(getWPLocation());
                            $content = $(content);
                        } else {
                            $content = $(node).children('div').first().clone(false, false);
                        }


                        $content = root.CP_Customizer.preview.cleanNode($content);
                        root.CP_Customizer.preview.insertNode($content, $(node));
                        root.CP_Customizer.hooks.doAction('section_list_item_refresh');
                    }
                }, {
                    'name': 'cloumns_per_row',
                    'title': window.CP_Customizer.translateCompanionString('Columns per row'),
                    'classes': 'subitems-arrow',
                    'on_hover': [function (node) {
                        var $subitemsContainer = $(this).children('.cog-subitems').length ? $(this).children('.cog-subitems') : $("<div class='cog-subitems' />");
                        $subitemsContainer.empty();
                        var cols = [1, 2, 3, 4, 6, 12];
                        for (var i = 0; i < cols.length; i++) {
                            var colNr = cols[i];
                            var itemData = {
                                'title': colNr + (colNr > 1 ? " " +
                                    window.CP_Customizer.translateCompanionString("columns") : " " +
                                    window.CP_Customizer.translateCompanionString("column")),
                                'name': colNr + "_columns",
                                'on_click': function (node) {

                                    var columns = this.data('name').replace('_columns', '');
                                    var value = parseInt(columns);
                                    columns = parseInt(columns);
                                    columns = parseInt(12 / columns);


                                    if (node.is('[data-dynamic-columns]')) {
                                        var setting = node.attr('data-dynamic-columns');
                                        if (setting !== "handled") {

                                            root.CP_Customizer.setMod(setting, columns, "refresh");

                                        } else {

                                            CP_Customizer.hooks.doAction('dynamic_columns_handle', columns, node, value);

                                        }
                                        return;
                                    }

                                    root.CP_Customizer.markSave();
                                    var _class = 'col-md-' + columns,
                                        colsRegexp = /((cp[0-9]+cols)(\s|$))|((col\-md\-[0-9]+)(\s|$))/ig,
                                        currentDevice = root.CP_Customizer.preview.currentDevice();

                                    switch (currentDevice) {
                                        case "tablet":
                                            colsRegexp = /((cp[0-9]+cols\-tablet)(\s|$))|((col\-sm\-[0-9]+)(\s|$))/ig;
                                            _class = 'col-sm-' + columns;
                                            break;

                                        case "mobile":
                                            colsRegexp = /((cp[0-9]+cols\-mobile)(\s|$))|((col\-xs\-[0-9]+)(\s|$))/;
                                            _class = 'col-xs-' + columns;
                                            break;
                                    }

                                    $(node).children().each(function (index, el) {
                                        var $col = $(el),
                                            _classAttr = $col.attr('class');

                                        // remove existing cols:
                                        _classAttr = _classAttr.replace(colsRegexp, "");

                                        // add new class
                                        _classAttr += " " + _class + " ";


                                        //remove not needed spaces in class attribute
                                        _classAttr = _classAttr.replace(/\s\s+/, " ");
                                        $col.attr('class', _classAttr);


                                    });

                                    if (!$(node).hasClass('row')) {
                                        $(node).attr('class', 'row');
                                    }


                                    // wait for animations
                                    _.delay(root.CP_Customizer.overlays.updateAllOverlays, 600);
                                }
                            };

                            var $button = root.CP_Customizer.overlays.getOverlayOptionButton(itemData, node);
                            $subitemsContainer.append($button);
                        }

                        $subitemsContainer.appendTo($(this));
                    }, function () {
                    }]
                }, {
                    'name': 'columns_reorder',
                    'title': window.CP_Customizer.translateCompanionString('Reorder Items'),
                    'classes': 'subitems-arrow',
                    'on_hover': [function (node) {

                        var $ = root.CP_Customizer.preview.jQuery();
                        var $subitemsContainer = $(this).children('.cog-subitems').length ? $(this).children('.cog-subitems') : $("<div class='cog-subitems' />");
                        $subitemsContainer.empty();

                        var itemsNo = $(node).children().length;
                        for (var i = 0; i < itemsNo; i++) {
                            var item = node.children().eq(i);
                            var title = item.text().replace(/\s\s+/g, " ").trim();


                            title = title.trim().length ? title : "";

                            if (!title && item.find('img').length) {
                                title = CP_Customizer.utils.getFileInfo(item.find('img').eq(0).attr('src')).filename;
                            }

                            if (!title) {
                                title = '[ ITEM ]';
                            }

                            title = title.replace(/(^.{1,10})(.*)$/, function (matches, firstMatch, secondMatch) {
                                var result = (firstMatch || "").trim() + (secondMatch && secondMatch.length ? "[...]" : "");
                                return result;
                            });

                            var itemData = {
                                'title': (i + 1) + ') ' + title,
                                'name': 'reorder_item_' + i,
                                'classes': 'sortable-item'
                            };

                            var $button = root.CP_Customizer.overlays.getOverlayOptionButton(itemData, item);
                            $button.data('item', item);
                            $subitemsContainer.append($button);
                        }

                        $subitemsContainer.appendTo($(this));
                        $subitemsContainer.sortable({
                            'axis': 'y',
                            'start': function (event, ui) {
                                ui.helper.css({
                                    'position': 'fixed',
                                    'transition': 'none',
                                    'background-color': '#ffffff',
                                    'left': ui.helper.offset().left + 'px'
                                });

                                ui.item.parent().addClass('big-heat-area');
                            },
                            sort: function (event, ui) {
                                ui.helper.css('top', event.clientY);
                            },
                            'stop': function (event, ui) {
                                var index = ui.item.index();
                                var node = jQuery(ui.item).data().item;
                                root.CP_Customizer.preview.insertNode(node, node.parent(), index);
                                ui.item.parent().removeClass('big-heat-area');
                            }
                        });
                    }, function () {
                    }]
                }]
            },
            'column': {
                'title': window.CP_Customizer.translateCompanionString('List item'),
                'toolbarTitle': 'List item',
                'node_binds': {
                    'hover': [function () {
                        $(this).data().overlay.removeClass('hide');
                    }, function () {
                        $(this).data().overlay.addClass('hide');
                    }]
                },
                'items': [{
                    'name': 'row_remove_item',
                    'title': window.CP_Customizer.translateCompanionString('Remove Item'),
                    'on_click': function (node) {
                        var $overlay = $($(node).data('overlay'));
                        if ($overlay.data('node-observer')) {
                            $overlay.data('node-observer').disconnect();
                        }

                        $overlay.remove();
                        $(node).remove();
                        $(this).trigger('reiki.update_overlays');
                        root.CP_Customizer.markSave();
                    }
                }]

            }
        });
    });

    function addSectionOverlay($nodes) {

        $nodes.addFixedOverlay({
            types: ["section", "list"],
            "classes": ['section-overlay']
        });

    }


    CP_Customizer.bind(CP_Customizer.events.ADD_FIXED_OVERLAYS, function (event, $startNode) {
        addSectionOverlay($startNode.find('[data-label]'));

        if ($startNode.is('[data-label]')) {
            _.delay(function () {
                addSectionOverlay($startNode);
            }, 0);
        }

    });
})(window, CP_Customizer, jQuery);
