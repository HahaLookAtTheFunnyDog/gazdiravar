(function (root, CP_Customizer, $) {

    var currentPageStyles = {};
    var styleEL = $("");
    var mod = null;

    var mediaMap = {
        "mobile": "@media screen and (max-width:767)",
        "tablet": "@media screen and (min-width:768)",
        "desktop": "@media screen and (min-width:1024)",
        "nomedia": false
    };

    var outputOrder = ['nomedia', 'mobile', 'tablet', 'desktop'];

    function sprintf_style_array(data, media) {
        var style = "";


        for (var selector in data) {
            var props = data[selector];
            var propsText = "";

            for (var prop in props) {
                var value = props[prop];
                propsText += "\t" + prop + ":" + value + ";\n";
            }

            style += selector + "{\n" + propsText + "\n}";
        }
        if (media) {
            style = media + "{\n" + style + "\n}";
        }

        return style + "\n\n";
    }

    function setModAndUpdate() {
        CP_Customizer.setMod(mod, _.clone(currentPageStyles), 'postMessage');

        var style = "";

        for (var i = 0; i < outputOrder.length; i++) {
            var media = outputOrder[i];
            var mediaQuery = mediaMap[media];
            style += sprintf_style_array(currentPageStyles[media], mediaQuery);
        }
        styleEL.text(style);
    }


    CP_Customizer.on(CP_Customizer.events.PREVIEW_LOADED, function () {
        currentPageStyles = CP_Customizer.preview.data('content_style');
        mod = 'custom_content_style_' + CP_Customizer.preview.data('pageID');
        styleEL = CP_Customizer.preview.find('#page-content-custom-styles');
    });


    CP_Customizer.contentStyle = {
        getStyle: function (selector, media) {

            if (!selector) {
                return {};
            }

            media = media || 'nomedia';
            return currentPageStyles[media][selector] || {};
        },

        removeSelector: function (selector, media, noUpdate) {
            media = media || 'nomedia';

            if (media === "all") {
                for (var m in currentPageStyles) {
                    this.removeSelector(selector, m, true);
                }
                setModAndUpdate();
                return;
            }

            if (currentPageStyles[media]) {
                if (currentPageStyles[media][selector]) {
                    delete currentPageStyles[media][selector];
                } else {
                    for (var s in currentPageStyles[media]) {
                        if (s.match(selector)) {
                            delete currentPageStyles[media][s];
                        }
                    }
                }
            }

            if (!noUpdate) {

                setModAndUpdate();
            }

        },


        getProp: function (selector, pseudo, prop, defaultValue, media) {
            pseudo = (pseudo || "").trim();
            selector = selector + pseudo;

            var style = this.getStyle(selector, media);
            return (style[prop] || "").toLowerCase().replace('!important', '').trim() || defaultValue;

        },

        getNodeProp: function (node, selector, pseudo, prop, media) {

            if (!node || node.length === 0) {
                return '';
            }

            node = CP_Customizer.preview.jQuery(node)[0];
            var defaultValue = CP_Customizer.preview.frame().getComputedStyle(node, pseudo).getPropertyValue(prop);
            return this.getProp(selector, pseudo, prop, defaultValue, media)
        },


        getNodeProps: function (node, selector, pseudo, props, media) {
            var result = {},
                manager = this;

            _.each(props, function (prop) {
                result[prop] = manager.getNodeProp(node, selector, pseudo, prop, media);
            });

            return result;
        },

        isImportant: function (selector, pseudo, prop, media) {
            pseudo = (pseudo || "").trim();
            selector = selector + pseudo;

            var style = this.getStyle(selector, media);
            var value = style[prop] || "";
            return value.toLowerCase().indexOf('!important') !== -1;

        },

        setProp: function (selector, pseudo, prop, value, media, handeledModUpdate) {
            media = media || 'nomedia';
            pseudo = (pseudo || "").trim();

            pseudo = (pseudo || "").trim();
            selector = selector + pseudo;

            if (!currentPageStyles[media]) {
                currentPageStyles[media] = {};
            }

            if (_.isArray(currentPageStyles[media])) {
                currentPageStyles[media] = _.extend({}, currentPageStyles[media]);
            }

            if (!currentPageStyles[media][selector]) {
                currentPageStyles[media][selector] = {};
            }

            currentPageStyles[media][selector][prop] = value;

            if (!handeledModUpdate) {
                setModAndUpdate();
            }
        },

        removeProp: function (selector, pseudo, prop, media) {
            media = media || 'nomedia';

            pseudo = (pseudo || "").trim();
            selector = selector + pseudo;

            if (currentPageStyles[media]) {
                if (currentPageStyles[media][selector]) {
                    if (currentPageStyles[media][selector][prop]) {
                        delete currentPageStyles[media][selector][prop];
                    }
                }
            }


            setModAndUpdate();
        },

        setProps: function (selector, pseudo, props, media) {
            for (var prop in props) {
                this.setProp(selector, pseudo, prop, props[prop], media, true);
            }

            setModAndUpdate();
        },

        merge: function (styles) {
            currentPageStyles = jQuery.extend(currentPageStyles, styles);
            setModAndUpdate();
        },

        recompileScssStyle: function () {
            return;
            var settings = CP_Customizer.options('scss_settings', {});
            var vars = {};

            for (var i = 0; i < settings.length; i++) {
                var setting = settings[i];
                var setting_vars = CP_Customizer.hooks.applyFilters('scss_setting_vars_' + setting, {});

                if (_.isEmpty(setting_vars)) {
                    setting_vars = CP_Customizer.hooks.applyFilters('scss_setting_vars', {}, setting);
                }

                vars = _.extend(_.clone(vars), _.clone(setting_vars));
            }

            var data = CP_Customizer.preview.data('scss', {});

            vars = _.extend(_.clone(data.vars), _.clone(vars));
            data.vars = vars;

            var request = CP_Customizer.IO.post('compiled_style_preview', {
                data: data
            });

            request.done(function (content) {
                CP_Customizer.preview.find('#ope-compiled-css').text(content);
            });

        },

        setSectionItemColor: function (node, colorMapping, newColor) {

            var $node = CP_Customizer.preview.jQuery(node),
                colorName = CP_Customizer.getThemeColor(newColor),
                colorClasses = Object.getOwnPropertyNames(CP_Customizer.getColorsObj()).concat(['color-white', 'color-black']);

            colorMapping.forEach(function (cMap) {
                if (colorName) {
                    if (cMap.colorClass) {
                        var oldColorClasses = colorClasses.map(function (color) {
                            return cMap.colorClass.prefix + color + cMap.colorClass.suffix;
                        });

                        var newClass = cMap.colorClass.prefix + colorName + cMap.colorClass.suffix;
                        var $item = $node.is(cMap.colorClass.selector || cMap.selector) ? $node : $node.find(cMap.colorClass.selector || cMap.selector);
                        $item.removeClass(oldColorClasses.join(' ')).addClass(newClass);
                    }
                }
            });

        },


        getSectionItemColor: function (node, colorMapping, defaultColor) {

            if (!colorMapping) {
                return 'rgba(0,0,0,0)';
            }

            var $node = CP_Customizer.preview.jQuery(node),
                firstCMap = colorMapping[0],
                selector = colorMapping[0].selector,
                colorClasses = Object.getOwnPropertyNames(CP_Customizer.getColorsObj()).concat(['color-white', 'color-black']),
                result = defaultColor || 'rgba(0,0,0,0)';

            // this class should not be here, they are fixing sections issues
            colorClasses = colorClasses.concat(['bg-orange']);
            if ($node.is('i.fa')) {
                colorClasses = colorClasses.concat(['gray']);
            }

            if (firstCMap.colorClass) {
                selector = firstCMap.colorClass.selector || selector;
                var $item = $node.is(selector) ? $node : $node.find(selector);

                for (var i = 0; i < colorClasses.length; i++) {
                    var colorClass = colorClasses[i],
                        nodeClass = firstCMap.colorClass.prefix + colorClass + firstCMap.colorClass.suffix;
                    if ($item.hasClass(nodeClass)) {
                        result = CP_Customizer.getColorValue(colorClass);
                        break;
                    }
                }
            }

            return result;

        }
    };


    CP_Customizer.styleUtils = {
        generateCSSLinerGradient: function (angle, colors) {
            var colorsArray = colors.map(function (color) {
                return color.value + " " + color.stop + '%'
            });

            colorsText = colorsArray.join(' , ');

            return 'linear-gradient(' + angle + 'deg, ' + colorsText + ')';

        },

        generateOneSideLinerGradient: function (angle, color, transpacency, from) {
            transpacency = transpacency || 50;
            startAlpha = transpacency / 2;
            from = from || 50;

            var fromColor = tinycolor(color).setAlpha(startAlpha / 100).toRgbString(),
                middleColor = tinycolor(color).setAlpha(transpacency / 100).toRgbString(),
                toColor = tinycolor(color).toHexString();

            var colors = [
                {
                    value: 'rgba(0,0,0,0)',
                    stop: 0
                },
                {
                    value: fromColor,
                    stop: from / 2
                },
                {
                    value: middleColor,
                    stop: from
                },
                {
                    value: toColor,
                    stop: 100
                }
            ];

            return this.generateCSSLinerGradient(angle, colors);

        },

        getCSSGradientColors: function (value, colorAsObject) {
            var parts = value.match(/,((.*?)\s([0-9]+)%)/ig);
            parts = (parts || []).map(function (part) {
                part = CP_Customizer.utils.phpTrim(part, ',').trim();
                var stop = part.match(/(\d+)%/igm)[0];
                var color = part.replace(stop, '').trim();

                return {
                    value: colorAsObject ? tinycolor(color) : color,
                    stop: CP_Customizer.utils.phpTrim(stop, '%').trim()
                }
            });

            return parts
        },

        getRGBAStringTransparency: function (color) {

          var transparency = tinycolor(color).getAlpha();

          return transparency;

        }
    }


})(window, CP_Customizer, jQuery);
