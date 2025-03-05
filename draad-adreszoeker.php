<?php
/**
 * Plugin Name:       Draad Adreszoeker
 * Plugin URI:        https://draad.nl/
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Draad Internet &amp; Media B.V.
 * Text Domain:       draad-az
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

		private $version;

		public function __construct() {

			// Register block
			add_action( 'init', [ $this, 'register_block' ] );

			// Register assets
			add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

			// Register ajax handler.
			add_action( 'wp_ajax_nopriv_draad_adreszoeker_get_advice', [ $this, 'get_advice' ] );
			add_action( 'wp_ajax_draad_adreszoeker_get_advice', [ $this, 'get_advice' ] );
			add_action( 'wp_ajax_nopriv_draad_adreszoeker_get_streets', [ $this, 'get_streets' ] );
			add_action( 'wp_ajax_draad_adreszoeker_get_streets', [ $this, 'get_streets' ] );

		}

		public function register_block() {
			register_block_type( __DIR__ . '/build/draad-adreszoeker' );
		}

		public function register_assets() {

			// Tabs
			wp_register_script( 'draad-tabs-script', plugin_dir_url( __FILE__ ) . 'build/js/draad-tabs.js', [], $this->version, true );
			wp_register_style( 'draad-tabs-style', plugin_dir_url( __FILE__ ) . 'build/css/style.css', [], $this->version );

			// Toggle
			wp_register_script( 'draad-toggle-script', plugin_dir_url( __FILE__ ) . 'build/js/draad-tabs.js', [], $this->version, true );
			wp_register_style( 'draad-toggle-style', plugin_dir_url( __FILE__ ) . 'build/css/style.css', [], $this->version );

		}

		public function get_advice() {

			wp_send_json_success( __( 'Resultaten successvol opgehaald.', 'draad-az' ) );

		}

		public function get_streets() {

			wp_send_json_success( __( 'Mooie lijst met straten.', 'draad-az' ) );

		}

	}

}

if ( class_exists( 'Draad_Adreszoeker' ) ) {
	new Draad_Adreszoeker();
}