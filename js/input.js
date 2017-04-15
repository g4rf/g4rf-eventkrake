/* global Leaflet, google */

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
    
    hint: function(msg) {
        var hint = jQuery("#eventkrake-input-hint");
        jQuery("p", hint).empty().append(msg);
        hint.finish().show().delay(5000).fadeOut();
    },
    
    mark: function(element) {
        jQuery(element).css("box-shadow", "0 0 3px 2px #f33").focus();
    },
    
    demark: function() {
        jQuery("#eventkrake-input-form :input").css("box-shadow", "none");
    },
    
    getTranslation: function(dataId) {
        return jQuery("#eventkrake-input-js-translations").data(dataId);
    },
    
    datepicker: function(datepicker) {
        jQuery(datepicker).each(function() {
            var dateField = jQuery(this)
                    .closest(".eventkrake-dateselect")
                    .find("input[name='eventkrake-startdate[]']");

            jQuery(this).datepicker({
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
                    jQuery(dateField).val(
                            jQuery.datepicker.formatDate("yy-mm-dd", date));
                }
            });
            
            jQuery(this).datepicker(
                "setDate",
                new Date(jQuery(dateField).data("default-date"))
            );
        });  
    },
    
    addEventRow: function() {
        var table = jQuery(".eventkrake-input-events table");
        var row = jQuery(".eventkrake-input-template", table).clone()
                .removeClass("eventkrake-input-template")
                .appendTo(table);
        Eventkrake.Input.datepicker(jQuery(".datepicker", row));
    },
    
    removeEventRow: function(row) {
        jQuery(row).remove();
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

/*** Eventbindings ***/
jQuery(document).ready(function() {
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
    
    // prev screen
    jQuery("#eventkrake-input-back").click(function() {
        var action = jQuery(".eventkrake-input-tab:visible").data("previous");
        
        // deactivate save button and activate next button
        jQuery("#eventkrake-input-save").prop("disabled", true);
        jQuery("#eventkrake-input-next").prop("disabled", false);
        
        // first tab
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
    
    // next screen
    jQuery("#eventkrake-input-next").click(function() {
        Eventkrake.Input.demark();        
        
        var action = jQuery(".eventkrake-input-tab:visible").data("next");
        
        switch(action) {
            case "location": // coming from captcha
                if(jQuery("[name='eventkrake-input-response']").val() == "") {
                    Eventkrake.Input.mark("[name='eventkrake-input-response']");
                    Eventkrake.Input.hint(
                        Eventkrake.Input.getTranslation("response-missing"));
                    return false;
                }
                if(jQuery("[name='eventkrake-input-email']").val() == "") {
                    Eventkrake.Input.mark("[name='eventkrake-input-email']");
                    Eventkrake.Input.hint(
                        Eventkrake.Input.getTranslation("email-missing"));
                    return false;
                }
                break;
            case "events": // coming from location
                // if new location check data
                if(jQuery("#eventkrake-input-add-location:visible").length) {
                    if(jQuery("[name='eventkrake-address']").val() == "") {
                        Eventkrake.Input.mark("[name='eventkrake-address']");
                        Eventkrake.Input.hint(
                            Eventkrake.Input.getTranslation("address-missing"));
                        return false;
                    }
                    if(jQuery("[name='eventkrake-location-name']").val() == "") {
                        Eventkrake.Input.mark("[name='eventkrake-location-name']");
                        Eventkrake.Input.hint(
                            Eventkrake.Input.getTranslation("location-name-missing"));
                        return false;
                    }
                }
                
                // deactivate this button and activate save button
                jQuery(this).prop("disabled", true);
                jQuery("#eventkrake-input-save").prop("disabled", false);
                break;
        }
        
        jQuery(".eventkrake-input-tab").removeClass("visible");
        jQuery(".eventkrake-input-tab[data-me='" + action + "']")
                .addClass("visible");
        
        jQuery("#eventkrake-input-form-elements").scrollTop(0);
        return false;
    });
    
    // submit data
    jQuery("#eventkrake-input-save").click(function() {
        Eventkrake.Input.demark();
        
        // check if events is complete
        var error = false;
        // Have to do this in reverse order, as a return false in the loop will 
        // cause no effect on marking the element. So we'll do this on every
        // empty element.
        jQuery(jQuery("[name='eventkrake-event-title[]']:visible").get().reverse())
            .each(function() {
                if(jQuery(this).val() == "") {
                    Eventkrake.Input.mark(this);
                    Eventkrake.Input.hint(
                        Eventkrake.Input.getTranslation("event-title-missing"));
                    error = true;
                }
            }
        );
        if(error) return false;
        
        //Eventkrake.Input.showAnimation();
        jQuery.ajax({
            cache: false,
            method: "POST",
            data: jQuery("#eventkrake-input-form").serialize(),
            statusCode: {
                200: function() {
                    
                }
            }
        });
        
        return false; // just to prevent submitting the form the normal way
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
    
    // create empty event entries
    for(var i = 0; i < 5; i++) {
        Eventkrake.Input.addEventRow();
    }
    
    // add new event row
    jQuery("#eventkrake-input-add-event").click(Eventkrake.Input.addEventRow);
    
    // remove event row
    jQuery(".eventkrake-input-events").on("click", 
        ".eventkrake-input-delete-event", function() {
            Eventkrake.Input.removeEventRow(jQuery(this).closest("tr"));
        }
    );
});