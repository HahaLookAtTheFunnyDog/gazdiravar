<?php

function mesmerize_force_check_updates()
{
    mesmerize_force_check_plugins_update();
    mesmerize_force_check_themes_update();
}

function mesmerize_force_check_plugins_update()
{
    $transient = get_site_transient('update_plugins');
    if ($transient) {
        foreach ($transient->checked as $path => $version) {
            if (strpos($path, "mesmerize-companion") !== false) {
                if (isset($transient->no_update[$path])) {
                    unset($transient->no_update[$path]);
                    unset($transient->checked[$path]);
                    set_site_transient('update_plugins', $transient);
                }
                break;
            }
        }
    }
}

function mesmerize_force_check_themes_update()
{
    $transient = get_site_transient('update_themes');
    if ($transient) {
        if ( ! isset($transient->response['mesmerize-pro'])) {
            if (isset($transient->checked['mesmerize-pro'])) {
                unset($transient->checked['mesmerize-pro']);
                set_site_transient('update_themes', $transient);
            }
        }
    }
}


function mesmerize_get_available_updates()
{
    
    $needs_update = array();
    
    $themes = get_theme_updates();
    
    $current_theme = get_template();
    
    if ($themes && isset($themes['mesmerize-pro']) && 'mesmerize-pro' === $current_theme) {
        $theme = $themes['mesmerize-pro'];
        
        $current_version = $theme->get('Version');
        if (version_compare($current_version, $theme->update['new_version'], "<")) {
            if (strtolower($current_version) !== "@@buildnumber@@") {
                $needs_update['themes'] = array(
                    array(
                        "version" => $theme->update['new_version'],
                        "name"    => $theme->get("Name"),
                    ),
                );
            }
        }
    }
    
    if ($themes && isset($themes['mesmerize']) && 'mesmerize' === $current_theme) {
        $theme                  = $themes['mesmerize'];
        $needs_update['themes'] = isset($needs_update['themes']) ? $needs_update['themes'] : array();
        /** @var WP_Theme $theme */
        $current_version = $theme->get('Version');
        
        if (version_compare($current_version, $theme->update['new_version'], "<")) {
            if (strtolower($current_version) !== "@@buildnumber@@") {
                $needs_update['themes'][] = array(
                    "version" => $theme->update['new_version'],
                    "name"    => $theme->get("Name"),
                );
            }
        }
        
        
    }
    
    $plugins = get_plugin_updates();
    if ($plugins) {
        foreach ($plugins as $file => $plugin) {
            $current_version = $plugin->Version;
            if (version_compare($current_version, $plugin->update->new_version, "<")) {
                if (strtolower($current_version) !== "@@buildnumber@@") {
                    if ($plugin->TextDomain == 'mesmerize-companion') {
                        $needs_update['plugins'] = array(
                            array(
                                "version" => $plugin->update->new_version,
                                "name"    => $plugin->Name,
                            ),
                        );
                    }
                }
            }
        }
    }
    
    return $needs_update;
}

function mesmerize_get_updates_msg()
{
    $updates = mesmerize_get_available_updates();
    
    $msg = "";
    
    if (isset($updates['themes'])) {
        for ($i = 0; $i < count($updates['themes']); $i++) {
            $update = $updates['themes'][$i];
            $msg    .= "<h1>New version (" . $update['version'] . ") available for " . $update['name'] . "</h1>";
        }
    }
    
    if (isset($updates['plugins'])) {
        for ($i = 0; $i < count($updates['plugins']); $i++) {
            $update = $updates['plugins'][$i];
            $msg    .= "<h1>New version (" . $update['version'] . ") available for " . $update['name'] . "</h1>";
        }
    }
    
    if ($msg) {
        $msg .= '<h2>Please update to the latest versions before editing in Customizer.</h2>';
        $msg .= '<br/>';
        $msg .= '<a href="' . get_admin_url(null, "update-core.php") . '" class="button button-orange">Go to updates</a> ';
    }
    
    return $msg;
}

add_action("admin_init", function () {
    global $pagenow;
    
    try {
        if ('customize.php' === $pagenow) {
            $theme = wp_get_theme();
            
            if ($theme->template == "mesmerize-pro" || ($theme->parent() && $theme->parent()->template == "mesmerize-pro")) {
                mesmerize_force_check_themes_update();
                
                if (function_exists("mesmerize_pro_require") && ! class_exists("Wp_License_Manager_Client")) {
                    mesmerize_pro_require('/inc/class-wp-license-manager-client.php');
                }
                
                if (class_exists("Wp_License_Manager_Client")) {
                    $licence_manager = new Wp_License_Manager_Client(
                        'mesmerize-pro',
                        'Mesmerize PRO',
                        'mesmerize-pro',
                        'http://onepageexpress.com/api/license-manager/v1/',
                        'theme'
                    );
                }
                
                wp_update_themes();
            }
            
        }
    } catch (Exception $e) {
    }
});

$theme           = wp_get_theme();
$__is_pro_theme  = ($theme->template == "mesmerize-pro" || ($theme->parent() && $theme->parent()->template == "mesmerize-pro"));
$__is_free_theme = ($theme->template == "mesmerize" || ($theme->parent() && $theme->parent()->template == "mesmerize"));

if ($theme && ($__is_free_theme || $__is_pro_theme)) {
    add_action('customize_controls_print_footer_scripts', function () {
        ?>
        <script type="text/javascript">
            CP_Customizer.addModule(function () {
                CP_Customizer.bind(CP_Customizer.events.PREVIEW_LOADED, function () {
                    var updates_msg = <?php echo json_encode(mesmerize_get_updates_msg()); ?>;
                    if (updates_msg) {
                        CP_Customizer.popUpInfo('Updates available',
                            '<div class="pro-popup-preview-container">' +
                            updates_msg +
                            '</div>'
                        );
                    }
                    ;
                });
            });
        </script>
        <?php
    }, 11);
}

/* 
	enable theme updates, by sending the version parameter
*/

add_filter('http_request_args', function ($r, $url) {
    if (strpos($url, "mesmerize-pro") !== false) {
        $r['body'] = array("v" => "1.0");
    }
    
    return $r;
}, PHP_INT_MAX, 2);

/* 
	fix updates apearring for pro child theme instead of pro theme
*/

add_filter('pre_set_site_transient_update_themes', function ($transient) {
    
    if (property_exists($transient, 'response') && is_array($transient->response)) {
        foreach ($transient->response as $slug => $value) {
            if ($slug != "mesmerize-pro" && strpos($value["package"], "mesmerize-pro") !== false) {
                
                $theme = wp_get_theme();
                if ($theme->parent() && $theme->parent()->template == "mesmerize-pro") {
                    // if different version, add as pro update//
                    
                    if ($theme->parent()->version != $value['new_version']) {
                        $transient->response['mesmerize-pro'] = $value;
                        $transient->checked['mesmerize-pro']  = $theme->parent()->version;
                    }
                }
                
                unset($transient->response[$slug]);
                
                if (isset($transient->checked[$slug])) {
                    unset($transient->checked[$slug]);
                }
            }
        }
    }
    
    return $transient;
}, PHP_INT_MAX);
