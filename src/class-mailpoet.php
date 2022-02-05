<?php

/**
 * MailPoet integration.
 *
 * @since 1.0.0
 */
class WPForms_MailPoet extends WPForms_Provider {

	/**
	 * Mailpoet API object
	 */
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

		if ( class_exists( \MailPoet\API\API::class ) ) {
			$this->connect = \MailPoet\API\API::MP( 'v1' );
		}
	}

	/**
	 * Process and submit entry to provider.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields
	 * @param array $entry
	 * @param array $form_data
	 * @param int $entry_id
	 */
	public function process_entry( $fields, $entry, $form_data, $entry_id = 0 ) {
	}

	/**
	 * Retrieve MailPoet lists.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function api_lists( $connection_id = '', $account_id = '' ) {
		return $this->connect->getLists();
	}

	/**
	 * Retrieve MailPoet fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function api_fields( $connection_id = '', $account_id = '', $list_id = '' ) {

		return array(
			array(
				'name'       => 'Email',
				'req'        => true,
				'tag'        => 'email',
				'field_type' => 'email',
			),
			array(
				'name'       => 'First Name',
				'req'        => false,
				'tag'        => 'first_name',
				'field_type' => 'text',
			),
			array(
				'name'       => 'Last Name',
				'req'        => false,
				'tag'        => 'last_name',
				'field_type' => 'text',
			),
		);
	}


	/**
	 * Authenticate with the API.
	 *
	 * @param array  $data
	 * @param string $form_id
	 *
	 * @since 1.0.0
	 *
	 * @return id
	 */
	public function api_auth( $data = array(), $form_id = '' ) {

		$id                              = uniqid();
		$providers                       = get_option( 'wpforms_providers', array() );
		$providers[ $this->slug ][ $id ] = array(
			'api'   => trim( $id ),
			'label' => sanitize_text_field( $data['label'] ),
			'date'  => time(),
		);

		update_option( 'wpforms_providers', $providers );

		return $id;
	}

	/**
	 * Retrieve provider account list groups.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 * @param string $list_id
	 *
	 * @return mixed array or error object.
	 */
	public function api_groups( $connection_id = '', $account_id = '', $list_id = '' ) {

		return new WP_Error( esc_html__( 'Groups do not exist.', 'wpforms-mailpoet' ) );
	}

	/**
	 * Provider account authorize fields HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function output_auth() {

		$providers = get_option( 'wpforms_providers' );
		$class     = ! empty( $providers[ $this->slug ] ) ? 'hidden' : '';

		$output = '<div class="wpforms-provider-account-add ' . $class . ' wpforms-connection-block">';

		$output .= '<h4>' . esc_html__( 'Add New Account', 'wpforms-mailpoet' ) . '</h4>';

		$output .= sprintf(
			'<input type="text" data-name="label" placeholder="%s" class="wpforms-required">',
			sprintf(
				/* translators: %s - current provider name. */
				esc_html__( '%s Account Nickname', 'wpforms-mailpoet' ),
				$this->name
			)
		);

		$output .= sprintf( '<button data-provider="%s">%s</button>', esc_attr( $this->slug ), esc_html__( 'Connect', 'wpforms-mailpoet' ) );

		$output .= '</div>';

		return $output;
	}

	/**
	 * Provider account list options HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id
	 * @param array  $connection
	 *
	 * @return string
	 */
	public function output_options( $connection_id = '', $connection = array() ) {
		return '';
	}

	/**
	 * Form fields to add a new provider account.
	 *
	 * @since 1.0.0
	 */
	public function integrations_tab_new_form() {

		printf(
			'<input type="text" name="label" placeholder="%s">',
			sprintf(
				/* translators: %s - current provider name. */
				esc_html__( '%s Account Nickname', 'wpforms-mailpoet' ),
				$this->name
			)
		);
	}
}

new WPForms_MailPoet();
