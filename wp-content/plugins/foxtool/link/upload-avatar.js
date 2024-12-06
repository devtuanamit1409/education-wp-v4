jQuery(document).ready(function($){
    $(document).on('click', '.foxtool-image', function(e) {
        e.preventDefault();
        var button = $(this);
        var foxtool_image_id = $('#foxtool_image_id');
        var mediaUploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select Image'
            },
            multiple: false  
        });
        mediaUploader.open();
        mediaUploader.on('select', function(){
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            foxtool_image_id.val(attachment.id);
            $('#foxtool-img').attr('src', attachment.url).show();
        });
    });
    $(document).on('click', '#reset-hinh-anh', function(e) {
        e.preventDefault();
        var foxtool_image_id = $('#foxtool_image_id');
        foxtool_image_id.val('');
        $('#foxtool-img').attr('src', '').hide();
    });
});
