jQuery(document).ready(function($){
    function an_type() {
        $('#postdivrich').hide();
        if ($('[name="a_notifications_eis_options[type]"]').val() == 'type_editor') {
            $('#postdivrich').show();
        }
        $('html,body').animate({
            scrollTop: $(window).scrollTop()+2
        }, 500);
    }
    if ($('body').hasClass('post-type-a_notifications')) {
        an_type();
        $(document).on('change', '[name="a_notifications_eis_options[type]"]', function(e) {
            an_type();
        });
    }
    $(document).on('click', '.an-options-all', function(e) {
        e.preventDefault();
        $(this).closest('.an-options-container').find('[type="checkbox"]').prop( "checked", true).trigger('click').trigger('click');
    });
    $(document).on('click', '.an-options-none', function(e) {
        e.preventDefault();
        $(this).closest('.an-options-container').find('[type="checkbox"]').prop( "checked", false).trigger('click').trigger('click');
    });
    function an_ajax(element,an_action,notification_id,value) {
        var do_ajax = $.ajax({
            type: "POST",
            url: ajaxurl,
            data : { action: 'an_ajax', an_action: an_action, notification_id: notification_id, value: value },
            cache: false,
            // async: false,
            success: function(html) {
                element.closest('.an-switch').siblings('.an-save-option').fadeIn('slow', function(e) {
                    setTimeout(function(){
                        element.closest('.an-switch').siblings('.an-save-option').fadeOut('slow');
                    }, 1000);
                });
            },
            error: function(html){
            }
        });
    }
    $(document).on('click', '[data-an-action]', function(e) {
        e.stopPropagation();
        let an_action = $(this).attr("data-an-action");
        let notification_id = $(this).attr("data-notification_id");
        let value = $(this).is(":checked");
        an_ajax($(this),an_action,notification_id,value);
    });
    $(document).on('input', '.an-options-container select', function(e) {
        let trigger_select_el = $(this);
        let trigger_description_el = trigger_select_el.closest('.an-options-container').find('.an-trigger-description').text('');
        if (admin_an_settings['triggers'] != undefined) {
            $.each(admin_an_settings['triggers'] , function(index, val) {
                if (val['description'] != undefined && val['id'] != undefined && trigger_select_el.val() == val['id']) {
                    trigger_description_el.text(val['description']);
                }
            });
        }
    });
});
