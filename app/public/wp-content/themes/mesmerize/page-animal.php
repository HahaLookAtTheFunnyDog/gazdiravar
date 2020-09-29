<?php
	session_start();
?>
<?php mesmerize_get_header(); ?>
<div id='page-content' class="page-content">
	<div class="<?php mesmerize_page_content_wrapper_class(); ?>">
        <?php
			if(is_array($_SESSION["recentlyViewed"])){
				if(!(in_array($_GET["id"],$_SESSION["recentlyViewed"]))){
					if(count($_SESSION["recentlyViewed"]) == 15){
						array_shift($_SESSION["recentlyViewed"]);
						array_push($_SESSION["recentlyViewed"], $_GET["id"]);
					}
					else{
						array_push($_SESSION["recentlyViewed"], $_GET["id"]);
					}
				}
			}
			else{
				$_SESSION["recentlyViewed"] = array($_GET["id"]);
			}
			global $wpdb;
			$query = "SELECT * FROM adoptionss WHERE adoption_id = " . $_GET["id"];
			$dog = $wpdb->get_results($query);
		?>
	</div>
</div>
<?php get_footer(); ?>
