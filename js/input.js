jQuery(document).ready(function() {
    /*** Eventbindings ***/
    // Ortliste auswählen
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
    // wenn Ort in Liste angeklickt, Formular abschicken
    jQuery("select[name='eventkrake-input-locationlist']").change(function() {
        Eventkrake.Input.showAnimation();
        jQuery("#eventkrake-input form").submit();
    });
    // Karte laden, falls Karte sichtbar
    if(! Eventkrake.Input.mapLoaded 
            && ! jQuery("#eventkrake-input-add-location").hasClass('invisible')) {
        Eventkrake.Input.mapLoaded = true;
        Eventkrake.Input.loadMap();
    }
    // Date-Picker laden
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
        firstDay: 1,
        
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
    jQuery(".datepicker[data-id='eventkrake-startdate']").datepicker(
        "setDate", new Date(jQuery("#eventkrake-startdate").data("default-date"))
    );
    jQuery(".datepicker[data-id='eventkrake-enddate']").datepicker(
        "setDate", new Date(jQuery("#eventkrake-enddate").data("default-date"))
    );
    
    /*** Submits (form validation) ***/
    jQuery("#eventkrake-input .submit").click(function() {
        Eventkrake.Input.showAnimation();
        var valid = true;
        
        // alte Nachrichten löschen
        Eventkrake.Input.clearMessages();
        
        // Response angegeben?
        if(! jQuery("input[name='eventkrake-input-response']").val().length) {
            jQuery("input[name='eventkrake-input-response']").addClass("highlight");
            valid = false;
            Eventkrake.Input.printMessage(
                    Eventkrake.Input.getTranslation("response-missing"), true);
        } else jQuery("input[name='eventkrake-input-response']").removeClass("highlight");
        
        // E-Mail checken
        if(! jQuery("input[name='eventkrake-input-email']").val().length) {
            jQuery("input[name='eventkrake-input-email']").addClass("highlight");
            valid = false;
            Eventkrake.Input.printMessage(
                    Eventkrake.Input.getTranslation("email-missing"), true);
        } else jQuery("input[name='eventkrake-input-email']").removeClass("highlight");
        
        // action wählen
        switch(jQuery(this).data("action")) {
            case "addlocation":
                if(! jQuery("input[name='eventkrake-lat']").val().length
                    || ! jQuery("input[name='eventkrake-lng']").val().length
                    || ! jQuery("input[name='eventkrake-address']").val().length) {
                        jQuery("input[name='eventkrake-address']").addClass("highlight");
                        valid = false;
                        Eventkrake.Input.printMessage(
                            Eventkrake.Input.getTranslation("address-missing"), true);
                } else jQuery("input[name='eventkrake-address']").removeClass("highlight");
                if(! jQuery("input[name='eventkrake-location-name']").val().length) {
                    jQuery("input[name='eventkrake-location-name']").addClass("highlight");
                    valid = false;
                    Eventkrake.Input.printMessage(
                        Eventkrake.Input.getTranslation("location-name-missing"), true);
                } else jQuery("input[name='eventkrake-location-name']").removeClass("highlight");
                
                // Form übermitteln
                if(valid) {
                    jQuery("#eventkrake-input form").append(
                        "<input type='hidden' name='eventkrake-input-action'" +
                            " value='addlocation' />").submit();
                } else {
                    Eventkrake.Input.hideAnimation();
                }
                break;
            case "addevent":
                if(! jQuery("select[name='eventkrake-input-locationlist'] option:selected").length) {
                    jQuery("select[name='eventkrake-input-locationlist']").addClass("highlight");
                    valid = false;
                    Eventkrake.Input.printMessage(
                        Eventkrake.Input.getTranslation("event-location-missing"), true);
                } else jQuery("select[name='eventkrake-input-locationlist']").removeClass("highlight");
                if(! jQuery("input[name='eventkrake-event-title']").val().length) {
                    jQuery("input[name='eventkrake-event-title']").addClass("highlight");
                    valid = false;
                    Eventkrake.Input.printMessage(
                        Eventkrake.Input.getTranslation("event-title-missing"), true);
                } else jQuery("input[name='eventkrake-event-title']").removeClass("highlight");
                if(! jQuery("textarea[name='eventkrake-event-text']").val().length) {
                    jQuery("textarea[name='eventkrake-event-text']").addClass("highlight");
                    valid = false;
                    Eventkrake.Input.printMessage(
                        Eventkrake.Input.getTranslation("event-text-missing"), true);
                } else jQuery("textarea[name='eventkrake-event-text']").removeClass("highlight");
                // Begin > Ende?
                var selStart = jQuery("#eventkrake-startdate").val() + "T" + 
                        jQuery("[name='eventkrake-starthour']").val() + ":" +
                        jQuery("[name='eventkrake-startminute']").val() + ":00";
                var selEnd = jQuery("#eventkrake-enddate").val() + "T" + 
                        jQuery("[name='eventkrake-endhour']").val() + ":" +
                        jQuery("[name='eventkrake-endminute']").val() + ":00";
                if(selStart > selEnd) {
                    jQuery("td.eventkrake-dateselect").addClass("highlight");
                    valid = false;
                    Eventkrake.Input.printMessage(
                        Eventkrake.Input.getTranslation("event-date-error"), true);
                } else jQuery("td.eventkrake-dateselect").removeClass("highlight");
                
                // Form übermitteln
                if(valid) {
                    jQuery("#eventkrake-input form").append(
                        "<input type='hidden' name='eventkrake-input-action'" +
                            " value='addevent' />").submit();
                } else {
                    Eventkrake.Input.hideAnimation();
                }
                break;
        }
    });
});

var Eventkrake = Eventkrake || {};
Eventkrake.Input = {
    mapLoaded: false,
    map: {},
    
    showAnimation: function() {
        jQuery("#eventkrake-input-loader").show();
    },
    
    hideAnimation: function() {
        jQuery("#eventkrake-input-loader").hide();
    },
    
    printMessage: function(message, error) {
        if(typeof error == 'undefined') error = false;
        
        jQuery("<div>| " + message + "</div>").css({
            "font-weight": "bold",
            "color": error ? "#c00" : "#060"
        }).appendTo("#eventkrake-input-messages");
        
        // Nachrichten anzeigen
        document.getElementById("eventkrake-input-messages").scrollIntoView();
    },
    
    clearMessages: function() {
        jQuery("#eventkrake-input-messages").empty();
    },
    
    getTranslation: function(dataId) {
        return jQuery("#eventkrake-input-js-translations").data(dataId);
    },
    
    loadMap: function() {
        // kein Map COntainer
        if(! jQuery("#eventkrake-map").length) return;
        
        /* Map für die Auswahl des Ortes */
        var lat = Eventkrake.Geo.StandardLat;
        if(jQuery("#eventkrake-map").data("lat"))
            lat = jQuery("#eventkrake-map").data("lat");
        var lng = Eventkrake.Geo.StandardLng;
        if(jQuery("#eventkrake-map").data("lng"))
            lng = jQuery("#eventkrake-map").data("lng");

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
        if(! jQuery("input[name='eventkrake-address']").val().length) {
            jQuery("input[name='eventkrake-address']").val(address);
        }

        jQuery("input[name='eventkrake-lat']").val(lat);
        jQuery("input[name='eventkrake-lng']").val(lng);
    }
};