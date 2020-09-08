<?php

function mesmerize_sprintf_style_array($data, $media = false)
{
    $style = "";

    if ( ! is_array($data) || empty($data)) {
        return $style;
    }

    foreach ($data as $selector => $props) {
        $propsText = "";
        foreach ($props as $prop => $value) {
            $propText = "\t{$prop}:{$value};\n";
            if ($media) {
                $propText = "\t{$propText}";
            }
            $propsText .= $propText;
        }

        if ($media) {
            $selector = "\t{$selector}";
        }

        $style .= "$selector{\n{$propsText}\n}";
    }

    if ($media) {
        $style = "$media{\n{$style}\n}";
    }

    return $style . "\n";

}


function mesmerize_print_content_custom_style()
{
    global $post;

    if ( ! $post) {
        return;
    }

    $mediaMap = array(
        "mobile"  => "@media screen and (max-width:767)",
        "tablet"  => "@media screen and (min-width:768)",
        "desktop" => "@media screen and (min-width:1024)",
        "nomedia" => false,
    );

    $mod  = 'custom_content_style_' . $post->ID;
    $data = get_theme_mod($mod, array(
        "mobile"  => array(),
        "tablet"  => array(),
        "desktop" => array(),
        "nomedia" => array(),
    ));

    $outputOrder = array('nomedia', 'mobile', 'tablet', 'desktop');

    $style = "";
    foreach ($outputOrder as $media) {
        $mediaQuery  = $mediaMap[$media];
        $mediaStyles = isset($data[$media]) ? $data[$media] : array();
        $style       .= mesmerize_sprintf_style_array($mediaStyles, $mediaQuery);
    }

    ?>
    <style id="page-content-custom-styles">
        <?php echo $style; ?>
    </style>
    <?php
}

add_action('wp_head', "mesmerize_print_content_custom_style", PHP_INT_MAX);

function mesmerize_pro_page_custom_styles_filter($value)
{

    global $post;

    $default = array(
        "mobile"  => new stdClass(),
        "tablet"  => new stdClass(),
        "desktop" => new stdClass(),
        "nomedia" => new stdClass(),
    );

    if ($post) {
        $mod  = 'custom_content_style_' . $post->ID;
        $data = get_theme_mod($mod, $default);

        $value['content_style'] = $data;
    } else {
        $value['content_style'] = $default;
    }


    return $value;
}

add_filter('cloudpress\customizer\preview_data', 'mesmerize_pro_page_custom_styles_filter');