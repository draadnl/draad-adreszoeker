<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<div class="draad-adreszoeker">
	<form action="post">
		<search class="draad-adreszoeker__filters">
			<div class="draad-adreszoeker__filter --street">
				<label for="street"><?php esc_attr_e('Straatnaam', 'draad-az') ?></label>
				<input list="street-list" id="street" name="street" placeholder="<?php esc_attr_e('Vul uw straatnaam in.', 'draad-az') ?>" autocomplete="off" required>
				<datalist id="street-list" class="draad-adreszoeker__suggestions"></datalist>
			</div>
	
			<div class="draad-adreszoeker__filter --number">
				<label for="huisnummer"><?php esc_attr_e('Huisnummer (zonder toevoeging)', 'draad-az') ?></label>
				<input type="number" id="huisnummer" name="huisnummer" placeholder="<?php esc_attr_e('Vul uw huisnummer in.', 'draad-az') ?>" max="9999" disabled required>
			</div>

			<input type="hidden" name="admin-ajax" value="<?= admin_url('admin-ajax.php'); ?>">
			<input type="hidden" name="action" value="draad_adreszoeker_get_advice">
			<input class="draad-adreszoeker__submit" type="submit" value="<?php esc_attr_e('Bekijk resultaat', 'draad-az') ?>" />
		</search>
		<output class="draad-adreszoeker__output"></output>
	</form>
</div> 