/* ==================================================
IMIC Framework Shortcode Panel jQuery Function
================================================== */
/////////////////////////////////////////////
// jQuery Show/Hide Option Functions
/////////////////////////////////////////////
// jQuery No Conflict
var $j = jQuery.noConflict();
// Shortcodes Function
(function($j) {
$j(document).ready(function() {
  
	// Setup the array of shortcode options
	$j.shortcode_select = {
		'0' : $j([]),
		'shortcode-gmap' : $j('#shortcode-gmap'),
		'shortcode-buttons' : $j('#shortcode-buttons'),
		'shortcode-icons' : $j('#shortcode-icons'),
		'shortcode-icons-box' : $j('#shortcode-icons-box'),
		'shortcode-icons-box' : $j('#shortcode-icons-box'),
		'shortcode-typography' : $j('#shortcode-typography'),
		'shortcode-tooltip' : $j('#shortcode-tooltip'),
		'shortcode-calendar' : $j('#shortcode-calendar'),
		'shortcode-columns' : $j('#shortcode-columns'),
		'shortcode-counters' : $j('#shortcode-counters'),
		'shortcode-progressbar' : $j('#shortcode-progressbar'),
		'shortcode-counters' : $j('#shortcode-counters'),
		'shortcode-sidebar' : $j('#shortcode-sidebar'),
		'shortcode-tables' : $j('#shortcode-tables'),
		'shortcode-pricing-table' : $j('#shortcode-pricing-table'),
		'shortcode-lists' : $j('#shortcode-lists'),
		'shortcode-tabs' : $j('#shortcode-tabs'),
		'shortcode-accordion' : $j('#shortcode-accordion'),
		'shortcode-toggle' : $j('#shortcode-toggle'),
		'shortcode-modal' : $j('#shortcode-modal'),
		'shortcode-form' : $j('#shortcode-form'),
		'shortcode-video' : $j('#shortcode-video'),
                };
	
	$j.typo_select = {
		'0' : $j([]),
		'typo-anchor' : $j('#typo-anchor'),
		'typo-address' : $j('#typo-address'),
		'typo-heading' : $j('#typo-heading'),
		'typo-alert' : $j('#typo-alert'),
		'typo-blockquote' : $j('#typo-blockquote'),
		'typo-dropcap' : $j('#typo-dropcap'),
		'typo-code' : $j('#typo-code'),
		'typo-label' : $j('#typo-label'),
		'typo-paragraph' : $j('#typo-paragraph'),
		'typo-divider' : $j('#typo-divider'),
		'typo-container' : $j('#typo-container'),
		'typo-span' : $j('#typo-span'),
		'typo-section' : $j('#typo-section'),
		'typo-div' : $j('#typo-div'),
		'typo-spacer' : $j('#typo-spacer'),
	};
	// Hide each option section
	$j.each($j.shortcode_select, function() {
		this.css({ display: 'none' });
	});
	
	// Show the selected option section
	$j('#shortcode-select').change(function() {
		$j.each($j.shortcode_select, function() {
			this.css({ display: 'none' });
		});
		$j.shortcode_select[$j(this).val()].css({
			display: 'block'
		});
	});
	
	// Hide each typography option section
	$j.each($j.typo_select, function() {
		this.css({ display: 'none' });
	});
	
	// Show the selected typography option section
	$j('#typography-type').change(function() {
		$j.each($j.typo_select, function() {
			this.css({ display: 'none' });
		});
		$j.typo_select[$j(this).val()].css({
			display: 'block'
		});
	});
	
    $j('.font-icon-grid').on('click', 'li', function() {
    	var selection = $j(this),
    		iconName = selection.find('i').attr('class');
    	    		
    	$j('.font-icon-grid li').removeClass('selected');
    	selection.addClass('selected');
    	selection.parent().parent().find('input').val(iconName);
    });
    
});
})($j);
/////////////////////////////////////////////
// Embed Shortcode Function
/////////////////////////////////////////////