<?php
defined( 'ABSPATH' ) || exit;

$location = wpcf_function()->campaign_location();
?>
<?php if($location){ ?>
    <div class="wpneo-location">
        <i class="wpneo-icon wpneo-icon-location"></i>
        <div class="wpneo-meta-desc"><?php echo wpcf_function()->campaign_location(); ?></div>
    </div>
<?php } ?>
