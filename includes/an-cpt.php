<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/

function advanced_notifications_register_cpt() {
    register_post_type( 'a_notifications', array(
        'labels' => array(
			'name'               => __( 'A Notifications', 'advanced-notifications' ),
			'singular_name'      => __( 'Notifications', 'advanced-notifications' ),
			'menu_name'          => __( 'A Notifications', 'advanced-notifications' ),
			'name_admin_bar'     => __( 'A Notifications', 'advanced-notifications' ),
			'add_new'            => __( 'Add New','advanced-notifications' ),
			'add_new_item'       => __( 'Add New', 'advanced-notifications' ),
			'new_item'           => __( 'New Item', 'advanced-notifications' ),
			'edit_item'          => __( 'Edit Item', 'advanced-notifications' ),
			'view_item'          => __( 'View Item', 'advanced-notifications' ),
			'all_items'          => __( 'Notifications', 'advanced-notifications' ),
			'search_items'       => __( 'Search', 'advanced-notifications' ),
			'parent_item_colon'  => __( 'Parent Item:', 'advanced-notifications' ),
			'not_found'          => __( 'No Items found.', 'advanced-notifications' ),
			'not_found_in_trash' => __( 'No Items found in Trash.', 'advanced-notifications' ),
		),
		// Frontend // Admin
		'label'                 => __( 'A notifications items', 'advanced-notifications' ),
		'description'           => __( 'A notifications items', 'advanced-notifications' ),
		'supports'              => apply_filters('an_cpt_supports', array('title' , 'editor')),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
        'publicly_queryable'    => true,
		'menu_position'         => 50,
		'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode('<svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="230.000000pt" height="230.000000pt" viewBox="0 0 230.000000 230.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,230.000000) scale(0.100000,-0.100000)" fill="#f0f6fc" stroke="none"><path d="M155 2233 c-44 -23 -65 -44 -86 -85 -18 -36 -19 -75 -19 -998 0 -866 2 -964 16 -993 23 -45 44 -66 86 -88 36 -18 75 -19 998 -19 866 0 964 2 993 16 45 23 66 44 88 86 18 36 19 75 19 998 0 923 -1 962 -19 998 -22 42 -43 63 -88 86 -29 14 -126 16 -995 16 -859 -1 -966 -3 -993 -17z m1957 -121 l33 -32 0 -930 0 -930 -33 -32 -32 -33 -930 0 -930 0 -32 33 -33 32 0 929 0 929 25 27 c14 15 34 31 45 36 11 4 433 7 937 6 l918 -2 32 -33z"/><path d="M250 1750 l0 -200 600 0 600 0 0 200 0 200 -600 0 -600 0 0 -200z"/><path d="M250 1150 l0 -200 700 0 700 0 0 200 0 200 -700 0 -700 0 0 -200z"/><path d="M250 550 l0 -200 800 0 800 0 0 200 0 200 -800 0 -800 0 0 -200z"/></g></svg>'),
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'rewrite'               => array('slug' => 'advanced-notification'),
		'capability_type'       => 'advanced_notifications',
		'map_meta_cap'          => true
    ) );
}
add_action( 'init', 'advanced_notifications_register_cpt', 0 );

function advanced_notifications_capabilities() {
	$role = get_role( 'administrator' );
	$role->add_cap( 'delete_advanced_notificationss', true );
	$role->add_cap( 'delete_others_advanced_notificationss', true );
	$role->add_cap( 'delete_private_advanced_notificationss', true );
	$role->add_cap( 'delete_published_advanced_notificationss', true );
	$role->add_cap( 'edit_advanced_notificationss', true );
	$role->add_cap( 'edit_others_advanced_notificationss', true );
	$role->add_cap( 'edit_private_advanced_notificationss', true );
	$role->add_cap( 'edit_published_advanced_notificationss', true );
	$role->add_cap( 'publish_advanced_notificationss', true );
	$role->add_cap( 'read_private_advanced_notificationss', true );
}
add_action( 'admin_init', 'advanced_notifications_capabilities' );

function advanced_notifications_deactivation() {
	$role = get_role( 'administrator' );
	$role->remove_cap( 'delete_advanced_notificationss');
	$role->remove_cap( 'delete_others_advanced_notificationss');
	$role->remove_cap( 'delete_private_advanced_notificationss');
	$role->remove_cap( 'delete_published_advanced_notificationss');
	$role->remove_cap( 'edit_advanced_notificationss');
	$role->remove_cap( 'edit_others_advanced_notificationss');
	$role->remove_cap( 'edit_private_advanced_notificationss');
	$role->remove_cap( 'edit_published_advanced_notificationss');
	$role->remove_cap( 'publish_advanced_notificationss');
	$role->remove_cap( 'read_private_advanced_notificationss');
}
register_deactivation_hook( ADVANCED_NOTIFICATIONS_ROOT_PATH, 'advanced_notifications_deactivation' );

function advanced_notifications_columns($columns) {
	unset(
		$columns['date'],
		$columns['comments']
	);
	$new_columns = array(
		'active' => __('Active', 'advanced-notifications'),
		'published_in' => __('Published in', 'advanced-notifications'),
		'devices' => __('Devices', 'advanced-notifications'),
		'display' => __('Display', 'advanced-notifications'),
		'rules' => __('Rules', 'advanced-notifications'),
	);
    return array_merge($columns, $new_columns);
}
add_filter( 'manage_a_notifications_posts_columns' , 'advanced_notifications_columns' );

function advanced_notifications_columns_data( $column, $post_id ) {
    global $an_functions, $an_designs, $an_locations;
    switch ( $column ) {
        case 'active' :
            $status = eis_get_option('a_notifications', 'status', $post_id);
            $switch = '<label class="an-switch">';
            $switch .= '    <input type="checkbox"' . checked($status,'checked',false) . ' data-an-action="status" data-notification_id="' . $post_id . '">';
            $switch .= '    <span class="an-switch-slider"></span>';
            $switch .= '</label>';
            $switch .= '<div class="an-save-option">✓ ' . __('Saved', 'advanced-notifications') . '</div>';
            echo $switch;
            break;
        case 'published_in' :
            $publish_type = eis_get_option('a_notifications', 'publish_type', $post_id);
            if ($publish_type == 'custom') {
                $publish_type_arr = array();
                $publish_post_types = eis_get_option('a_notifications', 'publish_post_types', $post_id);
                if (!empty($publish_post_types)) {
                    foreach ($publish_post_types as $key => $value) {
                        if (strpos($value, 'checked') !== false) {
                            $post_type_object = get_post_type_object($key);
                            $publish_type_arr[] = (isset($post_type_object->label) ? $post_type_object->label : $key);
                        }
                    }
                }
                $publish_taxonomies = eis_get_option('a_notifications', 'publish_taxonomies', $post_id);
                if (!empty($publish_taxonomies)) {
                    foreach ($publish_taxonomies as $key => $value) {
                        if (strpos($value, 'checked') !== false) {
                            $taxonomy_labels = get_taxonomy_labels(get_taxonomy($key));
                            $publish_type_arr[] = (isset($taxonomy_labels->singular_name) ? $taxonomy_labels->singular_name : $key);
                        }
                    }
                }
                $publish_type = implode(', ', $publish_type_arr);
            } else {
                $publish_type_name = $an_functions->publish_type();
                $publish_type = (isset($publish_type_name[$publish_type]) ? $publish_type_name[$publish_type] : $publish_type);
            }
            echo $publish_type;
            break;
        case 'devices' :
            $devices_list = str_replace(' ', '_', eis_get_option('a_notifications', 'devices_list', $post_id));
            $devices_list_arr = $an_functions->devices_list();
            echo (isset($devices_list_arr[$devices_list]) ? $devices_list_arr[$devices_list] : $devices_list);
            break;
        case 'display' :
            // $devices_list_arr = $an_functions->devices_list();
            // $devices_list = str_replace(' ', '_', eis_get_option('a_notifications', 'devices_list', $post_id));
            // echo __('Devices', 'advanced-notifications') . ': ' . (isset($devices_list_arr[$devices_list]) ? $devices_list_arr[$devices_list] : $devices_list) . '<br>';
            $locations_list = $an_locations->locations_list();
            $location = str_replace(' ', '_', eis_get_option('a_notifications', 'location', $post_id));
            echo __('Location', 'advanced-notifications') . ': ' . (isset($locations_list[$location]['label']) ? $locations_list[$location]['label'] : $location) . '<br>';
            $designs_list = $an_designs->designs_list();
            $design = str_replace(' ', '_', eis_get_option('a_notifications', 'design', $post_id));
            echo __('Designs', 'advanced-notifications') . ': ' . (isset($designs_list[$design]['label']) ? $designs_list[$design]['label'] : $design) . '<br>';
            $delay = str_replace(' ', '_', eis_get_option('a_notifications', 'delay', $post_id));
            if ($delay == 0) {
                $delay = __('No delay', 'advanced-notifications');
            } else {
                $delay = $delay . ' ' . __('seconds', 'advanced-notifications');
            }
            echo __('Delay', 'advanced-notifications') . ': ' . $delay . '<br>';
            $show_time = str_replace(' ', '_', eis_get_option('a_notifications', 'show_time', $post_id));
            if ($show_time == 0) {
                $show_time = __('Always', 'advanced-notifications');
            } else {
                $show_time = $show_time . ' ' . __('seconds', 'advanced-notifications');
            }
            echo __('Show Time', 'advanced-notifications') . ': ' . $show_time;
            break;
        case 'rules' :
            $close_button_limit = str_replace(' ', '_', eis_get_option('a_notifications', 'close_button_limit', $post_id));
            if ($close_button_limit == 'cancel_for') {
                $close_button_limit_for = eis_get_option('a_notifications', 'close_button_limit_for', $post_id);
                $close_button_limit = $close_button_limit_for['cancel_val'] . ' ' . $close_button_limit_for['cancel_unit'];
            }
            echo __('Close limit', 'advanced-notifications') . ': ' . $close_button_limit . '<br>';
            $limitations = str_replace(' ', '_', eis_get_option('a_notifications', 'limitations', $post_id));
            if ($limitations == 'custom_limitations') {
                $custom_limitations_times = eis_get_option('a_notifications', 'custom_limitations_times', $post_id);
                $custom_limitations_for = eis_get_option('a_notifications', 'custom_limitations_for', $post_id);
                $limitations = __('Every ', 'advanced-notifications') . $custom_limitations_times . ' ' . __('times', 'advanced-notifications');
                $limitations .= ', ' .__('for ', 'advanced-notifications') . $custom_limitations_for['limit_val'] . ' ' . $custom_limitations_for['limit_unit'];
            }
            echo __('Shows limit', 'advanced-notifications') . ': ' . $limitations;
            break;
	}
}
add_action( 'manage_a_notifications_posts_custom_column' , 'advanced_notifications_columns_data', 10, 2 );

function advanced_notifications_duplicate_link( $actions, $post ) {
	if (in_array($post->post_type, apply_filters('an_add_duplicate_link', array('a_notifications') )) && current_user_can('edit_advanced_notificationss')) {
		$actions['duplicate'] = '<a href="admin.php?action=advanced_notifications_duplicate_as_pending&amp;post=' . $post->ID . '" title="' . __('Duplicate this item', 'advanced-notifications') . '" rel="permalink">' . __('Duplicate', 'advanced-notifications') . '</a>';
		$actions['id'] = 'ID: ' . $post->ID . '</a>';
	}
	return $actions;
}
add_filter( 'post_row_actions', 'advanced_notifications_duplicate_link', 20, 2 );


function advanced_notifications_fix_post_name($post_id, $post, $update) {
    if ($post->post_type == 'a_notifications' && !wp_is_post_revision($post_id) && !wp_is_post_autosave( $post ) && $post->post_status !== 'auto-draft') {
        $post->post_name = 'an-' . $post_id;
        remove_action( 'save_post', 'advanced_notifications_fix_post_name', 10);
        wp_update_post($post);
        add_action( 'save_post', 'advanced_notifications_fix_post_name', 10, 3 );
    }
}
add_action( 'save_post', 'advanced_notifications_fix_post_name', 10, 3 );

function advanced_notifications_duplicate_as_pending() {
	if (!isset($_REQUEST['post']) || !isset($_REQUEST['action']) || $_REQUEST['action'] != 'advanced_notifications_duplicate_as_pending') {
		wp_die(__('No post to duplicate has been supplied!', 'advanced-notifications'));
	}
	$post_id = isset($_REQUEST['post']) ? absint($_REQUEST['post']) : null;
	$an_post = (array)get_post($post_id);
    if ($an_post) {
        $current_user = wp_get_current_user();
        unset($an_post['ID']);
        unset($an_post['guid']);
        unset($an_post['post_modified']);
        unset($an_post['post_modified_gmt']);
        unset($an_post['post_name']);
        $an_post['post_title'] = $an_post['post_title'] . ' - ' . __('copy', 'advanced-notifications');
        $an_post['post_author'] = $current_user->ID;
        $an_post['post_status'] = 'pending';
        // create new post
        $new_post_id = wp_insert_post($an_post);
        // set taxonomies to new post
        $taxonomies = get_object_taxonomies($an_post['post_type']); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }
        // set meta to new post
        $post_meta = get_post_meta($post_id);
        unset($post_meta['_edit_last']);
        unset($post_meta['_edit_lock']);
        unset($post_meta['_wp_old_date']);
        foreach ($post_meta as $key => $value) {
            $post_meta_value = get_post_meta($post_id, $key, true);
            update_post_meta( $new_post_id, $key, $post_meta_value );
        }
        wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
        exit;
    } else {
        wp_die(__('Post creation failed, could not find original post', 'advanced-notifications') . ': ' . $post_id);
    }
}
add_action( 'admin_action_advanced_notifications_duplicate_as_pending', 'advanced_notifications_duplicate_as_pending' );

function advanced_notifications_post_states( $states, $post ) {
    if ($post->post_type == 'a_notifications') {
        global $an_functions;
        $notification_types = $an_functions->notification_types();
        $type = eis_get_option('a_notifications', 'type', $post->ID);
        if (isset($notification_types[$type]) && $type != 'elementor') {
            $states[] = $notification_types[$type];
        }
    }
    return $states;
}
add_filter('display_post_states', 'advanced_notifications_post_states', 10, 2);

function advanced_notifications_updated_messages( $messages ) {
	$post             = get_post();
	// $post_type        = get_post_type( $post );
	// $post_type_object = get_post_type_object( $post_type );
	$messages['a_notifications'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Notification updated.', 'advanced-notifications' ) . ' <a href="' . get_permalink($post) . '" target="_blank">' . __( 'View Notification', 'advanced-notifications' ) . '</a>',
		2  => __( 'Custom field updated.', 'advanced-notifications' ),
		3  => __( 'Custom field deleted.', 'advanced-notifications' ),
		4  => __( 'Notification updated.', 'advanced-notifications' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Notification restored to revision from %s', 'advanced-notifications' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Notification published.', 'advanced-notifications' ) . ' <a href="' . get_permalink($post) . '" target="_blank">' . __( 'View Notification', 'advanced-notifications' ) . '</a>',
		7  => __( 'Notification saved.', 'advanced-notifications' ),
		8  => __( 'Notification submitted.', 'advanced-notifications' ),
		// 9  => sprintf(
		// 	__( 'My Post Type scheduled for: <strong>%1$s</strong>.' ),
		// 	// translators: Publish box date format, see http://php.net/date
		// 	date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
		// ),
        10  => __( 'Notification draft updated.', 'advanced-notifications' ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'advanced_notifications_updated_messages' );
