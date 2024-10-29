<?php
/*
*
*	Advanced Notifications V 1
*	------------------------------------------------
* 	Powered by  - https://wiliba.com
*
*/

get_header();
global $an_api, $an_functions;
$notification_id = get_the_id();
$notification = $an_api->get_notification($notification_id);
$devices_list = $an_functions->devices_list();
$delay = (isset($notification['delay']) && $notification['show_when'] == 'page_loaded_trigers') ? $notification['delay'] : 0;
$devices = isset($notification['devices_list']) ? $notification['devices_list'] : 'all-devices';
$devices = $devices_list[$devices];
$status_val = $delay ? __('Active delay', 'advanced-notifications') . ': ' . __('Showing in', 'advanced-notifications') . ' ' . $delay : __('The notification is displayed', 'advanced-notifications');
?>
<?php if ($an_functions->is_elementor_preview_mode()): ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
<?php else: ?>
	<div class="container">
		<div class="an-preview-container">
			<div class="an-preview-title"><?php echo __('Notification preview', 'advanced-notifications') . ': ' . get_the_title(); ?></div>
			<div class="an-preview-status">
				<!-- <span class="an-preview-status-label"><?php echo __('Notification status', 'advanced-notifications') . ': ' ?></span> -->
				<span class="an-preview-status-val" data-delay="<?php echo $delay ?>" data-delay-label="<?php echo __('Active delay', 'advanced-notifications') . ': ' . __('Showing in', 'advanced-notifications') ?>" data-show-label="<?php _e('The notification is displayed', 'advanced-notifications') ?>"><?php echo $status_val ?></span>
			</div>
			<div class="an-preview-devices"><?php echo __('Appears on', 'advanced-notifications') . ': ' . $devices ?></div>
		</div>
	</div>
<?php endif; ?>
<?php
get_footer();
