import { registerBlockType } from '@wordpress/blocks';
import { siteLogo } from '@wordpress/icons';
import metadata from './block.json';
import Edit from './edit';

registerBlockType( metadata.name, {
	icon: siteLogo,
	edit: Edit,
	save: () => null,
} );
