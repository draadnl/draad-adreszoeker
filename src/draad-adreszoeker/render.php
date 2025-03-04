<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<div class="draad-adreszoeker">
	<search class="draad-adreszoeker__filters filters">
		<form action="">
			<div class="draad-adreszoeker__filter">
				<label for="street"><?php esc_attr_e('Straatnaam', 'draad-adreszoeker') ?></label>
				<input list="street-list" id="street" name="street" placeholder="<?php esc_attr_e('Vul uw straatnaam in.', 'draad-adreszoeker') ?>" autocomplete="off">
				<datalist id="street-list"></datalist>
			</div>
	
			<div class="draad-adreszoeker__filter">
				<label for="huisnummer"><?php esc_attr_e('Huisnummer (zonder toevoeging)', 'draad-adreszoeker') ?></label>
				<input type="number" id="huisnummer" name="huisnummer" placeholder="<?php esc_attr_e('Vul uw huisnummer in.', 'draad-adreszoeker') ?>" max="9999" disabled>
			</div>

			<input type="submit" value="<?php esc_attr_e('Bekijk resultaat', 'draad-adreszoeker') ?>" />
		</form>
	</search>
	<output id="results" class="draad-adreszoeker__content content"></output>
</div> 