<?php mesmerize_get_header(); ?>
<div id='page-content' class="page-content">
	<div class="<?php mesmerize_page_content_wrapper_class(); ?>">
		<?php
			global $wpdb;
			$query = "SELECT * FROM dogs WHERE dog_id = " . $_GET["id"];
			$dog = $wpdb->get_results($query);
			print_r($dog);
		?>
	</div>
</div>
<?php get_footer(); ?>

