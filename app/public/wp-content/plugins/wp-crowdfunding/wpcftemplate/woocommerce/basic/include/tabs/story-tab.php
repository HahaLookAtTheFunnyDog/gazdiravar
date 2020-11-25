<?php
defined( 'ABSPATH' ) || exit;
global $post;
?>

<?php if($post->post_content) { ?>
    <div class="tab-description tab_col_9 tab-campaign-story-left">
        <h2><?php _e('Campaign Story', 'wp-crowdfunding') ?></h2>
        <?php the_content(); ?>
    </div>
<?php } ?>

<div class="tab-rewards tab_col_3 tab-campaign-story-right">
    <?php do_action('wpcf_campaign_story_right_sidebar'); ?>
	<div style="clear: both"></div>
</div>