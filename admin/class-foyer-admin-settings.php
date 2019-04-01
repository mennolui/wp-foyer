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
			'foyer-settings',
			array( __CLASS__, 'output_settings_page' )
		);
	}

	/**
	 * Gets the slug of the current settings tab.
	 *
	 * Checks if the selected tab exists.
	 * Falls back to the first tab when no valid tab is selected.
	 *
	 * @since	1.X.X
	 *
	 * @return	string	The slug of the current tab.
	 */
	static function get_current_tab() {
		$tabs = self::get_tabs();

		if ( ! empty( $_GET['tab'] ) && ! empty( $tabs[ $_GET['tab'] ] ) ) {
			return $_GET['tab'];
		}

		$tab_keys = array_keys( $tabs );

		if ( empty( $tab_keys ) ) {
			return false;
		}

		return $tab_keys[0];
	}

	/**
	 * Gets the settings page name for a tab.
	 *
	 * @since 	1.X.X
	 *
	 * @param	string	$tab		The slug of the tab to get the page name for.
	 * @return	string			The settings page name for this tab.
	 */
	static function get_page_name_for_tab( $tab ) {
		return 'foyer-' . $tab;
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
	 * Initializes the current settings tab.
	 *
	 * Only if a settings tab is being displayed.
	 *
	 * @since	1.X.X
	 *
	 * @return	void
	 */
	static function init_current_settings_tab() {

		if ( ! self::is_foyer_settings() ) {
			return false;
		}

		$tabs = self::get_tabs();

		if ( empty( $tabs ) ) {
			return;
		}

		$current_tab_data = $tabs[ self::get_current_tab() ];

		if ( empty( $current_tab_data['callback'] ) )  {
			return;
		}

		call_user_func( $current_tab_data['callback'] );
	}

	/**
	 * Checks if we are viewing or saving a Foyer settings screen.
	 *
	 * Does not work on 'admin_init' hook. Use 'current_screen' hook or later.
	 *
	 * @since	1.X.X
	 *
	 * @return	bool		True if we are viewing or saving a Foyer settings screen, false otherwise.
	 */
	static function is_foyer_settings() {

		$screen = get_current_screen();

		if ( empty( $screen ) ) {
			return false;
		}

		if ( 'foyer_page_foyer-settings' == $screen->id || 'options' == $screen->id ) {
			return true;
		}

		return false;
	}

	/**
	 * Outputs the settings page.
	 *
	 * @since	1.X.X
	 *
	 * @return	void
	 */
	static function output_settings_page() {
		?><div class="wrap">
			<h1><?php echo _x( 'Foyer', 'plugin name in admin menu', 'foyer' ) . ' ' . __( 'Settings' ); ?></h1>

			<?php if ( ! empty( self::get_tabs() ) ) { ?>

				<h2 class="nav-tab-wrapper">
					<?php foreach ( self::get_tabs() as $slug => $tab ) { ?>
						<a class="nav-tab <?php echo $slug == self::get_current_tab() ? 'nav-tab-active' : '';?>"
							href="?page=foyer-settings&tab=<?php echo $slug; ?>">
							<?php echo $tab['name']; ?>
						</a>
					<?php } ?>
				</h2>
				<form method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields( self::get_page_name_for_tab( self::get_current_tab() ) );
						do_settings_sections( self::get_page_name_for_tab( self::get_current_tab() ) );
						submit_button();
					?>
				</form>

			<?php } else { ?>

				<p><?php _e( 'There are currently no settings.', 'foyer' ); ?></p>

			<?php } ?>
		</div><?php
	}
}
