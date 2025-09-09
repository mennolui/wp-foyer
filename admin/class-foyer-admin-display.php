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

    // Removed: legacy single temporary channel meta box

    /**
     * Adds the multiple scheduled channels meta box (list).
     *
     * @since 1.7.6
     */
    static function add_channel_scheduler_list_meta_box() {
        add_meta_box(
            'foyer_channel_scheduler_list',
            __( 'Schedule channels (list)', 'foyer' ),
            array( __CLASS__, 'channel_scheduler_list_meta_box' ),
            Foyer_Display::post_type_name,
            'normal',
            'high'
        );
    }

    /**
     * Outputs the multi-entry scheduler.
     *
     * @since 1.7.6
     */
    static function channel_scheduler_list_meta_box( $post ) {
        wp_nonce_field( Foyer_Display::post_type_name, Foyer_Display::post_type_name.'_nonce' );

        $display = new Foyer_Display( $post );
        $schedules = $display->get_schedule();
        if ( empty( $schedules ) || ! is_array( $schedules ) ) { $schedules = array(); }
        $channels = Foyer_Channels::get_posts();

        ob_start();
        ?>
        <h4><?php echo esc_html__( 'Add channels', 'foyer' ); ?></h4>
        <?php
            $channels_ordered = array();
            $favorites = array();
            $others = array();
            foreach ( $channels as $ch ) {
                $is_fav = get_post_meta( $ch->ID, 'foyer_channel_is_favorite', true );
                if ( $is_fav ) { $favorites[] = $ch; } else { $others[] = $ch; }
            }
            foreach ( $favorites as $ch ) { $channels_ordered[] = $ch; }
            foreach ( $others as $ch ) { $channels_ordered[] = $ch; }
        ?>
        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin:4px 0 8px;">
            <label for="foyer_sched_selector_search" style="margin-right:6px;">
                <?php echo esc_html__( 'Search', 'foyer' ); ?>
            </label>
            <input type="search" id="foyer_sched_selector_search" class="regular-text" placeholder="<?php echo esc_attr__( 'Search by title or author…', 'foyer' ); ?>" style="max-width:280px;" />
            <label for="foyer_sched_selector_per_page" style="margin-left:auto;">
                <?php echo esc_html__( 'Rows per page', 'foyer' ); ?>
            </label>
                        <select id="foyer_sched_selector_per_page">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
        </div>
        <table class="widefat fixed striped" id="foyer_sched_selector" style="margin-bottom:12px;">
            <thead>
                <tr>
                    <th style="width:110px;">&nbsp;</th>
                    <th style="width:30px; text-align:center;" title="<?php echo esc_attr__( 'Favorite', 'foyer' ); ?>">★</th>
                    <th data-sort="title" class="foyer-sort-col"><span class="sort-label"><?php echo esc_html_x( 'Title', 'post title', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                    <th data-sort="author" class="foyer-sort-col" style="width:160px;"><span class="sort-label"><?php echo esc_html__( 'Author', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                    <th data-sort="date" class="foyer-sort-col" style="width:180px;"><span class="sort-label"><?php echo esc_html__( 'Date', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                    <th data-sort="slides" class="foyer-sort-col" style="width:120px;"><span class="sort-label"><?php echo esc_html__( 'Slides', 'foyer' ); ?></span> <span class="sort-ind"></span></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $channels_ordered ) ) : ?>
                    <tr><td colspan="6"><?php echo esc_html__( 'No channels found.', 'foyer' ); ?></td></tr>
                <?php else : ?>
                    <?php foreach ( $channels_ordered as $ch ) :
                        $author_name = get_the_author_meta( 'display_name', $ch->post_author );
                        $date_str = get_the_date( get_option( 'date_format' ), $ch ) . ' ' . get_the_time( get_option( 'time_format' ), $ch );
                        $date_ts = get_post_time( 'U', true, $ch );
                        $channel_obj = new Foyer_Channel( $ch );
                        $slides_count = count( $channel_obj->get_slides() );
                        $is_fav = get_post_meta( $ch->ID, 'foyer_channel_is_favorite', true );
                    ?>
                    <tr data-title="<?php echo esc_attr( get_the_title( $ch->ID ) ); ?>" data-author="<?php echo esc_attr( $author_name ); ?>" data-date-ts="<?php echo esc_attr( $date_ts ); ?>" data-slides="<?php echo esc_attr( $slides_count ); ?>" data-fav="<?php echo $is_fav ? '1' : '0'; ?>">
                        <td>
                            <button type="button" class="button button-primary foyer_sched_add_btn" data-channel-id="<?php echo intval( $ch->ID ); ?>" data-channel-title="<?php echo esc_attr( get_the_title( $ch->ID ) ); ?>"><?php echo esc_html__( 'Add', 'foyer' ); ?></button>
                        </td>
                        <td style="text-align:center;">
                            <?php if ( $is_fav ) { echo '<span style="color:#d98900; font-size:16px;">&#9733;</span>'; } ?>
                        </td>
                        <td><?php echo esc_html( get_the_title( $ch->ID ) ); ?></td>
                        <td><?php echo esc_html( $author_name ); ?></td>
                        <td><?php echo esc_html( $date_str ); ?></td>
                        <td><?php echo esc_html( $slides_count ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div id="foyer_sched_selector_pager" style="display:flex; gap:8px; align-items:center; justify-content:flex-end; margin:6px 0 12px;">
            <button type="button" class="button" id="foyer_sched_selector_prev">&laquo; <?php echo esc_html__( 'Prev', 'foyer' ); ?></button>
            <span id="foyer_sched_selector_page_info"></span>
            <button type="button" class="button" id="foyer_sched_selector_next"><?php echo esc_html__( 'Next', 'foyer' ); ?> &raquo;</button>
        </div>

        <h4><?php echo esc_html__( 'Scheduled channels', 'foyer' ); ?></h4>
        <style>
            /* Row highlighting for schedule status */
            #foyer_sched_list tbody tr.foyer-sched-active { background-color: #e9f7ef; }
            #foyer_sched_list tbody tr.foyer-sched-future { background-color: #e8f1fd; }
            #foyer_sched_list tbody tr.foyer-sched-past { background-color: #fdecea; }
            /* Keep action buttons on one line */
            #foyer_sched_list td:last-child { white-space: nowrap; }
            /* Right-align action column */
            #foyer_sched_list td:last-child, #foyer_sched_list th:last-child { text-align: right; }
        </style>
        <table class="widefat fixed striped" id="foyer_sched_list">
            <thead>
                <tr>
                    <th style="width:30%"><?php echo esc_html__( 'Channel', 'foyer' ); ?></th>
                    <th style="width:35%"><?php echo esc_html__( 'Show from', 'foyer' ); ?></th>
                    <th style="width:35%"><?php echo esc_html__( 'Until', 'foyer' ); ?></th>
                    <th style="width:180px">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $schedules ) ) : ?>
                    <tr class="foyer-sched-empty"><td colspan="4"><?php echo esc_html__( 'No scheduled channels.', 'foyer' ); ?></td></tr>
                <?php else :
                    // Sort by start time ascending; empty/missing start goes to the end
                    usort( $schedules, function( $a, $b ) {
                        $sa = isset( $a['start'] ) && is_numeric( $a['start'] ) ? intval( $a['start'] ) : PHP_INT_MAX;
                        $sb = isset( $b['start'] ) && is_numeric( $b['start'] ) ? intval( $b['start'] ) : PHP_INT_MAX;
                        if ( $sa === $sb ) { return 0; }
                        return ( $sa < $sb ) ? -1 : 1;
                    } );
                    foreach ( $schedules as $sch ) {
                    $start_val = ! empty( $sch['start'] ) ? date_i18n( self::get_channel_scheduler_defaults()['datetime_format'], intval( $sch['start'] ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true ) : '';
                    $end_val   = ! empty( $sch['end'] ) ? date_i18n( self::get_channel_scheduler_defaults()['datetime_format'], intval( $sch['end'] ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS, true ) : '';
                    $now_utc   = current_time( 'timestamp', true );
                    $start_utc = isset( $sch['start'] ) ? intval( $sch['start'] ) : null;
                    $end_utc   = isset( $sch['end'] ) ? intval( $sch['end'] ) : null;
                    $status_class = '';
                    if ( ! is_null( $end_utc ) && $end_utc < $now_utc ) {
                        $status_class = 'foyer-sched-past';
                    } elseif ( ! is_null( $start_utc ) && $start_utc <= $now_utc && ( is_null( $end_utc ) || $end_utc >= $now_utc ) ) {
                        $status_class = 'foyer-sched-active';
                    } elseif ( ! is_null( $start_utc ) && $start_utc > $now_utc ) {
                        $status_class = 'foyer-sched-future';
                    }
                    ?>
                    <tr class="<?php echo esc_attr( $status_class ); ?>">
                        <td>
                            <?php $cid = ! empty( $sch['channel'] ) ? intval( $sch['channel'] ) : 0; $ctitle = $cid ? get_the_title( $cid ) : ''; ?>
                            <input type="hidden" name="foyer_channel_scheduler_list_channel[]" value="<?php echo $cid ? intval( $cid ) : ''; ?>" />
                            <span class="foyer-sched-channel-title"><?php echo esc_html( $ctitle ); ?></span>
                        </td>
                        <td>
                            <span class="foyer-sched-start-text"><?php echo $start_val ? esc_html( $start_val ) : '&mdash;'; ?></span>
                            <input type="hidden" class="foyer-sched-start-hidden" name="foyer_channel_scheduler_list_start[]" value="<?php echo esc_attr( $start_val ); ?>" />
                            <input type="text" class="foyer-datetime foyer-sched-start-input" value="<?php echo esc_attr( $start_val ); ?>" style="display:none;" />
                        </td>
                        <td>
                            <span class="foyer-sched-end-text"><?php echo $end_val ? esc_html( $end_val ) : '&mdash;'; ?></span>
                            <input type="hidden" class="foyer-sched-end-hidden" name="foyer_channel_scheduler_list_end[]" value="<?php echo esc_attr( $end_val ); ?>" />
                            <input type="text" class="foyer-datetime foyer-sched-end-input" value="<?php echo esc_attr( $end_val ); ?>" style="display:none;" />
                        </td>
                        <td>
                            <button type="button" class="button button-primary foyer-sched-save" style="display:none;"><?php echo esc_html__( 'Save', 'foyer' ); ?></button>
                            <button type="button" class="button foyer-sched-edit"><?php echo esc_html__( 'Edit', 'foyer' ); ?></button>
                            <button type="button" class="button foyer-sched-remove" title="<?php echo esc_attr__( 'Remove', 'foyer' ); ?>">&times;</button>
                        </td>
                    </tr>
                <?php } endif; ?>
            </tbody>
        </table>
        <p class="description"><?php echo esc_html__( 'Use the list above to add channels, then set the time window here. You have to Update the channel so that changes take effect.', 'foyer' ); ?></p>
        <script>
        (function($){
            $(function(){
                function initPickers($scope){
                    if (!window.foyer_channel_scheduler_defaults) return;
                    $scope.find('input.foyer-datetime').each(function(){
                        var $i=$(this); if ($i.data('dtp-init')) return;
                        $i.foyer_datetimepicker({
                            format: foyer_channel_scheduler_defaults.datetime_format,
                            dayOfWeekStart: foyer_channel_scheduler_defaults.start_of_week,
                            step: 15,
                            validateOnBlur: false
                        });
                        $i.data('dtp-init', true);
                    });
                }
                initPickers($('#foyer_sched_list'));
                // Sorting, search, pagination for Add selector
                (function(){
                    var $table = $('#foyer_sched_selector');
                    var $rows = $table.find('tbody > tr');
                    var $search = $('#foyer_sched_selector_search');
                    var $perPage = $('#foyer_sched_selector_per_page');
                    var $prev = $('#foyer_sched_selector_prev');
                    var $next = $('#foyer_sched_selector_next');
                    var $info = $('#foyer_sched_selector_page_info');
                    var sortKey = 'title';
                    var sortDir = 'asc';
                    var currentPage = 1;

                    function applyFilters(){
                        var q = ($search.val()||'').toLowerCase();
                        $rows.each(function(){
                            var $tr=$(this);
                            var title=(String($tr.data('title')||'')).toLowerCase();
                            var author=(String($tr.data('author')||'')).toLowerCase();
                            var visible = (!q || title.indexOf(q)!==-1 || author.indexOf(q)!==-1);
                            $tr.toggle(visible);
                        });
                    }
                    function compareRows(a,b){
                        var $a=$(a),$b=$(b),dir=(sortDir==='asc')?1:-1;
                        // Always list favorites before others
                        var aFav = parseInt($a.data('fav'),10)||0;
                        var bFav = parseInt($b.data('fav'),10)||0;
                        if (aFav !== bFav) return aFav ? -1 : 1;
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
                    function refresh(){
                        $rows.show();
                        applyFilters();
                        sortRows();
                        paginate();
                    }
                    $search.on('input', function(){ currentPage=1; refresh(); });
                    $perPage.on('change', function(){ currentPage=1; refresh(); });
                    $table.find('thead').on('click', 'th.foyer-sort-col', function(){
                        var key=$(this).data('sort');
                        if(!key) return;
                        if(key===sortKey){ sortDir=(sortDir==='asc')?'desc':'asc'; }
                        else { sortKey=key; sortDir='asc'; }
                        currentPage=1; refresh();
                    });
                    $prev.on('click', function(){ if(currentPage>1){ currentPage--; paginate(); } });
                    $next.on('click', function(){ currentPage++; paginate(); });
                    refresh();
                })();
                // Add new entry from selector
                $(document).on('click', '.foyer_sched_add_btn', function(){
                    var id = $(this).data('channel-id');
                    var title = $(this).data('channel-title');
                    if (!id) return;
                    var $tb=$('#foyer_sched_list tbody');
                    // Remove placeholder row if present
                    $tb.find('tr.foyer-sched-empty').remove();
                    var $row=$('<tr class="foyer-sched-future">\n'
                        +'<td><input type="hidden" name="foyer_channel_scheduler_list_channel[]" value="'+id+'" />'
                        +'<span class="foyer-sched-channel-title"></span></td>\n'
                        +'<td>'
                        +'<span class="foyer-sched-start-text">&mdash;</span>'
                        +'<input type="hidden" class="foyer-sched-start-hidden" name="foyer_channel_scheduler_list_start[]" value="" />'
                        +'<input type="text" class="foyer-datetime foyer-sched-start-input" value="" style="display:none;" />'
                        +'</td>\n'
                        +'<td>'
                        +'<span class="foyer-sched-end-text">&mdash;</span>'
                        +'<input type="hidden" class="foyer-sched-end-hidden" name="foyer_channel_scheduler_list_end[]" value="" />'
                        +'<input type="text" class="foyer-datetime foyer-sched-end-input" value="" style="display:none;" />'
                        +'</td>\n'
                        +'<td>'
                        +'<button type="button" class="button button-primary foyer-sched-save" style="display:none;">'+foyer_i18n_save()+'</button> '
                        +'<button type="button" class="button foyer-sched-edit">'+foyer_i18n_edit()+'</button> '
                        +'<button type="button" class="button foyer-sched-remove" title="'+foyer_i18n_remove()+'">&times;</button>'
                        +'</td>\n'
                        +'</tr>');
                    $row.find('.foyer-sched-channel-title').text(title);
                    $tb.append($row);
                    initPickers($row);
                });
                // Helper i18n labels
                function foyer_i18n_edit(){ return '<?php echo esc_js( __( 'Edit', 'foyer' ) ); ?>'; }
                function foyer_i18n_save(){ return '<?php echo esc_js( __( 'Save', 'foyer' ) ); ?>'; }
                function foyer_i18n_remove(){ return '<?php echo esc_js( __( 'Remove', 'foyer' ) ); ?>'; }
                // Toggle edit mode per row
                $(document).on('click', '.foyer-sched-edit', function(){
                    var $row = $(this).closest('tr');
                    $row.find('.foyer-sched-start-text, .foyer-sched-end-text').hide();
                    $row.find('.foyer-sched-start-input, .foyer-sched-end-input').show();
                    $row.find('.foyer-sched-edit').hide();
                    $row.find('.foyer-sched-save').show();
                    initPickers($row);
                });
                $(document).on('click', '.foyer-sched-save', function(){
                    var $row = $(this).closest('tr');
                    var startVal = $row.find('.foyer-sched-start-input').val();
                    var endVal   = $row.find('.foyer-sched-end-input').val();

                    // Build full set with current row pending values
                    var entries = [];
                    $('#foyer_sched_list tbody tr').each(function(){
                        var $r=$(this);
                        var s = ($r.is($row)) ? startVal : $r.find('.foyer-sched-start-hidden').val();
                        var e = ($r.is($row)) ? endVal   : $r.find('.foyer-sched-end-hidden').val();
                        var c = $r.find('input[name=\'foyer_channel_scheduler_list_channel[]\']').val();
                        if (c && s && e) { entries.push({channel:c, start:s, end:e}); }
                    });

                    // AJAX validate overlap server-side (reuses WP format + timezone)
                    $.post(ajaxurl, {
                        action: 'foyer_validate_schedule',
                        nonce: (window.foyer_display_ajax?foyer_display_ajax.nonce:''),
                        payload: JSON.stringify({ entries: entries })
                    }).done(function(resp){
                        if (resp && resp.success) {
                            // Commit values into hidden + UI and exit edit mode
                            $row.find('.foyer-sched-start-hidden').val(startVal);
                            $row.find('.foyer-sched-end-hidden').val(endVal);
                            $row.find('.foyer-sched-start-text').text(startVal || '—');
                            $row.find('.foyer-sched-end-text').text(endVal || '—');
                            $row.find('.foyer-sched-start-input, .foyer-sched-end-input').hide();
                            $row.find('.foyer-sched-start-text, .foyer-sched-end-text').show();
                            $row.find('.foyer-sched-save').hide();
                            $row.find('.foyer-sched-edit').show();
                        } else {
                            var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Validation failed';
                            alert(msg);
                        }
                    }).fail(function(){
                        alert('Validation failed');
                    });
                });
                $(document).on('click','.foyer-sched-remove', function(){
                    var $tb=$('#foyer_sched_list tbody');
                    $(this).closest('tr').remove();
                    // If now empty, show placeholder row
                    if ($tb.find('tr').length === 0) {
                        $tb.append('<tr class="foyer-sched-empty"><td colspan="4">'+<?php echo json_encode( esc_html__( 'No scheduled channels.', 'foyer' ) ); ?>+'</td></tr>');
                    }
                });
            });
        })(jQuery);
        </script>
        <?php
        echo ob_get_clean();
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
    // Removed: legacy single temporary channel meta box renderer

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

        // Use site-configured date and time formats
        $site_datetime_format = trim( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
        if ( empty( $site_datetime_format ) ) { $site_datetime_format = 'Y-m-d H:i'; }

        $defaults = array(
            'datetime_format' => $site_datetime_format,
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
                        <label for="foyer_default_channels_per_page" style="margin-left:auto;">
                            <?php echo esc_html__( 'Rows per page', 'foyer' ); ?>
                        </label>
                        <select id="foyer_default_channels_per_page">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <?php
                        $channels = Foyer_Channels::get_posts();
                        // Ensure currently selected default channel appears first in the table, then favorites, then the rest.
                        $channels_ordered = array();
                        $favorites = array();
                        $others = array();
                        $selected_id = intval( $default_channel );
                        foreach ( $channels as $ch ) {
                            if ( $selected_id && intval( $ch->ID ) === $selected_id ) { continue; }
                            $is_fav = get_post_meta( $ch->ID, 'foyer_channel_is_favorite', true );
                            if ( $is_fav ) { $favorites[] = $ch; } else { $others[] = $ch; }
                        }
                        if ( $selected_id ) {
                            $sel = get_post( $selected_id );
                            if ( $sel ) { $channels_ordered[] = $sel; }
                        }
                        foreach ( $favorites as $ch ) { $channels_ordered[] = $ch; }
                        foreach ( $others as $ch ) { $channels_ordered[] = $ch; }
                    ?>
                    <style>
                    /* Highlight selected default channel row */
                    #foyer_default_channels_table tbody tr.selected-channel {
                        background-color: #e9f7ef; /* light green */
                    }
                    </style>
                    <table class="widefat fixed striped" id="foyer_default_channels_table" style="max-width:100%;">
                        <thead>
                            <tr>
                                <th style="width:42px;">&nbsp;</th>
                                <th style="width:30px; text-align:center;" title="<?php echo esc_attr__( 'Favorite', 'foyer' ); ?>">★</th>
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
                            <?php foreach ( $channels_ordered as $channel_post ) :
                                $author_name = get_the_author_meta( 'display_name', $channel_post->post_author );
                                $date_ts = get_post_time( 'U', true, $channel_post );
                                $channel_obj = new Foyer_Channel( $channel_post );
                                $slides_count = count( $channel_obj->get_slides() );
                                $checked = $default_channel == $channel_post->ID ? 'checked="checked"' : '';
                                $is_fav = get_post_meta( $channel_post->ID, 'foyer_channel_is_favorite', true ) ? 1 : 0;
                            ?>
                            <tr class="<?php echo ( $default_channel == $channel_post->ID ) ? 'selected-channel' : ''; ?>" data-title="<?php echo esc_attr( get_the_title( $channel_post->ID ) ); ?>" data-author="<?php echo esc_attr( $author_name ); ?>" data-date-ts="<?php echo esc_attr( $date_ts ); ?>" data-slides="<?php echo esc_attr( $slides_count ); ?>" data-selected="<?php echo ( $default_channel == $channel_post->ID ) ? '1' : '0'; ?>" data-fav="<?php echo intval( $is_fav ); ?>">
                                <td>
                                    <input type="radio" name="foyer_channel_editor_default_channel" value="<?php echo intval( $channel_post->ID ); ?>" <?php echo $checked; ?> />
                                </td>
                                <td style="text-align:center;">
                                    <?php if ( $is_fav ) { echo '<span style="color:#d98900; font-size:16px;">&#9733;</span>'; } ?>
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
                    <div id="foyer_default_channels_pager" style="display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:8px;">
                        <button type="button" class="button" id="foyer_default_channels_prev">&laquo; <?php echo esc_html__( 'Prev', 'foyer' ); ?></button>
                        <span id="foyer_default_channels_page_info"></span>
                        <button type="button" class="button" id="foyer_default_channels_next"><?php echo esc_html__( 'Next', 'foyer' ); ?> &raquo;</button>
                    </div>
                    <script type="text/javascript">
                    (function($){
                        $(function(){
                            var $table = $('#foyer_default_channels_table');
                            var $rows = $table.find('tbody > tr');
                            var $search = $('#foyer_default_channel_search');
                            var $perPage = $('#foyer_default_channels_per_page');
                            var $prev = $('#foyer_default_channels_prev');
                            var $next = $('#foyer_default_channels_next');
                            var $info = $('#foyer_default_channels_page_info');
                            var sortKey = 'title';
                            var sortDir = 'asc';
                            var currentPage = 1;

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
                                // Always keep the currently selected channel at the top
                                var aSel = parseInt($a.data('selected'),10)||0;
                                var bSel = parseInt($b.data('selected'),10)||0;
                                if (aSel !== bSel) return aSel ? -1 : 1;
                                // Then list favorites before others
                                var aFav = parseInt($a.data('fav'),10)||0;
                                var bFav = parseInt($b.data('fav'),10)||0;
                                if (aFav !== bFav) return aFav ? -1 : 1;
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

                            function refresh(){
                                $rows.show();
                                applyFilters();
                                sortRows();
                                paginate();
                            }

                            $search.on('input', function(){ currentPage = 1; refresh(); });
                            $table.find('thead').on('click', 'th.foyer-sort-col', function(){
                                var key=$(this).data('sort');
                                if(!key) return;
                                if(key===sortKey){ sortDir=(sortDir==='asc')?'desc':'asc'; }
                                else { sortKey=key; sortDir='asc'; }
                                currentPage = 1; refresh();
                            });
                            $perPage.on('change', function(){ currentPage = 1; refresh(); });
                            $prev.on('click', function(){ if(currentPage>1){ currentPage--; paginate(); } });
                            $next.on('click', function(){ currentPage++; paginate(); });

                            // Update green highlight when selection changes
                            $(document).on('change', 'input[name=foyer_channel_editor_default_channel]', function(){
                                $rows.removeClass('selected-channel').attr('data-selected','0');
                                var $tr = $(this).closest('tr');
                                $tr.addClass('selected-channel').attr('data-selected','1');
                                currentPage = 1; refresh();
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
/* Removed legacy get_scheduled_channel_html() block

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
                        // Favoriten zuerst in der Auswahl
                        $fav = array(); $non = array();
                        foreach ( $channels as $ch0 ) { if ( get_post_meta( $ch0->ID, 'foyer_channel_is_favorite', true ) ) { $fav[] = $ch0; } else { $non[] = $ch0; } }
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

	*/

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

        // Security nonce for display admin AJAX
        $ajax_sec = array( 'nonce' => wp_create_nonce( 'foyer_display_ajax_nonce' ) );
        wp_localize_script( Foyer::get_plugin_name() . '-admin', 'foyer_display_ajax', $ajax_sec );
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

        $new_schedules = array();

        // Build candidate schedules from POST without saving yet
        if ( isset( $_POST['foyer_channel_scheduler_list_channel'] ) && is_array( $_POST['foyer_channel_scheduler_list_channel'] ) ) {
            $chs = $_POST['foyer_channel_scheduler_list_channel'];
            $sts = isset($_POST['foyer_channel_scheduler_list_start']) ? $_POST['foyer_channel_scheduler_list_start'] : array();
            $eds = isset($_POST['foyer_channel_scheduler_list_end']) ? $_POST['foyer_channel_scheduler_list_end'] : array();
            $cnt = max( count($chs), count($sts), count($eds) );
            $fmt = self::get_channel_scheduler_defaults()['datetime_format'];
            $tz  = wp_timezone();
            for ( $i=0; $i < $cnt; $i++ ) {
                $cid = intval( $chs[$i] ?? 0 );
                $start_str = sanitize_text_field( $sts[$i] ?? '' );
                $end_str   = sanitize_text_field( $eds[$i] ?? '' );
                if ( empty( $cid ) || empty( $start_str ) || empty( $end_str ) ) { continue; }
                // Parse using site timezone and configured format, then convert to UTC
                $start = null; $end = null;
                try {
                    $dt = date_create_from_format( $fmt, $start_str, $tz );
                    if ( $dt instanceof DateTime ) { $dt->setTimezone( new DateTimeZone('UTC') ); $start = $dt->getTimestamp(); }
                } catch ( Exception $e ) {}
                try {
                    $dt = date_create_from_format( $fmt, $end_str, $tz );
                    if ( $dt instanceof DateTime ) { $dt->setTimezone( new DateTimeZone('UTC') ); $end = $dt->getTimestamp(); }
                } catch ( Exception $e ) {}
                if ( is_null( $start ) || is_null( $end ) ) { continue; }
                if ( $end <= $start ) {
                    $def = self::get_channel_scheduler_defaults();
                    $end = $start + $def['duration'];
                }
                $new_schedules[] = array( 'channel' => $cid, 'start' => $start, 'end' => $end );
            }
        }

        // Validate for overlaps (only if two or more entries)
        if ( count( $new_schedules ) > 1 ) {
            // Sort by start asc
            usort( $new_schedules, function( $a, $b ) { return ($a['start'] <=> $b['start']); } );
            for ( $i = 1; $i < count( $new_schedules ); $i++ ) {
                $prev = $new_schedules[$i-1];
                $curr = $new_schedules[$i];
                // Overlap if previous end is greater than current start
                if ( intval( $prev['end'] ) > intval( $curr['start'] ) ) {
                    self::add_admin_notice( 'error', __( 'Schedule conflict: time windows overlap. Please adjust the planned channels so they do not overlap.', 'foyer' ) );
                    return; // Abort without saving any schedule changes
                }
            }
        }

        // Passed validation: replace existing schedule with new set
        delete_post_meta( $display_id, 'foyer_display_schedule' );
        foreach ( $new_schedules as $schedule ) {
            add_post_meta( $display_id, 'foyer_display_schedule', $schedule, false );
        }

        // Legacy single temporary channel fields removed; only list-based schedule is saved
    }

    /**
     * Adds an admin notice to be displayed on next page load.
     *
     * @param string $type    One of 'error', 'warning', 'success', 'info'
     * @param string $message The message to show
     */
    static function add_admin_notice( $type, $message ) {
        $uid = get_current_user_id();
        if ( empty( $uid ) ) { return; }
        $key = 'foyer_display_notices_' . $uid;
        $notices = get_transient( $key );
        if ( empty( $notices ) || ! is_array( $notices ) ) { $notices = array(); }
        $notices[] = array( 'type' => $type, 'message' => $message );
        set_transient( $key, $notices, MINUTE_IN_SECONDS );
    }

    /**
     * Renders any pending admin notices.
     */
    static function render_notices() {
        $uid = get_current_user_id();
        if ( empty( $uid ) ) { return; }
        $key = 'foyer_display_notices_' . $uid;
        $notices = get_transient( $key );
        if ( empty( $notices ) || ! is_array( $notices ) ) { return; }
        delete_transient( $key );
        foreach ( $notices as $n ) {
            $class = 'notice';
            switch ( $n['type'] ) {
                case 'error': $class .= ' notice-error'; break;
                case 'warning': $class .= ' notice-warning'; break;
                case 'success': $class .= ' notice-success'; break;
                default: $class .= ' notice-info'; break;
            }
            echo '<div class="' . esc_attr( $class ) . '"><p>' . esc_html( $n['message'] ) . '</p></div>';
        }
    }

    /**
     * Validates schedule entries for overlaps using server-side parsing of WP-formatted datetimes.
     *
     * Expects POST 'payload' JSON with { entries: [ {channel,start,end}, ... ] }
     */
    static function validate_schedule_over_ajax() {
        check_ajax_referer( 'foyer_display_ajax_nonce', 'nonce', true );

        $payload = isset( $_POST['payload'] ) ? wp_unslash( $_POST['payload'] ) : '';
        $data = json_decode( $payload, true );
        if ( empty( $data ) || empty( $data['entries'] ) || ! is_array( $data['entries'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid payload', 'foyer' ) ), 400 );
        }
        $fmt = self::get_channel_scheduler_defaults()['datetime_format'];
        $tz  = wp_timezone();
        $cands = array();
        foreach ( $data['entries'] as $e ) {
            $start = null; $end = null;
            $s_in = isset( $e['start'] ) ? $e['start'] : '';
            $e_in = isset( $e['end'] ) ? $e['end'] : '';
            if ( empty( $s_in ) || empty( $e_in ) ) { continue; }
            try { $dt = date_create_from_format( $fmt, $s_in, $tz ); if ( $dt instanceof DateTime ) { $dt->setTimezone( new DateTimeZone('UTC') ); $start = $dt->getTimestamp(); } } catch ( Exception $ex ) {}
            try { $dt = date_create_from_format( $fmt, $e_in, $tz ); if ( $dt instanceof DateTime ) { $dt->setTimezone( new DateTimeZone('UTC') ); $end = $dt->getTimestamp(); } } catch ( Exception $ex ) {}
            if ( is_null( $start ) || is_null( $end ) ) { continue; }
            if ( $end <= $start ) {
                $def = self::get_channel_scheduler_defaults();
                $end = $start + $def['duration'];
            }
            $cands[] = array( 'start' => $start, 'end' => $end );
        }
        if ( count( $cands ) > 1 ) {
            usort( $cands, function( $a, $b ) { return ( $a['start'] <=> $b['start'] ); } );
            for ( $i = 1; $i < count( $cands ); $i++ ) {
                if ( intval( $cands[$i-1]['end'] ) > intval( $cands[$i]['start'] ) ) {
                    wp_send_json_error( array( 'message' => __( 'Schedule conflict: time windows overlap.', 'foyer' ) ), 200 );
                }
            }
        }
        wp_send_json_success( array( 'ok' => true ) );
    }
}
