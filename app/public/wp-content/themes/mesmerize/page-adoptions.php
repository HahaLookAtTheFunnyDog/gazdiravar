<?php mesmerize_get_header(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url('/wp-content/themes/mesmerize/adoption-assets/style.css'); ?>">
<?php 
	function appendFormHelper($filterArr, $arrName){
		if(is_array($filterArr) || is_object($filterArr)){
			
			foreach($filterArr as $filterVar){
				?>
				<input type="hidden" name="<?php echo $arrName . "[]" ?>" value="<?php echo $filterVar ?>">
				<?php
			}
		}
	}
	function appendForms(){
		appendFormHelper($_GET["breed"], "breed");
		appendFormHelper($_GET["age"], "age");
		appendFormHelper($_GET["gender"], "gender");
		appendFormHelper($_GET["size"], "size");
	}
?>
<div id='page-content' class="page-content">
	<div class="<?php mesmerize_page_content_wrapper_class(); ?>">
			<h2 style="text-align: center">Featured Adoptions</h2>
			<hr>
			<div class="container-fluid">
				<div class="row no-gutters" style="margin: auto;">
					<div class="col-md-12">
						<div class="slider">
							<?php
							global $wpdb;
							$featuredDogs = $wpdb->get_results('SELECT * FROM dogs a INNER JOIN featured b ON a.dog_id = b.dog_id');
							$featuredCount = 0;
							foreach($featuredDogs as $dog){
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
								style="background-image: url('<?php echo site_url("/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/featuredCover.jpg"); ?>'); ">
								<div class="featuredContent">
									<h2 style="color: white;">
										Hi I'm <?php echo $dog->name ?>
									</h2>
									<p style="color: white;">
										<?php echo $dog->description ?>
									</p>
								</div>
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
							for($i = 1; $i <= count($featuredDogs); $i++){
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
		<h2 style="text-align: center; margin-top: 10px;">Adoptions</h2>
		<hr>
		<div class="flexbox">
			<div class="col-md-3" style="width: 100%; margin-right: .5rem;">
				<div class="filters">
					<h2>
						Filters
					</h2>
					<ul>
						<li>
							<hr class="filterDivider">
							<h4>Breed</h4>
							<form method="get">
								<ul class="scrollRadio">
									<?php
									$breeds = $wpdb->get_results('SELECT breed_name FROM breeds ORDER BY breed_name ASC');
									foreach($breeds as $breed){
										?>
										<li>
											<input type="checkbox" id="<?php echo str_replace(' ', '', $breed->breed_name); ?>" name="breed[]" value="<?php echo $breed->breed_name ?>" class="dogSelection"
											<?php 
											if(is_array($_GET["breed"]) || is_object($_GET["breed"])){
												foreach($_GET["breed"] as $alreadySubmittedBreed){
													if($alreadySubmittedBreed == $breed->breed_name){
														?>
														disabled="disabled"
														<?php
													}
												}
											}
											?>
											>
											<label for="<?php echo str_replace(' ', '', $breed->breed_name); ?>"> <?php echo $breed->breed_name; ?> </label>
											<p class="quantity alignMargin">
												<?php
												$breedUpper = strtoupper($breed->breed_name);
												$queryPrepare = $wpdb->prepare("SELECT COUNT(breed_name) AS breedCount FROM dogs a INNER JOIN breeds b ON a.breed_id = b.breed_id WHERE UPPER(breed_name) = %s", "$breedUpper");
												$queryResult = $wpdb->get_results($queryPrepare);
												echo "(" . $queryResult[0]->breedCount . ")";
												?>
											</p>
											<br>
										</li>
										<?php
									}
									?>
								</ul>
								<?php
								appendForms();
								?>
								<button type="submit" class="filterSubmit hideFilterSubmit" name="breedSubmit" id="breedFilterSubmit">APPLY</button>
							</form>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Age</h4>
							<form method="get">
								<ul>
									<?php
									$ages = $wpdb->get_results('SELECT age_name FROM age');
									foreach($ages as $age){
										?>
										<li>
											<input type="checkbox" id="<?php echo str_replace(' ', '', $age->age_name); ?>" name="age[]" value="<?php echo $age->age_name ?>" class="ageSelection"
											<?php 
											if(is_array($_GET["age"]) || is_object($_GET["age"])){
												foreach($_GET["age"] as $alreadySubmittedAge){
													if($alreadySubmittedAge == $age->age_name){
														?>
														disabled="disabled"
														<?php
													}
												}
											}
											?>
											>
											<label for="<?php echo str_replace(' ', '', $age->age_name); ?>"> <?php echo $age->age_name ?> </label>
											<p class="quantity">
												<?php
												$ageUpper = strtoupper($age->age_name);
												$queryPrepare = $wpdb->prepare("SELECT COUNT(age_name) AS ageCount FROM dogs a INNER JOIN age b ON a.age_id = b.age_id WHERE UPPER(age_name) = %s", "$ageUpper");
												$queryResult = $wpdb->get_results($queryPrepare);
												echo "(" . $queryResult[0]->ageCount . ")";
												?>
											</p>
											<br>
										</li>
										<?php
									}
									?>
									<?php
									appendForms();
									?>
								</ul>
								<button type="submit" class="filterSubmit hideFilterSubmit" id="ageFilterSubmit">APPLY</button>
							</form>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Gender</h4>
							<form method="get">
								<ul>
									<?php
									$genders = $wpdb->get_results('SELECT gender FROM genders');
									foreach($genders as $gender){
										?>
										<li>
											<input type="checkbox" id="<?php echo str_replace(' ', '', $gender->gender); ?>" name="gender[]" value="<?php echo str_replace(' ', '', $gender->gender); ?>" class="genderSelection"
											<?php 
											if(is_array($_GET["gender"]) || is_object($_GET["gender"])){
												foreach($_GET["gender"] as $alreadySubmittedgender){
													if($alreadySubmittedgender == $gender->gender){
														?>
														disabled="disabled"
														<?php
													}
												}
											}
											?>
											>
											<label for="<?php echo str_replace(' ', '', $gender->gender); ?>"> <?php echo $gender->gender ?> </label>
											<p class="quantity">
												<?php 
												$genderUpper = strtoupper($gender->gender);
												$queryPrepare = $wpdb->prepare("SELECT COUNT(gender) AS genderCount FROM dogs a INNER JOIN genders b ON a.gender_id = b.gender_id WHERE UPPER(gender) = %s", "$genderUpper");
												$queryResult = $wpdb->get_results($queryPrepare);
												echo "(" . $queryResult[0]->genderCount . ")";
												?>
											</p>
											<br>
										</li>
										<?php
									}
									?>
								</ul>
								<?php
								appendForms();
								?>
								<button type="submit" class="filterSubmit hideFilterSubmit" id="genderFilterSubmit">APPLY</button>
							</form>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Size</h4>
							<form method="get">
								<ul>
									<?php
									$sizes = $wpdb->get_results('SELECT size FROM sizes');
									foreach($sizes as $size){
										?>
										<li>
											<input type="checkbox" id="<?php echo str_replace(' ', '', $size->size); ?>" name="size[]" value="<?php echo str_replace(' ', '', $size->size); ?>" class="sizeSelection"
											<?php 
											if(is_array($_GET["size"]) || is_object($_GET["size"])){
												foreach($_GET["size"] as $alreadySubmittedsize){
													if($alreadySubmittedsize == $size->size){
														?>
														disabled="disabled"
														<?php
													}
												}
											}
											?>
											>
											<label for="<?php echo str_replace(' ', '', $size->size); ?>"> <?php echo $size->size ?> </label>
											<p class="quantity">
												<?php
												$sizeUpper = strtoupper($size->size);
												$queryPrepare = $wpdb->prepare("SELECT COUNT(size) AS sizeCount FROM dogs a INNER JOIN sizes b ON a.size_id = b.size_id WHERE UPPER(size) = %s", "$sizeUpper");
												$queryResult = $wpdb->get_results($queryPrepare);
												echo "(" . $queryResult[0]->sizeCount . ")";
												?>
											</p>
											<br>
										</li>
										<?php
									}
									?>
								</ul>
								<?php
								appendForms();
								?>
								<button type="submit" class="filterSubmit hideFilterSubmit" id="sizeFilterSubmit">APPLY</button>
							</form>
						</li>
						<hr class="filterDivider">
						<li>
						<h4>Country</h4> 
						<select id="country" name="country" class="form-control" style="margin: 0;">
						<?php
							//My IP for testing since we're on local host
							//Can retrieve the actual user ip once the website is live
							$user_ip = "70.30.213.116";
							$geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
							$userCountry = $geo["geoplugin_countryName"];
							$countries = $wpdb->get_results("
								SELECT DISTINCT b.country_name FROM dogs a
								INNER JOIN countries b 
								ON a.country_id = b.country_id"
							);
							$countryAvailable = false;
							foreach($countries as $country){
								?>
								<option 
								<?php 
									if(strtoupper($userCountry) === strtoupper($country->country_name)){
										echo " selected ";
										$countryAvailable = true;
									}
								?>
								value="<?php echo $country->country_name; ?>"><?php echo $country->country_name; ?></option>
								<?php
							}
						?>
						</select>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-9">
				<div class="row">
					<div class="col-sm-12 appliedFiltersHeader" >
						<?php
						if(is_array($_GET["breed"]) || is_array($_GET["age"]) || is_array($_GET["gender"]) || is_array($_GET["size"])){
							?>
							<h4>
								Filters Applied
							</h4>
							<?php
						}
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<ul class="filterChoices">
							<?php
							if(is_array($_GET["breed"]) || is_object($_GET["breed"])){
								foreach($_GET["breed"] as $breed){
									?>
									<li class="filtersli">
										<?php echo $breed ?>
										<span class="filterClose" onclick='resubmit(
											<?php echo json_encode($_GET["breed"]); ?> ,
											<?php echo json_encode($_GET["age"]); ?>,
											<?php echo json_encode($_GET["gender"]); ?>,
											<?php echo json_encode($_GET["size"]); ?>,
											"<?php echo $breed ?>"
											);'>
											x
										</span>
									</li>
									<?php
								}
							}
							if(is_array($_GET["age"]) || is_object($_GET["age"])){
								foreach($_GET["age"] as $age){
									?>
									<li class="filtersli">
										<?php echo $age ?>
										<span class="filterClose"  onclick='resubmit(
											<?php echo json_encode($_GET["breed"]); ?>,
											<?php echo json_encode($_GET["age"]); ?>,
											<?php echo json_encode($_GET["gender"]); ?>,
											<?php echo json_encode($_GET["size"]); ?>,
											"<?php echo $age ?>"
											);'>
											x
										</span>
									</li>
									<?php
								}
							}
							if(is_array($_GET["gender"]) || is_object($_GET["gender"])){
								foreach($_GET["gender"] as $gender){
									?>
									<li class="filtersli">
										<?php echo $gender ?>
										<span class="filterClose"  onclick='resubmit(
											<?php echo json_encode($_GET["breed"]); ?>,
											<?php echo json_encode($_GET["age"]); ?>,
											<?php echo json_encode($_GET["gender"]); ?>,
											<?php echo json_encode($_GET["size"]); ?>,
											"<?php echo $gender ?>"
											);'>
											x
										</span>
									</li>
									<?php
								}
							}
							if(is_array($_GET["size"]) || is_object($_GET["size"])){
								foreach($_GET["size"] as $size){
									?>
									<li class="filtersli">
										<?php echo $size ?>
										<span class="filterClose"  onclick='resubmit(
											<?php echo json_encode($_GET["breed"]); ?>,
											<?php echo json_encode($_GET["age"]); ?>,
											<?php echo json_encode($_GET["gender"]); ?>,
											<?php echo json_encode($_GET["size"]); ?>,
											"<?php echo $size ?>"
											);'>
											x
										</span>
									</li>
									<?php
								}
							}
							if(is_array($_GET["breed"]) || is_array($_GET["age"]) || is_array($_GET["gender"]) || is_array($_GET["size"])){
								?>
								<li class="clearAll" onclick="clearAll()">
									Clear All
								</li>		
								<?php
							}
							?>
						</ul>
					</div>
				</div>
				<div class="row">
					<?php 
					$pageSize = 9;
					$page = $_GET["pid"] ?? 1;
					$pageLowerLimit = ($page-1) * $pageSize;
					$dogCount = $wpdb->get_results("SELECT COUNT(*) as NumberOfDogs FROM dogs")[0]->NumberOfDogs;
					$limitClause = " LIMIT " . $pageLowerLimit . "," . $pageSize;
					$orderClause = " ORDER BY a.dog_id ASC";
					function argumentCreator($arr,$columnName){
						$arguments = "";
						$edited = false;
						if(is_array($arr)){
							foreach($arr as $x){
								if($edited){
									$arguments .= " OR {$columnName} = '{$x}'";
								}
								else{
									$arguments .= "{$columnName} = '{$x}'";
								}
								$edited = true;
							}
						}
						return $arguments;
					}
					function finalQueryHelper($arguments,$finalQuery, $finalQueryAdjusted, $dogCountBaseQuery){
						foreach($arguments as $argument){
							if($argument){
								if($finalQueryAdjusted){
									$finalQuery .= " AND ( " . $argument . " )";
									$dogCountBaseQuery .= " AND ( " . $argument . " )";
								}
								else{
									$finalQuery .= "( " . $argument . " )";
									$dogCountBaseQuery .= "( " . $argument . " )";
									$finalQueryAdjusted = true;
								}
							}
						}
						return [$finalQuery,$dogCountBaseQuery];
					}
					$dogCount;
					$dogCountBaseQuery = "SELECT COUNT(*) as NumberOfDogs FROM dogs a 
					INNER JOIN breeds b ON a.breed_id = b.breed_id 
					INNER JOIN age c ON a.age_id = c.age_id 
					INNER JOIN genders d ON a.gender_id = d.gender_id 
					INNER JOIN sizes e ON a.size_id = e.size_id 
					INNER JOIN countries f ON a.country_id = f.country_id
					WHERE ";

					if(is_array($_GET["breed"]) || is_array($_GET["age"]) || is_array($_GET["gender"]) || is_array($_GET["size"])){
						$baseQuery = "
						SELECT a.dog_id, a.name,a.description,b.breed_name,c.age_name,d.gender,e.size,f.country_name
						FROM dogs a 
						INNER JOIN breeds b ON a.breed_id = b.breed_id 
						INNER JOIN age c ON a.age_id = c.age_id 
						INNER JOIN genders d ON a.gender_id = d.gender_id 
						INNER JOIN sizes e ON a.size_id = e.size_id 
						INNER JOIN countries f ON a.country_id = f.country_id
						WHERE ";
						
						$ageArguments = argumentCreator($_GET["age"], "age_name");
						$genderArguments = argumentCreator($_GET["gender"], "gender");
						$sizeArguments = argumentCreator($_GET["size"], "size");
						$breedArguments = argumentCreator($_GET["breed"], "breed_name");

						$finalQuery = $baseQuery;
						$finalQueryAdjusted = false;
						if($ageArguments){
							$finalQuery .= "( " . $ageArguments . " )";
							$dogCountBaseQuery  .= "( " . $ageArguments . " )";
							$finalQueryAdjusted = true;
						}
						$helper = finalQueryHelper([$genderArguments,$sizeArguments,$breedArguments],$finalQuery,$finalQueryAdjusted,$dogCountBaseQuery);
						$finalQuery = $helper[0];
						if($countryAvailable){
							$finalQuery .= " AND (f.country_name = '" . $userCountry . "') ";
						}
						$finalQuery .= $orderClause . $limitClause;
						print_r($finalQuery);

						$dogCount = $wpdb->get_results($helper[1])[0]->NumberOfDogs;
						$dogs = $wpdb->get_results($finalQuery);
					}
					else{
						$defaultQuery = 
						"SELECT a.dog_id, a.name,a.description,b.breed_name,c.age_name,d.gender FROM dogs a 
						INNER JOIN breeds b ON a.breed_id = b.breed_id 
						INNER JOIN age c ON a.age_id = c.age_id 
						INNER JOIN genders d ON a.gender_id = d.gender_id
						INNER JOIN countries f ON a.country_id = f.country_id ";
						if($countryAvailable){
							$defaultQuery .= " WHERE (f.country_name = '" . $userCountry . "') ";
						}
						$defaultQuery .= $orderClause . $limitClause;
						$dogs = $wpdb->get_results($defaultQuery);
						if($countryAvailable){
							$qry = "SELECT COUNT(*) as NumberOfDogs FROM dogs a INNER JOIN countries b ON a.country_id = b.country_id WHERE (b.country_name = '" . $userCountry . "') ";
							$dogCount = $wpdb->get_results($qry)[0]->NumberOfDogs;
						}
						else{
							$dogCount = $wpdb->get_results("SELECT COUNT(*) as NumberOfDogs FROM dogs")[0]->NumberOfDogs;
						}
					}
					for($i=0; $i<count($dogs);$i++){				
						if($i === 0 || $i == 3 || $i === 6){
							?>
							<div class="row spaced-cols content-center-sm" data-type="row">
							<?php
						}
						$url = site_url('/adoptions/dogs/?id=') . $dogs[$i]->dog_id;
						?>
						<div class="col-sm-4">
							<a href="<?php echo $url; ?>" style="text-decoration: none; color: #3C424F; ">
								<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
									<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
									<h6 class=""><?php echo $dog->name ?></h6> 
									<p class="small italic">Shelter Name</p>
									<p class="text-center"><?php echo $dog->description ?></p> 
								</div> 
							</a>
						</div>
						<?php
						if($i === 2 || $i === 5 || $i === 8){
							?>
							</div>
							<?php
						}
						elseif($i === count($dogs)-1){
							?>
							</div>
							<?php
						}
					}
					$numberOfPages = ceil($dogCount/$pageSize);
					?>
			<div class="row paginationSection">
				<div class="col-sm-12">
					<div class="pagination">
						<form method="GET" id="paginationForm">
						<a id="paginationFirst" class="paginationButton" onclick="paginationSubmit(1)">&laquo;</a>
						<?php
						for($i =1; $i <= $numberOfPages; $i++){
							if($i == $page){
								?>
								<a onclick="paginationSubmit(<?php echo $i; ?>)" id="<?php echo 'pag' . $i; ?>" class="active paginationButton"><?php echo $i ?></a>
								<?php
							}
							else{
								?>
								<a onclick="paginationSubmit(<?php echo $i; ?>)" id="<?php echo 'pag' . $i; ?>" class="paginationButton"><?php echo $i ?></a>
								<?php
							}
						}
						appendForms();
						?>
						<a id="paginationLast" class="paginationButton" onclick="paginationSubmit(<?php echo $numberOfPages; ?>)">&raquo;</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<hr style="margin-top: 2.5rem;">
<section class="recentlyViewed" style="width: 100%;">
	<h2 style="text-align: center;">Recently Viewed Pets</h2>
	<div class="carousel-container">
		<div class="carousel-inner">
			<div class="track">
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
					<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
				<div class="card-container">
					<div class="card boxShadowAnimate">
						<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
						<h6 style="text-align: center;">Pet Name</h6>
						<p style="text-align: center; class="small italic">Shelter Name</p>
					</div>
				</div>
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
</div>
</div>
<script src="<?php echo site_url('/wp-content/themes/mesmerize/adoption-assets/adoptionsScript.js'); ?>" type="text/javascript">
<?php get_footer(); ?>