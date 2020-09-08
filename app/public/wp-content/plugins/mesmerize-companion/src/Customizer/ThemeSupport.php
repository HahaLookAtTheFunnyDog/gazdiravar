<?php

namespace Mesmerize\Customizer;

class ThemeSupport {
    private static $defaultSupport = array();

    private static $defaultsReady = false;
    private static function setDefault() {

        if (self::$defaultsReady) {
            return;
        }

        self::$defaultsReady = true;

        $companion            = \Mesmerize\Companion::instance();
        self::$defaultSupport = array(
            'custom-background' => array(
                'default-color'      => "#F5FAFD",
//                'default-image'      => $companion->themeDataURL() . "/sections/images/Travel_through_New_York_wallpaper-1920x1200.jpg",
                'default-repeat'     => 'no-repeat',
                'default-position-x' => 'center',
                'default-attachment' => 'fixed',
            ),
        );
    }

    public static function load() {
        self::setDefault();

        $supports = apply_filters('cloudpress\theme_support', static::$defaultSupport);

        foreach ($supports as $key => $value) {
            add_theme_support($key, $value);
        }
    }
}