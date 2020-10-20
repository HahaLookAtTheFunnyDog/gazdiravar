<?php 
    mesmerize_get_header(); 
    global $wpdb;
    $shelter = $wpdb->get_results("SELECT * FROM shelters WHERE shelter_id = " . $_GET["id"])[0];
?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url('/wp-content/themes/mesmerize/adoption-assets/style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo site_url('/wp-content/themes/mesmerize/adoption-animal-assets/style.css'); ?>">

<div id='page-content' class="page-content">
    <div class="<?php mesmerize_page_content_wrapper_class(); ?>">
    <section>
    <h2 style="text-align: center"><?php echo $shelter->name; ?></h2>
	<hr>
    <div class="container-fluid">
			<div class="row no-gutters" style="margin: auto;">
				<div class="col-md-12">
					<div class="slider" style="cursor: pointer; ">
						<?php
						$featuredadoptions = $wpdb->get_results('SELECT * FROM adoptions a INNER JOIN featured b ON a.adoption_id = b.adoption_id');
						$featuredCount = 0;
						foreach($featuredadoptions as $adoption){
							if($featuredCount==0){
								?>
								<div class="slide activeSlide"
								<?php
							}
							else{
								?>
								<div class="slide"
								<?php
							}
							?>
							style="background-image: url('<?php echo site_url("/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/featuredCover.jpg"); ?>')" id="featured<?php echo $adoption->adoption_id; ?>"
							onclick='featuredClick()'>
						</div>
						<?php
						$featuredCount++;
					}
					?>
				</div>
			</div>
		</div>
        <div class="row no-gutters" style="margin: auto;">
			<div class="col-md-12">
				<div class="buttonContainer">
					<ul class="featuredButtons" style="list-style: none;">
						<?php
						for($i = 1; $i <= count($featuredadoptions); $i++){
							?>
							<li>
								<button id="featured<?php echo $i; ?>" onclick="featuredButton(<?php echo $i ?>)" class="featuredButton
									<?php
									if($i == 1){
										?>
										activeButton
										<?php
									}
									?>
									"></button>
								</li>
								<?php
							}
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
    </section>
    <section class="shelterPageContent">
        <div class="shelterPageContentPicture">
        </div>
    </section>
    <section class="shelterPageAdoptions">
    <hr style="margin-top: 2.5rem;">
    <section class="recentlyViewed">
		<h2 style="text-align: center;">More From <?php echo $shelter->name; ?></h2>
		<div class="carousel-container">
			<div class="carousel-inner">
				<div class="track">
                    <?php 
                    $query = "SELECT a.adoption_id, a.profile_picture_filename, a.name,a.description,b.breed_name,c.age_name,d.gender,f.country_name FROM adoptions a 
                    INNER JOIN breeds b ON a.breed_id = b.breed_id 
                    INNER JOIN age c ON a.age_id = c.age_id 
                    INNER JOIN genders d ON a.gender_id = d.gender_id
                    INNER JOIN countries f ON a.country_id = f.country_id WHERE a.shelter_id = " . $_GET["id"];
                    $adoptions = $wpdb->get_results($query);
					foreach($adoptions as $adoption){
						?>
						<div class="card-container">
							<form method="GET" action="animal/">
								<input type="hidden" name="id" value="<?php echo $adoption->adoption_id; ?>">
								<div class="card boxShadowAnimate" onclick="this.parentNode.submit();" style="cursor: pointer;">
									<?php
										$imgSrc = site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.png');
										if($adoption->profile_picture_filename){
											$imgSrc = site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/' . $adoption->profile_picture_filename . '.png');
										}
									?>
									<img src="<?php echo $imgSrc; ?>" class="round icon iconSmall">
									<h6 style="text-align: center;"><?php echo $adoption->name; ?></h6>
									<p style="text-align: center; class="small italic"><?php echo $adoption->country_name; ?></p>
								</a>
							</div>
						</form>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="nav">
			<button class="prev">
				<i class="material-icons">
					<
				</i>
			</button>
			<button class="next">
				<i class="material-icons">
					>
				</i>
			</button>
		</div>
	</div>
</section>
    </section>
    </div>
</div>
<script src="<?php echo site_url('/wp-content/themes/mesmerize/adoption-assets/adoptionsScript.js'); ?>" type="text/javascript">
<?php get_footer(); ?>
