jQuery(function($){
	"use strict";

var BORNTOGIVE = window.BORNTOGIVE || {};


BORNTOGIVE.megaMenu = function() {
        jQuery('.megamenu-sub-title').closest('ul.sub-menu').wrapInner('<div class="row" />').wrapInner('<div class ="megamenu-container container" />').wrapInner('<li />');
        jQuery('.megamenu-container').closest('li.menu-item-has-children').addClass('megamenu');
        var $class = '';
		jQuery(".megamenu-container").each(function(index, elem) {
    var numImages = $(this).find('.row').children().length;
	switch (numImages)
        {
            case 1:
                $class = 12;
                break;
            case 2:
                $class = 6;
                break;
            case 3:
                $class = 4;
                break;
            case 4:
                $class = 3;
                break;
            default:
                $class = 2;
        }
		$(this).find('.row').find('.col-md-3').each(function() {
            jQuery(this).removeClass('col-md-3').addClass('col-md-' + $class);
        })
		//$('.megamenu-container .row .div:has(.col-md-3)').addClass('col-md-' + $class).removeClass('col-md-3');
		//jQuery(this).find('.row').children().addClass('col-md-' + $class).removeClass('col-md-3');
    // do whatever processing you wanted to with numImages here
});
}

/* ==================================================
	Contact Form Validations
================================================== */
	BORNTOGIVE.ContactForm = function(){
		$('.contact-form').each(function(){
			var formInstance = $(this);
			formInstance.submit(function(){
		
			var action = $(this).attr('action');
		
			$("#message").slideUp(750,function() {
			$('#message').hide();
		
			$('#submit')
				.after('<img src="images/assets/ajax-loader.gif" class="loader" />')
				.attr('disabled','disabled');
		
			$.post(action, {
				fname: $('#fname').val(),
				lname: $('#lname').val(),
				email: $('#email').val(),
				phone: $('#phone').val(),
				comments: $('#comments').val()
			},
				function(data){
					document.getElementById('message').innerHTML = data;
					$('#message').slideDown('slow');
					$('.contact-form img.loader').fadeOut('slow',function(){$(this).remove()});
					$('#submit').removeAttr('disabled');
					if(data.match('success') != null) $('.contact-form').slideUp('slow');
		
				}
			);
			});
			return false;
		});
		});
	}
/* ==================================================
	Scroll Functions
================================================== */
	BORNTOGIVE.scrollToTop = function(){
			var windowWidth = $(window).width(),
			didScroll = false;
	
		var $arrow = $('#back-to-top');
		var $header = $('.site-header');
	
		$arrow.on('click',function(e) {
			$('body,html').animate({ scrollTop: "0" }, 750, 'easeOutExpo' );
			e.preventDefault();
		})
	
		$(window).scroll(function() {
			didScroll = true;
		});
	
		setInterval(function() {
			if( didScroll ) {
				didScroll = false;
	
				if( $(window).scrollTop() > 200 ) {
					$arrow.css("right",10);
				} else {
					$arrow.css("right","-40px");
				}
				
				
				if( $(window).scrollTop() > 90 ) {
					$header.addClass("sticky");
				} else {
					$header.removeClass("sticky");
				}
			}
		}, 250);
	}
/* ==================================================
   Accordion
================================================== */
	BORNTOGIVE.accordion = function(){
		var accordion_trigger = $('.accordion-heading.accordionize');
		
		accordion_trigger.delegate('.accordion-toggle','click', function(event){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$(this).addClass('inactive');
			}
			else{
				accordion_trigger.find('.active').addClass('inactive');          
				accordion_trigger.find('.active').removeClass('active');   
				$(this).removeClass('inactive');
				$(this).addClass('active');
			}
			event.preventDefault();
		});
	}
/* ==================================================
   Toggle
================================================== */
	BORNTOGIVE.toggle = function(){
		var accordion_trigger_toggle = $('.accordion-heading.togglize');
		
		accordion_trigger_toggle.delegate('.accordion-toggle','click', function(event){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$(this).addClass('inactive');
			}
			else{
				$(this).removeClass('inactive');
				$(this).addClass('active');
			}
			event.preventDefault();
		});
	}
/* ==================================================
   Tooltip
================================================== */
	BORNTOGIVE.toolTip = function(){ 
		$('a[data-toggle=tooltip]').tooltip(); 
		$('a[data-toggle=tooltip]').tooltip();
		$('a[data-toggle=popover]').popover({html:true}).on("click", function(e) { 
       		e.preventDefault(); 
       		$(this).focus(); 
		});
	}
/* ==================================================
   Twitter Widget
================================================== */
	BORNTOGIVE.TwitterWidget = function() {
		$('.twitter-widget').each(function(){
			var twitterInstance = $(this); 
			var twitterTweets = twitterInstance.attr("data-tweets-count") ? twitterInstance.attr("data-tweets-count") : "1"
			twitterInstance.twittie({
            	dateFormat: '%b. %d, %Y',
            	template: '<li><i class="fa fa-twitter"></i> {{tweet}} <span class="tweet-date">{{date}}</span></li>',
            	count: twitterTweets,
            	hideReplies: true
        	});
		});
	}
/* ==================================================
   Hero Flex Slider
================================================== */
	BORNTOGIVE.heroflex = function() {
		$('.heroflex').each(function(){
				var carouselInstance = $(this); 
				var carouselAutoplay = carouselInstance.attr("data-autoplay") == 'yes' ? true : false
				var carouselPagination = carouselInstance.attr("data-pagination") == 'yes' ? true : false
				var carouselArrows = carouselInstance.attr("data-arrows") == 'yes' ? true : false
				var carouselDirection = carouselInstance.attr("data-direction") ? carouselInstance.attr("data-direction") : "horizontal"
				var carouselStyle = carouselInstance.attr("data-style") ? carouselInstance.attr("data-style") : "fade"
				var carouselSpeed = carouselInstance.attr("data-speed") ? carouselInstance.attr("data-speed") : "5000"
				var carouselPause = carouselInstance.attr("data-pause") == 'yes' ? true : false
				
				carouselInstance.flexslider({
					animation: carouselStyle,
					easing: "swing",
					direction: carouselDirection,
					slideshow: carouselAutoplay,
					slideshowSpeed: carouselSpeed,
					animationSpeed: 600,
					initDelay: 0,
					randomize: false,
					pauseOnHover: carouselPause,
					controlNav: carouselPagination,
					directionNav: carouselArrows,
					prevText: "",
					nextText: ""
				});
		});
	}
/* ==================================================
   Flex Slider
================================================== */
	BORNTOGIVE.galleryflex = function() {
		$('.galleryflex').each(function(){
				var carouselInstance = $(this); 
				var carouselAutoplay = carouselInstance.attr("data-autoplay") == 'yes' ? true : false
				var carouselPagination = carouselInstance.attr("data-pagination") == 'yes' ? true : false
				var carouselArrows = carouselInstance.attr("data-arrows") == 'yes' ? true : false
				var carouselDirection = carouselInstance.attr("data-direction") ? carouselInstance.attr("data-direction") : "horizontal"
				var carouselStyle = carouselInstance.attr("data-style") ? carouselInstance.attr("data-style") : "fade"
				var carouselSpeed = carouselInstance.attr("data-speed") ? carouselInstance.attr("data-speed") : "5000"
				var carouselPause = carouselInstance.attr("data-pause") == 'yes' ? true : false
				
				carouselInstance.flexslider({
					animation: carouselStyle,
					easing: "swing",
					direction: carouselDirection,
					slideshow: carouselAutoplay,
					slideshowSpeed: carouselSpeed,
					animationSpeed: 600,
					initDelay: 0,
					animationLoop: false,
					randomize: false,
					pauseOnHover: carouselPause,
					controlNav: carouselPagination,
					directionNav: carouselArrows,
					prevText: "",
					nextText: ""
				});
		});
	}
/* ==================================================
   Owl Carousel
================================================== */
	BORNTOGIVE.OwlCarousel = function() {
		$('.owl-carousel').each(function(){
				var carouselInstance = $(this); 
				var carouselColumns = carouselInstance.attr("data-columns") ? carouselInstance.attr("data-columns") : "1"
				var carouselitemsDesktop = carouselInstance.attr("data-items-desktop") ? carouselInstance.attr("data-items-desktop") : "4"
				var carouselitemsDesktopSmall = carouselInstance.attr("data-items-desktop-small") ? carouselInstance.attr("data-items-desktop-small") : "3"
				var carouselitemsTablet = carouselInstance.attr("data-items-tablet") ? carouselInstance.attr("data-items-tablet") : "2"
				var carouselitemsMobile = carouselInstance.attr("data-items-mobile") ? carouselInstance.attr("data-items-mobile") : "1"
				var carouselAutoplay = carouselInstance.attr("data-autoplay") ? carouselInstance.attr("data-autoplay") : false
				var carouselPagination = carouselInstance.attr("data-pagination") == 'yes' ? true : false
				var carouselArrows = carouselInstance.attr("data-arrows") == 'yes' ? true : false
				var carouselSingle = carouselInstance.attr("data-single-item") == 'yes' ? true : false
				var carouselStyle = carouselInstance.attr("data-style") ? carouselInstance.attr("data-style") : "fade"
				var carouselRTL = carouselInstance.attr("data-rtl") ? carouselInstance.attr("data-rtl") : "ltr"
				
				carouselInstance.owlCarousel({
					items: carouselColumns,
					autoPlay : carouselAutoplay,
					navigation : carouselArrows,
					pagination : carouselPagination,
					itemsDesktop:[1199,carouselitemsDesktop],
					itemsDesktopSmall:[979,carouselitemsDesktopSmall],
					itemsTablet:[768,carouselitemsTablet],
					itemsMobile:[479,carouselitemsMobile],
					singleItem:carouselSingle,
					navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
					stopOnHover: true,
					lazyLoad: true,
					direction: carouselRTL,
					transitionStyle: carouselStyle
				});
		});
	}
/* ==================================================
   Magnific Popup
================================================== */
	BORNTOGIVE.Magnific = function() {
		jQuery('.format-gallery').each(function(){
			$(this).magnificPopup({
				delegate: 'a.popup-image', // child items selector, by clicking on it popup will open
				type: 'image',
				gallery:{enabled:true}
				// other options
			});
		});
		jQuery('.magnific-image').magnificPopup({ 
			type: 'image',
			gallery:{enabled:false}
			// other options
		});
		jQuery('.magnific-video').magnificPopup({ 
			type: 'iframe',
			gallery:{enabled:false}
			// other options
		});
	}
/* ==================================================
   Animated Counters
================================================== */
	BORNTOGIVE.Counters = function() {
		$('.cust-counter').each(function () {
			$(this).appear(function() {
			var counter = $(this).find(".timer .count").html();
			$(this).find(".timer .count").countTo({
				from: 0,
				to: counter,
				speed: 2000,
				refreshInterval: 60
				});
			});
		});
	}
/* ==================================================
   SuperFish menu
================================================== */
	BORNTOGIVE.SuperFish = function() {
		$('.sf-menu').superfish({
			  delay: 200,
			  animation: {opacity:'show', height:'show'},
			  speed: 'fast',
			  cssArrows: false,
			  disableHI: true
		});
		$(".dd-menu > li:has(ul)").find("a:first").append(" <i class='fa fa-caret-down'></i>");
		$(".dd-menu > li > ul > li:has(ul)").find("a:first").append(" <i class='fa fa-caret-right'></i>");
		$(".dd-menu > li > ul > li > ul > li:has(ul)").find("a:first").append(" <i class='fa fa-caret-right'></i>");
	}
/* ==================================================
   Header Functions
================================================== */
	BORNTOGIVE.StickyHeader = function() {
		$(".header-style2 .site-header").sticky();
		$(".header-style3 .fw-menu-wrapper").sticky();
	}
/* ==================================================
	Responsive Nav Menu
================================================== */
	BORNTOGIVE.MobileMenu = function() {
		// Responsive Menu Events
		$('#menu-toggle').on("click", function(){
			$(this).toggleClass("opened");
			$(".dd-menu").slideToggle();
			if( $(window).scrollTop() <= 0 ) {
				$(".site-header").toggleClass("menu-opened");
			}
			return false;
		});
		$(window).resize(function(){
			if($("#menu-toggle").hasClass("opened")){
				$(".dd-menu").css("display","block");
			} else {
				$("#menu-toggle").css("display","none");
			}
		});
	}
/* ==================================================
   IsoTope Portfolio
================================================== */
		BORNTOGIVE.IsoTope = function() {	
		$("ul.sort-source").each(function() {
			var source = $(this);
			var destination = $("ul.sort-destination[data-sort-id=" + $(this).attr("data-sort-id") + "]");
			if(destination.get(0)) {
				$(window).load(function() {
					destination.isotope({
						itemSelector: ".grid-item",
						layoutMode: 'sloppyMasonry'
					});
					source.find("a").on("click", function(e) {
						e.preventDefault();
						var $this = $(this),
							filter = $this.parent().attr("data-option-value");
						source.find("li.active").removeClass("active");
						$this.parent().addClass("active");
						destination.isotope({
							filter: filter
						});
						if(window.location.hash != "" || filter.replace(".","") != "*") {
							self.location = "#" + filter.replace(".","");
						}
						return false;
					});
					$(window).on("hashchange", function(e) {
						var hashFilter = "." + location.hash.replace("#",""),
							hash = (hashFilter == "." || hashFilter == ".*" ? "*" : hashFilter);
						source.find("li.active").removeClass("active");
						source.find("li[data-option-value='" + hash + "']").addClass("active");
						destination.isotope({
							filter: hash
						});
					});
					var hashFilter = "." + (location.hash.replace("#","") || "*");
					var initFilterEl = source.find("li[data-option-value='" + hashFilter + "'] a");
					if(initFilterEl.get(0)) {
						source.find("li[data-option-value='" + hashFilter + "'] a").click();
					} else {
						source.find("li:first-child a").click();
					}
				});
			}
		});
		$(window).load(function() {
			var IsoTopeCont = $(".isotope-grid");
			IsoTopeCont.isotope({
				itemSelector: ".grid-item",
				layoutMode: 'sloppyMasonry'
			});
			if ($(".grid-holder").length > 0){	
				var $container_blog = $('.grid-holder');
				$container_blog.isotope({
					itemSelector : '.grid-item'
				});
				$(window).resize(function() {
					var $container_blog = $('.grid-holder');
					$container_blog.isotope({
						itemSelector : '.grid-item'
					});
				});
			}
		});
	}
/* ==================================================
   Pricing Tables
================================================== */
	var $tallestCol;
	BORNTOGIVE.pricingTable = function(){
		$('.pricing-table').each(function(){
			$tallestCol = 0;
			$(this).find('> div .features').each(function(){
				($(this).height() > $tallestCol) ? $tallestCol = $(this).height() : $tallestCol = $tallestCol;
			});	
			if($tallestCol == 0) $tallestCol = 'auto';
			$(this).find('> div .features').css('height',$tallestCol);
		});
	}
/* ==================================================
   Circle Progress
================================================== */
	BORNTOGIVE.CProgress = function() {
		$('.cProgress').each(function(){
			var cproInstance = $(this); 
			var cprocomplete = cproInstance.attr("data-complete") ? cproInstance.attr("data-complete") : "0.1"
			var cprocolor = cproInstance.attr("data-color") ? cproInstance.attr("data-color") : "d82e67"
			var cprocompleteperc = cprocomplete/100
			cproInstance.circleProgress({
				value: cprocompleteperc,
				size: 60.0,
				emptyFill: 'rgba(0, 0, 0, .1)',
				fill: { color: '#'+cprocolor }
			}).on('circle-animation-progress', function(event, progress) {
				cproInstance.find('strong').html(parseInt(cprocomplete * progress, 10) + '<i>%</i>');
			});
		});
	}
	
/* ==================================================
   Init Functions
================================================== */
$(document).ready(function(){
	BORNTOGIVE.megaMenu();
	BORNTOGIVE.ContactForm();
	BORNTOGIVE.scrollToTop();
	BORNTOGIVE.accordion();
	BORNTOGIVE.toggle();
	BORNTOGIVE.toolTip();
	BORNTOGIVE.TwitterWidget();
	BORNTOGIVE.OwlCarousel();
	BORNTOGIVE.Magnific();
	BORNTOGIVE.SuperFish();
	BORNTOGIVE.Counters();
	BORNTOGIVE.IsoTope();
	BORNTOGIVE.StickyHeader();
	BORNTOGIVE.heroflex();
	BORNTOGIVE.galleryflex();
	BORNTOGIVE.pricingTable();
	BORNTOGIVE.MobileMenu();
	BORNTOGIVE.CProgress();
	$('.selectpicker').selectpicker();
	WWHGetter();
	// apply matchHeight to each item container's items
	$('.content').each(function() {
		$(this).find('.owl-carousel .grid-item').find('.cause-item-container').matchHeight({
			//property: 'min-height'
		});
		$(this).find('.featured-texts').find('.featured-text').matchHeight({
			//property: 'min-height'
		});
		$(this).find('.event-grid-item').find('.media-box').matchHeight({
			//property: 'min-height'
		});
		$(this).find('.event-grid-item').find('.grid-item-content').matchHeight({
			//property: 'min-height'
		});
		$(this).find('.owl-carousel').find('.blog-grid-item .media-box img').matchHeight({
			//property: 'min-height'
		});
	});
	$(".tabbed_widgets").each(function(){
		$(this).find(".tab-content .tab-pane:first").addClass("active");
		$(this).find(".nav-tabs li:first").addClass("active");
	});
	$('.causes-list-item').each(function(){
		var imgh = $(this).find('.media-box').height();
		var imght = parseInt(imgh, 10);
		var imghr = imght/2;
		$(this).find('.cProgress').css("top",imghr);
	});
	// Event page map
	$('.map-toggle-window').hide();
	$('.toggle-event-map').on('click',function(){
		$('.map-toggle-window').slideToggle();
		initGoogleMap();
		return false;
	});
	
});

// DESIGN ELEMENTS //
$(".gallery-updates").parents(".vc_row").prepend('<div class="half-bg-right accent-bg"></div>');

// Centering the dropdown menus
$(".dd-menu li").mouseover(function() {
	 var the_width = $(this).find("a").width();
	 var child_width = $(this).find("ul").width();
	 var width = ((child_width - the_width)/2);
	 $(this).find("ul").css('left', -width);
});

// WINDOW RESIZE FUNCTIONS //
$(window).resize(function(){
	WWHGetter();
});

// Any Button Scroll to section
$('.scrollto').on("click", function(){
	$.scrollTo( this.hash, 800, { easing:'easeOutQuint' });
	return false;
});


// Cart & Search option in header
$(".search-module-trigger").click(function(e){
	e.stopPropagation();
	$(".search-module-opened").toggle();
 	$('.cart-module-opened').hide();
	e.preventDefault();
});
$(".search-module-opened").click(function(e){
	e.stopPropagation();
});
$("#cart-module-trigger").click(function(e){
	e.stopPropagation();
	$(".cart-module-opened").toggle();
 	$('.search-module-opened').hide();
	e.preventDefault();
});
$(".cart-module-opened").click(function(e){
	e.stopPropagation();
});
$(document).click(function(e){
 	$('.search-module-opened, .cart-module-opened').hide();
});

// FITVIDS
$(".fw-video, .post-media").fitVids();

//Donation Modal
$('.predefined-amount input[name=donation-amount]:checked').parent('label').addClass("selected");
$('.predefined-amount input[name=donation-amount]').click(function () {
	$('.predefined-amount input[name=donation-amount]:not(:checked)').parent('label').removeClass("selected");
	$(this).parent('label').addClass("selected");
});

$(window).load(function(){
	$(".format-image").each(function(){
		$(this).find(".media-box").append("<span class='zoom'><span class='icon'><i class='fa fa-search'></i></span></span>");
	});
	$(".format-standard").each(function(){
		$(this).find(".media-box").append("<span class='zoom'><span class='icon'><i class='fa fa-plus'></i></span></span>");
	});
	$(".format-video").each(function(){
		$(this).find(".media-box").append("<span class='zoom'><span class='icon'><i class='fa fa-play'></i></span></span>");
	});
	$(".format-link").each(function(){
		$(this).find(".media-box").append("<span class='zoom'><span class='icon'><i class='fa fa-link'></i></span></span>");
	});
	$(".additional-images .owl-carousel .item-video").each(function(){
		$(this).append("<span class='icon'><i class='fa fa-play'></i></span>");
	});
	$('.carousel-wrapper').css('background','none');
	
});

// Icon Append
$('.basic-link').append(' <i class="fa fa-angle-right"></i>');
$('.basic-link.backward').prepend(' <i class="fa fa-angle-left"></i> ');
$("#comment-submit").wrapAll("<div class=\"row\"><div class=\"form-group\"><div class=\"col-md-12\">");
$("#comment-submit").addClass("btn btn-primary btn-lg");
$('ul.checks li').prepend('<i class="fa fa-check"></i> ');
$('ul.angles li, .widget_categories ul li a, .widget_archive ul li a, .widget_recent_entries ul li a, .widget_links ul li a, .widget_meta ul li a, .widget_charitable_campaign_terms_widget ul li a, .widget_nav_menu ul li a, .widget_charitable_donation_stats_widget ul li, .widget_product_categories ul li a, .widget_pages ul li a').prepend('<i class="fa fa-caret-right"></i> ');
$('.widget_recent_comments ul li').prepend('<i class="fa fa-comment"></i> ');
$('ul.chevrons li').prepend('<i class="fa fa-chevron-right"></i> ');
$('ul.carets li, ul.inline li').prepend('<i class="fa fa-caret-right"></i> ');
$('a.external').prepend('<i class="fa fa-external-link"></i> ');

// Animation Appear
var AppDel;
function AppDelFunction($appd) {
	$appd.addClass("appear-animation");
	if(!$("html").hasClass("no-csstransitions") && $(window).width() > 767) {
		$appd.appear(function() {
			var delay = ($appd.attr("data-appear-animation-delay") ? $appd.attr("data-appear-animation-delay") : 1);
			if(delay > 1) $appd.css("animation-delay", delay + "ms");
			$appd.addClass($appd.attr("data-appear-animation"));
			setTimeout(function() {
				$appd.addClass("appear-animation-visible");
			}, delay);
			clearTimeout();
		}, {accX: 0, accY: -150});
	} else {
		$appd.addClass("appear-animation-visible");
	}
}
function AppDelStopFunction() {
	clearTimeout(AppDel);
}
$("[data-appear-animation]").each(function() {
	var $this = $(this);
	AppDelFunction($this);
	AppDelStopFunction();
});
// Animation Progress Bars

var AppAni;
function AppAniFunction($anim) {
	$anim.appear(function() {
		var delay = ($anim.attr("data-appear-animation-delay") ? $anim.attr("data-appear-animation-delay") : 1);
		if(delay > 1) $anim.css("animation-delay", delay + "ms");
		$anim.addClass($anim.attr("data-appear-animation"));
		setTimeout(function() {
			$anim.animate({
				width: $anim.attr("data-appear-progress-animation")
			}, 1500, "easeOutQuad", function() {
				$anim.find(".progress-bar-tooltip").animate({
					opacity: 1
				}, 500, "easeOutQuad");
			});
		}, delay);
		clearTimeout();
	}, {accX: 0, accY: -50});
}
function AppAniStopFunction() {
	clearTimeout(AppAni);
}
$("[data-appear-progress-animation]").each(function() {
	var $this = $(this);
	AppAniFunction($this);
	AppAniStopFunction();
});

// Parallax Jquery Callings
if(!Modernizr.touch) {
	parallaxInit();
}
function parallaxInit() {
	$('.parallax1').parallax("50%", 0.1);
	$('.parallax2').parallax("50%", 0.1);
	$('.parallax3').parallax("50%", 0.1);
	$('.parallax4').parallax("50%", 0.1);
	$('.parallax5').parallax("50%", 0.1);
	$('.parallax6').parallax("50%", 0.1);
	$('.parallax7').parallax("50%", 0.1);
	$('.parallax8').parallax("50%", 0.1);
	/*add as necessary*/
}

// Window height/Width Getter Classes
function WWHGetter(){
	var wheighter = $(window).height();
	var wwidth = $(window).width();
	$(".wheighter").css("height",wheighter);
	$(".wwidth").css("width",wwidth);
}
});