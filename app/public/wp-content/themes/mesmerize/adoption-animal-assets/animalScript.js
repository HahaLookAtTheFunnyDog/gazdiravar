    //------------------------------------------------------------------------
	//Slideshow
    //------------------------------------------------------------------------
    const prevButton = document.querySelector('.slidePrev');
    const nextButton = document.querySelector('.slideNext');
    const slides     = document.querySelectorAll('.slide');

    nextButton.onclick = function(){
        const activeSlide = document.querySelector('.active');
        activeSlide.classList.remove('active');
        if(activeSlide.nextElementSibling){
            activeSlide.nextElementSibling.classList.add('active');
        }
        else{
            slides[0].classList.add('active');
        }
    }
    prevButton.onclick = function(){
        const activeSlide = document.querySelector('.active');
        activeSlide.classList.remove('active');
        if(activeSlide.previousElementSibling){
            activeSlide.previousElementSibling.classList.add('active');
        }
        else{
            slides[slides.length-1].classList.add('active');
        }
    }
    //------------------------------------------------------------------------
	//Adoption Form
    //------------------------------------------------------------------------
    const adoptFormContainer = document.querySelector(".adoptionFormContainer");
    const adoptButton = document.getElementById("adoptButton");
    const adoptFormCancel = document.getElementById("adoptFormCancel");

    adoptButton.onclick = function(){
        adoptFormContainer.style.display = "block";
    }
    adoptFormCancel.onclick = function(){
        adoptFormContainer.style.display = "none";
    }