jQuery(document).ready(function() {
	if (calenderEvents.calheadview == 1) {
		HeadLeft = "title";
		HeadCenter = "";
		HeadRight = "prev,next today";
	} else if (calenderEvents.calheadview == 2) {
		HeadLeft = "prev,next today";
		HeadCenter = "title";
		HeadRight = "month,agendaWeek,agendaDay";
	} 
	Limit = parseInt(calenderEvents.eventLimit);
    jQuery('.calendar').prepend('<div id="loading-image"><img id="loading-image-img" src="' + calenderEvents.homeurl + '/images/loader-new.gif" alt="Loading..." /></div>');
      jQuery('.calendar').fullCalendar({
        
        monthNames: calenderEvents.monthNames,
        monthNamesShort: calenderEvents.monthNamesShort,
        dayNames: calenderEvents.dayNames,
        dayNamesShort: calenderEvents.dayNamesShort,
        editable: false,
		header: {left: HeadLeft,
				center: HeadCenter,
				right:  HeadRight
				},
		buttonText: {
			today: calenderEvents.today,
			month: calenderEvents.month,
			week: calenderEvents.week,
			day: calenderEvents.day
			},
		eventLimit: Limit, // for all non-agenda views
		height: 'auto',
		fixedWeekCount: false,
		defaultView: calenderEvents.view,
		viewRender: function (view, element) {
		var b = jQuery('.calendar').fullCalendar('getDate');
		this_month = b.format('YYYY-MM-01');
		jQuery('.calendar').fullCalendar('removeEventSource', calenderEvents.homeurl + '/includes/json-feed.php'); 
		jQuery('.calendar').fullCalendar('refetchEvents');
		jQuery('.calendar').fullCalendar('addEventSource', { url: calenderEvents.homeurl + '/includes/json-feed.php',
						type: 'POST',
						data: {
						   event_cat_id: jQuery('.event_calendar').attr('id'),
						   month_event: this_month,
						  }})
		jQuery('.calendar').fullCalendar('refetchEvents');
	 	},
		googleCalendarApiKey: calenderEvents.googlekey,
			eventSources: [
				{
					googleCalendarId:calenderEvents.googlecalid
					
				},
				],
        timeFormat: calenderEvents.time_format,
        firstDay:calenderEvents.start_of_week,
        loading: function(bool) {
            if (bool)
                jQuery('#loading-image').show();
            else
                jQuery('#loading-image').hide();
        },
    });
});