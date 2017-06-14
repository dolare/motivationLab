(function() {
	tinymce.create('tinymce.plugins.imicframework_shortcodes', {
		init : function(ed, url) {
			ed.addCommand('imicframework_shortcodes', function() {
				ed.windowManager.open({
					file : url + '/imic-interface.php',
					width : 500 + ed.getLang('imicframework_shortcodes.delta_width', 0),
					height : 600 + ed.getLang('imicframework_shortcodes.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url
				});
			});
			
			ed.addButton('imicframework_shortcodes', {
				title : 'IMIC Framework Shortcodes',
				cmd : 'imicframework_shortcodes',
				image : url + '/imic-advance-button.jpg'
			});
			
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('imicframework_shortcodes', n.nodeName == 'IMG');
			});
			
		},
		
		createControl : function(n, cm) {
			return null;
		},
		
		getInfo : function() {
			return {
					longname  : 'imicframework_shortcodes',
					author 	  : 'IMICREATION',
					version   : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('imicframework_shortcodes', tinymce.plugins.imicframework_shortcodes);
})();