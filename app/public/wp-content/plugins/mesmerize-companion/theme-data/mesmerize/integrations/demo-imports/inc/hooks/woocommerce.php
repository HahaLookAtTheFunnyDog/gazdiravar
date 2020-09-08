<?php


function mesmerize_prepare_wc_shortcodes_in_pages_before_ocdi($data)
{
    delete_site_transient('mesmerize_ocdi_import_wc_categories');
}

add_action('pt-ocdi/before_content_import', 'mesmerize_prepare_wc_shortcodes_in_pages_before_ocdi', 20, 1);


add_filter('wxr_importer.pre_process.term', function ($data, $meta) {

    if (isset($data['taxonomy']) && $data['taxonomy'] === 'product_cat') {
        $transient = get_site_transient('mesmerize_ocdi_import_wc_categories');

        if ( ! $transient) {
            $transient = array();
        }

        $transient[$data['slug']] = array(
            'id' => $data['id'],
        );

        set_site_transient('mesmerize_ocdi_import_wc_categories', $transient);
    }

    return $data;


}, 10, 2);


function mesmerize_prepare_wc_shortcodes_in_pages_after_ocdi()
{
    $items   = get_posts(
        array(
            'posts_per_page' => -1,
            'post_type'      => 'page',
            'post_status'    => array('publish'),
        )
    );
    $pattern = get_shortcode_regex(array(
        'mesmerize_display_woocommerce_items',
    ));

    $product_cats = get_terms('product_cat');
    $wcCategories = array();

    if (is_array($product_cats)) {
        foreach ($product_cats as $cat) {
            $wcCategories[$cat->slug] = $cat->term_id;
        }
    }

    $transient = get_site_transient('mesmerize_ocdi_import_wc_categories');

    if ( ! $transient) {
        $transient = array();
    }

    $cats_match = array();
    foreach ($wcCategories as $slug => $id) {
        if (isset($transient[$slug])) {
            $cats_match[$transient[$slug]['id']] = $id;
        }
    }

    foreach ($items as $item) {
        /** @var WP_Post $item */
        $content             = $item->post_content;
        $attsValuesToReplace = array();
        $content             = preg_replace_callback("/$pattern/", function ($m) use (&$attsValuesToReplace, $cats_match) {
            $shortcode = $m[0];
            $tag       = $m[2];
            $attr      = shortcode_parse_atts($m[3]);
            $dataAttr  = esc_attr(trim($shortcode, '[]'));

            if (isset($attr['categories'])) {
                $categories    = explode(',', $attr['categories']);
                $newCategories = array();
                foreach ($categories as $cat) {
                    if (isset($cats_match[$cat])) {
                        $newCategories[] = $cats_match[$cat];
                    }
                }

                $attr['categories'] = implode(',', $newCategories);
            }

            $attrString = array();
            foreach ($attr as $key => $value) {
                $value         = esc_attr($value);
                $attrString [] = "{$key}=\"$value\"";
            }
            $newShortcode = '[mesmerize_display_woocommerce_items ' . implode(" ", $attrString) . ']';

            $attsValuesToReplace[$dataAttr] = esc_attr(trim($newShortcode, '[]'));

            return $newShortcode;

        }, $content);

        foreach ($attsValuesToReplace as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        wp_update_post(array(
            'ID'           => $item->ID,
            'post_content' => $content,
        ));
    }
}


add_action('pt-ocdi/after_import', 'mesmerize_prepare_wc_shortcodes_in_pages_after_ocdi', 20, 1);

function mesmerize_prepare_wc_pages_after_ocdi($data)
{
    $pages = isset($data['wc_pages']) ? $data['wc_pages'] : array(
        'cart'      => 'Cart',
        'checkout'  => 'Checkout',
        'myaccount' => 'My account',
        'shop'      => 'Shop',
    );

    foreach ($pages as $slug => $name) {
        $page = get_page_by_title($name);
        if ($page) {
            update_option("woocommerce_{$slug}_page_id", $page->ID, 'yes');
        }
    }
}

add_action('pt-ocdi/after_import', 'mesmerize_prepare_wc_pages_after_ocdi', 20, 1);
