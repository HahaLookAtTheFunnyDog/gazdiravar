<?php
	session_start();
?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<div class="adoptionFormContainer">
</div>
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
			$query = "SELECT a.adoption_id, a.profile_picture_filename, a.name,a.description,b.breed_name,c.age_name,d.gender,f.country_name, species_name, register_date, size, a.shelter_id FROM adoptions a 
			INNER JOIN breeds b ON a.breed_id = b.breed_id 
			INNER JOIN age c ON a.age_id = c.age_id 
			INNER JOIN genders d ON a.gender_id = d.gender_id
			INNER JOIN countries f ON a.country_id = f.country_id 
			INNER JOIN (SELECT species_name, breed_id FROM breeds a INNER JOIN species b ON a.species_id = b.species_id) g ON a.breed_id = g.breed_id
			INNER JOIN sizes h ON a.size_id = h.size_id
			WHERE adoption_id =  " . $_GET["id"];
			$adoption = $wpdb->get_results($query)[0];
			$shelter;
			if($adoption->shelter_id){
				$shelter = $wpdb->get_results("SELECT a.name, a.email, b.country_name FROM shelters a INNER JOIN countries b ON a.country_id = b.country_id")[0];
			}
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo site_url('/wp-content/themes/mesmerize/adoption-animal-assets/style.css'); ?>">
		<div class="container">
			<div class="row no-gutters">
				<div class="col-md-8 ">
					<section class="aboutSection">
						<div class="slideShowContainer">
							<div class="slideContainer">
								<div class="slide active" style="background-color: red;">
								</div>
								<div class="slide" style="background-color: green;">
								</div>
								<div class="slide" style="background-color: black;">
								</div>
								<div class="slide" style="background-color: yellow;">
								</div>
							</div>
							<div class="slidePrev">
								<button><</button>
							</div>
							<div class="slideNext">
							<button>></button>
							</div>
						</div>
						<div class="textPart">
						<h1><?php echo ucwords($adoption->name); ?></h1>
						<h3><?php echo $adoption->country_name; ?></h3>
						<p>
						<?php 
							echo $adoption->description;
						?>
						</p>
						</div>
					</section>
				</div>
				<div class="col-md-3 col-md-offset-1">
					<div class="row no-gutters">
						<section class="adoptSection">
							<div class="adoptionPicture">
							</div>
							<div class="adoptionInformation">
								<h4 class="adoptTitle">Ready to Help?</h4>
								<button class="adoptionButton" id="adoptButton">Adopt</button>
							</div>
							<div class="adoptionMoreInformation">
								<p style="text-align: center;">More Information</p>
							</div>
						</section>
					</div>
					<?php
						if($shelter){
					?>
					<div class="row no-gutters rowTopSpacing">
						<section class="shelterSection">
							<div class="shelterPicture">
							</div>
							<div class="shelterInfo">
								<h4 class="shelterName"><?php echo $shelter->name; ?></h4>
								<h5 class="locationPart"><?php echo $shelter->country_name; ?></h5>
								<h5 class="emailPart"><?php echo $shelter->email; ?></h5>
							</div>
							<div class="shelterViewMore">
								<p style="text-align: center;">More Information</p>
							</div>
						</section>
					</div>
					<?php
						}
					?>
					<div class="row no-gutters rowTopSpacing">
						<section class="infoSection">
							<div class="infoPicture">
							</div>
							<div class="infoBox container" style="width: 100%;">
								<h4 class="infoTitle">Information</h4>
								<div class="row">
									<div class="col-sm-6 borderRight">Register Date</div>
									<div class="col-sm-6 "><p class="dynamicInfo"><?php echo $adoption->register_date; ?></p></div>
								</div>
								<div class="row">
									<div class="col-sm-6 borderRight">Species</div>
									<div class="col-sm-6 "><p class="dynamicInfo"><?php echo $adoption->species_name; ?></p></div>
								</div>
								<div class="row">
									<div class="col-sm-6 borderRight">Breed</div>
									<div class="col-sm-6"><p class="dynamicInfo"><?php echo $adoption->breed_name; ?></p></div>
								</div>
								<div class="row">
									<div class="col-sm-6 borderRight">Gender</div>
									<div class="col-sm-6"><p class="dynamicInfo"><?php echo $adoption->gender; ?></p></div>
								</div>
								<div class="row">
									<div class="col-sm-6 borderRight">Age</div>
									<div class="col-sm-6"><p class="dynamicInfo"><?php echo $adoption->age_name; ?></p></div>
								</div>
								<div class="row">
									<div class="col-sm-6 borderRight">Size</div>
									<div class="col-sm-6"><p class="dynamicInfo"><?php echo $adoption->size; ?></p></div>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
			<?php
				$query = "SELECT a.adoption_id, a.profile_picture_filename, a.name,a.description,b.breed_name,c.age_name,d.gender,f.country_name, g.size FROM adoptions a 
				INNER JOIN breeds b ON a.breed_id = b.breed_id 
				INNER JOIN age c ON a.age_id = c.age_id 
				INNER JOIN genders d ON a.gender_id = d.gender_id
				INNER JOIN countries f ON a.country_id = f.country_id 
				INNER JOIN sizes g ON a.size_id = g.size_id
				WHERE d.gender = '" . $adoption->gender . "' 
				AND c.age_name = '" . $adoption->age_name . "'
				 AND g.size = '" . $adoption->size . 
				"' AND a.adoption_id <> '" . $adoption->adoption_id . "' LIMIT 4";
				$recommendAdoptions = $wpdb->get_results($query);
				if($recommendAdoptions){
			?>
			<div class="row no-gutters">
				<hr style="margin-top: 2.5rem;">
				<h2 style="text-align: center;">Similar Adoptions</h2>
				<div class="col-md-12">
					<section class="recommendedSection">
					<div class="carousel-inner">
						<div class="track">
							<?php
								foreach($recommendAdoptions as $adoption){
									?>
									<div class="card-container">
										<form method="GET" action="<?php echo site_url('/adoptions/animal/'); ?>">
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
					</section>
				</div>
			</div>
			<?php
				}
			?>
		</div>
	</div>
</div>
<div class="adoptionForm">
		<h1>Adoption Contact</h1>
		<div class="formAdoption">
			<label for="fname">First Name</label>
			<input required class="adoptionFormInput" type="text" id="fname" name="fname">
			<label for="lname">Last Name</label>
			<input required class="adoptionFormInput" type="text" id="lname" name="lname">
			<label for="emailAddr">Email</label>
			<input required class="adoptionFormInput" type="text" id="emailAddr" name="emailAddr">
			<label for="country">Country</label>
			<select required class="adoptionFormInput" id="country" name="country" class="form-control" style="margin: 0;">
				<?php
				$countries = $wpdb->get_results("
					SELECT country_name FROM countries"
				);
				foreach($countries as $country){
					?>
					<option 
					value="<?php echo $country->country_name; ?>"><?php echo $country->country_name; ?></option>
					<?php
				}
				?>
			</select>
			<label for="message">Additional Comments (Optional)</label>
			<textarea></textarea>
			<ul>
				<li>
					<button id="adoptionFormBtn">Submit</button>
				</li>
				<li>
					<button id="adoptFormCancel">Cancel</button>
				</li>
			</ul>
		</div>
	</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
<script src="<?php echo site_url('/wp-content/themes/mesmerize/adoption-animal-assets/animalScript.js'); ?>" type="text/javascript">
<?php get_footer(); ?>
