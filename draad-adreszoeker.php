<?php
/**
 * Plugin Name:       	Draad Adreszoeker
 * Description: 		Dit is de plugin waarmee adressen kunnen worden gevonden. Op basis van het gevonden adres worden bijbehorende teksten en adviezen ingeladen.
 * Plugin URI:        	https://draad.nl/
 * Version:           	1.0.0-DEV
 * Requires at least: 	6.7
 * Requires PHP:      	7.4
 * Requires Plugins: 	advanced-custom-fields-pro
 * Author:            	Draad Internet &amp; Media B.V.
 * Author URI: 			https://draad.nl/
 * Text Domain:       	draad-az
 *
 * @package DraadAdreszoeker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'DRAAD_ADRESZOEKER_DIR' ) ) {
    define( 'DRAAD_ADRESZOEKER_DIR', plugin_dir_path( __FILE__ ) ); // Define the plugin directory path
}

if ( ! defined( 'DRAAD_ADRESZOEKER_URL' ) ) {
    define( 'DRAAD_ADRESZOEKER_URL', plugin_dir_url( __FILE__ ) ); // Define the plugin directory URL
}

require_once DRAAD_ADRESZOEKER_DIR . 'includes/acf/acf.php';
require_once DRAAD_ADRESZOEKER_DIR . 'includes/post-types.php';

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

if ( !class_exists( 'Draad_Adreszoeker' ) ) {
	
	class Draad_Adreszoeker {

		private static $instance = null;

		private $version;

		public static function get_instance() {
			if ( self::$instance === null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {

			// Save Plugin data
			add_action( 'wp', [ $this, 'set_plugin_data' ] );
			register_activation_hook( __FILE__, [ $this, 'set_plugin_data' ] );

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

		public function set_plugin_data() {
			$pluginData = get_plugin_data( __FILE__ );
			$this->version = $pluginData['Version'];

			if ( get_option( 'draad_az_version' ) !== $this->version ) {
				update_option( 'draad_az_version', $this->version );
			}
		}

		public function register_block() {
			register_block_type( __DIR__ . '/build/draad-adreszoeker' );
		}

		public function register_assets() {

			// Tabs
			wp_register_script( 'draad-tabs-script', plugin_dir_url( __FILE__ ) . 'build/js/draad-tabs.js', [], $this->version, true );

			// Toggle
			wp_register_script( 'draad-toggle-script', plugin_dir_url( __FILE__ ) . 'build/js/draad-toggle.js', [], $this->version, true );

		}

		public function get_streets() {

			$streetQuery = filter_input( INPUT_POST,'street', FILTER_SANITIZE_STRING );
			$streetQuery = preg_replace( '/[^\w\s]/u', '', $streetQuery );

			if (  empty( $streetQuery ) || strlen( $streetQuery ) < 2 ) {
				wp_send_json_error('Straatnaam moet minimaal 2 karakters bevatten.');
			}

			global $wpdb;

			$query = $wpdb->prepare(
				'SELECT DISTINCT street FROM wp_draad_az_addresses WHERE street LIKE "%%%s%%"',
				$streetQuery
			);

			$results = $wpdb->get_results( $query, ARRAY_A );

			wp_send_json($results ?: []);

			wp_send_json_success( __( 'Mooie lijst met straten.', 'draad-az' ) );

		}

		public function get_advice() {

			$streetQuery = filter_input( INPUT_POST,'street', FILTER_SANITIZE_STRING );
			$streetQuery = preg_replace( '/[^\w\s]/u', '', $streetQuery );

			if (  empty( $streetQuery ) || strlen( $streetQuery ) < 2 ) {
				wp_send_json_error(__( 'Straatnaam moet minimaal 2 karakters bevatten.', 'draad-az' ));
			}

			$number = (int) filter_input( INPUT_POST,'number', FILTER_SANITIZE_NUMBER_INT ) ?: 0;

			if ( !$number ) {
				wp_send_json_error( __( 'Ongeldig huisnummer opgegeven.', 'draad-az' ) );
			}

			global $wpdb;

			$query = $wpdb->prepare(
				'SELECT * FROM wp_draad_az_addresses WHERE street = "%s" AND huisnummer = "%d" LIMIT 1',
				$streetQuery,
				$number
			);

			$neighbourhood = $wpdb->get_row( $query, ARRAY_A );
			ob_start();
			require_once DRAAD_ADRESZOEKER_DIR . 'templates/grid-container.php';
			$output = ob_get_clean();

			wp_send_json_success($output);

			wp_send_json_success( __( 'Resultaten successvol opgehaald.', 'draad-az' ) );

		}

	}

}

if ( class_exists( 'Draad_Adreszoeker' ) ) {
	Draad_Adreszoeker::get_instance();
}