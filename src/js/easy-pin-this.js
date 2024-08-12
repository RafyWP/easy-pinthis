jQuery(document).ready(function($) {
    $('#upload_image_button').on('click', function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: easyPinThis.selectImage,
            button: { text: easyPinThis.useImage },
            multiple: false
        }).open().on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $('#default_image').val(image_url);
        });
    });
});
