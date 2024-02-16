import metadata from './block.json';

export function BackendLabel() {
    return ( <div>{ metadata.title }</div> );
}

export function Content({ attributes }) {
    const { showContent, prefix } = attributes;
    if(! showContent) return (<></>);
    
    return (
        <div className={ prefix + "-content" }></div>
    );
}