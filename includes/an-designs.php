<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/
global $an_designs;
class AdvancedNotificationsDesigns
{
    function __construct() {
        add_action( 'an_admin_menu', array($this, 'an_designs_admin_menu' ), 15);
        add_action( 'init', array($this, 'an_register_designs_cpt' ), 0 );
        // filters
        add_filter( 'parent_file', array( $this, 'an_cpt_designs_parent_file' ) );
        add_filter( 'submenu_file', array( $this, 'an_cpt_designs_submenu_file' ) );
        add_filter( 'an_cpt_list', array( $this, 'an_add_designs_post_type' ) );
        add_filter( 'an_add_duplicate_link', array($this, 'an_add_designs_post_type' ) );
    }

    /* ADMIN MENU
    ================================================== */
    function an_designs_admin_menu() {
        add_submenu_page( 'edit.php?post_type=a_notifications', __( 'Designs', 'advanced-notifications' ), __( 'Designs', 'advanced-notifications' ), 'edit_advanced_notificationss', '/edit.php?post_type=an_designs', null);
    }
    function an_cpt_designs_parent_file( $parent_file ){
        global $current_screen;
        if (in_array( $current_screen->base, array( 'post', 'edit' ))) {
            if ('an_designs' == $current_screen->post_type) {
                $parent_file = 'edit.php?post_type=a_notifications';
            }
        }
        return $parent_file;
    }
    function an_cpt_designs_submenu_file( $submenu_file ){
        global $current_screen;
        if (in_array( $current_screen->base, array( 'post', 'edit' ))) {
            if ('an_designs' == $current_screen->post_type) {
                $submenu_file = 'edit.php?post_type=an_designs';
            }
        }
        return $submenu_file;
    }

    /* CPT
    ================================================== */
    function an_register_designs_cpt() {
        register_post_type( 'an_designs', array(
            'labels' => array(
        		'name'               => __( 'AN Designs', 'advanced-notifications' ),
        		'singular_name'      => _x( 'AN Designs items', 'post type singular name', 'advanced-notifications' ),
        		'menu_name'          => _x( 'AN Designs', 'admin menu', 'advanced-notifications' ),
        		'name_admin_bar'     => _x( 'AN Designs', 'add new on admin bar', 'advanced-notifications' ),
        		'add_new'            => _x( 'Add New', 'Post Type', 'advanced-notifications' ),
        		'add_new_item'       => __( 'Add New', 'advanced-notifications' ),
        		'new_item'           => __( 'New Item', 'advanced-notifications' ),
        		'edit_item'          => __( 'Edit Item', 'advanced-notifications' ),
        		'view_item'          => __( 'View Item', 'advanced-notifications' ),
        		'all_items'          => __( 'AN Designs items', 'advanced-notifications' ),
        		'search_items'       => __( 'Search', 'advanced-notifications' ),
        		'parent_item_colon'  => __( 'Parent Item:', 'advanced-notifications' ),
        		'not_found'          => __( 'No Items found.', 'advanced-notifications' ),
        		'not_found_in_trash' => __( 'No Items found in Trash.', 'advanced-notifications' ),
        	),
        	// Frontend // Admin
        	'label'                 => __( 'AN Designs items', 'advanced-notifications' ),
        	'description'           => __( 'AN Designs items', 'advanced-notifications' ),
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
        	'rewrite'               => array('slug' => 'advanced-notification-designs'),
        	'capability_type'       => 'advanced_notifications',
        	'map_meta_cap'          => true
        ) );
    }

    /* FUNCTIONS
    ================================================== */
    function get_designs() {
        $args = array(
            'post_type'   => 'an_designs',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        return get_posts($args);
    }
    function register_designs() {
        global $an_api;
        $designs = array(
            'default' => array(
                'id' => 'default',
                'label' => __('Default', 'advanced-notifications'),
            ),
            'success' => array(
                'id' => 'success',
                'label' => __('Success', 'advanced-notifications'),
            ),
            'info' => array(
                'id' => 'info',
                'label' => __('Info', 'advanced-notifications'),
            ),
            'warning' => array(
                'id' => 'warning',
                'label' => __('Warning', 'advanced-notifications'),
            ),
            'danger' => array(
                'id' => 'danger',
                'label' => __('Danger', 'advanced-notifications'),
            ),
            'an_netflix_notice_eis_disabled' => array(
                'id' => 'an_netflix_notice_eis_disabled',
                'label' => __('Netflix Notice', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
            ),
            'an_facebook_notice_eis_disabled' => array(
                'id' => 'an_facebook_notice_eis_disabled',
                'label' => __('Facebook Notice', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
            ),
        );
        $designs = apply_filters('an_pre_register_designs', $designs);
        foreach ($designs as $design) {
            $an_api->add_design($design);
        }
        $designs_posts = $this->get_designs();
        foreach ($designs_posts as $design_post) {
            $design_settings = (array) eis_get_option('an_designs', null, $design_post->ID);
            foreach ($design_settings as $option_key => $option_value) {
                $design_settings[$option_key] = str_replace($option_key . '_', '', $option_value);
            }
            $args = array(
                'id'   => $design_post->ID,
                'label'   => $design_post->post_title,
            );
            $an_api->add_design(array_merge($args, $design_settings));
        }
    }
    function designs_list($cache=true) {
        global $an_core, $an_register_designs;
        $designs_list = $an_core->an_get_cache('designs_list');
        if (!$designs_list || !$cache) {
            // must run first to get all register_designs to global var $an_register_designss
            $register_designs = $this->register_designs();
            $designs_list = array('register_designs' => $an_register_designs);
            $an_core->an_add_cache('designs_list', $designs_list);
        }
        return apply_filters('an_designs_list', $designs_list['register_designs']);
    }
    function an_add_designs_post_type($list) {
        $list[] = 'an_designs';
        return $list;
    }
}
$an_designs = new AdvancedNotificationsDesigns();
