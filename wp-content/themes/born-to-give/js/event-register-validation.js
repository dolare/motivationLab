jQuery(document).ready(function(){
	jQuery(".exhibition-time.toggle-label").click(function(){
		jQuery(".exhibition-time.toggle-label").removeClass("accent-bg");
		jQuery(this).addClass("accent-bg");
	});
	function ValidateEmail(email) {
	var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return expr.test(email);
}; 
jQuery("select.event-tickets").on('change', function(){
	var event_total_cost_new = 0;
	var $formid_new = jQuery(this).closest("form").attr('id');
		var value = jQuery("option:selected", this).text();
		var ticket_price = jQuery(this).attr("data-price");
		if(Math.floor(ticket_price))
		{
			
		}
		else
		{
			ticket_price = '';
		}
		jQuery(this).parent().parent().find(".total-cost-event").empty();
		jQuery(this).parent().parent().find(".total-cost-event").text(value*ticket_price);
		jQuery(".total-cost-event").each(function() {
		event_total_cost_new += Number(jQuery(this).html());
		  });
		 if(Math.floor(event_total_cost_new))
		 {
			 //alert("value");
			 jQuery('form#'+$formid_new).attr('action', event_registration_new.paypal_src);
			 jQuery('#submit-registration').attr('value', event_registration_new.pays);
		 }
		 else
		 {
			jQuery('form#'+$formid_new).attr('action', '');
			jQuery('#submit-registration').attr('value', event_registration_new.reg);
		 }
});
jQuery('#submit-registration').on('click',function(e) {
	var is_select = 0;
	var event_total_cost = 0;
	var ticket_arr = {};
	var $formid = jQuery(this).closest("form").attr('id');
	jQuery("label.error").hide();
	var $exhibition_time = jQuery("input[name=exhibition-time]:checked").val();
	jQuery(".error").removeClass("error");
	jQuery('form#'+$formid+' .message').empty();
	var form_action = jQuery("form#"+$formid).attr("action");
	if(event_registration.multiple!='')
	{
	jQuery("select.event-tickets").each(function() {
   	var value = jQuery("option:selected", this).text();
		var ticket_type = jQuery(this).attr("data-title");
		
		if(value!=0)
		{
			is_select = 1;
			var number_tickets = value;
			ticket_arr[ticket_type] = number_tickets;
		}
    });
	jQuery(".total-cost-event").each(function() {
		event_total_cost += Number(jQuery(this).html());
  });
	jQuery("input[name=amount]").val(event_total_cost);
	}
	var $eventid = jQuery("form#"+$formid+" #event_id");
	var $userfield = jQuery("form#"+$formid+" #username");
	var $emailfield = jQuery("form#"+$formid+" #email");
	var $event_date = jQuery("form#"+$formid+" #event_date");
	var $phone = jQuery("form#"+$formid+" #phone").val();
	var $notes = jQuery("form#"+$formid+" #notes").val();
	var $address = jQuery("form#"+$formid+" #address").val();
	var $lastname = jQuery("form#"+$formid+" #lastname").val();
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var isValid = true;
	if (jQuery.trim($userfield.val()) == '') {
		isValid = false;
		jQuery('form#'+$formid+' .message').append("<div class=\"alert alert-error\">"+event_registration.name+"</div>");
		return false;
	} else if(!ValidateEmail($emailfield.val())) {
		isValid = false;
		jQuery('form#'+$formid+' .message').append("<div class=\"alert alert-error\">"+event_registration.emails+"</div>");
		return false;
	}
	else if(is_select==0&&event_registration.multiple!='') {
		isValid = false;
		jQuery('form#'+$formid+' .message').append("<div class=\"alert alert-error\">"+event_registration.tickets+"</div>");
		return false;
	} else {
		jQuery('form#'+$formid+' .message').append("<div class=\"alert alert-success\">"+event_registration.process+"</div>");
		jQuery.ajax({
			type: 'POST',
			url: event_registration.url,
			dataType : "json",
			async: false,
			data: {
				action: 'borntogive_contact_event_manager',
				itemnumber: $eventid.val(),
				exhibition_time: $exhibition_time,
				event_date: $event_date.val(),
				name: $userfield.val(),
				lastname: $lastname,
				email: $emailfield.val(),
				phone: $phone,
				notes: $notes,
				address: $address,
				ticket_details: ticket_arr,
				costs: event_total_cost
			},
			success: function(data) {
				jQuery('form#'+$formid+' .message').empty();
				var form_action = jQuery('form#'+$formid).attr("action");
				var $return_url = jQuery("input[name=return]").val();
				if($return_url!='')
				{
					var new_return_url = $return_url+"&registrant="+data.registrant+"&reg=2";
					jQuery("input[name=return]").val(new_return_url);
					jQuery('.ticket-cost').html(data.cost);
					//jQuery('#dy-event-location').html(jQuery(".venue-title-reg").text());
				}
				if(form_action=='')
				{
					jQuery('#event_register').modal('hide');
					jQuery('#ticketModal').modal('show');
					jQuery('.ticket-id').html(data.regid);
					jQuery('.registerant-info').html(data.reguser);
					jQuery('.ticket-cost').html(data.cost);
					jQuery('#dy-event-date').html($event_date.val());
					jQuery('#dy-event-time').html($exhibition_time);
					//jQuery('#dy-event-location').html(jQuery(".venue-title-reg").text());
				}
			},
			complete: function() {
			}
	
	 	});
		if(form_action=='')
		{
			isValid = false;
		}
		else
		{
			isValid = true;
		}
   	}
	if (isValid == false) {	e.preventDefault(); }
});
});