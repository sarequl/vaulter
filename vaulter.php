<?php
/**
 * @package vaulter
 * @version 1.0.0
 */
/*
Plugin Name: Vaulter
Plugin URI: https://github.com/sarequl/vaulter
Description: Vaulter is an easy management system for WordPress websites. It will improve the wordpress website developer/owner experience.
Version: 1.0.0
Author: Sarequl Basar
Author URI: https://sarequl.me
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0
 */
define( 'vaulter', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_vaulter() {
	
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_vaulter() {
	
}

//admin panel
require plugin_dir_path( __FILE__ ) . 'admin/admin_options.php';

//includes
require plugin_dir_path( __FILE__ ) . 'includes/login_action.php';

