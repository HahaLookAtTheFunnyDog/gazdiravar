<?php


add_action("mesmerize_header_background_overlay_settings", "mesmerize_front_page_header_overlap_options", 5, 5);

function mesmerize_front_page_header_overlap_options($section, $prefix, $group, $inner, $priority)
{
    if ($inner) {
        return;
    }
    $priority = 5;
    $prefix   = "header";
    $section  = "header_background_chooser";
    $group    = "";

    mesmerize_add_kirki_field(array(
        'type'            => 'checkbox',
        'settings'        => 'header_overlap',
        'label'           => esc_html__('Allow content to overlap header', 'mesmerize'),
        'default'         => mesmerize_mod_default('header_overlap'),
        'section'         => $section,
        'priority'        => $priority,
        'group'           => $group,
        'transport'       => 'postMessage',
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), $inner),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'dimension',
        'settings'        => 'header_overlap_with',
        'label'           => esc_html__('Overlap with', 'mesmerize'),
        'section'         => $section,
        'default'         => '95px',
        'priority'        => $priority,
        'transport'       => 'postMessage',
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter',
            array(
                array(
                    "setting"  => "header_overlap",
                    "operator" => "==",
                    "value"    => true,
                ),
            ),
            $inner, 'simple'
        ),
        'group'           => $group,
    ));
}

add_filter('body_class', function ($classes) {

    $header_type = get_theme_mod('header_type', 'simple');

    if ( ! in_array($header_type, array('simple', 'slider'))) {
        return $classes;
    } else {
        // set overlap-first-section class depending on the header type
        if ($header_type == 'simple') {
            $overlap_mod = get_theme_mod('header_overlap', mesmerize_mod_default('header_overlap'));
        }
        if ($header_type == 'slider') {
            $overlap_mod = get_theme_mod('slider_overlap_header', true);
        }
        if (1 == intval($overlap_mod)) {
            $classes[] = "overlap-first-section";
        }

        return $classes;
    }

});

// always print overlap properties, to avoid a bug when the customizer is loaded without allow overlap,
// and checking the control will put the 'overlap-first-section' class on body, but the padding/negative margin wont be set
add_action('wp_head', function () {

    $inner = mesmerize_is_inner(true);

    // only print style for simple header type, slider doesn't need this style
    if ($inner || (get_theme_mod('header_type', 'simple') !== 'simple')) {
        return;
    }

    $overlap_with = get_theme_mod('header_overlap_with', '95px');
    $selector     = '.mesmerize-front-page.overlap-first-section:not(.mesmerize-front-page-with-slider)';

    ?>
    <style data-name="header-overlap-with">
        @media screen and (min-width: 768px) {
            <?php echo esc_attr($selector); ?> .header-homepage {
                padding-bottom: <?php echo esc_attr($overlap_with); ?>;
            }

            <?php echo esc_attr($selector); ?> .page-content div[data-overlap]:first-of-type > div:not([class*="section-separator"]) {
                margin-top: -<?php echo esc_attr($overlap_with); ?>;
            }
        }
    </style>
    <?php
});
