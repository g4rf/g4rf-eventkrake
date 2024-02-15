import { useBlockProps } from '@wordpress/block-editor';

import * as loader from './list-events';

export default function Save({ attributes }) {
    return loader.html(useBlockProps.save());
}

