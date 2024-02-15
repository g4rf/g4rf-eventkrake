export default function getHTML(blockProps) {
    const cssPrefix = "g4rf-eventkrake-events-list";
    const cssTemplate = cssPrefix + "-template";
        
    return (
        <ul { ...blockProps } >
            <li className={ cssPrefix + "-event " + cssTemplate }>
                
                { /* featured image */ }
                <img src="" alt="" />
                
                { /* title */ }
                <div className={ cssPrefix + "-title" }></div>
                
                { /* excerpt */ }
                <div className={ cssPrefix + "-excerpt" }></div>
                
                { /* content */ }
                <div className={ cssPrefix + "-content" }></div>
                
                { /* dates */ }
                <div className={ cssPrefix + "-dates" }>
                    <div className={ cssPrefix + "-date " + cssTemplate }>
                        <span className={ cssPrefix + "-start-date" }></span>
                        <span className={ cssPrefix + "-start-hour" }></span>
                        <span className={ cssPrefix + "-end-date" }></span>
                        <span className={ cssPrefix + "-end-hour" }></span>
                        <a className={ cssPrefix + "-ics" } href=""></a>
                    </div>
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