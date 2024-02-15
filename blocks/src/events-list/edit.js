import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
} from '@wordpress/components';
import { 
	useBlockProps,
} from '@wordpress/block-editor';

import * as loader from './list-events';

export default function Edit({ attributes, setAttributes, isSelected, clientId, 
        context: { postType, postId, queryId } }) 
{
    return loader.html(useBlockProps(), true);
}
