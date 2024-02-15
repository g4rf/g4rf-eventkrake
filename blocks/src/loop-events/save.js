import { useBlockProps } from '@wordpress/block-editor';

import getHTML from './loop-events';

export default function Save({ attributes }) {
    return getHTML(useBlockProps.save());
}

