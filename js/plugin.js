/* global L */

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
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors | Event data and WordPress plugin by <a href="http://eventkrake.de">Eventkrake</a>'
    },

    /**
    * @requires https://nominatim.openstreetmap.org
    */
    Geo: {
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
         * 		Dem Parameter werden die Koordinaten als [lat,lng]
         * 		übergeben.
         */
        getPosition : function(callback) {
            if(navigator.geolocation) { // try W3C Geolocation (Preferred)
                navigator.geolocation.getCurrentPosition(function(position) {
                    callback([
                        position.coords.latitude, position.coords.longitude
                    ]);
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
         *		erste gibt ein [lat, lng] zurück und der zweite eine
         *		wohlformatierte Adresse.
         */
        getLatLng : function(address, callback) {
            if(! address || address.length == 0) {
                callback(false, "Keine Adresse übergeben.");
                return;
            }

            jQuery.getJSON("https://nominatim.openstreetmap.org/search"
                    + "?q=" + address
                    + "&format=json&addressdetails=1&limit=1",
                function(data) {
                    if(data.length == 0) {
                        callback(false, "Keine Adresse gefunden.");
                    } else {
                        callback([data[0].lat, data[0].lon],
                            Eventkrake.Geo.formatAdress(data[0].address));
                    }
                }
            );
        },

        /**
         * Sucht anhand von Geokordinaten die nächste visuell darstellbare
         * Adresse. Sollten mehrere Ergebnisse vorliegen, wird das erste
         * zurückgegeben.
         * @param {Array} latlng [lat, lng] Koordinaten
         * @param {Function} callback Callback-Funktion mit zwei Parametern: Im
         *		Erfolgsfall gibt der erste entweder ein [lat, lng] zurück
         *		und der zweite die wohlformatierte Adresse. Im Fehlerfall gibt der
         *		erste Parameter false zurück und der zweite die Fehlermeldung.
         */
        getAddress : function(latlng, callback) {
            if((!latlng) || latlng.length == 0) {
                callback(false, "Keine Koordinaten übergeben.");
                return;
            }

            jQuery.getJSON("https://nominatim.openstreetmap.org/reverse"
                + "?format=json"
                + "&lat=" + latlng[0] + "&lon=" + latlng[1]
                + "&zoom=18&addressdetails=1",
                function(data) {
                    callback([data.lat, data.lon],
                        Eventkrake.Geo.formatAdress(data.address));
                }
            );
        },

        formatAdress: function(address) {
            var ret = "";
            if(address.road) ret += address.road;
            if(address.house_number) ret += " " + address.house_number;
            ret += ", ";
            if(address.postcode) ret += address.postcode + " ";
            if(address.city) ret += address.city;
            else ret += address.state;
            return ret;
        }
    }
};
