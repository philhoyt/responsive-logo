import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Button,
	ButtonGroup,
	PanelBody,
	Placeholder,
	RangeControl,
	ToggleControl,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const {
		desktopLogoId,
		desktopLogoUrl,
		desktopLogoAlt,
		mobileLogoId,
		mobileLogoUrl,
		mobileLogoAlt,
		breakpoint,
		maxWidth,
		linkToHome,
	} = attributes;

	const [ showMobile, setShowMobile ] = useState( false );

	const previewUrl = showMobile
		? mobileLogoUrl || desktopLogoUrl
		: desktopLogoUrl || mobileLogoUrl;
	const previewAlt = showMobile
		? mobileLogoAlt || desktopLogoAlt
		: desktopLogoAlt || mobileLogoAlt;

	const blockProps = useBlockProps( {
		className: 'responsive-site-logo-editor',
	} );

	function renderLogoSlot( { label, id, url, alt, onSelect, onRemove } ) {
		return (
			<div className="rsl-editor__slot">
				<p className="rsl-editor__slot-label">{ label }</p>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ onSelect }
						allowedTypes={ [ 'image' ] }
						value={ id }
						render={ ( { open } ) =>
							url ? (
								<div className="rsl-editor__preview">
									<img src={ url } alt={ alt } />
									<div className="rsl-editor__preview-actions">
										<Button
											variant="secondary"
											size="small"
											onClick={ open }
										>
											{ __(
												'Replace',
												'responsive-site-logo'
											) }
										</Button>
										<Button
											variant="link"
											size="small"
											isDestructive
											onClick={ onRemove }
										>
											{ __(
												'Remove',
												'responsive-site-logo'
											) }
										</Button>
									</div>
								</div>
							) : (
								<div className="rsl-editor__upload">
									<Button
										variant="secondary"
										onClick={ open }
									>
										{ __(
											'Select Image',
											'responsive-site-logo'
										) }
									</Button>
								</div>
							)
						}
					/>
				</MediaUploadCheck>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Images', 'responsive-site-logo' ) }>
					{ renderLogoSlot( {
						label: __( 'Desktop Logo', 'responsive-site-logo' ),
						id: desktopLogoId,
						url: desktopLogoUrl,
						alt: desktopLogoAlt,
						onSelect: ( media ) =>
							setAttributes( {
								desktopLogoId: media.id,
								desktopLogoUrl: media.url,
								desktopLogoAlt: media.alt || '',
								desktopLogoType: media.mime || '',
							} ),
						onRemove: () =>
							setAttributes( {
								desktopLogoId: undefined,
								desktopLogoUrl: '',
								desktopLogoAlt: '',
								desktopLogoType: '',
							} ),
					} ) }

					{ renderLogoSlot( {
						label: __( 'Mobile Logo', 'responsive-site-logo' ),
						id: mobileLogoId,
						url: mobileLogoUrl,
						alt: mobileLogoAlt,
						onSelect: ( media ) =>
							setAttributes( {
								mobileLogoId: media.id,
								mobileLogoUrl: media.url,
								mobileLogoAlt: media.alt || '',
								mobileLogoType: media.mime || '',
							} ),
						onRemove: () =>
							setAttributes( {
								mobileLogoId: undefined,
								mobileLogoUrl: '',
								mobileLogoAlt: '',
								mobileLogoType: '',
							} ),
					} ) }
				</PanelBody>

				<PanelBody title={ __( 'Settings', 'responsive-site-logo' ) }>
					<RangeControl
						label={ __(
							'Mobile Breakpoint (px)',
							'responsive-site-logo'
						) }
						value={ breakpoint }
						onChange={ ( value ) =>
							setAttributes( { breakpoint: value } )
						}
						min={ 320 }
						max={ 1200 }
						step={ 10 }
						help={ __(
							'Screen widths at or below this value will show the mobile logo.',
							'responsive-site-logo'
						) }
					/>
					<RangeControl
						label={ __( 'Max Width (px)', 'responsive-site-logo' ) }
						value={ maxWidth || 0 }
						onChange={ ( value ) =>
							setAttributes( {
								maxWidth: value > 0 ? value : undefined,
							} )
						}
						min={ 0 }
						max={ 800 }
						step={ 10 }
						help={ __(
							'Set to 0 for no limit.',
							'responsive-site-logo'
						) }
					/>
					<ToggleControl
						label={ __(
							'Link to Homepage',
							'responsive-site-logo'
						) }
						checked={ linkToHome }
						onChange={ ( value ) =>
							setAttributes( { linkToHome: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ previewUrl ? (
					<div className="rsl-editor__canvas-preview">
						<img
							src={ previewUrl }
							alt={ previewAlt }
							style={
								maxWidth ? { maxWidth: `${ maxWidth }px` } : {}
							}
						/>
						{ desktopLogoUrl && mobileLogoUrl && (
							<div className="rsl-editor__preview-toggle">
								<ButtonGroup>
									<Button
										variant={
											! showMobile
												? 'primary'
												: 'secondary'
										}
										size="small"
										onClick={ () => setShowMobile( false ) }
									>
										{ __(
											'Desktop',
											'responsive-site-logo'
										) }
									</Button>
									<Button
										variant={
											showMobile ? 'primary' : 'secondary'
										}
										size="small"
										onClick={ () => setShowMobile( true ) }
									>
										{ __(
											'Mobile',
											'responsive-site-logo'
										) }
									</Button>
								</ButtonGroup>
							</div>
						) }
					</div>
				) : (
					<Placeholder
						icon="format-image"
						label={ __(
							'Responsive Site Logo',
							'responsive-site-logo'
						) }
						instructions={ __(
							'Select desktop and mobile logos in the sidebar.',
							'responsive-site-logo'
						) }
					/>
				) }
			</div>
		</>
	);
}
