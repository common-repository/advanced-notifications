jQuery(document).ready( function($) {
	// HIDE CHILD FIELDS
	function eis_hide_children_of_hidden_parent(element) {
		element = typeof element !== 'undefined' ? element : false;
		if (element) {
			let parent_option_name = $(element).closest('[data-option-name]').data('option-name');
			if (typeof parent_option_name != 'undefined') {
				let parent_value = $(element).val();
				let children = $('[data-parent-option="'+parent_option_name+'"]');
				children.each(function(index,child) {
					let child_option_name = $(child).closest('[data-option-name]').data('option-name');
					if ($(child).hasClass('hidden')) {
						let children_2 = $('[data-parent-option="'+child_option_name+'"]').addClass('eis-children-hidden');
					} else {
						let children_2 = $('[data-parent-option="'+child_option_name+'"]').removeClass('eis-children-hidden');
					}
				});
			}
		} else {
			$('[data-parent-option]').each(function(index,element) {
				var parent_attr = $(element).find('select').attr("name");
				if (typeof parent_attr != 'undefined') {
					$('[data-parent-option]').each(function(index,ele) {
						var parent_option = $(this).data('parent-option');
						var parent = $('select[name$="['+parent_option+']"]').closest('.form-field');
						var is_parent = parent_attr.includes('['+parent_option+']');
						if (is_parent) {
							$(this).addClass('eis-children-hidden');
						} else if ($(parent).hasClass('hidden') != true) {
							$(this).removeClass('eis-children-hidden');
						}
					});
				}
			});
		}
	}
	function eis_hide_unselected_child_field(element) {
		element = typeof element !== 'undefined' ? element : false;
		if (element) {
			let parent_option_name = $(element).closest('[data-option-name]').data('option-name');
			if (typeof parent_option_name != 'undefined') {
				let parent_value = $(element).val();
				let children = $('[data-parent-option="'+parent_option_name+'"]');
				children.each(function(index,child) {
					let child_value = $(child).data('parent-value');
					if (typeof child_value != 'undefined') {
						if (jQuery.inArray(parent_value, child_value.split(',')) !== -1) {
							$(child).removeClass('hidden');
						} else {
							$(child).addClass('hidden');
						}
					}
				});
			}
		} else {
			$('[data-parent-option]').each(function(index,element) {
				let parent_option = $(element).data('parent-option');
				let parent_value = $(element).data('parent-value');
				if (typeof parent_value != 'undefined') {
					let parent = $('select[name$="['+parent_option+']"]').val();
					if (jQuery.inArray(parent, parent_value.split(',')) !== -1) {
						$(element).removeClass('hidden');
					} else {
						$(element).addClass('hidden');
					}
				}
			});
		}
		eis_hide_children_of_hidden_parent(element);
	}
	eis_hide_unselected_child_field();
	function eis_hide_selected_child_field(element) {
		element = typeof element !== 'undefined' ? element : false;
		if (element) {
			let parent_option_name = $(element).closest('[data-option-name]').data('option-name');
			if (typeof parent_option_name != 'undefined') {
				let parent_value = $(element).val();
				let children = $('[data-parent-option="'+parent_option_name+'"]');
				children.each(function(index,child) {
					let child_value = $(child).data('hide-value');
					if (typeof child_value != 'undefined') {
						if (jQuery.inArray(parent_value, child_value.split(',')) !== -1) {
							$(child).addClass('hidden');
						} else {
							$(child).removeClass('hidden');
						}
					}
				});
			}
		} else {
			$('[data-parent-option]').each(function(index,element) {
				let parent_option = $(element).data('parent-option');
				let hide_value = $(element).data('hide-value');
				if (typeof hide_value != 'undefined') {
					let parent = $('select[name$="['+parent_option+']"]').val();
					if (jQuery.inArray(parent, hide_value.split(',')) !== -1) {
						$(element).addClass('hidden');
					} else {
						$(element).removeClass('hidden');
					}
				}
			});
		}
		eis_hide_children_of_hidden_parent(element);
	}
	eis_hide_selected_child_field();
	$(document).on("input", ".eis-interface-settings input, .eis-interface-settings select", function(e){
		e.stopPropagation();
		eis_hide_unselected_child_field(this);
		eis_hide_selected_child_field(this);
	});

	$(document).on("click", "input[name*=_display_eis_checkbox]", function(e) {
		if ($(this).is(":checked")) {
			$(this).parent().children("input[type=hidden]").val($(this).val());
		} else {
			$(this).parent().children("input[type=hidden]").val('no');
		}
	});
});
