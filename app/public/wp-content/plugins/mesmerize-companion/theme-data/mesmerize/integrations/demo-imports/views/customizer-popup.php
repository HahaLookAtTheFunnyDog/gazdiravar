<link type="text/css" rel="stylesheet" href="<?php echo MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL . "/assets/ocdi.css"; ?>">

<?php

use ExtendThemes\DemoImportIntegration\DemoImportIntegration;


$demosURL = admin_url("/themes.php?page=mesmerize-welcome&tab=demo-imports");
$allDemos = \ExtendThemes\DemoImportIntegration\DemoImportIntegration::getDemoDataOCDIFormat();
?>

<!-- OCDI grid layout -->
<div class="ocdi__gl  js-ocdi-gl">
    <div class="popup-header">
        <div class="close-button"><span class="dashicons dashicons-no"></span></div>
        <h2><?php esc_html_e('Start from a complete predesigned site', 'mesmerize-companion'); ?></h2>
        <p><?php esc_html_e('Start from a predesigned site and go live in no time', 'mesmerize-companion'); ?></p>
        
        
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

    </div>
    <div class="ocdi__gl-item-container  wp-clearfix  js-ocdi-gl-item-container">
        <?php foreach ($allDemos as $index => $import_file): ?>
            <?php
            $categories           = isset($import_file['categories']) ? $import_file['categories'] : array();
            $sanitized_categories = array_map('sanitize_key', $categories);
            $img_src              = isset($import_file['import_preview_image_url']) ? $import_file['import_preview_image_url'] : '';
            
            ?>
            <div class="ocdi__gl-item js-ocdi-gl-item" data-categories="<?php echo implode(",", $sanitized_categories); ?>">
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
                    <h4 class="ocdi__gl-item-title" title="<?php echo esc_attr($import_file['import_file_name']); ?>"><?php echo esc_html($import_file['import_file_name']); ?></h4>
                    <a class="ocdi__gl-item-button  button  button-primary  js-ocdi-gl-import-data" href="<?php echo $demosURL ?>&demo=<?php echo $index; ?>"><?php esc_html_e('Import', 'mesmerize-companion'); ?></a>
                    <?php if ( ! empty($import_file['preview_url'])) : ?>
                        <a class="ocdi__gl-item-button  button" href="<?php echo esc_url($import_file['preview_url']); ?>" target="_blank"><?php esc_html_e('Preview', 'mesmerize-companion'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
