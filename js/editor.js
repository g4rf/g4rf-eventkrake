(function() {
	// FIXME: automatisieren über Webservice
	var categories = "other,concert,exhibition,theatre,sport,party";
	// FIXME: automatisieren über Webservice
	var groups = "0,brn2012";
	
	tinymce.create('tinymce.plugins.Eventkrake', {
		init : function(ed, url) {
			ed.addButton('eventkrake', {
				title : 'Eventkrake Vorlage',
				image : url + '/icon_map.png',
				onclick : function() {
					// grafische Oberfläche öffnen und Optionen eingeben lassen
					ed.execCommand('mceInsertContent', false, '[eventkrake ' +
						'template="simpletable" ' +
						'datestart="now" ' + 
						'dateend="+12 hours" ' +
						'width="100%" ' +
						'height="300px" ' +
						'address="" ' +
						'latlngbounds="((52.706347,13.018584)(52.314356,13.740935))" ' +
						'latlng="(52.523781,13.411430)" ' +						
						'zoom="16" ' +
						'categories="' + categories + '" ' +
						'groups="' + groups + '"]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Eventkrake Shortcode",
				author : 'g4rf',
				authorurl : 'http://g4rf.net/',
				infourl : 'http://g4rf.net/eventkrake/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('eventkrake', tinymce.plugins.Eventkrake);
})();