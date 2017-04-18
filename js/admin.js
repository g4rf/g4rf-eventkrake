jQuery(document).ready(function() {	
    /* Datepicker */
    jQuery(".datepicker").datepicker({
        dayNames: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", 
            "Freitag", "Samstag"],
        dayNamesMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
        dayNamesShort: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
        monthNames: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli",
            "August", "September", "Oktober", "November", "Dezember"],
        monthNamesShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul",
            "Aug", "Sep", "Okt", "Nov", "Dez"],
        
        dateFormat: "DD, d. M yy",
        
        onSelect: function(dateText, inst)  {
            var date = jQuery.datepicker.parseDate(
                jQuery(this).datepicker("option", "dateFormat"), 
                dateText,
                {
                    dayNamesMin: jQuery(this).datepicker("option", "dayNamesMin"),
                    dayNamesShort: jQuery(this).datepicker("option", "dayNamesShort"),
                    dayNames: jQuery(this).datepicker("option", "dayNames"),
                    monthNamesShort: jQuery(this).datepicker("option", "monthNamesShort"),
                    monthNames: jQuery(this).datepicker("option", "monthNames")
                }
            );
            var id = jQuery(this).data('id');
            jQuery("#"+id).val(jQuery.datepicker.formatDate("yy-mm-dd", date));
        }
    });

    /* Ort suchen */
    /*jQuery('input[name="addressfinder"]').keydown(function(event) {
            window.clearTimeout(Eventkrake.Admin.keyTimeout);
    });
    jQuery('input[name="addressfinder"]').keyup(function() {
            var words = jQuery(this).val();
            var url = jQuery(this).data("url");
            var id = jQuery(this).data("id");
            Eventkrake.Admin.keyTimeout = window.setTimeout(function() {
                    Eventkrake.Admin.findLocation(url, id, words);
            }, 500);
    });
    /* Ort auswählen */
    /*jQuery('select[name="locationid"]').change(function() {
        Eventkrake.Admin.showLocationInfo(
            jQuery(this).data("url"),
            jQuery(this).val(),
            "#" + jQuery(this).attr("id") + "_info"
        );
    });

    /* Map für die Auswahl des Ortes */
    if(document.getElementById(Eventkrake.Admin.mapId)) {
        var lat = parseFloat(jQuery("#" + Eventkrake.Admin.latId).val());
        var lng = parseFloat(jQuery("#" + Eventkrake.Admin.lngId).val());
        if(isNaN(lat)) lat = Eventkrake.Geo.StandardLat;
        if(isNaN(lng)) lng = Eventkrake.Geo.StandardLng;

        Eventkrake.Admin.map = Leaflet.map(document.getElementById(Eventkrake.Admin.mapId));
        var layer = new Leaflet.tileLayer(Eventkrake.Map.tileUrl, {
            attribution: Eventkrake.Map.attribution,
            maxZoom: 18
        });
        Eventkrake.Admin.map.setView([lat, lng], 17);
        Eventkrake.Admin.map.addLayer(layer);

        Eventkrake.Admin.map.markers = Eventkrake.Admin.map.markers || [];
        Eventkrake.Admin.map.markers.push(
            Leaflet.marker([lat, lng]).addTo(Eventkrake.Admin.map));

        Eventkrake.Admin.map.on('click', function(e) {
            var gLatLng = new google.maps.LatLng(e.latlng.lat, e.latlng.lng);
            Eventkrake.Geo.getAddress(
                gLatLng,
                function(notUsed, address) {
                    Eventkrake.Admin.loadNewAddressForLocation(gLatLng, address);
                }
            );
        });
    }
    
    jQuery('.eventkrake_lookforaddress').click(function() {
        Eventkrake.Geo.getLatLng(
            jQuery("#" + Eventkrake.Admin.addressId).val(),
            Eventkrake.Admin.loadNewAddressForLocation
        );
    });
    
    jQuery('#' + Eventkrake.Admin.recId).click(function() {
        jQuery("#" + Eventkrake.Admin.addressId).val(
            jQuery(this).text()
        );
    });
    
    // Link bei Events zu "Ort bearbeiten"
    jQuery("#eventkrake_locationid_wordpress_edit_location").click(function() {
        var locationId = jQuery("select[name='eventkrake_locationid_wordpress']").val();
        if(locationId > 0) {
            window.location.href = jQuery(this).data("url") + locationId;
        }
        return false;
    });
    
    // suggested categories
    jQuery(".eventkrake-cat-suggestion").click(function() {
        var categories = jQuery("[name='eventkrake_categories']")
                .val().split(",");
        var newCategories = [];
        for(var i = 0; i < categories.length; i++) {
            categories[i] = categories[i].trim();
            if(categories[i].length > 0) newCategories.push(categories[i]);
        }
        newCategories.push(jQuery(this).text());
        jQuery("[name='eventkrake_categories']").val(newCategories.join(", "));
    });
});

var Eventkrake = Eventkrake || {};
Eventkrake.Admin = {
    mapId: "eventkrake_map",
    addressId: "eventkrake_address",
    recId: "eventkrake_rec",
    latId: "eventkrake_lat",
    lngId: "eventkrake_lng",
    
    /** @ignore */
    map: null,

    /** @ignore */
    keyTimeout: null,

    /** @ignore */
    imageId: 0,

    /** Ändere Karte, Adresstext und LatLng. */
    loadNewAddressForLocation: function(gLatLng, address) {        
        if(gLatLng === false) {
            jQuery("#" + Eventkrake.Admin.recId).empty().append(address);
            return;
        }

        var lat = gLatLng.lat();
        var lng = gLatLng.lng();

        Eventkrake.Admin.map.panTo([lat,lng]);
        Eventkrake.Admin.map.markers[0].setLatLng([lat,lng]);

        jQuery("#" + Eventkrake.Admin.recId).empty().append(address);
        if(jQuery("#" + Eventkrake.Admin.addressId).val().length == 0) {
            jQuery("#" + Eventkrake.Admin.addressId).val(address);
        }

        jQuery("#" + Eventkrake.Admin.latId).val(lat);
        jQuery("#" + Eventkrake.Admin.lngId).val(lng);
    },

    /** Listet Orte anhand eines Suchstrings auf. */
    /*findLocation: function(url, id, words) {
        var params = {
            action: 'getlocations',
            location_search: words,
            limit: 100
        };		
        jQuery.getJSON(url, params, function(data) {
            var sel = "#location_" + id;
            jQuery(sel).find("option").not(".fixed").remove();
            for(var key in data) {
                jQuery(sel).append("<option value='" + data[key].id + "'>" + 
                    data[key].name + " (" + data[key].address + ")</option>");
            }
        });
    },*/

    /** Findet Adressen.
     */
    /*findAddress : function() {
            var id = jQuery(e).parents('.yourbash').attr('id');
            var a = '#'+id+' input[name="address[]"]';
            var lat =  '#'+id+' input[name="lat[]"]';
            var lng =  '#'+id+' input[name="lng[]"]';

            Geo.getLatLng(jQuery(a).val(), function(latlng, address) {
                    jQuery(a).val(address);
                    if(latlng === false) {
                            jQuery(lat).val('');
                            jQuery(lng).val('');
                    } else {
                            jQuery(lat).val(latlng.lat());
                            jQuery(lng).val(latlng.lng());
                    }
            });
    },*/

    /** Gibt Infos zu einem Ort in einem DIV aus
     * @param {String} url Die Webservice-URL.
     * @param {Number} locationId Die Id des Ortes.
     * @param {Object} div Das Element, wo die Daten abgelegt werden. Dazu wird 
     *      jedes Element mit dem Attribut data-info="{Wert}" mit dem
     *      entsprechenden {Wert} befüllt.
     */
    /*showLocationInfo : function(url, locationId, elem) {
        var params = {
            action: 'getlocation',
            location_id: locationId
        };		
        jQuery.getJSON(url, params, function(location) {
            if(typeof location !== "object") return;
            jQuery(elem).find("[data-info]").each(function() {
                var text = location[jQuery(this).data("info")];
                if(jQuery.isArray(text)) {
                    text = text.join(", ");
                }
                jQuery(this).html(text);
            });
        });
    }*/
};
