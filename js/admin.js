/* global Leaflet */

jQuery(window).on("load", function() {
    
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
            var latlng = [e.latlng.lat, e.latlng.lng];
            Eventkrake.Geo.getAddress(
                latlng,
                function(notUsed, address) {
                    Eventkrake.Admin.loadNewAddressForLocation(latlng, address);
                }
            );
        });
        
        // set timeout for drawing the map as Wordpress do some crazy things
        // rendering the admin screens
        window.setTimeout(function() {
            Eventkrake.Admin.map.invalidateSize({'pan': false});
            Eventkrake.Admin.map.setView([lat, lng], 17);
        }, 1000);
        
        jQuery('#eventkrake-reload-map').on("click", function() {
            Eventkrake.Admin.map.invalidateSize({'pan': false});
            Eventkrake.Admin.map.setView([lat, lng], 17);
        });
    }
    
    jQuery('.eventkrake_lookforaddress').on("click", function() {
        Eventkrake.Geo.getLatLng(
            jQuery("#" + Eventkrake.Admin.addressId).val(),
            Eventkrake.Admin.loadNewAddressForLocation
        );
    });

    jQuery('#' + Eventkrake.Admin.recId).on("click", function() {
        jQuery("#" + Eventkrake.Admin.addressId).val(
            jQuery(this).text()
        );
    });

    // Link bei Events zu "Ort bearbeiten"
    jQuery("#eventkrake_locationid_edit_location").on("click", function() {
        var locationId = jQuery("select[name='eventkrake_locationid']").val();
        if(locationId > 0) {
            window.location.href = jQuery(this).data("url") + locationId;
        }
        return false;
    });

    // suggested categories
    jQuery(".eventkrake-cat-suggestion").on("click", function() {
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

    // add new link in meta
    jQuery(".eventkrake-add-link").on("click", function(e) {
        e.preventDefault();
        jQuery(".eventkrake-links-template").clone()
                .removeClass("eventkrake-links-template eventkrake-hide")
                .insertBefore(jQuery(this).parent());
    });

    // add new time on events
    jQuery(".eventkrake-add-time").on("click", function(e) {
        e.preventDefault();
        var dates = jQuery(".eventkrake-template.eventkrake-dates").clone()
                .removeClass("eventkrake-template")
                .insertBefore(jQuery(this).parent());
    });

    // remove time on events
    jQuery("body").on("click", ".eventkrake-remove-date", function(e) {
        e.preventDefault();
        jQuery(this).parent().remove();
    });
    
    // compare start and end times and show warning if end < start
    jQuery(document).on("change",
            ".eventkrake-dates input, .eventkrake-dates select",
    function() {
        let parent = jQuery(this).parents(".eventkrake-dates");

        // start date
        let startDate = 
                jQuery(".eventkrake-date-start input[type='date']", parent);
        let startTimeHour = 
                jQuery(".eventkrake-date-start .eventkrake-hour", parent);
        let startTimeMinute = 
                jQuery(".eventkrake-date-start .eventkrake-minute", parent);
        
        // end date
        let endDate = 
                jQuery(".eventkrake-date-end input[type='date']", parent);
        let endTimeHour = 
                jQuery(".eventkrake-date-end .eventkrake-hour", parent);
        let endTimeMinute = 
                jQuery(".eventkrake-date-end .eventkrake-minute", parent);
        
        let start = new Date(startDate.val() + "T" + 
                startTimeHour.val() + ":" + startTimeMinute.val() + ":00");
        let end = new Date(endDate.val() + "T" + 
                endTimeHour.val() + ":" + endTimeMinute.val() + ":00");
 
        if(end <= start) {
            jQuery(".eventkrake-date-warning", parent).show();
        } else {
            jQuery(".eventkrake-date-warning", parent).hide();
        }
    });    
    
    // search for artist
    jQuery(".eventkrake-artist-select-search").on("keyup", function() {
        var select = jQuery(this).next(".eventkrake-artist-select");
        var search = jQuery(this).val().toLowerCase();
        
        if(search.length > 0) { // search something
            
            jQuery("div", select).each(function(i, div) {                
                if(jQuery(div).text().toLowerCase().indexOf(search) > -1) {
                    jQuery(div).show();
                } else {
                    jQuery(div).hide();
                }
            });
            return;
        } 
        
        // show all
        jQuery("div", select).show();
    });
    
    // click artist in select
    jQuery(".eventkrake-artist-select div").on("click", function() {
        const id = jQuery(this).data("id");
        const title = jQuery(this).text();
        // TODO: at the moment can be only one order area
        const order = jQuery(".eventkrake-artist-order");
        const button = jQuery(".eventkrake-order-artist.eventkrake-template", order)
                .clone()
                .removeClass("eventkrake-template")
                .data("id", id)
                .appendTo(order);
        jQuery(".eventkrake-order-artist-title", button).empty().append(title);
        jQuery(".eventkrake-order-artist-hidden", button).attr("value", id);
    });
    
    // remove artist from order
    jQuery("body").on("click", ".eventkrake-order-artist-delete", function() {
        jQuery(this).parent().remove();
    });
    
    // make artist order list sortable
    jQuery(".eventkrake-artist-order").sortable({
        cursor: "grabbing"
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
    loadNewAddressForLocation: function(latlng, address) {
        if(latlng === false) {
            jQuery("#" + Eventkrake.Admin.recId).empty().append(address);
            return;
        }

        Eventkrake.Admin.map.panTo(latlng);
        Eventkrake.Admin.map.markers[0].setLatLng(latlng);

        jQuery("#" + Eventkrake.Admin.recId).empty().append(address);
        if(jQuery("#" + Eventkrake.Admin.addressId).val().length == 0) {
            jQuery("#" + Eventkrake.Admin.addressId).val(address);
        }

        jQuery("#" + Eventkrake.Admin.latId).val(latlng[0]);
        jQuery("#" + Eventkrake.Admin.lngId).val(latlng[1]);
    }
};
