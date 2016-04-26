jQuery(document).ready(function() {
    /*** Eventbindings ***/
    // Ort auswählen
    jQuery("#eventkrake-input-select-location-button").click(function() {
        jQuery(this).addClass('eventkrake-selected');
        jQuery("#eventkrake-input-add-location-button")
                .removeClass('eventkrake-selected');
        jQuery('#eventkrake-input-select-location').show();
        jQuery('#eventkrake-input-add-location').hide();
        jQuery('#eventkrake-input-events').show();
    });
    // Ort erstellen
    jQuery("#eventkrake-input-add-location-button").click(function() {
        jQuery(this).addClass('eventkrake-selected');
        jQuery("#eventkrake-input-select-location-button")
                .removeClass('eventkrake-selected');
        jQuery('#eventkrake-input-select-location').hide();
        jQuery('#eventkrake-input-add-location').show();    
        jQuery('#eventkrake-input-events').hide();
        
        if(! Eventkrake.Input.mapLoaded) {
            Eventkrake.Input.mapLoaded = true;
            Eventkrake.Input.loadMap();
        }
    });
    // Submits (from validation)
    jQuery("#eventkrake-input .submit").click(function() {
        switch(jQuery(this).data("action")) {
            case "addlocation":
                if(jQuery("#eventkrake-lat").val().length == 0) {
                    
                }
                break;
            case "addevents":
                break;
        }
    });
});

Eventkrake.Input = {
    mapLoaded: false,
    map: {},
    
    loadMap: function() {
        /* Map für die Auswahl des Ortes */
        var lat = Eventkrake.Geo.StandardLat;
        var lng = Eventkrake.Geo.StandardLng;

        Eventkrake.Input.map = Leaflet.map('eventkrake-map');
        var layer = new Leaflet.tileLayer(Eventkrake.Map.tileUrl, {
            attribution: Eventkrake.Map.attribution,
            maxZoom: 18
        });
        Eventkrake.Input.map.setView([lat, lng], 17);
        Eventkrake.Input.map.addLayer(layer);

        Eventkrake.Input.map.markers = [];
        Eventkrake.Input.map.markers.push(
            Leaflet.marker([lat, lng]).addTo(Eventkrake.Input.map));

        Eventkrake.Input.map.on('click', function(e) {
            var gLatLng = new google.maps.LatLng(e.latlng.lat, e.latlng.lng);
            Eventkrake.Geo.getAddress(
                gLatLng,
                function(notUsed, address) {
                    Eventkrake.Input.loadNewAddressForLocation(gLatLng, address);
                }
            );
        });

        jQuery('#eventkrake-input .eventkrake_lookforaddress').click(function() {
            Eventkrake.Geo.getLatLng(
                jQuery("input[name='eventkrake-address']").val(),
                Eventkrake.Input.loadNewAddressForLocation
            );
        });

        jQuery("#eventkrake-rec").click(function() {
            jQuery("input[name='eventkrake-address']").val(
                jQuery(this).text()
            );
        });
    },
    
    loadNewAddressForLocation: function(gLatLng, address) {
        if(gLatLng === false) {
            jQuery("#eventkrake-rec").empty().append(address);
            return;
        }

        var lat = gLatLng.lat();
        var lng = gLatLng.lng();

        Eventkrake.Input.map.panTo([lat,lng]);
        Eventkrake.Input.map.markers[0].setLatLng([lat,lng]);

        jQuery("#eventkrake-rec").empty().append(address);
        if(jQuery("input[name='eventkrake-address']").val().length == 0) {
            jQuery("input[name='eventkrake-address']").val(address);
        }

        jQuery("#eventkrake-lat").val(lat);
        jQuery("#eventkrake-lng").val(lng);
    }
};