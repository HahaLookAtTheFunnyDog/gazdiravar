<?php


namespace Mesmerize\Assets;

class Assets
{

    public static function isScriptDebug()
    {
        $isScriptDebug = defined("SCRIPT_DEBUG") && SCRIPT_DEBUG;

        return $isScriptDebug;
    }

    public static function enqueue($type = 'style', $handle, $args = array())
    {
        /** @var \Mesmerize\Companion $companion */
        $companion = \Mesmerize\Companion::instance();
        $data      = array_merge(array(
            'src'           => '',
            'deps'          => array(),
            'has_min'       => false,
            'in_footer'     => true,
            'media'         => 'all',
            'ver'           => $companion->version,
            'in_preview'    => true,
            'register_only' => false,
        ), $args);

        if (is_customize_preview() && $data['in_preview'] === false) {
            return;
        }

        $isScriptDebug = defined("SCRIPT_DEBUG") && SCRIPT_DEBUG;
        if ($data['has_min'] && ! $isScriptDebug) {
            if ($type === 'style') {
                $data['src'] = str_replace('.css', '.min.css', $data['src']);
            }

            if ($type === 'script') {
                $data['src'] = str_replace('.js', '.min.js', $data['src']);
            }
        }

        if ($type == 'style') {
            if ($data['register_only'] === true) {
                wp_register_style($handle, $data['src'], $data['deps'], $data['ver'], $data['media']);
            } else {
                wp_enqueue_style($handle, $data['src'], $data['deps'], $data['ver'], $data['media']);

            }
        }

        if ($type == 'script') {

            if ($data['register_only'] === true) {
                wp_register_script($handle, $data['src'], $data['deps'], $data['ver'], $data['media']);
            } else {
                wp_enqueue_script($handle, $data['src'], $data['deps'], $data['ver'], $data['in_footer']);

            }
        }

    }

    public static function enqueueStyle($handle, $args)
    {
        self::enqueue('style', $handle, $args);
    }

    public static function enqueueScript($handle, $args)
    {
        self::enqueue('script', $handle, $args);
    }

    public static function groupStyles($handle, $toGroup = array())
    {
        self::enqueueStyle($handle, array(
            "src"  => null,
            "deps" => $toGroup,
        ));
    }

    public static function groupScripts($handle, $toGroup)
    {

        self::enqueueScript($handle, array(
            "src"           => null,
            "deps"          => $toGroup,
            "register_only" => true,
        ));
    }


    public static function replaceScripts($newHandle, $toReplace, $args)
    {
        wp_dequeue_script($newHandle);
        wp_deregister_script($newHandle);
        foreach ($toReplace as $handle) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        }

        self::enqueueScript($newHandle, $args);
    }

}