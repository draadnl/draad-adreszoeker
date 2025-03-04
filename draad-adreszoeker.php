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

if ( !class_exists( 'Draad_Adreszoeker' ) ) {
	
	class Draad_Adreszoeker {

		public function __construct() {

			// Register block
			add_action( 'init', [ $this, 'register_block' ] );

			// Register ajax handler.
			add_action( 'wp_ajax_nopriv_draad_adreszoeker', [ $this, 'handler' ] );
			add_action( 'wp_ajax_draad_adreszoeker', [ $this, 'handler' ] );
			add_action( 'wp_ajax_nopriv_draad_adreszoeker_get_streets', [ $this, 'get_streets' ] );
			add_action( 'wp_ajax_draad_adreszoeker_get_streets', [ $this, 'get_streets' ] );

		}

		public function register_block() {
			register_block_type( __DIR__ . '/build/draad-adreszoeker' );
		}

		public function handler() {

			wp_send_json_success( __( 'Resultaten successvol opgehaald.', 'draad-adreszoeker' ) );

		}

		public function get_streets() {

			wp_send_json_success( __( 'Mooie lijst met straten.', 'draad-adreszoeker' ) );

		}

	}

}

if ( class_exists( 'Draad_Adreszoeker' ) ) {
	new Draad_Adreszoeker();
}