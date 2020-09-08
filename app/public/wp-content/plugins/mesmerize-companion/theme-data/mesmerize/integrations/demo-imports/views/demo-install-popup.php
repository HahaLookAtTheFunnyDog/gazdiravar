<script type="text/template" id="tmpl-extendthemes-import-popup" style="display: none">
    <div class="extendthemes-demo-import-popup-container">
        <div class="image-container">
            <img src="{{ data.preview_image }}"/>
        </div>
        <div class="info">
            <h2><?php esc_html_e('You are about to import a demo site', 'mesmerize-companion'); ?></h2>
            <ul>
                <li><?php esc_html_e('No existing content or any other data will be deleted.', 'mesmerize-companion'); ?></li>
                <li><?php esc_html_e('Current pages will be moved to trash. You can restore them back at any time.', 'mesmerize-companion'); ?></li>
                <li><?php esc_html_e('Posts, pages, images, widgets, menus and other theme settings will get imported.', 'mesmerize-companion'); ?></li>
            </ul>
        </div>
        <# if(data.plugins && data.plugins.length){ #>
        <div class="plugins">
            <h3><?php esc_html_e('The following will be installed and activated as they are part of the demo', 'mesmerize-companion'); ?></h3>
            <ul class="plugins-list">
                <# _(data.plugins).each(function(plugin){ #>
                <li>
                    <label>
                        <input type="checkbox" style="display:none" data-slug="{{ plugin.slug }}" checked>
                        <span>{{ plugin.label }}</span>
                    </label>
                    <div class="plugin-status {{ plugin.status }}" data-slug="{{ plugin.slug }}">
                        <span><# print(ocdi.texts[plugin.status]) #></span>
                    </div>
                </li>
                <# }); #>
            </ul>
        </div>
        <# } #>
        <div class="popup-footer">
            <a class="button button-hero button-primary" data-name="import-data" data-id="{{ data.id }}">
                <# if(data.plugins && data.plugins.length){ #>
                <?php esc_html_e('Install plugins and import', 'mesmerize-companion'); ?>
                <# } else { #>
                <?php esc_html_e('Import', 'mesmerize-companion'); ?>
                <# } #>
            </a>
        </div>
    </div>
</script>
