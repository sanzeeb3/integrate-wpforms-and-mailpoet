<?php

/**
 * MailPoet integration.
 *
 * @since 1.0.0
 */
class WPForms_MailPoet extends WPForms_Provider {

    public $connect;

    /**
     * Initialize.
     *
     * @since 1.0.0
     */
    public function init() {

        $this->version  = WPFORMS_MAILPOET_VERSION;
        $this->name     = 'MailPoet';
        $this->slug     = 'mailpoet';
        $this->priority = 0.5;
        $this->icon     = plugins_url( 'assets/mailpoet.png', WPFORMS_MAILPOET_PLUGIN_FILE );

         if (class_exists(\MailPoet\API\API::class)) {
            $this->connect = \MailPoet\API\API::MP('v1');
        }
    }
    
    /**
     * Retrieve MailPoet lists.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function api_lists( $connection_id = '', $account_id = 'mailpoet-account-id' ) {
        return $this->connect->getLists();
    }

    /**
     * Retrieve MailPoet fields.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function api_fields( $connection_id = '', $account_id = 'mailpoet-account-id', $list_id = '' ) {
        return $this->connect->getSubscriberFields();
    }

    /**
     * Authenticate with the API.
     *
     * @param array $data
     * @param string $form_id
     *
     * @return mixed id or WP_Error object.
     */
    public function api_auth( $data = array(), $form_id = '' ) {

        $id                              = uniqid();
        $providers                       = get_option( 'wpforms_providers', array() );
        $providers[ $this->slug ][ $id ] = array(
            'api'       => trim( $id ),
            'date'      => time(),
        );

        update_option( 'wpforms_providers', $providers );

        return $id;
    }

    /**
     * Establish connection object to API.
     *
     * @since 1.0.0
     *
     * @param string $account_id
     *
     * @return mixed array or WP_Error object.
     */
    public function api_connect( $account_id ) {

        return 'mailpoet-account-id';
    }

    /*************************************************************************
     * Output methods - these methods generally return HTML for the builder. *
     *************************************************************************/

    /**
     * Provider account authorize fields HTML.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function output_auth() {
    }
}

new WPForms_MailPoet;