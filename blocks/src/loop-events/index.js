import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import "./style.scss";
import Edit from './edit';
import Save from './save';

const icon = (
    <svg version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g><g transform="translate(0 .44974)"><circle cx="6.1416" cy="6.1019" r="5.1081" fill="#8a0"/><circle cx="6.1814" cy="16.954" r="5.1081" fill="#08a"/></g><path d="m19.739 17.323h-8.366c-1.3182 0-2.3903 0.897-2.3903 2s1.072 2 2.3903 2h8.366c1.3182 0 2.3903-0.897 2.3903-2s-1.072-2-2.3903-2z" stroke-width="1.0932"/><path d="m19.739 10.323h-8.366c-1.3182 0-2.3903 0.897-2.3903 2s1.072 2 2.3903 2h8.366c1.3182 0 2.3903-0.897 2.3903-2s-1.072-2-2.3903-2z" stroke-width="1.0932"/><path d="m19.739 3.323h-8.366c-1.3182 0-2.3903 0.897-2.3903 2s1.072 2 2.3903 2h8.366c1.3182 0 2.3903-0.897 2.3903-2s-1.072-2-2.3903-2z" stroke-width="1.0932"/><circle cx="5.414" cy="19.323" r="2.5"/><circle cx="5.414" cy="12.323" r="2.5"/><circle cx="5.414" cy="5.323" r="2.5"/></g></svg>
);

registerBlockType( metadata.name, {
    icon: icon,
	edit: Edit,
	save: Save
} );