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
		height: 1rem;
	}
	.filters label{
		font-size: 1rem;

	}
	.scrollRadio{
		height: 15rem;
		overflow: scroll;
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
		transition: background-color .4s;
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
		overflow: hidden;
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
	}
	.activeButton{
		background-color: dodgerblue;
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
						<div class="slide  activeSlide" style="background-image: url('<?php echo site_url("/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/featuredCover.jpg"); ?>'); ">
							<div class="featuredContent">
								<h2 style="color: white;">
									Hi I'm Dog1
								</h2>
								<p style="color: white;">
									I really don’t like getting my photo taken. Professional headshots, family photos, and even selfies with friends — it’s not always a natural-feeling experience to be the focus of attention. The pressure of the photographer staring at me, trying to figure out what pose doesn’t make me look 5lbs heavier, holding a smile for what…
								</p>
							</div>
						</div>
						<div class="slide " style="background-image: url('<?php echo site_url("/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/featuredTwo.jpg"); ?>'); ">
							<div class="featuredContent">
								<h2 style="color: white;">
									Hi I'm Dog2
								</h2>
								<p style="color: white;">
									I really don’t like getting my photo taken. Professional headshots, family photos, and even selfies with friends — it’s not always a natural-feeling experience to be the focus of attention. The pressure of the photographer staring at me, trying to figure out what pose doesn’t make me look 5lbs heavier, holding a smile for what…
								</p>
							</div>
						</div>
						<div class="slide" style="background-image: url('<?php echo site_url("/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/featuredThree.jpg"); ?>'); ">
							<div class="featuredContent">
								<h2 style="color: white;">
									Hi I'm Dog3
								</h2>
								<p style="color: white;">
									I really don’t like getting my photo taken. Professional headshots, family photos, and even selfies with friends — it’s not always a natural-feeling experience to be the focus of attention. The pressure of the photographer staring at me, trying to figure out what pose doesn’t make me look 5lbs heavier, holding a smile for what…
								</p>
							</div>
						</div>
					</div>
					</div>
				</div>
				<div class="row no-gutters" style="margin: auto;">
					<div class="col-md-12">
						<div class="buttonContainer">
							<ul class="featuredButtons" style="list-style: none;">
								<li>
									<button id="featured1" class="featuredButton activeButton"></button>
								</li>
								<li>
									<button id="featured2" class="featuredButton"></button>
								</li>
								<li>
									<button id="featured3" class="featuredButton"></button>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<hr style="margin-top: 10px;">
		</section>
		<div class="flexbox">
			<div class="col-3" style="width: 100%; margin-right: .5rem;">
				<!--<?php wp_nav_menu(array('theme_location' => 'filter-menu')); ?>-->
				<div class="filters">
					<h2>
						Filters
					</h2>
					<ul>
						<li>
							<hr>
							<h4>Breed</h4>
							<ul class="scrollRadio">
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
							</ul>
						</li>
						<li>
							<hr>
							<h4>Age</h4>
							<ul>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
							</ul>
						</li>
						<li>
							<hr>
							<h4>Gender</h4>
							<ul>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
							</ul>
						</li>
						<li>
							<hr>
							<h4>Size</h4>
							<ul>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
							</ul>
						</li>
						<li>
							<hr>
							<h4>Good With</h4>
							<Ul>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
								<li>
									<input type="radio" id="GoldenRetriever" name="gender" value="GoldenRetriever">
									<label for="GoldenRetriever">Golden Retriever</label><br>
								</li>
							</Ul>
						</li>
						<hr>
					</ul>
					<button type="button" style="width: 100%;">APPLY</button>
				</div>
			</div>
			<div class="col-9">
				<div class="row spaced-cols content-center-sm" data-type="row">
					<div class="col-sm-4">
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
						</div> 
					</div>
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p>
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
							<h6 class="">Pet Name</h6>
							<p class="small italic">Shelter Name</p>
						</div>
					</div> 
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;"> 
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"> 
							<h6 class="">Pet Name</h6>
							<p class="small italic">Shelter Name</p>
						</div> 
					</div>
				</div>
				<div class="row spaced-cols content-center-sm" data-type="row" >
					<div class="col-sm-4">
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
						</div> 
					</div>
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p>
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
							<h6 class="">Pet Name</h6>
							<p class="small italic">Shelter Name</p>
						</div>
					</div> 
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;"> 
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"> 
							<h6 class="">Pet Name</h6>
							<p class="small italic">Shelter Name</p>
						</div> 
					</div>
				</div>
				<div class="row spaced-cols content-center-sm" data-type="row">
					<div class="col-sm-4">
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
							<h6 class="">Pet Name</h6> 
							<p class="small italic">Shelter Name</p>
						</div> 
					</div>
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;">
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p>
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
							<h6 class="">Pet Name</h6>
							<p class="small italic">Shelter Name</p>
						</div>
					</div> 
					<div class="col-sm-4"> 
						<div class="card y-move bordered" data-type="column" style="margin-bottom: 1.5rem;"> 
							<p class="text-center">Do you sometimes have the feeling that you’re running into the same obstacles over and over again? Many of my conflicts have the same feel to them, like “Hey, I think I’ve been here before,</p> 
							<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"> 
							<h6 class="">Pet Name</h6>
							<p class="small italic">Shelter Name</p>
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
							<div class="card">
								<div class="img">
									<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon">
								</div>
								<div class="info">
									<h6 class="">Pet Name</h6>
								</div>
							</div>
						</div>
						<div class="card-container">
							<div class="card">
								<div class="img">
									<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
									<div class="info">
										<h6 class="">Pet Name</h6>
									</div>
								</div>
							</div>
							<div class="card-container">
								<div class="card">
									<div class="img">
										<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
										<div class="info">
											<h6 class="">Pet Name</h6>
										</div>
									</div>
								</div>
								<div class="card-container">
									<div class="card">
										<div class="img">
											<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
											<div class="info">
												<h6 class="">Pet Name</h6>
											</div>
										</div>
									</div>
									<div class="card-container">
										<div class="card">
											<div class="img">
												<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
												<div class="info">
													<h6 class="">Pet Name</h6>
												</div>
											</div>
										</div>
										<div class="card-container">
											<div class="card">
												<div class="img">
													<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
													<div class="info">
														<h6 class="">Pet Name</h6>
													</div>
												</div>
											</div>
											<div class="card-container">
												<div class="card">
													<div class="img">
														<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
														<div class="info">
															<h6 class="">Pet Name</h6>
														</div>
													</div>
												</div>
												<div class="card-container">
													<div class="card">
														<div class="img">
															<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
															<div class="info">
																<h6 class="">Pet Name</h6>
															</div>
														</div>
													</div>
													<div class="card-container">
														<div class="card">
															<div class="img">
																<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																<div class="info">
																	<h6 class="">Pet Name</h6>
																</div>
															</div>
														</div>
														<div class="card-container">
															<div class="card">
																<div class="img">
																	<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																	<div class="info">
																		<h6 class="">Pet Name</h6>
																	</div>
																</div>
															</div>
															<div class="card-container">
																<div class="card">
																	<div class="img">
																		<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																		<div class="info">
																			<h6 class="">Pet Name</h6>
																		</div>
																	</div>
																</div>
																<div class="card-container">
																	<div class="card">
																		<div class="img">
																			<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																			<div class="info">
																				<h6 class="">Pet Name</h6>
																			</div>
																		</div>
																	</div>
																	<div class="card-container">
																		<div class="card">
																			<div class="img">
																				<img src="<?php echo site_url('/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/dog.jpg'); ?>" class="round icon"></div>
																				<div class="info">
																					<h6 class="">Pet Name</h6>
																				</div>
																			</div>
																		</div>
																		<div class="card-container">
																			<div class="card">
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
												const intervalTime = 5000;
												const button1 = document.getElementById('featured1');
												const button2 = document.getElementById('featured2');
												const button3 = document.getElementById('featured3');


												button1.onclick = function(){
													const activeSlide = document.querySelector('.activeSlide');
													activeSlide.classList.remove('activeSlide');
													slides[0].classList.add('activeSlide');

													const activeBtn = document.querySelector('.activeButton');
													activeBtn.classList.remove('activeButton');
													buttons[0].classList.add('activeButton');
												}
												button2.onclick = function(){
													const activeSlide = document.querySelector('.activeSlide');
													activeSlide.classList.remove('activeSlide');
													slides[1].classList.add('activeSlide');

													const activeBtn = document.querySelector('.activeButton');
													activeBtn.classList.remove('activeButton');
													buttons[1].classList.add('activeButton');
												}
												button3.onclick = function(){
													const activeSlide = document.querySelector('.activeSlide');
													activeSlide.classList.remove('activeSlide');
													slides[2].classList.add('activeSlide');

													const activeBtn = document.querySelector('.activeButton');
													activeBtn.classList.remove('activeButton');
													buttons[2].classList.add('activeButton');
												}
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


											</script>
											<?php get_footer(); ?>