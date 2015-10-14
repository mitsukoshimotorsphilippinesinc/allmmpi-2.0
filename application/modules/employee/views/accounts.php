<?php
	echo css('genealogy.css'); 
	echo js('apps/genealogy.js');
	echo js('libs/jquery.highlight.js'); 
	
	
	

	$selected_account = $this->session->userdata('selected_account');
    $selected_tab = $this->session->userdata('selected_tab');
	if($selected_account==FALSE)
	{
		$selected_account = $this->members_model->get_member_accounts("member_id = {$this->member->member_id}",NULL,"insert_timestamp ASC");			
		$selected_account = $selected_account[0];
	}

	$selected_account_id = $selected_account->account_id;
	$current_account = $this->members_model->get_member_account_by_account_id($selected_account->account_id);
	$account_status = $this->members_model->get_member_account_status_by_id($selected_account->account_status_id);
	$status = $account_status->account_status;
	
    // get select account upgrades
    $account_upgrades = $this->members_model->get_member_account_upgrades(array(
        'base_account_id' => $selected_account_id
    ));

    echo $genealogy_css;
?>



<style>
div.node-content .upgrade-two {
    background-color: #0000FF;
}
div.node-content .upgrade-one {
    background-color: #FFFF00;
}
div.node-content .erhm-level {
    background-color: #00FF00;
}
</style>
<div class="page-header clearfix">
	<div>
		<?php
			if($status == 'ACTIVE')
				$status = "<label style='display: inline;' class='label label-success'>{$status}</label>";
			elseif($status == 'INACTIVE')
				$status = "<label style='display: inline;' class='label label-important'>{$status}</label>";
			elseif($status == 'COMPANY')
				$status = "<label style='display: inline;'class='label label-success'>{$status}</label>";

		?>
		<h2 class='pull-left'>
            My Account <small id="header_account_id"></small>&nbsp;<span style="margin:1px;"><?=$status;?></span>
            <div>
                <button class="btn btn-primary btn-small btn-upgrade-account" data-member-account-id="<?=$selected_account_id?>"><i class="icon-arrow-up icon-white"></i> Upgrade Account</button>
				<?php
					
					if ($current_account->sms_notification == 0) {
						echo "<button class='btn btn-danger btn-small btn-sms-notification' data='{$current_account->sms_notification}'><i class='icon-ok-circle icon-white'></i>Enable Sms Notification</button>";
					} else {
						echo "<button class='btn btn-success btn-small btn-sms-notification' data='{$current_account->sms_notification}'><i class='icon-remove-circle icon-white'></i> Disable Sms Notification</button>";
					}					
                ?>
				<?php foreach($account_upgrades as $upgrade) : ?>
                <label style='display: inline;' class='label label-success'><?= $card_types[$upgrade->upgrade_type] ?></label>
                <?php endforeach; ?>
            </div>
        </h2>
	</div>
	<div class="control-group section-control-group pull-right">
			<label class="control-label">Select Account:</label>
			<?= $this->load->view('account/switcher', null, TRUE, 'members'); ?>
	</div>
</div>

<div class='ui-element'>

	
<?php if (!empty($accounts)): ?>
	
	<div id="account_tab_html">
		
	</div>
<?php endif; ?>
</div>
<script type="text/javascript">
	
	var newAccountModal;
	var account_id = '<?=$selected_account_id?>';
    var current_tab = "<?=$selected_tab?>";
	var member_id = <?=$this->member->member_id?>;
	var account = <?= json_encode($selected_account);?>;
    var hasError = false;


	/* functions */

    var bindRefreshBlock = function(){
        window.onbeforeunload = function(e) {
            return 'Your new account is still being processed. The processing will continue even if you are to leave this page, but please note that the updated results will only be reflected after the process is completed.';
        };
    };
	
	var submitAccountDetails = function(callback) {

        var _account_id = $("#account_id");
        var _card_code = $('#card_code');
        var _sponsor_id = $('#sponsor_id');
        var _upline_id = $('#upline_id');
        var _position = $('#position');

        var _account_id_error = $("#account_id_error");
        var _card_code_error = $('#card_code_error');
        var _sponsor_id_error = $('#sponsor_id_error');
        var _upline_id_error = $('#upline_id_error');
        var _position_error = $('#position_error');

        hasError = false;

        // check upline
        <?php /* NOTE: this is calling checkUplineOrSponsor on members/views/account/dashboard.php */ ?>
        checkUplineOrSponsor(_upline_id.val(), "upline", function(var_upline){
            if (var_upline.message != "")	{
                hasError = true;
                <?php /* NOTE: this is calling setFormControlErrorMsg on members/views/account/dashboard.php */ ?>
                setFormControlErrorMsg(_upline_id, var_upline.message,_upline_id_error);
            } else {
                checkPosition(var_upline.available_side, _position.val(), function(_positionMessage) {
                    if (_positionMessage != "OK")	{
                        hasError = true;
                        setFormControlErrorMsg(_position, _positionMessage,_position_error);
                    }
                });
            }

            if(!hasError) {
                if ($.trim(_account_id.val()) == '') {
                    hasError = true;
                    setFormControlErrorMsg(_account_id,"Account ID is required",_account_id_error);
                } else {
                    resetFormControl(_account_id,_account_id_error);
                }
            }
            
            if(!hasError) {
                if ($.trim(_card_code.val()) == '') {
                    hasError = true;
                    setFormControlErrorMsg(_card_code,"Card Code is required",_card_code_error);
                } else {
                    resetFormControl(_card_code,_card_code_error);
                }
            }

            if(!hasError) {
                if ($.trim(_sponsor_id.val()) == '') {
                    hasError = true;
                    setFormControlErrorMsg(_sponsor_id,"Sponsor ID is required",_sponsor_id_error);
                } else {
                    resetFormControl(_sponsor_id,_sponsor_id_error);
                }
            }
            
            if(!hasError) {
                if ($.trim(_upline_id.val()) == '') {
                    hasError = true;
                    setFormControlErrorMsg(_upline_id,"Upline ID is required",_upline_id_error);
                } else {
                    resetFormControl(_upline_id,_upline_id_error);
                }
            }
            
            if (hasError == false) {
                confirmAddAccount(_account_id.val(), _card_code.val(), _sponsor_id.val(), _upline_id.val(), _position.val());
            }

            if(_.isFunction(callback)) callback.call(this);
        });
    }


    var confirmAddAccount = function(account_id, card_code, sponsor_id, upline_id, position)  {
        b.request({
            url : '/members/account/confirm_add',
            data : {
                '_account_id' : account_id,
                '_card_code' : card_code,
                '_sponsor_id' : sponsor_id,
                '_upline_id' : upline_id,
                '_position' : position
            },
            on_success : function(data) {

                var newAccountModal = b.modal.new({
                    title: 'Add New Account :: Confirm',
                    disableClose: true,
                    width :400,
                    html: data.html,
                    buttons: {
                        'Ok' : function() {
                            newAccountModal.hide();
                            addNewAccount(account_id, card_code, sponsor_id, upline_id, position);
                        },
                        'Cancel' : function() {
                            newAccountModal.hide();
                        }
                    }
                });
                newAccountModal.show();

            }
        });

    };

    var addNewAccount = function(account_id, card_code, sponsor_id, upline_id, position) {
        b.request({
            url : '/members/account/add_account',
            data : {
                '_account_id' : account_id,
                '_card_code' : card_code,
                '_sponsor_id' : sponsor_id,
                '_upline_id' : upline_id,
                '_position' : position
            },
            on_success : function(data) {
                if (data.status == "1")	{

                    var newAccountModal = b.modal.new({
                        title: 'Add New Account :: Successful',
                        disableClose: true,
                        html: data.html,
                        buttons: {
                            'Ok' : function() {
                                newAccountModal.hide();
                                redirect('/members/accounts');

                            }
                        }
                    });
                    newAccountModal.show();

                } else {

                    var newAccountErrorModal = b.modal.new({
                        title: 'Add New Account :: Error',
                        disableClose: true,
                        html: data.html,
                        buttons: {
                            'Close' : function() {
                                newAccountErrorModal.hide();
                            }
                        }
                    });
                    newAccountErrorModal.show();

                }
            }
        });
    };

    var checkUplineOrSponsor = function(id_upline_sponsor, check_by, callback) {
        var m_result = {id: '', available_side: 0, is_valid_sponsor: false, message: ''};

        m_result.id = id_upline_sponsor;

        b.request({
            url : '/members/account/check_id',
            data : {
                '_id' : id_upline_sponsor,
                '_check_by' : check_by
            },
            on_success : function(data) {
                if (data.status == "1")	{

                    if (check_by == "upline") {
                        m_result.available_side = data.available_side;
                    }

                    if (check_by == "sponsor") {
                        m_result.is_valid_sponsor = data.is_valid_sponsor;
                    }

                } else {
                    m_result.message = data.html;
                }

                if(_.isFunction(callback)) callback.call(this,m_result);
            }
        })
    };


    var checkPosition = function(available_position, position, callback) {
        //alert(available_position + "|" + position);
        var _message = "";

        switch (available_position) {

            case '0' :
                _message = "No more available slot for Upline ID: " + $("#upline_id").val();
                break;
            case '1' :
                // check if position - right
                if (position == "right") {
                    _message = "OK";
                } else {
                    _message = "Left Slot is not available for Upline ID: " + $("#upline_id").val();
                }
                break;
            case '2' :
                // check if position - left
                if (position == "left") {
                    _message = "OK";
                } else {
                    _message = "Right Slot is not available for Upline ID: " + $("#upline_id").val();
                }
                break;
            case '3' :
                _message = "OK";
                break;
        }
        if(_.isFunction(callback)) callback.call(this,_message);

    };


	var upperCaseWords = function(str) {	    
		return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
	        return $1.toUpperCase();
	    });
	};


    var setFormControlErrorMsg = function(elem,msg,elem_error) {
        elem.parent().addClass("error");
        elem.parent().find(".help-inline").remove();
        //elem.parent().append("<span class='help-inline'>" + msg + "</span>");
        elem_error.html(msg);
    };

    var resetFormControl = function(elem,elem_error) {
        elem.parent().removeClass("error");
        elem.parent().find(".help-inline").remove();
        //elem.parent().append("<span class='help-inline'></span>");
        elem_error.html("");
    };


	var displayAccount = function(){
        showLoading();
		b.request({
			url: "/members/accounts/get_account_html",
			data: {
				"member_id": member_id,
				"account_id": account_id
			},
			on_success: function(data){
				$("#account_tab_html").html(data.data.html);
                $("#header_account_id").html(" - " + account_id);
                hideLoading();
                $(".tabbable .nav-tabs li a[href='#"+current_tab+"']").click();
			},
			on_error: function(){
                hideLoading();
			}
		});
	};

	var displayEarnings = function() {
        showLoading();
		b.request({
			url: "earnings/get_account_earnings",
			data: {
				"start_date": "<?= date('Y-m-d',strtotime($selected_account->insert_timestamp)) ?>",
				"end_date": "<?= date('Y-m-d') ?>",
				"member_id": member_id,
				"account_id" : account_id
			},
			on_success: function(data){
                if(data.status == "ok")
				{
					$("#igpsm_earnings").html(data.data.igpsm_earnings);

					$("#unilevel_earnings").html(data.data.unilevel_earnings);	
					$("#weekly_igpsm").html(data.data.weekly_igpsm);
					$("#monthly_unilevel").html(data.data.monthly_unilevel);
				}
                hideLoading(); 
			}
		});		
	};

	var displayHistory = function(start_date, end_date, type) {
        showLoading();
		
		b.request({
			url: "/members/earnings/get_account_history",
			data: {
				"account_id": account_id,
				"member_id": member_id,
				"start_date": start_date,
				"end_date": end_date,
                "type": type
			},
			on_success: function(data){
				$("#account_history").html(data.data.html);
                hideLoading();
			},
			on_error: function(){

			}
		});		
	};

	var displayAddAccount = function() {
		b.request({
			url: "/members/accounts/add",
			data: {				
				"_member_id":member_id,
                "_is_modal": 0
			},
			on_success: function(data){
				//$("#earnings").hide();
				$("#add_account_container").html(data.data.html);
				if(data.status == "error")
				{

				}
			},
			on_error: function(){
           	}
		});		
	};
	
	var sendPrivateMessage = function(email_to, email_from, recipient_name, sender_name, message, mobile_number_from)  {

        b.request({
            url : '/members/accounts/send_message',
            data : {
                '_email_to' : email_to,
                '_email_from' : email_from,
                '_recipient_name' : recipient_name,
                '_sender_name' : sender_name,
                '_message' : message,
                '_mobile_number_from' : mobile_number_from
            },
            on_success : function(data) {

                privateMessageSuccessModal = b.modal.new({
                    title: 'send Private Message :: Successful',
                    disableClose: true,
                    width :400,
                    html: data.data.html,
                    buttons: {
                        'Ok' : function() {
                            privateMessageSuccessModal.hide();

                        }
                    }
                });
                privateMessageSuccessModal.show();

            }
        });

    };

	var getUnilevel = function(page) {
		page = typeof(page) == 'undefined' ? 1 : page;
		
		b.request({
			url: "/members/genealogy/get_unilevel/"+page,
			data: {				
				"account_id":account_id
			},
			on_success: function(data){
				$("#unilevel_genealogy").html(data.data.html + data.data.pagination);
			},
			on_error: function(){
           	}
		});
		
	};

	/* end functions */


	$(document).ready(function(){
		displayAccount();

		$(document).on("change",'#start_date_month',function() {
			beyond.webcontrol.updateDateControl('start_date');
		});

		$(document).on("change",'#start_date_day',function() {
			beyond.webcontrol.updateDateControl('start_date');
		});

		$(document).on("change",'#start_date_year',function() {
			beyond.webcontrol.updateDateControl('start_date');
		});

		$('#start_date_month').trigger('change');
		$('#start_date_day').trigger('change');
		$('#start_date_year').trigger('change');

		$(document).on("change",'#end_date_month',function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		$(document).on("change",'#end_date_day',function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		$(document).on("change",'#end_date_year',function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		$('#end_date_month').trigger('change');
		$('#end_date_day').trigger('change');
		$('#end_date_year').trigger('change');

		$(document).on("change",'#history_start_date_month',function() {
			beyond.webcontrol.updateDateControl('history_start_date');
		});
		$(document).on("change",'#history_start_date_day',function() {
			beyond.webcontrol.updateDateControl('history_start_date');
		});
		$(document).on("change",'#history_start_date_year',function() {
			beyond.webcontrol.updateDateControl('history_start_date');
		});

		$('#history_start_date_month').trigger('change');
		$('#history_start_date_day').trigger('change');
		$('#history_start_date_year').trigger('change');

		$(document).on("change",'#history_end_date_month',function() {
			beyond.webcontrol.updateDateControl('history_end_date');
		});
		$(document).on("change",'#history_end_date_day',function() {
			beyond.webcontrol.updateDateControl('history_end_date');
		});
		$(document).on("change",'#history_end_date_year',function() {
			beyond.webcontrol.updateDateControl('history_end_date');
		});

		$('#history_end_date_month').trigger('change');
		$('#history_end_date_day').trigger('change');
		$('#history_end_date_year').trigger('change');
	
		$(".account_selector").live("click",function(){
			var _account_id = $(this).attr('data-id');
            var _account_tab = $('.tabbable .nav-tabs .active').attr('data');

			b.request({
				url: '/members/select_account',
				data: {
                    'account_id' : _account_id,
                    'account_tab' : _account_tab
                },
				on_success: function(data, status) {
					// do a page refresh
					location.reload();
				}
			});

			return false;
		});

		$("#submit_dates").click(function(){
			$("#submit_type").submit();
		});
		
		$("#account_earnings_button").live("click",function(e){
	        e.preventDefault();
			displayEarnings();
			return false;
		});

		$("#igpsm_genealogy_button").live("click",function(){
	        $('#add_account_container').html('');
			//$("#earnings").hide();
			genealogy.render({
				'target_id' : 'igpsm_genealogy', 
				'url' : '/members/genealogy/get_network', 
				'search_url' : '/members/genealogy/search',
				'downline_url' : '/members/genealogy/downline',
				'account_id' : account_id
			});		
			return false;
		});

		$("#igpsm_genealogy").live("on-add-new-distributor",function(e, parent_id, direction){	
	    	 var data_parent_account_id = parent_id;
		     var data_direction = direction;

			// redirect to registration
			redirect('/main/registration?upline_id=' + data_parent_account_id + '&position=' + data_direction);
	    });

	    //$(".btn-add-new-account").live("click",function(){
	    $("#igpsm_genealogy").live("on-add-new-account",function(e, parent_id, direction){
	        //var data_parent_account_id = $(this).attr('data-parent-account-id');
	        //var data_direction = $(this).attr('data-direction');
	        var data_parent_account_id = parent_id;
	        var data_direction = direction;
	        b.request({
	            url: "/members/accounts/add",
	            data: {
	                "_member_id":member_id,
	                "_is_modal": 1,
	                "_upline_id":data_parent_account_id,
	                "_position":data_direction
	            },
	            on_success: function(data){

	                if(data.status == "ok") {
	                    newAccountModal = b.modal.new({
	                        title: 'Add New Account Form',
	                        disableClose: true,
	                        width: 420,
	                        html: data.data.html,
	                        buttons: {
	                            'Submit' : function() {
	                                submitAccountDetails(function(){
                                        if (hasError == false) {
                                            newAccountModal.hide();
                                        }
                                    });                                    
	                            },
	                            'Cancel' : function() {
	                                newAccountModal.hide();
	                            }
	                        }
	                    });
	                    newAccountModal.show();
                        bindRefreshBlock();

	                } else {
						newAccountModal = b.modal.new({
	                        title: 'Error Notification',
	                        width: 300,
	                        html: data.data.html,
							disableClose: true,
							buttons: {
								Close: function(){
									newAccountModal.hide();
								}
							}
	                    });
	                    newAccountModal.show();
	                }
	            },
	            on_error: function(){
	            }
	        });

	        return false;

	    });


	    $("#igpsm_genealogy").live("on-send-private-message",function(e, member_account_id, member_fullname){

			var member_propercase = upperCaseWords(member_fullname.toLowerCase());

	        b.request({
	            url: "/members/accounts/check",
	            data: {
	                "_member_account_id":member_account_id,
	                },
	            on_success: function(data){

	                if(data.status == "ok") {
	                    privateMessageModal = b.modal.new({
	                        title: 'Send Private Message to <br/>' + member_fullname,
	                        disableClose: true,
	                        width: 350,
	                        html: data.data.html,
	                        buttons: {
	                            'Send' : function() {

	                                var _message = $.trim($('#member_message').val());
	                                privateMessageModal.hide();

	                                confirmSendMessageModal = b.modal.new({
	                                    title: 'Send Private Message :: Confirm',
	                                    width: 350,
	                                    html: '<p>Are you sure you want to send your private message to ' + member_fullname + '?</p>',
	                                    disableClose: true,
	                                    buttons: {
	                                        'Proceed' : function(){
	                                            confirmSendMessageModal.hide();
	                                            sendPrivateMessage(data.data.email_to, data.data.email_from, member_fullname, data.data.sender, _message, data.data.mobile_number_from);
	                                        }
	                                        ,
	                                        'Close' : function(){
	                                            confirmSendMessageModal.hide();
	                                        }
	                                    }
	                                });
	                                confirmSendMessageModal.show();

	                            },
	                            'Cancel' : function() {
	                                privateMessageModal.hide();
	                            }
	                        }
	                    });
	                    privateMessageModal.show();

	                } else {
	                    privateMessageErrorModal = b.modal.new({
	                        title: 'Send Private Message :: Error',
	                        width: 400,
	                        html: "<p>" + member_propercase + "'s " + data.data.html + "</p>",
	                        disableClose: true,
	                        buttons: {
	                            Close: function(){
	                                privateMessageErrorModal.hide();
	                            }
	                        }
	                    });
	                    privateMessageErrorModal.show();
	                }
	            },
	            on_error: function(){
	            }
	        });

	        return false;
	    });


		$("#igpsm_genealogy").live("on-upgrade-account",function(e, member_account_id, member_fullname){

			e.preventDefault();

            var account_upgrade_modal = beyond.modal.create({
                title: 'Account Upgrade :: ' +  member_account_id +'<br/><h4>' + member_fullname + '</h4>',
                html: _.template($('#account_upgrade_template').html(), {})
            });	
            account_upgrade_modal.show();

            $('.btn-template-upgrade-account').click(function(e){
                e.preventDefault();

                upgrade_card_trigger(member_account_id, account_upgrade_modal);
            });
	    });
		
		
	    $("#btn_cancel").live("click",function() {
	        newAccountModal.hide();
	    });


	    $('#earnings').live('click', function(e) {
	        e.preventDefault();
	        $('#add_account_container').html('');
	        //displayEarnings();
	    });




	    $("#unilevel_genealogy_button").live("click",function( e){
			e.preventDefault();

	        $('#add_account_container').html('');

			getUnilevel();

			return false;
		});

		$(document).undelegate('#unilevel_genealogy .pagination a', 'click');
		$(document).delegate('#unilevel_genealogy .pagination a', 'click', function(e) {
			e.preventDefault();

			var _href = $(this).attr('href');
			_href = _href.split("/");
			var _page = _href[_href.length-1];

			getUnilevel(_page);

		});


	    $("body").on('click', '.goto_page', function(e) {
			e.preventDefault();

	        var page = parseInt($(this).attr('page'));
	        currentPage = page;

	        b.request({
	            url: "/members/genealogy/get_unilevel",
	            data: {
	                "account_id":account_id,
	                "page":page
	            },
	            on_success: function(data){
	                $("#unilevel_genealogy").html(data.data.html + data.data.pagination);
	            },
	            on_error: function(){
	            }
	        });
	        return false;
	    });


		$("#get_history").live("click",function(e) {
			e.preventDefault();

			displayHistory();
			return false;
		});

		$("#add_account_button").live("click",function(e) {
			e.preventDefault();

			displayAddAccount();
		});

		$('.btn-sms-notification').click(function(e){
			e.preventDefault();
			var sms_notification = $(this).attr('data');
			var account_id = '<?= $selected_account_id ?>';
			
			b.request({
	            url: "/members/accounts/sms_notification",
	            data: {
	                "sms_notification":sms_notification,
					"account_id":account_id
	            },
	            on_success: function(data, status){
					if (data.status == 1) {
						var confirmNotificationModal = b.modal.new({
							title: data.data.title,
							html: data.data.html,
							disableClose: true,
							buttons: {
								'Confirm' : function() {
									// ajax request
									b.request({
										url: '/members/accounts/update_sms_notification',
										data: {
											"sms_notification":sms_notification,
											"account_id":account_id
										},

										on_success: function(data, status) {
											if (data.status == 1) {
												var okNotificationModal = b.modal.new({
													title: data.data.title,
													html: data.data.html,
													disableClose: true,
													buttons: {
														'Ok' : function() {
															okNotificationModal.hide();
															redirect('members/accounts');

														}
													}
												});
												
												okNotificationModal.show();
										
											} else {
												var errorNotificationModal = b.modal.new({
													title: data.data.title,
													html: data.data.html,
													disableClose: true,
													buttons: {
														'Close' : function() {
															errorNotificationModal.hide();

														}
													}
												});
												errorNotificationModal.show();
											}
										}
									});
									confirmNotificationModal.hide();
								},
								'Cancel' : function() {
									confirmNotificationModal.hide();
								}
							}
						});

						confirmNotificationModal.show();
					} else {
						var errorNotificationModal = b.modal.new({
							title: data.data.title,
							html: data.data.html,
							disableClose: true,
							buttons: {
								'Close' : function() {
									errorNotificationModal.hide();
								}
							}
						});
						errorNotificationModal.show();
					}
				
	            },
	            on_error: function(){
	            }
	        });
	        return false;
			
		});
		
        $('.btn-upgrade-account').click(function(e){
            e.preventDefault();
            var member_account_id = $(this).data('member-account-id');
            var account_upgrade_modal = beyond.modal.create({
                title: 'Account Upgrade',
                html: _.template($('#account_upgrade_template').html(), {})
            });
            account_upgrade_modal.show();

            $('.btn-template-upgrade-account').click(function(e){
                e.preventDefault();

                upgrade_card_trigger(member_account_id, account_upgrade_modal);
            });
        });
	});

    var bindP2PCheck = function(cb, member_account_id, account_upgrade_modal){
        var card_id = $('.account-upgrade-card-number').val();

        beyond.request({
            url: '/members/p2p/get_card_selection_products_member_encode',
            data: {
                card_id: card_id
            },
            on_success: function(data){
                if(data.status) {
                    console.log(data.data);
                    if(data.data.equipped) {
                        if(typeof cb == 'function') cb();
                    } else {
                        var selection_modal = beyond.modal.create({
                            title: 'Product Selection for '+card_id,
                            html: _.template($('#card_product_selection_template').html(), data.data),
                            buttons: {
                                'Assign to Card': function(){
                                    var total_qty = 0;
                                    $('.selected-product-qty').each(function(){
                                        total_qty += ($(this).val()*1);
                                    });
                                    if(total_qty == 0) {
                                        $('.selection-error').show().children('.msg').text("Total selected quantity may not be 0");
                                        return;
                                    }

                                    var confirm_modal = beyond.modal.create({
                                        title: 'Product Selection for '+card_id,
                                        html: 'Are you sure you want to assigned these products to this card?',
                                        disableClose: true,
                                        buttons: {
                                            'Yes': function(){
                                                confirm_modal.hide();

                                                var products = [];
                                                $('.selected-product-qty').each(function(){
                                                    if(($(this).val()*1) > 0) {
                                                        products.push({
                                                            product_id: $(this).data('pid'),
                                                            qty: ($(this).val()*1)
                                                        });
                                                    }
                                                });
                                                selection_modal.hide();

                                                beyond.request({
                                                    url: '/members/p2p/assign_products_member_encode',
                                                    data: {
                                                        card_id: card_id,
                                                        products: products
                                                    },
                                                    on_success: function(data){
                                                        var success_modal = beyond.modal.create({
                                                            title: 'Product Selection for '+card_id,
                                                            html: 'Product assignment successful!',
                                                            disableClose: true,
                                                            buttons: {
                                                                'Ok': function(){
                                                                    success_modal.hide();
                                                                    upgrade_card_trigger(member_account_id, account_upgrade_modal);
                                                                }
                                                            }
                                                        });
                                                        success_modal.show();
                                                    }
                                                });
                                            },
                                            'No': function(){
                                                confirm_modal.hide();
                                            }
                                        }
                                    });
                                    confirm_modal.show();                               
                                }
                            }
                        });
                        selection_modal.show();

                        $('.selected-product-qty').change(function(){
                            $('.selection-error').hide();
                            var product_id = $(this).data("pid");
                            var max_qty = $(this).data("maxqty");
                            $(this).val($(this).val()*1);

                            if($(this).val() > max_qty) {
                                $(this).val(0);
                                $('.selection-error').show().children('.msg').text("Selected quantity may not be greater than the available quantity");
                            }

                            var total_qty = 0;
                            $('.selected-product-qty').each(function(){
                                total_qty += ($(this).val()*1);
                            });
                            if(total_qty > 2)
                            {
                                $(this).val(0);
                                $('.selection-error').show().children('.msg').text("Total selected quantity may not exceed 2");
                            }
                        });
                    }
                } else {
                    var err_modal = beyond.modal.create({
                        title: 'P2P Card Check',
                        html: data.msg
                    });
                    err_modal.show();
                }
            }
        });
    };

    var upgrade_card_trigger = function(member_account_id, account_upgrade_modal){
        var card_number = $('.account-upgrade-card-number').val();
        var card_code = $('.account-upgrade-card-code').val();

        beyond.request({
            url: '/members/account/get_upgrade_card_details',
            data: {
                card_number: card_number,
                card_code: card_code,
                base_account_id: member_account_id
            },
            on_success: function(data){
                if(data.status) {
                    var confirm_modal = beyond.modal.create({
                        title: 'Account Upgrade',
                        html: _.template($('#account_upgrade_confirm_template').html(), {
                            account_id: member_account_id,
                            card_number: card_number,
                            card_code: card_code,
                            card_details: data.data.card_details
                        }),
                        disableClose: true,
                        buttons: {
                            'Confirm': function(){
                                confirm_modal.hide();
                                account_upgrade_modal.hide();

                                beyond.request({
                                    url: '/members/account/upgrade_account',
                                    data: {
                                        account_id: member_account_id,
                                        card_number: card_number,
                                        card_code: card_code
                                    },
                                    on_success: function(data){
                                        if(data.status) {
                                            var success_modal = beyond.modal.create({
                                                title: 'Account Upgrade',
                                                html: 'Account was upgraded successfully',
                                                disableClose: true,
                                                buttons: {
                                                    'Ok': function(){
                                                        window.location = "<?= site_url('members/accounts') ?>";
                                                    }
                                                }
                                            });
                                            success_modal.show();
                                        } else {
                                            var err_modal = beyond.modal.create({
                                                title: 'Account Upgrade',
                                                html: '<strong>Error: </strong>' + data.msg
                                            });
                                            err_modal.show();
                                        }
                                    }
                                });
                            },
                            'Cancel': function(){
                                confirm_modal.hide();
                            }
                        }
                    });

                    if(data.data.card_details.type == 'P2P')
                    {
                        bindP2PCheck(function(){
                            confirm_modal.show();
                        }, member_account_id, account_upgrade_modal);
                    }
                    else
                    {
                        confirm_modal.show();
                    }
                } else {
                    var err_modal = beyond.modal.create({
                        title: 'Account Upgrade',
                        html: '<strong>Error:</strong>' + data.msg
                    });
                    err_modal.show();
                }
            }
        });
    };

    //end of js for accounts tab	
</script>

<script type="text/template" id="account_upgrade_confirm_template">
    <div>Upgrading account <strong><%= account_id %></strong> with the following details:</div>
    <table class="table table-bordered" style="margin-top: 20px;">
        <tbody>
            <tr>
                <td style="text-align: right;">Card Number</td>
                <td><strong><%= card_number %></strong></td>
            </tr>
            <tr>
                <td style="text-align: right;">Card Code</td>
                <td><strong><%= card_code %></strong></td>
            </tr>
            <tr>
                <td style="text-align: right;">Card Type</td>
                <td><strong><%= card_details.type + ' - ' + card_details.type_name %></strong></td>
            </tr>
            <% if(card_details.type == "P2P") { %>
            <tr>
                <td style="text-align: right;">Items Stored</td>
                <td>
                    <% for(i in card_details.stored_items) { %>
                    <div><%= card_details.stored_items[i].product_name %> x<%= card_details.stored_items[i].qty %></div>
                    <% } %>
                    <% if(card_details.stored_items.length == 0) { %>
                    <div><i>No Items Stored</i></div>
                    <% } %>
                </td>
            </tr>
            <% } %>
        </tbody>
    </table>
</script>

<script type="text/template" id="account_upgrade_template">
	<div class="row-fluid form-horizontal">
        <div class="control-group">
            <label class="control-label">Card Number</label>
            <div class="controls">
                <input type="text" placeholder="65********" class="account-upgrade-card-number" />
                <!--//<button class="btn btn-primary btn-check-p2p-card" title="Check P2P"><i class="icon-tags icon-white"></i></button>//-->
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">Card Code</label>
            <div class="controls">
                <input type="text" placeholder="ABCD1234" class="account-upgrade-card-code" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
                <button class="btn btn-primary btn-template-upgrade-account">Upgrade Account</button>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="card_product_selection_template">
    <h5>Please select a total of exactly 2 quantity of any items below.</h5>
    <table class='table table-striped table-bordered'>
        <thead>
            <tr>
                <th>Product</th>
                <th>Available Qty</th>
                <th>Selected Qty</th>
            </tr>
        </thead>
        <tbody>
            <% $.each(products, function(i, v) { %>
            <tr>
                <td>
                    <div><%= v.name %></div>
                    <div>SRP <%= v.srp %></div>
                </td>
                <td><%= v.quantity %></td>
                <td><input type="text" class="input input-small selected-product-qty" data-pid="<%= v.product_id %>" data-maxqty="<%= v.quantity %>" value="0" /></td>
            </tr>
            <% }); %>
        </tbody>
    </table>
    <div class="alert alert-error hide selection-error">
        <span class="msg"></span>
    </div>
</script>
