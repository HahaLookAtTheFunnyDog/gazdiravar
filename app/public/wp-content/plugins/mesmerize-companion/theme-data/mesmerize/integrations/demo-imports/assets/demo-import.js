(function ($, __ocdiPreparePluginData) {


    $('.ocdi-installer .js-ocdi-nav-link').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        var $item = $(this),
            category = $item.attr('href').replace('#', '').trim(),
            $demos = $(".ocdi__gl-item.js-ocdi-gl-item");


        $item.parent().addClass('active').siblings().removeClass('active');

        if (category === "all") {
            $demos.show();
            return;
        }

        $demos.each(function () {
            var $demo = $(this),
                categories = ($demo.attr('data-categories') || "").split(",").map(function (item) {
                    return item.trim();
                });

            if (categories.indexOf(category) !== -1) {
                $demo.show();
            } else {
                $demo.hide();
            }

        });
    });

    function showPopUp(title, elementID, data) {
        var selector = "#TB_inline?inlineId=" + elementID;
        var query = [];

        $.each(data || {}, function (key, value) {
            query.push(key + "=" + value);
        });

        selector = query.length ? selector + "&" : selector + "";
        selector += query.join("&");

        tb_show(title, selector);
    }

    top.cp = {
        installPlugin: function (slug, successCallback, errorCallback) {
            wp.updates.ajax('install-plugin', {
                slug: slug,
                success: successCallback,
                error: errorCallback
            })
        },

        activatePlugin: function (activeUrl, successCallback, errorCallback) {

        }
    };

    var currentDemo = "";


    function installSuccessful(response) {
        response.debug = response.debug || [];
        jQuery("#tb_install_ocdi_response").append(response.debug.map(function (item) {
            return "<p>" + item + "</p>"
        }).join(""));
        __ocdiPreparePluginData.activate_url = response.activateUrl;
        activateOCDI();
    }

    function installFailed(response) {

        jQuery("#tb_install_ocdi_response").append(response.debug.map(function (item) {
            return "<p>" + item + "</p>"
        }).join(""));
        jQuery("#tb_install_ocdi_response").append("<p>Instalation failed. Please try to install and activate the plugin manually</p>");
    }

    function activateOCDI() {
        showPopUp('Installing One Click Demo Import Plugin', 'tb_install_ocdi');
        jQuery("#tb_install_ocdi_response").append("<h2>Activate 'One Click Demo Import' plugin</h2>");
        jQuery.ajax({
            url: window.__ocdiPreparePluginData.activate_url,
            method: 'GET',
            success: function () {
                window.location.hash = "#" + currentDemo;
                jQuery("#tb_install_ocdi_response").append("<p>Activation succesfull</p>");
                jQuery("#tb_install_ocdi_response").append("<p><strong>If the page does not refresh automaticly please click <a href='" + window.location + "'>here</a> </strong></p>");
                setTimeout(function () {
                    window.location.reload(true);
                }, 800);
            },
            error: function () {
                jQuery("#tb_install_ocdi_response").append("<p>Activation failed. Please try to activate the plugin manually</p>");
            }
        })
    }

    if (window.__ocdiPreparePluginData) {
        $('.js-ocdi-gl-import-data').click(function () {

            currentDemo = "demo-" + this.value;
            if (!__ocdiPreparePluginData.status.installed) {
                jQuery("#tb_install_ocdi_response").empty();
                showPopUp('Installing One Click Demo Import Plugin', 'tb_install_ocdi')
                top.cp.installPlugin(window.__ocdiPreparePluginData.slug, installSuccessful, installFailed);
            } else {
                if (!__ocdiPreparePluginData.status.active) {
                    activateOCDI();
                }
            }
        })
    }
})(jQuery, window.__ocdiPreparePluginData);


(function ($) {

    function installPlugin(slug, successCallback, errorCallback) {
        $(document).trigger('extendthemes-plugin-status-update', [slug, 'install']);
        wp.updates.ajax('install-plugin', {
            slug: slug,
            success: successCallback,
            error: errorCallback || function () {
            }
        })
    }


    function activatePlugin(plugin, successCallback, errorCallback, alwaysCallback) {
        $(document).trigger('extendthemes-plugin-status-update', [plugin.slug, 'activate']);
        $.get(plugin.activate_link)
            .done(successCallback || function () {
            })
            .fail(errorCallback || function () {

            })
            .always(function () {

                if (alwaysCallback) {
                    alwaysCallback.apply(this, arguments);
                }
            })
    }

    function installPlugins(plugins, callback) {
        var installedPlugins = 0;

        function pluginInstalled(slug) {
            $(document).trigger('extendthemes-plugin-status-update', [slug, 'ready']);
            installedPlugins++;

            if (plugins.length === installedPlugins) {
                if (callback) {
                    callback();
                }
            }
        }

        if (!plugins.length) {
            if (callback) {
                callback();
            }
        }

        plugins.forEach(function (plugin) {

            if (plugin.status === 'not-installed') {

                installPlugin(plugin.slug, function () {
                    plugin.activate_link = arguments[0].activateUrl;
                    activatePlugin(plugin, null, null, function () {

                        pluginInstalled(plugin.slug);
                    });
                }, function () {
                    pluginInstalled(plugin.slug);
                });
            }

            if (plugin.status === 'installed') {
                activatePlugin(plugin, null, null, function () {
                    pluginInstalled(plugin.slug);
                });
            }

            if (plugin.status === "active") {
                pluginInstalled(plugin.slug);
            }
        });

    }

    window.ExtendThemesDemoImporter = {

        getData: function (index) {
            return window.ocdi ? window.ocdi.import_files[index] : false;
        },

        openPopUp: function (title, content, callback) {
            tb_show(title || "Import Demo", '#TB_inline');
            var $tbWindow = $("#TB_window"),
                $tbContent = $("#TB_ajaxContent");
            $tbWindow.addClass('extendthemes-import-popup');
            $tbWindow.removeAttr('style');
            $tbContent.removeAttr('style').addClass('extendthemes-import-popup-content');
            $tbContent.append(content);

            $tbWindow.find('[data-name="import-data"]').click(function () {

                if ($(this).hasClass('disabled')) {
                    return;
                }

                $(this).prop('disabled', true).addClass('disabled');
                if (callback) {
                    callback($(this).attr('data-id'), $tbWindow)
                }
            })
        },


        importDemo: function (index, reqPlugins, $tbWindow) {

            var demo = ExtendThemesDemoImporter.getData(index);

            var pluginsToInstall = [];

            if (demo && demo.plugins) {
                for (var path in demo.plugins) {
                    if (!demo.plugins.hasOwnProperty(path)) {
                        continue;
                    }

                    var plugin = _.clone(demo.plugins[path]);

                    if (reqPlugins.indexOf(plugin.slug) !== -1) {
                        plugin.path = path;
                        pluginsToInstall.push(plugin);
                    }
                }
            }


            installPlugins(pluginsToInstall, function () {
                tb_remove();
                jQuery('[data-name="install-now"][value="' + index + '"]').trigger('click');
                jQuery('[onclick][value="' + index + '"]').hide();
                jQuery('.mesmerize-import-demo-sites').addClass('left').text(ocdi.texts.importing_title);
            }, $tbWindow);

        },

        checkInstallStatus: function (callback) {
            $(document).on('extendthemes-plugin-status-update', function (event, slug, status) {
                callback(slug, status);
            });
        },

        getPopupContent: function (index, popup) {
            var importData = this.getData(index),
                template = wp.template(popup || 'extendthemes-import-popup');

            return template({
                'name': importData.import_file_name || "",
                'preview_image': importData.import_preview_image_url || "",
                'preview_url': importData.preview_url,
                'plugins': _.toArray(importData.plugins || []),
                'id': index
            });
        },

        showImportPopup: function (index) {
            var importData = this.getData(index),
                content = this.getPopupContent(index),
                callback = function (index, $tbWindow) {
                    $(document).off('extendthemes-plugin-status-update');
                    var plugins = $tbWindow.find('.plugins-list input[type=checkbox]:checked').map(function () {
                        return jQuery(this).attr('data-slug');
                    }).toArray();

                    ExtendThemesDemoImporter.checkInstallStatus(function (slug, status) {

                        $tbWindow.find('[data-name="import-data"]').text(ocdi.texts.installing_plugins);
                        var $status = $tbWindow.find('.plugin-status[data-slug="' + slug + '"]');
                        if (status === "install") {
                            $status.attr('class', 'plugin-status installed blink');
                            $status.find('span').text(ocdi.texts.installing);
                        }

                        if (status === "activate") {
                            $status.attr('class', 'plugin-status active blink');
                            $status.find('span').text(ocdi.texts.activating);
                        }

                        if (status === "ready") {
                            $status.removeAttr('class').attr('class', 'plugin-status active');
                            $status.find('span').text(ocdi.texts.active);
                        }
                    });
                    ExtendThemesDemoImporter.importDemo(index, plugins, $tbWindow);
                };

            this.openPopUp('Import Demo', content, callback);
        },

        showImportPopupProOnly: function (index) {
            var content = this.getPopupContent(index, 'extendthemes-import-popup-pro'),
                callback = function () {

                };

            this.openPopUp('Import Demo', content, callback);
        }
    }

    jQuery(document).on('ocdiImportComplete', function () {
        jQuery('.extendthemes-after-import-controls').show();
    });
})(jQuery);


(function ($) {
    var hash = (window.location.hash || "").replace("#", "").trim();
    setTimeout(function () {
        history.replaceState({}, "", " ");
    }, 100);

    if (!hash) {
        if (window.location.search.match(/demo=([0-9]+)/)) {
            hash = window.location.search.match(/demo=([0-9]+)/).pop();
        }
    }

    $(window).load(function () {
        if (hash) {

            var valueID = hash.replace('demo-', '');

            if (window.ocdi_not_installed) {
                jQuery('.ocdi__gl-item-button[value="' + valueID + '"]').trigger('click');
                return;
            }

            var importData = ExtendThemesDemoImporter.getData(valueID);
            if (importData.pro_only === true) {
                ExtendThemesDemoImporter.showImportPopupProOnly(valueID);
            } else {
                ExtendThemesDemoImporter.showImportPopup(valueID);
            }

        }
    });

})(jQuery);
