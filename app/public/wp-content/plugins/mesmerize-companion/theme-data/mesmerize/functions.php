<?php

require_once $this->themeDataPath("/updates.php");
require_once $this->themeDataPath("/custom-style.php");
require_once $this->themeDataPath("/options/overlap.php");

if(!function_exists('mesmerize_get_upgrade_link')){
    function mesmerize_get_upgrade_link($args = array(), $hash = "")
    {
        $base_url = "https://extendthemes.com/go/mesmerize-upgrade/";
        $url      = add_query_arg($args, $base_url);
        
        if ($hash = trim($hash)) {
            $hash = "#" . $hash;
        }
        
        $url = $url . esc_url($hash);
        
        return apply_filters('mesmerize_upgrade_url', $url, $base_url, $args, $hash);
    }
    
}

//add_filter('mesmerize_can_show_demo_content', "__return_true");
add_filter('mesmerize_show_inactive_plugin_infos', "__return_false");


add_filter('mesmerize_full_width_page', function ($value) {

    if (Mesmerize\Companion::instance()->isMaintainable()) {
        $value = true;
    }

    return $value;
});

add_filter('mesmerize_page_content_wrapper_class', function ($class) {
    if (mesmerize_is_front_page() || apply_filters('mesmerize_full_width_page', false)) {
        $class = array_diff($class, array('gridContainer'));
    }

    return $class;
});


add_filter('mesmerize_page_content_class', function ($class) {
    if (Mesmerize\Companion::instance()->isMaintainable()) {
        $class[] = 'no-padding';
        $class   = array_diff($class, array('gridContainer'));
    }

    return $class;
});

function mesmerize_companion_get_post_thumbnail()
{
    ob_start();
    the_post_thumbnail('post-thumbnail', array('class' => 'blog-postimg'));
    $thumbnail = trim(ob_get_clean());

    if (empty($thumbnail)) {
        if (is_customize_preview() || 1) {
            return "<img src='https://placeholdit.imgix.net/~text?txtsize=38&bg=FF7F66&txtclr=FFFFFFe&w=400&h=250' class='blog-postimg'/>";
        } else {
            return $thumbnail;
        }
    }

    return $thumbnail;
}

function mesmerize_companion_latest_news_excerpt_length()
{
    return 30;
}

function mesmerize_companion_latest_excerpt_more()
{
    return "[&hellip;]";
}

function mesmerize_companion_latest_news($attrs)
{
    ob_start(); ?>
    <?php

    $atts = shortcode_atts(array(
        'columns'        => "4",
        'tablet_columns' => "6",
        'item_class'     => 'card y-move bordered',
        'posts'          => '',
    ), $attrs);

    $recentPosts = new WP_Query();

    $cols        = intval($atts['columns']);
    $tablet_cols = intval($atts['tablet_columns']);

    $post_numbers = ($atts['posts']) ? $atts['posts'] : 12 / $cols;

    add_filter('excerpt_length', 'mesmerize_companion_latest_news_excerpt_length');
    add_filter('excerpt_more', 'mesmerize_companion_latest_excerpt_more');

    ?>
    <div class="row center-sm content-left-sm">
        <?php
        $recentPosts->query('posts_per_page=' . $post_numbers . ';post_status=publish;post_type=post;ignore_sticky_posts=1;');
        while ($recentPosts->have_posts()):
            $recentPosts->the_post();
            if (is_sticky()) {
                continue;
            }
            ?>
            <div id="post-<?php the_ID(); ?>" class="col-sm-<?php echo $tablet_cols; ?> col-md-<?php echo $cols; ?> space-bottom space-bottom-xs">
                <div class="post-content <?php echo $atts['item_class']; ?>">
                    <?php mesmerize_print_post_thumb(); ?>
                    <div class="col-padding col-padding-xs">
                        <h3 class="post-title space-bottom-small">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        <?php the_excerpt(); ?>
                        <a class="read-more link" href="<?php echo get_permalink(); ?>">
                            <?php \Mesmerize\Companion::echoMod('latest_news_read_more', 'Read more'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
    <?php
    remove_filter('excerpt_length', 'mesmerize_companion_latest_news_excerpt_length');
    remove_filter('excerpt_more', 'mesmerize_companion_latest_excerpt_more');
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode('mesmerize_latest_news', 'mesmerize_companion_latest_news');


function mesmerize_companion_blog_link()
{
    if ('page' == get_option('show_on_front')) {
        if (get_option('page_for_posts')) {
            return esc_url(get_permalink(get_option('page_for_posts')));
        } else {
            return esc_url(home_url('/?post_type=post'));
        }
    } else {
        return esc_url(home_url('/'));
    }
}


function mesmerize_companion_contact_form($attrs = array())
{
    $atts = shortcode_atts(array(
        'shortcode' => "",
    ), $attrs);

    $contact_shortcode = "";
    if ($atts['shortcode']) {
        $contact_shortcode = "[" . html_entity_decode(html_entity_decode($atts['shortcode'])) . "]";
    }
    ob_start();

    if ($contact_shortcode !== "") {
        echo do_shortcode($contact_shortcode);
    } else {
        echo '<p style="text-align:center;color:#ababab">' . __('Contact form will be displayed here. To activate it you have to set the "contact form shortcode" parameter in Customizer.', 'mesmerize-companion') . '</p>';
    }

    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode('mesmerize_contact_form', 'mesmerize_companion_contact_form');


add_filter('cloudpress\template\page_content', function ($content) {
    $content = str_replace('[mesmerize_blog_link]', mesmerize_companion_blog_link(), $content);
    $content = str_replace('[tag_companion_uri]', \Mesmerize\Companion::instance()->themeDataURL(), $content);

    return $content;
});


add_filter('cloudpress\companion\ajax_cp_data', function ($data, $companion, $filter) {

    if ($filter !== "sections") {
        return $data;
    }

    /** @var \Mesmerize\Companion $companion */
    $contentSections  = $companion->loadPHPConfig($companion->themeDataPath("/sections/sections.php"));
    $data['sections'] = $contentSections;

    $showPro = apply_filters('mesmerize_show_info_pro_messages', true);

    if ($showPro) {
        $proSections      = $companion->loadPHPConfig($companion->themeDataPath("/sections/pro-only-sections.php"));
        $data['sections'] = array_merge($contentSections, $proSections);

    }


    if (apply_filters('mesmerize_show_custom_section', false)) {
        $customSectionContentFile = $companion->themeDataPath("/assets/custom-section.html");
        $customSectionContent     = file_get_contents($customSectionContentFile);

        $data['sections'][] = array(
            "index"       => 1,
            "id"          => "custom-section",
            "elementId"   => "custom-section",
            "type"        => "section-available",
            "name"        => "Custom Section",
            "content"     => $customSectionContent,
            "thumb"       => "\/\/onepageexpress.com\/default-assets\/previews\/custom-section.png",
            "preview"     => "\/\/onepageexpress.com\/default-assets\/previews\/custom-section.png",
            "description" => "simple custom section",
            "category"    => "custom",
            "prepend"     => false,
            "pro"         => true,
        );
    }

    return $data;
}, 10, 3);

add_filter('cloudpress\companion\ajax_cp_data', function ($data, $companion, $filter) {

    if ($filter !== "sections") {
        return $data;
    }

    $currentData = $data;

    $data = \Mesmerize\Companion::filterArrayDefaults($data);

    $data = isset($data['sections']) ? $data['sections'] : array();
    foreach ($data as $id => $item) {
        if ( ! isset($item['category'])) {
            $item['category'] = 'general';
        }

        $category = strtolower($item['category']);

        if ( ! isset($categorized[$category])) {
            $categorized[$category] = array();
        }

        $categorized[$category][] = $item;
    }

    $categorized = apply_filters('cloudpress\customizer\control\content_sections\data', $categorized);

    $currentData['sections'] = $categorized;

    return $currentData;

}, PHP_INT_MAX, 3);

add_action('cloudpress\template\load_assets', function ($companion) {
    $ver = $companion->version;


    if (apply_filters('mesmerize_load_bundled_version', true)) {

        /** @var \Mesmerize\Companion $companion */
        wp_enqueue_script('companion-bundle', $companion->themeDataURL('/assets/js/companion.bundle.min.js'), array(), $ver, true);
        wp_enqueue_style('companion-bundle', $companion->themeDataURL('/assets/css/companion.bundle.min.css'), array(), $ver);

        return;
    }

    wp_enqueue_style($companion->getThemeSlug() . '-common-css', $companion->themeDataURL('/assets/css/common.css'), array($companion->getThemeSlug() . '-style'), $ver);
    wp_enqueue_style('companion-page-css', $companion->themeDataURL('/sections/content.css'), array(), $ver);

    wp_register_script('companion-' . $companion->getThemeSlug(), null, array('jquery',), $ver);

    if ( ! is_customize_preview()) {
        wp_enqueue_script('companion-cotent-swap', $companion->themeDataURL('/assets/js/HoverFX.js'), array('companion-' . $companion->getThemeSlug()), $ver);
    }

    wp_enqueue_script('companion-countup', $companion->themeDataURL('/assets/js/countup.js'), array('companion-' . $companion->getThemeSlug()), $ver);


    wp_enqueue_script('companion-scripts', $companion->themeDataURL('/sections/scripts.js'), array('companion-' . $companion->getThemeSlug()), $ver);
});


function mesmerize_companion_page_builder_get_css_value($value, $unit = false)
{
    $noUnitValues = array('inherit', 'auto', 'initial');
    if ( ! in_array($value, $noUnitValues)) {
        return $value . $unit;
    }

    return $value;
}


function mesmerize_companion_get_front_page_content($companion)
{
    $defaultSections = apply_filters(
        'mesmerize_default_sections',
        array(
            "overlappable-4",
            "about-1",
            "features-12-card-bordered",
            "content-2",
            "content-3",
            "portfolio-4",
            "testimonials-5",
            "cta-blue-section",
            "team-2",
            "blog-section",
            "contact-1",
        )
    );

    $alreadyColoredSections = apply_filters('mesmerize_already_colored_sections', array("contact-1", "cta-blue-section"));

    /** @var \Mesmerize\Companion $companion */
    $availableSections = apply_filters('mesmerize_available_sections', $companion->loadPHPConfig($companion->themeDataPath("/sections/sections.php")), $companion);

    $content = "";

    $colors     = array('#ffffff', '#f5fafd');
    $colorIndex = 0;

    foreach ($defaultSections as $ds) {
        foreach ($availableSections as $as) {
            if ($as['id'] == $ds) {
                $_content = $as['content'];

                if (strpos($_content, 'data-bg="transparent"') === false && ! in_array($ds, $alreadyColoredSections)) {
                    $_content   = preg_replace('/\<div/', '<div style="background-color:' . $colors[$colorIndex] . '" ', $_content, 1);
                    $colorIndex = $colorIndex ? 0 : 1;
                } else {
                    $colorIndex = 0;
                }

                $_content = preg_replace('/\<div/', '<div id="' . $as['elementId'] . '" ', $_content, 1);

                $content .= $_content;
                break;
            }
        }
    }

    return $content;
}

add_filter('cloudpress\companion\front_page_content', function ($content, $companion) {
    $content = mesmerize_companion_get_front_page_content($companion);

    return \Mesmerize\Companion::filterDefault($content);
}, 10, 2);


add_filter('mesmerize_supports-header-slider', '__return_true');

add_filter('cloudpress\customizer\control\content_sections\data', function ($data) {
    $categories = array(
        'overlappable',
        'about',
        'features',
        'content',
        'cta',
        'counters',
        'FAQ',
        'gallery',
        'portfolio',
        'pricing',
        'promo',
        'testimonials',
        'clients',
        'team',
        'latest_news',
        'contact',
        'woocommerce',
    );

    $result = array();

    foreach ($categories as $cat) {
        if (isset($data[$cat])) {
            $result[$cat] = $data[$cat];
            unset($data[$cat]);
        }
    }

    $result = array_merge($result, $data);

    return $result;
});

add_filter('cloudpress\customizer\control\content_sections\category_label', function ($label, $category) {

    switch ($category) {
        case 'latest_news':
            $label = __("Latest News", 'mesmerize-companion');
            break;

        case 'cta':
            $label = __("Call to action", 'mesmerize-companion');
            break;

        default:
            $label = __($label, 'mesmerize-companion');
            break;
    }

    return $label;
}, 10, 2);


add_action('edit_form_after_title', 'mesmerize_companion_add_maintainable_filter');

function mesmerize_companion_add_maintainable_filter($post)
{
    $companion    = \Mesmerize\Companion::instance();
    $maintainable = $companion->isMaintainable($post->ID);

    add_editor_style(get_template_directory_uri() . "/style.css");
    add_editor_style(get_stylesheet_uri());

    add_editor_style($companion->themeDataURL('/assets/css/common.css'));
    add_editor_style($companion->themeDataURL('/sections/content.css'));
//    add_editor_style($companion->themeDataURL('/assets/css/HoverFX.css'));
    add_editor_style(get_template_directory_uri() . '/assets/font-awesome/font-awesome.min.css');


    if ($maintainable) {
        add_filter('tiny_mce_before_init', 'mesmerize_companion_maintainable_pages_tinymce_init');
    }
}

add_filter('body_class', function ($classes) {
    $companion    = \Mesmerize\Companion::instance();
    $maintainable = $companion->isMaintainable();

    if ($maintainable) {
        if (in_array('mesmerize-content-padding', $classes)) {
            $classes = array_diff($classes, array('mesmerize-content-padding'));
        }

        $classes[] = 'mesmerize-content-no-padding ';
    }

    return $classes;

}, PHP_INT_MAX);


function mesmerize_companion_maintainable_pages_tinymce_init($init)
{
    $init['verify_html'] = false;

    // convert newline characters to BR
    $init['convert_newlines_to_brs'] = true;

    // don't remove redundant BR
    $init['remove_redundant_brs'] = false;

    $init['remove_linebreaks'] = false;

    $opts                            = '*[*]';
    $init['valid_elements']          = $opts;
    $init['extended_valid_elements'] = $opts;
    $init['forced_root_block']       = false;
    $init['paste_as_text']           = true;

    return $init;
}

add_filter('mesmerize_header_presets', 'mesmerize_companion_header_presets_pro_info');

function mesmerize_companion_header_presets_pro_info($presets)
{


    if (apply_filters('mesmerize_show_info_pro_messages', true)) {
        $companion = \Mesmerize\Companion::instance();

        $proPresets = $companion->themeDataPath("/pro-only-presets.php");
        if (file_exists($proPresets)) {
            $proPresets = require_once($proPresets);
        } else {
            $proPresets = array();
        }

        $presets = array_merge($presets, $proPresets);

    }

    return $presets;
}

add_action('cloudpress\customizer\add_assets', 'mesmerize_load_theme_customizer_scripts', 10, 3);

function mesmerize_load_theme_customizer_scripts($customizer, $jsUrl, $cssUrl)
{
    $ver = $customizer->companion()->version;
    wp_enqueue_script('cp-customizer-shortcodes-theme-data', $customizer->companion()->themeDataURL("/assets/customizer/customizer-shortcodes.js"), array('customizer-base'), $ver, true);
}


function mesmerize_get_custom_mods()
{
    return array(
        ".header-homepage .hero-title" => array(
            'type' => 'data-theme',
            'mod'  => "header_title",
        ),

        ".header-homepage p.header-subtitle" => array(
            'type' => 'data-theme',
            'mod'  => "header_subtitle",
        ),

        ".header-homepage p.header-subtitle2" => array(
            'type' => 'data-theme',
            'mod'  => "header_subtitle2",
        ),

        "#footer-container.footer-7 .footer-description" => array(
            'type' => 'data-theme',
            'mod'  => "footer_content_box_text",
        ),
        ".footer .footer-box-1 > p"                      => array(
            'type' => 'data-theme',
            'mod'  => "footer_box1_content_text",
        ),
        ".footer .footer-box-2 > p"                      => array(
            'type' => 'data-theme',
            'mod'  => "footer_box2_content_text",
        ),
        ".footer .footer-box-3 > p"                      => array(
            'type' => 'data-theme',
            'mod'  => "footer_box3_content_text",
        ),

    );
}


add_filter('cloudpress\customizer\global_data', 'mesmerize_add_dynamic_mods_customizer_data');
function mesmerize_add_dynamic_mods_customizer_data($data)
{
    $mesmerize_custom_mods = mesmerize_get_custom_mods();
    $data['mods']          = apply_filters('mesmerize_dynamic_mods', $mesmerize_custom_mods);

    return $data;
}

add_action('cloudpress\companion\activated\mesmerize', function ($companion) {
    /** @var \Mesmerize\Companion $companion */
    $companion->_createFrontPage();
});


add_action('cloudpress\companion\deactivated\mesmerize', function ($companion) {
    /** @var \Mesmerize\Companion $companion */
    $companion->restoreFrontPage();
});


add_filter('cloudpress\customizer\get_data_filter', function ($value, $key) {
    if ($key === "data:sections" && ! is_array($value)) {
        $value = array();
    }

    return $value;
}, 10, 2);


add_filter('wpforms_shareasale_id', function ($shareid) {
    $shareid = "1836921";
    
    return $shareid;
}, PHP_INT_MAX);
