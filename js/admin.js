jQuery(document).ready(function($){
    var file_frame;
    var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;
    $(document).on("click", "#eapSocSettings .upload_image_button",function(e){

        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(e.target);

        jQuery.data(document.body, 'prevElement',$(e.target).prev());
        jQuery.data(document.body, 'previewImg',$(e.target).prev().prev());

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery( this ).data( 'uploader_title' ),
            button: {
                text: jQuery( this ).data( 'uploader_button_text' )
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {

            var inputText = jQuery.data(document.body,'prevElement');
            var prev   = jQuery.data(document.body,'previewImg');

            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();

            // Do something with attachment.id and/or attachment.url here
            inputText.val(attachment.url);
            prev.attr('src',attachment.url);
            inputText.trigger('change');
            $(e.target).siblings('.clear_img_btn').removeAttr('disabled');
        });

        // Finally, open the modal
        file_frame.open();

    });

    $('.add_media').on('click', function(){
        _custom_media = false;
    });


});