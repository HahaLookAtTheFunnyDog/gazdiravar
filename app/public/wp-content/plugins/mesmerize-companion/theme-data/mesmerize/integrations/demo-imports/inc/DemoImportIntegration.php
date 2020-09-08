<?php

namespace ExtendThemes\DemoImportIntegration;


class DemoImportIntegration
{
    private static $options
        = array(
            'remote_data_url_base'   => 'http://extendthemes.com/demos/wp-json/extendthemes/v1/demo-sites',
            'info_page_tabs_filter'  => 'mesmerize_info_page_tabs',
            'theme_version_function' => 'mesmerize_get_version',
            'pro_slug'               => 'mesmerize-pro',
        );
    
    private static $integrationPath = null;
    private static $_demos_data = null;
    
    /**
     * @return array
     */
    public static function getOption($name)
    {
        return isset(self::$options[$name]) ? self::$options[$name] : null;
    }
    
    public static function run($options = array())
    {
        DemoImportIntegration::$options = array_merge_recursive(DemoImportIntegration::$options, $options);
        
        DemoImportIntegration::addOCDIFilters();
        DemoImportIntegration::addOCDIImportHooks();
    }
    
    public static function isOCDIActive($callback)
    {
        add_action('after_setup_theme', function () use ($callback) {
            if (class_exists('\\OCDI\\OneClickDemoImport')) {
                call_user_func($callback);
            }
        }, PHP_INT_MAX);
    }
    
    
    public static function isOCDIInactive($callback)
    {
        add_action('after_setup_theme', function () use ($callback) {
            if ( ! class_exists('\\OCDI\\OneClickDemoImport')) {
                call_user_func($callback);
            }
        }, PHP_INT_MAX);
    }
    
    
    public static function isOCDIAJAX()
    {
        if ( ! wp_doing_ajax()) {
            return false;
        }
        
        if ( ! isset($_REQUEST['action'])) {
            return false;
        }
        
        return (strpos($_REQUEST['action'], 'ocdi_') !== false);
    }
    
    public static function addOCDIFilters()
    {
        
        global $pagenow;
        
        $is_page = ($pagenow === "themes.php" && isset($_REQUEST['page']));
        $is_ajax = DemoImportIntegration::isOCDIAJAX();
        
        if ($is_page || $is_ajax) {
            add_filter('pt-ocdi/import_files', function ($value) {
                $value = is_array($value) ? $value : array();
                
                return array_merge($value, DemoImportIntegration::getDemoDataOCDIFormat());
            });
        }
        
        
        add_filter('pt-ocdi/plugin_page_setup', '__return_false');
        add_filter('pt-ocdi/disable_pt_branding', '__return_true');
        
        add_filter('pt-ocdi/upload_file_path', function ($path) {
            $template = get_template();
            
            $newPath = "{$path}/{$template}-ocdi/";
            
            $newPath = wp_normalize_path($newPath);
            $newPath = trailingslashit($newPath);
            
            if ( ! is_dir($newPath)) {
                if ( ! wp_mkdir_p($newPath)) {
                    $newPath = $path;
                }
            }
            
            return $newPath;
        });
        
        add_action('wp_ajax_extendthemes_plugin_activation_url', array(__CLASS__, 'getPluginActivationURL'));
    }
    
    
    public static function getPluginActivationURL()
    {
        $plugin_file = isset($_REQUEST['plugin_file']) ? $_REQUEST['plugin_file'] : false;
        
        if ( ! $plugin_file) {
            wp_send_json(array(
                'error' => 'invalid_plugin_file',
            ));
        }
        
        $url = add_query_arg(array(
            '_wpnonce' => wp_create_nonce('activate-plugin_' . $plugin_file),
            'action'   => 'activate',
            'plugin'   => $plugin_file,
        ), network_admin_url('plugins.php'));
        
        wp_send_json(array(
            'url' => $url,
        ));
    }
    
    public static function integrationPath()
    {
        if ( ! DemoImportIntegration::$integrationPath) {
            DemoImportIntegration::$integrationPath = realpath(dirname(__FILE__) . "/..");
        }
        
        return DemoImportIntegration::$integrationPath;
    }
    
    public static function addOCDIImportHooks()
    {
        if ( ! defined('DOING_AJAX')) {
            
            add_filter(DemoImportIntegration::getOption('info_page_tabs_filter'), function ($tabs) {
                
                $data = array(
                    'title'   => __('Demo Import', 'mesmerize-companion'),
                    'partial' => DemoImportIntegration::integrationPath() . "/views/tab.php",
                );
                if (isset($tabs['demo-imports'])) {
                    unset($tabs['demo-imports']);
                }
                $tabs = mesmerize_associative_array_splice($tabs, 1, 'demo-imports', $data);
                
                return $tabs;
                
            }, 5);
            
            
        } else {
            
            if ( ! DemoImportIntegration::isOCDIAJAX()) {
                return;
            }
            
            require DemoImportIntegration::integrationPath() . "/inc/hooks/menu.php";
            require DemoImportIntegration::integrationPath() . "/inc/hooks/pages.php";
            require DemoImportIntegration::integrationPath() . "/inc/hooks/customizer.php";
            require DemoImportIntegration::integrationPath() . "/inc/hooks/woocommerce.php";
        }
    }
    
    
    public static function isDevMode()
    {
        return (defined('EXTENDTHEMES_DEMO_IMPORT_DEV_MODE') && EXTENDTHEMES_DEMO_IMPORT_DEV_MODE) || isset($_REQUEST['dev_mode']);
    }
    
    
    public static function getDemosImportURL()
    {
        
        $dev_mode = DemoImportIntegration::isDevMode();
        $base     = DemoImportIntegration::getOption('remote_data_url_base');
        $version  = call_user_func(DemoImportIntegration::getOption('theme_version_function'));
        $query    = array(
            'theme'    => get_template(),
            'pro-slug' => DemoImportIntegration::getOption('pro_slug'),
            'version'  => $version,
            'license'  => urlencode(''),
            'dev_mode' => $dev_mode ? "1" : "0",
        );
        
        $query_string = build_query($query);
        
        if ($query_string) {
            $query_string = "?" . $query_string;
        }
        
        $url = apply_filters('extendthemes_demos_import_url', $base . $query_string, $base, $query);
     
        return $url;
        
    }
    
    public static function demoDataRequestTimeout($value)
    {
        return 300;
    }
    
    public static function _getDemosData()
    {
        $transientKey   = apply_filters('extendthemes_demo_import_transient_key', get_template() . '_demos_to_import');
        $availableDemos = null;
        
        if (isset($_REQUEST['force_update_demos_sites'])) {
            $availableDemos = null;
        } else {
            if ( ! DemoImportIntegration::isDevMode()) {
                $availableDemos = get_transient($transientKey);
            }
        }
        
        if ( ! $availableDemos) {
            add_filter('http_request_timeout', array(__CLASS__, 'demoDataRequestTimeout'));
            $availableDemosServiceResponse = wp_remote_get(DemoImportIntegration::getDemosImportURL(), array(
                'body' => array(),
            ));
            remove_filter('http_request_timeout', array(__CLASS__, 'demoDataRequestTimeout'));
            
            if ($availableDemosServiceResponse instanceof \WP_Error) {
                die($availableDemosServiceResponse->get_error_message());
            } else {
                
                $availableDemos = json_decode($availableDemosServiceResponse['body'], true);
                
                if (isset($availableDemos['code']) && $availableDemos['code'] === 'extendthemes_themes_data_ok') {
                    $availableDemos = apply_filters('extendthemes_available_demos', $availableDemos);
                    
                    foreach ($availableDemos['data'] as $key => $demo) {
                        $availableDemos['data'][$key]['import_file_url']            = str_replace("https://", "http://", $availableDemos['data'][$key]['import_file_url']);
                        $availableDemos['data'][$key]['import_widget_file_url']     = str_replace("https://", "http://", $availableDemos['data'][$key]['import_widget_file_url']);
                        $availableDemos['data'][$key]['import_customizer_file_url'] = str_replace("https://", "http://", $availableDemos['data'][$key]['import_customizer_file_url']);
                    }
                    
                    if ( ! DemoImportIntegration::isDevMode()) {
                        set_transient($transientKey, $availableDemos, 86400);
                    } else {
                        delete_transient($transientKey);
                    }
                } else {
                    $availableDemos = array();
                }
            }
        }
        
        return $availableDemos;
    }
    
    public static function getDemosData()
    {
        if ( ! DemoImportIntegration::$_demos_data) {
            DemoImportIntegration::$_demos_data = DemoImportIntegration::_getDemosData();
        }
        
        return DemoImportIntegration::$_demos_data;
    }
    
    
    public static function isPluginInstalled($path)
    {
        return file_exists(WP_PLUGIN_DIR . "/{$path}");
    }
    
    public static function isPluginActive($path)
    {
        return (
            in_array($path, (array)get_option('active_plugins', array()))
            || in_array($path, (array)get_option('active_sitewide_plugins', array()))
        );
    }
    
    public static function getPluginStatus($path)
    {
        $status = "not-installed";
        if (self::isPluginInstalled($path)) {
            $status = "installed";
            
            if (self::isPluginActive($path)) {
                $status = "active";
            }
            
        }
        
        return $status;
    }
    
    public static function getDemoDataOCDIFormat()
    {
        $demo_data = DemoImportIntegration::getDemosData();
        $data      = isset($demo_data['data']) ? $demo_data['data'] : array();
        
        $nonces = array();
        foreach ($data as $key => $item) {
            if (isset($item['plugins']) && is_array($item['plugins'])) {
                foreach ($item['plugins'] as $plugin => $plugin_data) {
                    
                    if ( ! isset($nonces[$plugin])) {
                        $nonces[$plugin] = wp_create_nonce('activate-plugin_' . $plugin);
                    }
                    
                    $data[$key]['plugins'][$plugin]['status']        = DemoImportIntegration::getPluginStatus($plugin);
                    $data[$key]['plugins'][$plugin]['activate_link'] = add_query_arg(array(
                        '_wpnonce' => $nonces[$plugin],
                        'action'   => 'activate',
                        'plugin'   => $plugin,
                    ), network_admin_url('plugins.php'));
                }
                
            }
        }
        
        return $data;
    }
    
    
    public static function isDemoInDevMode($index)
    {
        $data = DemoImportIntegration::getDemoDataOCDIFormat();
        
        if (isset($data[$index]) && isset($data[$index]['is_dev']) && $data[$index]['is_dev']) {
            return true;
        }
        
        return false;
    }
    
    public static function isProOnly($index)
    {
        $data = DemoImportIntegration::getDemoDataOCDIFormat();
        
        return (isset($data[$index]) && isset($data[$index]['pro_only']) && $data[$index]['pro_only']);
    }
    
    
    public static function getPreviewURL($index)
    {
        $data   = DemoImportIntegration::getDemoDataOCDIFormat();
        $result = false;
        if (isset($data[$index]) && isset($data[$index]['preview_url']) && $data[$index]['preview_url']) {
            $result = $data[$index]['preview_url'];
            $result = untrailingslashit($result);
        }
        
        return $result;;
    }
    
    public static function getCurrentDemoIndex()
    {
        
        if (class_exists('\\OCDI\\OneClickDemoImport')) {
            $current_import_data = \OCDI\OneClickDemoImport::get_instance()->get_current_importer_data();
            
            if (isset($current_import_data['selected_index'])) {
                return $current_import_data['selected_index'];
            }
        }
        
        return false;
    }
    
    
    public static function getCurrentDemoData()
    {
        $index = DemoImportIntegration::getCurrentDemoIndex();
        
        
        if ($index !== false) {
            $demos = DemoImportIntegration::getDemoDataOCDIFormat();
            
            if (isset($demos[$index])) {
                return $demos[$index];
            }
        }
        
        return false;
    }
    
    public static function getDemoPlugins($demoData)
    {
        $plugins = isset($demoData['plugins']) ? $demoData['plugins'] : array();
        $result  = array();
        
        if (empty($plugins)) {
            return $plugins;
        }
        
        foreach ($plugins as $path => $name) {
            
            if ( ! is_plugin_active($path)) {
                $result[$path] = $name;
            }
        }
        
        return $result;
    }
    
    
    public static function getCategories()
    {
        
        foreach (DemoImportIntegration::getDemoDataOCDIFormat() as $item) {
            if ( ! empty($item['categories']) && is_array($item['categories'])) {
                foreach ($item['categories'] as $category) {
                    if ($category) {
                        $categories[sanitize_key($category)] = $category;
                    }
                }
            }
        }
        
        if (empty($categories)) {
            $categories = array();
        }
        
        asort($categories);
        
        return $categories;
    }
    
}
