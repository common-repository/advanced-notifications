var mobile_breakpoint = jQuery('.an-main-container').data('mobile_breakpoint');
var tablet_breakpoint = jQuery('.an-main-container').data('tablet_breakpoint');
function an_location_style(is_resize) {
    is_resize = typeof is_resize !== 'undefined' ? is_resize : false;
    var window_width = jQuery(window).width();
    jQuery('.an-location').each(function(index,element) {
        var location_id = jQuery(element).attr('id').replace('an-location-', '');
        if (an_settings['locations'].length !== 0 && an_settings['locations'][location_id] != undefined) {
            var mobile_location = an_settings['locations'][location_id]['mobile'];
            var tablet_location = an_settings['locations'][location_id]['tablet'];
            var desktop_location = an_settings['locations'][location_id]['desktop'];
            if (window_width < mobile_breakpoint) {
                var location = mobile_location;
            } else if (window_width < tablet_breakpoint) {
                var location = tablet_location;
            } else {
                var location = desktop_location;
            }
            jQuery(element).removeClass(mobile_location['class']).removeClass(tablet_location['class']).removeClass(desktop_location['class']).addClass(location['class']);
            let width_val = parseFloat(location['width_val']);
            let width_unit = location['width_unit'];
            jQuery(element).css("width", width_val+width_unit);
            let css = {};
            css["width"] = width_val+width_unit;
            let horizontal_position = location['horizontal_position'];
            jQuery(element).removeClass('an-horizontal-position-'+mobile_location['horizontal_position']).removeClass('an-horizontal-position-'+tablet_location['horizontal_position']).removeClass('an-horizontal-position-'+desktop_location['horizontal_position']).addClass('an-horizontal-position-'+horizontal_position);
            if (horizontal_position == 'left' || horizontal_position == 'right') {
                css[horizontal_position] = location['horizontal_val']+location['horizontal_unit'];
            } else {
                css['left'] = location['horizontal_val']+location['horizontal_unit'];
            }
            let vertical_position = location['vertical_position'];
            jQuery(element).removeClass('an-vertical-position-'+mobile_location['vertical_position']).removeClass('an-vertical-position-'+tablet_location['vertical_position']).removeClass('an-vertical-position-'+desktop_location['vertical_position']).addClass('an-vertical-position-'+vertical_position);
            if (vertical_position == 'bottom' || vertical_position == 'top') {
                css[vertical_position] = location['vertical_val']+location['vertical_unit'];
            } else {
                css['bottom'] = location['vertical_val']+location['vertical_unit'];
            }
            css["z-index"] = an_settings['locations'][location_id]['z_index'];
            if (vertical_position == 'top') {
                css["flex-direction"] = 'column-reverse';
            }
            jQuery(element).removeAttr('style').css(css);
            jQuery(document).trigger("an_location_style", [{element:element,location:location,css:css}]);
        }
    });
    if (!is_resize) {
        window.dispatchEvent(new Event('resize'));
    }
}
function an_design_style() {
    var window_width = jQuery(window).width();
    if (typeof an_settings['notifications'] === 'undefined' || an_settings['notifications'].length === 0) {
        return false;
    }
    jQuery('.an-container').each(function(index,element) {
        var notification = an_settings['notifications'][jQuery(element).attr('id').replace('an-', '')];
        var notification_design = (typeof an_settings['designs'][notification['design']] !== 'undefined') ? an_settings['designs'][notification['design']] : an_settings['designs']['default'];
        if (window_width < mobile_breakpoint) {
            var design = notification_design['mobile'];
            var design_close_button = notification_design['mobile_close_button'];
            var hidden = (notification['devices_list'] == 'tablet' || notification['devices_list'] == 'desktop_tablet' || notification['devices_list'] == 'desktop') ? true : false;
        } else if (window_width < tablet_breakpoint) {
            var design = notification_design['tablet'];
            var design_close_button = notification_design['tablet_close_button'];
            var hidden = (notification['devices_list'] == 'mobile' || notification['devices_list'] == 'desktop') ? true : false;
        } else {
            var design = notification_design['desktop'];
            var design_close_button = notification_design['desktop_close_button'];
            var hidden = (notification['devices_list'] == 'mobile' || notification['devices_list'] == 'tablet_mobile' || notification['devices_list'] == 'tablet') ? true : false;
        }
        let css = {};
        if (hidden) {
            css["display"] = 'none';
        } else {
            css["display"] = '';
            css["font-size"] = design['font_size']+'px';
            css["border-top"] = design['border_top'] > 0 ? design['border_top']+'px '+notification_design['border_style']+' '+notification_design['border_color'] : null;
            css["border-right"] = design['border_right'] > 0 ? design['border_right']+'px '+notification_design['border_style']+' '+notification_design['border_color'] : null;
            css["border-bottom"] = design['border_bottom'] > 0 ? design['border_bottom']+'px '+notification_design['border_style']+' '+notification_design['border_color'] : null;
            css["border-left"] = design['border_left'] > 0 ? design['border_left']+'px '+notification_design['border_style']+' '+notification_design['border_color'] : null;
            css["border-radius"] = design['border_radius_top_left']+'px '+design['border_radius_top_right']+'px '+design['border_radius_bottom_right']+'px '+design['border_radius_bottom_left']+'px';
            css["padding"] = design['padding_top']+'px '+design['padding_right']+'px '+design['padding_bottom']+'px '+design['padding_left']+'px';
            css["margin"] = design['margin_top']+'px '+design['margin_right']+'px '+design['margin_bottom']+'px '+design['margin_left']+'px';
        }
        jQuery(element).css(css);

        let close_button_css = {};
        let size = parseFloat(design_close_button['font_size']);
        if (design['close_button_type'] == 'icon') {
            close_button_css["width"] = size + 2;
            close_button_css["height"] = size + 2;
        }
        close_button_css["font-size"] = size;
        close_button_css["line-height"] = size + 2 +'px';
        jQuery(element).find('.an-close-button').css(close_button_css);
    });
}
function an_get_cookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function an_set_cookie(cname, cvalue, time) {
    var d = new Date();
    d.setTime(d.getTime() + time);
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function an_set_close_cookie(notification_id) {
    let notification = an_settings['notifications'][notification_id];
    if (notification['close_button_limit'] == 'cancel_for') {
        if (notification['close_button_limit_for']['cancel_unit'] == 'days') {
            var cancel_time = (24*60*60*1000);
        } else if (notification['close_button_limit_for']['cancel_unit'] == 'hours') {
            var cancel_time = (60*60*1000);
        } else {
            var cancel_time = (60*1000);
        }
        an_set_cookie('an_cancel_for_'+notification_id, 1, notification['close_button_limit_for']['cancel_val']*cancel_time);
    }
}
function an_set_limitations_cookie() {
    jQuery('.an-container').each(function(index,element) {
        let notification_id = jQuery(element).attr('id').replace('an-', '');
        let notification = an_settings['notifications'][notification_id];
        let an_limitations_cookie = parseFloat(an_get_cookie('an_limitations_'+notification_id));
        let limit_time = (notification['custom_limitations_for']['limit_unit'] == 'days') ? 86400000 : ((notification['custom_limitations_for']['limit_unit'] == 'hours') ? 3600000 : 60000);
        if (notification['limitations'] == "custom_limitations") {
            if (an_limitations_cookie > 0) {
                an_set_cookie('an_limitations_'+notification_id, an_limitations_cookie+1,(notification['custom_limitations_for']['limit_val']*limit_time));
            } else {
                an_set_cookie('an_limitations_'+notification_id, 1, (notification['custom_limitations_for']['limit_val']*limit_time));
            }
        } else if (an_limitations_cookie > 0) {
            an_set_cookie('an_limitations_'+notification_id, -1, 1);
        }
    });
}
function an_set_notification_limitations_cookie(notification_id) {
    if (typeof notification_id !== 'undefined') {
        let notification = an_settings['notifications'][notification_id];
        let an_limitations_cookie = parseFloat(an_get_cookie('an_limitations_'+notification_id));
        let limit_time = (notification['custom_limitations_for']['limit_unit'] == 'days') ? 86400000 : ((notification['custom_limitations_for']['limit_unit'] == 'hours') ? 3600000 : 60000);
        if (notification['limitations'] == "custom_limitations") {
            if (an_limitations_cookie > 0) {
                an_set_cookie('an_limitations_'+notification_id, an_limitations_cookie+1,(notification['custom_limitations_for']['limit_val']*limit_time));
            } else {
                an_set_cookie('an_limitations_'+notification_id, 1, (notification['custom_limitations_for']['limit_val']*limit_time));
            }
        } else if (an_limitations_cookie > 0) {
            an_set_cookie('an_limitations_'+notification_id, -1, 1);
        }
    }
}
function an_is_rules_pass(notification_id) {
    let notification = an_settings['notifications'][notification_id];
    let an_cancel_cookie = an_get_cookie('an_cancel_for_'+notification_id);
    if (notification['close_button_limit'] == "cancel_for" && an_cancel_cookie) {
        return false;
    }
    let an_limitations_cookie = an_get_cookie('an_limitations_'+notification_id);
    if (notification['limitations'] == "custom_limitations" && notification['custom_limitations_times'] <= an_limitations_cookie) {
        return false;
    }
    return true;
}
var an_show_when_page_loaded_arr = {};
function an_show_when_page_loaded(notification_id) {
    an_show_when_page_loaded_arr[notification_id] = true;
    let notification = an_settings['notifications'][notification_id];
    jQuery(document).trigger("an_show_when_page_loaded", [{notification:notification}]);
    return an_show_when_page_loaded_arr[notification_id];
}
var an_show_close = {};
function an_do_show(notification_id,close,is_first,set_cookie) {
    close = (typeof close !== 'undefined') ? close : true;
    is_first = (typeof is_first !== 'undefined') ? is_first : false;
    set_cookie = (typeof set_cookie !== 'undefined') ? set_cookie : true;
    let current_notification = jQuery('#an-'+notification_id);
    let notification = an_settings['notifications'][notification_id];
    let show_time = parseFloat(notification['show_time']);
    if (typeof an_show_close[notification_id] !== 'undefined') {
        clearTimeout(an_show_close[notification_id]);
    }
    if (current_notification.length && current_notification.hasClass('an-delay')) {
        an_design_style();
        let animation_in_duration = notification['animation_in_duration'];
        current_notification.parent().prepend(current_notification);
        current_notification.css("animation-duration", animation_in_duration+'s').removeClass('an-delay').addClass(notification['animation_in']);
        if (close) {
            if (show_time > 0) {
                an_show_close[notification_id] = setTimeout(function(){
                    an_do_close(notification_id);
                }, (show_time*1000));
            }
        }
    } else {
        if (show_time > 0 && close && !is_first) {
            an_show_close[notification_id] = setTimeout(function(){
                an_do_close(notification_id);
            }, (show_time*1000));
        }
    }
    if (set_cookie) {
        an_set_notification_limitations_cookie(notification_id);
    }
    an_location_style(true);
    an_design_style();
}
function an_show(notification_id,close) {
    if (typeof notification_id !== 'undefined' && an_is_rules_pass(notification_id)) {
        close = (typeof close !== 'undefined') ? close : true;
        an_do_show(notification_id, close);
    }
}
function an_do_close(notification_id) {
    let notification = an_settings['notifications'][notification_id];
    let element = jQuery('#an-'+notification_id);
    let element_full_height = element.outerHeight();
    let animation_out_duration = parseFloat(notification['animation_out_duration']);
    let animation_out_class = notification['animation_out'];
    if (typeof an_show_close[notification_id] !== 'undefined') {
        clearTimeout(an_show_close[notification_id]);
        delete an_show_close[notification_id];
    }
    element.css("animation-duration", animation_out_duration+'s').removeClass(notification['animation_in']).addClass(animation_out_class);
    let animation_out_class_delay = parseFloat(jQuery('.'+animation_out_class).css('animation-delay').replace(/[^0-9.]/g, ""));
    setTimeout(function(){
        element.animate({
            height: 0,
            padding: 0,
            margin: 0
        }, element_full_height*0.8, "linear", function() {
            element.addClass('an-delay').removeClass(animation_out_class).css('height', '');
            an_design_style();
        });
        an_location_style(false);
    }, (animation_out_duration+animation_out_class_delay)*1000);
}
function an_close(notification_id) {
    if (typeof notification_id !== 'undefined') {
        an_do_close(notification_id);
        an_set_close_cookie(notification_id);
    }
}
function an_get_trigger_notifications(trigger_id) {
    if (typeof trigger_id !== 'undefined') {
        let notifications_ids = [];
        if (an_settings['notifications'] != undefined) {
            jQuery.each(an_settings['notifications'] , function(index, notification) {
                if (notification['triggers'] != undefined && jQuery.inArray(trigger_id.toString(), notification['triggers']) !== -1) {
                    notifications_ids.push(notification['id']);
                }
            });
        }
        return notifications_ids;
    }
}
function an_get_triggers_ids_page(trigger_type) {
    if (typeof trigger_type !== 'undefined') {
        let triggers_ids = [];
        if (an_settings['triggers'] != undefined) {
            jQuery.each(an_settings['triggers'] , function(index, trigger) {
                if (trigger['trigger_type'] != undefined && trigger['trigger_type'] == trigger_type) {
                    triggers_ids.push(trigger['id']);
                }
            });
        }
        return triggers_ids;
    }
}
jQuery(document).ready(function($){
    // an_set_limitations_cookie();
    if (typeof an_settings['notifications'] === 'undefined' || an_settings['notifications'].length === 0) {
        return false;
    }
    an_location_style(false);
    an_design_style();
    $('.an-container').each(function(index,element) {
        let notification = an_settings['notifications'][$(element).attr('id').replace('an-', '')];
        // let design = an_settings['designs'][notification['design']];
        let design = (typeof an_settings['designs'][notification['design']] !== 'undefined') ? an_settings['designs'][notification['design']] : an_settings['designs']['default'];
        let location = an_settings['locations'][notification['location']];
        let css = {};
        css["color"] = design['text_color'];
        css["background-color"] = design['background_color'];
        if (design['background_image'] != '' && design['background_image'] == 'null') {
            css["background-image"] = 'url("'+design['background_image']+'")';
        }
        css["background-size"] = design['background_size'];
        css["background-repeat"] = design['background_repeat'];
        css["animation-duration"] = notification['animation_in_duration']+'s';
        if (design['active_shadow'] == 'yes') {
            let design_shadow = design['shadow'];
            css["box-shadow"] = design_shadow['offset_x']+'px '+design_shadow['offset_y']+'px '+design_shadow['blur_radius']+'px '+design_shadow['spread_radius']+'px '+design_shadow['shadow_color'];
        }
        $(element).css(css);
        let link_color = design['link_color'];
        $(element).find('a').each(function(index,a_element) {
            $(a_element).css("color", link_color);
        });
        if (design['close_button_enable'] == 'checked') {
            let close_button_continer_css = {};
            let position_name = design['close_button_position'];
            if (position_name == 'custom') {
                close_button_continer_css[design['close_button_horizontal_position']] = design['close_button_horizontal_position_val']+design['close_button_horizontal_position_unit'];
                close_button_continer_css[design['close_button_vertical_position']] = design['close_button_vertical_position_val']+design['close_button_vertical_position_unit'];
            } else {
                if (position_name.indexOf("top") >= 0) {
                    close_button_continer_css['top'] = '5px';
                } else {
                    close_button_continer_css['bottom'] = '5px';
                }
                if (position_name.indexOf("left") >= 0) {
                    close_button_continer_css['left'] = '5px';
                } else if (position_name.indexOf("center") >= 0) {
                    close_button_continer_css["left"] = '50%';
                    close_button_continer_css["transform"] = 'translateX(-50%)';
                } else {
                    close_button_continer_css['right'] = '5px';
                }
            }
            $(element).find('.an-close-container').css(close_button_continer_css);
            let close_button_css = {};
            close_button_css["color"] = design['close_button_color'];
            close_button_css["background-color"] = design['close_button_background_color'];
            $(element).find('.an-close-button').css(close_button_css);
        }
        if (an_is_rules_pass(notification['id']) && an_show_when_page_loaded(notification['id'])) {
            let show_when = (typeof(notification['show_when']) != "undefined" && notification['show_when'] !== null) ? notification['show_when'] : null;
            let delay = (show_when == 'page_loaded_trigers') ? parseFloat(notification['delay']) : 0;
            let set_delay = (delay > 0) ? (delay*1000) : (delay*100);
            setTimeout(function(){
                an_do_show(notification['id'], true, true);
                an_location_style(false);
            }, set_delay);
        }
    });

    $(window).resize(function() {
        an_location_style(true);
        an_design_style();
    });

    $(document).on("click", ".an-close-button", function(e){
        e.preventDefault();
        let notification_id = $(this).closest('.an-container').attr('id').replace('an-', '');
		an_close(notification_id);
	});

    if ($('.an-preview-container').length) {
        let delay = $('.an-preview-status-val').attr('data-delay');
        let delay_label = $('.an-preview-status-val').attr('data-delay-label');
        let show_label = $('.an-preview-status-val').attr('data-show-label');
        if (delay == 0) {
            $('.an-preview-status-val').text(show_label);
        } else {
            var an_countdown = setInterval(function(){
                delay -= 1;
                if(delay <= 0){
                    clearInterval(an_countdown);
                    $('.an-preview-status-val').text(show_label);
                } else {
                    $('.an-preview-status-val').text(delay_label + ' ' + delay);
                }
            }, 1000);
        }
    }
    $(document).on('mouseleave', function(e) {
        if (e.pageY - $(window).scrollTop() <= 1) {
            let notifications_ids = an_get_trigger_notifications('mouse_leave_from_top');
            $.each(notifications_ids , function(index, notification_id) {
                an_show(notification_id);
            });
        }
    });
    $('.an-container').on('mouseover focusin', function(e) {
        let notification_id = $(this).attr('id').replace('an-', '');
        let notification = an_settings['notifications'][notification_id];
        let show_time = parseFloat(notification['show_time']);
        if (typeof an_show_close[notification_id] !== 'undefined' && show_time > 0) {
            clearTimeout(an_show_close[notification_id]);
        }
    });
    $('.an-container').on('mouseout focusout', function(e) {
        let notification_id = $(this).attr('id').replace('an-', '');
        let notification = an_settings['notifications'][notification_id];
        let show_time = parseFloat(notification['show_time']);
        if (typeof an_show_close[notification_id] !== 'undefined' && show_time > 0) {
            an_do_show(notification_id, true, false, false);
        }
    });
    $(document).on('click', '*', function(e) {
        // e.stopPropagation();
        // e.stopImmediatePropagation();
        let element = $(this);
        let triggers_ids = an_get_triggers_ids_page('on_click');
        if (triggers_ids.length) {
            $.each(triggers_ids , function(index, trigger_id) {
                if (an_settings['triggers'][trigger_id] != undefined) {
                    let trigger_on_click = an_settings['triggers'][trigger_id];
                    if (trigger_on_click['trigger_element_name'] != undefined && trigger_on_click['trigger_element_name'] != '') {
                        let notifications_ids = an_get_trigger_notifications(trigger_id);
                        let trigger_element_name_arr = $.map(trigger_on_click['trigger_element_name'].split(","), $.trim).filter(Boolean); // string to array, trim and remove empty
                        if (trigger_on_click['trigger_element'] == 'class') {
                            $.each(trigger_element_name_arr , function(index, element_class) {
                                if (element.hasClass(element_class)) {
                                    $.each(notifications_ids , function(index, notification_id) {
                                        if (trigger_on_click['trigger_action'] == 'close') {
                                            an_close(notification_id);
                                        } else {
                                            an_show(notification_id);
                                        }
                                    });
                                }
                            });
                        }
                        if (trigger_on_click['trigger_element'] == 'id') {
                            $.each(trigger_element_name_arr , function(index, element_id) {
                                if (element.is('#'+element_id)) {
                                    $.each(notifications_ids , function(index, notification_id) {
                                        if (trigger_on_click['trigger_action'] == 'close') {
                                            an_close(notification_id);
                                        } else {
                                            an_show(notification_id);
                                        }
                                    });
                                }
                            });
                        }
                        if (trigger_on_click['trigger_element'] == 'tag') {
                            $.each(trigger_element_name_arr , function(index, element_tag) {
                                if (element.prop("tagName") == element_tag.toUpperCase()) {
                                    $.each(notifications_ids , function(index, notification_id) {
                                        if (trigger_on_click['trigger_action'] == 'close') {
                                            an_close(notification_id);
                                        } else {
                                            an_show(notification_id);
                                        }
                                    });
                                }
                            });
                        }
                    }
                }
            });
        }
    });
    $(document).on('mouseover focusin', '*', function(e) {
        // e.stopPropagation();
        // e.stopImmediatePropagation();
        let element = $(this);
        let triggers_ids = an_get_triggers_ids_page('on_hover');
        if (triggers_ids.length) {
            $.each(triggers_ids , function(index, trigger_id) {
                if (an_settings['triggers'][trigger_id] != undefined) {
                    let trigger_on_hover = an_settings['triggers'][trigger_id];
                    if (trigger_on_hover['trigger_element_name'] != undefined && trigger_on_hover['trigger_element_name'] != '') {
                        let notifications_ids = an_get_trigger_notifications(trigger_id);
                        let trigger_element_name_arr = $.map(trigger_on_hover['trigger_element_name'].split(","), $.trim).filter(Boolean); // string to array, trim and remove empty
                        if (trigger_on_hover['trigger_element'] == 'class') {
                            $.each(trigger_element_name_arr , function(index, element_class) {
                                if (element.hasClass(element_class)) {
                                    $.each(notifications_ids , function(index, notification_id) {
                                        if (trigger_on_hover['trigger_action'] == 'close') {
                                            an_close(notification_id);
                                        } else {
                                            an_show(notification_id);
                                        }
                                    });
                                }
                            });
                        }
                        if (trigger_on_hover['trigger_element'] == 'id') {
                            $.each(trigger_element_name_arr , function(index, element_id) {
                                if (element.is('#'+element_id)) {
                                    $.each(notifications_ids , function(index, notification_id) {
                                        if (trigger_on_hover['trigger_action'] == 'close') {
                                            an_close(notification_id);
                                        } else {
                                            an_show(notification_id);
                                        }
                                    });
                                }
                            });
                        }
                        if (trigger_on_hover['trigger_element'] == 'tag') {
                            $.each(trigger_element_name_arr , function(index, element_tag) {
                                if (element.prop("tagName") == element_tag.toUpperCase()) {
                                    $.each(notifications_ids , function(index, notification_id) {
                                        if (trigger_on_hover['trigger_action'] == 'close') {
                                            an_close(notification_id);
                                        } else {
                                            an_show(notification_id);
                                        }
                                    });
                                }
                            });
                        }
                    }
                }
            });
        }
    });
});
