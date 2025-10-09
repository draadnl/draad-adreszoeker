/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import { Surface, Heading1, Image, Paragraph, FormField, FormLabel, Textbox } from '@utrecht/component-library-react';
import { ResponsiveContent, SheetContainer, Button, ArrowRightIcon, StylesProvider } from '@gemeente-denhaag/components-react';

import './style.scss';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save( props ) {
	const {
		attributes: { heading, content, button, image },
	} = props;

	const blockProps = useBlockProps.save({
		className: 'utrecht-theme denhaag-theme draad-adreszoeker'
	});

	const ajaxUrl = '/wp-admin/admin-ajax.php';
	
		return (
			<div { ...blockProps }>
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
									<Heading1 className="utrecht-heading-2">{ heading }</Heading1>
									<Paragraph>{ content }</Paragraph>
									<form action="post">
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
							</Surface>
				
							<div className="draad-adreszoeker__output">
							</div>
						</SheetContainer>
					</ResponsiveContent>
				</StylesProvider>
			</div>
		);
}
