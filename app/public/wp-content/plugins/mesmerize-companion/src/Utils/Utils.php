<?php

namespace Mesmerize\Utils;

class Utils
{

    // http://php.net/manual/ro/function.array-merge-recursive.php#102379
    public static function mergeArrays($Arr1, $Arr2)
    {
        foreach ($Arr2 as $key => $Value) {
            if (array_key_exists($key, $Arr1) && is_array($Value)) {
                $Arr1[$key] = self::mergeArrays($Arr1[$key], $Arr2[$key]);
            } else {
                $Arr1[$key] = $Value;
            }
        }

        return $Arr1;
    }


    public static function getAllowCssProperties()
    {
        return apply_filters('safe_style_css', array(
            'background',
            'background-color',

            'border',
            'border-width',
            'border-color',
            'border-style',
            'border-right',
            'border-right-color',
            'border-right-style',
            'border-right-width',
            'border-bottom',
            'border-bottom-color',
            'border-bottom-style',
            'border-bottom-width',
            'border-left',
            'border-left-color',
            'border-left-style',
            'border-left-width',
            'border-top',
            'border-top-color',
            'border-top-style',
            'border-top-width',

            'border-spacing',
            'border-collapse',
            'caption-side',

            'color',
            'font',
            'font-family',
            'font-size',
            'font-style',
            'font-variant',
            'font-weight',
            'letter-spacing',
            'line-height',
            'text-decoration',
            'text-indent',
            'text-align',
            'text-transform',

            'height',
            'min-height',
            'max-height',

            'width',
            'min-width',
            'max-width',

            'margin',
            'margin-right',
            'margin-bottom',
            'margin-left',
            'margin-top',

            'padding',
            'padding-right',
            'padding-bottom',
            'padding-left',
            'padding-top',

            'clear',
            'cursor',
            'direction',
            'float',
            'overflow',
            'vertical-align',
            'list-style-type',
        ));
    }
}
