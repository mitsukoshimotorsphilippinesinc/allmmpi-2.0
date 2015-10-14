/*
 * RFID Keys lib javascript 
 *
 */

(function() {
	
	/*
	 * Initialize the beyond object
	 */
	var root = this;
	
	var rfid = {};
	
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
			exports = module.exports = vitalc;
		}
		exports.rfid = rfid;
	} else {
		root['rfid'] = rfid;
	}
	
	rfid.target_id = '';
	rfid._template = '<div id="rfid_notibox_backdrop" class="modal-backdrop in rfid_notibox" style="z-index: 5000; display:none;"></div>'
					 +'<div id="rfid_notibox" class="well rfid_notibox" style="position: fixed; top: 30%; left: 50%; width: 400px; height: 80px; margin-top: -40px;'
					 +' margin-left: -200px; font-size: 30px; background-color: #666; color: #eee; text-align: center; line-height: 45px; z-index:5100; display:none;">'
					 +'<p id="rfid_notibox_msg" style="font-size: 30px; line-height: 45px;">Place your ID to scan...<p><button id="btn_rfid_noti_close" class="btn btn-danger">Close</button</div>';

	rfid.last_result = '';
	rfid.value = '';
	
	rfid._default = {
		target_id : '',
		on_ok : function() {},
		on_cancel : function() {},
		auto_close : true,
	};

	rfid.scan = function(options) {
		
		var options = $.extend(rfid._default, options)

		rfid.target_id = options.target_id;
		
		rfid.last_result = rfid.value;
		rfid.value = '';
		
		// remove any existing instance to scan
		rfid.destroy();
		
		// append
		$('body').append(rfid._template);
		
		// display
		$('.rfid_notibox').show();
		
		$('#btn_rfid_noti_close').click(function(e) {
			e.preventDefault();
			rfid.destroy();
			if (_.isFunction(options.close_cb)) options.close_cb.call(this, rfid.value);
		});
		
		// bind key events
		$(document).unbind('keydown');
		$(document).bind('keydown', function (e) {
		    if (e.keyCode === 8) { // disable browser backspace
		        e.preventDefault();
		    }
		});
		
		$(document).unbind('keyup');
		$(document).bind('keyup',function(e) {
			e.preventDefault();
			if (e.keyCode == 13) {
				rfid.setValue();
				if (options.auto_close) rfid.destroy();
				if (_.isFunction(options.on_ok)) options.on_ok.call(this, rfid.value);
			} else if (e.keyCode == 8) {
				if (rfid.value.length > 0) rfid.value = rfid.value.substring(0, rfid.value.length-1);
			} else {
				// do something here
			}

		});
		
		$(document).unbind('keypress');
		$(document).bind('keypress',function(e) {
			e.preventDefault();
			if (!e.ctrlKey && !e.altKey)
				if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode >= 65 && e.charCode <= 90) || (e.charCode >= 97 && e.charCode <= 122 ) || e.charCode == 32) {
					rfid.value = rfid.value + String.fromCharCode(e.charCode);
				}
		});
		
		
	};
	
	rfid.setValue = function() {
		if ($('#'+rfid.target_id).length > 0) {
			var _tagName = $('#'+rfid.target_id).prop('tagName').toLowerCase();

			if (_tagName == "input" || _tagName == "textarea") {
				$('#'+rfid.target_id).val(rfid.value);
			} else {
				$('#'+rfid.target_id).html(rfid.value);
			}
		}
	};
	
	rfid.reset = function(reset_msg) {
		reset_msg = typeof(reset_msg) == 'undefined' ? true : reset_msg;
		if (reset_msg) rfid.setMsg('Place your ID to scan...');
		rfid.last_value = rfid.value;
		rfid.value = '';
	}
	
	rfid.setMsg = function(msg) {
		$('#rfid_notibox_msg').html(msg);
	};
	
	rfid.destroy = function() {
		
		// remove elements
		$('.rfid_notibox').hide();
		$('.rfid_notibox').remove();
		
		// unbind events
		$(document).unbind('keydown');
		$(document).unbind('keyup');
		$(document).unbind('keypress');
		
	};
	
	
}).call(this);

