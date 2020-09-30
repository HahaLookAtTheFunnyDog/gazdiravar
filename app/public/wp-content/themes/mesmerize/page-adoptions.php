<?php 
session_start();
function appendFormHelper($filterArr, $arrName){
	if(is_array($filterArr) || is_object($filterArr)){
		
		foreach($filterArr as $filterVar){
			?>
			<input type="hidden" name="<?php echo $arrName . "[]" ?>" value="<?php echo $filterVar ?>">
			<?php
		}
	}
}
function appendFilters($appendBreed = true){
	if($appendBreed){
		appendFormHelper($_GET["breed"], "breed");
	}
	appendFormHelper($_GET["age"], "age");
	appendFormHelper($_GET["gender"], "gender");
	appendFormHelper($_GET["size"], "size");
}
function appendCountry(){
	if($_GET["country"]){
		?>
		<input type="hidden" name="country" value="<?php echo $_GET["country"]; ?>">
		<?php
	}
}
function appendSpecies(){
	if($_GET["species"]){
		?>
		<input type="hidden" name="species" value="<?php echo $_GET['species']; ?>">
		<?php
	}
}
function appendOrder(){
	if($_GET["order"]){
		?>
		<input type="hidden" name="order" value="<?php echo $_GET["order"]; ?>">
		<?php
	}
}
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
function finalQueryHelper($arguments,$finalQuery, $finalQueryAdjusted, $adoptionCountBaseQuery, $userCountry, $countryAvailable){
	foreach($arguments as $argument){
		if($argument){
			if($finalQueryAdjusted){
				$finalQuery .= " AND ( " . $argument . " )";
				$adoptionCountBaseQuery .= " AND ( " . $argument . " )";
			}
			else{
				$finalQuery .= "( " . $argument . " )";
				$adoptionCountBaseQuery .= "( " . $argument . " )";
				$finalQueryAdjusted = true;
			}
		}
	}
	if($countryAvailable){
		$adoptionCountBaseQuery  .= " AND (f.country_name = '" . $userCountry . "') ";
		$finalQuery .= " AND (f.country_name = '" . $userCountry . "') ";
	}

	return [$finalQuery,$adoptionCountBaseQuery];
}
global $wpdb;
//Can retrieve the actual user ip once the website is live
$user_ip = $_SERVER['REMOTE_ADDR'];
$geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
global $userCountry;
if($_GET["country"]){
	$userCountry = $_GET["country"];
}
else{
	$userCountry = $geo["geoplugin_countryName"];
}
global $countryAvailable;
$countryQryPrep = "SELECT count(country_name) as 'country_count' FROM adoptions a INNER JOIN countries b ON a.country_id = b.country_id WHERE b.country_name = '" . $userCountry . "'";
$countryCount = $wpdb->get_results($countryQryPrep)[0]->country_count;
if($countryCount > 0){
	$countryAvailable = true;
}
else{
	$countryAvailable = false;
	$userCountry = "All Countries";
}
global $orderQuery;
$order = "Newest";
if(!($_GET["order"])){
	$orderQuery = "ORDER BY register_date DESC";
}
else{
	if($_GET["order"] == "Oldest"){
		$orderQuery = "ORDER BY register_date ASC";
		$order = "Oldest";
	}
	else{
		$orderQuery = "ORDER BY register_date DESC";
	}
}
global $speciesSelected;
if($_GET["species"]){
	$speciesSelected = $_GET["species"];
}
else{
	$speciesSelected = "dog";
}
mesmerize_get_header(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url('/wp-content/themes/mesmerize/adoption-assets/style.css'); ?>">
<div id='page-content' class="page-content">
	<div class="<?php mesmerize_page_content_wrapper_class(); ?>">
		<h2 style="text-align: center">Featured Adoptions</h2>
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
							<div class="featuredContent">
								<h2 style="color: white;">
									Hi I'm <?php echo $adoption->name ?>
								</h2>
								<p style="color: white;">
									<?php
										$shortDesc = $adoption->description;
										if(substr_count($shortDesc, ' ') > 15){
											$pos=strpos($adoption->description, ' ', 80);
											$shortDesc = substr($adoption->description,0,$pos );
											if(!($shortDesc == $adoption->description)){
												$shortDesc .= " ...";
											}
										}
									?>
									<?php echo $adoption->description ?>
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
						<h4>Species</h4> 
						<form method="GET">
							<select id="species" name="species" class="form-control" style="margin: 0;" onchange="this.parentNode.submit()">
								<?php
								$species = $wpdb->get_results("
									SELECT DISTINCT b.species_name FROM adoptions a
									INNER JOIN 
									(SELECT species_name, breed_id FROM breeds a 
									INNER JOIN species b ON a.species_id = b.species_id) b
									ON a.breed_id = b.breed_id"
								);
								foreach($species as $species){
									?>
									<option 
									<?php 
									if(strtoupper($speciesSelected) === strtoupper($species->species_name)){
										echo " selected ";
									}
									?>
									value="<?php echo $species->species_name; ?>"><?php echo $species->species_name; ?></option>
									<?php
								}
								?>
							</select>
							<?php 
							appendFilters(false);
							appendCountry();
							appendOrder();
							?>
							<button type="submit" class="filterSubmit hideFilterSubmit" id="speciesFilterSubmit">APPLY</button>
						</form>
					</li>
					<li>
						<hr class="filterDivider">
						<h4>Breed</h4>
						<form method="get">
							<ul class="scrollRadio">
								<?php
								$qryPrep = "SELECT breed_name FROM breeds a INNER JOIN species b ON a.species_id = b.species_id WHERE b.species_name = '" . $speciesSelected . "' ORDER BY breed_name ASC";
								$breeds = $wpdb->get_results($qryPrep);
								foreach($breeds as $breed){
									?>
									<li>
										<input type="checkbox" id="<?php echo str_replace(' ', '', $breed->breed_name); ?>" name="breed[]" value="<?php echo $breed->breed_name ?>" class="adoptionSelection"
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
											$queryPrepare;
											$queryResult;
											if($countryAvailable){
												$queryPrepare = "SELECT COUNT(breed_name) AS breedCount FROM adoptions a INNER JOIN breeds b ON a.breed_id = b.breed_id INNER JOIN countries c ON a.country_id = c.country_id WHERE UPPER(breed_name) = '" . $breedUpper . "' AND c.country_name = '" . $userCountry . "'";
												$queryResult = $wpdb->get_results($queryPrepare);
											}
											else{
												$queryPrepare = $wpdb->prepare("SELECT COUNT(breed_name) AS breedCount FROM adoptions a INNER JOIN breeds b ON a.breed_id = b.breed_id WHERE UPPER(breed_name) = %s", "$breedUpper");
												$queryResult = $wpdb->get_results($queryPrepare);
											}
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
							appendFilters();
							appendCountry();
							appendSpecies();
							appendOrder();
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
											$queryPrepare;
											$queryResult;
											if($countryAvailable){
												$qry = "SELECT COUNT(age_name) AS ageCount FROM adoptions a 
												INNER JOIN age b ON a.age_id = b.age_id 
												INNER JOIN countries c ON a.country_id = c.country_id 
												INNER JOIN 
												(SELECT species_name, breed_id FROM breeds a 
												INNER JOIN species b ON a.species_id = b.species_id) d ON a.breed_id = d.breed_id
												WHERE UPPER(age_name) = '" . $ageUpper . "' AND c.country_name = '" . $userCountry . "' AND species_name = '" . $speciesSelected . "'";
												$queryResult = $wpdb->get_results($qry);
											}
											else{
												$qryPrep = "SELECT COUNT(age_name) AS ageCount 
												FROM adoptions a 
												INNER JOIN age b ON a.age_id = b.age_id
												INNER JOIN 
												(SELECT species_name, breed_id FROM breeds a 
												INNER JOIN species b ON a.species_id = b.species_id) c ON a.breed_id = c.breed_id
												WHERE UPPER(age_name) = '" . $ageUpper ."' AND species_name = '" . $speciesSelected . "'";
												$queryResult = $wpdb->get_results($qryPrep);
											}
											echo "(" . $queryResult[0]->ageCount . ")";
											?>
										</p>
										<br>
									</li>
									<?php
								}
								?>
								<?php
								appendFilters();
								appendCountry();
								appendSpecies();
								appendOrder();
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
											$queryPrepare;
											$queryResult;
											if($countryAvailable){
												$qry = "SELECT COUNT(gender) AS genderCount FROM adoptions a 
												INNER JOIN genders b ON a.gender_id = b.gender_id 
												INNER JOIN countries c ON a.country_id = c.country_id 
												INNER JOIN 
												(SELECT species_name, breed_id FROM breeds a 
												INNER JOIN species b ON a.species_id = b.species_id) d ON a.breed_id = d.breed_id
												WHERE UPPER(gender) = '" . $genderUpper . "' AND c.country_name = '" . $userCountry . "' AND species_name = '" . $speciesSelected . "'";
												$queryResult = $wpdb->get_results($qry);
											}
											else{
												$qryPrep = "SELECT COUNT(gender) AS genderCount 
												FROM adoptions a 
												INNER JOIN genders b ON a.gender_id = b.gender_id
												INNER JOIN 
												(SELECT species_name, breed_id FROM breeds a 
												INNER JOIN species b ON a.species_id = b.species_id) c ON a.breed_id = c.breed_id
												WHERE UPPER(gender) = '" . $genderUpper ."' AND species_name = '" . $speciesSelected . "'";
												$queryResult = $wpdb->get_results($qryPrep);
											}
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
							appendFilters();
							appendCountry();
							appendSpecies();
							appendOrder();
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
											$queryPrepare;
											$queryResult;
											if($countryAvailable){
												$qry = "SELECT COUNT(size) AS sizeCount FROM adoptions a 
												INNER JOIN sizes b ON a.size_id = b.size_id 
												INNER JOIN countries c ON a.country_id = c.country_id 
												INNER JOIN 
												(SELECT species_name, breed_id FROM breeds a 
												INNER JOIN species b ON a.species_id = b.species_id) d ON a.breed_id = d.breed_id
												WHERE UPPER(size) = '" . $sizeUpper . "' AND c.country_name = '" . $userCountry . "' AND species_name = '" . $speciesSelected . "'";
												$queryResult = $wpdb->get_results($qry);
											}
											else{
												$qryPrep = "SELECT COUNT(size) AS sizeCount 
												FROM adoptions a 
												INNER JOIN sizes b ON a.size_id = b.size_id
												INNER JOIN 
												(SELECT species_name, breed_id FROM breeds a 
												INNER JOIN species b ON a.species_id = b.species_id) c ON a.breed_id = c.breed_id
												WHERE UPPER(size) = '" . $sizeUpper ."' AND species_name = '" . $speciesSelected . "'";
												$queryResult = $wpdb->get_results($qryPrep);
											}
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
							appendFilters();
							appendCountry();
							appendSpecies();
							appendOrder();
							?>
							<button type="submit" class="filterSubmit hideFilterSubmit" id="sizeFilterSubmit">APPLY</button>
						</form>
					</li>
					<hr class="filterDivider">
					<li>
						<h4>Country</h4> 
						<form method="GET">
							<select id="country" name="country" class="form-control" style="margin: 0;" onchange="this.parentNode.submit()">
								<option value="All">All Countries</option>
								<?php
								$countries = $wpdb->get_results("
									SELECT DISTINCT b.country_name FROM adoptions a
									INNER JOIN countries b 
									ON a.country_id = b.country_id"
								);
								foreach($countries as $country){
									?>
									<option 
									<?php 
									if(strtoupper($userCountry) === strtoupper($country->country_name)){
										echo " selected ";
									}
									?>
									value="<?php echo $country->country_name; ?>"><?php echo $country->country_name; ?></option>
									<?php
								}
								?>
							</select>
							<?php 
							appendFilters();
							appendSpecies();
							appendOrder();
							?>
							<button type="submit" class="filterSubmit hideFilterSubmit" id="countryFilterSubmit">APPLY</button>
						</form>
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
							<li class="clearAll" onclick="clearAll('<?php echo $userCountry; ?>', '<?php echo $speciesSelected; ?>','<?php echo $order; ?>')">
								Clear All
							</li>		
							<?php
						}
						?>
					</ul>
				</div>
				<row>
					<h4>
						Now Displaying For: <?php echo $userCountry; ?>
					</h4>
					<hr>
				</row>
			</div>
			<div class="row">
				<?php 
				$pageSize = 9;
				$page = $_GET["pid"] ?? 1;
				$pageLowerLimit = ($page-1) * $pageSize;
				$adoptionCount = $wpdb->get_results("SELECT COUNT(*) as NumberOfadoptions FROM adoptions")[0]->NumberOfadoptions;
				$limitClause = " LIMIT " . $pageLowerLimit . "," . $pageSize;
				$adoptionCount;
				$adoptionCountBaseQuery = "SELECT COUNT(*) as NumberOfadoptions FROM adoptions a 
				INNER JOIN breeds b ON a.breed_id = b.breed_id 
				INNER JOIN age c ON a.age_id = c.age_id 
				INNER JOIN genders d ON a.gender_id = d.gender_id 
				INNER JOIN sizes e ON a.size_id = e.size_id 
				INNER JOIN countries f ON a.country_id = f.country_id
				WHERE ";

				if(is_array($_GET["breed"]) || is_array($_GET["age"]) || is_array($_GET["gender"]) || is_array($_GET["size"])){
					$baseQuery = "
					SELECT a.adoption_id, a.profile_picture_filename, a.name,a.description,b.breed_name,c.age_name,d.gender,e.size,f.country_name
					FROM adoptions a 
					INNER JOIN breeds b ON a.breed_id = b.breed_id 
					INNER JOIN age c ON a.age_id = c.age_id 
					INNER JOIN genders d ON a.gender_id = d.gender_id 
					INNER JOIN sizes e ON a.size_id = e.size_id 
					INNER JOIN countries f ON a.country_id = f.country_id
					INNER JOIN (SELECT species_name, breed_id FROM breeds a INNER JOIN species b ON a.species_id = b.species_id) g ON a.breed_id = g.breed_id 
					WHERE ";
					
					$ageArguments = argumentCreator($_GET["age"], "age_name");
					$genderArguments = argumentCreator($_GET["gender"], "gender");
					$sizeArguments = argumentCreator($_GET["size"], "size");
					$breedArguments = argumentCreator($_GET["breed"], "breed_name");

					$finalQuery = $baseQuery;
					$finalQueryAdjusted = false;
					if($ageArguments){
						$finalQuery .= "( " . $ageArguments . " )";
						$adoptionCountBaseQuery  .= "( " . $ageArguments . " )";
						$finalQueryAdjusted = true;
					}
					$helper = finalQueryHelper([$genderArguments,$sizeArguments,$breedArguments],$finalQuery,$finalQueryAdjusted,$adoptionCountBaseQuery,$userCountry,$countryAvailable);
					$finalQuery = $helper[0];
					$finalQuery .= " AND species_name = '" . $speciesSelected . "'";
					
					$finalQuery .= $orderQuery . $limitClause;
					$adoptionCount = $wpdb->get_results($helper[1])[0]->NumberOfadoptions;
					$adoptions = $wpdb->get_results($finalQuery);
				}
				else{
					$defaultQuery = 
					"SELECT a.adoption_id, a.profile_picture_filename, a.name,a.description,b.breed_name,c.age_name,d.gender,f.country_name FROM adoptions a 
					INNER JOIN breeds b ON a.breed_id = b.breed_id 
					INNER JOIN age c ON a.age_id = c.age_id 
					INNER JOIN genders d ON a.gender_id = d.gender_id
					INNER JOIN countries f ON a.country_id = f.country_id 
					INNER JOIN (SELECT species_name, breed_id FROM breeds a INNER JOIN species b ON a.species_id = b.species_id) g ON a.breed_id = g.breed_id
					WHERE species_name = '" . $speciesSelected ."'";
					if($countryAvailable){
						$defaultQuery .= " AND (f.country_name = '" . $userCountry . "') ";
					}
					$defaultQuery .= $orderQuery . $limitClause;
					$adoptions = $wpdb->get_results($defaultQuery);
					if($countryAvailable){
						$qry = 
						"SELECT COUNT(*) as NumberOfadoptions FROM adoptions a 
						INNER JOIN countries b ON a.country_id = b.country_id
						INNER JOIN (SELECT species_name, breed_id FROM breeds a INNER JOIN species b ON a.species_id = b.species_id) c ON a.breed_id = c.breed_id 
						WHERE (b.country_name = '" . $userCountry . "') AND species_name = '" . $speciesSelected . "'";
						$adoptionCount = $wpdb->get_results($qry)[0]->NumberOfadoptions;
					}
					else{
						$qry = 
						"SELECT COUNT(*) as NumberOfadoptions FROM adoptions a 
						INNER JOIN (SELECT species_name, breed_id FROM breeds a INNER JOIN species b ON a.species_id = b.species_id) b ON a.breed_id = b.breed_id 
						WHERE species_name = '" . $speciesSelected . "'";
						$adoptionCount = $wpdb->get_results($qry)[0]->NumberOfadoptions;
					}
				}?>
				<div class="row">
					<div class="col-sm-12">
						<div class="dropdownsort">
							<ul style="float: right; list-style: none; background-color: dodgerblue; padding: 4px;">
								<li id="sortList"><a style="text-decoration: none; color: white;">Sorted By <?php if($_GET["order"]){echo $_GET["order"];}else{echo "Newest";} ?></a>
									<ul id="sub_navlist" style="float: right; margin-right: 0; margin-left: 80px; display: none; list-style: none; margin-top: 4px; color: #5B606B;  box-shadow: 5px 10px 18px #888888; ">
										<form id="orderForm" method="GET" style="margin: 0;">
											<li onclick="sortSubmit(this,'<?php echo $_GET['order']; ?>')">Newest</li><br>
											<li onclick="sortSubmit(this,'<?php echo $_GET['order']; ?>')">Oldest</li>
											<?php appendFilters(); appendCountry(); appendSpecies(); ?>
										</form>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<?php
				for($i=0; $i<count($adoptions);$i++){				
					if($i === 0 || $i == 3 || $i === 6){
						?>
						<div class="row spaced-cols content-center-sm" data-type="row">
							<?php
						}
						$url = site_url('/adoptions/adoptions/?id=') . $adoptions[$i]->adoption_id;
						?>
						<div class="col-sm-4">
							<form method="GET" action="animal/">
								<input type="hidden" name="id" value="<?php echo $adoptions[$i]->adoption_id; ?>">
								<a onclick="this.parentNode.submit()" class="cardLink">
									<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
										<?php
											$imgSrc = site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg');
											if($adoptions[$i]->profile_picture_filename){
												$imgSrc = site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/' . $adoptions[$i]->profile_picture_filename . '.jpg');
											}
										?>
										<img src="<?php echo  $imgSrc; ?>" class="round icon iconBig">
										<h6 class=""><?php echo $adoptions[$i]->name; ?></h6> 
										<p class="small italic"><?php echo $adoptions[$i]->country_name; ?></p>
										<?php
											$shortDesc = $adoptions[$i]->description;
											if(substr_count($shortDesc, ' ') > 10){
												$pos=strpos($adoptions[$i]->description, ' ', 40);
												$shortDesc = substr($adoptions[$i]->description,0,$pos );
												if(!($shortDesc == $adoptions[$i]->description)){
													$shortDesc .= " ...";
												}
											}
										?>
										<p class="text-center"><?php echo $shortDesc; ?></p> 
									</div> 
								</a>
							</form>
						</div>
						<?php
						if($i === 2 || $i === 5 || $i === 8){
							?>
						</div>
						<?php
					}
					elseif($i === count($adoptions)-1){
						?>
					</div>
					<?php
				}
			}
			$numberOfPages = ceil($adoptionCount/$pageSize);
			?>
			<div class="row paginationSection">
				<div class="col-sm-12">
					<div class="pagination">
						<?php
						if($numberOfPages > 0){
							?>
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
								appendFilters();
								appendCountry();
								appendOrder();
								?>
								<a id="paginationLast" class="paginationButton" onclick="paginationSubmit(<?php echo $numberOfPages; ?>)">&raquo;</a>
							</form>
							<?php
						} 
						else{
							?>
							<h1>
								Uh Oh, There doesn't seem to be any adoptions available with the filters you've chosen.
							</h1>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<hr style="margin-top: 2.5rem;">
<?php
if($_SESSION["recentlyViewed"]){
	?>
	<section class="recentlyViewed" style="width: 100%;">
		<h2 style="text-align: center;">Recently Viewed Pets</h2>
		<div class="carousel-container">
			<div class="carousel-inner">
				<div class="track">
					<?php 
					foreach(array_reverse($_SESSION["recentlyViewed"]) as $adoptionID){
						$query = "SELECT a.adoption_id, a.profile_picture_filename, a.name,a.description,b.breed_name,c.age_name,d.gender,f.country_name FROM adoptions a 
						INNER JOIN breeds b ON a.breed_id = b.breed_id 
						INNER JOIN age c ON a.age_id = c.age_id 
						INNER JOIN genders d ON a.gender_id = d.gender_id
						INNER JOIN countries f ON a.country_id = f.country_id WHERE a.adoption_id = " . $adoptionID;
						$adoption = $wpdb->get_results($query)[0];
						?>
						<div class="card-container">
							<form method="GET" action="animal/">
								<input type="hidden" name="id" value="<?php echo $adoption->adoption_id; ?>">
								<div class="card boxShadowAnimate" onclick="this.parentNode.submit();" style="cursor: pointer;">
									<?php
										$imgSrc = site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg');
										if($adoption->profile_picture_filename){
											$imgSrc = site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/' . $adoption->profile_picture_filename . '.jpg');
										}
									?>
									<img style="margin: auto; width: 8rem; height: 8rem;" src="<?php echo $imgSrc; ?>" class="round icon iconSmall">
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
<?php
}
?>
</div>
</div>
<script src="<?php echo site_url('/wp-content/themes/mesmerize/adoption-assets/adoptionsScript.js'); ?>" type="text/javascript">
	<?php get_footer(); ?>