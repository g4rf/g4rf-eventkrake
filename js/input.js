/* global Leaflet, google */

jQuery(document).ready(function() {
    /*** Eventbindings ***/
    
    // disables automatic button action in text fields
    jQuery("#eventkrake-input-form input[type='text']").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    
    // start form
    jQuery("#eventkrake-input-start").click(function() {
        jQuery("#eventkrake-input-background").appendTo("body");
        jQuery("#eventkrake-input-form").appendTo("body");
        jQuery("#eventkrake-input-loader").appendTo("body");
        Eventkrake.Input.hideAnimation();
        Eventkrake.Input.show();
    });
    
    // move between screens
    jQuery("#eventkrake-input-back").click(function() {
        var action = jQuery(".eventkrake-input-tab:visible").data("previous");
        
        if(action == "close") {
            Eventkrake.Input.hide();
            return false;
        }
        
        jQuery(".eventkrake-input-tab").removeClass("visible");
        jQuery(".eventkrake-input-tab[data-me='" + action + "']")
                .addClass("visible");
        
        jQuery("#eventkrake-input-form-elements").scrollTop(0);
        return false;
    });
    jQuery("#eventkrake-input-next").click(function() {
        var action = jQuery(".eventkrake-input-tab:visible").data("next");
        
        jQuery(".eventkrake-input-tab").removeClass("visible");
        jQuery(".eventkrake-input-tab[data-me='" + action + "']")
                .addClass("visible");
        
        jQuery("#eventkrake-input-form-elements").scrollTop(0);
        return false;
    });
    
    // switch between location list and add new location
    jQuery("[name='eventkrake-input-location-radio']").click(function() {        
        if(jQuery(this).val() == "add") {
            // Ort erstellen
            jQuery('#eventkrake-input-add-location').slideDown();
            jQuery('#eventkrake-input-select-location').slideUp();
            if(Eventkrake.Input.map == null) Eventkrake.Input.loadMap();
        
        } else {
            // Ortliste
            jQuery('#eventkrake-input-add-location').slideUp();
            jQuery('#eventkrake-input-select-location').slideDown();
        }
    });
    
    // load date picker
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
    jQuery("#eventkrake-input-save").click(function() {        
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
    map: null,
    
    showAnimation: function() {
        jQuery("#eventkrake-input-loader").show();
    },
    
    hideAnimation: function() {
        jQuery("#eventkrake-input-loader").hide();
    },
    
    show: function() {
        jQuery("#eventkrake-input-background").show();
        jQuery("#eventkrake-input-form").show();
    },
    
    hide: function() {
        jQuery("#eventkrake-input-form").fadeOut(200);
        jQuery("#eventkrake-input-background").fadeOut(250);        
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
        // no visible map container
        if(! jQuery("#eventkrake-map:visible").length) return;
        
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
            maxZoom: 19
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

        // search geo coordinates for address
        jQuery('#eventkrake-input-form .eventkrake_lookforaddress').click(function() {
            Eventkrake.Geo.getLatLng(
                jQuery("input[name='eventkrake-address']").val(),
                Eventkrake.Input.loadNewAddressForLocation
            );
        });

        // fill suggestion into address text field
        jQuery("#eventkrake-rec").click(function() {
            jQuery("input[name='eventkrake-address']").val(
                jQuery(this).text()
            );
        });
        
        // search address on enter in address field
        jQuery("input[name='eventkrake-address']").keypress(function(e) {
            if (e.which == 13) {
                jQuery(".eventkrake_lookforaddress").click();
                return false;
            }
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