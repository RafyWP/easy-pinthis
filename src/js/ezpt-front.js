jQuery(document).ready(function($) {
    $('.pin-click').on('click', function(e) {
        e.preventDefault();
        var pin_id = $(this).attr('pin-id');
        $('#pin_id').val(pin_id);
    });

    $('.folder .item').on('click', function(e) {
        e.preventDefault();
    
        var pin_id = $('#pin_id').val();
        var folder_id = $(this).attr('folder-id');
    
        var data = {
            pin_id: pin_id,
            folder_id: folder_id
        };
    
        fetch(ezptFront.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': ezptFront.nonce
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Sucesso:', data);
        })
        .catch((error) => {
            console.error('Erro:', error);
        });
    });    
});
