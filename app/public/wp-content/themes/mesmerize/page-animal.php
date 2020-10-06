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
			$query = "SELECT * FROM adoptions WHERE adoption_id = " . $_GET["id"];
			$dog = $wpdb->get_results($query)[0];
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo site_url('/wp-content/themes/mesmerize/adoption-animal-assets/style.css'); ?>">
		<div class="container">
			<div class="row no-gutters">
				<div class="col-md-8 ">
					<section class="aboutSection">
						<h1><?php echo ucwords($dog->name); ?></h1>
					</section>
				</div>
				<div class="col-md-3 col-md-offset-1">
					<div class="row no-gutters">
						<section class="adoptSection">
							<div class="adoptionPicture">
							</div>
							<div class="adoptionInformation">
								<h4 class="adoptTitle">Ready to Adopt?</h4>
								<button class="adoptionButton">Adopt</button>
								<button class="adoptionButton adoptionButtonSpacing">Sponsor</button>
							</div>
							<div class="adoptionMoreInformation">
								<p style="text-align: center;">More Information</p>
							</div>
						</section>
					</div>
					<div class="row no-gutters rowTopSpacing">
						<section class="shelterSection">
							<div class="shelterPicture">
							</div>
							<div class="shelterInfo">
								<h4 class="shelterName">Shelter Name</h4>
								<h5 class="locationPart">Country</h5>
								<h5 class="emailPart">email@emailprovider.com</h5>
							</div>
							<div class="shelterViewMore">
								<p style="text-align: center;">More Information</p>
							</div>
						</section>
					</div>
					<div class="row no-gutters rowTopSpacing">
						<section class="infoSection">
							<div class="infoPicture">
							</div>
							<div class="infoBox container" style="width: 100%;">
								<h4 class="infoTitle">Information</h4>
								<div class="row">
									<div class="col-sm-6">Register Date</div>
									<div class="col-sm-6">: SampleText</div>
									<hr>
								</div>
								<div class="row">
									<div class="col-sm-6">Species</div>
									<div class="col-sm-6">: SampleText</div>
									<hr>
								</div>
								<div class="row">
									<div class="col-sm-6">Breed</div>
									<div class="col-sm-6">: SampleText</div>
									<hr>
								</div>
								<div class="row">
									<div class="col-sm-6">Gender</div>
									<div class="col-sm-6">: SampleText</div>
									<hr>
								</div>
								<div class="row">
									<div class="col-sm-6">Age</div>
									<div class="col-sm-6">: SampleText</div>
									<hr>
								</div>
								<div class="row">
									<div class="col-sm-6">Size</div>
									<div class="col-sm-6">: SampleText</div>
								</div>
							</div>
							<div class="infoViewMore">
								<p style="text-align: center;">More Information</p>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo site_url('/wp-content/themes/mesmerize/adoption-animal-assets/animalScript.js'); ?>" type="text/javascript">
<?php get_footer(); ?>
