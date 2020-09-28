<?php
	session_start();
?>
<?php mesmerize_get_header(); ?>
<div id='page-content' class="page-content">
	<div class="<?php mesmerize_page_content_wrapper_class(); ?>">
		<?php
			print_r($_GET["id"]);
			if(is_array($_SESSION["recentlyViewed"])){
				if(!in_array($_GET["id"][0], $_SESSION["recentlyViewed"], true)){
					array_push($_SESSION["recentlyViewed"], $_GET["id"][0]);
				}
			}
			else{
				$_SESSION["recentlyViewed"] = array($_GET["id"][0]);
			}
			global $wpdb;
			$query = "SELECT * FROM dogs WHERE dog_id = " . $_GET["id"];
			$dog = $wpdb->get_results($query);
		?>
	</div>
</div>
<?php get_footer(); ?>

