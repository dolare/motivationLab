jQuery( document ).ready(function() {
	if(event_payment.name=="1")
	{
		jQuery('#event_register').modal('show');
	}
	else if(event_payment.name=="2")
	{
		jQuery('#event_register_thanks').modal('show');
	}
	jQuery("#find-ticket").click(function(){
		jQuery('#event_register_thanks').modal('hide');
		jQuery('#ticketModal').modal('show');
		return false;
	});
});