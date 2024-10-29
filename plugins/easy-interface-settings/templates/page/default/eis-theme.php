<?php
/*
*
*	Easy Interface Settings V 1
*	------------------------------------------------
* 	Copyright Linker  - https://linker.co.il
*
*/

$interface_id = sanitize_text_field($_GET['page']);
// $page_interface_option = get_save_eis_interfaces_options($interface_id);
// if ($page_interface_option == null) {
// 	update_option($interface_id . '_eis_options' , null);
// }
$interface_info = get_eis_interfaces($interface_id);
$interface_option = eis_get_option($interface_id);
ob_start();
settings_errors();
$settings_errors = ob_get_clean();
?>
<div class="wrap eis-page-container" data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>">
	<h2><?php echo esc_html($interface_info['settings_title']); ?></h2>
	<?php if ($interface_info['export_import']): ?>
		<div class="page-title-action eis-page-action eis-export-action">
			<?php _e('Export', 'easy-interface-settings') ?>
		</div>
		<div class="page-title-action eis-page-action eis-import-action">
			<?php _e('Import', 'easy-interface-settings') ?>
		</div>
	<?php endif; ?>
	<div class="eis-data-container eis-export-container">
		<textarea name="eis-export" rows="4" cols="80" readonly><?php echo json_encode($interface_option) ?></textarea>
		<div class="page-title-action eis-page-action eis-export-copy" data-label="<?php _e('Copy Settings', 'easy-interface-settings') ?>" data-copied="<?php _e('Copied', 'easy-interface-settings') ?>">
			<?php _e('Copy Settings', 'easy-interface-settings') ?>
		</div>
		<div class="page-title-action eis-page-action eis-data-close">
			<?php _e('Close', 'easy-interface-settings') ?>
		</div>
	</div>
	<div class="eis-data-container eis-import-container">
		<textarea name="eis-import" rows="4" cols="80"></textarea>
		<div class="page-title-action eis-page-action eis-import-settings" data-interface-id="<?php echo $interface_id ?>">
			<?php _e('Import Settings', 'easy-interface-settings') ?>
		</div>
		<div class="page-title-action eis-page-action eis-data-close">
			<?php _e('Close', 'easy-interface-settings') ?>
		</div>
	</div>
	<?php echo $settings_errors // no need to escaping ?>
	<form name="eis-form" method="post" action="options.php">
		<div class="eis-interface-settings<?php echo (!$interface_info['show_tabs'] ? ' no-tabs' : null) ?>" data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>">
			<?php
			eis_interface_tabs_options_print($interface_id);
			?>
		</div>
		<div class="eis-sidebar">
			<div class="eis-sidebar-box">
				<div class="eis-sidebar-box-title">
					<h3><?php _e( 'Save All Settings', 'easy-interface-settings' ) ?></h3>
				</div>
				<?php submit_button(); ?>
			</div>
		</div>
	</form>
</div>
<?php
