EventSubmission = function () {
    /// <summary>Constructor function of the event EventSubmission class.</summary>
    /// <returns type="EventSubmission" />
    return {
        ///<summary>
        ///Initializes the event submission.
        ///</summary>
        ///<returns type="initialization settings" />
        /// <since>1.0.0</since>
        init: function () {
            Common.logInfo("EventSubmission.init...");

            jQuery(document).on("click", ".event-manager-remove-uploaded-file", function (e) {
                e.preventDefault(); // Prevent default anchor behavior
                var parentDiv = jQuery(this).closest(".event-manager-uploaded-file");
                // Remove the preview span
                parentDiv.find(".event-manager-uploaded-file-preview").remove();
                // Clear the hidden input field value
                parentDiv.find('input[type="hidden"]').val('');
            });
            jQuery(".wpem_add_organizer_popup").on('click', function(){
                jQuery("#oragnizer_message").html('');
            });
            jQuery(".wpem_add_venue_popup").on('click', function(){
                jQuery("#venue_message").html('');
            });
            if (jQuery('#event_start_time').length > 0) {
                jQuery('#event_start_time').timepicker({
                    'timeFormat': wp_event_manager_event_submission.i18n_timepicker_format,
                    'step': wp_event_manager_event_submission.i18n_timepicker_step,
                });
            }

            if (jQuery('#event_end_time').length > 0) {
                jQuery('#event_end_time').timepicker({
                    'timeFormat': wp_event_manager_event_submission.i18n_timepicker_format,
                    'step': wp_event_manager_event_submission.i18n_timepicker_step,
                });
            }

            if (jQuery('input[data-picker="timepicker"]').length > 0) {
                jQuery('input[data-picker="timepicker"]').timepicker({
                    'timeFormat': wp_event_manager_event_submission.i18n_timepicker_format,
                    'step': wp_event_manager_event_submission.i18n_timepicker_step,
                });
            }

            if (jQuery('input[data-picker="datepicker"]#event_start_date').length > 0) {
                wp_event_manager_event_submission.start_of_week = parseInt(wp_event_manager_event_submission.start_of_week);

                if (wp_event_manager_event_submission.show_past_date) {
                    jQuery('input[data-picker="datepicker"]#event_start_date').datepicker({
                        dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                        firstDay: wp_event_manager_event_submission.start_of_week,
                        monthNames: wp_event_manager_event_submission.monthNames,
                        dayNames: wp_event_manager_event_submission.i18n_dayNames,
                        dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                    }).on('change', function () {

                        jQuery("#event_end_date").datepicker("destroy");

                        jQuery('input[data-picker="datepicker"]#event_end_date').datepicker({
                            minDate: jQuery('#event_start_date').val(),
                            dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                            firstDay: wp_event_manager_event_submission.start_of_week,
                            monthNames: wp_event_manager_event_submission.monthNames,
                            dayNames: wp_event_manager_event_submission.i18n_dayNames,
                            dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                        });

                    });
                } else {
                    jQuery('input[data-picker="datepicker"]#event_start_date').datepicker({
                        minDate: 0,
                        dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                        firstDay: wp_event_manager_event_submission.start_of_week,
                        monthNames: wp_event_manager_event_submission.monthNames,
                        dayNames: wp_event_manager_event_submission.i18n_dayNames,
                        dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                    }).on('change', function () {
                        // set the "event_start_date" end to not be later than "event_end_date" starts:
                        //jQuery('#event_start_date').datepicker('minDate', new Date(jQuery(this).val()));

                        jQuery("#event_end_date").datepicker("destroy");

                        jQuery('input[data-picker="datepicker"]#event_end_date').datepicker({
                            minDate: jQuery('#event_start_date').val(),
                            dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                            firstDay: wp_event_manager_event_submission.start_of_week,
                            monthNames: wp_event_manager_event_submission.monthNames,
                            dayNames: wp_event_manager_event_submission.i18n_dayNames,
                            dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                        });

                    });
                }
            }
            jQuery('body').on('change', '#event_end_date, #event_start_date, #event_start_time, #event_end_time', EventSubmission.actions.checkEndDate);
            EventSubmission.actions.checkEndDate();

            if (jQuery('input[data-picker="datepicker"]#event_end_date').length > 0) {
                jQuery('input[data-picker="datepicker"]#event_end_date').datepicker({
                    dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                    firstDay: wp_event_manager_event_submission.start_of_week,
                    monthNames: wp_event_manager_event_submission.monthNames,
                    dayNames: wp_event_manager_event_submission.i18n_dayNames,
                    dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin,
                    beforeShow: function (input, inst) {
                        var mindate = jQuery('input[data-picker="datepicker"]#event_start_date').datepicker('getDate');
                        jQuery(this).datepicker('option', 'minDate', mindate);
                    }
                }).on('change', function () {

                    jQuery("#event_registration_deadline").datepicker("destroy");

                    if (wp_event_manager_event_submission.show_past_date) {
                        jQuery('input[data-picker="datepicker"]#event_registration_deadline').datepicker({
                            maxDate: jQuery('#event_end_date').val(),
                            dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                            firstDay: wp_event_manager_event_submission.start_of_week,
                            monthNames: wp_event_manager_event_submission.monthNames,
                            dayNames: wp_event_manager_event_submission.i18n_dayNames,
                            dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                        });
                    }
                    else {
                        jQuery('input[data-picker="datepicker"]#event_registration_deadline').datepicker({
                            minDate: 0,
                            maxDate: jQuery('#event_end_date').val(),
                            dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                            firstDay: wp_event_manager_event_submission.start_of_week,
                            monthNames: wp_event_manager_event_submission.monthNames,
                            dayNames: wp_event_manager_event_submission.i18n_dayNames,
                            dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                        });
                    }
                });
            }

            if (jQuery('input[data-picker="datepicker"]#event_registration_deadline').length > 0) {
                if (wp_event_manager_event_submission.show_past_date) {
                    jQuery('input[data-picker="datepicker"]#event_registration_deadline').datepicker({
                        maxDate: jQuery('#event_end_date').val(),
                        dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                        firstDay: wp_event_manager_event_submission.start_of_week,
                        monthNames: wp_event_manager_event_submission.monthNames,
                        dayNames: wp_event_manager_event_submission.i18n_dayNames,
                        dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                    });
                }
                else {
                    jQuery('input[data-picker="datepicker"]#event_registration_deadline').datepicker({
                        minDate: 0,
                        maxDate: jQuery('#event_end_date').val(),
                        dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                        firstDay: wp_event_manager_event_submission.start_of_week,
                        monthNames: wp_event_manager_event_submission.monthNames,
                        dayNames: wp_event_manager_event_submission.i18n_dayNames,
                        dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                    });
                }
            }

            if (jQuery('input[data-picker="datepicker"]').length > 0) {
                if (wp_event_manager_event_submission.show_past_date) {
                    jQuery('input[data-picker="datepicker"]').datepicker({
                        dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                        firstDay: wp_event_manager_event_submission.start_of_week,
                        monthNames: wp_event_manager_event_submission.monthNames,
                        dayNames: wp_event_manager_event_submission.i18n_dayNames,
                        dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                    });
                }
                else {
                    jQuery('input[data-picker="datepicker"]').datepicker({
                        minDate: 0,
                        dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                        firstDay: wp_event_manager_event_submission.start_of_week,
                        monthNames: wp_event_manager_event_submission.monthNames,
                        dayNames: wp_event_manager_event_submission.i18n_dayNames,
                        dayNamesMin: wp_event_manager_event_submission.i18n_dayNamesMin
                    });
                }
            }

            //initially hide address, pincode, location textbox.
            if (jQuery('#event_online').length > 0) {
                //hide event venue name, address, location and pincode fields at the edit event when select online event
                if (jQuery('input[name=event_online]:checked').val() == 'yes') {
                    if (jQuery('.fieldset-event_venue_name').length > 0 && jQuery('input[name=event_venue_name]').length > 0) {
                        if (jQuery('input[name=event_venue_name]').attr('required'))
                            jQuery('input[name=event_venue_name]').attr('required', false);

                        jQuery('.fieldset-event_venue_name').hide();
                    }

                    if (jQuery('.fieldset-event_address').length > 0 && jQuery('input[name=event_address]').length > 0) {
                        if (jQuery('input[name=event_address]').attr('required'))
                            jQuery('input[name=event_address]').attr('required', false);

                        jQuery('.fieldset-event_address').hide();
                    }

                    if (jQuery('.fieldset-event_pincode').length > 0 && jQuery('input[name=event_pincode]').length > 0) {
                        if (jQuery('input[name=event_pincode]').attr('required'))
                            jQuery('input[name=event_pincode]').attr('required', false);

                        jQuery('.fieldset-event_pincode').hide();
                    }

                    if (jQuery('.fieldset-event_location').length > 0 && jQuery('input[name=event_location]').length > 0) {
                        if (jQuery('input[name=event_location]').attr('required'))
                            jQuery('input[name=event_location]').attr('required', false);

                        jQuery('.fieldset-event_location').hide();
                    }

                    if (jQuery('.fieldset-event_country').length > 0 && jQuery('select[name=event_country]').length > 0) {
                        if (jQuery('select[name=event_country]').attr('required'))
                            jQuery('select[name=event_country]').attr('required', false);

                        jQuery('.fieldset-event_country').hide();
                    }

                    if (jQuery('.fieldset-event_venue_ids').length > 0) {
                        jQuery('.fieldset-event_venue_ids').hide();
                    }
                }
            }

            //initially hide ticket price textbox
            if (jQuery('#event_ticket_options').length > 0 && jQuery('#event_ticket_options:checked').val() == 'free') {
                if (jQuery('input[name=event_ticket_price]').attr('required'))
                    jQuery('input[name=event_ticket_price]').attr('required', false);

                jQuery('.fieldset-event_ticket_price').hide();
            }

            jQuery('input[name=event_online]').on('change', EventSubmission.actions.onlineEvent);
            jQuery('input[name=event_ticket_options]').on('change', EventSubmission.actions.eventTicketOptions);

            jQuery('body').on('click', '.wpem_add_organizer', EventSubmission.actions.addOrganizer);
            jQuery('body').on('click', '.wpem_add_venue', EventSubmission.actions.addVenue);

            //add links for paid and free tickets   
            jQuery('.add-group-row').on('click', EventSubmission.actions.addGroupField);

            //delete tickets 
            jQuery(document).delegate('.remove-group-row', 'click', EventSubmission.actions.removeGroupField);

            jQuery("input[name=enable_health_guideline]").on('change', EventSubmission.actions.guidelineEvent);
            if (jQuery('input[name=enable_health_guideline]:checked').length > 0) {
                jQuery('input[name=enable_health_guideline]:checked').trigger('change');
            } else {
                jQuery("input[name=enable_health_guideline][value='no']").prop("checked", true).trigger("change");
            }

            jQuery("input[name=enable_health_guideline_other]").on('change', EventSubmission.actions.otherguidelineEvent);
            if (jQuery('input[name=enable_health_guideline_other]:checked').length > 0) {
                jQuery('input[name=enable_health_guideline_other]:checked').trigger('change');
            } else {
                jQuery("input[name=enable_health_guideline_other][value='no']").prop("checked", true).trigger("change");
            }
        },

        actions: {
            /// <summary>
            /// On click add ticket link fields paid and free
            //It will generate dynamic name and id for ticket fields.
            /// </summary>                 
            /// <returns type="generate name and id " />     
            /// <since>1.0.0</since>            
            addGroupField: function (event) {

                var $wrap = jQuery(this).closest('.field');
                var max_index = 0;

                $wrap.find('input.group-row').each(function () {
                    if (parseInt(jQuery(this).val()) > max_index) {
                        max_index = parseInt(jQuery(this).val());
                    }
                });

                var html = jQuery(this).data('row').replace(/%%group-row-index%%/g, max_index + 1);
                html = html.replace(/%group-row-index%/g, max_index + 1);

                jQuery(this).before(html);

                if ($wrap.find('input[data-picker="datepicker"]').length > 0) {
                    $wrap.find('input[data-picker="datepicker"]').datepicker({
                        dateFormat: wp_event_manager_event_submission.i18n_datepicker_format,
                        firstDay: wp_event_manager_event_submission.start_of_week
                    });
                }
                if ($wrap.find('input[data-picker="timepicker"]').length > 0) {
                    $wrap.find('input[data-picker="timepicker"]').timepicker({
                        'timeFormat': wp_event_manager_event_submission.i18n_timepicker_format,
                        'step': wp_event_manager_event_submission.i18n_timepicker_step,
                    });
                }
                if ($wrap.find('select[multiple="multiple"]').length > 0) {
                    $wrap.find('select[multiple="multiple"]').chosen();
                }
                event.preventDefault();
            },

            /// <summary>
            /// Remove Paid and free tickets fields 
            /// </summary>                 
            /// <returns type="remove paid and free tickets fields" />     
            /// <since>1.0.0</since>
            removeGroupField: function (event) {
                jQuery("." + this.id).remove();
                event.preventDefault();
            },

            /// <summary>
            /// Hide guideline
            /// </summary>
            /// <returns type="initialization settings" />
            /// <since>3.1.16</since>
            guidelineEvent: function (event) {
                event.preventDefault();
                var selectedValue = this.value;
                if (selectedValue == '' || selectedValue == 'no') {
                    jQuery('.fieldset-event_health_guidelines').hide();
                    jQuery('.fieldset-enable_health_guideline_other').hide();
                    jQuery('input[name=enable_health_guideline_other][value="no"]').prop('checked', true).trigger('change');
                } else {
                    jQuery('.fieldset-event_health_guidelines').show();
                    jQuery('.fieldset-enable_health_guideline_other').show();
                }
            },


            /// <summary>
            /// Hide other guideline text
            /// </summary>
            /// <returns type="initialization settings" />
            /// <since>3.1.16</since>
            otherguidelineEvent: function (event) {
                event.preventDefault();
                var selectedValue = this.value;
                if (selectedValue == '' || selectedValue == 'no')  {
                    jQuery('.fieldset-event_health_guidelines_other').hide();
                } else {
                    jQuery('.fieldset-event_health_guidelines_other').show();
                }
            },

            /// <summary>
            /// Check end date and time time
            /// </summary>
            /// <param name="parent" type="Event"></param>
            /// <returns type="actions" />
            /// <since>1.0.0</since>
            checkEndDate: function (event) {

                const startDate = jQuery('#event_start_date').val();
                const endDate = jQuery('#event_end_date').val();
                const startTime = jQuery('#event_start_time').val();

                if (startDate && endDate && startDate === endDate && startTime) {

                    jQuery('#event_end_time').timepicker('remove');
                    jQuery('#event_end_time').timepicker({
                        'timeFormat': wp_event_manager_event_submission.i18n_timepicker_format,
                        'step': wp_event_manager_event_submission.i18n_timepicker_step,
                        'disableTimeRanges': [['12:00am', startTime]],
                        'forceRoundTime': true,
                        'showDuration': false
                    });
                } else {
                    jQuery('#event_end_time').timepicker('remove');

                    jQuery('#event_end_time').timepicker({
                        'timeFormat': wp_event_manager_event_submission.i18n_timepicker_format,
                        'step': wp_event_manager_event_submission.i18n_timepicker_step
                    });
                }
            },

            /// <summary>
            /// Hide address,location and pincode filed when online event.
            /// </summary>
            /// <returns type="initialization settings" />
            /// <since>1.0.0</since>
            onlineEvent: function (event) {
                event.preventDefault();
                if (jQuery('#event_online').length > 0) {
                    if (jQuery(this).val() == "yes") {
                        if (jQuery('.fieldset-event_venue_name').length > 0 && jQuery('input[name=event_venue_name]').length > 0) {
                            if (jQuery('input[name=event_venue_name]').attr('required'))
                                jQuery('input[name=event_venue_name]').attr('required', false);

                            jQuery('.fieldset-event_venue_name').hide();
                        }
                        if (jQuery('.fieldset-event_address').length > 0 && jQuery('input[name=event_address]').length > 0) {
                            if (jQuery('input[name=event_address]').attr('required'))
                                jQuery('input[name=event_address]').attr('required', false);

                            jQuery('.fieldset-event_address').hide();
                        }
                        if (jQuery('.fieldset-event_pincode').length > 0 && jQuery('input[name=event_pincode]').length > 0) {
                            if (jQuery('input[name=event_pincode]').attr('required'))
                                jQuery('input[name=event_pincode]').attr('required', false);

                            jQuery('.fieldset-event_pincode').hide();
                        }
                        if (jQuery('.fieldset-event_location').length > 0 && jQuery('input[name=event_location]').length > 0) {
                            if (jQuery('input[name=event_location]').attr('required'))
                                jQuery('input[name=event_location]').attr('required', false);

                            jQuery('.fieldset-event_location').hide();
                        }
                        if (jQuery('.fieldset-event_country').length > 0 && jQuery('select[name=event_country]').length > 0) {
                            if (jQuery('select[name=event_country]').attr('required'))
                                jQuery('select[name=event_country]').attr('required', false);
    
                            jQuery('.fieldset-event_country').hide();
                        }
                        if (jQuery('.fieldset-event_venue_ids').length > 0) {
                            jQuery('.fieldset-event_venue_ids').hide();
                        }
                    } else {
                        if (jQuery('.fieldset-event_venue_name').length > 0 && jQuery('input[name=event_venue_name]').length > 0) {
                            if (jQuery('input[name=event_venue_name]').attr('required'))
                                jQuery('input[name=event_venue_name]').attr('required', true);

                            jQuery('.fieldset-event_venue_name').show();
                        }
                        if (jQuery('.fieldset-event_address').length > 0 && jQuery('input[name=event_address]').length > 0) {
                            if (jQuery('input[name=event_address]').attr('required'))
                                jQuery('input[name=event_address]').attr('required', true);

                            jQuery('.fieldset-event_address').show();
                        }
                        if (jQuery('.fieldset-event_pincode').length > 0 && jQuery('input[name=event_pincode]').length > 0) {
                            if (jQuery('input[name=event_pincode]').attr('required'))
                                jQuery('input[name=event_pincode]').attr('required', true);

                            jQuery('.fieldset-event_pincode').show();
                        }
                        if (jQuery('.fieldset-event_location').length > 0 && jQuery('input[name=event_location]').length > 0) {
                            if (jQuery('input[name=event_location]').attr('required'))
                                jQuery('input[name=event_location]').attr('required', true);

                            jQuery('.fieldset-event_location').show();
                        }
                        if (jQuery('.fieldset-event_country').length > 0 && jQuery('select[name=event_country]').length > 0) {
                            if (jQuery('select[name=event_country]').attr('required'))
                                jQuery('select[name=event_country]').attr('required', true);

                            jQuery('.fieldset-event_country').show();
                        }
                        if (jQuery('.fieldset-event_venue_ids').length > 0) {
                            jQuery('.fieldset-event_venue_ids').show();
                        }
                    }
                }
            },

            /// <summary>
            /// Show and Hide ticket price textbox.
            /// </summary>
            /// <returns type="initialization ticket price settings" />
            /// <since>1.0.0</since>
            eventTicketOptions: function (event) {
                event.preventDefault();
                Common.logInfo("EventDashboard.actions.eventTicketOptions...");
                if (jQuery('#event_ticket_options').length > 0) {
                    if (jQuery(this).val() == "free") {
                        if (jQuery('.fieldset-event_ticket_price').length > 0 && jQuery('input[name=event_ticket_price]').length > 0) {
                            if (jQuery('input[name=event_ticket_price]').attr('required'))
                                jQuery('input[name=event_ticket_price]').attr('required', false);

                            jQuery('.fieldset-event_ticket_price').hide();
                        }
                    } else {
                        if (jQuery('.fieldset-event_ticket_price').length > 0 && jQuery('input[name=event_ticket_price]').length > 0)
                            if (jQuery('input[name=event_ticket_price]').attr('required'))
                                jQuery('input[name=event_ticket_price]').attr('required', true);

                        jQuery('.fieldset-event_ticket_price').show();
                    }
                }
            },

            /// <summary>
            /// add organizer.
            /// </summary>
            /// <returns type="initialization ticket price settings" />
            /// <since>3.1.16</since>
            addOrganizer: function (event) {
                var formData = jQuery('body #submit-organizer-form').serialize();
                var organizer_description = tinyMCE.get('organizer_description').getContent();

                var fd = new FormData();
                if (jQuery('#organizer_logo').length > 0)
                    fd.append("organizer_logo", jQuery('#organizer_logo')[0].files[0]);

                fd.append("action", 'add_organizer');
                fd.append("form_data", formData);
                fd.append("organizer_description", organizer_description);

                jQuery.ajax({
                    url: wp_event_manager_event_submission.ajax_url,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    data: fd,
                    beforeSend: function(){
                        jQuery('.wpem_add_organizer').css('pointer-events', 'none');
                    },
                    success: function (responce) {
                        if (responce.code == 200) {
                            jQuery('select#event_organizer_ids').prepend('<option selected="selected" value="' + responce.organizer.organizer_id + '">' + responce.organizer.organizer_name + '</option>');
                            jQuery('#event_organizer_ids').trigger("chosen:updated");
                            jQuery('body #submit-organizer-form .event-manager-uploaded-files').html("");
                            jQuery('body #submit-organizer-form')[0].reset();
                            jQuery('.wpem_add_organizer').css('pointer-events', 'auto');
                            //jQuery('.wpem_add_organizer').css('pointer-events', 'none');
                            jQuery('#wpem_add_organizer_popup .wpem-modal-header-close .wpem-modal-close').trigger("click");
                            jQuery('body #submit-organizer-form .wpem-form-footer .wpem-alert-danger').remove();
                        } else {
                            jQuery('.wpem_add_organizer').css('pointer-events', 'auto');
                            jQuery('#oragnizer_message').html(responce.message);
                        }
                    }
                });
            },

            /// <summary>
            /// add venue.
            /// </summary>
            /// <returns type="initialization ticket price settings" />
            /// <since>3.1.16</since>
            addVenue: function (event) {
               
                var formData = jQuery('body #submit-venue-form').serialize();
                var venue_description = tinyMCE.get('venue_description').getContent();
                var fd = new FormData();
                console.log(jQuery('#venue_logo')[0]);
                if(jQuery('#venue_logo')[0] !== undefined){
                    fd.append("venue_logo", jQuery('#venue_logo')[0].files[0]);
                }
               
                fd.append("action", 'add_venue');
                fd.append("form_data", formData);
                fd.append("venue_description", venue_description);

                jQuery.ajax({
                    url: wp_event_manager_event_submission.ajax_url,
                    type: 'POST',
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function (responce) {
                        if (responce.code == 200) {
                            jQuery('select#event_venue_ids').append('<option selected="selected" value="' + responce.venue.venue_id + '">' + responce.venue.venue_name + '</option>');
                            jQuery('#event_venue_ids').trigger("chosen:updated");
                            jQuery('body #submit-venue-form .event-manager-uploaded-files').html("");
                            jQuery('body #submit-venue-form')[0].reset();
                            jQuery('.wpem_add_venue').css('pointer-events', 'auto');
                            jQuery('.wpem_add_venue').css('pointer-events', 'none');
                            jQuery('#wpem_add_venue_popup .wpem-modal-header-close .wpem-modal-close').trigger("click");
                            jQuery('body #submit-venue-form .wpem-form-footer .wpem-alert-danger').remove();
                        } else {
                            jQuery('#venue_message').html(responce.message);
                            jQuery('.wpem_add_venue').css('pointer-events', 'auto');
                        }
                    }
                });
            },
        } //end of action
    } //enf of return
}; //end of class
EventSubmission = EventSubmission();
jQuery(document).ready(function ($) {
    EventSubmission.init();
});