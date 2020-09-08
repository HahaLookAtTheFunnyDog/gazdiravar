(function ($) {

    var listItemTemplate = _.template(
        '<li class="full_row item" data-id="<%= sectionID %>">' +
        '       <div class="reorder-handler"></div>' +
        '       <div class="label-wrapper">' +
        '  <input class="item-label" value="<%= label %>" />' +
        '  <div class="anchor-info">' +
        '      #<%= id.replace(/#/,\'\') %>' +
        '  </div>' +
        '       </div>' +
        '     <div class="item-hover">' +
        '   <% if(setting) { %>' +
        '      <span title="' + window.CP_Customizer.translateCompanionString("Edit section settings") + '" data-setting="<%= setting %>" class="item-settings"></span>' +
        '  <%  } %>' +
        '  <span title="' + window.CP_Customizer.translateCompanionString("Toggle visibility in primary menu") + '" class="item-menu-visible <%= (inMenu?\'active\':\'\') %>"></span>' +
        '  <span title="' + window.CP_Customizer.translateCompanionString("Delete section from page") + '" class="item-remove"></span>' +
        '       </div>' +
        ' </li>'
    );

    window.CP_Customizer.addModule(function (CP_Customizer) {
        var control = wp.customize.panel('page_content_panel');

        if (!control) {
            return;
        }

        var $contentLi = control.container.eq(0);
        // remove default events
        $contentLi.children('h3').andSelf().off();

        $sectionsSidebarTogglers = $contentLi.find('.add-section-plus').add($contentLi.find('.cp-add-section'));

        CP_Customizer.one(CP_Customizer.events.PREVIEW_LOADED, function () {
            if (CP_Customizer.preview.data("can:change_page_settings")) {
                $contentLi.find('span.section-icon.setting').removeClass('hidden').click(function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var sectionID = "cp-current-page-settings";

                    if (CP_Customizer.isRightSidebarVisible(sectionID)) {
                        CP_Customizer.hideRightSidebar();
                    } else {
                        CP_Customizer.openRightSidebar(sectionID);
                    }
                });
            }
        });
        var sectionID = 'page_content_section';
        $sectionsSidebarTogglers.click(function (event) {
            event.preventDefault();
            event.stopPropagation();


            if (CP_Customizer.isRightSidebarVisible(sectionID)) {
                CP_Customizer.hideRightSidebar();
                $sectionsSidebarTogglers.removeClass('active');
            } else {
                CP_Customizer.openRightSidebar(sectionID);
                $sectionsSidebarTogglers.addClass('active');
            }
        });

        CP_Customizer.focusContentSection = function (toFocus) {
            CP_Customizer.openRightSidebar("page_content_section", {
                focus: '[data-category=' + toFocus + ']'
            })
        };

        function getNextInArray(data, start, separator) {
            var increments = data.filter(function (item) {
                return item.replace(start, '').length;
            }).map(function (item) {
                return parseInt(item.replace(start + (separator || '--'), '')) || 0;
            });

            return increments.length ? Math.max.apply(Math, increments) + 1 : 1;
        }

        function getNextId(start) {
            var ids = CP_Customizer.preview.getContentNodes().filter(function (item) {
                return (item.tagName.toLowerCase() === 'div' && item.id.indexOf(start) === 0)
            }).map(function (item) {
                return item.id
            });
            return start + "-" + getNextInArray(ids, start, '-');
        }

        function getNextDataId(start) {
            var dataIds = CP_Customizer.preview.getContentNodes().filter(function (item) {
                return (item.tagName.toLowerCase() === 'div' && item.getAttribute('data-id') && (item.getAttribute('data-id') || "").indexOf(start) === 0)
            }).map(function (item) {
                return item.getAttribute('data-id')
            });

            return start + "--" + getNextInArray(dataIds, start);
        }


        CP_Customizer.preview.insertSectionFromData = function (data, skipEvent) {
            if (data) {
                var $content = $(data.content),
                    dataId = getNextDataId(data.category || data.elementId);

                $content.attr('id', getNextId(data.category || data.elementId));
                $content.attr('data-id', dataId);

                var index;

                if (data.prepend === true || parseInt(data.prepend) === 1) {
                    index = 0;
                }

                CP_Customizer.preview.insertContentSection($content, index);

                var sectionStyle = CP_Customizer.getSectionExports(data.id).sectionStyle;
                if (sectionStyle) {
                    _.each(sectionStyle, function (selectors, media) {
                        _.each(selectors, function (props, selector) {
                            CP_Customizer.contentStyle.setProps('[data-id="' + dataId + '"] ' + selector, "", props, media);
                        });
                    });
                }

                if (skipEvent !== true) {
                    CP_Customizer.trigger('UPDATE_SECTIONS_LIST');
                }
            }

            return {
                index: index,
                $content: $content
            };
        };

        var $sectionsList = $("#page_full_rows");

        $sectionsList.sortable({
            scroll: true,
            appendTo: "body",
            axis: 'y',
            handle: '.reorder-handler',
            start: function (event, ui) {
                ui.placeholder.width(ui.item[0].offsetWidth);
                ui.placeholder.height(ui.item[0].offsetHeight);
                startPosition = ui.item.index();
            },
            sort: function (event, ui) {
                ui.helper.css({
                    'left': '18px',
                    'position': 'fixed',
                    'top': event.clientY
                });

            },
            stop: function (event, ui) {
                var node = CP_Customizer.preview.getRootNode().children('[data-id="' + ui.item.data('id') + '"]');
                var nodes = CP_Customizer.preview.getRootNode().children('[data-id]').not(node);
                var newPos = ui.item.index();

                if (newPos < nodes.length) {
                    nodes.eq(newPos).before(node);
                } else {
                    nodes.last().after(node);
                }

                CP_Customizer.setContent();
            }
        });

        $sectionsList.on('click', '.full_row .item-remove', function (event) {

            event.preventDefault();
            var sectionID = $(this).parents('.item').data('id');
            var node = CP_Customizer.preview.getSectionByDataId(sectionID);
            var exportID = CP_Customizer.preview.getNodeExportId(node);
            var anchor = node.attr('id');

            $(this).parents('.item').fadeOut(200);
            CP_Customizer.hooks.doAction('before_section_remove', $(node));
            $(node).remove();
            $(this).parents('.item').remove();

            $('[data-type="row-list-control"] [data-name="page_content"] [data-id="' + exportID + '"]').removeClass('already-in-page');
            CP_Customizer.updateState(false, false);
            CP_Customizer.overlays.hideMovableOverlays();

            if (CP_Customizer.menu.anchorExistsInPrimaryMenu(anchor)) {
                CP_Customizer.menu.removeAnchorFromPrimaryMenu(anchor);
            }

        });


        var labelChange = _.debounce(function () {
            var $item = $(this).closest('.full_row');
            var node = CP_Customizer.preview.getSectionByDataId($item.data('id'));
            var oldValue = node.attr('data-label');
            var value = this.value.trim();

            if (value === oldValue) {
                return;
            }

            if (value.length === 0) {
                value = oldValue;
                this.value = oldValue;
            }

            node.attr('data-label', value);
            node.data('label', value);

            var slug = CP_Customizer.getSlug(value);

            if (!slug) {
                return;
            }

            if (CP_Customizer.preview.getRootNode().find('[id="' + slug + '"]').length) {
                var found = false,
                    index = 1;
                while (!found) {
                    if (CP_Customizer.preview.getRootNode().find('[id="' + slug + '-' + index + '"]').length === 0) {
                        slug += '-' + index;
                        found = true;
                    } else {
                        index++;
                    }
                }
            }
            var oldId = node.attr('id');
            node.attr('id', slug);
            $(this).siblings('.anchor-info').text('#' + slug);

            if (CP_Customizer.menu.anchorExistsInPrimaryMenu(oldId)) {
                CP_Customizer.menu.updatePrimaryMenuAnchor(oldId, {
                    anchor: slug,
                    title: value
                });
            }

            CP_Customizer.setContent();
        }, 500);


        $sectionsList.on('keyup', '.full_row input', labelChange);

        $sectionsList.on('dblclick', '.anchor-info', function () {
            this.contentEditable = true;
        });

        $sectionsList.on('keypress', '.anchor-info', function (event) {

            if (event.which === 13) {
                event.preventDefault();
                event.stopPropagation();
                this.contentEditable = false;
            }

        });

        $sectionsList.on('focusout', '.anchor-info', function () {
            var slug = $(this).text();
            slug = CP_Customizer.getSlug(slug);
            $(this).text('#' + slug);

            var $item = $(this).closest('.full_row');
            var node = CP_Customizer.preview.getSectionByDataId($item.data('id'));
            var oldId = node.attr('id');
            node.attr('id', slug);
            node.attr('id', slug);
            if (CP_Customizer.menu.anchorExistsInPrimaryMenu(oldId)) {
                CP_Customizer.menu.updatePrimaryMenuAnchor(oldId, {
                    anchor: "#" + slug,
                    title: $item.find('input.item-label').val()
                });
            }

            this.contentEditable = false;
            CP_Customizer.setContent();
        });


        $sectionsList.on('click', '.full_row .item-menu-visible', function (event) {
            event.stopPropagation();
            event.preventDefault();
            event.stopImmediatePropagation();

            var $item = $(this).closest('.full_row'),
                $node = CP_Customizer.preview.getSectionByDataId($item.data('id'));

            if (false === CP_Customizer.menu.getPrimaryMenuID()) {
                CP_Customizer.menu.createPrimaryMenu();
            }

            var anchor = $node.attr('id');
            var label = $node.attr('data-label');

            if (CP_Customizer.menu.anchorExistsInPrimaryMenu(anchor)) {
                CP_Customizer.menu.removeAnchorFromPrimaryMenu(anchor);
                $(this).removeClass('active');
            } else {
                CP_Customizer.menu.addAnchorToPrimaryMenu(anchor, label);
                $(this).addClass('active');
            }
        });

        function focusSection(item) {

            var section = CP_Customizer.preview.getSectionByDataId($(item).data('id'));

            CP_Customizer.preview.find('html,body').animate({
                scrollTop: section.offset().top
            }, 500);

            $(item).addClass('focused').siblings().removeClass('focused');

            CP_Customizer.trigger('section_focused', section);
        }

        $sectionsList.on('click', '.full_row .item-settings', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var customizerSection = $(this).attr('data-setting');

            var section = CP_Customizer.preview.getSectionByDataId($(this).closest('.full_row').data('id'));

            if (CP_Customizer.isRightSidebarVisible(customizerSection)) {
                CP_Customizer.hideRightSidebar();
            }

            CP_Customizer.openRightSidebar(customizerSection, {
                floating: CP_Customizer.hooks.applyFilters('content_section_setting_float', true),
                y: $(this).offset().top,
                section: section
            });

            focusSection($(this).closest('.full_row'));

        });


        $sectionsList.on('click', '.full_row', function () {

            var section = CP_Customizer.preview.getSectionByDataId($(this).data('id'));

            if (!section.length) {
                return;
            }

            focusSection($(this));

            CP_Customizer.hideRightSidebar();


        });

        var skipableKeyCodes = [8, 46, 16, 17, 18];
        var labelValidaton = function (event) {

            if (skipableKeyCodes.indexOf(event.keyCode) === -1 && event.key.length === 1) {
                if (!event.key.match(/[A-Za-z0-9\s]/)) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            }
        };
        $sectionsList.on('keydown', '.full_row input', labelValidaton);

        function getListModel(elem) {
            var $node = $(elem),
                label = $node.attr('data-label') || $node.attr('id'),
                id = $node.attr('id') || "",
                sectionID = $node.attr('data-id'),
                exportID = $node.attr('data-export-id'),
                inMenu = CP_Customizer.menu.anchorExistsInPrimaryMenu(id),
                setting = $node.attr('data-setting') ? $node.attr('data-setting') : false;
            setting = CP_Customizer.hooks.applyFilters('content_section_setting', setting);

            return {
                label: label,
                id: id,
                setting: setting,
                sectionID: sectionID,
                exportID: exportID,
                inMenu: inMenu
            };
        }

        var openedSectionsCategories = {};

        function updateSectionsList() {
            var data = CP_Customizer.preview.getRootNode().children().map(function (index, elem) {
                return getListModel(elem);
            });

            $sectionsList.children('.item.full_row').remove();

            var availableRowsList = $('[data-type="row-list-control"] [data-name="page_content"]');
            var $controlItems = availableRowsList.find('li.available-item');

            var allowMultiple = (availableRowsList.closest('[data-selection="multiple"]').length > 0);


            function openCategory($category) {
                var $this = $($category);

                var $items = $this.data('items');

                if (!$items || $items.length === 0) {
                    $items = $this.nextUntil('li.category-title', 'li.available-item');
                    $this.data('items', $items);
                }

                $items.fadeIn(50);
                $this.attr('data-item-open', 1);
                $this.removeAttr('data-item-closed');
                openedSectionsCategories[$this.attr('data-category')] = true;
            }

            function closeCategory($category) {
                var $this = $($category);

                var $items = $this.data('items');

                if (!$items || $items.length === 0) {
                    $items = $this.nextUntil('li.category-title', 'li.available-item');
                    $this.data('items', $items);
                }

                $items.fadeOut(50);
                $this.removeAttr('data-item-open');
                $this.attr('data-item-closed', 1);
                openedSectionsCategories[$this.attr('data-category')] = false;
            }


            var $categoryTitles = availableRowsList.find('li.category-title');
            $categoryTitles.addClass('closeable');
            $categoryTitles.each(function (index) {
                    if (!$(this).attr('data-item-closed')) {
                        $(this).attr('data-item-open', 1);
                    }

                    if (index > 1 && !openedSectionsCategories[$(this).attr('data-category')]) {
                        closeCategory($(this));
                    }
                }
            );

            $categoryTitles.off('click').on('click', function () {


                if ($(this).attr('data-item-open')) {
                    closeCategory($(this));
                } else {
                    openCategory($(this));
                }
            });


            data.each(function (index, _data) {

                // ignore elements injected by plugins and that do not match the companion structure
                if (!_data.exportID) {
                    return;
                }

                $sectionsList.children('.empty').before(listItemTemplate(_data));


                if (allowMultiple && !data.once) {
                    return;
                }

                $controlItems.filter('[data-id="' + _data.exportID + '"]').addClass('already-in-page');
            });


            availableRowsList.parent().off('cp.item.click').on('cp.item.click', function (event, itemID, enabled) {
                var $ = CP_Customizer.preview.jQuery();
                var data = CP_Customizer.options('data:sections', {})[itemID];

                if (data['pro-only']) {

                    CP_Customizer.popUpInfo(window.CP_Customizer.translateCompanionString('This item requires PRO theme'),
                        '<div class="pro-popup-preview-container">' +
                        '   <img class="pro-popup-preview-image" src="' + data.preview + '">' +
                        '   <h3>' + window.CP_Customizer.translateCompanionString('This item is available only in the PRO version') + '</h3>' +
                        '   <p>' + window.CP_Customizer.translateCompanionString('Please upgrade to the PRO version to use this item and many others.') + '</p>' +
                        '   <br/>' +
                        '   <a href="' + window.mesmerize_customize_settings.upgrade_url + '" class="button button-orange" target="_blank">' +
                        window.CP_Customizer.translateCompanionString('Upgrade to PRO') + '</a> ' +
                        '</div>'
                    );

                    return;
                }

                var response = CP_Customizer.preview.insertSectionFromData(data, true);

                var _data = getListModel(response.$content);

                var $listChildren = $sectionsList.children().not('.empty')
                if (!_.isUndefined(response.index) && $listChildren.length) {
                    $listChildren.eq(response.index).before(listItemTemplate(_data));
                } else { 
                    $sectionsList.children('.empty').before(listItemTemplate(_data));
                }
            });
        }

        CP_Customizer.bind('PREVIEW_LOADED', updateSectionsList);
        CP_Customizer.bind('UPDATE_SECTIONS_LIST', updateSectionsList);

        CP_Customizer.bind(CP_Customizer.events.RIGHT_SECTION_CLOSED, function (ev, sidebar) {
            $contentLi.find('.cp-add-section.active').removeClass('active');
        });

        CP_Customizer.bind('content.section.hovered', function (event, $el) {
            var sectionId = $el.attr('data-id');
            var $item = $sectionsList.find('[data-id="' + sectionId + '"]');
            $item.addClass('focused').siblings().removeClass('focused');

            if (!$item.length) {
                return;
            }

            $item[0].scrollIntoViewIfNeeded();
        });

        function maybeLinkableItem(node, selector) {
            return (node.is(selector) || node.is('a') && node.children(selector).length && node.children().length === 1);
        }

        function maybeLinkableImage(node) {
            if (!node.closest(CP_Customizer.preview.getRootNode()).length) {
                return false;
            }


            var linkableImg = maybeLinkableItem(node, 'img'),
                linkableIcon = maybeLinkableItem(node, 'i.fa');

            return (linkableImg || linkableIcon);
        }

        // TODO: Needs refactoring. This should be a filter not a direct function
        function imageGroupAnchorClass(node) {
            var additional_class = '';

            if (node.parent().hasClass('image-group-bottom-3')) {
                if (node.hasClass('left-img')) {
                    additional_class = 'left-img-anchor';
                }
                if (node.hasClass('center-img')) {
                    additional_class = 'center-img-anchor';
                }
                if (node.hasClass('right-img')) {
                    additional_class = 'right-img-anchor';
                }
            }
            if (node.parent().hasClass('image-group-2-img')) {
                if (node.hasClass('img-1')) {
                    additional_class = 'img-1-anchor';
                }
                if (node.hasClass('img-2')) {
                    additional_class = 'img-2-anchor';
                }
            }
            if (node.parent().hasClass('image-group-bottom-3-img')) {
                if (node.hasClass('left-img')) {
                    additional_class = 'left-img-anchor';
                }
                if (node.hasClass('center-img')) {
                    additional_class = 'center-img-anchor';
                }
                if (node.hasClass('right-img')) {
                    additional_class = 'right-img-anchor';
                }
            }

            return (additional_class ? 'class="' + additional_class + '"' : '');
        }


        function setToolbarLinkButton(toolbar, node) {
            var _class, _title;

            if (node.is('a')) {
                _class = 'fa-chain-broken';
                _title = 'Remove Image Link';
            } else {
                _class = 'fa-link';
                _title = 'Add Image Link';
            }

            toolbar.addToolbarItem({
                'name': 'image-link',
                'icon': _class,
                'title': _title,
                'onClick': function () {
                    if (!node.is('a')) {
                        node.wrap('<a data-cp-link="1" href="#" ' + imageGroupAnchorClass(node) + '></a>');
                        node = node.parent();
                        this.setIcon('fa-chain-broken');
                        setTimeout(function () {
                            node.trigger('click');
                        }, 0);
                    } else {
                        node = node.children();
                        node.unwrap();
                        this.setIcon('fa-link');
                    }

                    CP_Customizer.preview.markNode(node);
                    CP_Customizer.preview.decorateElements(node);
                    CP_Customizer.updateState()
                }
            });
        }

        CP_Customizer.hooks.addAction('node_hover_overlay_updated', function (node, overlay, toolbar) {
            if (maybeLinkableImage(node)) {
                setToolbarLinkButton(toolbar, node);
            }
        });

    });
})(jQuery, window);
