/*
 * GBS Core javascript 
 *
 */

/*
 *  DUMMY FIREBUG CONSOLE
 */

if (typeof(console) == 'undefined') {
    console = {
        log: function() {},
        debug: function() {},
        info: function() {},
        warn: function() {},
        error: function() {},
        assert: function() {},
        dir: function() {},
        dirxml: function() {},
        trace: function() {},
        group: function() {},
        time: function() {},
        timeEnd: function() {},
        profile: function() {},
        profileEnd: function() {},
        count: function() {}
    };
}

/*
 * updating indexOf array based function
 */
if(!Array.indexOf) {
	Array.prototype.indexOf = function(obj){
		for(var i=0; i<this.length; i++){
			if(this[i]==obj){
				return i;
			}
		}
		return -1;
	}
}

/*
 * generate Unique GUID
 */
var generateGuid = function() {
    var result, i, j;
    result = '';
    for(j=0; j<32; j++) {
        if( j == 8 || j == 12|| j == 16|| j == 20)
        result = result + '_';
        i = Math.floor(Math.random()*16).toString(16).toUpperCase();
        result = result + i;
    }
    return result;
};


var numberFormat = function( number, decimals, dec_point, thousands_sep ) {
    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 0 : decimals;
    var d = dec_point == undefined ? "." : dec_point;
    var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace((new RegExp('(\\d{3})(?=\\d)','g')), "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}; // end numberFormat

/*
 * check if value is numeric 
 */
function isNumeric(val) {
   return (val - 0) == val && val.length > 0;
}


// redirect(module)
/* function redirect(url) {
        url = base_url + url;
        location.replace(url);
} */
function redirect(url, replace) {
    if (typeof(replace) == "undefined") replace = false;
	//if (url[0] == '/') url = url.substring(1);
    //url = base_url + url;
    if (replace){
        window.location.replace(url);
	}else{
		//url = base_url + url;
		if (url[0] != '/')
			url = '/' + url;

        window.location = url;
	}
}

$(".numeric-entry").live("keydown",function(event){
	if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46) {
        // Allow normal operation
    } else {
        // Prevent the rest		
        event.preventDefault();
    }
});

var showLoading = function(show_message) {
	if (typeof(show_message) == 'undefined') show_message = true;
	if (show_message) {
		$('.loading_message').show();
	} else {
		$('.loading_message').hide();
	}
    $("#loading_overlay").fadeIn(100);
};

var hideLoading = function() {
	$("#loading_overlay").fadeOut(300);
	$("#loading_text").html("");
};


/*
 * jQuery check if element still exist
 */
jQuery.fn.exists = function() {
    return $(this).length > 0;
};

(function() {
	
	/*
	 * Initialize the beyond object
	 */
	var root = this;
	
	var beyond = {};
	
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
			exports = module.exports = beyond;
		}
		exports.beyond = beyond;
		exports.b = beyond;
	} else {
		root['beyond'] = beyond;
		root['b'] = beyond;
	}
	
	/* 
	 * Add Ajax Request
	 * Params:
	 * - url : (string) url where to send the request to
	 * - data : (json) Data to be submitted
	 * - track : (boolean) weather to call track function on request
	 * - no_duplicate : (boolean) weather the request will be unique and receiving the same request will be cancelled
	 * - method : (string), POST, GET, DELETE, PUT
	 * - before_send : (event, function) will be called on before send event
	 * - on_sucess : (event, funciton) will be called once the request is completed without any errors
	 * - on_error : (event, function) will be called once the request is completed with errors
	 * - on_track : (function) will be called if the track property is set to true
	 */
	
	// Add collection of requested urls
	beyond.requested_urls = [];
	
	// add the request method for submitting ajax request
	beyond.request = function(params) {

		if (typeof(params.url) == "undefined") return false; // required param
		if (typeof(params.data) == "undefined") params.data = {};
		if (typeof(params.track) == "undefined") params.track = false;
		if (typeof(params.no_duplicate) == "undefined") params.no_duplicate = false;
 		if (typeof(params.method) == "undefined") params.method = "POST";
		if (typeof(params.with_overlay) == "undefined") params.with_overlay = true;
		
		if (params.with_overlay) showLoading();
	
		var _request_url = params.url + JSON.stringify(params.data);
		
		if (params.no_duplicate) {
			var _idx = _.indexOf(beyond.requested_urls, _request_url);
			if (_idx >= 0) {
				// url request exists
				return false;
			}
		}
		
		beyond.requested_urls.push(_request_url);
		
		// EXPECTATION WILL BE THAT ALL REQUEST WILL BE JSON ENCODED TYPE
	    var req = jQuery.ajax({
	        type: params.method,
	        url: params.url,
	        dataType: "json",
	        data: params.data,
	        beforeSend: function(xhr) {
	            if (_.isFunction(params.before_send)) params.before_send(params.url, params.data);
	        },
	        success: function(data, status) {

	            // remove the requested url
	            var _idx = _.indexOf(beyond.requested_urls, _request_url);
	            beyond.requested_urls.splice(_idx, 1);

				if (params.with_overlay) hideLoading();
				
	            if (_.isFunction(params.on_success)) params.on_success(data, status);

	        },
	        error: function(request, status, error_thrown) {
	            // remove the requested url
	            _idx = beyond.requested_urls.indexOf(_request_url);
	            beyond.requested_urls.splice(_idx, 1);

				if (params.with_overlay) hideLoading();
				
	            if (_.isFunction(params.on_error)) params.on_error(request, status, error_thrown);
	
	        }
	    });

		if (params.track) {
			// do analytics tracking here
			if (_.isFunction(params.on_track)) params.on_track(params.url, params.data);
		}
		
	    return req;
	};
	
	/*
	 * Add browser detection
	 */
	beyond.browser = {
	    init: function(){
	        this.name = this.searchString(this.dataBrowser) || "An unknown browser";
	        this.version = this.searchVersion(navigator.userAgent) ||
	        this.searchVersion(navigator.appVersion) ||
	        "an unknown version";
	        this.OS = this.searchString(this.dataOS) || "an unknown OS";
	    },
	    searchString: function(data){
	        for (var i = 0; i < data.length; i++) {
	            var dataString = data[i].string;
	            var dataProp = data[i].prop;
	            this.versionSearchString = data[i].versionSearch || data[i].identity;
	            if (dataString) {
	                if (dataString.indexOf(data[i].subString) != -1) 
	                    return data[i].identity;
	            }
	            else 
	                if (dataProp) 
	                    return data[i].identity;
	        }
	    },
	    searchVersion: function(dataString){
	        var index = dataString.indexOf(this.versionSearchString);
	        if (index == -1) 
	            return;
	        return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
	    },
	    dataBrowser: [{
	        string: navigator.userAgent,
	        subString: "Chrome",
	        identity: "Chrome"
	    }, {
	        string: navigator.userAgent,
	        subString: "OmniWeb",
	        versionSearch: "OmniWeb/",
	        identity: "OmniWeb"
	    }, {
	        string: navigator.vendor,
	        subString: "Apple",
	        identity: "Safari",
	        versionSearch: "Version"
	    }, {
	        prop: window.opera,
	        identity: "Opera"
	    }, {
	        string: navigator.vendor,
	        subString: "iCab",
	        identity: "iCab"
	    }, {
	        string: navigator.vendor,
	        subString: "KDE",
	        identity: "Konqueror"
	    }, {
	        string: navigator.userAgent,
	        subString: "Firefox",
	        identity: "Firefox"
	    }, {
	        string: navigator.vendor,
	        subString: "Camino",
	        identity: "Camino"
	    }, { // for newer Netscapes (6+)
	        string: navigator.userAgent,
	        subString: "Netscape",
	        identity: "Netscape"
	    }, {
	        string: navigator.userAgent,
	        subString: "MSIE",
	        identity: "Explorer",
	        versionSearch: "MSIE"
	    }, {
	        string: navigator.userAgent,
	        subString: "Gecko",
	        identity: "Mozilla",
	        versionSearch: "rv"
	    }, { // for older Netscapes (4-)
	        string: navigator.userAgent,
	        subString: "Mozilla",
	        identity: "Netscape",
	        versionSearch: "Mozilla"
	    }],
	    dataOS: [{
	        string: navigator.platform,
	        subString: "Win",
	        identity: "Windows"
	    }, {
	        string: navigator.platform,
	        subString: "Mac",
	        identity: "Mac"
	    }, {
	        string: navigator.userAgent,
	        subString: "iPhone",
	        identity: "iPhone/iPod"
	    }, {
	        string: navigator.platform,
	        subString: "Linux",
	        identity: "Linux"
	    }]

	};
	
	// initialize the browser detection
	beyond.browser.init();
	
	/*
	 * Add Parsing of URI
	 */
	beyond.parseUri = function(url) {
		
		var	o   = beyond.parseUri.options,
			m   = o.parser[o.strictMode ? "strict" : "loose"].exec(url),
			uri = {},
			i   = 14;

		while (i--) uri[o.key[i]] = m[i] || "";

		uri[o.q.name] = {};
		uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
			if ($1) uri[o.q.name][$1] = $2;
		});

		return uri;
		
	};
	
	beyond.parseUri.options = {
		strictMode: false,
		key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
		q:   {
			name:   "queryKey",
			parser: /(?:^|&)([^&=]*)=?([^&]*)/g
		},
		parser: {
			strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
			loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
		}
	};
	
	beyond.uri = beyond.parseUri(this.location.href);
	
	
	/*
	 * Add Modal window
	 *
	 */
	beyond._modals = {};

	var _modal = {
		init: function(params) {
			
            if (typeof(params) == "undefined") params = {};
			if (typeof(params.width) == "undefined") params.width = 560;
			if (typeof(params.height) == "undefined") params.height = 'auto';
			if (typeof(params.disableClose) == "undefined") params.disableClose = false;
			if (typeof(params.buttons) == "undefined") params.buttons = {};
			if (typeof(params.onHide) == "undefined") params.onHide = null;
			if (typeof(params.cssClass) == "undefined") params.cssClass = '';
			
			this.buttons = params.buttons;
			this.onHide = params.onHide;
			
			var _modal_id = _.uniqueId('modal_');
			this.id = _modal_id;
			
			var _close_tag = '';
			var _buttons_tag = '';
			
			if (!params.disableClose) _buttons_tag += '<a href="#" class="btn btn_modal_close" onclick="return false;" >Close</a>';
			_.each(this.buttons, function(val, key) {
				var _id = key.toLowerCase().replace(/ /g, '_').replace(/-/g,'_');				
				_buttons_tag += '<a id="' + _modal_id + '_btn_' + _id + '" href="#" class="btn btn-primary" onclick="return false;" >' + key + '</a>';  
			});
			
			if (!params.disableClose) _close_tag = '<a class="close btn_modal_close" data-dismiss="modal">&times;</a>';
			this._html = '<div id="' + this.id + '" class="modal hide '+params.cssClass+'" style="width: ' + (_.isNumber(params.width) ? params.width + 'px' : params.width) + '; height: ' + (_.isNumber(params.height) ? params.height + 'px' : params.height) + ';"><div class="modal-header">' + _close_tag + '<h3>' + params.title + '</h3></div>' ;
			this._html += '<div class="modal-body">' + params.html + '</div>';
			this._html += '<div class="modal-footer">' + _buttons_tag + '</div></div>'
			
		},
		show: function(params) {
			$('body').append(this._html);
			var wrapper = $('body');
			// re-center it and override top and margin
			//$('#' + this.id).css({left: ( wrapper.width() - $('#' + this.id).width() ) / 2, margin:0, top: 100});
			var left_val = ( $(window).width() - $('#' + this.id).width() ) / 2;
			var top_val =  ( $(window).height() - $('#' + this.id).height() ) / 2;
			var position_val = 'fixed';
			
			if($(window).width() < $('#' + this.id).width() || $(window).height() < $('#' + this.id).height()){
				top_val = 500; position_val = 'absolute';
				left_val = ( wrapper.width() - $('#' + this.id).width() ) / 2;
			}
			
			$('#' + this.id).css({left: left_val, margin:0, top: top_val, position: position_val});
			
			var _modal_id = this.id;
			// implement and itirate button functions
			_.each(this.buttons, function(val, key) {
				var _id = key.toLowerCase().replace(/ /g, '_').replace(/-/g,'_');				
				$('#' + _modal_id + '_btn_' + _id).click(function(e) {
					e.preventDefault();
					val.call();
				});
			});
			
			var _current = this;
			$('#'+_modal_id+'.modal .btn_modal_close').click(function(e) {
				e.preventDefault();
				_current.hide();
			});

			if (typeof(params) == 'undefined') params = {};
			params['backdrop'] = 'static';
			params['keyboard'] = false;

			$('#' + this.id).modal(params);
			$('#' + this.id).modal('show');

			var zIndex1 = $('#' + this.id).css('z-index');

			if (zIndex1 == '1050') {
				if ($('#' + this.id).prevAll('.modal').length > 0) {
					var _last = _.last($('#' + this.id).prevAll('.modal'));
					zIndex1 = parseInt($(_last).css('z-index')) + 100;
				}
				var zIndex2 = zIndex1 - 10;
				$('#' + this.id).css({'z-index' : zIndex1});
				$('#' + this.id).next().css({'z-index' : zIndex2});
			}
			
			$('html,body').animate({
			        scrollTop: $("#"+this.id).offset().top,
			        scrollLeft: $("#"+this.id).offset().left},
			        'slow');

		},
		hide: function(destroy) {
			destroy = typeof(destroy) == "undefined" ? true : destroy; 
			if ($('#' + this.id).exists()) {
				var _this = this;
				$('#' + this.id).bind('hidden', function() {
					$('#' + _this.id).unbind('hidden');
					$('#' + _this.id).remove();
					if(_.isFunction(_this.onHide)) {
						_this.onHide.call();
					}
					if (destroy) _this.destroy();
				});
				$('#' + this.id).modal('hide');

			}
		},
		destroy: function() {
			this.hide();
			delete beyond._modals[this.id];
		}
	};

	beyond.modal = {
		create : function(params) {
			var _new_modal = _.extend(_modal);
			_new_modal.init(params);
			beyond._modals[_new_modal.id] = _.clone(_new_modal);
			return beyond._modals[_new_modal.id];
		},
		find: function(id) {
			return _.find(beyond._modals, function(val){ return val.id = id; });
		}
	};
	
	beyond.modal['new'] = beyond.modal.create;
	
	beyond.getImageSize = function(img_url) {
		var newImg = new Image();
		newImg.src = img_url;
		return { 'width' :  newImg.width, 'height' : newImg.height};
	};
	
	
	beyond.webcontrol = {};
	beyond.webcontrol.updateDateControl = function(id) {
		if ($('#'+id).exists()) {
			var _month = $('#'+id+'_month option:selected').val(); 
			var _day = $('#'+id+'_day option:selected').val(); 
			var _year = $('#'+id+'_year option:selected').val();
			var _sel = _month > 0 && _day > 0 && _year > 0; 
			var _value = '';

			if (_month > 0 && _year > 0) {
				var _tmpDate = new Date((_month) +' 1 ,'+_year);

				var _totalFeb = 28;

				if ((_tmpDate.getFullYear() % 4 == 0) && (_tmpDate.getFullYear() % 100 != 0) || (_tmpDate.getFullYear() % 400 == 0)){
					_totalFeb = 29;  
				}

				var _totalDays = [31, _totalFeb, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
				var _monthDays = _totalDays[_month - 1];
				
				var _days_tag = '<option value="0">-</option>';
				for (var i = 1; i <= _monthDays; i++)
					_days_tag += '<option value="'+i+'">'+i+'</option>';
				$('#'+id+'_day').html(_days_tag);
				if (_day > _monthDays) {
					_day = _monthDays;
					$('#'+id+'_day').val(_monthDays);
				}
				else
				{
					$('#'+id+'_day').val(_day);
				}
				
			}
			
			if (_sel) _value = _year + '-' + _month + '-' + _day;
			$('#'+id).val(_value);
		}
	};
	
	beyond.renderTinyMCE = function(elem_id, params) {

		if (typeof(tinyMCE)) {
			tinyMCE.init({
				// Location of TinyMCE script
				script_url : '/assets/js/libs/tinymce/tiny_mce.js',
				mode: "exact",
				elements : elem_id,
				// General options
				theme : "advanced",
				plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

				// Theme options
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : false,

				// Example content CSS (should be your site CSS)
				content_css : "/assets/js/libs/tinymce/themes/advanced/skins/default/content.css",

				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "lists/template_list.js",
				external_link_list_url : "lists/link_list.js",
				external_image_list_url : "lists/image_list.js",
				media_external_list_url : "lists/media_list.js"
			});
		}
		
	};
	
	beyond.sizeGripper = {};
	beyond.sizeGripper.minHeight = 0;
	beyond.sizeGripper.init = function(minHeight) {
		if (_.isUndefined(minHeight)) minHeight = 0;
		if (!$('#size-grip-anchor').exists())
		{
			var _tag = "<div id='size-grip-anchor' style='position:fixed; bottom:0; height:1px; left:0; width:1px;'></div>";
			$('body').append(_tag);
		}
		
		beyond.sizeGripper.minHeight = _.isNumber(minHeight) ? minHeight : 0;
		
	};
	beyond.sizeGripper.getHeight = function() {
		var _top = 0; 
		el = document.getElementById('size-grip-anchor');
		while( el != null ){
			_top += el.offsetTop;
			el = el.offsetParent;
		}
		if (beyond.sizeGripper.minHeight > _top) _top = beyond.sizeGripper.minHeight;
		return _top;
	};
	
	beyond.navigateBack = function() {
		history.back();
		return false;
	};
	
	beyond.numericalPad = function(el_select) {
		$(el_select).keydown( function(e) {
			if(e.keyCode != 8 && e.keyCode != 46 && e.keyCode != 39 && e.keyCode != 37 && e.keyCode != 35 && e.keyCode != 36){
				if(e.shiftKey || !((57 >= e.keyCode && e.keyCode >= 48) || (105 >= e.keyCode && e.keyCode >= 96))) e.preventDefault();
			}
		});
	};
	
	beyond.enableButtons = function(selector) {
		if($.trim(selector).length > 0){
			$(selector).removeAttr('disabled');
			$(selector).removeClass('btn-disabled');
			$(selector).removeClass('disabled');
		}
	};
	
	beyond.disableButtons = function(selector) {
		if($.trim(selector).length > 0){
			$(selector).attr('disabled', 'disabled');
			$(selector).addClass('btn-disabled');
			$(selector).addClass('disabled');
		}
	};
	
	beyond.dateFormat = function () {
	    var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
	      timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
	      timezoneClip = /[^-+\dA-Z]/g,
	      pad = function (val, len) {
	        val = String(val);
	        len = len || 2;
	        while (val.length < len) val = "0" + val;
	        return val;
	      };
	    // Regexes and supporting functions are cached through closure
	    return function (date, mask, utc) {
	      var dF = beyond.dateFormat;
	      // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
	      if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
	        mask = date;
	        date = undefined;
	      }
	      // Passing date through Date applies Date.parse, if necessary
	      date = date ? new Date(date) : new Date;
	      if (isNaN(date)) throw SyntaxError("invalid date");
	      mask = String(dF.masks[mask] || mask || dF.masks["default"]);
	      // Allow setting the utc argument via the mask
	      if (mask.slice(0, 4) == "UTC:") {
	        mask = mask.slice(4);
	        utc = true;
	      }
	      var _ = utc ? "getUTC" : "get",
	        d = date[_ + "Date"](),
	        D = date[_ + "Day"](),
	        m = date[_ + "Month"](),
	        y = date[_ + "FullYear"](),
	        H = date[_ + "Hours"](),
	        M = date[_ + "Minutes"](),
	        s = date[_ + "Seconds"](),
	        L = date[_ + "Milliseconds"](),
	        o = utc ? 0 : date.getTimezoneOffset(),
	        flags = {
	          d: d,
	          dd: pad(d),
	          ddd: dF.i18n.dayNames[D],
	          dddd: dF.i18n.dayNames[D + 7],
	          m: m + 1,
	          mm: pad(m + 1),
	          mmm: dF.i18n.monthNames[m],
	          mmmm: dF.i18n.monthNames[m + 12],
	          yy: String(y).slice(2),
	          yyyy: y,
	          h: H % 12 || 12,
	          hh: pad(H % 12 || 12),
	          H: H,
	          HH: pad(H),
	          M: M,
	          MM: pad(M),
	          s: s,
	          ss: pad(s),
	          l: pad(L, 3),
	          L: pad(L > 99 ? Math.round(L / 10) : L),
	          t: H < 12 ? "a" : "p",
	          tt: H < 12 ? "am" : "pm",
	          T: H < 12 ? "A" : "P",
	          TT: H < 12 ? "AM" : "PM",
	          Z: utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
	          o: (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
	          S: ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
	        };
	      return mask.replace(token, function ($0) {
	        return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
	      });
	    };
	  }();
	
	// Some common format strings
	beyond.dateFormat.masks = {
	  "default": "ddd mmm dd yyyy HH:MM:ss",
	  shortDate: "m/d/yy",
	  mediumDate: "mmm d, yyyy",
	  longDate: "mmmm d, yyyy",
	  fullDate: "dddd, mmmm d, yyyy",
	  shortTime: "h:MM TT",
	  mediumTime: "h:MM:ss TT",
	  longTime: "h:MM:ss TT Z",
	  isoDate: "yyyy-mm-dd",
	  isoTime: "HH:MM:ss",
	  isoDateTime: "yyyy-mm-dd'T'HH:MM:ss",
	  isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
	};
	
	// Internationalization strings
	beyond.dateFormat.i18n = {
	  dayNames: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
	  monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
	};
	
	beyond.isValidDate = function(str_date) {
		var _date = new Date(str_date);
		if ( Object.prototype.toString.call(_date) === "[object Date]" ) {
			// it is a date
			if ( isNaN( _date.getTime() ) ) {  // d.valueOf() could also work
				return false;
			} else {
				return true;
			}
		} else {
			return false
		}
	};
	
}).call(this);

$(document).ready(function() {
	
	// Disable certain links in docs
	$('section [href^=#]').click(function (e) {
		e.preventDefault()
	});
	
	window.prettyPrint && prettyPrint();

	$("body").on("click", "[data-action='navigate-back']", function(e) {
		e.preventDefault();
		history.back();
	});
	
});


