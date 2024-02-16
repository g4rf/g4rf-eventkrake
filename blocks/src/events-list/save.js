import { useBlockProps } from '@wordpress/block-editor';

import * as List from './list-events';

export default function Save({ attributes }) {
    return (
        <div { ...useBlockProps.save() } >
            
            { /*List.html()*/ }
            
        </div>
    );
}

