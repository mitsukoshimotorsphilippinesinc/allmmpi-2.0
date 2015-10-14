/*
 * Vital-C javascript 
 *
 */

(function() {
	
	/*
	 * Initialize the beyond object
	 */
	var root = this;
	
	var member = {};
	
	var vitalc = {};
	
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
			exports = module.exports = vitalc;
		}
		exports.vitalc = vitalc;
		exports.vc = vitalc;
	} else {
		root['vitalc'] = vitalc;
		root['vc'] = vitalc;
	}
	
	vitalc.member = {
		member_id : "",
		first_name : "",
		last_name: "",
		member_status_id : 0,
		funds: 0,
		gift_cheques: 0,
		selected_account_id: 0
	};

	/***************************************************************************/
	// C Points
	vitalc.cpoints = {};
	vitalc.cpoints.converter = function(admin, member_id) {
		conv_data = (admin)?{member_id: member_id}:{};
		console.log(conv_data);
		admin_url = (admin)?'/admin':'';
		beyond.request({
			url: admin_url + '/cpoints/converter_modal',
			data: conv_data,
			on_success: function(data){
				if(data.status) {
					var check_amount = function(type_max_amount) {
						$('.converted-amount').val(0);

						$('.converter-amount').val($('.converter-amount').val() * 1);
						var type_max_amount = data.data.member_details[$('.converter-convert-type').val().toLowerCase()];
						if($('.converter-amount').val() > type_max_amount) {
							$('.alert-converter-error').text('Amount specified is greater than the available.').show();
							return false;
						}

						var ratios = {};
						for(i in data.data.ratios) ratios[data.data.ratios[i].from_type] = data.data.ratios[i];

						$('.converted-amount').val($('.converter-amount').val() * ratios[$('.converter-convert-type').val()].cpoint_ratio);
						return true;
					};

					var converter_modal = beyond.modal.create({
						title: 'C Points Converter',
						html: data.data.html,
						buttons: {
							'Convert': function(){
								if(check_amount) {
									var confirm_modal = beyond.modal.create({
										title: 'C Points Converter',
										html: 'Are you sure you want to convert ' + $('.converter-amount').val() +' ' + $('.converter-convert-type').val().replace(/_/i," ") + ' to C POINTS?',
										disableClose: true,
										buttons: {
											'Yes Convert': function(){
												beyond.request({
													url: admin_url + '/cpoints/convert',
													data: {
														type: $('.converter-convert-type').val(),
														amount: $('.converter-amount').val(),
														member_id: member_id
													},
													on_success: function(data){
														if(data.status) {
															var success_modal = beyond.modal.create({
																title: 'C Points Converter',
																html: 'Converting was successful',
																disableClose: true,
																buttons: {
																	'Ok': function(){
																		window.location = (admin)?"/network/members/earnings/" + member_id:"/members";
																	}
																}
															});
															success_modal.show();
														} else {
															var error_modal = beyond.modal.create({
																title: 'C Points Converter',
																html: data.msg
															});
															error_modal.show();
														}
													}
												});
												confirm_modal.hide();
											},
											'Cancel': function(){
												confirm_modal.hide();
											}
										}
									});
									confirm_modal.show();
								}
							}
						}
					});
					converter_modal.show();

					$('.converter-amount').blur(function(){
						$('.alert-converter-error').hide();
						check_amount();
					});

					$('.converter-convert-type').change(function(){
						$('.alert-converter-error').hide();
						check_amount();
					});
				}
			}
		});
	};
	
	/***************************************************************************/
	// Member > Repeat Sales
	vitalc.encodeSales = {};
	vitalc.encodeSales.initialize = function(){
	
	//var _htmlBody = "";
	
	root['errorModal'] = b.modal.create({
		title: 'Get Account Points :: Error',
		disableClose: true,
		html: "",
		width: 400,
		buttons: {
			'Close' : function() {
				errorModal.hide();
			}
		}
	});
	

		//$(".account_selector").on("click",function(e){
		//	
		//	e.preventDefault();
		//	
		//	var _account_id = $(this).attr('data-id');
        //
	    //    b.request({
	    //        url: '/members/select_account',
	    //        data: {'account_id' : _account_id},
	    //        on_success: function(data, status) {
	    //            // do a page refresh
	    //            $("#selected_account").html(_account_id);
	    //            location.reload();
	    //        }
	    //    });
	    //    return false;
	    //});
		
		$("#card_id").keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
		
		$('#btn_submit').on("click",function(e) {	
			e.preventDefault();
			vitalc.encodeSales.encode();
			return false;
		});
		
		$('#account_id').on("blur",function(e) {	
			e.preventDefault();
			vitalc.encodeSales.getAccountPoints();
			return false;
		});
	
	};
	
	
	vitalc.encodeSales.getAccountPoints = function() {
		var _account_id = $('#account_id');
		
		b.request({
            url : '/admin/encode_sales/get_account_points',
            data : {
                '_account_id' : _account_id.val()
            },
            on_success : function(data) {
                if (data.status == "1")	{
                    $("#account_details_container").html();
                    $("#account_details_container").html(data.html);
        
                } else {      
					$("#account_details_container").html();
                    $("#account_details_container").html(data.html);
					//_htmlBody = data.html;					
					if ($('#' + errorModal.id).length == 0) {							
						errorModal.show();
						$('#' + errorModal.id+ ' .modal-body').html(data.message); 
					} 
                }
            } // end on_success
        });
		
		
	};
	
	vitalc.encodeSales.checkIfRSCard = function(card_id, card_code, callback) {	
		var m_result = {is_valid_rs: 0, message: '',error_in: ''};
		b.request({
			url : '/admin/encode_sales/check_rs',
			data : {
				'_card_id' : card_id,	
				'_card_code' : card_code				
			},
			
			on_success : function(data) {
				if (data.status == 1){
					m_result.is_valid_rs = 1;
		            m_result.message = "Valid Card";
				} else {
					m_result.is_valid_rs = 0;
		            m_result.message = data.message;
					m_result.error_in = data.error_in;
				}
				if(_.isFunction(callback)) callback.call(this,m_result);
			} // end on_success			
		});			
	};
	
	vitalc.encodeSales.checkIfVAlidAccountID = function(account_id, callback) {	
		var m_result = {is_valid_account: 0, message: '', account_status_id: ''};
		b.request({
			url : '/admin/encode_sales/check_account_id',
			data : {
				'account_id' : account_id				
			},
			
			on_success : function(data) {
				if (data.status == 1){
					m_result.is_valid_account = 1;
		            m_result.message = "Valid Account";
				} else {
					m_result.is_valid_account = 0;
		            m_result.message = data.message;
					m_result.account_status_id = data.account_status_id;
				}
				if(_.isFunction(callback)) callback.call(this,m_result);
			} // end on_success			
		});	
	};
	
	vitalc.encodeSales.encode = function(){
		var _account_id = $('#account_id');
		var _position = $('#position');
		var _card_code = $('#card_code');
		var _card_id = $('#card_id');
		var _maintenance_period = $('#maintenance_period');
		var hasError = false;
		
		if ($.trim(_account_id.val()) == '') {
        	hasError = true;
			vitalc.encodeSales.setFormControlErrorMsg(_account_id,"Account ID is required",0);
        } else {
			vitalc.encodeSales.resetFormControl(_account_id);
		}

		if ($.trim(_card_code.val()) == '') {
        	hasError = true;
			vitalc.encodeSales.setFormControlErrorMsg(_card_code,"Card Code is required",0);
        } else {
			vitalc.encodeSales.resetFormControl(_card_code);
		}

		if ($.trim(_card_id.val()) == '') {
        	hasError = true;
			vitalc.encodeSales.setFormControlErrorMsg(_card_id,"Card ID is required",0);
        } else {
			vitalc.encodeSales.resetFormControl(_card_id);
		}

		vitalc.encodeSales.checkIfVAlidAccountID($.trim(_account_id.val()),function(var_account) {	
		
			if (var_account.is_valid_account == 0) {	
				hasError = true;														
				vitalc.encodeSales.setFormControlErrorMsg(_account_id, var_account.message,0);		
			} else {
				vitalc.encodeSales.resetFormControl(_account_id);
			}			
		});
		
		vitalc.encodeSales.checkIfRSCard($.trim(_card_id.val()), $.trim(_card_code.val()),function(var_rscard){	
			if (hasError == false) {			
				
				if (var_rscard.is_valid_rs == 0) {	
					hasError = true;												
					if (var_rscard.error_in == "card") {			
						vitalc.encodeSales.setFormControlErrorMsg(_card_id, var_rscard.message,0);
					} else {
						vitalc.encodeSales.setFormControlErrorMsg(_card_code, var_rscard.message,0);
					}			
				}				
			}
			
			if ($.trim(_position.val()) == '') {
	        	hasError = true;
				vitalc.encodeSales.setFormControlErrorMsg(_position,"Position is required",0);
	        } else {
				vitalc.encodeSales.resetFormControl(_position);
			}

			if ($.trim(_maintenance_period.val()) == '') {
	        	hasError = true;
				vitalc.encodeSales.setFormControlErrorMsg(_maintenance_period,"Maintenance Period is required",0);
	        } else {
				vitalc.encodeSales.resetFormControl(_maintenance_period);
			}

			//if (vitalc.member.selected_account_id == '') {
	        //	hasError = true;
			//	vitalc.encodeSales.setFormControlErrorMsg(vitalc.member.selected_account_id,"Account ID is required",1);
			//}

			// check if account_id is valid (cm_member_accounts where account_status_id <> 2)
			
			if (hasError == false) {

					// confirmation
				beyond.request({
					url : '/admin/encode_sales/confirm_credit',
					data : {
						'_position' : _position.val(),
						'_card_code' : _card_code.val(),
						'_card_id' : _card_id.val(),
						'_maintenance_period' : _maintenance_period.val(),
                        '_account_id' : _account_id.val()
					},				

					on_success : function(data) {
						if (data.status == "1")	{

							var confirmModal = b.modal.create({
								title: 'Card Code Verification :: Confirm',
								disableClose: true,
								html: data.html,
								buttons: {							
									'Yes' : function() {
										// credit card points
                                        vitalc.encodeSales.creditCardPoints(vitalc.member.member_id, _account_id.val(), _card_id.val(), _position.val(), _card_code.val(), _maintenance_period.val());
										confirmModal.hide();
									},
									'No' : function() {
										confirmModal.hide();
									}
								}
							});
							confirmModal.show();

						} else {
							var errorConfirmModal = b.modal.create({
								title: 'Card Code Verification :: Error',
								disableClose: true,
								html: data.html,
								buttons: {
									'Ok' : function() {
										errorConfirmModal.hide();
									}
								}
							});
							errorConfirmModal.show();
						}
					} // end on_success
				})				
				return false;
			}

		});
	};
	
	vitalc.encodeSales.setFormControlErrorMsg = function(elem,msg,is_child) {
		if (is_child == 1) {
			//elem.parent().parent().addClass("error");
			//elem.parent().parent().find(".help-inline").remove();			
			//elem.parent().append("<span class='help-inline'>" + msg + "</span>");			
		} else {
			elem.parent().addClass("error");
			elem.parent().find(".help-inline").remove();			
			elem.parent().append("<span class='help-inline'>" + msg + "</span>");
		}	
	};
	
	vitalc.encodeSales.resetFormControl = function(elem) {
		elem.parent().removeClass("error");
		elem.parent().find(".help-inline").remove();			
		elem.parent().append("<span class='help-inline'></span>");		
	};
	
	vitalc.encodeSales.creditCardPoints = function(member_id, account_id, card_id, position, card_code, maintenance_period) {
		beyond.request({
			url : '/admin/encode_sales/credit_points',
			data : {
				'_member_id' : member_id,
				'_account_id' : account_id,
				'_card_id' : card_id,
				'_position' : position,
				'_card_code' : card_code,
				'_maintenance_period' : maintenance_period				
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var creditPointsModal = b.modal.create({
						title: 'Add New Card Series :: Successful',
						disableClose: true,
						html: data.html,
						buttons: {						
							'Close' : function() {
								creditPointsModal.hide();
								redirect("admin/encode_sales");
							}
						}
					});
					creditPointsModal.show();
					
				} else {
					var errorCreditPointsModal = b.modal.create({
						title: 'Card Code Verification :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorCreditPointsModal.hide();
							}
						}
					});
					errorCreditPointsModal.show();
				}
			} // end on_success
		})
	};
	/***************************************************************************/
	
	
	/***************************************************************************
	 * Raffle Management
	 ***************************************************************************/
	
	vitalc.rm = {};
	vitalc.rm.raffles = {}; // will hold the raffles information
	
	vitalc.rm.get = function() {
		
	};
	
	vitalc.rm.render = function(el) {
		
	};
	
	vitalc.rm.check = function() {
		
	};
	
	vitalc.rm.draw = function() {
		
	};
	
	vitalc.rm.completed = function() {
		
	};
	
	
}).call(this);

/*************************************************
 * important to be executed before hand
 */
$(document).ready(function() {
	$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });
	$('body').on('touchstart.dropdown', '.dropdown-submenu', function (e) { e.stopPropagation(); });
	
	//fix navbar css
	var subnav_height = $("#header.navbar .nav").height();
	var body_pad = (subnav_height + $('.navbar-fixed-top .navbar-inner .container').height() + 14);
	$("body").css("padding-top", body_pad); $(".subnav").css('height',subnav_height);
    $(window).resize(function(){ //fix navbar on resize
		subnav_height = $("#header.navbar .nav").height();
		body_pad = (subnav_height + $('.navbar-fixed-top .navbar-inner .container').height() + 14);
		$("body").css("padding-top", body_pad); $(".subnav").css('height',subnav_height);
     });
});


