import metadata from './block.json';

/**
 * Loads the events data into the list.
 * @param {string} start PHP date definition
 * @param {string} end PHP date definition
 * @returns {null}
 */
export function load(start = 'now', end = '+10 years') {
    const $ = jQuery;
    const prefix = ".g4rf-eventkrake-events-list";    
    const template = "g4rf-eventkrake-events-list-template";
    const list = $(prefix + "-list");
    
    $.getJSON("/wp-json/eventkrake/v3/events", {
        earliestStart: start,
        latestStart: end
    }, function(data) { $.each(data.events, function(index, eventData) {
        
        let eventHtml = $(prefix + "-event." + template, list)
                .clone()
                .removeClass(template)
                .appendTo(list);

        $(prefix + "-image", eventHtml).attr("src", eventData.image);
        $(prefix + "-title", eventHtml).append(eventData.title);
        $(prefix + "-excerpt", eventHtml).append(eventData.excerpt);
        $(prefix + "-content", eventHtml).append(eventData.content);
       
        // location
        let location = data.locations[eventData.locationId];
        $(prefix + "-location-title", eventHtml).append(location.title);
        $(prefix + "-location-title-with-link a", eventHtml)
                .attr("href", location.url)
                .append(location.title);
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
        if (start.toDateString() !== end.toDateString()) {
            // not on same day
            $(prefix + "-end-date", eventHtml).append(
                    end.toLocaleDateString(undefined, dateOptions));
        }
        $(prefix + "-end-time", eventHtml).append(
                end.toLocaleTimeString(undefined, timeOptions));
        // ics
        $(prefix + "-ics", eventHtml).attr("href", eventData.icsUrl);
        
    });});
}

/**
 * Creates the HTML to put the event data in.
 * @param {obejct} blockProps
 * @param {boolean} [isAdmin=false] true if in block editor
 * @returns {String}
 */
export function html(blockProps, isAdmin = false) {
    const cssPrefix = "g4rf-eventkrake-events-list";
    const cssTemplate = cssPrefix + "-template";
        
    return (
        <div { ...blockProps } >

            <BackendLabel isAdmin={isAdmin} />
            
            <ul className={ cssPrefix + "-list" }>
                <li className={ cssPrefix + "-event " + cssTemplate }>

                    { /* featured image */ }
                    <img className={ cssPrefix + "-image" } src="" alt="" />

                    { /* title */ }
                    <div className={ cssPrefix + "-title" }></div>

                    { /* excerpt */ }
                    <div className={ cssPrefix + "-excerpt" }></div>

                    { /* content */ }
                    <div className={ cssPrefix + "-content" }></div>

                    { /* date */ }
                    <div className={ cssPrefix + "-date" }>
                        <span className={ cssPrefix + "-start-date" }></span>
                        <span className={ cssPrefix + "-start-time" }></span>
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
        </div>
    );
}


function BackendLabel({ isAdmin }) {
    if(isAdmin) {
        return (<div>{ metadata.title }</div>);
    }
    return (<></>);
}