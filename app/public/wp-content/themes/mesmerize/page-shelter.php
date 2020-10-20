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
    </section>
    </div>
</div>
<script src="<?php echo site_url('/wp-content/themes/mesmerize/adoption-assets/adoptionsScript.js'); ?>" type="text/javascript">
<?php get_footer(); ?>
