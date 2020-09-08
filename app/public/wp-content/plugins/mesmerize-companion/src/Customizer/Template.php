<?php

namespace Mesmerize\Customizer;

class Template
{
    
    public static function load($companion)
    {
        $themeWA      = $companion->getCustomizerData('data:widgets_areas');
        $widgetsAreas = apply_filters('cloudpress\template\widgets_areas', $themeWA);
        
        if (is_array($widgetsAreas)) {
            foreach ($widgetsAreas as $data) {
                self::addWidgetsArea($data);
            }
        }

//        add_filter('cloudpress\customizer\global_data', array(__CLASS__, '_prepareStaticSections'));
        
        add_filter('the_content', array(__CLASS__, 'filterContent'), 0);
        
        add_filter('template_include', array(__CLASS__, 'filterTemplateFile'));
        
        // add_filter('do_shortcode_tag', array(__CLASS__, 'shortcodeTagFilter'), 10, 4);
        
        // parse shortcodes make the clean for preview
        // add_filter('the_content', array(__CLASS__, 'decorateShortcodesInHTMLTags'), PHP_INT_MAX);
    }
    
    
    public static function shortcodeTagFilter($output, $tag, $attr, $m)
    {
        if (is_customize_preview()) {
            $inAttribute = false;
            // try to see where is the tag called from by
            // triggering a fake Exception
            try {
                throw new \Exception('Look For Shortcode Type');
            } catch (\Exception $e) {
                $trace = $e->getTrace();
                if (isset($trace[6])) {
                    if ($trace[6]['function'] === "do_shortcodes_in_html_tags") {
                        $inAttribute = true;
                    }
                }
            }
            $shortcode = $m[0];
            if ($inAttribute) {
                $output = "CPSHORTCODE___" . bin2hex($shortcode) . "___" . $output;
            } else {
                $uid         = uniqid(md5($shortcode));
                $commentText = "cp-shortcode:{$uid}:{$shortcode}";
                $output      = "<!--$commentText-->" . $output . "<!--$commentText-->";
            }
        }
        
        return $output;
    }
    
    public static function decorateShortcodesInHTMLTags($content)
    {
        if (is_customize_preview()) {
            // match inside an attribute
            $content = preg_replace_callback("/ (.*?)\=[\"\']CPSHORTCODE___([a-zA-Z0-9]+)___/m", function ($matches) {
                $attr         = $matches[1];
                $shortcodeBin = $matches[2];
                $match        = $matches[0];
                
                
                $match = str_replace("CPSHORTCODE___{$shortcodeBin}___", "", $match);
                
                $shortcode = hex2bin($shortcodeBin);
                $shortcode = esc_attr($shortcode);
                
                $shortcodeAttr = " data-shortcode-{$attr}=\"{$shortcode}\" ";
                
                return $shortcodeAttr . $match;
            }, $content);
        }
        
        return $content;
    }
    
    public static function removeGutenberg()
    {
        $prioriries = apply_filters('cloudpress\companion\gutenberg_autop_filters', array(6, 8));
        foreach ($prioriries as $priority) {
            remove_filter('the_content', 'gutenberg_wpautop', $priority);
        }
        do_action('cloudpress\companion\remove_gutenberg');
    }
    
    public static function filterContent($content)
    {
        $companion = \Mesmerize\Companion::instance();
        if ($companion->isMaintainable()) {
            remove_filter('the_content', 'wpautop');
            static::removeGutenberg();
            
            return Template::content($content, false);
        }
        
        return $content;
    }
    
    public static function filterTemplateFile($template)
    {
        global $post;
        $companion = \Mesmerize\Companion::instance();
        
        $template = apply_filters('cloudpress\companion\template', $template, $companion, $post);
        
        if ($post && $companion->isMaintainable($post->ID)) {
            $companion->loadMaintainablePageAssets($post, $template);
        }
        
        return $template;
    }
    
    
    public static function _prepareStaticSections($globalData)
    {
        $globalData['contentSections'] = array();
        
        foreach ($globalData['data']['sections'] as $section) {
            $section['content']                            = isset($section['content']) ? $section['content'] : false;
            $section['content']                            = apply_filters('cloudpress\template\page_content', $section['content']);
            $globalData['contentSections'][$section['id']] = $section;
        }
        
        return $globalData;
    }
    
    public static function header($slug = "", $isMod = false, $modDefault = null)
    {
        if ($isMod) {
            $slug = get_theme_mod($slug, $modDefault);
        }
        
        if (is_callable($slug)) {
            call_user_func($slug);
        } else {
            $slug = str_replace(".php", "", $slug);
            
            if (locate_template("header-{$slug}.php", false)) {
                get_header($slug);
            } else {
                $slug = $slug . ".php";
                
                if (file_exists(\Mesmerize\Companion::instance()->themeDataPath($slug))) {
                    require_once \Mesmerize\Companion::instance()->themeDataPath($slug);
                } else {
                    get_header();
                }
            }
        }
    }
    
    
    public static function content($content = null, $echo = true)
    {
        if ($content === null) {
            // directly call for the page content
            ob_start();
            remove_filter('the_content', 'wpautop');
            static::removeGutenberg();
            the_content();
            $content = ob_get_clean();
        } else {
            // inside the filter
            
            if (is_customize_preview()) {
                
                $settingContent = get_theme_mod('page_content', array());
                if ($settingContent && is_string($settingContent) && ! empty($settingContent)) {
                    $settingContent = json_decode(urldecode($settingContent), true);
                }
                
                $pageId = get_the_ID();
                if ($settingContent && ! empty($settingContent) && isset($settingContent[$pageId])) {
                    $content = $settingContent[$pageId];
                    $content = preg_replace(\Mesmerize\Customizer\Settings\ContentSetting::$pageIDRegex, "", $content);
                }
                
                // add a data-cpid attr to all nodes inside the page.
                // the unmkared nodes will be removed on save 
                $parts = wp_html_split($content);
                
                $index = 0;
                foreach ($parts as &$part) {
                    $part2 = trim($part);
                    if (strpos($part2, '<') === 0 && strpos($part2, '/') !== 1) {
                        $part = preg_replace('/(\<[a-zA-Z0-9\-]+)/', "$1 data-cpid=\"cp_node_{$index}\" ", $part);
                        $index++;
                    }
                }
                
                $content = implode('', $parts);
            }
            
            $content = apply_filters('cloudpress\template\page_content', $content);
            if (is_customize_preview()) {
                $content = "<style id='cp_customizer_content_area_start'></style>" . $content;
            }
        }
        
        if ($echo) {
            echo $content;
        } else {
            return $content;
        }
    }
    
    public static function footer($slug = "", $isMod = false, $modDefault = null)
    {
        if ($isMod) {
            $slug = get_theme_mod($slug, $modDefault);
        }
        
        if (is_callable($slug)) {
            call_user_func($slug);
        } else {
            $slug = str_replace(".php", "", $slug);
            
            if (locate_template("footer-{$slug}.php", false)) {
                get_footer($slug);
            } else {
                
                $slug = $slug . ".php";
                
                if (file_exists(\Mesmerize\Companion::instance()->themeDataPath($slug))) {
                    require_once \Mesmerize\Companion::instance()->themeDataPath($slug);
                } else {
                    get_footer();
                }
            }
        }
    }
    
    private static function preSetWidget($sidebar, $name, $args = array())
    {
        if ( ! $sidebars = get_option('sidebars_widgets')) {
            $sidebars = array();
        }
        
        // Create the sidebar if it doesn't exist.
        if ( ! isset($sidebars[$sidebar])) {
            $sidebars[$sidebar] = array();
        }
        
        // Check for existing saved widgets.
        if ($widget_opts = get_option("widget_$name")) {
            // Get next insert id.
            ksort($widget_opts);
            end($widget_opts);
            $insert_id = key($widget_opts);
        } else {
            // None existing, start fresh.
            $widget_opts = array('_multiwidget' => 1);
            $insert_id   = 0;
        }
        // Add our settings to the stack.
        $widget_opts[++$insert_id] = $args;
        // Add our widget!
        $sidebars[$sidebar][] = "$name-$insert_id";
        
        update_option('sidebars_widgets', $sidebars);
        update_option("widget_$name", $widget_opts);
    }
    
    public static function addWidgetsArea($data)
    {
        add_action('widgets_init', function () use ($data) {
            register_sidebar(array(
                'name'          => $data['name'],
                'id'            => $data['id'],
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h4>',
                'after_title'   => '</h4>',
            ));
            
            $active_widgets = get_option('sidebars_widgets');
            $index          = count($active_widgets) + 1;
            if (empty($active_widgets[$data['id']]) && get_theme_mod('first_time_widget_' . $data['id'], true)) {
                set_theme_mod('first_time_widget_' . $data['id'], false);
                
                $widget_content = array(
                    'title'  => __($data['title'], 'cloudpress-companion-companion'),
                    'text'   => '<ul><li><a href="http://#">Documentation</a></li><li><a href="http://#">Forum</a></li><li><a href="http://#">FAQ</a></li><li><a href="http://#">Contact</a></li></ul>',
                    'filter' => false,
                );
                
                self::preSetWidget($data['id'], 'text', $widget_content);
            }
            
        });
    }
    
    public static function getWidgetsArea($id)
    {
        ob_start(); ?>
        <div data-widgets-area="<?php echo $id; ?>">
            <?php dynamic_sidebar($id); ?>
        </div>
        <?php ;
        $content = ob_get_clean();
        
        return trim($content);
    }
    
    public static function getModsData($mods)
    {
        $results = array();
        foreach ($mods as $mod => $default) {
            $value         = \Mesmerize\Companion::getThemeMod($mod, $default);
            $value         = \Mesmerize\Companion::filterDefault($value);
            $results[$mod] = $value;
        }
        
        return $results;
    }
    
    public static function loadThemeModPartial($mod, $default = '', $data = null, $once = true)
    {
        if (empty($default)) {
            $default = '[tag_companion_dir]/partials/header/default';
        }
        
        $template_file = \Mesmerize\Companion::getThemeMod($mod, $default);
        $template_file = \Mesmerize\Companion::filterDefault($template_file);
        $template_file = str_replace('.php', '', $template_file) . ".php";
        
        if (is_array($data)) {
            extract($data);
        }
        
        // wordpess date_defaults
        global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
        
        if (is_array($wp_query->query_vars)) {
            extract($wp_query->query_vars, EXTR_SKIP);
        }
        
        if (isset($s)) {
            $s = esc_attr($s);
        }
        
        if ($once) {
            require_once($template_file);
        } else {
            require($template_file);
        }
    }
}
