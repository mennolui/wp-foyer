<?php

/**
 * The channel admin-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		Foyer
 * @subpackage	Foyer/admin
 * @author		Menno Luitjes <menno@mennoluitjes.nl>
 */
class Foyer_Admin_Channel {

	/**
	 * Adds a Slide Count column to the Channels admin table, just after the title column.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	array	$columns	The current columns.
	 * @return	array				The new columns.
	 */
    static function add_slides_count_column( $columns ) {
        $new_columns = array();

        foreach( $columns as $key => $title ) {
            $new_columns[$key] = $title;

            if ( 'title' == $key ) {
                // Add favorite star and slides count columns after the title column
                $new_columns['favorite'] = __( 'Favorite', 'foyer' );
                $new_columns['slides_count'] = __( 'Number of slides', 'foyer' );
            }
        }
        return $new_columns;
    }

	/**
	 * Adds a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return void
	 */
    static function add_slide_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = intval( $_POST['channel_id'] );
		$add_slide_id = intval( $_POST['slide_id'] );

		if ( empty( $channel_id ) || empty( $add_slide_id ) ) {
			wp_die();
		}

		/* Check if the channel post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$channel = new Foyer_Channel( $channel_id );
		$slides = $channel->get_slides();

		$new_slides = array();
		foreach( $slides as $slide ) {
			$new_slides[] = $slide->ID;
		}

        $new_slides[] = $add_slide_id;

        update_post_meta( $channel_id, Foyer_Slide::post_type_name, $new_slides );
        // Initialize window entry (optional, left empty)
        $windows = get_post_meta( $channel_id, 'foyer_channel_slide_windows', true );
        if ( empty( $windows ) || ! is_array( $windows ) ) { $windows = array(); }
        if ( empty( $windows[ $add_slide_id ] ) ) {
            $windows[ $add_slide_id ] = array( 'start' => null, 'end' => null );
            update_post_meta( $channel_id, 'foyer_channel_slide_windows', $windows );
        }

		echo self::get_slides_list_html( get_post( $channel_id ) );
		wp_die();
	}

	/**
	 * Adds the slides editor meta box to the channel admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function add_slides_editor_meta_box() {
		add_meta_box(
			'foyer_slides_editor',
			_x( 'Slides', 'slide cpt', 'foyer' ),
			array( __CLASS__, 'slides_editor_meta_box' ),
			Foyer_Channel::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Adds the settings meta box to the channel admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function add_slides_settings_meta_box() {
		add_meta_box(
			'foyer_slides_settings',
			__( 'Slideshow settings' , 'foyer' ),
			array( __CLASS__, 'slides_settings_meta_box' ),
			Foyer_Channel::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Outputs the Slides Count column.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	string	$column		The current column that needs output.
	 * @param 	int 	$post_id 	The current display ID.
	 * @return	void
	 */
    static function do_slides_count_column( $column, $post_id ) {
        if ( 'slides_count' === $column ) {
            $channel = new Foyer_Channel( $post_id );
            echo intval( count( $channel->get_slides() ) );
            return;
        }
        if ( 'favorite' === $column ) {
            $is_fav = (bool) get_post_meta( $post_id, 'foyer_channel_is_favorite', true );
            $icon = $is_fav ? '★' : '☆';
            $title = $is_fav ? __( 'Unmark favorite', 'foyer' ) : __( 'Mark favorite', 'foyer' );
            $cls = $is_fav ? 'foyer-fav is-fav' : 'foyer-fav';
            echo '<a href="#" class="foyer-fav-toggle ' . esc_attr( $cls ) . '" data-postid="' . intval( $post_id ) . '" aria-label="' . esc_attr( $title ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $icon ) . '</a>';
            echo '<style>.column-favorite{width:80px}.foyer-fav{font-size:18px; text-decoration:none;}.foyer-fav.is-fav{color:#d98900}</style>';
            return;
        }
    }

    /**
     * Toggles favorite flag over AJAX.
     *
     * @since 1.8.0
     */
    static function toggle_favorite_over_ajax() {
        check_ajax_referer( 'foyer_channel_admin_ajax_nonce', 'nonce', true );
        $post_id = intval( $_POST['post_id'] ?? 0 );
        $set     = sanitize_text_field( $_POST['set'] ?? '' );
        if ( empty( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
        }
        if ( $set === '1' ) {
            update_post_meta( $post_id, 'foyer_channel_is_favorite', '1' );
        } else {
            delete_post_meta( $post_id, 'foyer_channel_is_favorite' );
        }
        wp_send_json_success( array( 'post_id' => $post_id, 'is_favorite' => ( $set === '1' ) ) );
    }

    /**
     * In the Channels admin list, show favorites first by default.
     *
     * Keeps user-chosen sorting intact (only applies when no explicit orderby set).
     *
     * @since 1.8.0
     */
    static function prefer_favorites_in_admin_list( $query ) {
        // No-op: replaced by posts_clauses-based ordering to include non-favorites as well.
    }

    /**
     * Modify SQL ORDER BY to put favorites first without filtering out others.
     *
     * @since 1.8.0
     */
    static function order_favorites_first_clause( $clauses, $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) { return $clauses; }
        $post_type = $query->get( 'post_type' );
        if ( empty( $post_type ) ) { $post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : ''; }
        if ( Foyer_Channel::post_type_name !== $post_type ) { return $clauses; }

        global $wpdb;
        $meta_key = 'foyer_channel_is_favorite';
        $case = "CASE WHEN EXISTS (SELECT 1 FROM {$wpdb->postmeta} pmf WHERE pmf.post_id = {$wpdb->posts}.ID AND pmf.meta_key = '" . esc_sql( $meta_key ) . "' AND pmf.meta_value = '1') THEN 0 ELSE 1 END";

        if ( ! empty( $clauses['orderby'] ) ) {
            $clauses['orderby'] = $case . ' ASC, ' . $clauses['orderby'];
        } else {
            $clauses['orderby'] = $case . ' ASC';
        }

        return $clauses;
    }

	/**
	 * Gets the HTML to add a slide in the slides editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 * @since	1.1.0			Fix: List of slides was limited to 5 items.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	string	$html	The HTML to add a slide in the slides editor.
	 */
	static function get_add_slide_html() {

		ob_start();

            ?>
                <div class="foyer_slides_editor_add">
                    <h4><?php echo esc_html__( 'Available slides', 'foyer' ); ?></h4>
                    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin:8px 0 12px;">
                        <label for="foyer_slides_table_search" style="margin-right:6px;">
                            <?php echo esc_html__( 'Search', 'foyer' ); ?>
                        </label>
                        <input type="search" id="foyer_slides_table_search" class="regular-text" placeholder="<?php echo esc_attr__( 'Search by title or author…', 'foyer' ); ?>" style="max-width:320px;" />
                        <label style="display:flex; align-items:center; gap:6px;">
                            <input type="checkbox" id="foyer_slides_table_hide_in_channel" checked="checked" />
                            <?php echo esc_html__( 'Hide slides already in this channel', 'foyer' ); ?>
                        </label>
                        <label for="foyer_slides_table_per_page" style="margin-left:auto;">
                            <?php echo esc_html__( 'Rows per page', 'foyer' ); ?>
                        </label>
                        <select id="foyer_slides_table_per_page">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <?php
                        // Slides currently in this channel (to disable/hide in table)
                        $current_channel = new Foyer_Channel( get_post() );
                        $current_slides = $current_channel->get_slides();
                        $in_channel_ids = array();
                        if ( ! empty( $current_slides ) ) {
                            foreach ( $current_slides as $s ) { $in_channel_ids[] = intval( $s->ID ); }
                        }

                        // Allow customization of the slides list via filters.
                        $query_args = apply_filters( 'foyer/admin/channel/add_slide_query_args', array( 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );
                        $slides = Foyer_Slides::get_posts( $query_args );
                        $slides = apply_filters( 'foyer/admin/channel/add_slide_posts', $slides );
                    ?>
                    <table class="widefat fixed striped" id="foyer_available_slides_table">
                        <thead>
                            <tr>
                                <th style="width:110px;">&nbsp;</th>
                                <th data-sort="title" class="foyer-sort-col"><span class="sort-label"><?php echo esc_html_x( 'Title', 'post title', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                                <th data-sort="author" class="foyer-sort-col" style="width:140px;"><span class="sort-label"><?php echo esc_html__( 'Author', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                                <th data-sort="date" class="foyer-sort-col" style="width:160px;"><span class="sort-label"><?php echo esc_html__( 'Date', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                                <th data-sort="format" class="foyer-sort-col" style="width:160px;"><span class="sort-label"><?php echo esc_html__( 'Format', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                                <th data-sort="background" class="foyer-sort-col" style="width:200px;"><span class="sort-label"><?php echo esc_html__( 'Background', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ( empty( $slides ) ) : ?>
                            <tr>
                                <td colspan="6"><?php echo esc_html__( 'No slides found.', 'foyer' ); ?></td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $slides as $slide ) :
                                $slide_obj = new Foyer_Slide( $slide );
                                $format = Foyer_Slides::get_slide_format_by_slug( $slide_obj->get_format() );
                                $background = Foyer_Slides::get_slide_background_by_slug( $slide_obj->get_background() );
                                $is_in_channel = in_array( intval( $slide->ID ), $in_channel_ids, true );
                                $author_name = get_the_author_meta( 'display_name', $slide->post_author );
                                $date_str = get_the_date( get_option( 'date_format' ), $slide ) . ' ' . get_the_time( get_option( 'time_format' ), $slide );
                            ?>
                            <tr data-slide-id="<?php echo intval( $slide->ID ); ?>" data-in-channel="<?php echo $is_in_channel ? '1' : '0'; ?>" data-title="<?php echo esc_attr( get_the_title( $slide->ID ) ); ?>" data-author="<?php echo esc_attr( $author_name ); ?>" data-date-ts="<?php echo esc_attr( get_post_time( 'U', true, $slide ) ); ?>" data-format="<?php echo esc_attr( isset( $format['title'] ) ? $format['title'] : '' ); ?>" data-background="<?php echo esc_attr( isset( $background['title'] ) ? $background['title'] : '' ); ?>">
                                <td>
                                    <button type="button" class="button button-primary foyer_add_slide_btn" data-slide-id="<?php echo intval( $slide->ID ); ?>" <?php echo $is_in_channel ? 'disabled' : ''; ?>><?php echo $is_in_channel ? esc_html__( 'Added', 'foyer' ) : esc_html__( 'Add', 'foyer' ); ?></button>
                                </td>
                                <td><?php echo esc_html( get_the_title( $slide->ID ) ); ?></td>
                                <td><?php echo esc_html( $author_name ); ?></td>
                                <td><?php echo esc_html( $date_str ); ?></td>
                                <td><?php echo esc_html( isset( $format['title'] ) ? $format['title'] : '' ); ?></td>
                                <td><?php echo esc_html( isset( $background['title'] ) ? $background['title'] : '' ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <div id="foyer_slides_table_pager" style="display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:8px;">
                        <button type="button" class="button" id="foyer_slides_table_prev">&laquo; <?php echo esc_html__( 'Prev', 'foyer' ); ?></button>
                        <span id="foyer_slides_table_page_info"></span>
                        <button type="button" class="button" id="foyer_slides_table_next"><?php echo esc_html__( 'Next', 'foyer' ); ?> &raquo;</button>
                    </div>
                    <script type="text/javascript">
                    (function($){
                        $(function(){
                            var $metaBox = $('.foyer_meta_box.foyer_slides_editor');
                            var channelId = $metaBox.data('channel-id');
                            var $table = $('#foyer_available_slides_table');
                            var $rows = $table.find('tbody > tr');
                            var $search = $('#foyer_slides_table_search');
                            var $hideInChannel = $('#foyer_slides_table_hide_in_channel');
                            var $perPage = $('#foyer_slides_table_per_page');
                            var $pager = $('#foyer_slides_table_pager');
                            var $prev = $('#foyer_slides_table_prev');
                            var $next = $('#foyer_slides_table_next');
                            var $info = $('#foyer_slides_table_page_info');
                            var currentPage = 1;
                            var sortKey = 'title';
                            var sortDir = 'asc';

                            function applyFilters(){
                                var q = ($search.val()||'').toLowerCase();
                                var hideUsed = $hideInChannel.is(':checked');
                                $rows.each(function(){
                                    var $tr = $(this);
                                    var inChannel = $tr.data('in-channel') == 1;
                                    var title = (String($tr.data('title')||'')).toLowerCase();
                                    var author = (String($tr.data('author')||'')).toLowerCase();
                                    var matches = (!q || title.indexOf(q) !== -1 || author.indexOf(q) !== -1);
                                    var visible = matches && !(hideUsed && inChannel);
                                    $tr.toggle(visible);
                                });
                            }

                            function paginate(){
                                var per = parseInt($perPage.val(), 10) || 10;
                                // Reset to the full filtered set before slicing to avoid shrinking pool
                                $rows.show();
                                applyFilters();
                                var visibleRows = $rows.filter(':visible');
                                var total = visibleRows.length;
                                var totalPages = Math.max(1, Math.ceil(total / per));
                                if(currentPage > totalPages) currentPage = totalPages;
                                var start = (currentPage - 1) * per;
                                var end = start + per;
                                visibleRows.hide().slice(start, end).show();
                                $info.text(currentPage + ' / ' + totalPages);
                                $prev.prop('disabled', currentPage <= 1);
                                $next.prop('disabled', currentPage >= totalPages);
                            }

                            function compareRows(a, b){
                                var $a = $(a), $b = $(b);
                                var dir = (sortDir === 'asc') ? 1 : -1;
                                if (sortKey === 'date') {
                                    var ta = parseInt($a.data('date-ts'), 10) || 0;
                                    var tb = parseInt($b.data('date-ts'), 10) || 0;
                                    if (ta === tb) return 0;
                                    return (ta < tb ? -1 : 1) * dir;
                                } else {
                                    var sa = String($a.data(sortKey) || '').toLowerCase();
                                    var sb = String($b.data(sortKey) || '').toLowerCase();
                                    if (sa === sb) return 0;
                                    return (sa < sb ? -1 : 1) * dir;
                                }
                                // Close the panel after save
                                var $panel = $slideBlock.find('.foyer_slides_editor_slides_slide_schedule');
                                $panel.stop(true, true).slideUp(120);
                            }

                            function updateSortIndicators(){
                                var arrows = { asc: '\u25B2', desc: '\u25BC' };
                                $table.find('thead th.foyer-sort-col .sort-ind').text('');
                                var $th = $table.find('thead th.foyer-sort-col[data-sort="'+sortKey+'"]');
                                $th.find('.sort-ind').text(arrows[sortDir] || '');
                            }

                            function sortRows(){
                                var $tbody = $table.find('tbody');
                                var visible = $rows.filter(':visible').get();
                                visible.sort(compareRows);
                                $tbody.append(visible);
                                $tbody.append($rows.filter(':hidden'));
                                updateSortIndicators();
                            }

                            function refresh(){
                                // First, show all rows to allow filtering logic to work on full set
                                $rows.show();
                                applyFilters();
                                sortRows();
                                paginate();
                            }

                            $search.on('input', function(){ currentPage = 1; refresh(); });
                            $hideInChannel.on('change', function(){ currentPage = 1; refresh(); });
                            $perPage.on('change', function(){ currentPage = 1; refresh(); });
                            $prev.on('click', function(){ if(currentPage>1){ currentPage--; paginate(); } });
                            $next.on('click', function(){ currentPage++; paginate(); });

                            // Sorting handlers
                            $table.find('thead').on('click', 'th.foyer-sort-col', function(){
                                var key = $(this).data('sort');
                                if (!key) return;
                                if (key === sortKey) {
                                    sortDir = (sortDir === 'asc') ? 'desc' : 'asc';
                                } else {
                                    sortKey = key;
                                    sortDir = 'asc';
                                }
                                currentPage = 1;
                                refresh();
                            });

                            refresh();

                            $(document).on('click', '.foyer_add_slide_btn', function(e){
                                e.preventDefault();
                                var $btn = $(this);
                                var slideId = parseInt($btn.data('slide-id'), 10);
                                if(!channelId || !slideId) return;
                                $btn.prop('disabled', true);
                                $.post(ajaxurl, {
                                    action: 'foyer_slides_editor_add_slide',
                                    channel_id: channelId,
                                    slide_id: slideId,
                                    nonce: (window.foyer_slides_editor_security ? foyer_slides_editor_security.nonce : '')
                                })
                                .done(function(html){
                                    // Replace slides list with refreshed HTML
                                    var $list = $('.foyer_slides_editor_slides');
                                    if($list.length){ $list.replaceWith(html); }
                                    // Mark row as in-channel and disable button
                                    var $row = $table.find('tr[data-slide-id="'+slideId+'"]');
                                    $row.attr('data-in-channel','1');
                                    $row.find('.foyer_add_slide_btn').prop('disabled', true).text('<?php echo esc_js( __( 'Added', 'foyer' ) ); ?>');
                                    refresh();
                                })
                                .always(function(){
                                    $btn.prop('disabled', false);
                                });
                            });
                        });
                    })(jQuery);
                    </script>
                </div>
                <?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the HTML to set the slides duration in the slides settings meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post	The post object of the current display.
	 * @return	string	$html	The HTML to set the slides duration in the slides settings meta box.
	 */
	static function get_set_duration_html( $post ) {

		$duration_options = self::get_slides_duration_options();
		$default_duration = Foyer_Slides::get_default_slides_duration();

		$default_option_name = '(' . __( 'Default', 'foyer' );
		if ( ! empty( $duration_options[ $default_duration ] ) ) {
			$default_option_name .= ' [' . $duration_options[ $default_duration ] . ']';
		}
		$default_option_name .= ')';

		$channel = new Foyer_Channel( $post );
		$selected_duration = $channel->get_saved_slides_duration();

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_slides_settings_duration">
						<?php echo esc_html__( 'Duration', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_slides_settings_duration" name="foyer_slides_settings_duration">
						<option value=""><?php echo esc_html( $default_option_name ); ?></option>
						<?php
							foreach ( $duration_options as $key => $name ) {
								$selected = '';
								if ( $selected_duration == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $name ); ?>
									</option>
								<?php
							}
						?>
					</select>
				</td>
			</tr>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the HTML to set the slides transition in the slides settings meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post	The post object of the current display.
	 * @return	string	$html	The HTML to set the slides transition in the slides settings meta box.
	 */
	static function get_set_transition_html( $post ) {

		$transition_options = self::get_slides_transition_options();
		$default_transition = Foyer_Slides::get_default_slides_transition();

		$default_option_name = '(' . __( 'Default', 'foyer' );
		if ( ! empty( $transition_options[ $default_transition ] ) ) {
			$default_option_name .= ' [' . $transition_options[ $default_transition ] . ']';
		}
		$default_option_name .= ')';

		$channel = new Foyer_Channel( $post );
		$selected_transition = $channel->get_saved_slides_transition();

		ob_start();

		?>
			<tr>
				<th>
					<label for="foyer_slides_settings_transition">
						<?php echo esc_html__( 'Transition', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<select id="foyer_slides_settings_transition" name="foyer_slides_settings_transition">
						<option value=""><?php echo esc_html( $default_option_name ); ?></option>
						<?php
							foreach ( $transition_options as $key => $name ) {
								$selected = '';
								if ( $selected_transition == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $name ); ?>
									</option>
								<?php
							}
						?>
					</select>
				</td>
			</tr>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the slides duration options.
	 *
	 * @since	1.0.0
	 * @since	1.2.4	Added longer slide durations, up to 120 seconds.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	array	The slides duration options.
	 */
	static function get_slides_duration_options() {

		for ( $sec = 2; $sec <= 20; $sec++ ) {
			$secs[] = $sec;
		}
		for ( $sec = 25; $sec <= 60; $sec += 5 ) {
			$secs[] = $sec;
		}
		for ( $sec = 90; $sec <= 120; $sec += 30 ) {
			$secs[] = $sec;
		}

		$slides_duration_options = array();
		foreach ( $secs as $sec ) {
			$slides_duration_options[ $sec ] = $sec . ' ' . _n( 'second', 'seconds', $sec, 'foyer' );
		}

		/**
		 * Filter available slides duration options.
		 *
		 * @since	1.0.0
		 * @param	array	$slides_duration_options	The currently available slides duration options.
		 */
		$slides_duration_options = apply_filters( 'foyer/slides/duration/options', $slides_duration_options );

		return $slides_duration_options;
	}

	/**
	 * Gets the HTML that lists all slides in the slides editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped and sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 * @since	1.5.0	Added a foyer-slide-is-stack class to stack slides.
	 *					Added an overlay for slides containing slide title, format and background, to be shown on hover.
	 * @since	1.5.1	Removed the translatable string 'x' to make translation easier.
	 * @since	1.7.4	Added a filter that allows diplaying of slide previews to be disabled.
	 *
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists all slides in the slides editor.
	 */
        static function get_slides_list_html( $post ) {

            $channel = new Foyer_Channel( $post );
            $slides = $channel->get_slides();

            // Load existing per-slide windows (UTC timestamps)
            $slide_windows = get_post_meta( $channel->ID, 'foyer_channel_slide_windows', true );
            if ( empty( $slide_windows ) || ! is_array( $slide_windows ) ) {
                $slide_windows = array();
            }

		/**
		 * Filters whether to display slide previews.
		 *
		 * @since	1.7.4
		 *
		 * @param	bool	$display_slide_previews		Indicates whether to display slide previews on the channel
		 *												admin screen or not.
		 */
		$display_slide_previews = apply_filters( 'foyer/admin/channel/display_slide_previews', true );

		ob_start();

		?>
			<div class="foyer_slides_editor_slides">
				<?php

					if ( empty( $slides ) ) {
						?><p>
							<?php echo esc_html__( 'No slides in this channel yet.', 'foyer' ); ?>
						</p><?php
					}
					else {

						$i = 0;
						foreach( $slides as $slide ) {

							$slide_url = get_permalink( $slide->ID );
							$slide_url = add_query_arg( 'foyer-preview', 1, $slide_url );
							$slide_format_data = Foyer_Slides::get_slide_format_by_slug( $slide->get_format() );
							$slide_background_data = Foyer_Slides::get_slide_background_by_slug( $slide->get_background() );

							?>
								<div class="foyer_slides_editor_slides_slide<?php
									if ( $slide->is_stack() ) { echo ' foyer-slide-is-stack'; }
								?>"
									data-slide-id="<?php echo intval( $slide->ID ); ?>"
									data-slide-key="<?php echo $i; ?>"
								>
									<div class="foyer_slides_editor_slides_slide_iframe_container">
										<div class="foyer_slides_editor_slides_slide_iframe_container_overlay">
											<h4><?php echo esc_html( get_the_title( $slide->ID ) ); ?></h4>
											<dl>
												<dt><?php _e( 'Format', 'foyer'); ?></dt>
												<dd><?php echo esc_html( $slide_format_data['title'] ); ?></dd>
											</dl>
											<dl>
												<dt><?php _e( 'Background', 'foyer'); ?></dt>
												<dd><?php echo esc_html( $slide_background_data['title'] ); ?></dd>
											</dl>
										</div>
										<?php if ( $display_slide_previews ) { ?>
											<iframe src="<?php echo esc_url( $slide_url ); ?>" width="1080" height="1920"></iframe>
										<?php } ?>
									</div>
                                <div class="foyer_slides_editor_slides_slide_caption">
                                    <?php echo esc_html_x( 'Slide', 'slide cpt', 'foyer' ) . ' ' . ( $i + 1 ); ?>
                                    (<a href="#" class="foyer_slides_editor_slides_slide_remove">x</a>)
                                    <?php
                                        // Compact summary badge if a window is set (fits under 108px preview)
                                        $summary_text = '';
                                        if ( isset( $slide_windows[ $slide->ID ] ) && ( ! empty( $slide_windows[ $slide->ID ]['start'] ) || ! empty( $slide_windows[ $slide->ID ]['end'] ) ) ) {
                                            $s = isset( $slide_windows[ $slide->ID ]['start'] ) ? intval( $slide_windows[ $slide->ID ]['start'] ) : 0;
                                            $e = isset( $slide_windows[ $slide->ID ]['end'] ) ? intval( $slide_windows[ $slide->ID ]['end'] ) : 0;
                                            $fmt_day_time = 'd.m. H:i';
                                            $fmt_time = 'H:i';
                                            $sv = $s ? date_i18n( $fmt_day_time, $s + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true ) : '';
                                            $ev = $e ? date_i18n( $fmt_day_time, $e + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true ) : '';
                                            if ( $sv && $ev ) {
                                                // If same day, compress to d.m. H:i–H:i
                                                if ( date_i18n( 'Ymd', $s, true ) === date_i18n( 'Ymd', $e, true ) ) {
                                                    $ev_time = date_i18n( $fmt_time, $e + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
                                                    $summary_text = $sv . '–' . $ev_time;
                                                } else {
                                                    $summary_text = $sv . '–' . $ev;
                                                }
                                            } elseif ( $sv ) {
                                                $summary_text = '≥ ' . $sv;
                                            } elseif ( $ev ) {
                                                $summary_text = '≤ ' . $ev;
                                            }
                                        }
                                        if ( ! empty( $summary_text ) ) {
                                            ?>
                                            <span class="foyer-slide-window-badge" style="display:block; max-width:108px; margin:6px auto 0; color:#333; background:#eef3ff; border:1px solid #c9d8ff; padding:2px 4px; border-radius:3px; font-size:11px; line-height:1.2; white-space:normal; word-break:break-word;">
                                                <?php echo esc_html( $summary_text ); ?>
                                            </span>
                                            <?php
                                        }
                                    ?>
                                    <button type="button" class="button-link foyer-slide-window-toggle" data-slide-id="<?php echo intval( $slide->ID ); ?>" style="margin-left:8px;">
                                        <?php echo esc_html__( 'Zeit bearbeiten', 'foyer' ); ?>
                                    </button>
                                </div>
                                <?php
                                    $scheduler_defaults = Foyer_Admin_Display::get_channel_scheduler_defaults();
                                    $w = isset( $slide_windows[ $slide->ID ] ) ? $slide_windows[ $slide->ID ] : array();
                                    $start_val = '';
                                    $end_val = '';
                                    if ( ! empty( $w['start'] ) ) {
                                        $start_val = date_i18n( $scheduler_defaults['datetime_format'], intval( $w['start'] ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
                                    }
                                    if ( ! empty( $w['end'] ) ) {
                                        $end_val = date_i18n( $scheduler_defaults['datetime_format'], intval( $w['end'] ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
                                    }
                                ?>
                                <div class="foyer_slides_editor_slides_slide_schedule" style="padding:8px 12px 12px; background:#f8f8f8; border:1px solid #e2e2e2; margin-top:6px; display:none;">
                                    <label style="display:inline-block; min-width:90px;" for="foyer_slide_window_start_<?php echo intval( $slide->ID ); ?>"><?php echo esc_html__( 'Visible from', 'foyer' ); ?></label>
                                    <input type="text" class="foyer-slide-window-start" id="foyer_slide_window_start_<?php echo intval( $slide->ID ); ?>" value="<?php echo esc_attr( $start_val ); ?>" style="max-width:180px;" />
                                    <span style="display:inline-block; min-width:70px; margin-left:12px;"><?php echo esc_html__( 'Until', 'foyer' ); ?></span>
                                    <input type="text" class="foyer-slide-window-end" id="foyer_slide_window_end_<?php echo intval( $slide->ID ); ?>" value="<?php echo esc_attr( $end_val ); ?>" style="max-width:180px;" />
                                    <button type="button" class="button foyer-slide-window-save" data-slide-id="<?php echo intval( $slide->ID ); ?>" style="margin-left:8px;">
                                        <?php echo esc_html__( 'Save', 'foyer' ); ?>
                                    </button>
                                </div>
                            </div>
							<?php

							$i++;
						}
					}
				?>
                </div>
                <script type="text/javascript">
                (function($){
                    $(function(){
                        // Init datetimepickers for per-slide windows (lazy on first open)
                        function initPickers($scope){
                            if (!window.foyer_channel_scheduler_defaults) return;
                            $scope.find('.foyer-slide-window-start, .foyer-slide-window-end').each(function(){
                                var $input = $(this);
                                if ($input.data('picker-initialized')) return;
                                $input.foyer_datetimepicker({
                                    format: foyer_channel_scheduler_defaults.datetime_format,
                                    dayOfWeekStart: foyer_channel_scheduler_defaults.start_of_week,
                                    step: 15,
                                    validateOnBlur: false
                                });
                                $input.data('picker-initialized', true);
                            });
                        }

                        // Toggle schedule panel
                        $(document).on('click', '.foyer-slide-window-toggle', function(e){
                            e.preventDefault();
                            e.stopPropagation();
                            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                            var $btn = $(this);
                            var $slideBlock = $btn.closest('.foyer_slides_editor_slides_slide');
                            var $panel = $slideBlock.find('.foyer_slides_editor_slides_slide_schedule');
                            $panel.stop(true, true).slideToggle(120, function(){
                                if ($panel.is(':visible')) initPickers($panel);
                            });
                            return false;
                        });

                        // Save per-slide window
                            $(document).on('click', '.foyer-slide-window-save', function(e){
                                e.preventDefault();
                                var $btn = $(this);
                                var $wrap = $btn.closest('.foyer_slides_editor');
                                var channelId = $wrap.data('channel-id');
                                var slideId = parseInt($btn.data('slide-id'), 10);
                                var $slideBlock = $btn.closest('.foyer_slides_editor_slides_slide');
                                var start = $slideBlock.find('.foyer-slide-window-start').val();
                                var end = $slideBlock.find('.foyer-slide-window-end').val();
                                if(!channelId || !slideId) return;
                                $btn.prop('disabled', true);
                                $.post(ajaxurl, {
                                    action: 'foyer_channel_set_slide_window',
                                    channel_id: channelId,
                                    slide_id: slideId,
                                    start: start,
                                    end: end,
                                    nonce: (window.foyer_slides_editor_security ? foyer_slides_editor_security.nonce : '')
                                }).done(function(resp){
                                $btn.addClass('button-primary');
                                setTimeout(function(){ $btn.removeClass('button-primary'); }, 600);
                                // Update summary badge in caption
                                var $caption = $slideBlock.find('.foyer_slides_editor_slides_slide_caption');
                                var $badge = $caption.find('.foyer-slide-window-badge');
                                var txt = '';
                                if (start && end) txt = start + '  ' + end; // placeholder, will adjust below
                                else if (start) txt = ' ' + start; // ≥ symbol
                                else if (end) txt = ' ' + end; // ≤ symbol
                                // Use clean symbols: ≥ and ≤ and en dash
                                if (start && end) txt = start + ' – ' + end;
                                else if (start) txt = '≥ ' + start;
                                else if (end) txt = '≤ ' + end;
                                // Prefer server-provided compact summary if available
                                if (resp && resp.success && resp.data && typeof resp.data.summary !== 'undefined') {
                                    txt = resp.data.summary || '';
                                }
                                if (!txt) {
                                    $badge.remove();
                                } else if ($badge.length) {
                                    $badge.text(txt);
                                } else {
                                    $('<span/>', { 'class': 'foyer-slide-window-badge', text: txt })
                                      .attr('style','display:block; max-width:108px; margin:6px auto 0; color:#333; background:#eef3ff; border:1px solid #c9d8ff; padding:2px 4px; border-radius:3px; font-size:11px; line-height:1.2; white-space:normal; word-break:break-word;')
                                      .appendTo($caption);
                                }
                                }).always(function(){
                                    $btn.prop('disabled', false);
                                });
                            });
                    });
                })(jQuery);
                </script>
                <?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the slides transition options.
	 *
	 * @since	1.0.0
	 * @since	1.2.4	Added a ‘No transition’ option.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	array	The slides transition options.
	 */
	static function get_slides_transition_options() {

		$slides_transition_options = array(
			'fade' => __( 'Fade', 'foyer' ),
			'slide' => __( 'Slide', 'foyer' ),
			'none' => __( 'No transition', 'foyer' ),
		);

		/**
		 * Filter available slides transition options.
		 *
		 * @since	1.0.0
		 * @param	array	$slides_transition_options	The currently available slides transition options.
		 */
		$slides_transition_options = apply_filters( 'foyer/slides/transition/options', $slides_transition_options );

		return $slides_transition_options;
	}

	/**
	 * Localizes the JavaScript for the channel admin area.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.2.6	Changed handle of script to {plugin_name}-admin.
	 * @since	1.3.2	Changed method to static.
	 */
    static function localize_scripts() {

        $defaults = array( 'confirm_remove_message' => esc_html__( 'Are you sure you want to remove this slide from the channel?', 'foyer' ) );
        wp_localize_script( Foyer::get_plugin_name() . '-admin', 'foyer_slides_editor_defaults', $defaults );

        $security = array( 'nonce' => wp_create_nonce( 'foyer_slides_editor_ajax_nonce' ) );
        wp_localize_script( Foyer::get_plugin_name() . '-admin', 'foyer_slides_editor_security', $security );

        // Nonce for channel list actions (e.g., toggle favorite)
        $chan_sec = array( 'nonce' => wp_create_nonce( 'foyer_channel_admin_ajax_nonce' ) );
        wp_localize_script( Foyer::get_plugin_name() . '-admin', 'foyer_channels_list_security', $chan_sec );

        // Lightweight inline script to toggle favorite in list table
        $inline_js = <<<'JS'
(function($){$(function(){
$(document).on('click','.foyer-fav-toggle',function(e){
    e.preventDefault();
    var $a=$(this);
    var id=$a.data('postid');
    if(!id) return;
    var willSet=$a.hasClass('is-fav')? '0':'1';
    $a.addClass('is-busy');
    $.post(ajaxurl,{
        action:'foyer_channel_toggle_favorite',
        nonce:(window.foyer_channels_list_security?foyer_channels_list_security.nonce:''),
        post_id:id,
        set:willSet
    }).done(function(resp){
        if(resp&&resp.success){
            if(willSet==='1'){ $a.addClass('is-fav').text('★'); }
            else { $a.removeClass('is-fav').text('☆'); }
        }
    }).always(function(){ $a.removeClass('is-busy'); });
});
});})(jQuery);
JS;
        if ( function_exists( 'wp_add_inline_script' ) ) {
            wp_add_inline_script( Foyer::get_plugin_name() . '-admin', $inline_js, 'after' );
        }
    }

	/**
	 * Removes the sample permalink from the Channel edit screen.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	string	$sample_permalink
	 * @return 	string
	 */
	static function remove_sample_permalink( $sample_permalink ) {

		$screen = get_current_screen();

		// Bail if not on Channel edit screen.
		if ( empty( $screen ) || Foyer_Channel::post_type_name != $screen->post_type ) {
			return $sample_permalink;
		}

		return '';
	}

	/**
	 * Removes a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.2.4	You can now remove the first slide (slide_key 0) of a channel. Fixes #1.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	void
	 */
    static function remove_slide_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = intval( $_POST['channel_id'] );
		$remove_slide_key = intval( $_POST['slide_key'] );

		if ( empty( $channel_id ) ) {
			wp_die();
		}

		/* Check if this post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$channel = new Foyer_Channel( $channel_id );
		$slides = $channel->get_slides();

		/* Check if the channel has slides */
		if ( empty( $slides ) ) {
			wp_die();
		}

		$new_slides = array();
		foreach( $slides as $slide ) {
			$new_slides[] = $slide->ID;
		}

		if ( ! isset( $new_slides[$remove_slide_key] ) ) {
			wp_die();
		}

        $removed_id = $new_slides[$remove_slide_key];
        unset( $new_slides[$remove_slide_key] );
        update_post_meta( $channel_id, Foyer_Slide::post_type_name, $new_slides );
        // Remove any window entry for this slide
        $windows = get_post_meta( $channel_id, 'foyer_channel_slide_windows', true );
        if ( ! empty( $windows ) && is_array( $windows ) && isset( $windows[ $removed_id ] ) ) {
            unset( $windows[ $removed_id ] );
            update_post_meta( $channel_id, 'foyer_channel_slide_windows', $windows );
        }

		echo self::get_slides_list_html( get_post( $channel_id ) );
		wp_die();
	}

	/**
	 * Reorders slides over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return void
	 */
    static function reorder_slides_over_ajax() {

		check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

		$channel_id = intval( $_POST['channel_id'] );
		$slide_ids = array_map( 'intval', $_POST['slide_ids'] );

		if ( empty( $channel_id ) || empty( $slide_ids ) ) {
			wp_die();
		}

		/* Check if this post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

        $new_slides = array();
        foreach( $slide_ids as $slide_id ) {
            $new_slides[] = $slide_id;
        }

        update_post_meta( $channel_id, Foyer_Slide::post_type_name, $new_slides );

        echo self::get_slides_list_html( get_post( $channel_id ) );
        wp_die();
    }

    /**
     * Sets per-slide schedule window (start/end) over AJAX.
     *
     * Expects local datetime strings in WP timezone; converts to UTC timestamps.
     *
     * @since 1.7.6
     * @return void
     */
    static function set_slide_window_over_ajax() {

        check_ajax_referer( 'foyer_slides_editor_ajax_nonce', 'nonce' , true );

        $channel_id = intval( $_POST['channel_id'] );
        $slide_id   = intval( $_POST['slide_id'] );
        $start_in   = isset( $_POST['start'] ) ? wp_unslash( $_POST['start'] ) : '';
        $end_in     = isset( $_POST['end'] ) ? wp_unslash( $_POST['end'] ) : '';

        if ( empty( $channel_id ) || empty( $slide_id ) ) {
            wp_send_json_error( array( 'message' => 'Missing ids' ), 400 );
        }

        // Parse using site timezone, convert to UTC
        $defaults = Foyer_Admin_Display::get_channel_scheduler_defaults();
        $fmt = isset( $defaults['datetime_format'] ) ? $defaults['datetime_format'] : 'Y-m-d H:i';
        $tz = wp_timezone();
        $start_ts_utc = null;
        $end_ts_utc   = null;

        if ( ! empty( $start_in ) ) {
            try {
                $dt = date_create_from_format( $fmt, $start_in, $tz );
                if ( $dt instanceof DateTime ) {
                    $dt->setTimezone( new DateTimeZone( 'UTC' ) );
                    $start_ts_utc = $dt->getTimestamp();
                }
            } catch ( Exception $e ) {}
        }
        if ( ! empty( $end_in ) ) {
            try {
                $dt = date_create_from_format( $fmt, $end_in, $tz );
                if ( $dt instanceof DateTime ) {
                    $dt->setTimezone( new DateTimeZone( 'UTC' ) );
                    $end_ts_utc = $dt->getTimestamp();
                }
            } catch ( Exception $e ) {}
        }

        // Validate order if both set
        if ( ! is_null( $start_ts_utc ) && ! is_null( $end_ts_utc ) && $end_ts_utc < $start_ts_utc ) {
            wp_send_json_error( array( 'message' => 'End before start' ), 400 );
        }

        $windows = get_post_meta( $channel_id, 'foyer_channel_slide_windows', true );
        if ( empty( $windows ) || ! is_array( $windows ) ) { $windows = array(); }
        $windows[ $slide_id ] = array(
            'start' => $start_ts_utc,
            'end'   => $end_ts_utc,
        );
        update_post_meta( $channel_id, 'foyer_channel_slide_windows', $windows );
        // Build compact summary string for UI badge
        $summary = '';
        $fmt_day_time = 'd.m. H:i';
        $fmt_time = 'H:i';
        if ( $start_ts_utc && $end_ts_utc ) {
            $same_day = ( gmdate( 'Ymd', $start_ts_utc ) === gmdate( 'Ymd', $end_ts_utc ) );
            $sv = date_i18n( $fmt_day_time, $start_ts_utc + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
            if ( $same_day ) {
                $ev = date_i18n( $fmt_time, $end_ts_utc + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
                $summary = $sv . '–' . $ev;
            } else {
                $ev = date_i18n( $fmt_day_time, $end_ts_utc + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
                $summary = $sv . '–' . $ev;
            }
        } elseif ( $start_ts_utc ) {
            $summary = '≥ ' . date_i18n( $fmt_day_time, $start_ts_utc + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
        } elseif ( $end_ts_utc ) {
            $summary = '≤ ' . date_i18n( $fmt_day_time, $end_ts_utc + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true );
        }

        wp_send_json_success( array( 'ok' => true, 'summary' => $summary ) );
    }

	/**
	 * Saves all custom fields for a channel.
	 *
	 * Triggered when a channel is submitted from the channel admin form.
	 *
	 * @since 	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	int		$post_id	The channel id.
	 * @return void
	 */
	static function save_channel( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		/* Check if our nonce is set */
		if ( ! isset( $_POST[Foyer_Channel::post_type_name.'_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST[Foyer_Channel::post_type_name.'_nonce'];

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, Foyer_Channel::post_type_name ) ) {
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

		/* Check if slides settings are included (empty or not) in form */
		if (
			! isset( $_POST['foyer_slides_settings_duration'] ) ||
			! isset( $_POST['foyer_slides_settings_transition'] )
		) {
			return $post_id;
		}

		$foyer_slides_settings_duration = intval( $_POST['foyer_slides_settings_duration'] );
		if ( empty( $foyer_slides_settings_duration ) ) {
			$foyer_slides_settings_duration = '';
		}

		$foyer_slides_settings_transition = sanitize_title( $_POST['foyer_slides_settings_transition'] );
		if ( empty( $foyer_slides_settings_transition ) ) {
			$foyer_slides_settings_transition = '';
		}

		update_post_meta( $post_id, Foyer_Channel::post_type_name . '_slides_duration' , $foyer_slides_settings_duration );
		update_post_meta( $post_id, Foyer_Channel::post_type_name . '_slides_transition' , $foyer_slides_settings_transition );

		// Save favorite flag (checkbox)
		$fav = isset( $_POST['foyer_channel_is_favorite'] ) ? '1' : '';
		if ( ! empty( $fav ) ) {
			update_post_meta( $post_id, 'foyer_channel_is_favorite', '1' );
		} else {
			delete_post_meta( $post_id, 'foyer_channel_is_favorite' );
		}
	}

	/**
	 * Outputs the content of the slides editor meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current channel.
	 */
	static function slides_editor_meta_box( $post ) {

		wp_nonce_field( Foyer_Channel::post_type_name, Foyer_Channel::post_type_name.'_nonce' );

		ob_start();

		?>
			<div class="foyer_meta_box foyer_slides_editor" data-channel-id="<?php echo intval( $post->ID ); ?>">

				<?php
					echo self::get_slides_list_html( $post );
					echo self::get_add_slide_html();
				?>

			</div>
		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the content of the slides settings meta box.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current channel.
	 */
	static function slides_settings_meta_box( $post ) {

		wp_nonce_field( Foyer_Channel::post_type_name, Foyer_Channel::post_type_name.'_nonce' );

		ob_start();

		?>
			<table class="foyer_meta_box_form form-table foyer_slides_settings_form">
				<tbody>
					<?php


						echo self::get_set_duration_html( $post );
						echo self::get_set_transition_html( $post );
						echo self::get_set_favorite_html( $post );

					?>
				</tbody>
			</table>
		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Gets the HTML to set the favorite flag in the slides settings meta box.
	 *
	 * @since 1.8.0
	 *
	 * @param WP_Post $post The post object of the current channel.
	 * @return string $html  The HTML to set the favorite flag in the slides settings meta box.
	 */
	static function get_set_favorite_html( $post ) {
		$checked = get_post_meta( $post->ID, 'foyer_channel_is_favorite', true ) ? 'checked="checked"' : '';

		ob_start();
		?>
			<tr>
				<th>
					<label for="foyer_channel_is_favorite">
						<?php echo esc_html__( 'Favorite', 'foyer' ); ?>
					</label>
				</th>
				<td>
					<label style="display:flex; align-items:center; gap:6px;">
						<input type="checkbox" id="foyer_channel_is_favorite" name="foyer_channel_is_favorite" value="1" <?php echo $checked; ?> />
						<span><?php echo esc_html__( 'Mark this channel as favorite', 'foyer' ); ?></span>
					</label>
				</td>
			</tr>
		<?php

		$html = ob_get_clean();
		return $html;
	}
}
