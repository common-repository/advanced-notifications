jQuery(document).ready( function($) {

	$(document).on('click', '.tabs a', function(e) {
		e.preventDefault();
		// $('.tabs a').removeClass('active');
		$(this).parent().parent().children().each(function(index,element) {
			$(element).children('a').removeClass('active');
		});
		var currntTab = $(this).attr('class');
		$(this).addClass('active');
		$(this).parent().parent().parent().children('.tab-content').hide();
		$('#' + currntTab).show();

		if ($('[data-save-tab]').data('save-tab') == true) {
			var tab_id = $(this).data("tab-id");
			var interface_id = $(this).parent().parent().data("interface-id");
			var ajaxURL = $('.eis-interface-settings').data("ajax-url");
			var saveTabName = $.ajax({
				type: "POST",
				url: ajaxURL,
				data : { action: 'eis_save_current_tab_ajax', interface_id: interface_id, tab_id: tab_id },
				cache: false,
				success: function(html) {

				},
				error: function(html){

				}
			});
		}
	});

	var mediaUploader;
	var imagePreview;
	var imageURL;
	var fileURL;
	$(document).on("click", ".upload-button", function(e){
		e.preventDefault();
		imagePreview = $(this).parent().children('.image-preview');
		imageURL = $(this).parent().children('.image-url');
		imageName = $(this).parent().children('.eis-file-name');
		// If the uploader object has already been created, reopen the dialog
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
			text: 'Choose Image'
		}, multiple: false });

		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function() {
			attachment = mediaUploader.state().get('selection').first().toJSON();
			// $('#image_admin_login_logo').attr('src', attachment.url);
			// $('#url_admin_login_logo').val(attachment.url);

			imagePreview.attr('src', attachment.url);
			imageURL.val(attachment.url);
			fileURL = attachment.url;
			imageName.html(fileURL.substring(fileURL.lastIndexOf('/')+1));
		});
		// Open the uploader dialog
		mediaUploader.open();
	});

	$(document).on("click", ".remove-image", function(e){
		$(this).parent().children('.image-preview').attr('src', '');
		$(this).parent().children('.image-url').val('');
		$(this).parent().children('.eis-file-name').html('');
	});

	// Gallery
	var galleryUploader;
	$('.gallery-button').click(function(e){
			e.preventDefault();
			// If the frame already exists, re-open it.
			if ( galleryUploader ) {
					galleryUploader.open();
					return;
			}
			// Sets up the media library frame
			galleryUploader = wp.media.frames.galleryUploader = wp.media({
					title: gallery_ids.title,
					button: { text:  gallery_ids.button },
					library: { type: 'image' },
					multiple: true
			});

			// Create Featured Gallery state. This is essentially the Gallery state, but selection behavior is altered.
			galleryUploader.states.add([
					new wp.media.controller.Library({
							id:         'eis-gallery',
							title:      'Select Images for Gallery',
							priority:   20,
							toolbar:    'main-gallery',
							filterable: 'uploaded',
							library:    wp.media.query( galleryUploader.options.library ),
							multiple:   galleryUploader.options.multiple ? 'reset' : false,
							editable:   true,
							allowLocalEdits: true,
							displaySettings: true,
							displayUserSettings: true
					}),
			]);

			galleryUploader.on('open', function() {
					var selection = galleryUploader.state().get('selection');
					var library = galleryUploader.state('gallery-edit').get('library');
					var ids = $('#gallery_ids').val();
					if (ids) {
							idsArray = ids.split(',');
							idsArray.forEach(function(id) {
									attachment = wp.media.attachment(id);
									attachment.fetch();
									selection.add( attachment ? [ attachment ] : [] );
							});
				 }
			});
			galleryUploader.on('ready', function() {
					$( '.media-modal' ).addClass( 'no-sidebar' );
			});
			// When an image is selected, run a callback.
			//galleryUploader.on('update', function() {
			galleryUploader.on('select', function() {
					var imageIDArray = [];
					var imageHTML = '';
					var metadataString = '';
					images = galleryUploader.state().get('selection');
					imageHTML += '<div class="eis-gallery-container">';
					images.each(function(attachment) {
							imageIDArray.push(attachment.attributes.id);
							imageHTML += '<div class="eis-image-gallery-container"><span class="eis-gallery-close"><img id="'+attachment.attributes.id+'" src="'+attachment.attributes.sizes.thumbnail.url+'"></span></div>';
					});
					imageHTML += '</ul>';
					metadataString = imageIDArray.join(",");
					if (metadataString) {
							$("#gallery_ids").val(metadataString);
							$(".eis-gallery-src").html(imageHTML);
							setTimeout(function(){
									ajaxUpdateTempMetaData();
							},0);
					}
			});
			// Finally, open the modal
			galleryUploader.open();
	});

	$(document).on('click', '.eis-gallery-close', function(event){
		event.preventDefault();
		if (confirm('Are you sure you want to remove this image?')) {
			var removedImage = $(this).children('img').attr('id');
			var oldGallery = $("#gallery_ids").val();
			var newGallery = oldGallery.replace(','+removedImage,'').replace(removedImage+',','').replace(removedImage,'');
			$(this).closest('.eis-image-gallery-container').remove();
			$("#gallery_ids").val(newGallery);
		}
	});

	$(document).on("click", ".remove-gallery", function(e) {
		$(this).parent().find('.eis-gallery-container').remove();
		$(this).parent().find('#gallery_ids').val('');
	});

	$(document).ready( function(){
		$('.eis-color-picker').cs_wpColorPicker();
	});

	$('.eis-date-input').datepicker({
		container: '.eis-interface-settings',
		format: "dd/mm/yyyy",
		todayHighlight: true,
		language: $('.wp-toolbar').attr('lang'),
	    autoclose: true,
	});

	$(document).on("click", ".eis-export-action", function(e) {
		$('.eis-import-container').removeClass('open');
		$('.eis-export-container').toggleClass('open');
	});
	$(document).on("click", ".eis-data-close", function(e) {
		$('.eis-data-container').removeClass('open');
	});
	$(document).on("click", ".eis-export-copy", function(e) {
		$('.eis-export-container textarea').select();
    	document.execCommand('copy');
		$(this).text($(this).data('copied'));
		setTimeout(function(){
			$('.eis-export-copy').text($('.eis-export-copy').data('label'));
		}, 2000);
	});
	$(document).on("click", ".eis-import-action", function(e) {
		$('.eis-export-container').removeClass('open');
		$('.eis-import-container').toggleClass('open');
	});
	$(document).on("click", ".eis-import-settings", function(e) {
		$('.eis-loader-container').show();
		var interface_id = $(this).data('interface-id');
		var settings = $('.eis-import-container textarea').val();
		var ajaxURL = $('.eis-page-container').data("ajax-url");
		var saveSettings = $.ajax({
			type: "POST",
			url: ajaxURL,
			data : { action: 'eis_save_interface_settings', interface_id: interface_id, settings: settings },
			cache: false,
			success: function(html) {
				location.reload();
			},
			error: function(html) {
				$('.eis-loader-container').hide();
			}
		});
	});
});
