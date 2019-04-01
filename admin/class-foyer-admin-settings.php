<?php

/**
 * The admin settings functionality.
 *
 * @since		1.X.X
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Settings {

	private static $tabs = array();

	/**
	 * Adds a Settings submenu to the Foyer admin menu.
	 *
	 * @since	1.X.X
	 */
	static function add_settings_submenu() {
		add_submenu_page(
			'foyer',
			_x( 'Foyer', 'plugin name in admin menu', 'foyer' ) . ' ' . __( 'Settings' ),
			__( 'Settings' ),
			'manage_options',
			'foyer_settings',
			array( __CLASS__, 'settings_page' )
		);
	}

	/**
	 * Gets the slug of the current settings tab.
	 *
	 * @since	1.X.X
	 *
	 * @return	string	The slug of the current tab.
	 */
	static function get_current_tab() {
		if ( ! empty( $_GET['tab'] ) ) {
			return $_GET['tab'];
		}

		$tab_keys = array_keys( self::get_tabs() );

		if ( empty( $tab_keys ) ) {
			return false;
		}

		return $tab_keys[0];
	}

	/**
	 * Gets all registered settings tabs.
	 *
	 * @since	1.X.X
	 *
	 * @return	array	The registered settings tabs.
	 */
	static function get_tabs() {
		if ( empty( self::$tabs ) ) {

			$tabs = array();

			/**
			 * Filter the settings tabs.
			 *
			 * @since	1.X.X
			 * @param	array	$tabs	The currently registered settings tabs.
			 */
			self::$tabs = apply_filters( 'foyer/admin/settings/tabs', $tabs );
		}

		return self::$tabs;
	}

	/**
	 * Outputs the settings page.
	 *
	 * @since	1.X.X
	 *
	 * @return	void
	 */
	static function settings_page() {
		?><div class="wrap">
			<h1><?php echo _x( 'Foyer', 'plugin name in admin menu', 'foyer' ) . ' ' . __( 'Settings' ); ?></h1>

			<?php if ( ! empty( self::get_tabs() ) ) { ?>

				<h2 class="nav-tab-wrapper">
					<?php foreach ( self::get_tabs() as $key => $val ) { ?>
						<a class="nav-tab <?php echo $key == self::get_current_tab() ? 'nav-tab-active' : '';?>"
							href="?page=foyer_settings&tab=<?php echo $key; ?>">
							<?php echo $val; ?>
						</a>
					<?php } ?>
				</h2>
				<form method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields( self::get_current_tab() );
						do_settings_sections( self::get_current_tab() );
						submit_button();
					?>
				</form>

			<?php } else { ?>

				<p><?php _e( 'There are currently no settings.', 'foyer' ); ?></p>

			<?php } ?>
		</div><?php
	}
}
