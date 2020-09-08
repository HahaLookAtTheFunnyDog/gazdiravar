<?php
/**
 * Created by PhpStorm.
 * User: yupal
 * Date: 6/20/2018
 * Time: 6:16 PM
 */

namespace ExtendThemes\DemoImportIntegration;


class ContentHooks
{
    public static function prepareContentBeforeOCDI($post_type)
    {
        $items         = get_posts(
            array(
                'posts_per_page' => -1,
                'post_type'      => $post_type,
                'post_status'    => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            )
        );
        $guidsToUpdate = array();
        
        foreach ($items as $item) {
            /** @var WP_Post $item */
            $newGuid = (strpos($item->guid, '-old') !== false) ? $item->guid : $item->guid . "-old";
            
            
            switch ($post_type) {
                case "page":
                    $newTitle = (strpos($item->post_title, '[OLD]') !== false) ? $item->post_title : $item->post_title . " [OLD]";
                    $newName  = (strpos($item->post_name, '-before-import') !== false) ? $item->post_name : sanitize_title($item->post_name . "-before-import");
                    wp_update_post(array(
                        'ID'         => $item->ID,
                        'post_name'  => $newName,
                        'post_title' => $newTitle,
                    ));
                    
                    delete_post_meta($item->ID, 'is_mesmerize_front_page');
                    wp_trash_post($item->ID);
                    
                    if ($newGuid !== $item->guid) {
                        $guidsToUpdate[$item->ID] = $newGuid;
                    }
                    break;
                case 'nav_menu_item':
                    if ($newGuid !== $item->guid) {
                        $guidsToUpdate[$item->ID] = $newGuid;
                    }
                    
                    break;
            }
            
        }
        
        global $wpdb;
        
        foreach ($guidsToUpdate as $id => $guid) {
            $query = $wpdb->prepare("UPDATE  {$wpdb->prefix}posts SET guid=%s WHERE ID=%d", $guid, $id);
            $wpdb->query($query);
        }
    }
}
