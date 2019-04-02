<?php

/**
 * The admin settings functionality for licenses.
 *
 * @since		1.X.X
 *
 * @package		Foyer
 * @subpackage	Foyer
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Settings_Licenses {

	/**
	 * Actives a license for an add-on through the Foyer.tv EDD API.
	 *
	 * @since	1.X.X
	 *
	 * @param	array	$licensed_addon	The add-on to deactivate the license for.
	 * @return	void
	 */
	static function activate_license_for_addon( $licensed_addon ) {

		$addon_slug = $licensed_addon['slug'];

		// retrieve the license from the database
		$license_key = trim( get_option( $addon_slug . '_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license_key,
			'item_name'  => $licensed_addon['edd_item_name'], // the exact name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( FOYER_EDD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			}
			else {
				$message = __( 'An error occurred, please try again.' );
			}
		}
		else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf(
							__( 'This appears to be an invalid license key for %s.' ),
							$licensed_addon['edd_item_name']
						);
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}
			}
		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			add_settings_error(
				Foyer_Admin_Settings::get_page_name_for_tab( 'licenses' ),
				$addon_slug . '_license_activate', $message, 'error'
			);

			return;
		}

		$message = sprintf( __( 'License activated for %s.' ), $licensed_addon['edd_item_name'] );

		add_settings_error(
			Foyer_Admin_Settings::get_page_name_for_tab( 'licenses' ),
			$addon_slug . '_license_activate', $message, 'updated'
		);

		update_option( $addon_slug . '_license_status', $license_data->license );
	}

	/**
	 * Adds a 'License keys' section to the 'Licenses' settings tab.
	 *
	 * @since	1.X.X
	 *
	 * @return 	void
	 */
	static function add_license_keys_section() {
		add_settings_section(
			'foyer_license_keys', // ID
			__( 'License keys', 'foyer' ), // Title
			array( __CLASS__, 'output_license_keys_settings_section' ), // Callback
			Foyer_Admin_Settings::get_page_name_for_tab( 'licenses' ) // Page
		);
	}

	/**
	 * Adds a licence key settings field to the 'License keys' section for every add-on.
	 *
	 * @since	1.X.X
	 *
	 * @return 	void
	 */
	static function add_license_key_settings_fields() {

		$page_name = Foyer_Admin_Settings::get_page_name_for_tab( 'licenses' );

		foreach ( Foyer_Addons::get_licensed_addons() as $licensed_addon ) {

			if ( empty( $licensed_addon['slug'] ) ) {
				continue;
			}

			$settings_field_id = $licensed_addon['slug'] . '_license_key';

			register_setting(
				$page_name, // Page
				$settings_field_id, // ID
				array(
					'sanitize_callback' => function( $new_value ) use ( $licensed_addon ) {
						return self::sanitize_license_key_setting( $new_value, $licensed_addon ) ;
					},
				)
			);

			add_settings_field(
				$settings_field_id, // ID
				$licensed_addon['edd_item_name'], // Title
				array( __CLASS__, 'output_license_key_settings_field' ), // Callback
				$page_name, // Page
				'foyer_license_keys', // Section
				array(
					'licensed_addon' => $licensed_addon,
				)
			);
		}
	}

	/**
	 * Deactives a license for an add-on through the Foyer.tv EDD API.
	 *
	 * @since	1.X.X
	 *
	 * @param	array	$licensed_addon	The add-on to deactivate the license for.
	 * @return	void
	 */
	static function deactivate_license_for_addon( $licensed_addon ) {

		$addon_slug = $licensed_addon['slug'];

		// retrieve the license from the database
		$license_key = trim( get_option( $addon_slug . '_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_name'  => $licensed_addon['edd_item_name'], // the exact name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( FOYER_EDD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			}
			else {
				$message = __( 'An error occurred, please try again.' );
			}
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license != 'deactivated' ) {
			$message = sprintf( __( 'Deactivating license key for %s failed.' ), $licensed_addon['edd_item_name'] );
		}

		if ( ! empty( $message ) ) {
			add_settings_error(
				Foyer_Admin_Settings::get_page_name_for_tab( 'licenses' ),
				$addon_slug . '_license_deactivate', $message, 'error'
			);

			return;
		}

		$message = sprintf( __( 'License deactivated for %s.' ), $licensed_addon['edd_item_name'] );

		add_settings_error(
			Foyer_Admin_Settings::get_page_name_for_tab( 'licenses' ),
			$addon_slug . '_license_deactivate', $message, 'updated'
		);

		delete_option( $addon_slug . '_license_status' );
	}

	/**
	 * Initializes our settings tabs.
	 *
	 * @since	1.X.X
	 *
	 * @return 	void
	 */
	static function init_settings_tab() {
		self::add_license_keys_section();
		self::add_license_key_settings_fields();
	}

	/**
	 * Outputs the license key settings field for a licensed add-on.
	 *
	 * @since	1.X.X
	 *
	 * @param	array	$args	The args for the settings field, containing the licensed add-on.
	 * @return	void
	 */
	static function output_license_key_settings_field( $args ) {

		$addon_slug = $args['licensed_addon']['slug'];

		$license_key = trim( get_option( $addon_slug . '_license_key' ) );
		$license_status = get_option( $addon_slug . '_license_status' );

		?><input type="text" class="regular-text"
			id="<?php echo $addon_slug; ?>_license_key"
			name="<?php echo $addon_slug; ?>_license_key"
			value="<?php echo esc_attr( $license_key ); ?>">
		<?php if ( ! empty( $license_key ) ) { ?>

			<?php wp_nonce_field( $addon_slug . '_nonce', $addon_slug . '_nonce' ); ?>

			<?php if ( 'valid' == $license_status ) { ?>

				<input type="submit" class="button-secondary"
					name="<?php echo $addon_slug; ?>_license_deactivate"
					value="<?php _e( 'Deactivate License', 'foyer' ); ?>">

			<?php } else { ?>

				<input type="submit" class="button-secondary"
					name="<?php echo $addon_slug; ?>_license_activate"
					value="<?php _e( 'Activate License', 'foyer' ); ?>">

			<?php } ?>

		<?php }
	}

	/**
	 * Outputs our license keys settings section.
	 *
	 * Displays errors that occured while processing actions, if any.
	 *
	 * @since	1.X.X
	 *
	 * @return	void
	 */
	static function output_license_keys_settings_section() {
		// Outputs settings errors, even though they were not added within a register_setting() callback
		settings_errors( Foyer_Admin_Settings::get_page_name_for_tab( 'licenses' ) );
	}

	/**
	 * Processes actions on the Licenses settings tab.
	 *
	 * @since	1.X.X
	 *
	 * @return	void
	 */
	static function process_actions() {

		if ( ! Foyer_Admin_Settings::is_foyer_settings() ) {
			return;
		}

		foreach ( Foyer_Addons::get_licensed_addons() as $licensed_addon ) {

			if ( isset( $_POST[ $licensed_addon['slug'] . '_license_activate' ] ) ) {
			 	if ( check_admin_referer( $licensed_addon['slug'] . '_nonce', $licensed_addon['slug'] . '_nonce' ) ) {
					self::activate_license_for_addon( $licensed_addon );
				}
				break;
			}

			if ( isset( $_POST[ $licensed_addon['slug'] . '_license_deactivate' ] ) ) {
			 	if ( check_admin_referer( $licensed_addon['slug'] . '_nonce', $licensed_addon['slug'] . '_nonce' ) ) {
					self::deactivate_license_for_addon( $licensed_addon );
				}
				break;
			}
		}
	}

	/**
	 * Adds the Licenses tab to the Foyer settings page.
	 *
	 * @since	1.X.X
	 *
	 * @param	array	$tabs	The current tabs.
	 * @return	array			The tabs with the Licenses tab added.
	 */
	static function register_settings_tab( $tabs ) {
		if ( ! empty( Foyer_Addons::get_licensed_addons() ) ) {
			$tabs['licenses'] = array(
				'name' => _x( 'Licenses', 'name of settings tab', 'foyer' ),
				'callback' => array( __CLASS__, 'init_settings_tab' ),
			);
		}

		return $tabs;
	}

	/**
	 * Sanitizes a license key setting before being saved.
	 *
	 * Invalidates the license when a new license key was entered.
	 *
	 * @since	1.X.X
	 *
	 * @param	string	$new_value		The new value of the license key setting.
	 * @param	array	$licensed_addon	The add-on to sanitize the license key setting for.
	 * @return	string					The new value of the license key setting, unchanged.
	 */
	static function sanitize_license_key_setting( $new_value, $licensed_addon ) {

		$old_value = trim( get_option( $licensed_addon['slug'] . '_license_key' ) );

		if ( ! empty( $old_value ) && $old_value != $new_value ) {
			// new license key has been entered, so must reactivate
			delete_option( $licensed_addon['slug'] . '_license_status' );
		}

		return $new_value;
	}
}
