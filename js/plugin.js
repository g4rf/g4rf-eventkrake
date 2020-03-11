var Leaflet = L.noConflict();

var Eventkrake = {
    load: function(o) {
        var submitdata = {
            "eventkrake": jQuery(o).data()
        };
        jQuery.post(submitdata.eventkrake.requesturl, submitdata, function(html) {
            jQuery(o).html(html);
        }).fail(function(status) {
            console.log("ERROR");
            console.log(status);
        });	
    },
    
    Map: {
	tileUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
	//tileUrl: 'http://{s}.tile.cloudmade.com/d53fe132e8734827be5f33054d8c16a6/107213/256/{z}/{x}/{y}.png',
	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors | Event data and WordPress plugin by <a href="http://eventkrake.de">Eventkrake</a>'
    },
        
    /**
    * @requires http://maps.google.com/maps/api/js
    */
    Geo: {
	/**
	 * Zugriffspunkt für den Google-Geocoder.
	 */
	Geocoder : new google.maps.Geocoder(),
	
	/**
	 * Speichert einen allgemeinen Lat-Wert.
	 */
	StandardLat : 52.523781,
	
	/**
	 * Speichert einen allgemeinen Lng-Wert.
	 */
	StandardLng : 13.411430,
	
	/**
	 * Versucht automatisch den Standort zu bestimmen.
	 * @param {Function} callback Die Callback-Funktion mit einem Parameter.
	 * 		Dem Parameter werden die Koordinaten als {google.maps.LatLng} 
	 * 		übergeben.
	 */
	getPosition : function(callback) {
            if(navigator.geolocation) { // try W3C Geolocation (Preferred)
                navigator.geolocation.getCurrentPosition(function(position) {
                    callback(new google.maps.LatLng(
                        position.coords.latitude, position.coords.longitude
                    ));
                }, function(msg) {
                    callback(false);
                });
            } else { // Browser doesn't support Geolocation
    		callback(false);
            }
	},
	
	/**
	 * Sucht anhand einer Adresse Geokordinaten. Sollten mehrere Ergebnisse
	 * vorliegen, wird das erste zurückgegeben.
	 * @param {String} address Adresse
	 * @param {Function} callback Callback-Funktion mit zwei Parametern: Der
	 *		erste gibt ein {google.maps.LatLng} zurück und der zweite eine
	 *		wohlformatierte Adresse.
	 */
	getLatLng : function(address, callback) {
            var self = this;
            
            if((!address) || address.length == 0) {
                callback(false, "Keine Adresse übergeben.");
                return;
            }

            if (self.Geocoder) {
                self.Geocoder.geocode(
                    {'address': address, 'region': 'de'},
                    function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            callback(results[0].geometry.location, results[0].formatted_address);
                        } else {
                            callback(false, "Adresse aus folgendem Grund nicht gefunden: " + status);
                        }
                    }
                );
            } else {
                callback(false, "Interner Fehler: Keinen Geocoder gefunden.");
            }
	},
	
	/**
	 * Sucht anhand von Geokordinaten die nächste visuell darstellbare
	 * Adresse. Sollten mehrere Ergebnisse vorliegen, wird das erste
	 * zurückgegeben.
	 * @param {google.maps.LatLng} latlng Koordinaten
	 * @param {Function} callback Callback-Funktion mit zwei Parametern: Im
	 *		Erfolgsfall gibt der erste entweder ein {google.maps.LatLng} zurück
	 *		und der zweite die wohlformatierte Adresse. Im Fehlerfall gibt der
	 *		erste Parameter false zurück und der zweite die Fehlermeldung.
	 */
	getAddress : function(latlng, callback) {
            var self = this;
		
            if((!latlng) || latlng.length == 0) {
                callback(false, "Keine Koordinaten übergeben.");
                return;
            }
		
            if (self.Geocoder) {
                self.Geocoder.geocode({'latLng': latlng},
                    function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            callback(results[0].geometry.location, results[0].formatted_address);
                        } else {
                            callback(false, "Koordinaten aus folgendem Grund nicht gefunden: " + status);
                        }
                    }
                );
            } else {
                callback(false, "Interner Fehler: Keinen Geocoder gefunden.");
            } 
	}
    }
};