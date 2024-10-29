<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/
global $an_triggers;
class AdvancedNotificationsTriggers
{
    function __construct() {
        add_action( 'an_admin_menu', array($this, 'an_triggers_admin_menu' ), 25);
        add_action( 'init', array($this, 'an_register_triggers_cpt' ), 0 );
        // filters
        add_filter( 'parent_file', array( $this, 'an_cpt_triggers_parent_file' ) );
        add_filter( 'submenu_file', array( $this, 'an_cpt_triggers_submenu_file' ) );
        add_filter( 'an_cpt_list', array( $this, 'an_add_triggers_post_type' ) );
        add_filter( 'an_add_duplicate_link', array($this, 'an_add_triggers_post_type' ) );
        add_filter( 'an_localize_script', array($this, 'an_triggers_localize_script' ) );
    }

    /* ADMIN MENU
    ================================================== */
    function an_triggers_admin_menu() {
        add_submenu_page( 'edit.php?post_type=a_notifications', __( 'Triggers', 'advanced-notifications' ), __( 'Triggers', 'advanced-notifications' ), 'edit_advanced_notificationss', '/edit.php?post_type=an_triggers', null);
    }
    function an_cpt_triggers_parent_file( $parent_file ){
        global $current_screen;
        if (in_array( $current_screen->base, array( 'post', 'edit' ))) {
            if ('an_triggers' == $current_screen->post_type) {
                $parent_file = 'edit.php?post_type=a_notifications';
            }
        }
        return $parent_file;
    }
    function an_cpt_triggers_submenu_file( $submenu_file ){
        global $current_screen;
        if (in_array( $current_screen->base, array( 'post', 'edit' ))) {
            if ('an_triggers' == $current_screen->post_type) {
                $submenu_file = 'edit.php?post_type=an_triggers';
            }
        }
        return $submenu_file;
    }

    /* CPT
    ================================================== */
    function an_register_triggers_cpt() {
        register_post_type( 'an_triggers', array(
            'labels' => array(
        		'name'               => __( 'AN Triggers', 'advanced-notifications' ),
        		'singular_name'      => _x( 'AN Triggers items', 'post type singular name', 'advanced-notifications' ),
        		'menu_name'          => _x( 'AN Triggers', 'admin menu', 'advanced-notifications' ),
        		'name_admin_bar'     => _x( 'AN Triggers', 'add new on admin bar', 'advanced-notifications' ),
        		'add_new'            => _x( 'Add New', 'Post Type', 'advanced-notifications' ),
        		'add_new_item'       => __( 'Add New', 'advanced-notifications' ),
        		'new_item'           => __( 'New Item', 'advanced-notifications' ),
        		'edit_item'          => __( 'Edit Item', 'advanced-notifications' ),
        		'view_item'          => __( 'View Item', 'advanced-notifications' ),
        		'all_items'          => __( 'AN Triggers items', 'advanced-notifications' ),
        		'search_items'       => __( 'Search', 'advanced-notifications' ),
        		'parent_item_colon'  => __( 'Parent Item:', 'advanced-notifications' ),
        		'not_found'          => __( 'No Items found.', 'advanced-notifications' ),
        		'not_found_in_trash' => __( 'No Items found in Trash.', 'advanced-notifications' ),
        	),
        	// Frontend // Admin
        	'label'                 => __( 'AN Triggers items', 'advanced-notifications' ),
        	'description'           => __( 'AN Triggers items', 'advanced-notifications' ),
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
        	'rewrite'               => array('slug' => 'advanced-notification-triggers'),
        	'capability_type'       => 'advanced_notifications',
        	'map_meta_cap'          => true
        ) );
    }

    /* FUNCTIONS
    ================================================== */
    function get_triggers() {
        $args = array(
            'post_type'   => 'an_triggers',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        return get_posts($args);
    }
    function register_triggers() {
        global $an_api;
        $triggers = array(
            'mouse_leave_from_top' => array(
                'id' => 'mouse_leave_from_top',
                'label' => __('Mouse leave from top', 'advanced-notifications'),
                'description' => __('Suitable for the situation where the visitor leaves the site to the top of the browser with the mouse, for example to close the tab', 'advanced-notifications'),
            ),
        );
        foreach ($triggers as $trigger) {
            $an_api->add_trigger($trigger);
        }
        $triggers_posts = $this->get_triggers();
        foreach ($triggers_posts as $trigger_post) {
            $trigger_settings = (array) eis_get_option('an_triggers', null, $trigger_post->ID);
            foreach ($trigger_settings as $option_key => $option_value) {
                $trigger_settings[$option_key] = str_replace($option_key . '_', '', $option_value);
            }
            $args = array(
                'id'   => $trigger_post->ID,
                'label'   => $trigger_post->post_title,
                'description'   => eis_get_option('an_triggers', 'trigger_description', $trigger_post->ID),
            );
            $an_api->add_trigger(array_merge($args, $trigger_settings));
        }
    }
    function triggers_list($cache=true) {
        global $an_core, $an_register_triggers;
        $triggers_list = $an_core->an_get_cache('triggers_list');
        if (!$triggers_list || !$cache) {
            // must run first to get all register_triggers to global var $an_register_triggerss
            $register_triggers = $this->register_triggers();
            $triggers_list = array('register_triggers' => $an_register_triggers);
            $an_core->an_add_cache('triggers_list', $triggers_list);
        }
        return apply_filters('an_triggers_list', $triggers_list['register_triggers']);
    }
    function an_add_triggers_post_type($list) {
        $list[] = 'an_triggers';
        return $list;
    }
    function an_triggers_localize_script($an_localize_script) {
        // $an_localize_script['triggers']
        return $an_localize_script;
    }
}
$an_triggers = new AdvancedNotificationsTriggers();
