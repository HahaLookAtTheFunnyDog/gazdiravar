<?php
defined( 'ABSPATH' ) || exit;

$col_num = get_option('number_of_words_show_in_listing_description', 130);

$desc = wpcf_function()->limit_word_text(strip_tags(get_the_content()), $col_num);
?>
<?php if($desc){ ?>
    <p class="wpneo-short-description"><?php echo $desc; ?></p>
<?php } ?>