<?php
/**
 * Plugin Name:       Draad Adreszoeker
 * Plugin URI:        https://draad.nl/
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Draad Internet &amp; Media B.V.
 * Text Domain:       draad-adreszoeker
 *
 * @package DraadAdreszoeker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function draad_adreszoeker_block_init() {
	register_block_type( __DIR__ . '/build/draad-adreszoeker' );
}
add_action( 'init', 'draad_adreszoeker_block_init' );
