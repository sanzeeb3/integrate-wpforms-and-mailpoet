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

require_once WPFORMS_MAILPOET_PLUGIN_PATH . '/src/Plugin.php';

/**
 * Return the main instance of Plugin Class.
 *
 * @since  1.0.0
 *
 * @return Plugin.
 */
function wpforms_mailpoet() {
    $instance = \WPFormsMailPoet\Plugin::get_instance();

    $instance->init();

    return $instance;

}
wpforms_mailpoet();