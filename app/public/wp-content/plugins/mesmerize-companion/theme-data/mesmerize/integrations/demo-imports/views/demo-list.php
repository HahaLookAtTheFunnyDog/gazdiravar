<?php
/**
 * The plugin page view - the "settings" page of the plugin.
 *
 * @package ocdi
 */

namespace OCDI;


use ExtendThemes\DemoImportIntegration\DemoImportIntegration;

wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style('wp-jquery-ui-dialog');
wp_enqueue_style('ocdi-main-css', PT_OCDI_URL . 'assets/css/main.css', array(), PT_OCDI_VERSION);
wp_enqueue_style('extendthemes-demo-list', MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL . '/assets/demo-list.css', array(), PT_OCDI_VERSION);
wp_enqueue_script('ocdi-main-js', PT_OCDI_URL . 'assets/js/main.js', array('jquery', 'jquery-ui-dialog'), PT_OCDI_VERSION);

$extemdthemes_theme_importer = OneClickDemoImport::get_instance();
$predefined_themes           = $extemdthemes_theme_importer->import_files;

wp_localize_script('ocdi-main-js', 'ocdi',
    array(
        'ajax_url'         => admin_url('admin-ajax.php'),
        'ajax_nonce'       => wp_create_nonce('ocdi-ajax-verification'),
        'import_files'     => $predefined_themes,
        'wp_customize_on'  => apply_filters('pt-ocdi/enable_wp_customize_save_hooks', false),
        'import_popup'     => false,
        'theme_screenshot' => wp_get_theme()->get_screenshot(),
        'texts'            => array(
            'missing_preview_image' => esc_html__('No preview image defined for this import.', 'mesmerize-companion'),
            'dialog_title'          => esc_html__('Are you sure?', 'mesmerize-companion'),
            'dialog_no'             => esc_html__('Cancel', 'mesmerize-companion'),
            'dialog_yes'            => esc_html__('Yes, import!', 'mesmerize-companion'),
            'selected_import_title' => "",
            'not-installed'         => esc_html__('Not installed', 'mesmerize-companion'),
            'installed'             => esc_html__('Installed', 'mesmerize-companion'),
            'active'                => esc_html__('Active', 'mesmerize-companion'),
            'installing_plugins'    => esc_html__('Installing Plugins', 'mesmerize-companion'),
            'installing'            => esc_html__('Installing', 'mesmerize-companion'),
            'activating'            => esc_html__('Activating', 'mesmerize-companion'),
            'importing_title'       => esc_html__('Importing the following demo site', 'mesmerize-companion'),
        
        ),
        'dialog_options'   => apply_filters('pt-ocdi/confirmation_dialog_options', array()),
    
    )
);


if ( ! empty($extemdthemes_theme_importer->import_files) && isset($_GET['import-mode']) && 'manual' === $_GET['import-mode']) {
    $predefined_themes = array();
}

?>
    
    <?php if (empty($extemdthemes_theme_importer->import_files)) : ?>
    <div class="notice  notice-info  is-dismissible">
        <p><?php esc_html_e('There are no predefined import files available in this theme. Please upload the import files manually!', 'mesmerize-companion'); ?></p>
    </div>
<?php endif; ?>


    <!-- OCDI grid layout -->
    <?php if ( ! isset($_GET['manual-upload'])): ?>
    <div class="ocdi__gl  js-ocdi-gl">
        <?php
        // Prepare navigation data.
        $categories = DemoImportIntegration::getCategories();
        ?>
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
                <div class="ocdi__gl-search">
                    <input type="search" class="ocdi__gl-search-input  js-ocdi-gl-search" name="ocdi-gl-search" value="" placeholder="<?php esc_html_e('Search demos...', 'mesmerize-companion'); ?>">
                </div>
            </div>
        <?php endif; ?>
        <div class="ocdi__gl-item-container  wp-clearfix  js-ocdi-gl-item-container">
            <?php foreach ($predefined_themes as $index => $import_file) : ?>
                
                <?php
                // Prepare import item display data.
                $img_src               = isset($import_file['import_preview_image_url']) ? $import_file['import_preview_image_url'] : '';
                $demo_required_plugins = DemoImportIntegration::getDemoPlugins($import_file);
                // Default to the theme screenshot, if a custom preview image is not defined.
                if (empty($img_src)) {
                    $theme   = wp_get_theme();
                    $img_src = $theme->get_screenshot();
                }
                
                ?>
                <div class="ocdi__gl-item js-ocdi-gl-item" data-categories="<?php echo esc_attr(Helpers::get_demo_import_item_categories($import_file)); ?>" data-name="<?php echo esc_attr(strtolower($import_file['import_file_name'])); ?>">
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
                            <div class="ocdi__gl-item-image  ocdi__gl-item-image--no-image"><?php esc_html_e('No preview image.', 'mesmerize-companion'); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="ocdi__gl-item-footer<?php echo ! empty($import_file['preview_url']) ? '  ocdi__gl-item-footer--with-preview' : ''; ?>">
                        <button style="display: none" class="js-ocdi-gl-import-data" data-name="install-now" value="<?php echo esc_attr($index); ?>"></button>
                        <h4 class="ocdi__gl-item-title" title="<?php echo esc_attr($import_file['import_file_name']); ?>"><?php echo esc_html($import_file['import_file_name']); ?></h4>
                        
                        <?php if (DemoImportIntegration::isProOnly($index)): ?>
                            <button class="ocdi__gl-item-button  button  button-primary pro-only" value="<?php echo esc_attr($index); ?>" onclick="ExtendThemesDemoImporter.showImportPopupProOnly(<?php echo esc_attr($index); ?>);">
                                <?php esc_html_e('Import', 'mesmerize-companion'); ?>
                            </button>
                        <?php else: ?>
                            <button class="ocdi__gl-item-button  button  button-primary" value="<?php echo esc_attr($index); ?>" onclick="ExtendThemesDemoImporter.showImportPopup(<?php echo esc_attr($index); ?>);">
                                <?php esc_html_e('Import', 'mesmerize-companion'); ?>
                            </button>
                        <?php endif; ?>
                        <?php if ( ! empty($import_file['preview_url'])) : ?>
                            <a class="ocdi__gl-item-button  button" href="<?php echo esc_url($import_file['preview_url']); ?>" target="_blank"><?php esc_html_e('Preview', 'mesmerize-companion'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="clearfix"></div>
        </div>
    </div>
<?php else: ?>
    <div class="ocdi__file-upload-container">
        <h2><?php esc_html_e('Manual demo files upload', 'mesmerize-companion'); ?></h2>

        <div class="ocdi__file-upload">
            <h3><label for="content-file-upload"><?php esc_html_e('Choose a XML file for content import:', 'mesmerize-companion'); ?></label></h3>
            <input id="ocdi__content-file-upload" type="file" name="content-file-upload">
        </div>

        <div class="ocdi__file-upload">
            <h3><label for="widget-file-upload"><?php esc_html_e('Choose a WIE or JSON file for widget import:', 'mesmerize-companion'); ?></label></h3>
            <input id="ocdi__widget-file-upload" type="file" name="widget-file-upload">
        </div>

        <div class="ocdi__file-upload">
            <h3><label for="customizer-file-upload"><?php esc_html_e('Choose a DAT file for customizer import:', 'mesmerize-companion'); ?></label></h3>
            <input id="ocdi__customizer-file-upload" type="file" name="customizer-file-upload">
        </div>
        
        <?php if (class_exists('ReduxFramework')) : ?>
            <div class="ocdi__file-upload">
                <h3><label for="redux-file-upload"><?php esc_html_e('Choose a JSON file for Redux import:', 'mesmerize-companion'); ?></label></h3>
                <input id="ocdi__redux-file-upload" type="file" name="redux-file-upload">
                <div>
                    <label for="redux-option-name" class="ocdi__redux-option-name-label"><?php esc_html_e('Enter the Redux option name:', 'mesmerize-companion'); ?></label>
                    <input id="ocdi__redux-option-name" type="text" name="redux-option-name">
                </div>
            </div>
        <?php endif; ?>
    </div>

    <p class="ocdi__button-container">
        <button class="ocdi__button  button  button-hero  button-primary  js-ocdi-import-data"><?php esc_html_e('Import Demo Data', 'mesmerize-companion'); ?></button>
    </p>

<?php endif; ?>

    <div id="js-ocdi-modal-content"></div>


    <p class="ocdi__ajax-loader  js-ocdi-ajax-loader">
        <?php esc_html_e('Please wait! The import process can take a few minutes.', 'mesmerize-companion'); ?>
        <span class="extendthemes-progress-bar">
            <span class="indeterminate"></span>
        </span>
    </p>

    <div class="ocdi__response  js-ocdi-ajax-response"></div>

    <div class="extendthemes-after-import-controls" style="display: none">
        <a class="button button-primary" href="<?php echo esc_attr(site_url()); ?>" target="_blank"><?php esc_html_e('See the result', 'mesmerize-companion'); ?></a>
        <a class="button" href="<?php menu_page_url('mesmerize-welcome'); ?>&tab=demo-imports"><?php esc_html_e('Back to demo import', 'mesmerize-companion'); ?></a>
    </div>
    <?php

require_once DemoImportIntegration::integrationPath() . "/views/demo-install-popup.php";
require_once DemoImportIntegration::integrationPath() . "/views/pro-only-popup.php";
