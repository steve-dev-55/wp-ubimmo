<?php

/**
 * @package  Ubimmo
 */
/*
Plugin Name: Ubimmo 
Plugin URI: 
Description: Ubimmo est un plugin WordPress qui permet d'importer automatiquement des annonces immobilières sur Ubiflow. 
Version: 0.0.1
Author: Steve Djoumessi
Author URI: 
License: GPLv2 or later
Text Domain: Ubimmo
*/

// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
function activate_ubimmo_plugin() {
	Inc\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'activate_ubimmo_plugin' );

/**
 * The code that runs during plugin deactivation
 */
function deactivate_ubimmo_plugin() {
	Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_ubimmo_plugin' );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Inc\\Init' ) ) {
	Inc\Init::register_services();
}