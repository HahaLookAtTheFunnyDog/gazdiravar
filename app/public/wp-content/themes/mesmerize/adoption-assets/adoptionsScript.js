    //------------------------------------------------------------------------
	//PAGINATION SUBMIT
    //------------------------------------------------------------------------
    function paginationSubmit(pid){
        const paginationForm = document.getElementById('paginationForm');

        var pidInput = document.createElement("input");
        pidInput.value = pid;
        pidInput.type ="hidden";
        pidInput.name = "pid";
        paginationForm.appendChild(pidInput);
        paginationForm.submit();
    }
    //------------------------------------------------------------------------
	//FILTER TAGS
	//------------------------------------------------------------------------
	function resubmitHelper(arr,remove,form,str){
		var i;
		for(i = 0; i < arr.length; i++){
			if(!(arr[i] === remove)){
				var elementInput = document.createElement("input");
				elementInput.value = arr[i];
				elementInput.name = str + "[]";
				form.appendChild(elementInput);
			}
		}
	}
	function resubmit(breed, age, gender, size, remove){
		breed = breed || 0;
		age = age || 0;
		gender = gender || 0;
		size = size || 0;

		var myjson = JSON.stringify(breed);
		breed = JSON.parse(myjson);

		myjson = JSON.stringify(age);
		ages = JSON.parse(myjson);

		myjson = JSON.stringify(gender);
		genders = JSON.parse(myjson);

		myjson = JSON.stringify(size);
		sizes = JSON.parse(myjson);

		var form = document.createElement("form");
		form.method = "GET";
		resubmitHelper(breed,remove,form,"breed");
		resubmitHelper(ages,remove,form,"age");
		resubmitHelper(genders,remove,form,"gender");
		resubmitHelper(sizes,remove,form,"size");
		document.body.appendChild(form);
		form.submit();
	}
	function clearAll(){
		var form = document.createElement("form");
		document.body.appendChild(form);
		form.submit();
	}
	//------------------------------------------------------------------------
	//FILTER Checkbox
	//------------------------------------------------------------------------
	function filterFunctionalityHelper(selection,btn){
		var k;
		for(k = 0; k < selection.length; k++){
			selection[k].onclick = function(){
				if(btn.classList.contains('hideFilterSubmit')){
					btn.classList.remove('hideFilterSubmit');
				}
				if (this.previous) {
					this.checked = false;
					var i;
					for(i = 0; i < selection.length; i++){
						if(selection[i].checked){
							break;
						}
						if(i==selection.length-1){
							btn.classList.add('hideFilterSubmit');
						}
					}
				}
				this.previous = this.checked;
			}
		}
	}
	const dogFilters = document.querySelectorAll('.dogSelection');
	const dogSubmit = document.getElementById('breedFilterSubmit');
	const ageFilters = document.querySelectorAll('.ageSelection');
	const ageSubmit = document.getElementById('ageFilterSubmit');
	const genderFilters = document.querySelectorAll('.genderSelection');
	const genderSubmit = document.getElementById('genderFilterSubmit');
	const sizeFilters = document.querySelectorAll('.sizeSelection');
	const sizeSubmit = document.getElementById('sizeFilterSubmit');
	var k;
	filterFunctionalityHelper(dogFilters,dogSubmit);
	filterFunctionalityHelper(ageFilters,ageSubmit);
	filterFunctionalityHelper(genderFilters,genderSubmit);
	filterFunctionalityHelper(sizeFilters,sizeSubmit);

	function countrySelector(){
		const countryFilter = document.getElementById('countryFilterSubmit');
		countryFilter.classList.remove('hideFilterSubmit');
	}
	//------------------------------------------------------------------------
	//RECENTLY VIEWED
	//------------------------------------------------------------------------
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
	//------------------------------------------------------------------------
	//SLIDES
	//------------------------------------------------------------------------
	const buttons = document.querySelectorAll('.featuredButton');
	const slides = document.querySelectorAll('.slide');
	const intervalTime = 8000;

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

	function featuredButton(index){
		const activeSlide = document.querySelector('.activeSlide');
		activeSlide.classList.remove('activeSlide');
		slides[index-1].classList.add('activeSlide');

		const activeBtn = document.querySelector('.activeButton');
		activeBtn.classList.remove('activeButton');
		buttons[index-1].classList.add('activeButton');
	}