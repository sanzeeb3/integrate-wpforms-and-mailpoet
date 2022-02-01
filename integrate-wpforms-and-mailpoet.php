<?php
/**
 * Plugin Name: Integrate WPForms and MailPoet
 * Description: Add subscribers from WPForms to MailPoet.
 * Version: 1.0.0
 * Author: Sanjeev Aryal
 * Author URI: https://www.sanjeebaryal.com.np
 * Text Domain: wpforms-mailpoet-integration
 */

defined( 'ABSPATH' ) || die();

define( 'WPFORMS_MAILPOET_INTEGRATION_PLUGIN_FILE', __FILE__ );
define( 'WPFORMS_MAILPOET_INTEGRATION_PLUGIN_PATH', __DIR__ );

require_once WPFORMS_MAILPOET_INTEGRATION_PLUGIN_PATH . '/src/Plugin.php';

/**
 * Return the main instance of Plugin Class.
 *
 * @since  1.0.0
 *
 * @return Plugin.
 */
function wpforms_mailpoet_integration() {
    $instance = \WPFormsMailPoetIntegration\Plugin::get_instance();

    $instance->init();

    return $instance;

}

wpforms_mailpoet_integration();