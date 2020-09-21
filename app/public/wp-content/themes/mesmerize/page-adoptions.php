<?php mesmerize_get_header(); ?>
<style>
	.filters ul{
		list-style: none;
		margin: 0;
	}
	.filters ul li{
		text-align: left;
	}
	.filters input{
		/*height: 1rem;*/
	}
	.filters label{
		font-size: 1rem;

	}
	.scrollRadio{
		height: 15rem;
		overflow: scroll;
	}
	.scrollRadio::-webkit-scrollbar {
		width: 2px; 
	}
	.paginationSection{
		width: 100%;
		margin: 0;
	}
	.pagination{
		text-align: center;
	}
	.pagination a {
		color: black;
		padding: 8px 16px;
		text-decoration: none;
		transition: background-color .5s;
	}
	.pagination a.active {
		background-color: dodgerblue;
		color: white;
	}
	.pagination a:hover:not(.active) {
		background-color: #ddd;
	}




	@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
	.carousel-container {
		width: 100%;
		margin: 50px auto;
		min-height: 200px;
		position: relative;
	}
	@media screen and (max-width: 768px) {
		.carousel-container {
			width: 80%;
		}
	}
	@media screen and (max-width: 1024px) {
		.carousel-container {
			width: 85%;
		}
	}
	.carousel-container .carousel-inner {
		overflow: auto;
	}
	.carousel-container .track {
		display: inline-flex;
		transition: transform 0.5s;
	}
	.carousel-container .card-container {
		width: 259px;
		flex-shrink: 0;
		height: 250px;
		padding-right: 15px;
		box-sizing: border-box;
	}
	.carousel-container .card-container .card {
		width: 100%;
		height: 100%;
		border: 1px solid #ccc;
		box-sizing: border-box;
		border-radius: 10px;
		display: flex;
		flex-direction: column;
	}
	.nav button {
		background-color: dodgerblue;
		color: white;
		width: 60px;
		height: 60px;
		border-radius: 50%;
		border: 1px solid #aaa;
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		cursor: pointer;
	}
	.nav .prev {
		left: -30px;
		display: none;
	}
	.nav .prev.show {
		display: block;
	}
	.nav .next {
		right: -30px;
	}
	.nav .next.hide {
		display: none;
	}
	.card > * {
		flex: 1;
	}
	.card .img {
		display: flex;
		justify-content: center;
		align-items: center;
		font-size: 30px;
	}
	.card .info {
		text-align: center;
		flex-basis: 40px;
		color: #fff;
		flex-grow: 0;
		padding: 10px;
		box-sizing: border-box;
	}
	

	.slider {
		position: relative;
		overflow: hidden;
		height: 500px;
		width: 100%;
	}
	.activeSlide{
		opacity: 1 !important;
	}
	.slide{
		position: absolute;
		transition: opacity 0.4s ease-in-out;
		opacity: 0;
		background-size: cover;                      
		background-repeat: no-repeat;
		background-position: center center; 
		height: 100%; 
		width: 100%;
		padding: 0;
	}
	.featuredContent{
		position: absolute; 
		bottom: 20px;
		width: 600px;
		background-color: rgba(0, 0, 0, 0.7);
		color: #fff;
		padding: 35px;
	}


	.buttonContainer{
		text-align:center;
	}
	.featuredButtons{
		display: inline-block;
		width: auto;
	}
	.featuredButtons li{
		display: inline;
	}
	.featuredButton {
		background-color: #fff;
		height:10px;
		width: 10px;
		border-radius: 50%;
		border: 1px solid dodgerblue;
		cursor: pointer;
	}
	.activeButton{
		background-color: dodgerblue;
	}


	.quantity{
		display: inline;
		float: right;
		margin: 0;
		padding: 0;
		margin-right: 7px;
	}
	.alignMargin{
		margin-right: 5px;
	}
	.filterDivider{
		margin: 0;
	}

	.filterChoices{
		list-style: none;
		margin: 0;
		margin-bottom: 20px;
	}
	.filterChoices li{
		display: inline-block;
	}
	.filtersli{
		background-color: dodgerblue;
		color: white;
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 10px;
		padding-right: 10px;
		border-radius: 10px;
		margin-right: 10px;
	}
	.filterClose{
		font-weight: bold;
		cursor: pointer;
		margin-left: 10px;
	}
	.clearAll{
		cursor: pointer;
	}
	.appliedFiltersHeader{
		margin-bottom: 5px;
	}
	.filterSubmit{
		display: block;
		color: white;
		background-color: dodgerblue;
		width: 100%;
		border: 0px;
		margin-bottom: 5px;
	}
	.hideFilterSubmit{
		display: none;
	}
	.iconBig{
		width: 13rem !important;
		height: 13rem !important;
	}
	.boxShadowAnimate:hover{
		box-shadow: 0 0 11px rgba(33,33,33,.2); 
	}
</style>
<div id='page-content' class="page-content">
	<div class="<?php mesmerize_page_content_wrapper_class(); ?>">
		<section>
			<h2 style="text-align: center">Featured Adoptions</h2>
			<hr>
			<div class="container-fluid">
				<div class="row no-gutters" style="margin: auto;">
					<div class="col-md-12">
						<div class="slider">
							<?php
							global $wpdb;
							$dogs = $wpdb->get_results('SELECT * FROM dogs a INNER JOIN featured b ON a.dog_id = b.dog_id');
							$featuredCount = 0;

							foreach($dogs as $dog){
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
							for($i = 1; $i <= count($dogs); $i++){
								?>
								<li>
									<button id="featured<?php echo $i; ?>" class="featuredButton
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
			<hr style="margin-top: 10px;">
		</section>
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
							<form>
								<ul class="scrollRadio">
									<?php
									$breeds = $wpdb->get_results('SELECT breed_name FROM breeds ORDER BY breed_name ASC');
									foreach($breeds as $breed){
										?>
										<li>
											<input type="radio" id="<?php echo str_replace(' ', '', $breed->breed_name); ?>" name="<?php echo str_replace(' ', '', $breed->breed_name); ?>" value="<?php echo str_replace(' ', '', $breed->breed_name); ?>" class="dogSelection">
											<label for="<?php echo str_replace(' ', '', $breed->breed_name); ?>"> <?php echo $breed->breed_name; ?> </label>
											<p class="quantity alignMargin">
												(0)
											</p>
											<br>
										</li>
										<?php
									}
									?>
								</ul>
								<button type="submit" class="filterSubmit hideFilterSubmit" id="breedFilterSubmit">APPLY</button>
							</form>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Age</h4>
							<ul>
								<?php
								$ages = $wpdb->get_results('SELECT age_name FROM age');
								foreach($ages as $age){
									?>
									<li>
										<input type="radio" id="<?php echo str_replace(' ', '', $age->age_name); ?>" name="<?php echo str_replace(' ', '', $age->age_name); ?>" value="<?php echo str_replace(' ', '', $age->age_name); ?>" class="ageSelection">
										<label for="<?php echo str_replace(' ', '', $age->age_name); ?>"> <?php echo $age->age_name ?> </label>
										<p class="quantity">
											(0)
										</p>
										<br>
									</li>
									<?php
								}
								?>
							</ul>
							<button type="submit" class="filterSubmit hideFilterSubmit" id="ageFilterSubmit">APPLY</button>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Gender</h4>
							<ul>
								<?php
								$genders = $wpdb->get_results('SELECT gender FROM genders');
								foreach($genders as $gender){
									?>
									<li>
										<input type="radio" id="<?php echo str_replace(' ', '', $gender->gender); ?>" name="<?php echo str_replace(' ', '', $gender->gender); ?>" value="<?php echo str_replace(' ', '', $gender->gender); ?>" class="genderSelection">
										<label for="<?php echo str_replace(' ', '', $gender->gender); ?>"> <?php echo $gender->gender ?> </label>
										<p class="quantity">
											(0)
										</p>
										<br>
									</li>
									<?php
								}
								?>
							</ul>
							<button type="submit" class="filterSubmit hideFilterSubmit" id="genderFilterSubmit">APPLY</button>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Size</h4>
							<ul>
								<?php
									$sizes = $wpdb->get_results('SELECT size FROM sizes');
									foreach($sizes as $size){
										?>
										<li>
									<input type="radio" id="<?php echo str_replace(' ', '', $size->size); ?>" name="<?php echo str_replace(' ', '', $size->size); ?>" value="<?php echo str_replace(' ', '', $size->size); ?>" class="sizeSelection">
									<label for="<?php echo str_replace(' ', '', $size->size); ?>"> <?php echo $size->size ?> </label>
									<p class="quantity">
										(0)
									</p>
									<br>
								</li>
										<?php
									}
								?>
							</ul>
							<button type="submit" class="filterSubmit hideFilterSubmit" id="sizeFilterSubmit">APPLY</button>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Good With</h4>
							<Ul>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Kids</label>
									<p class="quantity">
										(0)
									</p>
									<br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Other Dogs</label>
									<p class="quantity">
										(0)
									</p>
									<br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Cats</label>
									<p class="quantity">
										(0)
									</p>
									<br>
								</li>
							</Ul>
						</li>
						<li>
							<hr class="filterDivider">
							<h4>Within</h4>
							<Ul>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">50 Miles</label>
									<p class="quantity">
										(0)
									</p>
									<br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">100 Miles</label>
									<p class="quantity">
										(0)
									</p>
									<br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">200 Miles</label>
									<p class="quantity">
										(0)
									</p>
									<br>
								</li>
							</Ul>
						</li>
						<hr class="filterDivider">
					</ul>
				</div>
			</div>
			<div class="col-md-9">
				<div class="row">
					<div class="col-sm-12 appliedFiltersHeader" >
						<h4>
							Filters Applied
						</h4>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<ul class="filterChoices">
							<li class="filtersli">
								50 Miles
								<span class="filterClose">
									x
								</span>
							</li>
							<li class="filtersli">
								Golden Retriever
								<span class="filterClose">
									x
								</span>
							</li>
							<li class="filtersli">
								Akita
								<span class="filterClose">
									x
								</span>
							</li>
							<li class="filtersli">
								Puppy
								<span class="filterClose">
									x
								</span>
							</li>
							<li class="clearAll">
								Clear All
							</li>
						</ul>
					</div>
				</div>
				<div class="row spaced-cols content-center-sm" data-type="row">
					<div class="col-sm-4">
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div> 
					</div>
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div>
					</div> 
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;"> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div> 
					</div>
				</div>
				<div class="row spaced-cols content-center-sm" data-type="row" >
					<div class="col-sm-4">
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div> 
					</div>
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div>
					</div> 
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;"> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div> 
					</div>
				</div>
				<div class="row spaced-cols content-center-sm" data-type="row">
					<div class="col-sm-4">
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div> 
					</div>
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div>
					</div> 
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;"> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon iconBig">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
							<p class="text-center">My name's pet, I like going for long walks in the sun and to bark at mail men</p> 
						</div> 
					</div>
				</div>
				<div class="row no-gutters paginationSection">
					<div class="pagination">
						<a href="#">&laquo;</a>
						<a class="active" href="#">1</a>
						<a href="#">2</a>
						<a href="#">3</a>
						<a href="#">4</a>
						<a href="#">5</a>
						<a href="#">6</a>
						<a href="#">&raquo;</a>
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
								<div class="img">
									<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
								</div>
								<div class="info">
									<h6 class="">Pet Name</h6>
								</div>
							</div>
						</div>
						<div class="card-container">
							<div class="card boxShadowAnimate">
								<div class="img">
									<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
									<div class="info">
										<h6 class="">Pet Name</h6>
									</div>
								</div>
							</div>
							<div class="card-container">
								<div class="card boxShadowAnimate">
									<div class="img">
										<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
										<div class="info">
											<h6 class="">Pet Name</h6>
										</div>
									</div>
								</div>
								<div class="card-container">
									<div class="card boxShadowAnimate">
										<div class="img">
											<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
											<div class="info">
												<h6 class="">Pet Name</h6>
											</div>
										</div>
									</div>
									<div class="card-container">
										<div class="card boxShadowAnimate">
											<div class="img">
												<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
												<div class="info">
													<h6 class="">Pet Name</h6>
												</div>
											</div>
										</div>
										<div class="card-container">
											<div class="card boxShadowAnimate">
												<div class="img">
													<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
													<div class="info">
														<h6 class="">Pet Name</h6>
													</div>
												</div>
											</div>
											<div class="card-container">
												<div class="card boxShadowAnimate">
													<div class="img">
														<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
														<div class="info">
															<h6 class="">Pet Name</h6>
														</div>
													</div>
												</div>
												<div class="card-container">
													<div class="card boxShadowAnimate">
														<div class="img">
															<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
															<div class="info">
																<h6 class="">Pet Name</h6>
															</div>
														</div>
													</div>
													<div class="card-container">
														<div class="card boxShadowAnimate">
															<div class="img">
																<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																<div class="info">
																	<h6 class="">Pet Name</h6>
																</div>
															</div>
														</div>
														<div class="card-container">
															<div class="card boxShadowAnimate">
																<div class="img">
																	<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																	<div class="info">
																		<h6 class="">Pet Name</h6>
																	</div>
																</div>
															</div>
															<div class="card-container">
																<div class="card boxShadowAnimate">
																	<div class="img">
																		<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																		<div class="info">
																			<h6 class="">Pet Name</h6>
																		</div>
																	</div>
																</div>
																<div class="card-container">
																	<div class="card boxShadowAnimate">
																		<div class="img">
																			<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																			<div class="info">
																				<h6 class="">Pet Name</h6>
																			</div>
																		</div>
																	</div>
																	<div class="card-container">
																		<div class="card boxShadowAnimate">
																			<div class="img">
																				<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																				<div class="info">
																					<h6 class="">Pet Name</h6>
																				</div>
																			</div>
																		</div>
																		<div class="card-container">
																			<div class="card boxShadowAnimate">
																				<div class="img">
																					<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																					<div class="info">
																						<h6 class="">Pet Name</h6>
																					</div>
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
													<script>
														const prev  = document.querySelector('.prev');
														const next = document.querySelector('.next');

														const track = document.querySelector('.track');

														let carouselWidth = document.querySelector('.carousel-container').offsetWidth;

														window.addEventListener('resize', () => {
															carouselWidth = document.querySelector('.carousel-container').offsetWidth;
														})

														let index = 0;

														next.addEventListener('click', () => {
															index++;
															prev.classList.add('show');
															track.style.transform = `translateX(-${index * carouselWidth}px)`;
															if (track.offsetWidth - (index * carouselWidth) < carouselWidth) {
																next.classList.add('hide');
															}

															else if(track.offsetWidth - (index * carouselWidth)*2 < 100){
																next.classList.add('hide');
															}
														})
														prev.addEventListener('click', () => {
															index--;
															next.classList.remove('hide');
															if (index === 0) {
																prev.classList.remove('show');
															}
															track.style.transform = `translateX(-${index * carouselWidth}px)`;
														})

												//slides

												const buttons = document.querySelectorAll('.featuredButton');
												const slides = document.querySelectorAll('.slide');
												const intervalTime = 8000;
												const button1 = document.getElementById('featured1');
												const button2 = document.getElementById('featured2');
												const button3 = document.getElementById('featured3');
												const button4 = document.getElementById('featured4');


												const nextSlide = () => {
													const current = document.querySelector('.activeSlide');
													if (current.nextElementSibling) {
														current.nextElementSibling.classList.add('activeSlide');
													} 
													else {
														slides[0].classList.add('activeSlide');
													}
													var i;
													for (i = 0; i < buttons.length; i++) {
														if(buttons[i].classList.contains('activeButton')){
															buttons[i].classList.remove('activeButton');
															if(i+1<buttons.length){
																buttons[i+1].classList.add('activeButton');
																break;
															}
															else{
																buttons[0].classList.add('activeButton');
																break;
															}
														}
													}
													setTimeout(() => current.classList.remove('activeSlide'));
												};
												slideInterval = setInterval(nextSlide, intervalTime);


												button1.onclick = function(){
													const activeSlide = document.querySelector('.activeSlide');
													activeSlide.classList.remove('activeSlide');
													slides[0].classList.add('activeSlide');

													const activeBtn = document.querySelector('.activeButton');
													activeBtn.classList.remove('activeButton');
													buttons[0].classList.add('activeButton');
													clearInterval(slideInterval);
													slideInterval = setInterval(nextSlide, intervalTime);
													
												}
												button2.onclick = function(){
													const activeSlide = document.querySelector('.activeSlide');
													activeSlide.classList.remove('activeSlide');
													slides[1].classList.add('activeSlide');

													const activeBtn = document.querySelector('.activeButton');
													activeBtn.classList.remove('activeButton');
													buttons[1].classList.add('activeButton');

													clearInterval(slideInterval);
													slideInterval = setInterval(nextSlide, intervalTime);
												}
												button3.onclick = function(){
													const activeSlide = document.querySelector('.activeSlide');
													activeSlide.classList.remove('activeSlide');
													slides[2].classList.add('activeSlide');

													const activeBtn = document.querySelector('.activeButton');
													activeBtn.classList.remove('activeButton');
													buttons[2].classList.add('activeButton');

													clearInterval(slideInterval);
													slideInterval = setInterval(nextSlide, intervalTime);
												}
												button4.onclick = function(){
													const activeSlide = document.querySelector('.activeSlide');
													activeSlide.classList.remove('activeSlide');
													slides[3].classList.add('activeSlide');

													const activeBtn = document.querySelector('.activeButton');
													activeBtn.classList.remove('activeButton');
													buttons[3].classList.add('activeButton');

													clearInterval(slideInterval);
													slideInterval = setInterval(nextSlide, intervalTime);
												}

												//Filters
												const dogFilters = document.querySelectorAll('.dogSelection');
												const dogSubmit = document.getElementById('breedFilterSubmit');
												var k;
												for(k = 0; k < dogFilters.length; k++){
													dogFilters[k].onclick = function(){
														if(dogSubmit.classList.contains('hideFilterSubmit')){
															dogSubmit.classList.remove('hideFilterSubmit');
														}
														if (this.previous) {
															this.checked = false;
															var i;
															for(i = 0; i < dogFilters.length; i++){
																if(dogFilters[i].checked){
																	break;
																}
																if(i==dogFilters.length-1){
																	dogSubmit.classList.add('hideFilterSubmit');
																}
															}
														}
														this.previous = this.checked;
													}
												}

												const ageFilters = document.querySelectorAll('.ageSelection');
												const ageSubmit = document.getElementById('ageFilterSubmit');
												for(k = 0; k < ageFilters.length; k++){
													ageFilters[k].onclick = function(){
														if(ageSubmit.classList.contains('hideFilterSubmit')){
															ageSubmit.classList.remove('hideFilterSubmit');
														}
														if (this.previous) {
															this.checked = false;
															var i;
															for(i = 0; i < ageFilters.length; i++){
																if(ageFilters[i].checked){
																	break;
																}
																if(i==ageFilters.length-1){
																	ageSubmit.classList.add('hideFilterSubmit');
																}
															}
														}
														this.previous = this.checked;
													}
												}

												const genderFilters = document.querySelectorAll('.genderSelection');
												const genderSubmit = document.getElementById('genderFilterSubmit');
												for(k = 0; k < genderFilters.length; k++){
													genderFilters[k].onclick = function(){
														if(genderSubmit.classList.contains('hideFilterSubmit')){
															genderSubmit.classList.remove('hideFilterSubmit');
														}
														if (this.previous) {
															this.checked = false;
															var i;
															for(i = 0; i < genderFilters.length; i++){
																if(genderFilters[i].checked){
																	break;
																}
																if(i==genderFilters.length-1){
																	genderSubmit.classList.add('hideFilterSubmit');
																}
															}
														}
														this.previous = this.checked;
													}
												}

												const sizeFilters = document.querySelectorAll('.sizeSelection');
												const sizeSubmit = document.getElementById('sizeFilterSubmit');
												for(k = 0; k < ageFilters.length; k++){
													sizeFilters[k].onclick = function(){
														if(sizeSubmit.classList.contains('hideFilterSubmit')){
															sizeSubmit.classList.remove('hideFilterSubmit');
														}
														if (this.previous) {
															this.checked = false;
															var i;
															for(i = 0; i < sizeFilters.length; i++){
																if(sizeFilters[i].checked){
																	break;
																}
																if(i==sizeFilters.length-1){
																	sizeSubmit.classList.add('hideFilterSubmit');
																}
															}
														}
														this.previous = this.checked;
													}
												}
											</script>
											<?php get_footer(); ?>