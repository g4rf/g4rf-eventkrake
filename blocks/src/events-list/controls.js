import metadata from './block.json';

export function Image({ attributes }) {
    const { showImage, prefix } = attributes;
    if(! showImage) return (<></>);
    
    return (
        <a className={ prefix + "-image" } href="">
            <img  src="" alt="" />
        </a>
    );
}

export function Title({ attributes }) {
    const { showTitle, prefix } = attributes;
    if(! showTitle) return (<></>);
    
    return (
        <div className={ prefix + "-title" }>
            <h4><a href=""></a></h4>
        </div>
    );
}

export function Date({ attributes }) {
    const { 
        showDate,
        showDateStart,
        showDateEnd,
        showDateIcs,
        prefix } = attributes;
    
    if(! showDate) return (<></>);
    
    let start = <></>;
    if(showDateStart) {
        start = (
            <>
                <span className={ prefix + "-start-date" }></span>
                <span className={ prefix + "-start-time" }></span>
            </>
        );
    }
    
    let end = <></>;
    if(showDateEnd) {
        end = (
            <>
                <span className={ prefix + "-end-date" }></span>
                <span className={ prefix + "-end-time" }></span>
            </>
        );
    }
    
    let seperator = <></>;
    if(showDateStart && showDateEnd) {
        seperator = <span className={ prefix + "-date-separator" }>–</span>;
    }
    
    let ics = <></>;
    if(showDateIcs) {
        ics = <a className={ prefix + "-date-ics" } href="">ics</a>;
    }
    
    return (
        <div className={ prefix + "-date" }>
            { start }
            { seperator }
            { end }
            { ics }
        </div>
    );
}

export function Location({ attributes }) {
    const { 
        showLocation,
        showLocationWithLink,
        showLocationAddress,
        prefix } = attributes;
    
    if(! showLocation) return (<></>);
    
    let title = <></>;
    if(showLocationWithLink) {
        title = ( 
            <span className={ prefix + "-location-title-with-link" }>
                <a href=""></a>
            </span>
        );
    } else {
        title = <span className={ prefix + "-location-title" }></span>;
    }
    
    let address = <></>;
    if(showLocationAddress) {
        address = <span className={ prefix + "-location-address" }></span>;
    }
    
    let seperator = <></>;
    if(showLocationAddress) {
        seperator = (
            <span className={ prefix + "-location-seperator" }>{
                "//"
            }</span>
        );
    }
    
    return (
        <div className={ prefix + "-location" }>
            { title }
            { seperator }
            { address }
        </div>
    );
}

export function Excerpt({ attributes }) {
    const { showExcerpt, prefix } = attributes;
    if(! showExcerpt) return (<></>);
    
    return (
        <div className={ prefix + "-excerpt" }><p></p></div>
    );
}

export function Content({ attributes }) {
    const { showContent, prefix } = attributes;
    if(! showContent) return (<></>);
    
    return (
        <div className={ prefix + "-content" }></div>
    );
}

export function Seperator({ attributes }) {
    const { showSeperator, prefix } = attributes;
    if(! showSeperator) return (<></>);
    
    return (
        <hr className={ prefix + "-seperator" } />
    );
}