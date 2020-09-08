<?php

use ExtendThemes\DemoImportIntegration\DemoImportIntegration;

$slug         = 'one-click-demo-import';
$state        = \Mesmerize\Companion_Plugin::get_plugin_state($slug);
$link         = '';
$messageTitle = '';
$actionLabel  = '';

if ($state['installed']) {
    $link         = \Mesmerize\Companion_Plugin::get_activate_link($slug);
    $messageTitle = sprintf(
        __('To import the demo content please activate the %s plugin', 'mesmerize-companion'),
        "<strong>One Click Demo Install</strong>"
    );
    $actionLabel  = __('Activate the plugin now', 'mesmerize-companion');
} else {
    $link         = \Mesmerize\Companion_Plugin::get_install_link($slug);
    $messageTitle = sprintf(
        __('To import the demo content please install and activate the %s plugin', 'mesmerize-companion'),
        "<strong>One Click Demo Install</strong>"
    );
    $actionLabel  = __('Install the plugin now', 'mesmerize-companion');
}

wp_enqueue_style('mesmerize-demo-import-ocdi-style', MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL . "/assets/ocdi.css");

$allDemos = DemoImportIntegration::getDemoDataOCDIFormat();
wp_localize_script('mesmerize-demo-import-script', '__ocdiPreparePluginData', array(
    'install_url'  => \Mesmerize\Companion_Plugin::get_install_link($slug),
    'activate_url' => \Mesmerize\Companion_Plugin::get_activate_link($slug),
    'status'       => $state,
    'slug'         => $slug,
    'demos'        => $allDemos,
));

wp_localize_script('mesmerize-demo-import-script', 'ocdi',
    array(
        'ajax_url'         => admin_url('admin-ajax.php'),
        'ajax_nonce'       => wp_create_nonce('ocdi-ajax-verification'),
        'import_files'     => DemoImportIntegration::getDemoDataOCDIFormat(),
        'wp_customize_on'  => apply_filters('pt-ocdi/enable_wp_customize_save_hooks', false),
        'import_popup'     => false,
        'theme_screenshot' => wp_get_theme()->get_screenshot(),
        'texts'            => array(
            'missing_preview_image' => esc_html__('No preview image defined for this import.', 'mesmerize-companion'),
            'dialog_title'          => esc_html__('Are you sure?', 'mesmerize-companion'),
            'dialog_no'             => esc_html__('Cancel', 'mesmerize-companion'),
            'dialog_yes'            => esc_html__('Yes, import!', 'mesmerize-companion'),
            'selected_import_title' => esc_html__('Selected demo import:', 'mesmerize-companion'),
            'not-installed'         => esc_html__('Not installed', 'mesmerize-companion'),
            'installed'             => esc_html__('Installed', 'mesmerize-companion'),
            'active'                => esc_html__('Active', 'mesmerize-companion'),
            'installing_plugins'    => esc_html__('Installing Plugins', 'mesmerize-companion'),
            'installing'            => esc_html__('Installing', 'mesmerize-companion'),
            'activating'            => esc_html__('Activating', 'mesmerize-companion'),
        
        ),
        'dialog_options'   => apply_filters('pt-ocdi/confirmation_dialog_options', array()),
    
    )
);


?>
    <script>
        window.ocdi_not_installed = true;
    </script>
    <!-- OCDI grid layout -->
    <div class="ocdi__gl  js-ocdi-gl ocdi-installer">
        <?php $categories = DemoImportIntegration::getCategories(); ?>
        <?php if ( ! empty($categories)) : ?>
            <div class="ocdi__gl-header  js-ocdi-gl-header">
                <nav class="ocdi__gl-navigation">
                    <ul>
                        <li class="active"><a href="#all" class="ocdi__gl-navigation-link  js-ocdi-nav-link"><?php esc_html_e('All', 'mesmerize-companion'); ?></a></li>
                        <?php foreach ($categories as $key => $name) : ?>
                            <li><a href="#<?php echo esc_attr($key); ?>" class="ocdi__gl-navigation-link  js-ocdi-nav-link"><?php echo esc_html($name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
        <div class="ocdi__gl-item-container  wp-clearfix  js-ocdi-gl-item-container">
            <?php if ( ! count($allDemos)): ?>
                <p class="no-demos-avaiable mesmerize_install_notice"><?php esc_html_e('No demos available', 'mesmerize-companion'); ?></p>
            <?php endif; ?>
            <?php foreach ($allDemos as $index => $import_file) :
                $categories = isset($import_file['categories']) ? $import_file['categories'] : array();
                $sanitized_categories = array_map('sanitize_key', $categories);
                $img_src = isset($import_file['import_preview_image_url']) ? $import_file['import_preview_image_url'] : '';
                ?>
                <div class="ocdi__gl-item js-ocdi-gl-item" data-categories="<?php echo implode(" , ", $sanitized_categories); ?>">
                    <?php if (DemoImportIntegration::isProOnly($index)): ?>
                        <div class="pro-icon"></div>
                    <?php endif; ?>
                    <?php if (DemoImportIntegration::isDemoInDevMode($index)): ?>
                        <div class="dev-mode">DEV MODE<br/><span><?php echo $import_file['modified']; ?></span></div>
                    <?php endif; ?>
                    <div class="ocdi__gl-item-image-container">
                        <?php if ( ! empty($img_src)) : ?>
                            <img class="ocdi__gl-item-image" src="<?php echo esc_url($img_src) ?>">
                        <?php else : ?>
                            <div class="ocdi__gl-item-image  ocdi__gl-item-image--no-image">
                                <?php esc_html_e('No preview image.', 'mesmerize-companion'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="ocdi__gl-item-footer<?php echo ! empty($import_file['preview_url']) ? ' ocdi__gl-item-footer--with-preview' : ''; ?>">
                        <h4 class="ocdi__gl-item-title" title="<?php echo esc_attr($import_file['import_file_name']); ?>">
                            <?php echo esc_html($import_file['import_file_name']); ?>
                        </h4>
                        <?php if (DemoImportIntegration::isProOnly($index)): ?>
                            <button class="ocdi__gl-item-button  button  button-primary pro-only" value="<?php echo esc_attr($index); ?>" onclick="ExtendThemesDemoImporter.showImportPopupProOnly(<?php echo esc_attr($index); ?>);">
                                <?php esc_html_e('Import', 'mesmerize-companion'); ?>
                            </button>
                        <?php else: ?>
                            <button class="ocdi__gl-item-button  button  button-primary  js-ocdi-gl-import-data" value="<?php echo esc_attr($index); ?>">
                                <?php esc_html_e('Import', 'mesmerize-companion'); ?>
                            </button>
                        <?php endif; ?>
                        <?php if ( ! empty($import_file['preview_url'])) : ?>
                            <a class="ocdi__gl-item-button  button" href="<?php echo esc_url($import_file['preview_url']); ?>" target="_blank">
                                <?php esc_html_e('Preview', 'mesmerize-companion'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="clearfix"></div>
        </div>
    </div>
    <div id="tb_install_ocdi" style="display: none;">
        <h2>Please wait 'One Click Demo Import Plugin' is Installing</h2>
        <div id="tb_install_ocdi_response">

        </div>
    </div>


    <!-- pro popup -->
<?php require_once DemoImportIntegration::integrationPath() . "/views/pro-only-popup.php";
