<?php


use ExtendThemes\DemoImportIntegration\CustomizerImporter;


add_action('wp_ajax_ocdi_import_customizer_data', function () {
    
    remove_all_actions('pt-ocdi/customizer_import_execution');
    add_action('pt-ocdi/customizer_import_execution', function ($selected_import_files) {
        if ( ! empty($selected_import_files['customizer'])) {
            require_once MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH . "/inc/CustomizerImporter.php";
            CustomizerImporter::import($selected_import_files['customizer']);
        }
    }, 10, 1);
}, 0);


function mesmerize_prepare_theme_mods_before_ocdi()
{
    remove_theme_mods();
    do_action('cloudpress\companion\clear_caches');
    
}

function mesmerize_set_fonts_after_import_mods()
{
    $fonts = get_theme_mod('web_fonts', false);
    
    if ( ! $fonts) {
        return;
    }
    
    try {
        $fonts = json_decode($fonts, true);
    } catch (Exception $e) {
        return;
    }
    
    $theme_mods_fonts = array();
    
    foreach (Kirki::$fields as $field) {
        if (isset($field['type']) && isset($field['settings']) && $field['type'] === 'kirki-typography') {
            
            $value = get_theme_mod($field['settings']);
            if (isset($value['font-family'])) {
                $family          = $value['font-family'];
                $variant         = isset($value['variant']) ? $value['variant'] : "400";
                $current_value   = isset($theme_mods_fonts[$family]) ? $theme_mods_fonts[$family] : array();
                $current_value[] = $variant;
                
                $theme_mods_fonts[$family] = array_unique($current_value);
            }
        }
    }
    
    foreach ($theme_mods_fonts as $font => $variants) {
        foreach ($fonts as $index => $value) {
            if ($value['family'] === $font) {
                foreach ($variants as $v) {
                    if ( ! in_array($v, $value['weights'])) {
                        $value['weights'][] = $v;
                    }
                }
                
                $fonts[$index]['weights'] = array_unique($value['weights']);
                unset($theme_mods_fonts[$font]);
                break;
            }
        }
    }
    
    foreach ($theme_mods_fonts as $font => $variants) {
        $fonts[] = array(
            'family'  => $font,
            'weights' => $variants,
        );
    }
    
    try {
        $fonts = json_encode($fonts);
        
        if (is_string($fonts)) {
            set_theme_mod('web_fonts', $fonts);
        }
        
    } catch (Exception $e) {
    
    }
}

function mesmerize_replace_url_in_customizer_options($options, $from_url, $to_url)
{
    foreach ($options as $key => $value) {
        if ($key === "CP_AUTO_SETTING" || $key === "page_content") {
            unset($options[$key]);
            continue;
        }
        
        if (is_string($value)) {
            
            $decoded = json_decode($value, true);
            
            if ($decoded !== null) {
                $value         = mesmerize_replace_url_in_customizer_options($decoded, $from_url, $to_url);
                $options[$key] = json_encode($value);
            } else {
                $url = preg_replace('#^https?://#', '', $from_url);
                
                if (strpos($value, $url) !== false) {
                    
                    preg_match("#/(\d{4}.*)#", $value, $matches);
                    
                    // seems to be an assets file
                    if (count($matches)) {
                        $asset_path    = array_pop($matches);
                        $options[$key] = $to_url . "/" . trim($asset_path, "/");
                    } else {
                        $site_url      = untrailingslashit(site_url());
                        $options[$key] = str_replace("https://{$url}", $site_url, $options[$key]);
                        $options[$key] = str_replace("http://{$url}", $site_url, $options[$key]);
                        $options[$key] = str_replace($url, $site_url, $options[$key]);
                    }
                }
            }
            
            
        } else {
            if (is_array($value)) {
                $options[$key] = mesmerize_replace_url_in_customizer_options($value, $from_url, $to_url);
            }
        }
    }
    
    return $options;
}

function mesmerize_clear_cached_mods_style_after_import()
{
    mesmerize_set_fonts_after_import_mods();
    
    // url replace in customizer
    $current_import_data = \OCDI\OneClickDemoImport::get_instance()->get_current_importer_data();
    $url                 = \ExtendThemes\DemoImportIntegration\DemoImportIntegration::getPreviewURL($current_import_data['selected_index']);
    $url                 = untrailingslashit($url);
    $to_url              = wp_upload_dir();
    $to_url              = untrailingslashit($to_url['baseurl']);
    
    
    $options = get_theme_mods();
    $options = mesmerize_replace_url_in_customizer_options($options, $url, $to_url);
    
    $theme_slug = get_option('stylesheet');
    update_option("theme_mods_{$theme_slug}", $options);
    do_action('cloudpress\companion\clear_caches');
    update_option('feature_popup_demo_import_disabled', 1, 'yes');
}

add_action('pt-ocdi/customizer_import_execution', 'mesmerize_prepare_theme_mods_before_ocdi', 5, 1);

add_action('pt-ocdi/after_import', 'mesmerize_clear_cached_mods_style_after_import', PHP_INT_MAX);
