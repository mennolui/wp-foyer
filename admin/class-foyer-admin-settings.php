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
	 * Gets the URL for a tab on the Foyer settings page.
	 *
	 * @since 	1.X.X
	 *
	 * @param	string	$tab		The slug of the tab to get the URL for.
	 * @return	string			The URL of the tab on the Foyer settings page.
	 */
	static function get_settings_tab_url( $tab ) {
		return add_query_arg( 'tab', $tab, self::get_settings_url() );
	}

	/**
	 * Gets the URL for the Foyer settings page.
	 *
	 * @since 	1.X.X
	 *
	 * @return	string			The URL of the Foyer settings page.
	 */
	static function get_settings_url() {
		return menu_page_url( 'foyer-settings', false );
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
	 * Uses $_SERVER['REQUEST_URI'] because get_current_screen() does not work on 'admin_init'.
	 *
	 * @since	1.X.X
	 *
	 * @return	bool		True if we are viewing or saving a Foyer settings screen, false otherwise.
	 */
	static function is_foyer_settings() {

		if ( ! is_admin() ) {
			return false;
		}

		if (
			false === strpos( $_SERVER['REQUEST_URI'], '/admin.php?page=foyer-settings' ) &&
			false === strpos( $_SERVER['REQUEST_URI'], '/options.php' )
		) {
			return false;
		}

		return true;
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
							href="<?php echo self::get_settings_tab_url( $slug ); ?>">
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
