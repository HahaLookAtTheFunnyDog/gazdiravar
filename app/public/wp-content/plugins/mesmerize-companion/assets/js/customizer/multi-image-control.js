(function (root, CP_Customizer) {

    var itemTemplate = _.template(
        '<div class="cp-multi-image-item attachment-media-view attachment-media-view-image ">' +
        '            <div class="thumbnail thumbnail-image">' +
        '                <img id="<%= id %>-thumb" class="attachment-thumb" src="<%= value %>" draggable="false" alt="">	' +
        '            </div>' +
        '            <div class="actions">' +
        '                <input type="hidden" value="<%= value %>" id="<%= id %>" />' +
        '                <span title="<%= cpMultiImageTexts.changeTitle %>" class="open-right section-icon" onClick=\'CP_Customizer.openMediaBrowser("image", jQuery("#<%= id %>"))\'></span>' +
        '                <% if(showRemove){ %>' +
        '                    <span data-action="remove" title="<%= cpMultiImageTexts.deleteTitle %>" class="item-remove"></span>' +
        '                <% } %>' +
        '            </div>' +
        '            <script>' +
        '                jQuery("#<%= id %>").change(function(){' +
        '                    jQuery(\'#<%= id %>-thumb\').attr(\'src\',this.value);' +
        '                });' +
        '            </script>' +
        '</div>'
    );

    function getManagerData($manager) {
        return $manager.find('input[type="hidden"]').map(function () {
            return this.value
        }).toArray();
    }


    function setManagerData($manager, data) {
        $manager.empty();

        for (var i = 0; i < data.length; i++) {
            $manager.append(
                itemTemplate({
                    "id": _.uniqueId("cp-multi-image-item-"),
                    "value": data[i],
                    "showRemove": data.length > parseInt($manager.data().min)
                })
            );
        }

        if ($manager.data().max < 0 || $manager.data().max > data.length) {
            $manager.append('' +
                '<div class="add-new-container">' +
                '    <button type="button" data-action="add-new-item" class="button upload-button control-focus" >' + cpMultiImageTexts.addImage + '</button> ' +
                '</div>');
        }


    }

    function getManager($item) {
        $item = jQuery($item);
        if ($item.is('[data-type="cp-multi-image-manager"]')) {
            return $item;
        } else {
            return $item.closest('[data-type="cp-multi-image-manager"]');
        }
    }


    jQuery(document).ready(function ($) {
        $(document).on('change', '[data-type="cp-multi-image-manager"] input[type="hidden"]', function () {
            var $manager = getManager(this);
            var setting = $manager.attr('data-customize-setting-link');
            root.CP_Customizer.setMod(setting, getManagerData($manager));
        });
        $(document).on('click', '[data-type="cp-multi-image-manager"] [data-action="add-new-item"]', function () {
            var $manager = getManager(this);
            var setting = _.clone($manager.attr('data-customize-setting-link'));
            var data = wp.customize(setting).get();
            data.push('');
            setManagerData($manager, data);
            root.CP_Customizer.setMod(setting, data);
        });

        $(document).on('click', '[data-type="cp-multi-image-manager"] [data-action="remove"]', function () {
            var $manager = getManager(this);
            var setting = $manager.attr('data-customize-setting-link');
            var data = _.clone(wp.customize(setting).get());
            var index = $(this).closest('.attachment-media-view').index();
            data.splice(index, 1);
            setManagerData($manager, data);
            root.CP_Customizer.setMod(setting, data);
        });

    });

    CP_Customizer.bind(CP_Customizer.events.PREVIEW_LOADED, function () {
        jQuery('[data-type="cp-multi-image-manager"]').each(function () {
            var $manager = getManager(this);
            var setting = $manager.attr('data-customize-setting-link');
            wp.customize(setting).bind(function (data) {
                setManagerData($manager, data);
            });

            var data = wp.customize(setting).get();

            if (!data || !data.length) {
                data = ["", ""];
            } else {
                if (data.length === 1) {
                    data.push("");
                }
            }

            setManagerData($manager, data);
        });
    });
})(window, window.CP_Customizer);