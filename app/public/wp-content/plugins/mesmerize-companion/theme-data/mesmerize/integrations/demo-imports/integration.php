<?php

if ( ! defined('ABSPATH')) {
    die('Silence is golden');
}

// run only in wp admin
if ( ! is_admin()) {
    return;
}


if ( ! defined('MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH')) {
    define('MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH', dirname(__FILE__) . '/');
}

if ( ! defined('MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL')) {
    define('MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL', plugin_dir_url(__FILE__));
}

require_once MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH . "/inc/DemoImportIntegration.php";
require_once MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH . "/inc/ContentHooks.php";

\ExtendThemes\DemoImportIntegration\DemoImportIntegration::run();

//\ExtendThemes\DemoImportIntegration\DemoImportIntegration::isOCDIActive('mesmerize_add_ocdi_tgma_options');
\ExtendThemes\DemoImportIntegration\DemoImportIntegration::isOCDIInactive('mesmerize_add_ocdi_tgma_options');

function mesmerize_add_ocdi_tgma_options()
{
    add_filter('mesmerize_tgmpa_plugins', function ($plugins) {
        $plugins[] = array(
            'name'     => 'One Click Demo Import',
            'slug'     => 'one-click-demo-import',
            'required' => false,
        );
        
        return $plugins;
    });
    
    add_filter('mesmerize_skip_tgma_plugin_from_notices', function ($value, $slug) {
        if ($slug === 'one-click-demo-import') {
            $value = true;
        }
        
        return $value;
    }, 10, 2);
    
}

function mesmerize_get_customizer_demo_import_content()
{
    ob_start();
    require MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH . "/views/customizer-popup.php";
    
    return ob_get_clean();
}


add_filter('cloudpress\customizer\feature_popups', function ($popups) {
    
    $popups[] = array(
        'title'    => 'Import a demo site',
        'id'       => 'demo_import',
        'content'  => mesmerize_get_customizer_demo_import_content(),
        'autoload' => true,
        'data'     => array(
            'class' => "ocdie-tbWindow",
        ),
        'assets'   => array(
//            'style' => "",
            'script' => MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL . "/assets/demo-import-customizer.js",
        ),
    );
    
    $theme_mods = get_theme_mods();
    foreach ($theme_mods as $key => $value) {
        if (strpos($key, 'header') !== false) {
            update_option("feature_popup_demo_import_disabled", 1, 'no');
            break;
        }
    }
    
    return $popups;
});


add_action('customize_register', function ($wp_customize) {
    
    /** @var WP_Customize_Manager $wp_customize */
    
    
    $wp_customize->add_setting('extendthemes_start_from_demo_site_setting', array(
        'default' => '',
    ));
    
    $wp_customize->add_control('extendthemes_start_from_demo_site_control', array(
        'section'  => 'extendthemes_start_from_demo_site',
        'settings' => 'extendthemes_start_from_demo_site_setting',
    ));
    
});

function mesmerize_load_demo_theme_partial()
{
    mesmerize_load_theme_partial('demo-imports');
}

add_action('admin_menu', function () {
    $title = apply_filters('mesmerize_demos_page_name', __('Mesmerize Demos', 'mesmerize-companion'));
    add_theme_page(
        $title,
        $title,
        'activate_plugins',
        'mesmerize-demos',
        'mesmerize_load_demo_theme_partial');
}, 11);


add_action('admin_head', function () {
    ?>
    <style type="text/css">
        #adminmenu .wp-not-current-submenu li > a[href*="page=mesmerize-demos"]:before,
        #adminmenu .wp-has-current-submenu ul > li > a[href*="page=mesmerize-demos"]:before {
            width: 1em;
            height: 1em;
            font-size: 14px;
            font-family: dashicons;
            text-decoration: inherit;
            font-weight: normal;
            font-style: normal;
            vertical-align: top;
            text-align: center;
            content: "\f547";
            margin-right: 4px;
        }

    </style>
    <?php
});
