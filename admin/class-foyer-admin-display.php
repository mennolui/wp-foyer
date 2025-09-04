<?php

/**
 * The display admin-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Display {

	/**
	 * Adds Default Channel and Active Channel columns to the Displays admin table.
	 *
	 * Also removes the Date column.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	array	$columns	The current columns.
	 * @return	array				The new columns.
	 */
	static function add_channel_columns($columns) {
		unset($columns['date']);
		return array_merge($columns,
			array(
				'default_channel' => __('Default channel', 'foyer'),
				'active_channel' => __('Active channel', 'foyer'),
			)
		);
	}

	/**
	 * Adds the channel editor meta box to the display admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.5.1	Added context to the translatable string 'Channel' to make translation easier.
	 */
	static function add_channel_editor_meta_box() {
		add_meta_box(
			'foyer_channel_editor',
			_x( 'Channel', 'channel cpt', 'foyer' ),
			array( __CLASS__, 'channel_editor_meta_box' ),
			Foyer_Display::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Adds the channel scheduler meta box to the display admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function add_channel_scheduler_meta_box() {
		add_meta_box(
			'foyer_channel_scheduler',
			__( 'Schedule temporary channel' , 'foyer' ),
			array( __CLASS__, 'channel_scheduler_meta_box' ),
			Foyer_Display::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Outputs the content of the channel editor meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	static function channel_editor_meta_box( $post ) {

		wp_nonce_field( Foyer_Display::post_type_name, Foyer_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>"
				name="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="foyer_meta_box_form form-table foyer_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
				<tbody>
					<?php

						echo self::get_default_channel_html( $post );

					?>
				</tbody>
			</table>

		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the content of the channel scheduler meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	static function channel_scheduler_meta_box( $post ) {

		wp_nonce_field( Foyer_Display::post_type_name, Foyer_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>"
				name="foyer_channel_editor_<?php echo Foyer_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="foyer_meta_box_form form-table foyer_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
				<tbody>
					<?php

						echo self::get_scheduled_channel_html( $post );

					?>
				</tbody>
			</table>

		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the Active Channel and Defaults Channel columns.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.3.2	Changed method to static.
	 *					Used post_id param instead of get_the_id() to allow for testing.
	 *					Outputs 'None' if no channel is set. Fixes #10.
	 *
	 * @param 	string	$column		The current column that needs output.
	 * @param 	int 	$post_id 	The current display ID.
	 * @return	void
	 */
	static function do_channel_columns( $column, $post_id ) {

	    switch ( $column ) {

		    case 'active_channel' :

				$display = new Foyer_Display( $post_id );

				if ( ! $active_channel_id = $display->get_active_channel() ) {
					_e( 'None', 'foyer' );
					break;
				}

				$channel = new Foyer_Channel( $active_channel_id );

				?><a href="<?php echo esc_url( get_edit_post_link( $channel->ID ) ); ?>"><?php
					echo esc_html( get_the_title( $channel->ID ) );
				?></a><?php

		        break;

		    case 'default_channel' :

				$display = new Foyer_Display( $post_id );

				if ( ! $default_channel_id = $display->get_default_channel() ) {
					_e( 'None', 'foyer' );
					break;
				}

				$channel = new Foyer_Channel( $default_channel_id );

				?><a href="<?php echo esc_url( get_edit_post_link( $channel->ID ) ); ?>"><?php
					echo esc_html( get_the_title( $channel->ID ) );
				?></a><?php

		        break;
	    }
	}

	/**
	 * Gets the defaults to be used in the channel scheduler.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	string	The defaults to be used in the channel scheduler.
	 */
	static function get_channel_scheduler_defaults() {
		$language_parts = explode( '-', get_bloginfo( 'language' ) );

		$defaults = array(
			'datetime_format' => 'Y-m-d H:i',
			'duration' => 1 * 60 * 60, // one hour in seconds
			'locale' => $language_parts[0], // locale formatted as 'en' instead of 'en-US'
			'start_of_week' => get_option( 'start_of_week' ),
		);

		/**
		 * Filters the channel scheduler defaults.
		 *
		 * @since 1.0.0
		 *
		 * @param array $defaults	The current defaults to be used in the channel scheduler.
		 */
		return apply_filters( 'foyer/channel_scheduler/defaults', $defaults );
	}

	/**
	 * Gets the HTML that lists the default channel in the channel editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped and sanitized the output.
	 * @since	1.2.3	Changed the list of available channels from limited to unlimited.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists the default channel in the channel editor.
	 */
	static function get_default_channel_html( $post ) {

		$display = new Foyer_Display( $post );
		$default_channel = $display->get_default_channel();

        ob_start();

        ?>
            <tr>
                <th>
                    <label>
                        <?php echo esc_html__( 'Default channel', 'foyer' ); ?>
                    </label>
                </th>
                <td>
                    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin:4px 0 8px;">
                        <label for="foyer_default_channel_search" style="margin-right:6px;">
                            <?php echo esc_html__( 'Search', 'foyer' ); ?>
                        </label>
                        <input type="search" id="foyer_default_channel_search" class="regular-text" placeholder="<?php echo esc_attr__( 'Search by title or author…', 'foyer' ); ?>" style="max-width:280px;" />
                    </div>
                    <?php
                        $channels = Foyer_Channels::get_posts();
                    ?>
                    <table class="widefat fixed striped" id="foyer_default_channels_table" style="max-width:720px;">
                        <thead>
                            <tr>
                                <th style="width:42px;">&nbsp;</th>
                                <th data-sort="title" class="foyer-sort-col"><span class="sort-label"><?php echo esc_html_x( 'Title', 'post title', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                                <th data-sort="author" class="foyer-sort-col" style="width:160px;"><span class="sort-label"><?php echo esc_html__( 'Author', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                                <th data-sort="date" class="foyer-sort-col" style="width:180px;"><span class="sort-label"><?php echo esc_html__( 'Date', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                                <th data-sort="slides" class="foyer-sort-col" style="width:120px;"><span class="sort-label"><?php echo esc_html__( 'Slides', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ( empty( $channels ) ) : ?>
                            <tr><td colspan="5"><?php echo esc_html__( 'No channels found.', 'foyer' ); ?></td></tr>
                        <?php else : ?>
                            <?php foreach ( $channels as $channel_post ) :
                                $author_name = get_the_author_meta( 'display_name', $channel_post->post_author );
                                $date_ts = get_post_time( 'U', true, $channel_post );
                                $channel_obj = new Foyer_Channel( $channel_post );
                                $slides_count = count( $channel_obj->get_slides() );
                                $checked = $default_channel == $channel_post->ID ? 'checked="checked"' : '';
                            ?>
                            <tr data-title="<?php echo esc_attr( get_the_title( $channel_post->ID ) ); ?>" data-author="<?php echo esc_attr( $author_name ); ?>" data-date-ts="<?php echo esc_attr( $date_ts ); ?>" data-slides="<?php echo esc_attr( $slides_count ); ?>">
                                <td>
                                    <input type="radio" name="foyer_channel_editor_default_channel" value="<?php echo intval( $channel_post->ID ); ?>" <?php echo $checked; ?> />
                                </td>
                                <td><?php echo esc_html( get_the_title( $channel_post->ID ) ); ?></td>
                                <td><?php echo esc_html( $author_name ); ?></td>
                                <td><?php echo esc_html( get_the_date( get_option( 'date_format' ), $channel_post ) . ' ' . get_the_time( get_option( 'time_format' ), $channel_post ) ); ?></td>
                                <td><?php echo esc_html( $slides_count ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <script type="text/javascript">
                    (function($){
                        $(function(){
                            var $table = $('#foyer_default_channels_table');
                            var $rows = $table.find('tbody > tr');
                            var $search = $('#foyer_default_channel_search');
                            var sortKey = 'title';
                            var sortDir = 'asc';

                            function applyFilters(){
                                var q = ($search.val()||'').toLowerCase();
                                $rows.each(function(){
                                    var $tr = $(this);
                                    var title = (String($tr.data('title')||'')).toLowerCase();
                                    var author = (String($tr.data('author')||'')).toLowerCase();
                                    var visible = (!q || title.indexOf(q)!==-1 || author.indexOf(q)!==-1);
                                    $tr.toggle(visible);
                                });
                            }

                            function compareRows(a,b){
                                var $a=$(a),$b=$(b),dir=(sortDir==='asc')?1:-1;
                                if (sortKey==='date' || sortKey==='slides'){
                                    var va=parseInt($a.data(sortKey),10)||0;
                                    var vb=parseInt($b.data(sortKey),10)||0;
                                    if(va===vb) return 0; return (va<vb?-1:1)*dir;
                                } else {
                                    var sa=String($a.data(sortKey)||'').toLowerCase();
                                    var sb=String($b.data(sortKey)||'').toLowerCase();
                                    if(sa===sb) return 0; return (sa<sb?-1:1)*dir;
                                }
                            }

                            function updateSortIndicators(){
                                var arrows={asc:'\u25B2',desc:'\u25BC'};
                                $table.find('thead th.foyer-sort-col .sort-ind').text('');
                                $table.find('thead th.foyer-sort-col[data-sort="'+sortKey+'"] .sort-ind').text(arrows[sortDir]||'');
                            }

                            function sortRows(){
                                var $tbody=$table.find('tbody');
                                var visible=$rows.filter(':visible').get();
                                visible.sort(compareRows);
                                $tbody.append(visible);
                                $tbody.append($rows.filter(':hidden'));
                                updateSortIndicators();
                            }

                            function refresh(){
                                $rows.show();
                                applyFilters();
                                sortRows();
                            }

                            $search.on('input', function(){ refresh(); });
                            $table.find('thead').on('click', 'th.foyer-sort-col', function(){
                                var key=$(this).data('sort');
                                if(!key) return;
                                if(key===sortKey){ sortDir=(sortDir==='asc')?'desc':'asc'; }
                                else { sortKey=key; sortDir='asc'; }
                                refresh();
                            });

                            refresh();
                        });
                    })(jQuery);
                    </script>
                </td>
            </tr>
        <?php

        $html = ob_get_clean();

        return $html;
	}

	/**
	 * Gets the HTML that lists the scheduled channels in the channel scheduler.
	 *
	 * Currently limited to only one scheduled channel.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped and sanitized the output.
	 * @since	1.2.3	Changed the list of available channels from limited to unlimited.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists the scheduled channels in the channel scheduler.
	 */
	static function get_scheduled_channel_html( $post ) {

		$display = new Foyer_Display( $post );
		$schedule = $display->get_schedule();

		if ( !empty( $schedule ) ) {
			$scheduled_channel = $schedule[0];
		}

		$channel_scheduler_defaults = self::get_channel_scheduler_defaults();

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel">
						<?php echo esc_html__( 'Temporary channel', 'foyer' ); ?>
					</label>
				</th>
					<td>
						<div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin:4px 0 8px;">
							<label for="foyer_scheduled_channel_search" style="margin-right:6px;"><?php echo esc_html__( 'Search', 'foyer' ); ?></label>
							<input type="search" id="foyer_scheduled_channel_search" class="regular-text" placeholder="<?php echo esc_attr__( 'Search by title or author…', 'foyer' ); ?>" style="max-width:280px;" />
						</div>
						<?php
							$channels = Foyer_Channels::get_posts();
							$selected_id = ! empty( $scheduled_channel['channel'] ) ? intval( $scheduled_channel['channel'] ) : 0;
						?>
						<table class="widefat fixed striped" id="foyer_scheduled_channels_table" style="max-width:720px;">
							<thead>
								<tr>
									<th style="width:42px;">&nbsp;</th>
									<th data-sort="title" class="foyer-sort-col"><span class="sort-label"><?php echo esc_html_x( 'Title', 'post title', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
									<th data-sort="author" class="foyer-sort-col" style="width:160px;"><span class="sort-label"><?php echo esc_html__( 'Author', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
									<th data-sort="date" class="foyer-sort-col" style="width:180px;"><span class="sort-label"><?php echo esc_html__( 'Date', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
									<th data-sort="slides" class="foyer-sort-col" style="width:120px;"><span class="sort-label"><?php echo esc_html__( 'Slides', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
								</tr>
							</thead>
							<tbody>
							<?php if ( empty( $channels ) ) : ?>
								<tr><td colspan="5"><?php echo esc_html__( 'No channels found.', 'foyer' ); ?></td></tr>
							<?php else : ?>
								<?php foreach ( $channels as $channel_post ) :
									$author_name = get_the_author_meta( 'display_name', $channel_post->post_author );
									$date_ts = get_post_time( 'U', true, $channel_post );
									$channel_obj = new Foyer_Channel( $channel_post );
									$slides_count = count( $channel_obj->get_slides() );
									$checked = $selected_id == $channel_post->ID ? 'checked="checked"' : '';
								?>
								<tr data-title="<?php echo esc_attr( get_the_title( $channel_post->ID ) ); ?>" data-author="<?php echo esc_attr( $author_name ); ?>" data-date-ts="<?php echo esc_attr( $date_ts ); ?>" data-slides="<?php echo esc_attr( $slides_count ); ?>">
									<td><input type="radio" name="foyer_channel_editor_scheduled_channel" value="<?php echo intval( $channel_post->ID ); ?>" <?php echo $checked; ?> /></td>
									<td><?php echo esc_html( get_the_title( $channel_post->ID ) ); ?></td>
									<td><?php echo esc_html( $author_name ); ?></td>
									<td><?php echo esc_html( get_the_date( get_option( 'date_format' ), $channel_post ) . ' ' . get_the_time( get_option( 'time_format' ), $channel_post ) ); ?></td>
									<td><?php echo esc_html( $slides_count ); ?></td>
								</tr>
								<?php endforeach; ?>
							<?php endif; ?>
							</tbody>
						</table>
						<script type="text/javascript">
						(function($){
							$(function(){
								var $table = $('#foyer_scheduled_channels_table');
								var $rows = $table.find('tbody > tr');
								var $search = $('#foyer_scheduled_channel_search');
								var sortKey = 'title';
								var sortDir = 'asc';

								function applyFilters(){
									var q = ($search.val()||'').toLowerCase();
									$rows.each(function(){
										var $tr = $(this);
										var title = (String($tr.data('title')||'')).toLowerCase();
										var author = (String($tr.data('author')||'')).toLowerCase();
										var visible = (!q || title.indexOf(q)!==-1 || author.indexOf(q)!==-1);
										$tr.toggle(visible);
									});
								}

								function compareRows(a,b){
									var $a=$(a),$b=$(b),dir=(sortDir==='asc')?1:-1;
									if (sortKey==='date' || sortKey==='slides'){
										var va=parseInt($a.data(sortKey),10)||0;
										var vb=parseInt($b.data(sortKey),10)||0;
										if(va===vb) return 0; return (va<vb?-1:1)*dir;
									} else {
										var sa=String($a.data(sortKey)||'').toLowerCase();
										var sb=String($b.data(sortKey)||'').toLowerCase();
										if(sa===sb) return 0; return (sa<sb?-1:1)*dir;
									}
								}

								function updateSortIndicators(){
									var arrows={asc:'\u25B2',desc:'\u25BC'};
									$table.find('thead th.foyer-sort-col .sort-ind').text('');
									$table.find('thead th.foyer-sort-col[data-sort="'+sortKey+'"] .sort-ind').text(arrows[sortDir]||'');
								}

								function sortRows(){
									var $tbody=$table.find('tbody');
									var visible=$rows.filter(':visible').get();
									visible.sort(compareRows);
									$tbody.append(visible);
									$tbody.append($rows.filter(':hidden'));
									updateSortIndicators();
								}

								function refresh(){
									$rows.show();
									applyFilters();
									sortRows();
								}

								$search.on('input', function(){ refresh(); });
								$table.find('thead').on('click', 'th.foyer-sort-col', function(){
									var key=$(this).data('sort');
									if(!key) return;
									if(key===sortKey){ sortDir=(sortDir==='asc')?'desc':'asc'; }
									else { sortKey=key; sortDir='asc'; }
									refresh();
								});

								refresh();
							});
						})(jQuery);
						</script>
					</td>
			</tr>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel_start">
						<?php echo esc_html__( 'Show from', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<input type="text" id="foyer_channel_editor_scheduled_channel_start" name="foyer_channel_editor_scheduled_channel_start" value="<?php if ( ! empty( $scheduled_channel['start'] ) ) { echo esc_html( date_i18n( $channel_scheduler_defaults['datetime_format'], $scheduled_channel['start'] + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true ) ); } ?>" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="foyer_channel_editor_scheduled_channel_end">
						<?php echo esc_html__( 'Until', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<input type="text" id="foyer_channel_editor_scheduled_channel_end" name="foyer_channel_editor_scheduled_channel_end" value="<?php if ( ! empty( $scheduled_channel['end'] ) ) { echo esc_html( date_i18n( $channel_scheduler_defaults['datetime_format'], $scheduled_channel['end'] + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true ) ); } ?>" />
				</td>
			</tr>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Localizes the JavaScript for the display admin area.
	 *
	 * @since	1.0.0
	 * @since	1.3.1	Changed handle of script to {plugin_name}-admin.
	 * @since	1.3.2	Changed method to static.
	 */
	static function localize_scripts() {

		$channel_scheduler_defaults = self::get_channel_scheduler_defaults();
		wp_localize_script( Foyer::get_plugin_name() . '-admin', 'foyer_channel_scheduler_defaults', $channel_scheduler_defaults );
	}

	/**
	 * Saves all custom fields for a display.
	 *
	 * Triggered when a display is submitted from the display admin form.
	 *
	 * @since 	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	int		$post_id	The channel id.
	 * @return void
	 */
	static function save_display( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		/* Check if our nonce is set */
		if ( ! isset( $_POST[Foyer_Display::post_type_name.'_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST[Foyer_Display::post_type_name.'_nonce'];

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, Foyer_Display::post_type_name ) ) {
			return $post_id;
		}

		/* If this is an autosave, our form has not been submitted, so we don't want to do anything */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		/* Check the user's permissions */
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		/* Input validation */
		/* See: https://codex.wordpress.org/Data_Validation#Input_Validation */
		$channel = intval( $_POST['foyer_channel_editor_default_channel'] );
		$display_id = intval( $_POST['foyer_channel_editor_' . Foyer_Display::post_type_name] );

		if ( empty( $display_id ) ) {
			return $post_id;
		}

		if ( ! empty( $channel ) ) {
			update_post_meta( $display_id, Foyer_Channel::post_type_name, $channel );
		}
		else {
			delete_post_meta( $display_id, Foyer_Channel::post_type_name );
		}

		/**
		 * Save schedule for temporary channels.
		 */
		self::save_schedule( $post_id );

	}

	/**
	 * Save all scheduled channels for this display.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.0.1	Removed the $values param that contained $_POST, to always be aware
	 * 					we're working with $_POST data.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @access	private
	 * @param 	array	$values			All form values that were submitted from the display admin page.
	 * @param 	int		$display_id		The ID of the display that is being saved.
	 * @return 	void
	 */
	private static function save_schedule( $display_id ) {

		delete_post_meta( $display_id, 'foyer_display_schedule' );

		$foyer_channel_editor_scheduled_channel = intval( $_POST['foyer_channel_editor_scheduled_channel'] );
		if ( empty( $foyer_channel_editor_scheduled_channel ) ) {
			return;
		}

		$foyer_channel_editor_scheduled_channel_start = sanitize_text_field( $_POST['foyer_channel_editor_scheduled_channel_start'] );
		if ( empty( $foyer_channel_editor_scheduled_channel_start ) ) {
			return;
		}

		$foyer_channel_editor_scheduled_channel_end = sanitize_text_field( $_POST['foyer_channel_editor_scheduled_channel_end'] );
		if ( empty( $foyer_channel_editor_scheduled_channel_end ) ) {
			return;
		}

		/**
		 * Store all scheduled channels.
		 * Currently only one scheduled channel is saved.
		 *
		 * Makes sure that start and end times are stored in UTC.
		 * Makes sure end time never equals or is before the start time.
		 */

		$start = strtotime( $foyer_channel_editor_scheduled_channel_start ) - get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		$end = strtotime( $foyer_channel_editor_scheduled_channel_end ) - get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

		if ( $end <= $start ) {
			// End time is invalid, set based on start time and default duration
			$channel_scheduler_defaults = self::get_channel_scheduler_defaults();
			$end = $start + $channel_scheduler_defaults['duration'];
		}

		$schedule = array(
			'channel' => $foyer_channel_editor_scheduled_channel,
			'start' => 	$start,
			'end' => $end,
		);

		add_post_meta( $display_id, 'foyer_display_schedule', $schedule, false );
	}
}
