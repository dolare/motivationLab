function embedSelectedShortcode() {
        var shortcodeHTML;
	var shortcode_panel = document.getElementById('shortcode_panel');
	var current_shortcode = shortcode_panel.className.indexOf('current');
	if (current_shortcode != -1) {
		
		// SHORTCODE SELECT
		var shortcode_select = document.getElementById('shortcode-select').value;
		
		/////////////////////////////////////////
		////	SHORTCODE OPTION VARIABLES
		/////////////////////////////////////////
		
		// Button
		var button_type = document.getElementById('button-type').value;
		var button_colour = document.getElementById('button-colour').value;
		var button_text = document.getElementById('button-text').value;
		var button_url = document.getElementById('button-url').value;
		var button_extraclass = document.getElementById('button-extraclass').value;
		var button_size = document.getElementById('button-size').value;
		var button_target = "";
			
		if (document.getElementById('button-target').checked) {
			button_target = "_blank";
		} else {
			button_target = "_self";
		}
		
		// Icons
		var icon_image = document.getElementById('icon-image').value;
		// Icons Box
		var icon_box_image = document.getElementById('icon-box-image').value;
		var line_icon_box_image = document.getElementById('line-icon-box-image').value;
		var icon_title = document.getElementById('icon-title').value;
		var icon_description = document.getElementById('icon-description').value;
		var icon_link = document.getElementById('icon-link').value;
		var icon_type = document.getElementById('icon-type').value;
		var icon_shade = document.getElementById('icon-shade').value;
		var icon_outline = document.getElementById('icon-outline').value;
		var icon_effect = document.getElementById('icon-effect').value;
		var icon_box = document.getElementById('icon-box').value;
		//Google Map
		var map_address = document.getElementById('map-address').value;
		
		//Sidebar
		var sidebar_listing = document.getElementById('sidebar-listing').value;
		var sidebar_column = document.getElementById('sidebar-column').value;
		
		// Typography
		var typography_type = document.getElementById('typography-type').value;
		
		// Video
		var video_url = document.getElementById('video-url').value;
		var video_width = document.getElementById('video-width').value;
		var video_height = document.getElementById('video-height').value;
		var video_full_width = document.getElementById('video-full').value;
		
		// Anchor Tags
		var anchor_href = document.getElementById('anchor-href').value;
		var anchor_xclass = document.getElementById('anchor-xclass').value;
		
		// Paragraph Tags
		var paragraph_xclass = document.getElementById('paragraph-xclass').value;
		
		// Span Tags
		var span_xclass = document.getElementById('span-xclass').value;
		
		//Div Tags
		div_xclass = document.getElementById('div-xclass').value;	
		
		//Spacer Tags
		spacer_class = document.getElementById('spacer-size').value;	
		spacer_xclass = document.getElementById('spacer-xclass').value;	
		
		//Section Tags
		section_xclass = document.getElementById('section-xclass').value;		
		
		// Heading Tags
		var heading_size = document.getElementById('heading-size').value;
		var heading_extra = document.getElementById('heading-extra').value;
		var heading_icon = document.getElementById('heading-icon').value;
		var heading_type = document.getElementById('heading-type').value;
		
		// Container Tags
		var container_xclass = document.getElementById('container-xclass').value;
		
		// Divider Tags
		var divider_extra = document.getElementById('divider-extra').value;
		
		// Alert Box Tags
		var alert_type = document.getElementById('alert-type').value;
		var alert_close = document.getElementById('alert-close').checked;
			
		
		
		// Blockquote Box Tags
		var blockquote_name = document.getElementById('blockquote-name').value;	
		
		// Dropcap Box Tags
		var dropcap_type = document.getElementById('dropcap-type').value;
		
		// Code Box Tags
		var code_type = document.getElementById('code-type').value;				
		
		// Label Tags
		var label_type = document.getElementById('label-type').value;
		
		// Columns
		var column_options = document.getElementById('column-options').value;
		var column_xclass = document.getElementById('column-xclass').value;
		var column_animation = document.getElementById('column-animation').value;
		
		// Counters
		var count_to = document.getElementById('count-to').value;
		var count_subject = document.getElementById('count-subject').value;
		var count_speed = document.getElementById('count-speed').value;
		var count_image = document.getElementById('count-image').value;
		var count_textstyle = document.getElementById('count-textstyle').value;
			
		// Progress Bar
		var progressbar_percentage = document.getElementById('progressbar-percentage').value;
		var progressbar_text = document.getElementById('progressbar-text').value;
		var progressbar_value = document.getElementById('progressbar-value').value;
		var progressbar_type = document.getElementById('progressbar-type').value;
		var progressbar_colour = document.getElementById('progressbar-colour').value;
		
		
		// Tooltip
		var tooltip_text = document.getElementById('tooltip-text').value;
		var tooltip_link = document.getElementById('tooltip-link').value;
		var tooltip_direction = document.getElementById('tooltip-direction').value;
		
		// Tabs Tags
		var tabs_size = document.getElementById('tabs-size').value;
		var tabs_id = document.getElementById('tabs-id').value;
		
		// Accordion Tags
		var accordion_size = document.getElementById('accordion-size').value;
		var accordion_id = document.getElementById('accordion-id').value;	
		
		// Toggle Tags
		var toggle_size = document.getElementById('toggle-size').value;
		var toggle_id = document.getElementById('toggle-id').value;		
		
		// Table
		var table_type = document.getElementById('table-type').value;
		var table_head = document.getElementById('table-head').value;
		var table_columns = document.getElementById('table-columns').value;
		var table_rows = document.getElementById('table-rows').value;
		
		// Lists
		var list_type = document.getElementById('list-type').value;
		var list_icon = document.getElementById('list-icon').value;
		var list_items = document.getElementById('list-items').value;
		var list_extra = document.getElementById('list-extra').value;
				
		// Modal Box
		var modal_id = document.getElementById('modal-id').value;
		var modal_title = document.getElementById('modal-title').value;
		var modal_text = document.getElementById('modal-text').value;
		var modal_button = document.getElementById('modal-button').value;	
		/////////////////////////////////////////
                 // Form Email
		//var form_email = document.getElementById('form_email').value;	
		var form_title = document.getElementById('form-title').value;	
		
		
		/////////////////////////////////////////
		////	VIDEO SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-video') {
			shortcodeHTML = '[video url="'+video_url+'" width="'+video_width+'" height="'+video_height+'" full="'+video_full_width+'"]';	
		}
		/////////////////////////////////////////
		////	CALENDAR SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-calendar') {
			shortcodeHTML = '[event_calendar]';	
		}
		/////////////////////////////////////////
		////	BUTTON SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-buttons') {
			shortcodeHTML = '[imic_button colour="'+button_colour+'" type="'+button_type+'" link="'+button_url+'" target="'+button_target+'" extraclass="'+button_extraclass+'" size="'+button_size+'"]'+button_text+'[/imic_button]';	
		}
               /////////////////////////////////////////
		////	FORM SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-form') {
			shortcodeHTML = '[imic_form form_title="'+form_title+'"]';	
		}
		
		/////////////////////////////////////////
		////	ICON SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-icons') {
			shortcodeHTML = '[icon image="'+icon_image+'"]';	
		}
		/////////////////////////////////////////
		////	ICON BOX SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-icons-box') {
			shortcodeHTML = '[icon_box icon_image="'+icon_box_image+'" line_icon="'+line_icon_box_image+'" title="'+icon_title+'" description="'+icon_description+'" link="'+icon_link+'" type="'+icon_type+'" shade="'+icon_shade+'" effect="'+icon_effect+'" box="'+icon_box+'" outline="'+icon_outline+'"]';	
		}
		
		/////////////////////////////////////////
		////	SIDEBAR SHORTCODE OUTPUT
		/////////////////////////////////////////
				
		if (shortcode_select == 'shortcode-sidebar') {
		
			shortcodeHTML = '<br/>[sidebar id="' + sidebar_listing + '" column="'+sidebar_column+'"]<br/>';
		
		}
		
		/////////////////////////////////////////
		////	TYPOGRAPHY SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-typography') {
			switch (typography_type){
				case 'typo-anchor':	shortcodeHTML = '[anchor href="'+anchor_href+'" extra="'+anchor_xclass+'"][/anchor]'; break;
				case 'typo-address':	shortcodeHTML = '[address][/address]'; break;
				case 'typo-paragraph':	shortcodeHTML = '[paragraph extra="'+paragraph_xclass+'"][/paragraph]'; break;
				case 'typo-thematic-break':	shortcodeHTML = '[thematic_break]'; break;
				case 'typo-divider':	shortcodeHTML = '[divider extra="'+divider_extra+'"]'; break;
				case 'typo-heading':	shortcodeHTML = '[heading icon="'+heading_icon+'" type="'+heading_type+'" size="'+heading_size+'" extra="'+heading_extra+'"][/heading]'; break;
				case 'typo-alert':	shortcodeHTML = '[alert type="'+alert_type+'" close="'+alert_close+'"][/alert]'; break;
				case 'typo-blockquote':	shortcodeHTML = '[blockquote name="'+blockquote_name+'"][/blockquote]'; break;
				case 'typo-dropcap':	shortcodeHTML = '[dropcap type="'+dropcap_type+'"][/dropcap]'; break;
				case 'typo-code':	shortcodeHTML = '[code type="'+code_type+'"][/code]'; break;
				case 'typo-label':	shortcodeHTML = '[label type="'+label_type+'"][/label]'; break;
				case 'typo-container':	shortcodeHTML = '[container extra="'+container_xclass+'"][/container]'; break;
				//case 'typo-spacer':	shortcodeHTML = '[spacer size="'+spacer_size+'"]'; break;
				case 'typo-span':	shortcodeHTML = '[span extra="'+span_xclass+'"][/span]'; break;
				case 'typo-strong':	shortcodeHTML = '[strong][/strong]'; break;
				case 'typo-section':	shortcodeHTML = '[section extra="'+section_xclass+'"][/section]'; break;
				case 'typo-div':	shortcodeHTML = '[div extra="'+div_xclass+'"][/div]'; break;
				case 'typo-spacer':	shortcodeHTML = '[spacer size="'+spacer_class+'" extra="'+spacer_xclass+'"][/spacer]'; break;
			}	
		}
		
		/////////////////////////////////////////
		////	GOOGLE MAP SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-gmap') {
			shortcodeHTML = '[gmap address="'+map_address+'"]';	
		}
		
		/////////////////////////////////////////
		////	COLUMNS SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-columns' && column_options == 'one_full') {
			shortcodeHTML = '[one_full extra="'+ column_xclass +'" anim="'+column_animation+'"]1 Text[/one_full]';	
		}
		if (shortcode_select == 'shortcode-columns' && column_options == 'two_halves') {
			shortcodeHTML = '[one_half extra="'+ column_xclass +'" anim="'+column_animation+'"]1/2 Text[/one_half][one_half extra="'+ column_xclass +'" anim="'+column_animation+'"]1/2 Text[/one_half]';	
		}
		if (shortcode_select == 'shortcode-columns' && column_options == 'three_thirds') {
			shortcodeHTML = '[one_third extra="'+ column_xclass +'" anim="'+column_animation+'"]1/3 Text[/one_third][one_third extra="'+ column_xclass +'" anim="'+column_animation+'"]1/3 Text[/one_third][one_third extra="'+ column_xclass +'" anim="'+column_animation+'"]1/3 Text[/one_third]';	
		}
		if (shortcode_select == 'shortcode-columns' && column_options == 'four_quarters') {
			shortcodeHTML = '[one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth][one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth][one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth][one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth]';	
		}
		if (shortcode_select == 'shortcode-columns' && column_options == 'six_one_sixths') {
			shortcodeHTML = '[one_sixth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/6 Text[/one_sixth][one_sixth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/6 Text[/one_sixth][one_sixth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/6 Text[/one_sixth][one_sixth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/6 Text[/one_sixth][one_sixth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/6 Text[/one_sixth][one_sixth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/6 Text[/one_sixth]';	
		}
               
                if (shortcode_select == 'shortcode-columns' && column_options == 'one_halves_two_quarters') {
			shortcodeHTML = '[one_half extra="'+ column_xclass +'" anim="'+column_animation+'"]1/2 Text[/one_half][one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth][one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth]';	
		}
                if (shortcode_select == 'shortcode-columns' && column_options == 'three_two_thirds') {
			shortcodeHTML = '[one_third extra="'+ column_xclass +'" anim="'+column_animation+'"]1/3 Text[/one_third][two_third extra="'+ column_xclass +'" anim="'+column_animation+'"]2/3 Text[/two_third]';	
		}
                if (shortcode_select == 'shortcode-columns' && column_options == 'two_thirds_one_thirds') {
			shortcodeHTML = '[two_third extra="'+ column_xclass +'" anim="'+column_animation+'"]2/3 Text[/two_third][one_third extra="'+ column_xclass +'" anim="'+column_animation+'"]1/3 Text[/one_third]';	
		}
                if (shortcode_select == 'shortcode-columns' && column_options == 'two_quarters_one_halves') {
			shortcodeHTML = '[one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth][one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth][one_half extra="'+ column_xclass +'" anim="'+column_animation+'"]1/2 Text[/one_half]';	
		}
                if (shortcode_select == 'shortcode-columns' && column_options == 'one_quarters_one_halves_one_quarters') {
			shortcodeHTML = '[one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth][one_half extra="'+ column_xclass +'" anim="'+column_animation+'"]1/2 Text[/one_half][one_fourth extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/one_fourth]';	
		}
		if (shortcode_select == 'shortcode-columns' && column_options == '') {
			shortcodeHTML = '[custom extra="'+ column_xclass +'" anim="'+column_animation+'"]1/4 Text[/custom]';	
		}
			
			/////////////////////////////////////////
		////	COUNTERS SHORTCODE OUTPUT
		/////////////////////////////////////////
				
		if (shortcode_select == 'shortcode-counters') {
		
			shortcodeHTML = '[imic_count to="' + count_to + '" speed="' + count_speed + '" icon="' + count_image + '" textstyle="' + count_textstyle + '" subject="' + count_subject + '"]';
		
		}
				
		/////////////////////////////////////////
		////	MODAL BOX SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-modal') {
			shortcodeHTML = '[modal_box id="'+modal_id+'" title="'+modal_title+'" text="'+modal_text+'" button="'+modal_button+'"]';	
		}
		
		
		/////////////////////////////////////////
		////	PROGRESS BAR SHORTCODE OUTPUT
		/////////////////////////////////////////
				
		if (shortcode_select == 'shortcode-progressbar') {
		
			shortcodeHTML = '[progress_bar percentage="' + progressbar_percentage + '" name="' + progressbar_text + '" value="' + progressbar_value + '" type="' + progressbar_type + '" colour="' + progressbar_colour + '"]';
		
		}
		
		
		/////////////////////////////////////////
		////	TOOLTIP SHORTCODE OUTPUT
		/////////////////////////////////////////
				
		if (shortcode_select == 'shortcode-tooltip') {
		
			shortcodeHTML = '[imic_tooltip link="' + tooltip_link + '" direction="' + tooltip_direction + '" title="'+ tooltip_text +'"]TEXT HERE[/imic_tooltip]';
		
		}
		
		
		
		/////////////////////////////////////////
		////	TABLE SHORTCODE OUTPUT
		/////////////////////////////////////////
	
		if (shortcode_select == 'shortcode-tables') {
			
			shortcodeHTML = '[htable type="' + table_type + '"]';
			
			if (table_head == "yes") {
				shortcodeHTML += '[thead][trow]';
				for ( var hc = 0; hc < table_columns; hc++ ) {
					shortcodeHTML += '[thcol]HEAD COL ' + parseInt(hc + 1) + '[/thcol]';
				}
				shortcodeHTML += '[/trow][/thead]';
			}
			shortcodeHTML += '[tbody]';
			
			for ( var r = 0; r < table_rows; r++ ) {
				shortcodeHTML += '[trow]';
				for ( var nc = 0; nc < table_columns; nc++ ) {
					shortcodeHTML += '[tcol]ROW ' + parseInt(r + 1) + ' COL ' + parseInt(nc + 1) + '[/tcol]';
				} 
				shortcodeHTML += '[/trow]';
			}
			
			shortcodeHTML += '[/tbody]';
			
			shortcodeHTML += '[/htable]';
		}
		
		
		/////////////////////////////////////////
		////	LIST SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-lists') {
			shortcodeHTML = '[list type='+ list_type +' extra='+ list_extra +']';
			
			for ( var li = 0; li < list_items; li++ ) {
				if((list_type == 'icon')||(list_type == 'inline')){
					shortcodeHTML += '[list_item icon="'+ list_icon +'" type="'+ list_type +'"]Item text '+ parseInt(li + 1) +'[/list_item]';
				}else if(list_type == 'desc'){
					shortcodeHTML += '[list_item_dt]Item text '+ parseInt(li + 1) +'[/list_item_dt][list_item_dd]Item text '+ parseInt(li + 1) +'[/list_item_dd]';
				}else{
					shortcodeHTML += '[list_item]Item text '+ parseInt(li + 1) +'[/list_item]';			
				}
			}
			
			shortcodeHTML += '[/list]';	
		}
		/////////////////////////////////////////
		////	ACCORDION SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-accordion') {
			
			shortcodeHTML = '[accordions id="'+ accordion_id +'"]';
			
			index = 0;
			for ( var hc = 0; hc < accordion_size; hc++ ) {
				if(index==0){ accordionClass='active'; accordionIn='in'; }else{ accordionClass=''; accordionIn='';}
				
				shortcodeHTML += '[accgroup]';
				shortcodeHTML += '[acchead id="'+ accordion_id + '" tab_id="'+ accordion_id + hc +'" class="'+ accordionClass +'"]Accordion Panel #' + parseInt(hc + 1) + '[/acchead]';
				shortcodeHTML += '[accbody tab_id="'+ accordion_id + hc +'" in="'+ accordionIn +'"]Accordion Body #' + parseInt(hc + 1) + '[/accbody]';
				shortcodeHTML += '[/accgroup]';
				index++;
			}
			
			shortcodeHTML += '[/accordions]';
		}
		
		/////////////////////////////////////////
		////	TOGGLE SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-toggle') {
			
			shortcodeHTML = '[toggles id="'+ toggle_id +'"]';
			
			for ( var hc = 0; hc < toggle_size; hc++ ) {
				shortcodeHTML += '[togglegroup]';
				shortcodeHTML += '[togglehead id="'+ toggle_id + '" tab_id="'+ toggle_id + hc +'"]Toggle Panel #' + parseInt(hc + 1) + '[/togglehead]';
				shortcodeHTML += '[togglebody tab_id="'+ toggle_id + hc +'"]Toggle Body #' + parseInt(hc + 1) + '[/togglebody]';
				shortcodeHTML += '[/togglegroup]';
			}
			
			shortcodeHTML += '[/toggles]';
		}
		/////////////////////////////////////////
		////	TABS SHORTCODE OUTPUT
		/////////////////////////////////////////
		if (shortcode_select == 'shortcode-tabs') {
			shortcodeHTML = '[tabs]';
			
			shortcodeHTML += '[tabh]';
			index = 0;
			for ( var hc = 0; hc < tabs_size; hc++ ) {
				if(index==0){ tabClass='active'; }else{ tabClass=''; }
				shortcodeHTML += '[tab id="'+ tabs_id + hc +'" class="'+ tabClass +'"]TAB HEAD ' + parseInt(hc + 1) + '[/tab]';
				index++;
			}
			shortcodeHTML += '[/tabh]';
			
			shortcodeHTML += '[tabc]';
			flag = 0;
			for ( var r = 0; r < tabs_size; r++ ) {
				if(flag==0){ tabCClass='active'; }else{ tabCClass=''; }
				shortcodeHTML += '[tabrow id="'+ tabs_id + r +'" class="'+ tabCClass +'"]TAB CONTENT'+ parseInt(r + 1) +'[/tabrow]';
				flag++;
			}
			shortcodeHTML += '[/tabc]';
			
			shortcodeHTML += '[/tabs]';
		}
	}
	
		
	/////////////////////////////////////////
	////	TinyMCE Callback & Embed
	/////////////////////////////////////////
	var tmce_ver=window.tinyMCE.majorVersion;
	if (current_shortcode != -1) {
		activeEditor = window.tinyMCE.activeEditor.id;
		if (tmce_ver >= 4) {
		parent.tinyMCE.execCommand('mceInsertContent', 
false,shortcodeHTML);
		parent.tinyMCE.activeEditor.windowManager.close(); }
		else {
		window.tinyMCE.execInstanceCommand(activeEditor, 'mceInsertContent', false, shortcodeHTML);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		}
       tinyMCEPopup.close();   
	} else {
		tinyMCEPopup.close();		
	}
	return;
}