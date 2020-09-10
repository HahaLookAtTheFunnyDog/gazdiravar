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

</style>

<div id='page-content' class="page-content">
	<div class="<?php mesmerize_page_content_wrapper_class(); ?>">
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

</script>
<?php get_footer(); ?>