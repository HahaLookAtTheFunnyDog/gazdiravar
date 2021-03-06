<?php 
    mesmerize_get_header(); 
    global $wpdb;
    $shelter = $wpdb->get_results("SELECT * FROM shelters INNER JOIN countries ON shelters.country_id = countries.country_id WHERE shelter_id = " . $_GET["id"])[0];
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
	<div class="row">
	<div class="col-md-9" >
    <section class="shelterPageContent">
		<div class="row">
			<div class="col-md-12">
				<h1>About</h1>
				<p>
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras porta nisi tellus, eget laoreet elit consectetur ut. Ut at blandit nisi. Aenean sagittis imperdiet neque in rhoncus. Duis tristique volutpat ipsum, a viverra neque rhoncus sed. Vivamus id cursus metus, quis luctus lectus. Vivamus vulputate magna turpis, in tempus diam posuere at. In semper, nibh non porttitor pretium, dui arcu pretium ligula, vitae feugiat enim tortor ac urna. Integer vitae blandit sapien. Pellentesque placerat tempor justo, ac lacinia tellus porttitor nec. Suspendisse lobortis ornare nibh. Fusce egestas, justo nec faucibus mollis, nibh dui fringilla ipsum, sed tempor lorem nisi sed nulla. In quis venenatis tellus. Sed risus enim, lobortis consectetur leo et, porta pretium lectus. Praesent nec eros vel mi eleifend tristique. Suspendisse potenti. Aliquam est sem, pharetra eu lacus id, vulputate condimentum magna.

Aenean quis ligula semper ex malesuada venenatis. Phasellus interdum blandit mauris, eu porttitor orci feugiat sit amet. Integer tristique elit nisl, eu p est blandit ac. Suspendisse sit amet lacus lectus. Etiam eros nisl, sagittis vitae magna in, aliquam condimentum arcu. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aliquam elementum nibh purus. Nam euismod neque sed egestas porttitor. Fusce porta consequat faucibus. Etiam tristique massa sed nulla cursus, auctor tincidunt nisl iaculis.
				</p>
			</div>
		</div>
	</section>
	</div>
	<div class="col-md-3">
		<section class="shelterPageContent">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h3>Contact Us</h3>
					<h5><?php echo $shelter->country_name; ?></h5>
					<h5><?php echo $shelter->street_name; ?></h5>
					<h5><?php echo strtoupper($shelter->postal_code); ?></h5>
					<h5><?php echo $shelter->email; ?></h5>
				</div>
			</div>
			<div class="row" style="margin-top: 20px;">
				<div class="col-md-12">
						<ul>
							<li>
								<a href="http://<?php echo $shelter->instagram_link; ?>">
								<i class="fab fa-facebook-square fa-2x"></i>
								</a>
							</li>
							<li>
								<a href="http://<?php echo $shelter->twitter_link; ?>">
								<i class="fab fa-twitter-square fa-2x"></i>
								</a>
							</li>
						</ul>
				</div>
			</div>
		</div>
		</section>
	</div>
	</div>
    <section class="shelterPageAdoptions">
    <hr style="margin-top: 2.5rem;">
    <section class="recentlyViewed">
		<h2 style="text-align: center;">Adoptions From <?php echo $shelter->name; ?></h2>
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
