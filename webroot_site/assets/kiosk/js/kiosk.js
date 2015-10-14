/*
 * Vital-C Kiosk javascript 
 *
 */

(function() {
	
	/*
	 * Initialize the beyond object
	 */
	var root = this;
	
	var kiosk = {};
	
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
			exports = module.exports = kiosk;
		}
		exports.kiosk = kiosk;
		exports.k = kiosk;
	} else {
		root['kiosk'] = kiosk;
		root['k'] = kiosk;
	}
	
	kiosk.item_categories = {};
	kiosk.product_types = {};
	
	kiosk.screen = '';
	kiosk.screen_title = '';
	kiosk.screen_url = '';
	
	
	kiosk.login = function(url, username, password, cb) {
		
		b.request({
			url: url,
			data: {'username' : username, 'password' : password},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};
	
	kiosk.breadcrumbs = [];
	kiosk.renderBreadcrumbs = function() {
		$('#kiosk_breadcrumbs').replaceWith(_.template($('#breadcrumbs-template').html(), {'breadcrumbs' : kiosk.breadcrumbs}));
	};
	
	kiosk.addBreadcrumbs = function(item) {
		var _default_item = {
			'code' : '',
			'name' : ''
		};
		
		if (typeof(item) == 'undefined') return false;
		
		item = $.extend({}, _default_item, item);
		
		kiosk.breadcrumbs.push(item);

	};
	
	/**************
	 * Profile functions
	 */
	
	kiosk.profile = {};
	kiosk.profile.target_id = 'member_area_content';
	kiosk.profile.base_url = '/kiosk/profile';
	
	kiosk.profile.handleAction = function(type, id) {
		
		if (type == 'profile') {
			if (_.indexOf(['profile','accounts', 'earnings', 'orders', 'encoding', 'vouchers'], id) != -1) {
				kiosk.profile.renderPage(id, function(data) {
					if (id == 'orders') {
						// remove download
						$('#btn_download').remove();
					}
				});
			} 
		}
		
	};
	
	kiosk.profile.renderPage = function(page, cb) {
		
		target_id = kiosk.profile.target_id;
		
		// set navi status
		$('#profile-navbar li').removeClass('active');
		$('#profile-navbar li a[data-id="'+page+'"]').parent().addClass('active');
		console.log('#profile-navbar li a[data-id="'+page+'"]');
		$('#profile-dropdown-menu li').removeClass('active');
		$('#profile-dropdown-menu li a[data-id="'+page+'"]').parent().addClass('active');
		
		b.request({
			url: kiosk.profile.base_url+'/get_'+page,
			data: {},
			on_success: function(data, status) {
				$('#'+target_id).html(data.data.html);
				
				// disable form submition
				$('form').attr('onsubmit', 'return false;').attr('method', 'post');
				
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};
	
	
	/**************
	 * Store functions
	 */
	

}).call(this);


// Generic event handlers

$(document).off("click", "a.active-link");
$(document).on("click", "a.active-link", function(e) {
	e.preventDefault();
	
	var type = $(this).data('type');
	var id = $(this).data('id');
	
	console.log('type = ' + type + ' ; id = ' + id);
	
	if (kiosk.screen == 'members_area') {
		
		kiosk.profile.handleAction(type, id);
		
	} else if (kiosk.screen == 'store') {
		
	}
	
});

$(document).off("submit", "form");
$(document).off("submit", "form", function(e) {
	console.log('on submit');
	return false;
});

