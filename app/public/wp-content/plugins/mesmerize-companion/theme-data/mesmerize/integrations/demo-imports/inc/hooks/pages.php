<?php

function mesmerize_prepare_pages_before_ocdi($data)
{
    \ExtendThemes\DemoImportIntegration\ContentHooks::prepareContentBeforeOCDI('page');
    delete_site_transient('mesmerize_ocdi_import_page_mod');
    
}

add_action('pt-ocdi/before_content_import', 'mesmerize_prepare_pages_before_ocdi', 20, 1);


function mesmerize_prepare_pages_after_ocdi($data)
{
    
    $pages = isset($data['pages']) ? $data['pages'] : array();
    $pages = array_merge(
        array(
            "front_page" => "Front Page",
            "blog_page"  => "Blog",
        ),
        $pages);
    
    
    $front_page   = get_page_by_title($pages['front_page']);
    $blog_page    = get_page_by_title($pages['blog_page']);
    $blog_page_id = null;
    
    if ( ! $blog_page) {
        $blog_page_id = wp_insert_post(
            array(
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
                'post_name'      => 'blog',
                'post_title'     => 'Blog',
                'post_status'    => 'publish',
                'post_type'      => 'page',
            )
        );
    } else {
        $blog_page_id = $blog_page->ID;
    }
    
    update_option('show_on_front', 'page');
    update_option('page_on_front', $front_page->ID);
    update_option('page_for_posts', $blog_page->ID);
    
    // update page theme mod
    $pagesModsTransient = get_site_transient('mesmerize_ocdi_import_page_mod');
    delete_site_transient('mesmerize_ocdi_import_page_mod');
    
    $guidsMatch = array();
    foreach ((array)$pagesModsTransient as $oldID => $data) {
        $guidsMatch[$data['guid']] = array(
            'old_id' => $oldID,
            'new_id' => null,
        );
    }
    
    if (count($guidsMatch)) {
        $guids        = array_keys($guidsMatch);
        $placeholders = array_fill(0, count($guids), '%s');
        $format       = implode(', ', $placeholders);
        
        global $wpdb;
        $query = $wpdb->prepare("SELECT ID, guid FROM $wpdb->posts WHERE guid IN($format)", $guids);
        
        $result = $wpdb->get_results($query);
        
        foreach ((array)$result as $item) {
            if (isset($guidsMatch[$item->guid])) {
                $guidsMatch[$item->guid]['new_id'] = $item->ID;
            }
        }
        
        foreach ($guidsMatch as $guidMatch) {
            if ($guidMatch['new_id']) {
                $pageStyle = get_theme_mod("custom_content_style_{$guidMatch['old_id']}", null);
                if ($pageStyle) {
                    remove_theme_mod("custom_content_style_{$guidMatch['old_id']}");
                    set_theme_mod("custom_content_style_{$guidMatch['new_id']}", $pageStyle);
                }
            }
        }
    }
    
    
}

add_action('pt-ocdi/after_import', 'mesmerize_prepare_pages_after_ocdi', 20, 1);


add_filter('wxr_importer.pre_process.post', function ($data, $meta, $comments, $terms) {
    
    if (isset($data['post_type']) && $data['post_type'] === 'page') {
        $pagesModsTransient = get_site_transient('mesmerize_ocdi_import_page_mod');
        
        if ( ! $pagesModsTransient) {
            $pagesModsTransient = array();
        }
        
        $pagesModsTransient[$data['post_id']] = array(
            'guid' => $data['guid'],
        );
        
        set_site_transient('mesmerize_ocdi_import_page_mod', $pagesModsTransient);
        
        $current_demo_data = \ExtendThemes\DemoImportIntegration\DemoImportIntegration::getCurrentDemoData();
        
        if ($current_demo_data) {
            $from_url = $current_demo_data['preview_url'];
            $url      = preg_replace('#^https?://#', '', $from_url);
            
            $url    = untrailingslashit($url);
            $to_url = site_url();
            $to_url = untrailingslashit($to_url);
            
            $data['post_content'] = str_replace('href="' . "https://{$url}", 'href="' . $to_url, $data['post_content']);
            $data['post_content'] = str_replace('href="' . "http://{$url}", 'href="' . $to_url, $data['post_content']);
            $data['post_content'] = str_replace('href="' . "{$url}", 'href="' . $to_url, $data['post_content']);
            
            $data['post_content'] = str_replace("href='" . "https://{$url}", "href='" . $to_url, $data['post_content']);
            $data['post_content'] = str_replace("href='" . "http://{$url}", "href='" . $to_url, $data['post_content']);
            $data['post_content'] = str_replace("href='" . "{$url}", "href='" . $to_url, $data['post_content']);
        }
    }
    
    return $data;
}, 10, 4);
