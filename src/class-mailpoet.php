<?php

/**
 * MailPoet integration.
 *
 * @since 1.0.0
 */
class WPForms_MailPoet extends WPForms_Provider {

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
    }
}

new WPForms_MailPoet;