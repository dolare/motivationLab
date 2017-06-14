function add_image(obj) {
        var parent=jQuery(obj).parent().parent('div.field_row');
        var inputField = jQuery(parent).find("input.meta_image_url");
        var fileFrame = wp.media.frames.file_frame = wp.media({
            multiple: false
        });
        fileFrame.on('select', function() {
            var url = fileFrame.state().get('selection').first().toJSON();
            inputField.val(url.id);
            jQuery(parent)
            .find("div.image_wrap")
            .html('<img src="'+url.url+'" height="125" width="200" />');
        });
        fileFrame.open();
    //});
    };
    function remove_field(obj) {
        var parent=jQuery(obj).parent().parent();
        //console.log(parent)
        parent.remove();
    }
    function add_field_row() {
        var row = jQuery('#master-row').html();
        jQuery(row).appendTo('#field_wrap');
    }
	function add_field_row_new() {
        var row = jQuery('#master-row-new').html();
        jQuery(row).appendTo('#field_wrap-new');
    }