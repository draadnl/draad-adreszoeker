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

import { Surface, Image, Heading2, Paragraph, FormField, FormLabel, Textbox } from '@utrecht/component-library-react';
import { Button, ArrowRightIcon, StylesProvider } from '@gemeente-denhaag/components-react';

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
		attributes: { heading, content, button, postUrl, image },
	} = props;

	const blockProps = useBlockProps.save({
		className: 'utrecht-theme denhaag-theme draad-adreszoeker'
	});

	const ajaxUrl = '/wp-admin/admin-ajax.php';

	return (
		<div { ...blockProps }>
			<StylesProvider>
				<Surface className="draad-adreszoeker__form">
					<div>
						<Heading2>{ heading }</Heading2>
						<Paragraph className="utrecht-paragraph--lead">{ content }</Paragraph>
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
