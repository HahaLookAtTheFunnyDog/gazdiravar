jQuery(document).ready(function($){
    //------------------------------------------------------------------------
	//Adoption Form
    //------------------------------------------------------------------------
    const formAdoption = document.querySelector(".formAdoption");
    formAdoption.onsubmit = function(){
        var inputs = document.querySelectorAll(".adoptionFormInput");
        var data = {};
        for(var i = 0; i < inputs.length; i++){
            data[inputs[i].getAttribute("name")] = inputs[i].value;
        }
        jQuery.post(document.location.origin + '/adoptionFormSubmit',
         data,
         function(data){
            alert(data);
         });     
    }
    const adoptFormContainer = document.querySelector(".adoptionFormContainer");
    const adoptionForm = document.querySelector(".adoptionForm");
    const adoptButton = document.getElementById("adoptButton");
    const adoptFormCancel = document.getElementById("adoptFormCancel");
    adoptionForm.remove();
    adoptButton.onclick = function(){
        adoptionForm.style.display = "block";
        adoptFormContainer.appendChild(adoptionForm);
        adoptFormContainer.style.display = "block";
    }
    adoptFormCancel.onclick = function(){
        adoptFormContainer.style.display = "none";
    }
    
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
});