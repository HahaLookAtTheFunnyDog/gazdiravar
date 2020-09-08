(function ($) {

    // source: https://github.com/andymantell/node-wpautop
    function _autop_newline_preservation_helper(matches) {
        return matches[0].replace("\n", "<WPPreserveNewline />");
    };

    function wpautop(pee, br) {
        if (typeof(br) === 'undefined') {
            br = true;
        }

        var pre_tags = {};
        if (pee.trim() === '') {
            return '';
        }

        pee = pee + "\n"; // just to make things a little easier, pad the end
        if (pee.indexOf('<pre') > -1) {
            var pee_parts = pee.split('</pre>');
            var last_pee = pee_parts.pop();
            pee = '';
            pee_parts.forEach(function (pee_part, index) {
                var start = pee_part.indexOf('<pre');

                // Malformed html?
                if (start === -1) {
                    pee += pee_part;
                    return;
                }

                var name = "<pre wp-pre-tag-" + index + "></pre>";
                pre_tags[name] = pee_part.substr(start) + '</pre>';
                pee += pee_part.substr(0, start) + name;

            });

            pee += last_pee;
        }

        pee = pee.replace(/<br \/>\s*<br \/>/, "\n\n");

        // Space things out a little
        var allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
        pee = pee.replace(new RegExp('(<' + allblocks + '[^>]*>)', 'gmi'), "\n$1");
        pee = pee.replace(new RegExp('(</' + allblocks + '>)', 'gmi'), "$1\n\n");
        pee = pee.replace(/\r\n|\r/, "\n"); // cross-platform newlines

        if (pee.indexOf('<option') > -1) {
            // no P/BR around option
            pee = pee.replace(/\s*<option'/gmi, '<option');
            pee = pee.replace(/<\/option>\s*/gmi, '</option>');
        }

        if (pee.indexOf('</object>') > -1) {
            // no P/BR around param and embed
            pee = pee.replace(/(<object[^>]*>)\s*/gmi, '$1');
            pee = pee.replace(/\s*<\/object>/gmi, '</object>');
            pee = pee.replace(/\s*(<\/?(?:param|embed)[^>]*>)\s*/gmi, '$1');
        }

        if (pee.indexOf('<source') > -1 || pee.indexOf('<track') > -1) {
            // no P/BR around source and track
            pee = pee.replace(/([<\[](?:audio|video)[^>\]]*[>\]])\s*/gmi, '$1');
            pee = pee.replace(/\s*([<\[]\/(?:audio|video)[>\]])/gmi, '$1');
            pee = pee.replace(/\s*(<(?:source|track)[^>]*>)\s*/gmi, '$1');
        }

        pee = pee.replace(/\n\n+/gmi, "\n\n"); // take care of duplicates

        // make paragraphs, including one at the end
        var pees = pee.split(/\n\s*\n/);
        pee = '';
        pees.forEach(function (tinkle) {
            pee += '<p>' + tinkle.replace(/^\s+|\s+$/g, '') + "</p>\n";
        });

        pee = pee.replace(/<p>\s*<\/p>/gmi, ''); // under certain strange conditions it could create a P of entirely whitespace
        pee = pee.replace(/<p>([^<]+)<\/(div|address|form)>/gmi, "<p>$1</p></$2>");
        pee = pee.replace(new RegExp('<p>\s*(</?' + allblocks + '[^>]*>)\s*</p>', 'gmi'), "$1", pee); // don't pee all over a tag
        pee = pee.replace(/<p>(<li.+?)<\/p>/gmi, "$1"); // problem with nested lists
        pee = pee.replace(/<p><blockquote([^>]*)>/gmi, "<blockquote$1><p>");
        pee = pee.replace(/<\/blockquote><\/p>/gmi, '</p></blockquote>');
        pee = pee.replace(new RegExp('<p>\s*(</?' + allblocks + '[^>]*>)', 'gmi'), "$1");
        pee = pee.replace(new RegExp('(</?' + allblocks + '[^>]*>)\s*</p>', 'gmi'), "$1");

        if (br) {
            pee = pee.replace(/<(script|style)(?:.|\n)*?<\/\\1>/gmi, _autop_newline_preservation_helper); // /s modifier from php PCRE regexp replaced with (?:.|\n)
            pee = pee.replace(/(<br \/>)?\s*\n/gmi, "<br />\n"); // optionally make line breaks
            pee = pee.replace('<WPPreserveNewline />', "\n");
        }

        pee = pee.replace(new RegExp('(</?' + allblocks + '[^>]*>)\s*<br />', 'gmi'), "$1");
        pee = pee.replace(/<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)/gmi, '$1');
        pee = pee.replace(/\n<\/p>$/gmi, '</p>');

        if (Object.keys(pre_tags).length) {
            pee = pee.replace(new RegExp(Object.keys(pre_tags).join('|'), "gi"), function (matched) {
                return pre_tags[matched];
            });
        }

        return pee;
    };

    CP_Customizer.addModule(function (CP_Customizer) {
        CP_Customizer.utils = CP_Customizer.utils || {};

        CP_Customizer.utils.wpautop = function (value) {
            value = wpautop(value);

            if (jQuery(value).length === 1) {
                return jQuery(value).html();
            }

            return value;

        };
        
        CP_Customizer.utils.phpTrim = function (str, charlist) {

            var whitespace = [
                ' ',
                '\n',
                '\r',
                '\t',
                '\f',
                '\x0b',
                '\xa0',
                '\u2000',
                '\u2001',
                '\u2002',
                '\u2003',
                '\u2004',
                '\u2005',
                '\u2006',
                '\u2007',
                '\u2008',
                '\u2009',
                '\u200a',
                '\u200b',
                '\u2028',
                '\u2029',
                '\u3000'
            ].join('');

            var l = 0;
            var i = 0;
            str += '';

            if (charlist) {
                whitespace = (charlist + '').replace(/([[\]().?/*{}+$^:])/g, '$1')
            }
            l = str.length
            for (i = 0; i < l; i++) {
                if (whitespace.indexOf(str.charAt(i)) === -1) {
                    str = str.substring(i)
                    break
                }
            }
            l = str.length
            for (i = l - 1; i >= 0; i--) {
                if (whitespace.indexOf(str.charAt(i)) === -1) {
                    str = str.substring(0, i + 1)
                    break
                }
            }
            return whitespace.indexOf(str.charAt(0)) === -1 ? str : ''
        };

        CP_Customizer.utils.normalizeBackgroundImageValue = function (value) {
            value = value.replace(/url\((.*)\)/, "$1");
            return CP_Customizer.utils.phpTrim(value, "\"'");
        };


        CP_Customizer.utils.htmlDecode = function (value) {
            var result = $('<div/>').html(value).text();
            return result;
        };

        CP_Customizer.utils.htmlEscape = function (str) {
            return str
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        };


        var htmlEntityMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        };

        CP_Customizer.utils.htmlUnescape = function (str) {

            for (var item in htmlEntityMap) {
                var regExp = new RegExp(htmlEntityMap[item], 'g');
                str = str.replace(regExp, item);
            }

            return str;
        };


        CP_Customizer.utils.setToPath = function (schema, path, value) {

            if (!schema) {
                return schema;
            }

            if (_.isString(schema)) {
                schema = CP_Customizer.utils.normalizeValue(schema);
            }

            var pList = path.split('.');
            var len = pList.length;

            if (len > 1) {
                var first = pList.shift();
                var remainingPath = pList.join('.');
                schema[first] = CP_Customizer.utils.setToPath(schema[first], remainingPath, value);
            } else {
                schema[path] = value;
            }

            return schema;
        };

        CP_Customizer.utils.normalizeShortcodeString = function (shortcode) {
            shortcode = CP_Customizer.utils.htmlDecode(shortcode);

            return '[' + CP_Customizer.utils.phpTrim(shortcode) + ']';
        };

        CP_Customizer.utils.getSpectrumColorFormated = function ($spectrumElement) {
            var value = $spectrumElement.spectrum('get');


            if (!value) {
                return value;
            }

            if (value.getAlpha() < 1) {
                return "rgba(" + value._r + "," + value._g + "," + value._b + "," + value._a + ")";
            } else {
                return value.toHexString();
            }
        };

        CP_Customizer.utils.normalizeValue = function (value, convertToArray) {


            if (_.isString(value)) {

                try {
                    value = decodeURI(value);

                } catch (e) {

                }

                try {
                    value = JSON.parse(value);

                } catch (e) {

                }

            }

            if (_.isObject(value) && convertToArray) {
                var hasOnlyNumberKeys = _.keys(value).map(function (k) {
                    return _.isNumber(parseInt(k))
                }).reduce(function (a, b) {
                    return (a && b);
                }, true);

                if (hasOnlyNumberKeys) {
                    var newValue = [];
                    _.keys(value).forEach(function (k) {

                        if (_.isUndefined(value[k])) {
                            return;
                        }

                        newValue.push(value[k]);
                    });

                    value = newValue;
                }
            }

            return value;
        };

        CP_Customizer.utils.getValue = function (component) {
            var value = undefined;

            if (component instanceof CP_Customizer.wpApi.Control) {
                value = component.setting.get();
            }

            if (component instanceof CP_Customizer.wpApi.Setting) {
                value = component.get();
            }

            if (_.isString(component)) {
                value = CP_Customizer.wpApi(component).get();
            }

            if (_.isString(value)) {

                try {
                    value = decodeURI(value);

                } catch (e) {

                }

                try {
                    value = JSON.parse(value);
                } catch (e) {

                }

            }

            return value;
        };

        CP_Customizer.utils.deepClone = function (toClone, asArray) {
            var result = jQuery.extend(true, {}, toClone);
            if (asArray) {
                result = _.toArray(result);
            }
            return result;
        };

        CP_Customizer.utils.cssValueNumber = function (value) {
            var matches = value.match(/\d+(.\d+)?/);

            if (!matches || !_.isArray(matches)) {
                return null;
            }

            return matches.shift();
        };

        CP_Customizer.utils.arrayChunk = function (bigArray, perGroup) {
            perGroup = perGroup || 5;
            var result = _.groupBy(bigArray, function (element, index) {
                return Math.floor(index / perGroup);
            });

            return _.toArray(result);
        };

        CP_Customizer.utils.normalizeClassAttr = function (classes, asSelector) {
            classes = classes.split(' ').filter(function (item) {
                return (item.trim().length > 0);
            });

            if (asSelector) {
                return (classes.length ? '.' + classes.join('.') : '');
            } else {
                return classes.join(' ');
            }
        };

        CP_Customizer.utils.getFileInfo = function (url) {
            var filename = url.substring(url.lastIndexOf("/") + 1, url.lastIndexOf("."));
            var parts = url.split("/" + filename)[0];
            var path = parts[0];
            var extension = parts.length > 1 ? parts[1].split('/')[0] : '';

            return {
                path: path,
                file: filename + (extension ? '.' + extension : extension),
                filename: filename,
                extension: extension
            }
        };

        var imageExtensions = ["tif", "tiff", "gif", "jpeg", "jpg", "jif", "jfif", "jp2", "jpx", "j2k", "j2c", "fpx", "pcd", "png", "pdf", "bmp", "ico"];
        CP_Customizer.utils.isImageFile = function (url) {
            var fileInfo = CP_Customizer.utils.getFileInfo(url);

            return (imageExtensions.indexOf(fileInfo.extension) !== -1);

        };

        // https://stackoverflow.com/a/13896633
        // TODO: make work with ?x[a]=2&x[b]=3
        CP_Customizer.utils.parseUrlQuery = function (str) {
            if (typeof str !== "string" || str.length === 0) return {};
            var s = str.split("&");
            var s_length = s.length;
            var bit, query = {}, first, second;
            for (var i = 0; i < s_length; i++) {
                bit = s[i].split("=");
                first = decodeURIComponent(bit[0]);
                if (first.length === 0) continue;
                second = decodeURIComponent(bit[1]);
                if (typeof query[first] === "undefined") query[first] = second;
                else if (query[first] instanceof Array) query[first].push(second);
                else query[first] = [query[first], second];
            }
            return query;
        };


        CP_Customizer.utils.stringifyUrlQuery = function (query) {
            var queryString = "";
            for (var key in query) {

                if (!query.hasOwnProperty(key)) {
                    continue;
                }

                var data = query[key];

                if (!_.isUndefined(data)) {
                    if (_.isString(data)) {
                        queryString += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(data);
                    }

                    if (_.isArray(data)) {
                        for (var i = 0; i < data.length; i++) {
                            queryString += '&' + encodeURIComponent(key) + '[' + i + ']=' + encodeURIComponent(data[i]);
                        }
                    }

                } else {
                    queryString += '&' + encodeURIComponent(key);
                }
            }

            if (queryString.length) {
                queryString = '?' + CP_Customizer.utils.phpTrim(queryString, '&');
            }

            return queryString;
        };

        CP_Customizer.utils.parseURL = function (url) {
            var location = ((url.split('?')[0] || '').split('#')[0] || ''),
                queryString = (url.indexOf('?') !== -1) ? url.split('?').pop().split('#')[0] : '',
                query = CP_Customizer.utils.parseUrlQuery(queryString),
                hash = (url.indexOf('#') !== -1) ? url.replace(/(.*)#/, '').trim() : '';

            return {
                location: location.replace(/\/$/, ''),
                query: query,
                hash: hash.length ? '#' + hash : ''
            }

        };

        CP_Customizer.utils.removeUrlQueryStrings = function (url, strings) {
            var parsedUrl = CP_Customizer.utils.parseURL(url),
                hash = parsedUrl.hash,
                queryKeys = Object.getOwnPropertyNames(parsedUrl.query),
                query = {};

            for (var i = 0; i < queryKeys.length; i++) {
                var key = queryKeys[i];
                if (strings.indexOf(key) === -1) {
                    query[key] = parsedUrl.query[key];
                }
            }

            var queryString = CP_Customizer.utils.stringifyUrlQuery(query);

            if (!queryString.length) {
                queryString = "/";
            }

            return parsedUrl.location + queryString + hash;
        };

        CP_Customizer.utils.nodeMatchingClasses = function (node, classList, firstMatchOnly) {

            if (!$(node).length) {
                if (firstMatchOnly) {
                    return undefined;
                }
                return [];
            }

            result = Array.from($(node)[0].classList).filter(function (_class) {
                return (classList.indexOf(_class) !== -1);
            });

            if (firstMatchOnly) {
                if (result.length) {
                    result = result[0];
                } else {
                    result = undefined;
                }
            }

            return result;
        };

        CP_Customizer.utils.colorInArray = function (colorsArray, color, includeAlpha) {
            var inArray = false;
            color = tinycolor(color);

            for (var i = 0; i < colorsArray.length; i++) {

                var _color = tinycolor(colorsArray[i]);
                inArray = (color._r === _color._r) && (color._g === _color._g) && (color._b === _color._b);

                if (includeAlpha) {
                    inArray = inArray && (color._a === _color._a);
                }

                if (inArray) {
                    break;
                }
            }

            return inArray;

        }

        CP_Customizer.utils.valueToBool = function (value) {
            if (_.isBoolean(value)) {
                return value;
            }

            if (_.isString(value)) {

                var _v = value.toLowerCase();
                if (_v === "yes" || _v === "true") {
                    return true;
                }

                if (_v === "no" || _v === "false") {
                    return false;
                }
            }


            if (!isNaN(value)) {
                return !!parseInt(value);
            }

            return !!value;
        }


        CP_Customizer.utils.convertColor = {
            toRGB: function (color) {
                return tinycolor(color).toRgbString();
            },
            toRGBA: function (color, alpha) {
                return tinycolor(color).setAlpha(alpha).toRgbString();
            },
            toHex: function (color) {
                return tinycolor(color).toHexString();
            },
            brighten: function (color, value) {
                return tinycolor(color).brighten(value).toRgbString();
            }
        }

        CP_Customizer.hasClass = function (element, classes) {
            var elementClasses = Array.from($(element)[0].classList);
        }
    });
})(jQuery);
