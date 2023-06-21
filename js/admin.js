/* global Leaflet */

jQuery(document).ready(function() {
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
    jQuery("#eventkrake_locationid_edit_location").click(function() {
        var locationId = jQuery("select[name='eventkrake_locationid']").val();
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

    // add new link in meta
    jQuery(".eventkrake-add-link").click(function(e) {
        e.preventDefault();
        jQuery(".eventkrake-links-template").clone()
                .removeClass("eventkrake-links-template eventkrake-hide")
                .insertBefore(jQuery(this).parent());
    });

    // add new time on events
    jQuery(".eventkrake-add-time").click(function(e) {
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
    
    // search for select
    jQuery(".eventkrake-select-search").on("keyup", function() {
        var select = jQuery(this).next(".eventkrake-select-multiple");
        var search = jQuery(this).val().toLowerCase();
        
        if(search.length > 0) { // search something
            
            jQuery("label", select).each(function(i, label) {                
                if(jQuery(label).text().toLowerCase().indexOf(search) > -1) {
                    jQuery(label).show();
                } else {
                    jQuery(label).hide();
                }
            });
            return;
        } 
        
        // show all
        jQuery("label", select).show();
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
