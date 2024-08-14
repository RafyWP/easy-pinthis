jQuery(document).ready(function($) {
    var frame;
    
    $('#upload_images_button').on('click', function(e) {
        e.preventDefault();
        
        if (frame) {
            frame.open();
            return;
        }
        
        frame = wp.media({
            title: 'Select Images',
            button: {
                text: 'Use these images'
            },
            multiple: true
        });
        
        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            var imageIds = [];
            $('#image_preview').html('');
            
            $.each(attachments, function(index, attachment) {
                imageIds.push(attachment.id);
                $('#image_preview').append('<img src="' + attachment.url + '" style="width: 100px; height: auto; margin-right: 10px;" />');
            });
            
            $('#ez_pin_group_images').val(imageIds.join(','));
        });
        
        frame.open();
    });
});
