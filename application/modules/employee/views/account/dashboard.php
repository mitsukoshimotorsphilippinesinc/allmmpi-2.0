
<div id="card_details_container" class="section-container">
	<fieldset>
		<div class="control-group">
        	<label class="control-label">Control Code: <em>*</em>&nbsp;<span id="account_id_error"></span></label>
        	<input class='input-large' type='text' id='account_id' placeholder="65********" name='account_id' maxlength='10' value=''>
        </div>

		<div class="control-group">
			<label class="control-label" for="card_code">RN: <span class='is required'><em>*</em>&nbsp;</span><span id="card_code_error"></span></label>
			<input type="text" class='input-large'  placeholder="ABCD1234" name="card_code" id="card_code" value="">
        </div>

        <div class="control-group">
            <label class="control-label" for="sponsor_id">Sponsor ID: <span class='is required'><em>*</em>&nbsp;</span><span id="sponsor_id_error"></span></label>
            <input type="text" class='input-large' style='float:left;' placeholder="65********" name="sponsor_id" id="sponsor_id" value="" maxlength='10'>
            <div style='float:left;margin:5px;cursor:pointer;' title='Check Sponsor'><button class='btn btn-success' id='check-sponsor-availability' style='margin-top:-5px;' type='button'>
                <span>Check</span></button>
            </div>
            <div class='clearfix'></div>
        </div>

        <div class="control-group">
			<label class="control-label" for="upline_id">Upline ID: <span class='is required'><em>*</em>&nbsp;</span><span id="upline_id_error"></span></label>
			<input type="text" class='input-large' style='float:left;' placeholder="65********" name="upline_id" id="upline_id" value="" maxlength='10'>
            <div style='float:left;margin:5px;cursor:pointer;' title='Check Upline'><button id='check-upline-availability' class='btn btn-success' style='margin-top:-5px;' type='button'>
                <span>Check</span></button>
            </div>
            <div class='clearfix'></div>
		</div>

		<div class="control-group">
			<label class="control-label" for="position">Position: <em>*</em><span id="position_error"></span></label>
			<select id="position">
				<option value="left">Left</option>
				<option value="right">Right</option>
			</select>
		</div>

        <?php
            if ($is_modal  == 0) {
        ?>
            <hr/>
            <a id='btn_submit' class='btn btn-medium btn-primary'><span>Submit</span></a>
            <a id='btn_clear' class='btn btn-medium'></i><span>Clear</span></a>

        <?php
            }
        ?>

	</fieldset>
</div>

<script type="text/javascript">
    var is_modal = <?= $is_modal ?>;
    var _upline_id = <?= $upline_id ?>;
    var _position = "<?= $position ?>";


		
		$(document).ready(function() {
	        if (is_modal == 1) {
	            $("#upline_id").val(_upline_id);
	            if (_position == "left") {
	                $("#position option[value='left']").attr("selected", true);
	            } else {
	                $("#position option[value='right']").attr("selected", true);
	            }
	        }
	    });

        $("#check-sponsor-availability").bind("click",function(e){
            e.preventDefault();
            checkSponsorIdAvailability(false);
            return false;
        });


        $("#check-upline-availability").bind("click",function(e){
            e.preventDefault();
            checkUplineIdAvailability(false);
            return false;
        });

		var setFormControlErrorMsg = function(elem,msg,elem_error) {
			elem.parent().addClass("error");
			elem.parent().removeClass("success");
			//elem.parent().find(".help-inline").remove();
			elem.parent().find(".check").remove();
			//elem.parent().append("<span class='help-inline'>" + msg + "</span>");
	        elem_error.html(msg);
		};

		var resetFormControl = function(elem,elem_error) {
			elem.parent().removeClass("error");
			elem.parent().find(".help-inline").remove();
			//elem.parent().append("<span class='help-inline'></span>");
	        elem_error.html("");
		};

		var checkIfSPCard = function(account_id, card_code, callback) {
			var m_result = {is_valid_sp: 0, message: ''};


			b.request({
				url : '/members/account/check_sp',
				data : {
					'_account_id' : account_id,
					'_card_code' : card_code
				},

				on_success : function(data) {
					if (data.status == 1){
						m_result.is_valid_sp = 1;
			            m_result.message = "Valid Account";
					} else {
						m_result.is_valid_sp = 0;
			            m_result.message = data.message;
					}
					if(_.isFunction(callback)) callback.call(this,m_result);
				} // end on_success
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



		$('#btn_submit').live("click",function() {
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

			var hasError = false;

			if ($.trim(_account_id.val()) == '') {
	        	hasError = true;
				setFormControlErrorMsg(_account_id,"Account ID is required",_account_id_error);
	        } else {
				resetFormControl(_account_id,_account_id_error);
			}

			if ($.trim(_card_code.val()) == '') {
	        	hasError = true;
				setFormControlErrorMsg(_card_code,"Card Code is required",_card_code_error);
	        } else {
				resetFormControl(_card_code,_card_code_error);
			}

			if ($.trim(_sponsor_id.val()) == '') {
	        	hasError = true;
				setFormControlErrorMsg(_sponsor_id,"Sponsor ID is required",_sponsor_id_error);
	        } else {
				resetFormControl(_sponsor_id,_sponsor_id_error);
			}

			if ($.trim(_upline_id.val()) == '') {
	        	hasError = true;
				setFormControlErrorMsg(_upline_id,"Upline ID is required",_upline_id_error);
	        } else {
				resetFormControl(_upline_id,_upline_id_error);
			}

			/*// if sp card
			checkIfSPCard(_account_id.val(), _card_code.val(),function(var_spcard){
				//alert(var_spcard.is_valid_sp);
				if (var_spcard.is_valid_sp == 0) {
					hasError = true;
					setFormControlErrorMsg(_account_id, var_spcard.message);
				} else {
					*/
					// check sponsor
					checkUplineOrSponsor(_sponsor_id.val(), "sponsor", function(var_sponsor){
						//alert(var_sponsor.message);
						if (var_sponsor.message != "")	{
							hasError = true;
							setFormControlErrorMsg(_sponsor_id, var_sponsor.message,_sponsor_id_error);
						}
					});

					// check upline
					checkUplineOrSponsor(_upline_id.val(), "upline", function(var_upline){
						//alert(var_sponsor.message);
						
						if (var_upline.message != "")	{
							hasError = true;
							setFormControlErrorMsg(_upline_id, var_upline.message,_upline_id_error);
						} else {

							checkPosition(var_upline.available_side, _position.val(), function(_positionMessage) {
								if (_positionMessage != "OK")	{
									hasError = true;
									setFormControlErrorMsg(_position, _positionMessage,_position_error);
								} else {

									confirmAddAccount(_account_id.val(), _card_code.val(), _sponsor_id.val(), _upline_id.val(), _position.val());

								}
							});
						}
					});
				//}
			//});

		});


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

	    var checkSponsorIdAvailability = function(from_submit, callback) {
	        var _sponsor_id = $("#sponsor_id");
	        var _upline_id = $("#upline_id");
	        var _parent = _sponsor_id.parent();
	        var _upline_parent = _upline_id.parent();
	        var sponsor_id = $.trim(_sponsor_id.val());
	        var upline_id = $.trim(_upline_id.val());
	        var is_valid_sponsor = 0;

	        b.request({
	            url: '/main/registration/check_sponsor_id',
	            data: {
	                'sponsor_id': sponsor_id,
	                'upline_id': upline_id,
	                'from_submit': from_submit
	            },

	            on_success: function(data, status) {

	                if (data.status==1) {
	                    if (from_submit == false) {
	                        var check_sponsorid_modal = b.modal.new({
	                            title: 'Sponsor Verification',
	                            width: '580px',
	                            html: data.html
	                        });
	                        check_sponsorid_modal.show();
	                    }

	                    if (data.valid_sponsor == 1) {
	                        _parent.removeClass("control-group error");
	                        _parent.addClass("control-group success");
	                        _parent.find(".check").remove();
	                        _parent.find(".required").append("<span class='check'>(Valid)</span>");
	                        $("#sponsor_id_error").html("");

	                    } else {
	                        _parent.removeClass("control-group success");
	                        _parent.addClass("control-group error");
	                        _parent.find(".check").remove();
	                        _parent.find(".required").append("<span class='check'>(Invalid)</span>");
	                        $("#sponsor_id_error").html("");
	                    }

	                    //alert("from data:" + data.valid_sponsor);
	                    is_valid_sponsor = data.valid_sponsor;

	                } else {
	                    if (data.upline_flag == 1) {
	                        _upline_parent.addClass("control-group error");
	                        _upline_parent.find(".check").remove();
	                        _upline_parent.find(".required").append("<span class='check'>(Required)</span>");
	                    }

	                    _parent.addClass("control-group error");
	                    _parent.find(".check").remove();
	                    _parent.find(".required").append("<span class='check'>(" + data.html + ")</span>");
	                    $("#sponsor_id_error").html("");
	                }
	                if(_.isFunction(callback)) callback.call(this, is_valid_sponsor);
	            }

	        });

	        //return is_valid_sponsor;
	    };


	    var checkUplineIdAvailability = function(from_submit, callback) {
	        var _upline_id = $("#upline_id");
	        var _parent = _upline_id.parent();
	        var upline_id = $.trim(_upline_id.val());
	        var is_valid_upline = 0;

	        var m_result = {available_side: 0, is_valid_upline: false, message: ''};


	        b.request({
	            url: '/main/registration/check_upline_id',
	            data: {
	                'upline_id': upline_id,
	                'check_from': 'members-account'
	            },
	            on_success: function(data, status) {
	                if (data.status==1)
	                {
	                    //is_valid_upline = data.is_valid_upline;
	                    //_position = data.side_value;
	                    m_result.available_side = data.side_value;
	                    m_result.is_valid_upline = data.is_valid_upline;

	                   var check_sponsorid_modal = b.modal.new({
	                        title: 'Upline Verification',
	                        width: '580px',
	                        html: data.html,
	                        disableClose : true,
	                        buttons: {
	                            'Ok' : function() {
	                                //alert(data.side_value);
	                                //alert($('#available_position').val());

	                                if ($('#available_position').val() == "left")
	                                //if (data.side_value == "left")
	                                {
	                                    $("#position option[value='left']").attr("selected", true);
	                                    $("#position").attr("disabled", true);

	                                    _parent.addClass("control-group success");
	                                    _parent.find(".check").remove();
	                                    _parent.find(".required").append("<span class='check'>(Valid)</span>");
	                                    $("#upline_id_error").html("");
	                                }

	                                else if ($('#available_position').val() == "right")
	                                //else if (data.side_value == "right")
	                                {
	                                    $("#position option[value='right']").attr("selected", true);
	                                    $("#position").attr("disabled", true);
	                                    resetFormControl($("#position"), $("#position_error"));

	                                    _parent.addClass("control-group success");
	                                    _parent.find(".check").remove();
	                                    _parent.find(".required").append("<span class='check'>(Valid)</span>");
	                                    $("#upline_id_error").html("");
	                                }

	                                /*else  ($('#available_position').val() == "")
	                                //else if (data.side_value == "both")
	                                {
	                                    $("#position option[value='right']").attr("selected", true);
	                                    resetFormControl($("#position"));

	                                    _parent.addClass("control-group success");
	                                    _parent.find(".check").remove();
	                                    _parent.find(".required").append("<span class='check'>(Valid)</span>");
	                                }*/
	                                else
	                                {
	                                    _parent.addClass("control-group error");
	                                    _parent.find(".check").remove();
	                                    _parent.find(".required").append("<span class='check'>(No Available Slot)</span>");
	                                    $("#upline_id_error").html("");
	                                }

	                                check_sponsorid_modal.hide();

	                            },
	                            'Cancel' : function() {
	                                check_sponsorid_modal.hide();
	                            }
	                        }
	                   });
	                   check_sponsorid_modal.show();

	                }
	                else
	                {
	                    _parent.removeClass("control-group success");

	                    _parent.addClass("control-group error");
	                    _parent.find(".check").remove();
	                    _parent.find(".required").append("<span class='check'>(" + data.html + ")</span>");
	                    $("#upline_id_error").html("");
	                }
	                if(_.isFunction(callback)) callback.call(this, m_result);
	            }
	        });
	    };


		$("#btn_clear").click(function() {
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

            _account_id.val("");
            _card_code.val("");
            _upline_id.val("");
            _sponsor_id.val("");

            resetFormControl(_account_id, _account_id_error);
            resetFormControl(_card_code, _card_code_error);
            resetFormControl(_sponsor_id, _sponsor_id_error);
            resetFormControl(_upline_id, _upline_id_error);
            resetFormControl(_position, _position_error);
		});



</script>
