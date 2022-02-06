<?php
/**
 * Plugin Name: WPForms MailPoet
 * Description: MailPoet integration with WPForms.
 * Version: 1.0.0
 * Author: Sanjeev Aryal
 * Author URI: https://www.sanjeebaryal.com.np
 * Text Domain: wpforms-mailpoet
 */

defined( 'ABSPATH' ) || die();

define( 'WPFORMS_MAILPOET_PLUGIN_FILE', __FILE__ );
define( 'WPFORMS_MAILPOET_PLUGIN_PATH', __DIR__ );

/**
 * Plugin version.
 */
const WPFORMS_MAILPOET_VERSION = '1.0.0';

add_action(
	'wpforms_loaded',
	function() {

	require_once WPFORMS_MAILPOET_PLUGIN_PATH . '/src/class-mailpoet.php';
    
    // Load translated strings.
    load_plugin_textdomain( 'wpforms-mailpoet', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}
);
