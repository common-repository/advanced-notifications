<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/
global $an_locations;
class AdvancedNotificationsLocations
{
    function __construct() {
        add_action( 'an_admin_menu', array($this, 'an_locations_admin_menu' ), 25);
        add_action( 'init', array($this, 'an_register_locations_cpt' ), 0 );
        // filters
        add_filter( 'parent_file', array( $this, 'an_cpt_locations_parent_file' ) );
        add_filter( 'submenu_file', array( $this, 'an_cpt_locations_submenu_file' ) );
        add_filter( 'an_cpt_list', array( $this, 'an_add_locations_post_type' ) );
        add_filter( 'an_add_duplicate_link', array($this, 'an_add_locations_post_type' ) );
    }

    /* ADMIN MENU
    ================================================== */
    function an_locations_admin_menu() {
        add_submenu_page( 'edit.php?post_type=a_notifications', __( 'Locations', 'advanced-notifications' ), __( 'Locations', 'advanced-notifications' ), 'edit_advanced_notificationss', '/edit.php?post_type=an_locations', null);
    }
    function an_cpt_locations_parent_file( $parent_file ){
        global $current_screen;
        if (in_array( $current_screen->base, array( 'post', 'edit' ))) {
            if ('an_locations' == $current_screen->post_type) {
                $parent_file = 'edit.php?post_type=a_notifications';
            }
        }
        return $parent_file;
    }
    function an_cpt_locations_submenu_file( $submenu_file ){
        global $current_screen;
        if (in_array( $current_screen->base, array( 'post', 'edit' ))) {
            if ('an_locations' == $current_screen->post_type) {
                $submenu_file = 'edit.php?post_type=an_locations';
            }
        }
        return $submenu_file;
    }

    /* CPT
    ================================================== */
    function an_register_locations_cpt() {
        register_post_type( 'an_locations', array(
            'labels' => array(
    			'name'               => __( 'AN Locations', 'advanced-notifications' ),
    			'singular_name'      => _x( 'AN Locations items', 'post type singular name', 'advanced-notifications' ),
    			'menu_name'          => _x( 'AN Locations', 'admin menu', 'advanced-notifications' ),
    			'name_admin_bar'     => _x( 'AN Locations', 'add new on admin bar', 'advanced-notifications' ),
    			'add_new'            => _x( 'Add New', 'Post Type', 'advanced-notifications' ),
    			'add_new_item'       => __( 'Add New', 'advanced-notifications' ),
    			'new_item'           => __( 'New Item', 'advanced-notifications' ),
    			'edit_item'          => __( 'Edit Item', 'advanced-notifications' ),
    			'view_item'          => __( 'View Item', 'advanced-notifications' ),
    			'all_items'          => __( 'AN Locations items', 'advanced-notifications' ),
    			'search_items'       => __( 'Search', 'advanced-notifications' ),
    			'parent_item_colon'  => __( 'Parent Item:', 'advanced-notifications' ),
    			'not_found'          => __( 'No Items found.', 'advanced-notifications' ),
    			'not_found_in_trash' => __( 'No Items found in Trash.', 'advanced-notifications' ),
    		),
    		// Frontend // Admin
    		'label'                 => __( 'AN Locations items', 'advanced-notifications' ),
    		'description'           => __( 'AN Locations items', 'advanced-notifications' ),
    		'supports'              => array( 'title' ),
    		'hierarchical'          => false,
    		'public'                => false,
    		'show_ui'               => true,
    		'show_in_menu'          => false,
            'publicly_queryable'    => false,
    		'menu_position'         => 100,
    		'menu_icon'             => 'dashicons-megaphone',
    		'show_in_admin_bar'     => false,
    		'show_in_nav_menus'     => false,
    		'can_export'            => true,
    		'has_archive'           => false,
    		'exclude_from_search'   => true,
    		'rewrite'               => array('slug' => 'advanced-notification-locations'),
    		'capability_type'       => 'advanced_notifications',
    		'map_meta_cap'          => true
        ) );
    }
    /* FUNCTIONS
    ================================================== */
    function get_locations() {
        $args = array(
            'post_type'   => 'an_locations',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        return get_posts($args);
    }
    function register_locations() {
        global $an_api;
        $locations = array(
            'bottom_site_direction' => array(
                'id' => 'bottom_site_direction',
                'label' => __('Bottom & Site direction', 'advanced-notifications'),
            ),
            'bottom_opposite_site_direction' => array(
                'id' => 'bottom_opposite_site_direction',
                'label' => __('Bottom & Opposite site direction', 'advanced-notifications'),
            ),
            'bottom_left' => array(
                'id' => 'bottom_left',
                'label' => __('Bottom & Left', 'advanced-notifications'),
                'desktop' => array(
                    'horizontal_position' => 'left',
                ),
            ),
            'bottom_right' => array(
                'id' => 'bottom_right',
                'label' => __('Bottom & Right', 'advanced-notifications'),
                'desktop' => array(
                    'horizontal_position' => 'right',
                ),
            ),
            'top_site_direction' => array(
                'id' => 'top_site_direction',
                'label' => __('Top & Site direction', 'advanced-notifications'),
            ),
            'top_opposite_site_direction' => array(
                'id' => 'top_opposite_site_direction',
                'label' => __('Top & Opposite site direction', 'advanced-notifications'),
            ),
            'top_left' => array(
                'id' => 'top_left',
                'label' => __('Top & Left', 'advanced-notifications'),
                'desktop' => array(
                    'horizontal_position' => 'left',
                    'vertical_position' => 'top',
                ),
            ),
            'top_right' => array(
                'id' => 'top_right',
                'label' => __('Top & Right', 'advanced-notifications'),
                'desktop' => array(
                    'horizontal_position' => 'right',
                    'vertical_position' => 'top',
                ),
            ),
            'bottom_center_eis_disabled' => array(
                'id' => 'bottom_center_eis_disabled',
                'label' => __('Bottom & Center', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
            ),
            'top_center_eis_disabled' => array(
                'id' => 'top_center_eis_disabled',
                'label' => __('Top & Center', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
            ),
            'center_left_eis_disabled' => array(
                'id' => 'center_left_eis_disabled',
                'label' => __('Center & Left', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
            ),
            'center_right_eis_disabled' => array(
                'id' => 'center_right_eis_disabled',
                'label' => __('Center & Right', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
            ),
            'center_center_eis_disabled' => array(
                'id' => 'center_center_eis_disabled',
                'label' => __('Center & Center', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
            ),
        );
        $locations = apply_filters('an_pre_register_locations', $locations);
        foreach ($locations as $location) {
            $an_api->add_location($location);
        }
        $locations_posts = $this->get_locations();
        foreach ($locations_posts as $location_post) {
            $location_settings = (array) eis_get_option('an_locations', null, $location_post->ID);
            foreach ($location_settings as $option_key => $option_value) {
                $location_settings[$option_key] = str_replace($option_key . '_', '', $option_value);
            }
            $args = array(
                'id'   => $location_post->ID,
                'label'   => $location_post->post_title,
            );
            $an_api->add_location(array_merge($args, $location_settings));
        }
    }
    function locations_list($cache=true) {
        global $an_core, $an_register_locations;
        $locations_list = $an_core->an_get_cache('locations_list');
        if (!$locations_list || !$cache) {
            // must run first to get all register_locations to global var $an_register_locationss
            $register_locations = $this->register_locations();
            $locations_list = array('register_locations' => $an_register_locations);
            $an_core->an_add_cache('locations_list', $locations_list);
        }
        return apply_filters('an_locations_list', $locations_list['register_locations']);
    }
    function an_add_locations_post_type($list) {
        $list[] = 'an_locations';
        return $list;
    }
}
$an_locations = new AdvancedNotificationsLocations();
