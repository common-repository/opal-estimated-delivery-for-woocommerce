jQuery( function( $ ) {

	// class OEDW_Backend_Handler {
	// 	constructor() {
	
	// 	}
	// }

	function clear_form_elements($selector) {
		$selector.each(function(i) {
			$(this).off();
			switch(this.type) {
				case 'select-one':
					$(this).val($(this).find('option:first-child').val());
					break;
				case 'password':
				case 'text':
				case 'textarea':
				case 'file':
				case 'date':
				case 'number':
				case 'tel':
				case 'email':
					$(this).val('');
					break;
				case 'checkbox':
				case 'radio':
					this.checked = false;
					$(this).change();
					break;
			}
			$(this).change();
		});
	}

	function oedw_toggle_field_conditon($selector = false) {
		var $selector = !$selector ? $('.oedw_setting_field[type="checkbox"]') : $selector;
		$selector.on('change', function(e) {
			e.preventDefault();
			var curCheck = $(this).is(":checked"),
				par = $(this).closest('.options_group_condition'),
				fieldCondition = par.next('.oedw_group_settings_mt.oedw_group_settings_condition');
			if (fieldCondition.length) {
				fieldCondition.each(function() {
					if ($(this).hasClass('toggle_hidden')) {
						if (curCheck) {
							$(this).slideDown();
						} 
						else {
							$(this).slideUp();
						}
					}
					else {
						$(this).toggleClass('setting_hidden');
					}
				})
			}
		});
	}

	function oedw_init_flatpickr($selector = false) {
		var $selector = !$selector ? $('.oedw_datetime_picker') : $selector;
		
		if($selector.length) {
			$selector.each(function() {
				let	$type = $(this).attr('type'),
					$mode = $(this).attr('mode'),
					$date_format = $(this).attr('date-format'),
					flatpickrConfig = {};
		
				var tomorrow = new Date();
				tomorrow.setDate(tomorrow.getDate() + 1);
				switch ($type) {
					case 'datetime-local':
						flatpickrConfig = {
							enableTime: true,
							altInput: true,
							altFormat: $date_format+" H:i",
							dateFormat: "Y-m-d H:i",
							// minDate: tomorrow
						};
						break;
					case 'time':
						flatpickrConfig = {
							enableTime: true,
							noCalendar: true,
							dateFormat: "H:i",
						};
						break;
					default:
						flatpickrConfig = {
							altInput: true,
							altFormat: $date_format,
							dateFormat: 'Y-m-d',
							// minDate: tomorrow
						};
						break;
				}

				if($mode && $mode == 'multiple') flatpickrConfig['mode'] = 'multiple';
		
				$(this).flatpickr(flatpickrConfig);
			})
		}
	}
	
	function oedw_save_settings() {
		if (!$('#oedw_submit_settings').length) return false;

		$('#oedw_submit_settings').on('click', function(e) {
			e.preventDefault();
			$(this).addClass('loading');

			var data = {};
			data['action'] = 'oedw_handle_settings_form';
			data['ajax_nonce_parameter'] = oedw_script.security_nonce;
			$('.oedw_g_set_tabcontents .oedw_setting_field').each(function() {
				if ($(this).attr('type') == 'checkbox' && !$(this).is(":checked")) {
					data[$(this).attr('name')] = 0;	
				}
				else {
					data[$(this).attr('name')] = $(this).val();
				}
			});

			$.ajax({ 
				url: oedw_script.ajaxurl,
				type: "post", 
				dataType: 'json', 
				data: data, 
				success: function(data) { 
					$.toast({
						heading: 'Success',
						text: data.data.message,
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right',
						hideAfter: 6000
					})
					
				}, 
				error: function() { 
					alert("An error occured, please try again.");          
				} 
			});   
		});
	}

	function oedw_init_select2_settings($selector = false) {
		var $selector = !$selector ? $('.oedw_init_select2') : $selector;

		if (!$selector.length) return false;
		
		$selector.each(function() {
			var optionSelect2;
			if ($(this).hasClass('oedw_rules_apply')) {
				optionSelect2 = {
					ajax: {
						url: oedw_script.ajaxurl,
						dataType: 'json',
						delay: 250,
						data: function (params) {
							var term = this.closest('.oedw_wrapper_rules_apply').parent().find('.oedw_rule_apply_for').val();
							return {
								term: term,
								q: params.term, // search query
								ajax_nonce_parameter: oedw_script.security_nonce,
								action: 'oedw_load_rule_apply_ajax'
							};
						},
						processResults: function( data ) {
							var options = [];
							if ( data ) {
								$.each( data, function( index, text ) {
									options.push( { id: text[0], text: text[1]  } );
								});
							}
							return {
								results: options
							};
						},
						cache: true
					},
					minimumInputLength: 1,
					// placeholder: function(){
					// 	$(this).data('placeholder');
					// },
				};
			}

			// Init select2
			$(this).select2(optionSelect2);
		})
	}
	
	function oedw_change_rule_apply_for($selector = false) {
		var $selector = !$selector ? $('.oedw_rule_apply_for') : $selector;

		if (!$selector.length) return false;
		if (!$('.oedw_rule_apply_for').length) return false;

		$('.oedw_rule_apply_for').on('change', function(e) {
			e.preventDefault();
			var rule = $(this).val(),
				applyField = $(this).closest('.oedw_setting_form').next('.oedw_wrapper_rules_apply');
			if ($.inArray(rule, ['all', 'instock', 'outofstock', 'onbackorder']) === -1) {
				if (applyField.hasClass('oedw_hidden')) applyField.removeClass('oedw_hidden');
				applyField.find('.oedw_rules_apply').val(null).trigger('change');
			}
			else {
				applyField.addClass('oedw_hidden');
			}
		})
	}

	function oedw_init_repeater_rules() {
		if (!$('#oedw_rules_settings .oedw_wrapper_rules').length) return false;

		$('#oedw_rules_settings .oedw_wrapper_rules').oedwRepeater({
			btnAddClass: 'rpt_btn_add',
			btnRemoveClass: 'rpt_btn_remove',
			groupClass: 'oedw_rules_box',
			minItems: 1,
			maxItems: 0,
			startingIndex: $('.oedw_rules_box').length - 1,
			showMinItemsOnLoad: true,
			reindexOnDelete: true,
			repeatMode: 'insertAfterLast',
			animation: 'fade',
			animationSpeed: 400,
			animationEasing: 'swing',
			clearValues: true,
			afterAdd: function($item) {
				if (!$item.hasClass('added')) {
					// Clear new field
					clear_form_elements($item.find('.oedw_setting_field'));

					// Reinit toggle condition
					oedw_toggle_field_conditon($item.find('.oedw_setting_field[type="checkbox"]'));

					// Reinit flatpickr
					oedw_init_flatpickr($item.find('.oedw_datetime_picker'));

					// Reinit select2
					$item.find('.oedw_rules_apply').empty();
					$item.find('.oedw_init_select2').val('');
					oedw_init_select2_settings($item.find('.oedw_init_select2'));

					// Recatch event change
					oedw_change_rule_apply_for($item.find('.oedw_rule_apply_for'));

					// Trigger change
					$item.find('.oedw_setting_field').change();
				}
				
				//afterAdded
				$item.addClass('added');
			},
		});
	}

    $(document).ready(function($) {
		// Setting page
		$( '.oedw_wrap_settings .oedw_g_set_tabs li a' ).on( 'click', function(e) {
			// e.preventDefault();
			$( '.oedw_wrap_settings .oedw_g_set_tabs li a' ).removeClass('active');
			$(this).addClass('active');
			$('.oedw_tabcontent').hide();
			$($(this).attr('href')).show();
		})

		oedw_init_repeater_rules();
		oedw_toggle_field_conditon();
		oedw_init_flatpickr();
		oedw_save_settings();
		oedw_init_select2_settings();
		oedw_change_rule_apply_for();

    });

});