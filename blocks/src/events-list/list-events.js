import { InspectorControls } from '@wordpress/block-editor';
import { ToggleControl, PanelBody } from '@wordpress/components';

import * as Controls from './controls';

import metadata from './block.json';


/**
 * Loads the events data into the list.
 * @param {object} arguments[0]
 *      {HTML} block: Parent block that contains the events list.
 *      {boolean} isEditor: if in block editor or not, default false
 *      {string} start: PHP date definition, default "now"
 *      {string} end: PHP date definition, default "+10 years"
 * @returns {null}
 */
export function load(
        { block, isEditor = false, start = 'now', end = '+10 years' } )
{
    const $ = jQuery;
    
    // prevent double loading (especially in editor)
    if($(block).data("loading")) return;    
    $(block).data("loading", true);
    
    const prefix = ".g4rf-eventkrake-events-list";    
    const template = "g4rf-eventkrake-events-list-template";
    const list = $(prefix + "-list", block);
    
    // remove old blocks
    $(".g4rf-eventkrake-events-list-event", list).not("." + template).remove();
    
    $.getJSON("/wp-json/eventkrake/v3/events", {
        earliestStart: start,
        latestStart: end
    }, function(data) { $.each(data.events, function(index, eventData) {
        
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
        $(prefix + "-title h3 a", eventHtml).append(eventData.title);
        if(!isEditor) {
            $(prefix + "-title h3 a", eventHtml).attr("href", eventData.url);
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
        // ics
        if(!isEditor) {
            $(prefix + "-ics", eventHtml).attr("href", eventData.icsUrl);
        }
        
        $(block).data("loading", false);
    });});
}

/**
 * Creates the HTML to put the event data in.
 * @param {object} arguments[0]
 *      {object} attributes The settings of the control.
 * @returns {String}
 */
export function html({ attributes }) {    
    const cssPrefix = "g4rf-eventkrake-events-list";
    const cssTemplate = cssPrefix + "-template";
    
    attributes.prefix = cssPrefix;
    attributes.template = cssTemplate;
    
    return (
        <ul className={ cssPrefix + "-list" }>
            <li className={ cssPrefix + "-event " + cssTemplate } href="">

                { /* featured image */ }
                <a className={ cssPrefix + "-image" } href="">
                    <img  src="" alt="" />
                </a>

                { /* title */ }
                <div className={ cssPrefix + "-title" }>
                    <h3><a href=""></a></h3>
                </div>

                { /* excerpt */ }
                <div className={ cssPrefix + "-excerpt" }><p></p></div>

                { /* content */ }
                <Controls.Content attributes={attributes} />

                { /* date */ }
                <div className={ cssPrefix + "-date" }>
                    <span className={ cssPrefix + "-start-date" }></span>
                    <span className={ cssPrefix + "-start-time" }></span>
                    <span className={ cssPrefix + "-date-separator" }>–</span>
                    <span className={ cssPrefix + "-end-date" }></span>
                    <span className={ cssPrefix + "-end-time" }></span>
                    <a className={ cssPrefix + "-ics" } href="">ics</a>
                </div>

                { /* location */ }
                <div className={ cssPrefix + "-location" }>
                    { /* title */ }
                    <div className={ cssPrefix + "-location-title" }></div>
                    { /* title with link */ }
                    <div className={ cssPrefix + "-location-title-with-link" }>
                        <a href=""></a>
                    </div>
                    { /* address */ }
                    <div className={ cssPrefix + "-location-address" }></div>
                </div>

            </li>
        </ul>
    );
}

