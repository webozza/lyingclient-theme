jQuery(document).ready(function($) {

	$('.tab-pane#client-details input, .tab-pane#experience input').parent().append('<p style="color:red" class="webozza-error-msg"></p>')
	
	function errorMsg() {
		const client_input_error = $(".client-input-error");
        const exp_add_client_name = jQuery('input[name="exp_add_client_name"]').val();
        const exp_add_client_phone_number = jQuery('input[name="exp_add_client_phone_number"]').val();
        const exp_add_client_email = jQuery('input[name="exp_add_client_email"]').val();
        const exp_add_client_zipcode = jQuery('input[name="exp_add_client_zipcode"]').val();
		var re = /\S+@\S+\.\S+/;
		if(exp_add_client_name == ""){
			$('input#exp_add_client_name').parent().find('.webozza-error-msg').text("Client Name Field Is Missing! Try again");
		} 
		if(exp_add_client_email == ""){
			$('input#exp_add_client_email').parent().find('.webozza-error-msg').text("Client Email Field Is Missing! Try again");
		} 
		if(!re.test(exp_add_client_email)){
			$('input#exp_add_client_email').parent().find('.webozza-error-msg').text("Please Enter Valid E-mail! Try again");
		} 
		if(exp_add_client_zipcode == ""){
			$('input#exp_add_client_zipcode').parent().find('.webozza-error-msg').text("Client zip Code Field Is Missing! Try again");
		} 
		if(isNaN(exp_add_client_zipcode)){
			$('input#exp_add_client_zipcode').parent().find('.webozza-error-msg').text("Please Enter valid Client Zip Code! Try again");
		}
	}
	
    function FieldsChecker(stepTwo){
        const client_input_error = $(".client-input-error");
        const exp_add_client_name = jQuery('input[name="exp_add_client_name"]').val();
        const exp_add_client_phone_number = jQuery('input[name="exp_add_client_phone_number"]').val();
        const exp_add_client_email = jQuery('input[name="exp_add_client_email"]').val();
        const exp_add_client_zipcode = jQuery('input[name="exp_add_client_zipcode"]').val();

        var re = /\S+@\S+\.\S+/;
        

        if(exp_add_client_name != "" && exp_add_client_email != "" && re.test(exp_add_client_email) && exp_add_client_zipcode != "" && !isNaN(exp_add_client_zipcode) ){
            const nextTabLinkEl = $(".nav-tabs .active").closest("li").next("li").find("a")[0];
            const nextTab = new bootstrap.Tab(nextTabLinkEl);
            nextTab.show();
            
            if(client_input_error.hasClass("active")){
                client_input_error.removeClass("active");
            }
			
            if(stepTwo){
                $("ul.nav-tabs .step-two-prevent a").attr("style", "pointer-events: auto")
            }
        }else{


            if(exp_add_client_name == ""){
                client_input_error.html("<p>Client Name Field Is Missing! Try again</p>");
				$('input#exp_add_client_name').parent().find('.webozza-error-msg').text("Client Name Field Is Missing! Try again");
            } else if(exp_add_client_email == ""){
				client_input_error.html("<p>Client Email Field Is Missing! Try again</p>");
				$('input#exp_add_client_email').parent().find('.webozza-error-msg').text("Client Email Field Is Missing! Try again");
            } else if(!re.test(exp_add_client_email)){
                client_input_error.html("<p>Please Enter Valid E-mail! Try again</p>");
				$('input#exp_add_client_email').parent().find('.webozza-error-msg').text("Please Enter Valid E-mail! Try again");
            } else if(exp_add_client_zipcode == ""){
                client_input_error.html("<p>Client zip Code Field Is Missing! Try again</p>");
				$('input#exp_add_client_zipcode').parent().find('.webozza-error-msg').text("Client zip Code Field Is Missing! Try again");
            } else if(isNaN(exp_add_client_zipcode)){
                client_input_error.html("<p>Please Enter valid Client Zip Code! Try again</p>");
				$('input#exp_add_client_zipcode').parent().find('.webozza-error-msg').text("Please Enter valid Client Zip Code! Try again");
            } else{
                client_input_error.html("<p>Something Went! Try again</p>");
            }
            client_input_error.addClass("active");
            client_input_error.focus();

            setTimeout(()=>{
                client_input_error.removeClass("active");
            },10000)
    
        }
		
    }
    $(".btnNext").click((e)=> {
		errorMsg();
		$(this).attr("style", "pointer-events: none; cursor:not-allowed");
        FieldsChecker(true);
    });

    $("ul.nav-tabs .step-two-prevent").click((e)=>{
        errorMsg();
        $("ul.nav-tabs .step-two-prevent a").attr("style", "pointer-events: none");
        FieldsChecker(true);
    });
    
    $(".btnPrevious").click(function() {
        const prevTabLinkEl = $(".nav-tabs .active").closest("li").prev("li").find("a")[0];
        const prevTab = new bootstrap.Tab(prevTabLinkEl);
        prevTab.show();
    });



    //add experience attachement addon


    jQuery('a.exp_add_client_attachement_addon').click((e)=>{
        e.preventDefault();

        jQuery('div.exp_add_client_attachement_div').append('<div class="exp_add_client_attachement_parent_div"><img class="exp_add_client_attachement_parent_preview" height="250" width="250" style="border: 2px solid red; padding: 5px; margin: 5px" /><input type="file" accept="image/png, image/jpeg" name="exp_add_client_attachement[]" id="exp_add_client_attachement" /><a href="#" class="exp_add_client_attachement_remove_child">Remove</a></div>');

        attachementRemoveFunction();
        attachementChangeFunction();
    });



    function attachementChangeFunction(){
        jQuery('.exp_add_client_attachement_div input[name="exp_add_client_attachement[]"]').change((e)=>{
            const blob_url = URL.createObjectURL(e.target.files[0]);
            const parent_div_parent = jQuery(e.currentTarget).parents('.exp_add_client_attachement_parent_div');
            console.log('s')
            parent_div_parent.children('.exp_add_client_attachement_parent_preview').addClass('active');
            parent_div_parent.children('.exp_add_client_attachement_parent_preview').attr('src', blob_url);
        });
    }
    attachementChangeFunction();

    function attachementRemoveFunction(){
        jQuery('a.exp_add_client_attachement_remove_child').click((e)=>{
            e.preventDefault();

            jQuery(e.currentTarget).parents('.exp_add_client_attachement_parent_div').remove();
        });
    }
    attachementRemoveFunction();
	
	// Star Rating Mandatory
	$('.star-wrapper input').attr('required','');
	$('.star-wrapper input').on('click', function() {
		$('.star-wrapper input').removeAttr('required');
		$(this).attr('required')
	});
	
	// If user didn't give rating or selected from select field
	
	$('select#exp_add_client_category').change(function() {
		var selectedVal = $('select#exp_add_client_category').find(':selected').val();
		if(!selectedVal == 0) {
			$('select#exp_add_client_category').removeAttr('required');
		}
	});

	$('#my-client-experience-container form button.bsui.btn-primary').on('click', function() {
		if($('.star-wrapper input').attr('required').length > 0) {
			$('.star-wrapper .webozza-error-msg').text('please rate experience');
		}
		if($('select#exp_add_client_category').find(':selected').val() == 0) {
			$('#exp_add_client_category').parent().append('<p style="color:red" class="webozza-error-msg">Please Select A Value</p>')
		}
	});
	
	// Select field mandatory
	$('select#exp_add_client_category').attr('required','');
	
	
});