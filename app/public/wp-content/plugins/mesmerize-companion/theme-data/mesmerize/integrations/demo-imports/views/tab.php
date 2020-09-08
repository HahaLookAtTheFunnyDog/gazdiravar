<?php
wp_enqueue_style('mesmerize-demo-import-style', MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL . "/assets/style.css");
wp_enqueue_script('mesmerize-demo-import-script', MESMERIZE_DEMOS_IMPORTS_INTEGRATION_URL . "/assets/demo-import.js", array('jquery'), null, true);

wp_enqueue_script('plugin-install');
if ('plugin-information' != $tab) {
    add_thickbox();
}

$body_id = $tab;

wp_enqueue_script('updates');

?>
<div class="tab-cols">
    <h2 class="mesmerize-import-demo-sites"><?php _e('Import Demo sites with one click', 'mesmerize-companion'); ?></h2>

    <div class="ocdi  wrap  about-wrap">
        <?php
        if (class_exists('OCDI\\OneClickDemoImport')) {
            require MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH . "/views/demo-list.php";
        } else {
            require MESMERIZE_DEMOS_IMPORTS_INTEGRATION_PATH . "/views/ocdi-installer.php";
        }
        ?>
    </div>
</div>
