(function (root, CP_Customizer, $) {

    CP_Customizer.addModule(function (CP_Customizer) {
        _.extend(CP_Customizer, {
            menu: {

                getGlobalOption: function(name, defaultValue){
                    if (cpCustomizerGlobal && cpCustomizerGlobal.pluginOptions && cpCustomizerGlobal.pluginOptions.hasOwnProperty(name)) {
                        return cpCustomizerGlobal.pluginOptions[name];
                    }

                    return defaultValue;
                },

                getPrimaryLocationModName: function() {
                    var primaryLocation = this.getGlobalOption("primaryMenuLocation", "primary");
                    return 'nav_menu_locations[' + primaryLocation + ']';
                },

                getHomeUrl: function() {
                    var primaryMenuHomeUrl = this.getGlobalOption("homeUrl", CP_Customizer.preview.data().siteURL);
                    return primaryMenuHomeUrl;
                },

                canSetPrimaryLocation: function() {
                    var canSetPrimaryLocation = this.getGlobalOption("canSetPrimaryLocation", true);
                    return canSetPrimaryLocation;
                },

                getPrimaryLocationDefaultLanguageMenu: function() {
                    var primaryLocationDefaultLanguageMenu = this.getGlobalOption("primaryLocationDefaultLanguageMenu", -1);
                    return primaryLocationDefaultLanguageMenu;
                },

                createPrimaryMenu: function () {
                    var api = root.wp.customize;
                    var customizeId,
                        name = name || root.CP_Customizer.translateCompanionString("Main Menu"),
                        placeholderId = api.Menus.generatePlaceholderAutoIncrementId();

                    customizeId = 'nav_menu[' + String(placeholderId) + ']';

                    api.create(customizeId, customizeId, {}, {
                        type: 'nav_menu',
                        transport: api.Menus.data.settingTransport,
                        previewer: api.previewer
                    });

                    api(customizeId).set($.extend({},
                        api.Menus.data.defaultSettingValues.nav_menu, {
                            name: name
                        }
                    ));


                    menuSection = new api.Menus.MenuSection(customizeId, {
                        params: {
                            id: customizeId,
                            panel: 'nav_menus',
                            title: name,
                            customizeAction: api.Menus.data.l10n.customizingMenus,
                            type: 'nav_menu',
                            priority: 10,
                            menu_id: placeholderId
                        }
                    });
                    api.section.add(customizeId, menuSection);

                    if (this.canSetPrimaryLocation()) {
                        // set location
                        api(this.getPrimaryLocationModName()).set(placeholderId);
                    }

                    var defaultMenu = this.getPrimaryLocationDefaultLanguageMenu();

                    if (defaultMenu !== -1) {
                        wp.customize.bind("save-request-params", function (query) {
                            query.icl_translation_of = defaultMenu;
                            return query;
                        });
                    }


                    // create home page menu item;

                    this.addAnchorToPrimaryMenu('#page-top', 'Home');
                },


                getPrimaryMenuID: function () {
                    var menuId = wp.customize(this.getPrimaryLocationModName()).get();
                    if (wp.customize('nav_menu[' + menuId + ']')) {
                        return menuId;
                    } else {
                        return false;
                    }
                },

                getPrimaryMenu: function () {
                    var menuId = CP_Customizer.menu.getPrimaryMenuID();
                    if (menuId !== false) {
                        return wp.customize('nav_menu[' + menuId + ']').get();
                    } else {
                        return false;
                    }
                },

                focusPrimaryMenuCustomize: function () {
                    var menuId = CP_Customizer.menu.getPrimaryMenuID();
                    if (menuId !== false) {
                        wp.customize.section('nav_menu[' + menuId + ']').focus();
                        return true;
                    }

                    return false;
                },

                getPrimaryMenuControl: function () {
                    var api = root.wp.customize;
                    var menuId = CP_Customizer.menu.getPrimaryMenuID();

                    if (menuId) {
                        return api.Menus.getMenuControl(menuId);
                    } else {
                        return false;
                    }
                },

                addAnchorToPrimaryMenu: function (anchor, title) {
                    var api = root.wp.customize;
                    var customizeId, placeholderId, settingArgs, setting, menuItemControl, menuId, menuControl, position = 0,
                        priority = 10;

                    menuId = CP_Customizer.menu.getPrimaryMenuID();
                    menuControl = CP_Customizer.menu.getPrimaryMenuControl();

                    if (!menuId || !menuControl) {
                        return;
                    }

                    _.each(menuControl.getMenuItemControls(), function (control) {
                        if (false === control.setting()) {
                            return;
                        }
                        priority = Math.max(priority, control.priority());
                        if (0 === control.setting().menu_item_parent) {
                            position = Math.max(position, control.setting().position);
                        }
                    });
                    position += 1;
                    priority += 1;

                    var url = CP_Customizer.menu.getHomeUrl();
                    if (anchor.replace(/#/, '').length) {
                        url = CP_Customizer.preview.data().pageURL + "#" + anchor.replace(/#/, '');
                    }

                    var item = $.extend({},
                        api.Menus.data.defaultSettingValues.nav_menu_item, {
                            'title': title,
                            'url': url,
                            'type': 'custom',
                            'type_label': api.Menus.data.l10n.custom_label,
                            'object': 'custom'
                        }, {
                            nav_menu_term_id: menuId,
                            original_title: title,
                            position: position
                        });


                    placeholderId = api.Menus.generatePlaceholderAutoIncrementId();
                    customizeId = 'nav_menu_item[' + String(placeholderId) + ']';
                    settingArgs = {
                        type: 'nav_menu_item',
                        transport: api.Menus.data.settingTransport,
                        previewer: api.previewer
                    };

                    setting = api.create(customizeId, customizeId, {}, settingArgs);
                    setting.set(item);

                    menuItemControl = new api.controlConstructor.nav_menu_item(customizeId, {
                        params: {
                            type: 'nav_menu_item',
                            content: '<li id="customize-control-nav_menu_item-' + String(placeholderId) + '" class="customize-control customize-control-nav_menu_item"></li>',
                            section: menuControl.id,
                            priority: priority,
                            active: true,
                            settings: {
                                'default': customizeId
                            },
                            menu_item_id: placeholderId
                        },
                        previewer: api.previewer
                    });

                    api.control.add(customizeId, menuItemControl);
                    menuControl.debouncedReflowMenuItems();



                },

                anchorExistsInPrimaryMenu: function (anchor) {
                    var menuControl = CP_Customizer.menu.getPrimaryMenuControl();

                    if (!menuControl) {
                        return false;
                    }

                    anchor = anchor.replace(/#/, '');

                    var itemsControl = menuControl.getMenuItemControls();
                    for (var i = 0; i < itemsControl.length; i++) {
                        var itemControl = itemsControl[i];
                        var url = (itemControl.setting.get().url || "").split("#").pop().trim();
                        if (url === anchor) {
                            return true;
                        }
                    }

                    return false;
                },


                updatePrimaryMenuAnchor: function (oldAnchor, options) {
                    var menuControl = CP_Customizer.menu.getPrimaryMenuControl();

                    if (!menuControl) {
                        return false;
                    }

                    oldAnchor = oldAnchor.replace(/#/, '');
                    var newAnchor = options.anchor.replace(/#/, '');
                    var title = options.title;

                    var itemsControl = menuControl.getMenuItemControls();
                    for (var i = 0; i < itemsControl.length; i++) {
                        var itemControl = itemsControl[i];
                        var url = (itemControl.setting.get().url || "").split("#").pop().trim();
                        if (url === oldAnchor) {
                            var itemData = itemControl.setting();
                            if (itemData) {
                                itemData = _.clone(itemData);
                                itemData.title = title;
                                itemData.url = CP_Customizer.preview.data().pageURL + "#" + newAnchor;
                                itemControl.setting.set(itemData);
                                return true;
                            }
                        }
                    }

                    return false;
                },

                removeAnchorFromPrimaryMenu: function (anchor) {
                    var api = root.wp.customize;
                    var menuControl = CP_Customizer.menu.getPrimaryMenuControl();

                    if (!menuControl) {
                        return false;
                    }

                    anchor = anchor.replace(/#/, '');

                    var itemsControl = menuControl.getMenuItemControls();
                    for (var i = 0; i < itemsControl.length; i++) {
                        var itemControl = itemsControl[i];
                        var url = (itemControl.setting.get().url || "").split("#").pop();
                        if (url === anchor) {
                            itemControl.setting.set(false);
                            api.control.remove(itemControl.id);
                            return true;
                        }
                    }

                    return false;
                }
            }
        });
    });

})(window, CP_Customizer, jQuery);
