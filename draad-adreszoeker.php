<?php
/**
 * Plugin Name:       	Draad Adreszoeker
 * Description: 		Dit is de plugin waarmee adressen kunnen worden gevonden. Op basis van het gevonden adres worden bijbehorende teksten en adviezen ingeladen.
 * Plugin URI:        	https://draad.nl/
 * Version:           	2.0.0
 * Requires at least: 	6.8
 * Requires PHP:      	8.1
 * Requires Plugins: 	advanced-custom-fields-pro
 * Author:            	Draad Internet &amp; Media B.V.
 * Author URI: 			https://draad.nl/
 * Text Domain:       	draad-adreszoeker
 *
 * @package DraadAdreszoeker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'DRAAD_ADRESZOEKER_FILE' ) ) {
    define( 'DRAAD_ADRESZOEKER_FILE', __FILE__ ); // Define the plugin directory path
}

if ( ! defined( 'DRAAD_ADRESZOEKER_DIR' ) ) {
    define( 'DRAAD_ADRESZOEKER_DIR', plugin_dir_path( __FILE__ ) ); // Define the plugin directory path
}

if ( ! defined( 'DRAAD_ADRESZOEKER_URL' ) ) {
    define( 'DRAAD_ADRESZOEKER_URL', plugin_dir_url( __FILE__ ) ); // Define the plugin directory URL
}

// require_once 'vendor/autoload.php';
require_once DRAAD_ADRESZOEKER_DIR . 'includes/acf/acf.php';
require_once DRAAD_ADRESZOEKER_DIR . 'includes/post-types.php';
require_once DRAAD_ADRESZOEKER_DIR . 'includes/class-draad-adreszoeker-admin.php';
require_once DRAAD_ADRESZOEKER_DIR . 'includes/class-draad-adreszoeker-import.php';
require_once DRAAD_ADRESZOEKER_DIR . 'includes/class-draad-adreszoeker.php';

if ( class_exists( 'Draad_Adreszoeker' ) ) {
	Draad_Adreszoeker::get_instance();
}

if ( class_exists( 'Draad_Adreszoeker_Import' ) ) {
    $importer = new Draad_Adreszoeker_Import();
}

if ( class_exists( 'Draad_Adreszoeker_Admin' ) ) {
    $admin = new Draad_Adreszoeker_Admin();
}