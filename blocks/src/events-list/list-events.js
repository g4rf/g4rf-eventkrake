import { InspectorControls } from '@wordpress/block-editor';
import { ToggleControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import * as Controls from './controls';

/**
 * Loads the events data into the list.
 * @param {object} arguments[0]
 *      {HTML} block: Parent block that contains the events list.
 *      {boolean} isEditor: if in block editor or not, default false
 *      {string} start: PHP date definition, default "now"
 *      {string} end: PHP date definition, default "+10 years"
 * @returns {null}
 */
export function load( { block, isEditor = false } )
{   
    const $ = jQuery;
   
    // prevent double loading (especially in editor)
    if($(block).data("loading")) return;    
    $(block).data("loading", true);
    
    const prefix = ".g4rf-eventkrake-events-list";    
    const template = "g4rf-eventkrake-events-list-template";
    const list = $(prefix + "-list", block);
    
    const start = $(prefix + "-list", block).attr("data-start");
    const end = $(prefix + "-list", block).attr("data-end");
    
    // remove old blocks
    $(".g4rf-eventkrake-events-list-event", list).not("." + template).remove();
    
    // hide noevents message
    $(".g4rf-eventkrake-noevents", list).hide();
    
    // show spinner
    $(".g4rf-eventkrake-spinner", list).show();
    
    // add compatibility with WP Multilang
    let language = "";
    const bodyClasses = $("body").attr("class");
    const languages = bodyClasses.match(/language-(?<language>[a-z]{2})/);
    if(languages) {
        language = "/" + languages.groups.language;
    }
    
    $.getJSON(language + "/wp-json/eventkrake/v3/events", {
        earliestEnd: start,
        latestStart: end
    }, function(data) { 
        
        // hide spinner
        $(".g4rf-eventkrake-spinner", list).hide();
        
        // show noevents message
        $(".g4rf-eventkrake-noevents", list).show();
        
        // crawl events
        $.each(data.events, function(index, eventData)
        {
            // hide noevents message
            $(".g4rf-eventkrake-noevents", list).hide();
    
            const eventHtml = $(prefix + "-event." + template, list)
                    .clone()
                    .removeClass(template)
                    .appendTo(list);

            // image
            $(prefix + "-image img", eventHtml).attr("src", eventData.image);
            if(!isEditor) {
                $(prefix + "-image", eventHtml).attr("href", eventData.url);
            }

            // title
            $(prefix + "-title a", eventHtml).append(eventData.title);
            if(!isEditor) {
                $(prefix + "-title a", eventHtml).attr("href", eventData.url);
            }

            // excerpt
            $(prefix + "-excerpt p", eventHtml).append(eventData.excerpt);

            // content
            $(prefix + "-content", eventHtml).append(eventData.content);

            // location
            const location = data.locations[eventData.locationId];
            $(prefix + "-location-title", eventHtml).append(location.title);
            // location with link
            $(prefix + "-location-title-with-link a", eventHtml)
                    .append(location.title);
            if(!isEditor) {
                $(prefix + "-location-title-with-link a", eventHtml)
                    .attr("href", location.url);
            }
            // location address
            $(prefix + "-location-address", eventHtml).append(location.address);

            // dates
            const start = new Date(eventData.start);
            const end = new Date(eventData.end);
            let door = false;
            if(eventData.door != false) door = new Date(eventData.door);
            const dateOptions = {
                weekday: "short",
                day: "numeric",
                month: "short",
                year: "numeric"
            };
            const timeOptions = {
                hour: "2-digit",
                minute: "2-digit"
            };
            // start
            $(prefix + "-start-date", eventHtml).append(
                    start.toLocaleDateString(undefined, dateOptions));
            $(prefix + "-start-time", eventHtml).append(
                    start.toLocaleTimeString(undefined, timeOptions));
            // end
            if (start.toDateString() === end.toDateString()) {
                // on same day
                $(prefix + "-end-date", eventHtml).remove();
            } else {
                // not on same day
                $(prefix + "-end-date", eventHtml).append(
                        end.toLocaleDateString(undefined, dateOptions));
            }
            $(prefix + "-end-time", eventHtml).append(
                    end.toLocaleTimeString(undefined, timeOptions));
            //door
            if(door != false) {
                $(prefix + "-door-label", eventHtml).append(
                                        __('Doors:', 'eventkrake'));
                $(prefix + "-door-time", eventHtml).append(
                    door.toLocaleTimeString(undefined, timeOptions));
            } else {
                $(prefix + "-door-label", eventHtml).hide();
                $(prefix + "-door-time", eventHtml).hide();
            }
            // ics
            if(!isEditor) {
                $(prefix + "-ics", eventHtml).attr("href", eventData.icsUrl);
            }
            
            // classes; show them only in frontend
            if( ! isEditor )
            {   
                let classes =[];

                // id
                classes.push(Eventkrake.cssClass(
                        eventData.id, "g4rf-eventkrake-id"
                ));
                // uid
                classes.push(Eventkrake.cssClass(
                        eventData.uid, "g4rf-eventkrake-uid"
                ));
                // title
                classes.push(Eventkrake.cssClass(
                        eventData.title, "g4rf-eventkrake-title"
                ));
                
                // location id
                classes.push(Eventkrake.cssClass(
                        location.id, "g4rf-eventkrake-location-id"
                ));
                // location name
                classes.push(Eventkrake.cssClass(
                        location.title, "g4rf-eventkrake-location-title"
                ));
                
                // start day
                classes.push(Eventkrake.cssClass(
                        start.getDate(), "g4rf-eventkrake-start-day"
                ));
                // start month
                classes.push(Eventkrake.cssClass(
                        start.getMonth() + 1, "g4rf-eventkrake-start-month"
                ));
                // start year
                classes.push(Eventkrake.cssClass(
                        start.getFullYear(), "g4rf-eventkrake-start-year"
                ));
                // start weekday
                classes.push(Eventkrake.cssClass(
                        start.getDay(), "g4rf-eventkrake-start-weekday"
                ));        
                // start hour
                classes.push(Eventkrake.cssClass(
                        start.getHours(), "g4rf-eventkrake-start-hour"
                ));        
                // start minute
                classes.push(Eventkrake.cssClass(
                        start.getMinutes(), "g4rf-eventkrake-start-minute"
                ));
                
                // end day
                classes.push(Eventkrake.cssClass(
                        end.getDate(), "g4rf-eventkrake-end-day"
                ));
                // end month
                classes.push(Eventkrake.cssClass(
                        end.getMonth() + 1, "g4rf-eventkrake-end-month"
                ));
                // end year
                classes.push(Eventkrake.cssClass(
                        end.getFullYear(), "g4rf-eventkrake-end-year"
                ));
                // end weekday
                classes.push(Eventkrake.cssClass(
                        end.getDay(), "g4rf-eventkrake-end-weekday"
                ));        
                // end hour
                classes.push(Eventkrake.cssClass(
                        end.getHours(), "g4rf-eventkrake-end-hour"
                ));        
                // end minute
                classes.push(Eventkrake.cssClass(
                        end.getMinutes(), "g4rf-eventkrake-end-minute"
                ));
                // door
                if(door != false) {
                    // door hour
                    classes.push(Eventkrake.cssClass(
                            door.getHours(), "g4rf-eventkrake-door-hour"
                    ));        
                    // door minute
                    classes.push(Eventkrake.cssClass(
                            door.getMinutes(), "g4rf-eventkrake-door-minute"
                    ));
                }

                // artists
                if(eventData.artists.length > 0) {
                    // has artists
                    classes.push("g4rf-eventkrake-has-artists");
                    
                    eventData.artists.forEach(function(artistId) {
                        const artist = data.artists[artistId];
                        
                        // artist id
                        classes.push(Eventkrake.cssClass(
                            artist.id, "g4rf-eventkrake-artist-id"
                        ));
                        // artist name
                        classes.push(Eventkrake.cssClass(
                            artist.title, "g4rf-eventkrake-artist-title"
                        ));
                    });                    
                }

                // eventkrake categories
                if(eventData.categories.length > 0) {
                    // has eventkrake categories
                    classes.push("g4rf-eventkrake-has-categories");
                    
                    eventData.categories.forEach(function(category) {
                        // eventkrake category
                        classes.push(Eventkrake.cssClass(
                            category, "g4rf-eventkrake-category"
                        ));
                    });                    
                }
                
                // wp categories
                if(eventData.wpcategories.length > 0) {
                    // has wp categories
                    classes.push("g4rf-eventkrake-has-wpcategories");
                    
                    eventData.wpcategories.forEach(function(wpCategory) {
                        // wp category
                        classes.push(Eventkrake.cssClass(
                            wpCategory, "g4rf-eventkrake-wpcategory"
                        ));
                    });                    
                }
                
                // wp tags
                if(eventData.wptags.length > 0) {
                    // has wp tags
                    classes.push("g4rf-eventkrake-has-wptags");
                    
                    eventData.wptags.forEach(function(wpTag) {
                        // wp tag
                        classes.push(Eventkrake.cssClass(
                            wpTag, "g4rf-eventkrake-wptag"
                        ));
                    });                    
                }
                
                eventHtml.addClass(classes);
            }
        });
    }).always(function() {
        $(block).data("loading", false);
    });
}

/**
 * Creates the HTML to put the event data in.
 * @param {object} arguments[0]
 *      {object} attributes The settings of the control.
 * @returns {String}
 */
export function html({ attributes }) {    
    const prefix = "g4rf-eventkrake-events-list";
    const template = prefix + "-template";
    
    attributes.prefix = prefix;
    attributes.template = template;
    
    return (
        <div className={ prefix + "-list" } 
             data-start={ attributes.dateStart }
             data-end={ attributes.dateEnd } >
            
            <div className="g4rf-eventkrake-spinner"></div>
            <div className="g4rf-eventkrake-noevents">{ 
                __('No events at this time.', 'eventkrake') 
            }</div>
            
            <div className={ prefix + "-event " + template } >

                { /* featured image */ }
                <Controls.Image attributes={attributes} />
                
                <div className={ prefix + "-info" }>

                    { /* title */ }
                    <Controls.Title attributes={attributes} />

                    { /* date */ }
                    <Controls.Date attributes={attributes} />

                    { /* location */ }
                    <Controls.Location attributes={attributes} />

                    { /* excerpt */ }
                    <Controls.Excerpt attributes={attributes} />

                    { /* content */ }
                    <Controls.Content attributes={attributes} />
                        
                </div>
                
                <Controls.Seperator attributes={attributes} />
            </div>
        </div>
    );
}

