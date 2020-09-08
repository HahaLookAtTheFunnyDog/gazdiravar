(function ($) {

    if (window.wp && window.wp.customize) {
        return;
    }

    debugger;

    window.initHoverFX = function (options, isUI, resizeEvent) {
        var defaults, item, opts, params, instanceName, className, classElements, doesCSS3, supportRGBA;
        item = $('.contentswap-effect');
        supportRGBA = true;
        doesCSS3 = true;

        opts = options;

        function setParams(elem) {

            className = $(elem).attr('class');
            classElements = className.split(" ");
            for (var i = 0; i <= item.length; i++) {
                for (var prop in opts) {
                    if (classElements[i] === prop) {
                        instanceName = prop;
                    }
                }
            }

            return opts ? params = opts[instanceName] : params = defaults[instanceName];
        }

        function getInner(elem, type) {

            var inner;
            switch (type) {
                case "overlay":
                    inner = $(elem).find('.swap-inner');
                    break;
                case "imageSwap":
                    inner = $(elem).find('.second-image');
                    break;
                case "caption":
                    inner = $(elem).find('.swap-inner');
                    break;
            }
            return inner;
        }

        function getDimms(elem) {
            var dims = {
                width: '100%',
                height: Math.abs(parseFloat($(elem).outerHeight(false)))
            };
            return dims
        }

        function moveCSSProps(source, target) {
            var ml = source.css('margin-left');
            var mr = source.css('margin-right');
            var mt = source.css('margin-top');
            var mb = source.css('margin-bottom');
            var floatVal = source.css('float');
            source.css({
                'float': 'none',
                'margin-left': '0px',
                'margin-right': '0px',
                'margin-top': '0px',
                'margin-bottom': '0px'
            })
            target.css({
                'float': floatVal,
                'margin-left': ml,
                'margin-right': mr,
                'margin-top': mt,
                'margin-bottom': mb
            })

        }

        function setCaptionHeight(height, elem) {
            if (height != 'auto' || height != 'undefined') elem.css('height', parseInt(height) + 'px');
        }

        function genRGBA(hex, alpha, elem) {
            //          with both longhand and shorthand support (accepts both #fff or #fffff)
            if (hex.indexOf('#') == -1 || hex.indexOf('rgb(') > -1 || hex.indexOf('rgba') > -1) {
                return {
                    rgba: hex
                };
            }
            var opacity;
            alpha ? opacity = (alpha / 100).toFixed(2) : opacity = 1;

            function convertRGBDecimalToHex(rgb) {

                // check for RGB
                var regexRGB = /rgb *\( *([0-9]{1,3}) *, *([0-9]{1,3}) *, *([0-9]{1,3}) *\)/;
                var values = regexRGB.exec(rgb);

                // check for RGBA
                if (!values) {
                    var regexRGBA = /rgba *\( *([0-9]{1,3}) *, *([0-9]{1,3}) *, *([0-9]{1,3}) *, *(0.+[0-9]) *\)/;
                    values = regexRGBA.exec(rgb);
                }

                if (!values) return rgb;
                if (values.length != 5) {
                    return rgb; // fall back to what was given.              
                }
                var r = Math.round(parseFloat(values[1]));
                var g = Math.round(parseFloat(values[2]));
                var b = Math.round(parseFloat(values[3]));
                if (values[4]) {
                    opacity = values[4];
                }
                return "#" + (r + 0x10000).toString(16).substring(3).toUpperCase() + (g + 0x10000).toString(16).substring(3).toUpperCase() + (b + 0x10000).toString(16).substring(3).toUpperCase();
            }

            hex = convertRGBDecimalToHex(hex);

            var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function (m, r, g, b) {
                return r + r + g + g + b + b;
            });

            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

            function alhpaToHex(val) {
                var num = val * 255;
                return Math.floor(num).toString(16);
            }

            if (typeof(elem) !== 'undefined' && result) {
                switch (supportRGBA) {
                    case true:
                        elem.css('background-color', 'rgba(' + [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)].join(",") + ',' + opacity + ')');
                        break;
                    case false:
                        elem.css({
                            'background': 'transparent',
                            'filter': 'progid:DXImageTransform.Microsoft.gradient(startColorstr=#' + alhpaToHex(opacity) + result[0].replace('#', '') + ',endColorstr=#' + alhpaToHex(opacity) + result[0].replace('#', '') + ');',
                            'zoom': 1
                        });
                        break;
                }
            }

            return result ? {
                rgba: 'rgba(' + [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)].join(",") + ',' + opacity + ')',
                fallback: alhpaToHex(opacity) + result[0].replace('#', '')
            } : null;
        }

        function getEaseType(ease) {
            //    Add more ease types - work in progress
            var easeCSS = "";
            var easeType;
            var oldWebkit = false;

            switch (ease.toLowerCase()) {
                case 'easeinoutback':
                    easeType = 'cubic-bezier(0.680, -0.550, 0.265, 1.550)';
                    oldWebkit = 'cubic-bezier(0.680, 0, 0.265, 1)';
                    break;
                case 'quick-in':
                    easeType = "cubic-bezier(0.315, -0.185, 0.000, 1.270)";
                    oldWebkit = "cubic-bezier(0.315, 0, 0.000, 1)";
                    break;
                case 'accelerate':
                    easeType = "cubic-bezier(0.885, 0.420, 0.000, 1.270)";
                    oldWebkit = "cubic-bezier(0.885, 0.420, 0.000, 1)";
                    break;
                case 'super-fast':
                    easeType = "cubic-bezier(0.000, 0.870, 0.000, 0.890)";
                    break;
                case 'ease-in-out-back':
                    easeType = "cubic-bezier(0.680, -0.550, 0.265, 1.550)";
                    oldWebkit = "cubic-bezier(0.680, 0, 0.265, 1)";
                    break;
                case 'ease-in-out-expo':
                    easeType = "cubic-bezier(1.000, 0.000, 0.000, 1.000)";
                    break;
                case 'ease-out-back':
                    easeType = "cubic-bezier(0.175, 0.885, 0.320, 1.275)";
                    oldWebkit = "cubic-bezier(0.175, 0.885, 0.320, 1)";
                    break;
                case 'ease-out-circ':
                    easeType = "cubic-bezier(0.075, 0.820, 0.165, 1.000)";
                    break;
                case 'ease-in-back':
                    easeType = "cubic-bezier(0.600, -0.280, 0.735, 0.045)";
                    oldWebkit = "cubic-bezier(0.600, 0, 0.735, 0.045)";
                    break;
                case 'ease-in-circ':
                    easeType = "cubic-bezier(0.600, 0.040, 0.980, 0.335)";
                    oldWebkit = "cubic-bezier(0.600, 0.040, 0.980, 0.335)";
                    break;
                case 'ease':
                    easeType = "ease";
                    break;
                case "ease-in":
                    easeType = "ease-in";
                    break;
                case "ease-in-out":
                    easeType = "ease-in-out";
                    break;
                case "ease-out":
                    easeType = "ease-out";
                    break;
            }

            if (oldWebkit) {
                easeType = oldWebkit;
                // easeCSS += "-webkit-transition-timing-function:" + oldWebkit + ";";
            }
            var prefixes = ["-webkit-", "-moz-", "-o-", ""];
            for (var i = 0; i < prefixes.length; i++) {
                easeCSS += prefixes[i] + "transition-timing-function:" + easeType + ";"
            }

            return easeCSS;


        };


        function centerIcon(icon) {
            var elem = $(icon);
            elem
                .parent()
                .css({
                    'width': elem.width(),
                    'height': elem.height()
                })
        }

        options.resizeEvent = resizeEvent ? true : false;


        start(options);

        function start(options) {
            var addEffects = $();

            item.each(function () {

                var self, params, inner, overlay, initial, dims, type, transitionProp, easeType, icon;
                self = $(this);
                self.unbind('.hoverfx');
                self.css({
                    "width": "",
                    "height": ""
                });


                params = setParams(self);
                inner = getInner(self, params.contentType);
                initial = self.find('.initial-image');
                overlay = self.find('.overlay');
                params.overlayColor = inner.css('background-color');
                params.innerColor = inner.css('background-color');
                inner.removeAttr('style');
                overlay.removeAttr('style');
                initial.removeAttr('style');

                moveCSSProps(initial, self);

                dims = getDimms(self);
                //console.log(dims);
                easeType = getEaseType(params.effectEasing);
                type = params.contentType;

                icon = inner.find('.swap-icons img');
                centerIcon(icon);
                if (type == 'overlay') {
                    transitionProp = 'all';
                } else {
                    transitionProp = 'margin';
                }
                initial.css('float', 'none');

                if (type == "caption") {
                    if (self.find('.caption-wrap').length == 0) {
                        var captionWrap = $('<div />').addClass('caption-wrap overlay');
                        inner.wrap(captionWrap);
                    }
                    overlay = self.find('.caption-wrap');
                } else {
                    overlay = self.find('.overlay');
                }
                overlay.add(inner).css('display', 'block');


                function applyTransition(elem, prop, easeCSS, duration, includeTiming) {
                    var property = prop ? prop : "all";
                    var customEase = "";
                    var dur = params.effectDelay + "ms";
                    var delay = "";


                    var transitionValue = property + " " + dur;
                    elem.css({
                        '-webkit-transition': transitionValue,
                        '-moz-transition': transitionValue,
                        '-o-transition': transitionValue,
                        'transition': transitionValue
                    })
                    if (includeTiming) {
                        applyTiming(elem, easeCSS);
                    }
                }

                function applyTiming(elem, easeCSS) {
                    elem.attr('style', elem.attr('style') + easeCSS);
                }

                self.addClass(params.effectType);

                var loaded = false;

                if (initial.height() > 0) {

                    loaded = true;
                    var initialDims = getDimms(initial);
                    self.css({
                        'width': initialDims.width/*,
                        'height': initialDims.height*/
                    });

                    if (type == "overlay") {
                        applyTransition(inner, transitionProp, easeType, params.effectDelay, true);
                    }
                }

                initial.load(function () {
                    var imageDims = getDimms($(this));

                    self.css({
                        'width': imageDims.width/*,
                        'height': imageDims.height*/
                    });
                    if (type == 'overlay') {
                        inner.css({
                            'margin-top': 0 / 2,
                            'margin-left': Math.abs(imageDims.width - inner.outerWidth()) / 2
                        });
                    }
                    if (type == "overlay") {
                        applyTransition(inner, transitionProp, easeType, params.effectDelay);
                    }

                });

                inner.css({
                    'background-color': genRGBA(params.overlayColor, params.overlayAlpha).rgba
                });

                if (type == "overlay") {
                    applyTransition(overlay, 'opacity', easeType, params.effectDelay, true);
                }


                var showCaption, hideCaption;

                var isMobile = {
                    Android: function () {
                        return navigator.userAgent.match(/Android/i);
                    },
                    BlackBerry: function () {
                        return navigator.userAgent.match(/BlackBerry/i);
                    },
                    iOS: function () {
                        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                    },
                    Opera: function () {
                        return navigator.userAgent.match(/Opera Mini/i);
                    },
                    Windows: function () {
                        return navigator.userAgent.match(/IEMobile/i);
                    },
                    any: function () {
                        return !!(isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows() || ("ontouchstart" in document));
                    }
                };

                var _isMobile = isMobile.any();

                if (params.link) {
                    if (_isMobile == true) {

                    } else {
                        self.bind('click.hoverfx', function () {
                            if (params.openPage === 'same') {
                                window.location.assign(params['link']);
                            } else {
                                window.open(params['link'], '_blank');
                            }
                        });
                    }
                }


                if (type == 'overlay') {
                    self.unbind('.caption');
                    self.addClass('contentswap-overlay');
                    var initialDims = getDimms(initial);
                    //console.log(dims.width + " .. " +  inner.outerWidth());
                    if (initial.height() > 0) {
                        inner.css({
                            // 'margin-top': Math.abs(initialDims.height - inner.outerHeight()) / 2,
                            'margin-top': 0 / 2,
                            'margin-left': Math.abs(initialDims.width - inner.outerWidth()) / 2
                        });
                    }

                }

                if (type == 'caption') {
                    self.addClass('caption');

                    if (params.direction == 'top') {
                        if (!loaded) {
                            setTimeout(function () {
                                overlay.css({
                                    'margin-top': (-inner.outerHeight() - 1) + 'px',
                                    'opacity': 1
                                });
                            }, 10);

                        } else {
                            overlay.css({
                                'margin-top': (-inner.outerHeight() - 1) + 'px',
                                'opacity': 1
                            });
                        }

                        setTimeout(function () {
                            overlay.insertBefore(initial);

                        }, 10);
                    } else {

                        setTimeout(function () {
                            overlay.css({
                                'opacity': 1
                            });
                        }, 10);
                    }
                    //else applyTransition(initial,transitionProp);
                    var showCaption, hideCaption;


                    //work in progress
                    if (params.captionType == "over") {

                        if (params.direction == 'top') {
                            overlay.css({
                                'position': 'absolute',
                                'z-index': 9991

                            });
                        } else {
                            overlay.css({
                                'margin-top': '1px'
                            });
                        }
                        applyTransition(overlay, 'margin', easeType, params.effectDelay, true);

                        showCaption = function () {

                            overlay.css({
                                'margin-top': (-inner.outerHeight()) + 'px'
                            });
                            if (params.direction == 'top') overlay.css('margin-top', 0);
                        };
                        hideCaption = function () {
                            overlay.css({
                                'margin-top': "0px"
                            });
                            if (params.direction == 'top') overlay.css('margin-top', (-inner.outerHeight()) + 'px');
                        }
                    } else {
                        applyTransition(overlay, "margin", easeType, params.effectDelay, true);
                        applyTransition(initial, "margin", easeType, params.effectDelay, true);


                        showCaption = function () {

                            if (params.captionHeight <= inner.outerHeight()) {
                                setCaptionHeight(params.captionHeight, inner);
                            }

                            if (params.direction == 'top' && params.captionType != 'over') {

                                overlay.css({
                                    'margin-top': '0px'
                                });
                            } else {
                                overlay.css({
                                    'margin-top': -inner.outerHeight()
                                })
                                initial.css({
                                    'margin-top': -inner.outerHeight(),
                                    'margin-bottom': inner.outerHeight()
                                })

                            }
                        }

                        hideCaption = function () {
                            if (params.direction == 'top') {
                                overlay.css({
                                    'margin-top': (-inner.outerHeight() - 1) + 'px'
                                })
                            } else {
                                overlay.css({
                                    'margin-top': '1px'
                                })
                                initial.css({
                                    'margin-top': '0px',
                                    'margin-bottom': '0px'
                                })
                            }
                        }
                    }


                }

                if (_isMobile == true) {
                    self.addClass('hover');
                    if (type == "caption") {
                        showCaption();
                    } else {

                    }

                } else {
                    if (type == "caption") {
                        self.bind('mouseenter.hoverfx', showCaption)
                            .bind('mouseleave.hoverfx', hideCaption);
                    }

                }


            });
        }

    }
})(jQuery);
