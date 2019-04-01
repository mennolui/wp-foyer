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
			'', // Callback
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

			register_setting( $page_name, $settings_field_id );

			add_settings_field(
				$settings_field_id, // ID
				$licensed_addon['name'], // Title
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
			value="<?php echo esc_attr( $license_key ); ?>" />

		<?php if ( false !== $license_key ) { ?>

			<?php wp_nonce_field( $addon_slug . '_nonce', $addon_slug . '_nonce' ); ?>

			<?php if ( 'valid' == $license_status ) { ?>

				<input type="submit" class="button-secondary"
					name="<?php echo $addon_slug; ?>_license_deactivate"
					value="<?php _e( 'Deactivate License', 'foyer' ); ?>" />

			<?php } else { ?>

				<input type="submit" class="button-secondary"
					name="<?php echo $addon_slug; ?>_license_activate"
					value="<?php _e( 'Activate License', 'foyer' ); ?>" />

			<?php } ?>

		<?php }
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
}
