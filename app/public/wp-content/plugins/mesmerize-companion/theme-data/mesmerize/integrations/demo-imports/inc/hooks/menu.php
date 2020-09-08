<?php


function mesmerize_prepare_menus_before_ocdi($data)
{
    delete_transient('mesmerize_menu_items_keys_match');
    $menus = isset($data['menus']) ? $data['menus'] : array("primary" => "Main Menu");
    
    foreach ($menus as $location => $menuName) {
        $terms = get_term_by('name', $menuName, 'nav_menu');
        
        if ($terms) {
            if ( ! is_array($terms)) {
                $terms = array($terms);
            }
            
            foreach ($terms as $term) {
                /** @var WP_Term $term */
                wp_update_term($term->term_id, $term->taxonomy, array(
                    'name' => $term->name . ' [OLD]',
                    'slug' => $term->slug . "_" . time(),
                ));
                
            }
        }
    }
    
    \ExtendThemes\DemoImportIntegration\ContentHooks::prepareContentBeforeOCDI('nav_menu_item');
}

add_action('pt-ocdi/before_content_import', 'mesmerize_prepare_menus_before_ocdi', 10, 1);


function mesmerize_prepare_menus_after_ocdi($data)
{
    $menus       = isset($data['menus']) ? $data['menus'] : array("primary" => "Main Menu");
    $cachedTerms = array();
    $modValue    = array();
    foreach ($menus as $location => $menuName) {
        
        if ( ! isset($cachedTerms[$menuName])) {
            $cachedTerms[$menuName] = get_term_by('name', $menuName, 'nav_menu');
        }
        $term = $cachedTerms[$menuName];
        
        if ($term) {
            $modValue[$location] = $term->term_id;
        }
    }
    
    if (count($modValue)) {
        set_theme_mod('nav_menu_locations', $modValue);
    }
    
    $demoURLBase = untrailingslashit($data['preview_url']);
    $demoURLBase = preg_replace('#^https?://#', '', $demoURLBase);
    $blogURL     = untrailingslashit(home_url());
    $menu_map    = (array)get_site_transient('mesmerize_menu_items_keys_match');
    delete_transient('mesmerize_menu_items_keys_match');
    
    foreach ($cachedTerms as $term) {
        $menuItems = wp_get_nav_menu_items($term->term_id);
        
        /** @var WP_Post $menuItem */
        foreach ($menuItems as $menuItem) {
            if ($menuItem->type === 'custom' && strpos($menuItem->url, $demoURLBase) !== false) {
                
                $newURL = str_replace("https://{$demoURLBase}", $blogURL, $menuItem->url);
                $newURL = str_replace("http://{$demoURLBase}", $blogURL, $newURL);
                $newURL = str_replace($demoURLBase, $blogURL, $newURL);
                
                wp_update_nav_menu_item($term->term_id, $menuItem->ID, array(
                    'menu-item-object-id'   => $menuItem->object_id,
                    'menu-item-object'      => $menuItem->object,
                    'menu-item-parent-id'   => isset($menu_map[$menuItem->menu_item_parent]) ? $menu_map[$menuItem->menu_item_parent] : $menuItem->menu_item_parent,
                    'menu-item-position'    => $menuItem->menu_order,
                    'menu-item-type'        => $menuItem->type,
                    'menu-item-title'       => $menuItem->post_title,
                    'menu-item-url'         => $newURL,
                    'menu-item-description' => $menuItem->post_content,
                    'menu-item-attr-title'  => $menuItem->post_excerpt,
                    'menu-item-target'      => $menuItem->target,
                    'menu-item-classes'     => $menuItem->classes,
                    'menu-item-xfn'         => $menuItem->xfn,
                    'menu-item-status'      => $menuItem->post_status,
                ));
            }
        }
    }
}

add_action('pt-ocdi/after_import', 'mesmerize_prepare_menus_after_ocdi', 20, 1);


add_action('wxr_importer.processed.post', function ($post_id, $data, $meta, $comments, $terms) {
    if ('nav_menu_item' === $data['post_type']) {
        $transient = get_site_transient('mesmerize_menu_items_keys_match');
        
        if ( ! $transient) {
            $transient = array();
        }
        
        $transient[$data['post_id']] = $post_id;
        
        set_site_transient('mesmerize_menu_items_keys_match', $transient);
    }
}, 10, 5);


function mesmerize_set_permalinks_structure()
{
    global $wp_rewrite;
    
    /** @var WP_Rewrite $wp_rewrite */
    $wp_rewrite->set_permalink_structure('/%postname%/');
    update_option("rewrite_rules", false);
    $wp_rewrite->flush_rules(true);
}

add_action('pt-ocdi/after_import', 'mesmerize_set_permalinks_structure', 20, 1);
