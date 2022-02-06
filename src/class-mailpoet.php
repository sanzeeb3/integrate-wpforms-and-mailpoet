<?php

/**
 * MailPoet integration.
 *
 * @since 1.0.0
 */
class Integrate_WPForms_And_MailPoet extends WPForms_Provider {

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

		$this->version  = INTEGRATE_WPFORMS_AND_MAILPOET_VERSION;
		$this->name     = 'MailPoet';
		$this->slug     = 'mailpoet';
		$this->priority = 0.5;
		$this->icon     = plugins_url( 'assets/mailpoet.png', INTEGRATE_WPFORMS_AND_MAILPOET_PLUGIN_FILE );

		if ( class_exists( \MailPoet\API\API::class ) ) {
			$this->connect = \MailPoet\API\API::MP( 'v1' );
		} else {
			return;
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
	 * @param int   $entry_id
	 */
	public function process_entry( $fields, $entry, $form_data, $entry_id = 0 ) {

		// Only run if this form has a connections for this provider.
		if ( empty( $form_data['providers'][ $this->slug ] ) ) {
			return;
		}

		// Fire for each connection.
		foreach ( $form_data['providers'][ $this->slug ] as $connection ) {
			$account_id      = $connection['account_id'];
			$list_id         = $connection['list_id'];
			$email_data      = explode( '.', $connection['fields']['email'] );
			$first_name_data = explode( '.', $connection['fields']['first_name'] );
			$last_name_data  = explode( '.', $connection['fields']['last_name'] );

			$email      = $fields[ $email_data[0] ]['value'];
			$first_name = $fields[ $first_name_data[0] ]['first'];
			$last_name  = $fields[ $last_name_data[0] ]['last'];

			if ( empty( $email ) ) {
				continue;
			}

			// Check for conditionals.
			$pass = $this->process_conditionals( $fields, $entry, $form_data, $connection );

			if ( ! $pass ) {
				wpforms_log(
					'MailPoet Subscription stopped by conditional logic',
					$fields,
					array(
						'type'    => array( 'provider', 'conditional_logic' ),
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					)
				);

				continue;
			}

			/**
			 * Add a subscriber.
			 *
			 * @link https://github.com/mailpoet/mailpoet/blob/master/mailpoet/doc/api_methods/AddSubscriber.md.
			 */
			$this->connect->addSubscriber(
				array(
					'email'      => $email,
					'first_name' => $first_name,
					'last_name'  => $last_name,
				),

				array( $connection['list_id'] )				
			);
		}
	}

	/**
	 * Retrieve MailPoet lists.
	 *
	 * @since 1.0.0
	 *
	 * @link https://github.com/mailpoet/mailpoet/blob/master/mailpoet/doc/api_methods/GetLists.md
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
	 * @link https://github.com/mailpoet/mailpoet/blob/master/mailpoet/doc/api_methods/GetSubscriberFields.md
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

		return new WP_Error( esc_html__( 'Groups do not exist.', 'integrate-wpforms-and-mailpoet' ) );
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

		$output .= '<h4>' . esc_html__( 'Add New Account', 'integrate-wpforms-and-mailpoet' ) . '</h4>';

		$output .= sprintf(
			'<input type="text" data-name="label" placeholder="%s" class="wpforms-required">',
			sprintf(
				/* translators: %s - current provider name. */
				esc_html__( '%s Account Nickname', 'integrate-wpforms-and-mailpoet' ),
				$this->name
			)
		);

		$output .= sprintf( '<button data-provider="%s">%s</button>', esc_attr( $this->slug ), esc_html__( 'Connect', 'integrate-wpforms-and-mailpoet' ) );

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
				esc_html__( '%s Account Nickname', 'integrate-wpforms-and-mailpoet' ),
				$this->name
			)
		);
	}
}

new Integrate_WPForms_And_MailPoet();
