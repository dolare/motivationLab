(function($) {
  "use strict";
 jQuery(function($){
      if(jQuery('#upload_image_preview').attr('src')==''){
       jQuery('#upload_image_preview').hide();
       jQuery('#upload_agent_button_remove').hide();
    }
    jQuery('#upload_agent_button_remove').live('click', function() {
        jQuery('#upload_image_preview').attr('src','');
        jQuery('#agent_url').val('');
        jQuery('#upload_image_preview').hide();
    })
    jQuery('#upload_agent_button').live('click', function() {
        var fileFrame = wp.media.frames.file_frame = wp.media({
            multiple: false
        });
        fileFrame.on('select', function() {
            var attachment = fileFrame.state().get('selection').first().toJSON();
	    jQuery('#agent_url').val(attachment.id);
            jQuery('#upload_image_preview').show();
            jQuery('#upload_agent_button_remove').show();
            jQuery('img#upload_image_preview').attr('src',attachment.url);
});
fileFrame.open(); 
});
});
$(document).ready(function(){
//header options for page/post
	var $borntogive_pages_choose_slider_display = jQuery('#borntogive_pages_Choose_slider_display');
    function pages_slider_display() {
        var $borntogive_pages_slider_image = jQuery('#borntogive_pages_slider_image-description').parent().parent();
        var $borntogive_pages_slider_pagination = jQuery('#borntogive_pages_slider_pagination').parent().parent();
        var $borntogive_pages_slider_auto_slide = jQuery('#borntogive_pages_slider_auto_slide').parent().parent();
        var $borntogive_pages_slider_direction_arrows = jQuery('#borntogive_pages_slider_direction_arrows').parent().parent();
        var $borntogive_pages_slider_effects = jQuery('#borntogive_pages_slider_effects').parent().parent();
		var $borntogive_pages_nivo_effects = jQuery('#borntogive_pages_nivo_effects').parent().parent();
        var $borntogive_pages_select_revolution_from_list = jQuery('#borntogive_pages_select_revolution_from_list').parent().parent();
		var $borntogive_banner_image = jQuery('#borntogive_header_image-description').parent().parent();
		var $borntogive_pages_slider_height = jQuery('#borntogive_pages_slider_height').parent().parent();
		var $borntogive_pages_banner_description = jQuery('#borntogive_pages_banner-description').parent().parent();
        var $borntogive_pages_banner_color = jQuery('#borntogive_pages_banner_color').parents('.rwmb-color-wrapper');
        var $borntogive_pages_banner_color_label = $borntogive_pages_banner_color.prev('.rwmb-label');
           if ($borntogive_pages_choose_slider_display.val() == 3) {
            $borntogive_pages_slider_image.show();
            $borntogive_pages_slider_pagination.show();
            $borntogive_pages_slider_auto_slide.show();
            $borntogive_pages_slider_direction_arrows.show();
            $borntogive_pages_slider_effects.show();
			 $borntogive_pages_slider_height.show();
            $borntogive_pages_select_revolution_from_list.hide();
			 $borntogive_banner_image.hide();
			 $borntogive_pages_banner_color.hide();
			 $borntogive_pages_nivo_effects.hide();
			 $borntogive_pages_banner_description.hide();
        }
		else if ($borntogive_pages_choose_slider_display.val() == 4) {
            $borntogive_pages_slider_image.show();
            $borntogive_pages_slider_pagination.show();
            $borntogive_pages_slider_auto_slide.show();
            $borntogive_pages_slider_direction_arrows.show();
			$borntogive_pages_nivo_effects.show();
            $borntogive_pages_slider_effects.hide();
			 $borntogive_pages_slider_height.show();
            $borntogive_pages_select_revolution_from_list.hide();
			 $borntogive_banner_image.hide();
			 $borntogive_pages_banner_color.hide();
			 $borntogive_pages_banner_description.hide();
        }
		else if($borntogive_pages_choose_slider_display.val() == 2) {
			  $borntogive_banner_image.show();
			  $borntogive_pages_banner_description.show();
			  $borntogive_pages_slider_image.hide();
            $borntogive_pages_slider_pagination.hide();
            $borntogive_pages_slider_auto_slide.hide();
            $borntogive_pages_slider_direction_arrows.hide();
            $borntogive_pages_slider_effects.hide();
            $borntogive_pages_select_revolution_from_list.hide();
			$borntogive_pages_slider_height.show();
			$borntogive_pages_banner_color.hide();
			$borntogive_pages_banner_color_label.hide();
			$borntogive_pages_nivo_effects.hide();
		}
        else if($borntogive_pages_choose_slider_display.val() == 5) {
             $borntogive_pages_slider_image.hide();
            $borntogive_pages_slider_pagination.hide();
            $borntogive_pages_slider_auto_slide.hide();
            $borntogive_pages_slider_direction_arrows.hide();
            $borntogive_pages_slider_effects.hide();
			$borntogive_banner_image.hide();
			$borntogive_pages_slider_height.hide();
			$borntogive_pages_banner_color.hide();
			$borntogive_pages_nivo_effects.hide();
            $borntogive_pages_select_revolution_from_list.show();
        }
		else if($borntogive_pages_choose_slider_display.val() == 1) {
			$borntogive_pages_banner_color.show();
			$borntogive_pages_banner_color_label.show();
			$borntogive_pages_banner_description.show();
			$borntogive_pages_slider_image.hide();
            $borntogive_pages_slider_pagination.hide();
            $borntogive_pages_slider_auto_slide.hide();
            $borntogive_pages_slider_direction_arrows.hide();
            $borntogive_pages_slider_effects.hide();
			$borntogive_banner_image.hide();
			$borntogive_pages_slider_height.show();
            $borntogive_pages_select_revolution_from_list.hide();
			$borntogive_pages_nivo_effects.hide();
		}
		else {
			$borntogive_pages_slider_image.hide();
            $borntogive_pages_slider_pagination.hide();
            $borntogive_pages_slider_auto_slide.hide();
            $borntogive_pages_slider_direction_arrows.hide();
            $borntogive_pages_slider_effects.hide();
			$borntogive_banner_image.hide();
			$borntogive_pages_slider_height.hide();
            $borntogive_pages_select_revolution_from_list.hide();
			$borntogive_pages_banner_color.hide();
			$borntogive_pages_nivo_effects.hide();
			$borntogive_pages_banner_description.hide();
		}
    }
    pages_slider_display();
    $borntogive_pages_choose_slider_display.change(function() {
        pages_slider_display();
    });
		// Event Recurrence Box
    var $borntogive_event_frequency_type = jQuery('#borntogive_event_frequency_type');
   function eventRecurrenceDisplay() {
        var $borntogive_event_day_month = jQuery('#borntogive_event_day_month').closest('.rwmb-field');
        var $borntogive_event_week_day = jQuery('#borntogive_event_week_day').closest('.rwmb-field');
        var $borntogive_event_frequency = jQuery('#borntogive_event_frequency').closest('.rwmb-field');
        var $borntogive_event_frequency_count = jQuery('#borntogive_event_frequency_count').closest('.rwmb-field');
				var $borntogive_event_multiple_type = jQuery('#borntogive_event_multiple_type');
     if ($borntogive_event_frequency_type.val() == 0) {
            $borntogive_event_day_month.hide();
            $borntogive_event_week_day.hide();
            $borntogive_event_frequency.hide();
            $borntogive_event_frequency_count.hide();
       }
       else if ($borntogive_event_frequency_type.val() == 1) {
            $borntogive_event_day_month.hide();
            $borntogive_event_week_day.hide();
            $borntogive_event_frequency.show();
            $borntogive_event_frequency_count.show();
       }
        else {
            $borntogive_event_day_month.show();
            $borntogive_event_week_day.show();
            $borntogive_event_frequency.hide();
            $borntogive_event_frequency_count.show();
        }
    }
    eventRecurrenceDisplay();
    $borntogive_event_frequency_type.change(function() {
        eventRecurrenceDisplay();
    });
		//Load Social Sites list for Staff Members
jQuery("#team_meta_box").on('click','#Social',function(){
	//alert("saibaba");
	var text_name = jQuery(this).find('input[type=text]').attr('name');
        jQuery( "body" ).data("text_name", text_name );
        jQuery("label#Social input").removeClass("fb");
	jQuery("label#Social").addClass("sfb");
	name = jQuery("label.sfb input").addClass("fb");
	var label = jQuery('label[for="'+jQuery(this).attr('id')+'"]');
	if(jQuery("#socialicons").length == 0) {
	jQuery("#team_meta_box").append("<div id=\"socialicons\"><div class=\"inside\"><div class=\"rwmb-meta-box\"><div class=\"rwmb-field rwmb-select-wrapper\"><div class=\"rwmb-label\"><label for=\"select_social_icons\">Select Social Icons</label></div><div class=\"rwmb-input\"><select class=\"rwmb-select\" id=\"social\"><option value\"select\">Select</option><option value=\"facebook\">facebook</option><option value=\"bitbucket\">bitbucket</option><option value=\"dribbble\">dribbble</option><option value=\"dropbox\">dropbox</option><option value=\"flickr\">flickr</option><option value=\"foursquare\">foursquare</option><option value=\"github\">github</option><option value=\"gittip\">gittip</option><option value=\"google-plus\">google-plus</option><option value=\"instagram\">instagram</option><option value=\"linkedin\">linkedin</option><option value=\"pagelines\">pagelines</option><option value=\"pinterest\">pinterest</option><option value=\"skype\">skype</option><option value=\"tumblr\">tumblr</option><option value=\"twitter\">twitter</option><option value=\"vimeo-square\">vimeo-square</option><option value=\"youtube\">youtube</option></select></div></div></div></div></div></div>");
	}
});
jQuery("#team_meta_box").on('change','div#socialicons select#social',function(text_id){
		var text_name=jQuery( "body" ).data( "text_name" );
                jQuery("#socialicons").remove();
                jQuery("label[id='Social']").find('input[name$="'+text_name+'"]').val(this.value);
//		jQuery( 'input[name$="'+text_name+'"]').val(this.value);
		jQuery("input").removeClass("fb");
	});
        jQuery("label[for='imic_social_icon_list']").click(function(e){
            e.preventDefault();
        });
});
jQuery('#charitable_settings_donation_form_display').parents('tr').hide();
jQuery('#charitable_settings_donation_form_display').parents('tr').prev('tr').hide();
jQuery('.redux-message').hide();
})(jQuery);