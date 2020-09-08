<?php

namespace Mesmerize\Customizer;

class Customizer
{
    public $cpData = null;
    
    /** @var \Mesmerize\Companion $_companion */
    private $_companion = null;
    
    private $globalScriptsPrinted = false;
    private $autoSetting = false;
    
    private $registeredTypes
        = array(
            'panels'   => array(
                "Mesmerize\\Customizer\\BasePanel" => true,
            ),
            'sections' => array(),
            'controls' => array(
                "Mesmerize\\Customizer\\BaseControl" => true,
            ),
        );
    
    public function __construct($companion)
    {
        $this->_companion = $companion;
        
        
        if ( ! $this->customizerSupportsViewedTheme()) {
            return;
        }
        
        do_action('cloudpress\customizer\loaded');
        
        $this->register(array($this, '_registerComponents'));
        
        $this->registerScripts(array($this, '_registerAssets'), 20);
        $this->previewInit(array($this, '_registePreviewAssets'));
        
        $this->register(array($this, '_addGlobalScript'));
        $this->previewInit(array($this, '_previewScript'));
        
        add_filter('customize_dynamic_setting_args', array($this, '_autoSettingsOptions'), PHP_INT_MAX, 2);
        add_filter('customize_dynamic_setting_class', array($this, '_autoSettingsClass'), 10, 3);
        
        add_filter('option_theme_mods_' . get_stylesheet(), array($this, 'addAutoSettingsInPreview'), 0);
    }
    
    public function customizerSupportsViewedTheme()
    {
        
        $supported = $this->companion()->isCurrentThemeSupported();
        $supported = apply_filters('cloudpress\customizer\supports', $supported);
        
        return $supported;
        
    }
    
    public function companion()
    {
        return $this->_companion;
    }
    
    public function register($callback, $priority = 40)
    {
        add_action('customize_register', $callback, $priority);
    }
    
    public function registerScripts($callback, $priority = 40)
    {
        add_action('customize_controls_enqueue_scripts', $callback, $priority);
    }
    
    public function previewInit($callback, $priority = 40)
    {
        add_action('customize_preview_init', $callback, $priority);
    }
    
    public function addAutoSettingsInPreview($values)
    {
        
        if (is_customize_preview()) {
            global $wp_customize;
            $settings = $wp_customize->unsanitized_post_values();
            
            foreach ($settings as $mod => $value) {
                if (strpos($mod, "CP_AUTO_SETTING[") === 0) {
                    $key          = str_replace("CP_AUTO_SETTING[", "", $mod);
                    $key          = trim($key, "[]");
                    $values[$key] = $value;
                }
            }
        }
        
        return $values;
        
    }
    
    public function _registerComponents($wp_customize)
    {
        $this->cpData = apply_filters('cloudpress\customizer\data', $this->_companion->getCustomizerData(), $this);
        $this->registerComponents($wp_customize);
    }
    
    private function registerComponents($wp_customize)
    {
        $wp_customize->register_panel_type("Mesmerize\\Customizer\\BasePanel");
        $wp_customize->register_control_type("Mesmerize\\Customizer\\BaseControl");
        
        foreach ($this->cpData['customizer'] as $category => $components) {
            switch ($category) {
                case 'panels':
                    $this->registerPanels($wp_customize, $components);
                    break;
                case 'sections':
                    $components = $this->cpData['customizer']['sections'];
                    $this->registerSections($wp_customize, $components);
                    break;
                
                case 'controls':
                    $components = $this->cpData['customizer']['controls'];
                    $this->registerControls($wp_customize, $components);
                    break;
                case 'settings':
                    $components = $this->cpData['customizer']['settings'];
                    $this->registerSettings($wp_customize, $components);
                    break;
            }
        }
    }
    
    public function registerPanels($wp_customize, $components)
    {
        foreach ($components as $id => $data) {
            if ($panel = $wp_customize->get_panel($id)) {
                if (isset($data['wp_data'])) {
                    foreach ($data['wp_data'] as $key => $value) {
                        $panel->$key = $value;
                    }
                }
                continue;
            }
            
            
            $panelClass = "Mesmerize\\Customizer\\BasePanel";
            
            if (isset($data['class']) && $data['class']) {
                $panelClass = $data['class'];
            }
            
            if ( ! isset($this->registeredTypes['panels'][$panelClass])) {
                $this->registeredTypes['panels'][$panelClass] = true;
            }
            
            
            if (strpos($panelClass, "WP_Customize_") !== false) {
                $data = isset($data['wp_data']) ? $data['wp_data'] : array();
            }
            
            $wp_customize->add_panel(new $panelClass($wp_customize, $id, $data));
        }
    }
    
    public function registerSections($wp_customize, $components)
    {
        foreach ($components as $id => $data) {
            if ($section = $wp_customize->get_section($id)) {
                if (isset($data['wp_data'])) {
                    foreach ($data['wp_data'] as $key => $value) {
                        $section->$key = $value;
                    }
                }
                continue;
            }
            
            $sectionClass = "Mesmerize\\Customizer\\BaseSection";
            
            if (isset($data['class']) && $data['class']) {
                $sectionClass = $data['class'];
            }
            
            if ( ! isset($this->registeredTypes['sections'][$sectionClass])) {
                $this->registeredTypes['sections'][$sectionClass] = true;
                $wp_customize->register_section_type($sectionClass);
            }
            
            
            if (strpos($sectionClass, "WP_Customize_") !== false) {
                $data = isset($data['wp_data']) ? $data['wp_data'] : array();
            }
            
            $wp_customize->add_section(new $sectionClass($wp_customize, $id, $data));
        }
    }
    
    public function registerControls($wp_customize, $components)
    {
        foreach ($components as $id => $data) {
            if ($control = $wp_customize->get_control($id)) {
                if (isset($data['wp_data'])) {
                    foreach ($data['wp_data'] as $key => $value) {
                        $control->$key = $value;
                    }
                }
                continue;
            }
            
            $controlClass = "Mesmerize\\Customizer\\BaseControl";
            if (isset($data['class']) && $data['class']) {
                $controlClass = $data['class'];
            }
            
            if ( ! isset($this->registeredTypes['controls'][$controlClass])) {
                $this->registeredTypes['controls'][$controlClass] = true;
                // $wp_customize->register_control_type($controlClass);
            }
            
            
            if (strpos($controlClass, "WP_Customize_") !== false) {
                $data = isset($data['wp_data']) ? $data['wp_data'] : array();
            }
            
            if (strpos($controlClass, "kirki:") === 0) {
                $data         = isset($data['wp_data']) ? $data['wp_data'] : array();
                $data['type'] = str_replace("kirki:", "", $controlClass);
                \Kirki::add_field($id, $data);
            } else {
                $wp_customize->add_control(new $controlClass($wp_customize, $id, $data));
            }
        }
    }
    
    public function registerSettings($wp_customize, $components)
    {
        foreach ($components as $id => $data) {
            if ($setting = $wp_customize->get_setting($id)) {
                if (isset($data['wp_data'])) {
                    foreach ($data['wp_data'] as $key => $value) {
                        if ($key === "default") {
                            $value = BaseSetting::filterDefault($value);
                        }
                        $setting->$key = $value;
                    }
                }
                continue;
            }
            
            $settingClass = "Mesmerize\\Customizer\\BaseSetting";
            
            if (isset($data['class']) && $data['class']) {
                $settingClass = $data['class'];
            }
            
            if (strpos($settingClass, "WP_Customize_") !== false) {
                $data = isset($data['wp_data']) ? $data['wp_data'] : array();
            }
            
            
            if (strpos($settingClass, "kirki") === 0) {
                $settingClass        = "Mesmerize\\Customizer\\BaseSetting";
                $data['__is__kirki'] = true;
            }
            
            
            $setting = new $settingClass($wp_customize, $id, $data);
            
            if ( ! $setting->isKirki()) {
                $wp_customize->add_setting($setting);
            }
            
            if (method_exists($setting, 'setControl')) {
                $setting->setControl();
            }
        }
    }
    
    public function _registerAssets($wp_customize)
    {
        
        $self = $this;
        
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
        
        $jsUrl  = $self->companion()->assetsRootURL() . "/js/customizer/";
        $cssUrl = $self->companion()->assetsRootURL() . "/css";
        
        $ver        = $self->companion()->version;
        $textDomain = $self->companion()->getThemeSlug();
        
        
        wp_enqueue_style('cp-customizer-base', $cssUrl . '/customizer.css', array(), $ver);
        
        if (apply_filters('\cloudpress\customizer\load_bundled_version', true)) {
            wp_enqueue_script('customizer-base', $jsUrl . "customizer.bundle.min.js", array("{$textDomain}-customize"), $ver, true);
        } else {
            wp_enqueue_script('cp-customizer-speakurl', $jsUrl . "../libs/speakingurl.js", array(), $ver, true);
            wp_enqueue_script('cp-hooks-manager', $jsUrl . "../libs/hooks-manager.js", array(), $ver, true);
            wp_enqueue_script('cp-customizer-base', $jsUrl . "customizer-base.js", array('cp-hooks-manager', 'cp-customizer-speakurl', "{$textDomain}-customize"), $ver, true);
            wp_enqueue_script('cp-customizer-utils', $jsUrl . "customizer-utils.js", array('cp-customizer-base'), $ver, true);
            wp_enqueue_script('cp-customizer-support', $jsUrl . "customizer-support.js", array(), $ver, true);
            
            wp_enqueue_script('cp-webfonts', $jsUrl . '/web-fonts.js', array('jQuery'));
            wp_enqueue_script('cp-customizer-shortcodes-popup', $jsUrl . "/customizer-shortcodes-popup.js", array('cp-customizer-base'), $ver, true);
            wp_enqueue_script('cp-customizer-custom-popup', $jsUrl . "/customizer-custom-popup.js", array('cp-customizer-base'), $ver, true);
            
            
            wp_register_script('customizer-base', null, array('cp-customizer-base', 'cp-customizer-utils', 'cp-customizer-support', 'cp-hooks-manager'), $ver, true);
            wp_enqueue_script('customizer-base');
            
            wp_enqueue_script('customizer-custom-style-manager',
                $jsUrl . "/customizer-custom-style-manager.js", array('customizer-base'), $ver, true);
            wp_enqueue_script('customizer-section-settings-controls',
                $jsUrl . "/customizer-section-settings-controls.js", array('customizer-base'), $ver, true);
            
            wp_enqueue_script('customizer-current-page-settings',
                $jsUrl . "/customizer-current-page-settings.js", array('customizer-base'), $ver, true);
            
            wp_enqueue_script('customizer-section-settings-panel',
                $jsUrl . "/customizer-section-settings-panel.js", array('customizer-section-settings-controls'), $ver, true);
            
            wp_enqueue_script('customizer-features-popup',
                $jsUrl . "/customizer-features-popup.js", array('customizer-base'), $ver, true);
        }
        
        
        wp_localize_script('customizer-base', '__mesmerizeCustomizerStrings', Translations::getTranslations());
        do_action('cloudpress\customizer\add_assets', $self, $jsUrl, $cssUrl);
        
        $this->maybeFeaturePopups();
    }
    
    public function maybeFeaturePopups()
    {
        $featuresPopups            = apply_filters('cloudpress\customizer\feature_popups', array());
        $featuresPopupsToLoad      = array();
        $startWithFeaturePopup     = null;
        $startWithFeaturePopupData = null;
        
        foreach ($featuresPopups as $key => $data) {
            $disabled = get_option("feature_popup_{$data['id']}_disabled", false);
            if ( ! intval($disabled)) {
                $startWithFeaturePopup     = $data['id'];
                $startWithFeaturePopupData = $data;
            }
            
            if ($data['autoload']) {
                $featuresPopupsToLoad[$data['id']] = $data;
            }
        }
        
        if ($startWithFeaturePopup) {
            $featuresPopupsToLoad[$startWithFeaturePopup] = $startWithFeaturePopupData;
        }
        
        foreach ($featuresPopupsToLoad as $popup) {
            
            if ( ! isset($popup['assets'])) {
                continue;
            }
            
            if (isset($popup['assets']['style'])) {
                wp_enqueue_style("{$popup['id']}-style", $popup['assets']['style']);
            }
            
            if (isset($popup['assets']['script'])) {
                $deps = array('customizer-features-popup');
                
                if (apply_filters('\cloudpress\customizer\load_bundled_version', true)) {
                    $deps = array('customizer-base');
                }
                
                wp_enqueue_script("{$popup['id']}-script", $popup['assets']['script'], $deps, null, true);
            }
        }
        
        add_filter('cloudpress\customizer\global_data', function ($data) use ($startWithFeaturePopup, $featuresPopupsToLoad) {
            
            $data = array_merge($data, array(
                "startWithFeaturePopup" => $startWithFeaturePopup,
                "featuresPopups"        => $featuresPopupsToLoad,
                "featurePopupsNonce"    => wp_create_nonce("companion_disable_popup"),
            ));
            
            return $data;
            
        });
        
    }
    
    public function _addGlobalScript($wp_customize)
    {
        $self = $this;
        
        
        add_action('customize_controls_print_scripts', function () {
            if (isset($_REQUEST['cp__changeset__preview'])): ?>
                <style>
                    #customize-controls {
                        display: none !important;
                    }

                    div#customize-preview {
                        position: fixed;
                        top: 0px;
                        left: 0px;
                        height: 100%;
                        width: 100%;
                        z-index: 10000000;
                        display: block;
                    }

                    html, body {
                        width: 100%;
                        max-width: 100%;
                        overflow-x: hidden;
                    }
                </style>
                <script>
                    window.__isCPChangesetPreview = true;
                </script>
            <?php endif;
        });
        
        
        add_action('customize_controls_print_footer_scripts', function () use ($self) {
            
            if (defined("CP__addGlobalScript")) {
                return;
            }
            
            define("CP__addGlobalScript", "1");
            
            $isScriptDebugging           = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG);
            $isShowingNextFeaturesActive = (defined('SHOW_NEXT_FEATURES') && SHOW_NEXT_FEATURES);
            
            
            $globalData = apply_filters('cloudpress\customizer\global_data', array(
                "version"                => $self->companion()->getCustomizerData('version'),
                "data"                   => $self->companion()->getCustomizerData('data'),
                "slugPrefix"             => $self->companion()->getThemeSlug(true),
                "cssAllowedProperties"   => \Mesmerize\Utils\Utils::getAllowCssProperties(),
                "stylesheetURL"          => get_stylesheet_directory_uri(),
                "includesURL"            => includes_url(),
                "themeURL"               => get_template_directory_uri(),
                "isMultipage"            => $self->companion()->isMultipage(),
                "restURL"                => get_rest_url(),
                "SCRIPT_DEBUG"           => $isScriptDebugging,
                "SHOW_NEXT_FEATURES"     => $isShowingNextFeaturesActive,
                "isWoocommerceInstalled" => class_exists('WooCommerce'),
            
            ));
            ?>
            <!-- CloudPress Companion Global Data START -->
            <script type="text/javascript">
                (function () {
                    parent.cpCustomizerGlobal = window.cpCustomizerGlobal = {
                        pluginOptions:  <?php echo json_encode($globalData); ?>
                    };
                })();
            </script>

            <div id="cp-full-screen-loader" class="active">
                <div class="wrapper">
                    <div id="floatingCirclesG">
                        <div class="f_circleG" id="frotateG_01"></div>
                        <div class="f_circleG" id="frotateG_02"></div>
                        <div class="f_circleG" id="frotateG_03"></div>
                        <div class="f_circleG" id="frotateG_04"></div>
                        <div class="f_circleG" id="frotateG_05"></div>
                        <div class="f_circleG" id="frotateG_06"></div>
                        <div class="f_circleG" id="frotateG_07"></div>
                        <div class="f_circleG" id="frotateG_08"></div>
                    </div>
                    <p class="message-area"><?php _e('Please wait,<br/>this might take a little while', 'one-page-express-pro') ?></p>
                </div>
            </div>
            
            <?php $frontpageCB = uniqid('cb_') . "_CreateFrontendPage"; ?>
            <div class='reiki-needed-container' data-type="select">
                <div class="description customize-section-description">
                    <span><?php _e('This section only works when the ' . $self->companion()->getThemeName() . ' custom front page is open in Customizer', 'cloudpress-companion'); ?>.</span>
                    <a onclick="<?php echo $frontpageCB ?>()" class="reiki-needed select available-item-hover-button"><?php _e('Open ' . $self->companion()->getThemeName() . ' Front Page', 'reiki-companion'); ?></a>
                </div>
            </div>
            <script>
                <?php echo $frontpageCB ?>  = function () {
                    jQuery.post(
                        parent.ajaxurl,
                        {
                            action: 'create_home_page',
                            create_home_page_nounce: '<?php echo wp_create_nonce('create_home_page_nounce'); ?>'
                        },
                        function (response) {
                            parent.window.location = (parent.window.location + "").split("?")[0];
                        }
                    );
                }
            </script>

            <div class='reiki-needed-container' data-type="activate">
                <div class="description customize-section-description">
                    <span><?php _e('This section only works when the ' . $self->companion()->getThemeName() . ' custom front page is activated', 'cloudpress-companion'); ?>.</span>
                    <a onclick="<?php echo $frontpageCB ?>()" class="reiki-needed activate available-item-hover-button"><?php _e('Activate ' . $self->companion()->getThemeName() . ' Front Page', 'cloudpress-companion'); ?></a>
                </div>
            </div>
            
            <?php $makeMaintainable = uniqid('cb_') . "_MakePageMaintainable"; ?>

            <script>
                var <?php echo $makeMaintainable ?> =

                function () {
                    var page = top.CP_Customizer.preview.data().pageID;
                    jQuery.post(ajaxurl, {
                        action: 'cp_open_in_customizer',
                        page: page,
                        mark_as_editable: true
                    }).done(function (response) {
                        window.location = response.trim();
                    });
                }
            </script>

            <div class='reiki-needed-container' data-type="edit-this-page">
                <div class="description customize-section-description">
                    <span><?php _e('This page is not marked as editable in Customizer', 'cloudpress-companion'); ?>.</span>
                    <a onclick="<?php echo $makeMaintainable ?>()" class="reiki-needed edit-this-page available-item-hover-button"><?php _e('Make this page editable in customizer', 'cloudpress-companion'); ?></a>
                    <span style="font-size: 11px; padding-top: 14px;line-height: 1.2;"><?php _e('A page revision will be created so you can go back if the button was pressed by mistake', 'cloudpress-companion'); ?>.</span>
                </div>
            </div>


            <div class='reiki-needed-container' data-type="edit-this-product">
                <div class="description customize-section-description">
                    <span><?php _e('This product page is not marked as editable in Customizer', 'cloudpress-companion'); ?>.</span>
                    <a onclick="<?php echo $makeMaintainable ?>()" class="reiki-needed edit-this-page available-item-hover-button"><?php _e('Make this product editable in customizer', 'cloudpress-companion'); ?></a>
                    <span style="font-size: 11px; padding-top: 14px;line-height: 1.2;"><?php _e('A page revision will be created so you can go back if the button was pressed by mistake', 'cloudpress-companion'); ?>.</span>
                </div>
            </div>
            
            
            <?php do_action("cloudpress\customizer\global_scripts", $self); ?>
            <!-- CloudPress Companion Global Data END -->
            <?php
            
        });
    }
    
    public function _registePreviewAssets($wp_customize)
    {
        $jsUrl  = $this->_companion->assetsRootURL() . "/js/customizer";
        $cssUrl = $this->_companion->assetsRootURL() . "/css";
        
        wp_enqueue_style('cp-customizer-spectrum', $cssUrl . '/libs/spectrum.css');
        wp_enqueue_style('cp-customizer-preview', $cssUrl . '/preview.css');
        wp_enqueue_style('cp-customizer-preview-tinymce', $cssUrl . '/tinymce.css');
        
        wp_enqueue_script('cp-customizer-preview', $jsUrl . "/preview.js", array('jquery', 'jquery-ui-sortable', 'customize-preview'));
    }
    
    public function _autoSettingsOptions($args, $setting)
    {
        $settingRegex = \Mesmerize\Customizer\Settings\AutoSetting::SETTING_PATTERN;
        
        if (preg_match($settingRegex, $setting)) {
            $args = array(
                'transport' => 'postMessage',
                'type'      => \Mesmerize\Customizer\Settings\AutoSetting::TYPE,
            );
        }
        
        return $args;
    }
    
    public function _autoSettingsClass($class, $setting, $args)
    {
        $settingRegex = \Mesmerize\Customizer\Settings\AutoSetting::SETTING_PATTERN;
        
        if (preg_match($settingRegex, $setting)) {
            $class = "\\Mesmerize\\Customizer\\Settings\\AutoSetting";
        }
        
        return $class;
    }
    
    public function _previewScript($wp_customize)
    {
        if (defined("CP__previewScript")) {
            return;
        }
        
        define("CP__previewScript", "1");
        
        $self = $this;
        
        add_action('wp_footer', function () use ($self) {
            global $wp_query, $post;
            
            $vars              = $self->queryVarsCleaner($wp_query->query_vars);
            $vars['post_type'] = get_post_type();
            
            $previewData = apply_filters('cloudpress\customizer\preview_data', array(
                "version"                => $self->companion()->getCustomizerData('version'),
                "slug"                   => $self->companion()->getThemeSlug(),
                "maintainable"           => $self->companion()->isMaintainable(),
                "isFrontPage"            => $self->companion()->isFrontPage(),
                "canEditInCustomizer"    => $self->companion()->canEditInCustomizer(),
                "pageID"                 => $self->companion()->getCurrentPageId(),
                "queryVars"              => $vars,
                "hasFrontPage"           => ($self->companion()->getFrontPage() !== null),
                "siteURL"                => get_home_url(),
                "pageURL"                => $post ? get_page_link() : null,
                "includesURL"            => includes_url(),
                "mod_defaults"           => apply_filters('cloudpress\customizer\mod_defaults', array()),
                "isWoocommerceInstalled" => class_exists('WooCommerce'),
            ));
            ?>
            <!-- CloudPress Companion Preview Data START -->
            <script type="text/javascript">
                (function () {
                    window.cpCustomizerPreview = <?php echo json_encode($previewData); ?>;
                    wp.customize.bind('preview-ready', function () {
                        jQuery(window).load(function () {

                            setTimeout(function () {
                                parent.postMessage('cloudpress_update_customizer', "*");
                            }, 100);

                        });

                    });
                })();
            </script>

            <style>
                *[contenteditable="true"] {
                    user-select: auto !important;
                    -webkit-user-select: auto !important;
                    -moz-user-select: text !important;
                }
            </style>
            
            <?php do_action("cloudpress\customizer\preview_scripts", $self); ?>
            <!-- CloudPress Companion Preview Data END -->
            <?php
            
        });
    }
    
    public function queryVarsCleaner($input)
    {
        foreach ($input as $key => &$value) {
            if (is_array($value)) {
                $value = $this->queryVarsCleaner($value);
            } else {
                if (strpos($key, 'cache') !== false) {
                    unset($input[$key]);
                }
            }
        }
        
        return array_filter($input);
    }
    
    public function removeNamespace($name)
    {
        $parts  = explode("\\", $name);
        $result = array();
        
        foreach ($parts as $part) {
            $part = trim($part);
            if ( ! empty($part)) {
                $result[] = $part;
            }
        }
        
        $result = implode("-", $result);
        
        return strtolower($result);
    }
}
