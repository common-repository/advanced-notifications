<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/
global $an_animations;
class AdvancedNotificationsAnimations
{
    function __construct() {
        // add_action();
    }

    /* FUNCTIONS
    ================================================== */
    function an_animations_in() {
        $animations_in = array(
            'an-fedein' => __('Fede In', 'advanced-notifications'),
            'an-fedein-left' => __('Fede In Left', 'advanced-notifications'),
            'an-fedein-right' => __('Fede In Right', 'advanced-notifications'),
            'an-fedein-up' => __('Fede In Up', 'advanced-notifications'),
            'an-fedein-down' => __('Fede In Down', 'advanced-notifications'),
            'an-bounce-in' => __('Bounce In', 'advanced-notifications'),
            'an-bounce-in-left' => __('Bounce In Left', 'advanced-notifications'),
            'an-bounce-in-right' => __('Bounce In Right', 'advanced-notifications'),
            'an-bounce-in-up' => __('Bounce In Up', 'advanced-notifications'),
            'an-bounce-in-down' => __('Bounce In Down', 'advanced-notifications'),
            'an-slide-in-left' => __('Slide In Left', 'advanced-notifications'),
            'an-slide-in-right' => __('Slide In Right', 'advanced-notifications'),
            'an-slide-in-up' => __('Slide In Up', 'advanced-notifications'),
            'an-slide-in-down' => __('Slide In Down', 'advanced-notifications'),
            'an-vertical-flip-in' => __('Vertical Flip In', 'advanced-notifications'),
            'an-unfold-down-in' => __('Unfold Down In', 'advanced-notifications'),
            'an-netflix-notice-in_eis_disabled' => __('Netflix Notice In', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
        );
        return apply_filters( 'an_animations_in', $animations_in );
    }
    function an_animations_out() {
        $animations_out = array(
    		'an-fedeout' => __('Fede Out', 'advanced-notifications'),
    		'an-fedeout-left' => __('Fede Out Left', 'advanced-notifications'),
    		'an-fedeout-right' => __('Fede Out Right', 'advanced-notifications'),
    		'an-fedeout-up' => __('Fede Out Up', 'advanced-notifications'),
    		'an-fedeout-down' => __('Fede Out Down', 'advanced-notifications'),
    		'an-slide-out-left' => __('Slide Out Left', 'advanced-notifications'),
    		'an-slide-out-right' => __('Slide Out Right', 'advanced-notifications'),
    		'an-slide-out-up' => __('Slide Out Up', 'advanced-notifications'),
    		'an-slide-out-down' => __('Slide Out Down', 'advanced-notifications'),
            'an-netflix-notice-out_eis_disabled' => __('Netflix Notice Out', 'advanced-notifications') . ' - ' . __('PRO', 'advanced-notifications'),
    	);
        return apply_filters( 'an_animations_out', $animations_out );
    }
}
$an_animations = new AdvancedNotificationsAnimations();
