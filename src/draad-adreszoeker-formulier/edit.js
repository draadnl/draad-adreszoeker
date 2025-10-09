/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import { SelectControl, PanelBody, PanelRow, Button as WPButton, TextControl } from '@wordpress/components';

import { useSelect } from '@wordpress/data';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls, RichText, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import { Surface, Image, Paragraph, FormField, FormLabel, Textbox } from '@utrecht/component-library-react';
import { Button, ArrowRightIcon, StylesProvider } from '@gemeente-denhaag/components-react';

import './style.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( props ) {

	const {
		attributes: { heading, content, button, post, postUrl, image },
		setAttributes,
	} = props;

	const { pages, currentPostUrl } = useSelect( ( select ) => {
		const { getEntityRecords, getEntityRecord } = select( 'core' );

		// Query args
		const query = {
			status: 'publish',
			per_page: -1
		}

		let currentPostUrl = '';
		if ( post ) {
			const postData = getEntityRecord( 'postType', 'page', post );
			currentPostUrl = postData?.link || '';
		}

		return {
			pages: getEntityRecords( 'postType', 'page', query ),
			currentPostUrl: currentPostUrl
		}
	}, [ post ] )

	// Update postUrl attribute when currentPostUrl changes
	if ( currentPostUrl && currentPostUrl !== postUrl ) {
		setAttributes( { postUrl: currentPostUrl } );
	}

	// populate options for <SelectControl>
	let options = [];
	if( pages ) {
		options.push( { value: '0', label: 'Selecteren...' } )
		pages.forEach( ( page ) => {
			options.push( { value : String(page.id), label : page.title.rendered } )
		})
	} else {
		options.push( { value: '0', label: 'Loading...' } )
	}

	const blockProps = useBlockProps({
		className: 'utrecht-theme denhaag-theme draad-adreszoeker'
	});

	const onChangeHeading = ( text ) => {
		setAttributes( { heading: text } );
	};

	const onChangeContent = ( text ) => {
		setAttributes( { content: text } );
	};

	const onChangeButton = ( text ) => {
		setAttributes( { button: text } );
	};

	const onChangePost = ( value ) => {
		const postId = parseInt(value);
		setAttributes( { post: postId, postUrl: '' } );
	};

	const onChangeImage = ( media ) => {
		setAttributes( { 
			image: {
				url: media ? media.url : '',
				alt: media ? media.alt : '',
				id: media ? media.id : null
			}
		} );
	};

	const ajaxUrl = '/wp-admin/admin-ajax.php';

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Instellingen', 'draad-az' ) } initialOpen={ true }>
					<PanelRow>
						<div style={{ width: '100%' }}>
							<SelectControl 
								label={ __( 'Redirect pagina', 'draad-az' ) }
								options={ options } 
								value={ String(post) }
								onChange={ onChangePost }
							/>
						</div>
					</PanelRow>
					<PanelRow>
						<div style={{ width: '100%' }}>
							<TextControl
								label={ __( 'Button tekst', 'draad-az' ) }
								value={ button }
								onChange={ onChangeButton }
								placeholder={ __( 'Bekijk', 'draad-az' ) }
							/>
						</div>
					</PanelRow>
					<PanelRow>
						<div style={{ width: '100%' }}>
							<label style={{ marginBottom: '8px', display: 'block', fontWeight: '600' }}>
								{ __( 'Afbeelding', 'draad-az' ) }
							</label>
							{ image.url && (
								<div style={{ marginBottom: '12px' }}>
									<img 
										src={ image.url } 
										alt={ image.alt }
										style={{ width: '100%', height: 'auto', maxHeight: '200px', objectFit: 'cover' }}
									/>
								</div>
							) }
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ onChangeImage }
									allowedTypes={ ['image' ] }
									value={ image.id }
									render={ ({ open }) => (
										<WPButton
											onClick={ open }
											variant={ image.url ? 'secondary' : 'primary' }
											style={{ marginBottom: '8px' }}
										>
											{ image.url ? __( 'Vervang afbeelding', 'draad-az' ) : __( 'Selecteer afbeelding', 'draad-az' ) }
										</WPButton>
									) }
								/>
							</MediaUploadCheck>
							{ image.url && (
								<WPButton
									onClick={ () => onChangeImage( null ) }
									variant="link"
									isDestructive
								>
									{ __( 'Verwijder afbeelding', 'draad-az' ) }
								</WPButton>
							) }
						</div>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<StylesProvider>
				<Surface className="draad-adreszoeker__form">
					<div>
						<RichText
							tagName="h2"
							className="nl-heading nl-heading--level-2"
							onChange={ onChangeHeading }
							value={ heading }
							/>
						<RichText
							tagName="p"
							className="utrecht-paragraph utrecht-paragraph--lead"
							onChange={ onChangeContent }
							value={ content }/>
						<form action="post" data-redirect={ postUrl }>
							<FormField
								type="text"
								className="draad-adreszoeker__field --street"
								>
								<FormLabel
									className="utrecht-form-field__label"
									htmlFor="street"
									>
									{ __( 'Straatnaam', 'draad-az' ) }
								</FormLabel>
								<Paragraph className="utrecht-form-field__input">
									<Textbox
										id="street"
										name="straatnaam"
										required
										type="text"
										defaultValue=""
										list="street-list"
										/>
									<datalist id="street-list" className="draad-adreszoeker__suggestions"></datalist>
								</Paragraph>
							</FormField>
							<FormField
								type="text"
								className="draad-adreszoeker__field --housenumber"
								>
								<FormLabel
									className="utrecht-form-field__label"
									htmlFor="huisnummer"
									>
									{ __( 'Huisnummer', 'draad-az' ) }
								</FormLabel>
								<Paragraph className="utrecht-form-field__input">
									<Textbox
										id="huisnummer"
										name="huisnummer"
										required
										type="number"
										defaultValue=""
										max="9999"
										/>
								</Paragraph>
							</FormField>
							<input type="hidden" name="admin-ajax" value={ ajaxUrl }></input>
							<input type="hidden" name="action" value="draad_adreszoeker_get_advice_react"></input>
							<Button
								icon={<ArrowRightIcon />}
								iconAlign="end"
								type="submit"
								>
								{ button }
							</Button>
						</form>
					</div>
					{ image.url && (
						<Image
							alt={ image.alt }
							height={194}
							photo
							src={ image.url }
							width={304}
							/>
					) }
				</Surface>
			</StylesProvider>
		</div>
	);
}
