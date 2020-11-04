jQuery(document).ready(function($){
    //------------------------------------------------------------------------
	//Adoption Form
    //------------------------------------------------------------------------
    function validateEmail(email) 
    {
        var re = /\S+@\S+\.\S+/;
        if(re.test(email)){
            return true;
        }
        else{
            alert("Invalid email");
            return false;
        }
        return re.test(email);
    }
    function allLetter(input){ 
        var letters = /^[A-Za-z]+$/;
        if(input.match(letters)){
            return true;
        }
        else{
            alert("Invalid name");
            return false;
        }
    }
    const adoptionBtn = document.getElementById("adoptionFormBtn");
    const fnameInput = document.getElementById("fname");
    const lnameInput = document.getElementById("lname");
    const email = document.getElementById("emailAddr");
    adoptionBtn.onclick = function(){
        if(validateEmail(email.value) && allLetter(fnameInput.value) && allLetter(lnameInput.value)){
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