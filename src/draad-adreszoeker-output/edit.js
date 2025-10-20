/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import { PanelBody, PanelRow, Button as WPButton, TextControl } from '@wordpress/components';

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
import { ResponsiveContent, SheetContainer, Button, ArrowRightIcon, StylesProvider } from '@gemeente-denhaag/components-react';

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
		attributes: { heading, button, image },
		setAttributes,
	} = props;

	const blockProps = useBlockProps({
		className: 'utrecht-theme denhaag-theme draad-adreszoeker'
	});

	const onChangeHeading = ( text ) => {
		setAttributes( { heading: text } );
	};

	const onChangeButton = ( text ) => {
		setAttributes( { button: text } );
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
				<PanelBody title={ __( 'Instellingen', 'draad-adreszoeker' ) } initialOpen={ true }>
					<PanelRow>
						<div style={{ width: '100%' }}>
							<TextControl
								label={ __( 'Button tekst', 'draad-adreszoeker' ) }
								value={ button }
								onChange={ onChangeButton }
								placeholder={ __( 'Bekijk', 'draad-adreszoeker' ) }
							/>
						</div>
					</PanelRow>
					<PanelRow>
						<div style={{ width: '100%' }}>
							<label style={{ marginBottom: '8px', display: 'block', fontWeight: '600' }}>
								{ __( 'Afbeelding', 'draad-adreszoeker' ) }
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
											{ image.url ? __( 'Vervang afbeelding', 'draad-adreszoeker' ) : __( 'Selecteer afbeelding', 'draad-adreszoeker' ) }
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
									{ __( 'Verwijder afbeelding', 'draad-adreszoeker' ) }
								</WPButton>
							) }
						</div>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<StylesProvider>
				{ image.url && (
					<Image
						alt={ image.alt }
						width={1920}
						height={390}
						photo
						src={ image.url }
						className="draad-adreszoeker__banner"
						/>
				) }
				<ResponsiveContent>
					<SheetContainer>
						<Surface className="draad-adreszoeker__form">
							<div>
								<RichText
									tagName="h1"
									className="nl-heading nl-heading--level-2"
									onChange={ onChangeHeading }
									value={ heading }
									/>
								<form action="post">
									<FormField
										type="text"
										className="draad-adreszoeker__field --street"
										>
										<FormLabel
											className="utrecht-form-field__label"
											htmlFor="street"
											>
											{ __( 'Straatnaam', 'draad-adreszoeker' ) }
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
											{ __( 'Huisnummer', 'draad-adreszoeker' ) }
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
									<input type="hidden" name="action" value="draad_adreszoeker_get_advice"></input>
									<Button
										icon={<ArrowRightIcon />}
										iconAlign="end"
										type="submit"
										>
										{ button }
									</Button>
								</form>
							</div>
						</Surface>
		
						<div className="draad-adreszoeker__output">
						</div>
					</SheetContainer>
				</ResponsiveContent>
			</StylesProvider>
		</div>
	);
}
