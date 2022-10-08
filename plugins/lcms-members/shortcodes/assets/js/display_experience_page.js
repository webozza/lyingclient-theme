jQuery(document).ready(function ($) {
    $(".btnNext").click(function () {
		errorMsg();
		FieldsChecker(true);
        const nextTabLinkEl = $(".nav-tabs .active").closest("li").next("li").find("a")[0];
        const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();
    });

    $(".btnPrevious").click(function () {
        const prevTabLinkEl = $(".nav-tabs .active").closest("li").prev("li").find("a")[0];
        const prevTab = new bootstrap.Tab(prevTabLinkEl);
        prevTab.show();
    });

    //attachement

    $(".attachment_div .attachment_popup_div .attachment_popup_div_close").click(() => {
        $(".attachment_div .attachment_popup_div").removeClass("active");
    })
    $(".attachment_div .view_attachment").click(() => {
        if ($(".attachment_div .attachment_popup_div").hasClass("active")) {
            $(".attachment_div .attachment_popup_div").removeClass("active");
        } else {
            $(".attachment_div .attachment_popup_div").addClass("active");
        }
    });



    var experience_post_id = jQuery('input[name="experience_post_id"]').val();
    var dbase_ajax_url = jQuery('input[name="admin_ajax_url_dbase"]').val();

    //edit experience attachement addon
    
    jQuery('a.exp_edit_client_attachement_addon').click((e) => {
        e.preventDefault();

        jQuery('div.exp_edit_client_attachement_div').append('<div class="exp_edit_client_attachement_parent_div"><img src="none" class="exp_edit_client_attachement_image_preview" height="250" width="250" style="border: 2px solid red; padding: 5px; margin: 5px"/><input type="file" accept="image/png, image/jpeg" name="exp_edit_client_attachement[]" id="exp_edit_client_attachement" class="form-control" /><input type="hidden" name="exp_edit_client_attachement_input[]" id="exp_edit_client_attachement_input" class="form-control" /><a href="#" class="btn exp_edit_client_attachement_remove_child">Remove</a></div>');

        attachementEditRemoveFunction();
    
    });


    //attachement   
    function attachementEditRemoveFunction() {
        jQuery('a.exp_edit_client_attachement_remove_child').click((e) => {
            e.preventDefault();

            jQuery(e.currentTarget).parents('.exp_edit_client_attachement_parent_div').remove();
        });

        jQuery('input[name="exp_edit_client_attachement[]"]').change((e) => {
            const blob_url = URL.createObjectURL(e.target.files[0]);
            const parent_div_parent = jQuery(e.currentTarget).parents('.exp_edit_client_attachement_parent_div');


            parent_div_parent.prepend('<p class="exp_edit_client_attachement_loading_state">your image is uploading!...</p>');

            if (parent_div_parent.children('.exp_edit_client_attachement_image_preview').hasClass('active')) {
                parent_div_parent.children('.exp_edit_client_attachement_image_preview').removeClass('active')
            }

            const formData = new FormData();

            const image_info = jQuery(e.currentTarget).prop("files")[0];

            formData.append("action", "ajax_function_for_image_uploading");
            formData.append("experience_post_id", experience_post_id);
            formData.append("exp_edit_client_attachement", image_info);

            jQuery.ajax({
                type: 'POST',
                url: dbase_ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    console.log('Posting Response...');
                },
                success: function (data) {

                    console.log(data);
                    parent_div_parent.children('.exp_edit_client_attachement_image_preview').attr('src', data);
                    parent_div_parent.children('.exp_edit_client_attachement_image_preview').addClass('active');
                    parent_div_parent.children('.exp_edit_client_attachement_loading_state').remove();

                    parent_div_parent.children('input[name="exp_edit_client_attachement_input[]"]').attr("value", data)

                },
                error: function (response) {
                    console.log(response);
                }
            });


        });
    }
    attachementEditRemoveFunction();


});