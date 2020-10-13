jQuery(document).ready(function($){

    //------------------------------------------------------------------------
	//Adoption Form
    //------------------------------------------------------------------------
    const formAdoption = document.querySelector(".formAdoption");
    /*
    formAdoption.onsubmit = function(){
        var inputs = document.querySelectorAll(".adoptionFormInput");
        var data = {};
        for(var i = 0; i < inputs.length; i++){
            data[inputs[i].getAttribute("name")] = inputs[i].value;
        }
        var jsonData = JSON.stringify(data);        
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : document.location.origin + '/wp-content/themes/mesmerize/adoptionFormSubmit.php',
            data : jsonData,
            success: function() {
               alert("Success");
            },
            error: function(){
                alert("error");
            }
         });
    }*/
    const adoptFormContainer = document.querySelector(".adoptionFormContainer");
    const adoptionForm = document.querySelector(".adoptionForm");
    const adoptButton = document.getElementById("adoptButton");
    const adoptFormCancel = document.getElementById("adoptFormCancel");
    adoptionForm.remove();
    adoptButton.onclick = function(){
        adoptionForm.style.display = "block";
        adoptFormContainer.appendChild(adoptionForm);
        adoptFormContainer.style.display = "block";
        var inputs = document.querySelectorAll(".adoptionFormInput");
        var data = {};
        for(var i = 0; i < inputs.length; i++){
            data[inputs[i].getAttribute("name")] = inputs[i].value;
        }
        var jsonData = JSON.stringify(data);   
        jQuery.post(document.location.origin + '/wp-content/themes/mesmerize/adoptionFormSubmit.php',
         jsonData,
         function(data){
            console.log(data);
         });     
    }
    adoptFormCancel.onclick = function(){
        adoptFormContainer.style.display = "none";
    }
    function adoptionFormSubmit(){
        alert("hello");
    
        /*
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : myAjax.ajaxurl,
            data : {action: "my_user_like", post_id : post_id, nonce: nonce},
            success: function(response) {
               if(response.type == "success") {
                  jQuery("#like_counter").html(response.like_count);
               }
               else {
                  alert("Your like could not be added");
               }
            }
         });*/
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