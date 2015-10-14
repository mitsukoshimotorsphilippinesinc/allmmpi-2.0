
(function() {
	
	/*
	 * Initialize the beyond object
	 */
	var root = this;
	
	var webpoi = {};

	var trans = {
		transaction_id : '',
		transaction_code : '',
		transaction_type : '',
		status : '',
		releasing_facility_id: '',
		is_override: '',
		customer : {
			member_id : '',
			account_id : '',
			fullname : '',
			first_name : '',
			middle_name : '',
			last_name : '',
			member_types: [],
			funds: '',
			giftcheque: '',
			is_account_active : false,
			employee_slots : {},
			is_employee : 0
		},
		fpv_vouchers: {},
		mpv_vouchers: {},
		p2p_vouchers: {},
		products : {},
		rebate_products : {},
		summary : {
			sub_total : 0,
			vat : 0,
			discounts : {},
			payments : [],
			rebate_amount : 0,
			pdv_amount: 0
		}
	};
	
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
			exports = module.exports = webpoi;
			exports = module.exports = trans;
		}
		exports.webpoi = webpoi;
		exports.trans = trans;
	} else {
		root['webpoi'] = webpoi;
		root['trans'] = trans;
	}
	
	
	webpoi.hold_trans = [];
	
	webpoi.items = {};
	
	webpoi.out_of_stock_items = {};
	
	// poi transaction type (0:regular, 1:gc)
	webpoi.trans_type = 0;
	
	//specific product types
	webpoi.p2p_package_product_type = 0;
	
	// facility 
	webpoi.facility = {};
	webpoi.facility.items = {};
	
	//p2p package counter
	webpoi.p2p_package_counter = 0;
	
	// user
	webpoi.user = {};
	
	// settings
	webpoi.settings = {};
	webpoi.settings.vat_percent = 0;
	webpoi.settings.epc_discount = 0;
	webpoi.settings.credit_card_and_cheque_discount_reduction = 0;
	webpoi.settings.epc_min_amount = 0;
	webpoi.settings.stockist_min_amount = 0;
	webpoi.settings.payment_methods = {
		'cash' : "Cash",
		'cheque' : "Cheque",
		'card' : "Credit Card",
		'funds' : "Funds",
		'giftcheque' : "Gift Cheque",
		'onlinegiftcheque' : "Online Gift Cheque",
		'gcep' : "GCEP",
		'cpoints' : "C Points"
	};
	webpoi.settings.loading_image_url = '';
	webpoi.transaction_details = {};
	webpoi.transaction_details.rate_to_use = 1;
	webpoi.transaction_details.remaining_rebate_amount = 0;
	webpoi.transaction_details.used_rebate_amount = 0;
	webpoi.transaction_details.total_vat_amount = 0;
	webpoi.transaction_details.total_discount = 0;
	webpoi.transaction_details.total_payment = 0;
	webpoi.transaction_details.total_due = 0;
	webpoi.transaction_details.amount_due = 0;
	webpoi.transaction_details.change = 0;
	webpoi.transaction_details.remaining_rebate_amount = 0;
	webpoi.transaction_details.used_rebate_amount = 0;
	webpoi.transaction_details.vatable_sales_amount = 0;
	webpoi.transaction_details.ar_number = '';
	webpoi.transaction_details.ar_issued_date = '';
	webpoi.transaction_details.ar_remarks = '';
	webpoi.out_of_stocked_inventory = [];
	webpoi.is_gc_exclusive = 0;
	
	webpoi.override_remarks = "";
	
	// functions
	
	webpoi.init = function() {
	
		// initialize
		b.disableButtons('#btn-cancel');
		webpoi.setPOIButtons();
		
		var rel = $('#selected_facility_input').val();
		$("#releasing_facility_id option[value='"+rel+"']").attr('selected', 'selected');
	};
	
	webpoi.setPOITrans = function(type) {
		
		webpoi.trans_type = type;
		webpoi.setPOIButtons();
		
		var rate = $('#ddRates').val();
		if (type == 1) {
			if (rate != 1) {
				$('#ddRates').val('1');
				$('#ddRates').trigger('change');
			}
			$('#ddRates').attr('disabled', 'disabled');
			$('#ddRates option[value="4"]').hide();
		} else if (type == 2) {
			if (rate != 4) {
				$('#ddRates option[value="4"]').show();
				$('#ddRates').val('4');
				$('#ddRates').trigger('change');
			}
			$('#ddRates').attr('disabled', 'disabled');
		} else {
			if (rate == 4) {
				$('#ddRates').val('1');
				$('#ddRates').trigger('change');
			}
			$('#ddRates').removeAttr('disabled', 'disabled');
			$('#ddRates option[value="4"]').hide();
		}
		
	};
	
	webpoi.setPOIButtons = function() {
		// check poi type (0: regular, 1: gc)
		if (webpoi.trans_type == 0) {
			b.enableButtons('.btn-payments');
			b.disableButtons('.btn-payments[data-type="giftcheque"]');
			b.disableButtons('.btn-payments[data-type="onlinegiftcheque"]');
			b.disableButtons('.btn-payments[data-type="gcep"]');
			b.enableButtons('.btn-discounts');
			b.enableButtons('.btn-vouchers');
			b.enableButtons('.btn-add-discount');
			b.disableButtons('.btn-misc');
		} else if (webpoi.trans_type == 1) {
			b.disableButtons('.btn-payments');
			b.enableButtons('.btn-payments[data-type="giftcheque"]');
			b.enableButtons('.btn-payments[data-type="onlinegiftcheque"]');
			b.enableButtons('.btn-payments[data-type="gcep"]');
			b.disableButtons('.btn-discounts');
			b.disableButtons('.btn-vouchers');
			b.disableButtons('.btn-add-discount');
			b.disableButtons('.btn-misc');
		} else if (webpoi.trans_type == 2) {
			b.disableButtons('.btn-payments');
			b.disableButtons('.btn-discounts');
			b.disableButtons('.btn-vouchers');
			b.enableButtons('.btn-misc');
		} else {
			b.enableButtons('.btn-payments');
			b.disableButtons('.btn-payments[data-type="giftcheque"]');
			b.disableButtons('.btn-payments[data-type="onlinegiftcheque"]');
			b.disableButtons('.btn-payments[data-type="gcep"]');
			b.enableButtons('.btn-discounts');
			b.enableButtons('.btn-vouchers');
			b.disableButtons('.btn-misc');
		}
	};
	
	webpoi.newTransaction = function() {
		
		trans.transaction_id = '';
		trans.transaction_code = '';
		trans.transaction_type = '';
		trans.status = '';
		trans.releasing_facility_id = '';
		trans.is_override = '';
		trans.customer = {};
		trans.customer.member_id = '';
		trans.customer.account_id = '';
		trans.customer.fullname = '';
		trans.customer.first_name = '';
		trans.customer.middle_name = '';
		trans.customer.last_name = '';
		trans.customer.funds = '';
		trans.customer.giftcheque = '';
		trans.customer.member_types= [];
		trans.customer.is_account_active = false;
		trans.customer.employee_slots = {};
		trans.customer.is_employee = 0;
		trans.customer.is_on_hold = 0;

		trans.products = {};
		trans.rebate_products = {};
		trans.fpv_vouchers = {};
		trans.mpv_vouchers = {};
		trans.p2p_vouchers = {};
		trans.summary = {};
		trans.summary.sub_total = 0;
		trans.summary.mpv_total = 0;
		trans.summary.fpv_total = 0;
		trans.summary.p2p_total = 0;
		trans.summary.vat = 0;
		trans.summary.vatable_discount = 0;
		trans.summary.discounts = {};
		trans.summary.payments = [];
		trans.summary.rebate_amount = 0;
		trans.summary.pdv_amount = 0;
		trans.summary.misc_amount = 0;
		trans.summary.net_max_discount = 0;
		
		webpoi.transaction_details.rate_to_use = 1;
		webpoi.transaction_details.total_vat_amount = 0;
		webpoi.transaction_details.total_discount = 0;
		webpoi.transaction_details.total_payment = 0;
		webpoi.transaction_details.total_due = 0;
		webpoi.transaction_details.amount_due = 0;
		webpoi.transaction_details.change = 0;
		webpoi.transaction_details.remaining_rebate_amount = 0;
		webpoi.transaction_details.used_rebate_amount = 0;
		webpoi.transaction_details.vatable_sales_amount = 0;
		webpoi.transaction_details.ar_number = '';
		webpoi.transaction_details.ar_remarks = '';
		webpoi.out_of_stocked_inventory = [];
		
		b.enableButtons('#content button');
		
		$('.help-block').hide();
		$('.help-block').parents('.control-group').removeClass('error');
		
		$('.customer-funds-and-gc').hide();
		webpoi.refreshDisplay();
		
		webpoi.init();
		
	};
	
	webpoi.getProductImage = function(product_id) {
		
		var url = '';
		if (typeof(webpoi.items.products[product_id]) != 'undefined') {
			if (typeof(webpoi.items.products[product_id].image_filename) != 'undefined') {
				var _images = $.parseJSON(webpoi.items.products[product_id].image_filename);
				$.each(_images, function(index, item) {
					if (item.is_default) {
						url = item.url;
						return false;
					}
				});
				if (url.length == 0) {
					if (_images.length > 0) {
						url = _images[0].url;
					}
				}
			}
		}
		return url;
	};

	webpoi.assignCustomer = function() {
		var modal = b.modal.create({
			title: "Assign Customer",
			html: _.template($('#assign-customer-template').html(), {}),
			width: 700,
			buttons : {
				'Clear' : function() {
				
					$("#txt_member_search_key").val("");
					$("#txt_employee_search_key").val("");
					$("#txt_nonmember_fullname").val("");
					$("#assign-customer-list").html("");
					
					//webpoi.clearCustomer();
					//modal.hide();
				}
			}
		});
		
		modal.show();
		
		// assign modal events
		$('#btn_nonmember_assign').click(function(e) {
			e.preventDefault();
			$('#frm_assign_nonmember').removeClass('error');
			$('#txt_nonmember_fullname_help').html('');
			$('#txt_nonmember_fullname_help').hide();
			
			var fullname = $.trim($('#txt_nonmember_fullname').val());
			
			if (fullname.length == 0) {
				$('#frm_assign_nonmember').addClass('error');
				$('#txt_nonmember_fullname_help').html('Fullname cannot be empty.');
				$('#txt_nonmember_fullname_help').show();
				return;
			}
			
			trans.customer.member_id = '';
			trans.customer.account_id = '';
			trans.customer.fullname = fullname;
			trans.customer.first_name = '';
			trans.customer.middle_name = '';
			trans.customer.last_name = '';
			trans.customer.funds = '';
			trans.customer.giftcheque = '';
			trans.customer.member_types = [];
			trans.customer.is_account_active = false;
			
			//remove p2p packages
			$.each(trans.products, function(index, item){
				console.log('Ive got a dream');
				if(item.product_type_id == webpoi.p2p_package_product_type) {
					console.log(trans.products);
					delete trans.products[item.product_id];
				}
			});
			console.log(trans.products);
			//set vouchers to empty
			trans.fpv_vouchers = {};
			trans.mpv_vouchers = {};
			trans.p2p_vouchers = {};
			webpoi.refreshDisplay();
			
			$('.customer-funds-and-gc').hide();
			webpoi.refreshCustomer();
			modal.hide();
		});
		
		$('#btn_member_search').click(function(e) {
			e.preventDefault();
			$('#frm_assign_search').removeClass('error');
			$('#txt_member_search_key_help').html('');
			
			var search_key = $.trim($('#txt_member_search_key').val());
			
			if (search_key.length == 0) {
				$('#frm_assign_search').addClass('error');
				$('#txt_member_search_key_help').html('Account ID cannot be empty.');
				$('#txt_member_search_key_help').show();
				return;
			}
			
			webpoi.searchMembers(search_key, function(data) {
				
				if (data.status == 'ok') {
					
					var members = data.data.members;
					$('#assign-customer-listing').html(_.template($('#assign-customer-item-template').html(), {'members' : members}));
					$.each(data.data.keys, function(index, key_item) {

						$('#assign-customer-listing td:nth-child(2)').highlight(key_item);
					});
					
					// apply click event on select buttons
					$('#assign-customer-listing .btn-select-member').click(function(e) {
						var member_id = $(this).data('id');
						var account = _.clone(members[member_id]);
						
						webpoi.setMember(account);
						
						//set vouchers to empty
						trans.fpv_vouchers = {};
						trans.mpv_vouchers = {};
						trans.p2p_vouchers = {};
						webpoi.refreshDisplay();
						
						modal.hide();
						
					});

				} else {
					$('#assign-customer-listing').html('<tr><td colspan="3">'+data.msg+'</td></tr>');
				}
				
			});
			
		});
		
		$('#btn_employee_search').click(function(e) {
			e.preventDefault();
			$('#frm_employee_search').removeClass('error');
			$('#txt_employee_search_key_help').html('');
			
			var search_key = $.trim($('#txt_employee_search_key').val());
			
			if (search_key.length == 0) {
				$('#frm_employee_search').addClass('error');
				$('#txt_employee_search_key_help').html('Account ID cannot be empty.');
				$('#txt_employee_search_key_help').show();
				return;
			}
			
			webpoi.searchEmployees(search_key, function(data) {
				
				if (data.status == 'ok') {
					
					var employees = data.data.employees;

					$('#assign-customer-listing').html(_.template($('#assign-employee-item-template').html(), {'employees' : employees}));
					$.each(data.data.keys, function(index, key_item) {

						$('#assign-customer-listing td:nth-child(2)').highlight(key_item);
					});
					
					// apply click event on select buttons
					$('#assign-customer-listing .btn-select-employee').click(function(e) {
						var employee_id = $(this).data('id');
						var account = _.clone(employees[employee_id]);
						
						webpoi.setEmployee(account);
						
						//set vouchers to empty
						trans.fpv_vouchers = {};
						trans.mpv_vouchers = {};
						trans.p2p_vouchers = {};
						
						//remove p2p packages
						$.each(trans.products, function(index, item){
							console.log('Ive got a dream');
							if(item.product_type_id == webpoi.p2p_package_product_type) {
								delete trans.products[item.product_id];
							}
						});
						
						webpoi.refreshDisplay();
						modal.hide();
						
					});
					
				} else {
					$('#assign-customer-listing').html('<tr><td colspan="3">'+data.msg+'</td></tr>');
				}
				
			});
			
		});
	};
	
	webpoi.setReleasingFacility = function(releasing_facility_id) {
		trans.releasing_facility_id = releasing_facility_id;
		//webpoi.facility = releasing_facility;
		b.request({
			url: '/webpoi/change_selected_facility',
			data: {'facility_id':releasing_facility_id},
			on_success: function(data){
				if(data.status == 'ok'){
					webpoi.facility = data.data.facility;
					
				}
			}
		});
	}
	
	webpoi.setMember = function(account) {
		
		trans.customer.member_id = account.member_id;
		trans.customer.account_id = account.account_id;
		trans.customer.fullname = account.fullname;
		trans.customer.first_name = account.first_name;
		trans.customer.middle_name = account.middle_name;
		trans.customer.last_name = account.last_name;
		trans.customer.is_account_active = account.is_account_active == 1;
		trans.customer.funds = account.funds;
		trans.customer.giftcheque = account.giftcheque;
		trans.customer.gcep = account.gcep;
		trans.customer.cpoints = account.cpoints;
		trans.customer.member_types = [];
		trans.customer.employee_slots = {};
		trans.customer.is_employee = 0;
		trans.customer.is_on_hold = account.is_on_hold;
		webpoi.refreshCustomer();

		// do an ajax to get the memeber's type
		b.request({
			with_overlay: false,
			url: '/webpoi/get_member_types',
			data: {'member_id' : account.member_id},
			on_success: function(data, status) {
				
				if (data.status == 'ok') {
					trans.customer.member_types = data.data;
					$('.customer-funds-and-gc').show();
					webpoi.refreshCustomer();
				}

			}
		});
		
		if(trans.customer.is_on_hold == 1)
		{
			var hold_modal = b.modal.create({
				title: 'Member On-Hold',
				html: "Member is put on-hold. Cannot pay by Funds or Giftcheques.",
				width: 300
			});
			hold_modal.show();
		}
		
		webpoi.checkOnHold();
	};
	
	webpoi.setEmployee = function(account) {
		
		trans.customer.member_id = account.member_id;
		trans.customer.account_id = account.account_id;
		trans.customer.fullname = account.fullname;
		trans.customer.first_name = account.first_name;
		trans.customer.middle_name = account.middle_name;
		trans.customer.last_name = account.last_name;
		trans.customer.is_account_active = account.is_account_active == 1;
		trans.customer.member_types = account.member_types;
		trans.customer.employee_slots = account.employee_slots;
		trans.customer.is_employee = 1;
		webpoi.refreshCustomer();

	};
	
	webpoi.checkOnHold = function() {
		if(trans.customer.is_on_hold == 1)
		{
			//disable buttons
			b.disableButtons('.btn-payments[data-type="funds"]');
			b.disableButtons('.btn-payments[data-type="giftcheque"]');
			b.disableButtons('.btn-payments[data-type="onlinegiftcheque"]');
			b.disableButtons('.btn-payments[data-type="gcep"]');
		}	
	}
	
	webpoi.searchMembers = function(search_key, cb, with_overlay) {
		with_overlay = typeof(with_overlay) == 'undefined' ? true : with_overlay;
		b.request({
			'with_overlay' : with_overlay,
			url: '/webpoi/get_members',
			data: {'search_key' : search_key},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};
	
	webpoi.searchEmployees = function(search_key, cb, with_overlay) {
		with_overlay = typeof(with_overlay) == 'undefined' ? true : with_overlay;
		b.request({
			'with_overlay' : with_overlay,
			url: '/webpoi/get_employees',
			data: {'search_key' : search_key},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
		
	};
	
	webpoi.refreshCustomer = function() {
		var fullname = trans.customer.fullname.length == 0 ? 'No Customer Name' : trans.customer.fullname;
		var account_id = trans.customer.account_id.length == 0 ? '' : trans.customer.account_id;
		
		var member_type = '';
		$.each(trans.customer.member_types, function(index, item) {
			member_type = member_type + '<span class="label label-info">'+item.member_type+'</span>';
		});
		
		var active_state = '';
		if (trans.customer.account_id.length > 0) {
			if (trans.customer.is_account_active) {
				active_state = '<span class="label label-successs">Active</span>&nbsp;';
			} else {
				active_state = '<span class="label label-important">In Active</span>&nbsp;';
			}
		}
		
			
		$('#customer-name').html(fullname);
		//$('#customer-name').attr('title', fullname);
		$('#customer-name').attr('data-original-title', trans.customer.fullname.length == 0 ? '' : trans.customer.fullname);
		$('#customer-account-id').html(account_id);

		//place funds and gc
		$('#customer-funds').html("<strong>"+trans.customer.funds+"</strong>");
		$('#customer-gc').html("<strong>"+trans.customer.giftcheque+"</strong>");
		$('#customer-gcep').html("<strong>"+trans.customer.gcep+"</strong>");
		$('#customer-cpoints').html("<strong>"+trans.customer.cpoints+"</strong>");
		
		$('#customer-member-type').html(active_state+member_type);
		if (trans.customer.fullname.length > 0) 
			$('#customer-name').addClass('selected-customer');
		else
			$('#customer-name').removeClass('selected-customer');
			
	};
	
	webpoi.clearCustomer = function() {
		trans.customer.member_id = '';
		trans.customer.account_id = '';
		trans.customer.fullname = '';
		trans.customer.account_type = '';
		trans.customer.is_account_active = false;
		trans.customer.employee_slots = {};
		trans.customer.is_employee = 0;
		
		webpoi.refreshCustomer();
	};
	
	webpoi.searchProduct = function(product_code, cb) {
		b.request({
			url: '/webpoi/get_product',
			data: {'product_code' : product_code, 'trans_type' : this.trans_type},
			on_success: function(data, status) {
				if (_.isFunction(cb)) cb.call(this, data);
			}
		});
	};
	
	webpoi.browseProductModal = null;
	webpoi.browseProduct = function() {
		
		webpoi.browseProductModal = b.modal.create({
			title: "Browse Products",
			html: _.template($('#product-grid-template').html(),{}),
			width: 830,
		});
		webpoi.browseProductModal.show();
		
		// display loading
		$('.product-box-grids').hide();
		$('#product-browse-grid-1').html('<div>Loading... <img src="'+webpoi.settings.loading_image_url+'" alt="" /></div>');
		$('#product-browse-grid-1').show();
		
		var _render_products = function() {
			prod_grid_pages = [];
			
			if (webpoi.trans_type == 0) {
				prod_grid_pages.push('Home');
				// display the product types
				var html = _.template($('#product-grid-items-template').html(), {'grid_type' : 'product_types', 'items' : webpoi.items.product_types });
			} else {
				console.log(webpoi.items.product_types);
				prod_grid_pages.push('Home');
				// display product category
				//html = _.template($('#product-grid-items-template').html(), {'grid_type' : 'categories', 'items' : webpoi.items.item_categories, 'pages' : prod_grid_pages });
				html = _.template($('#product-grid-items-template').html(), {'grid_type' : 'product_types', 'items' : webpoi.items.product_types});
			}
			
			$('#product-browse-grid-1').html(html);
			
			
			webpoi.updateProductBrowsePager();
		};
		
		if (_.size(webpoi.items) > 0) {
			_render_products();
		} else {
			webpoi.getProducts(function() {
				_render_products();
			});
		}
	};
	
	webpoi.updateProductBrowsePager = function() {
		// update pager
		var html = _.template($('#product-grid-pager-template').html(), {'pages' : prod_grid_pages});
		$('#product-browse-pager').html(html);
		
		$('.product-browse-pager-item').click(function(e) {
			e.preventDefault();
			var idx = $(this).data('index');
			idx = parseInt(idx) + 1;
			$('.product-box-grids').hide();
			$('#product-browse-grid-'+idx).show();
			prod_grid_pages.splice(idx);
			webpoi.updateProductBrowsePager();
		});
	};
	
	webpoi.getProducts = function(cb) {
	
		b.request({
			with_overlay: true,
			url: '/webpoi/get_product_list',
			data: {},
			on_success: function(data, status) {
				webpoi.items = data.data;

				// do a little data processing
				webpoi.items.product_types.products = [];

				$.each(webpoi.items.products, function(index, item) {

					if (typeof(webpoi.items.item_categories[item.item_sub_type_id]) != 'undefined') {
						if (typeof(webpoi.items.item_categories[item.item_sub_type_id].products) == 'undefined')
							webpoi.items.item_categories[item.item_sub_type_id].products = [];

						webpoi.items.item_categories[item.item_sub_type_id].products.push(_.clone(item));
					}


					if (typeof(webpoi.items.product_types[item.product_type_id]) != 'undefined') {
						if (typeof(webpoi.items.product_types[item.product_type_id].products) == 'undefined')
							webpoi.items.product_types[item.product_type_id].products = [];

						webpoi.items.product_types[item.product_type_id].products.push(_.clone(item));
					}
						

				});
				
				b.request({
					with_overlay: true,
					url: '/webpoi/get_product_list/0',
					data: {},
					on_success: function(data, status) {
						webpoi.items.inactive_products = data.data.products;
					}
				});

				if (_.isFunction(cb)) cb.call(this);
			}
		});
		
	};
	
	webpoi.getFacilityItems = function(cb) {
		b.request({
			with_overlay: true,
			url: '/webpoi/get_facility_product_list',
			data: {'facility_id' : trans.releasing_facility_id},
			on_success: function(data, status) {
				webpoi.facility.items = data.data;
				if (_.isFunction(cb)) cb.call(this);
			}
		});
	};
	
	webpoi.isInStock = function(item_id, qty) {
		//alert(item_id);
		// make sure it a number
		qty = parseInt(qty);
		
		if(item_id == 0) //services, non-inventory products
		{
			return {'result' : true, 'quantity' : qty};
		}
		else if (typeof(webpoi.facility.items[item_id]) == 'undefined') {
				//alert(item_id);
				return {'result' : false, 'quantity' : 0};
		} else {
			var check_qty = parseInt(webpoi.facility.items[item_id].qty);
			
			if (check_qty < qty)
				return {'result' : false, 'quantity' : webpoi.facility.items[item_id].qty};
				
		}
		return {'result' : true, 'quantity' : webpoi.facility.items[item_id].qty};
	};
	
	webpoi.checkStocks = function(cb) {
		
		var _check_stocks = function() {
			webpoi.out_of_stocked_inventory = [];
			
			var products = [];
			
			var _products = {};
			// consolidate quantities of all products and child products
			$.each(trans.products, function(index, prod) {
				if (prod.selected_sub_products.length > 0)
				{
					$.each(prod.selected_sub_products, function(index, item) {
						//console.log(item);
						if (typeof(_products[item.item_id]) == 'undefined') 
						{
							_products[item.item_id] = {"product_id": item.child_product_id,"item_id": item.item_id,"quantity":item.quantity};
						}
						else 
						{
							_products[item.item_id].quantity = parseInt(_products[item.item_id].quantity) + parseInt(item.quantity	);
						}
					});
				}
				else
				{
					if (typeof(_products[prod.item_id]) == 'undefined') _products[prod.item_id] = {"product_id": prod.product_id,"item_id": prod.item_id,"quantity":prod.quantity};
					else _products[prod.item_id].quantity = parseInt(_products[prod.item_id].quantity) + parseInt(prod.quantity	);
				}
			});
			
			$.each(trans.rebate_products, function(index, prod) {
				if (typeof(_products[prod.item_id]) == 'undefined') _products[prod.item_id] = {"product_id": prod.product_id,"item_id": prod.item_id,"quantity":prod.quantity};
				else _products[prod.item_id].quantity = parseInt(_products[prod.item_id].quantity) + parseInt(prod.quantity);
			});
			
			
			$.each(_products, function(index, prod) {
				var ret = webpoi.isInStock(prod.item_id, prod.quantity);
				if (!ret.result) {
					webpoi.out_of_stocked_inventory.push({'product_id' : prod.product_id, 'available_quantity' : ret.quantity, 'requesting_quantity' : prod.quantity});
				}
			});
			return true;
		};
		
		/*if (_.size(webpoi.facility.items) == 0) {
			webpoi.getFacilityItems(function() {
				_check_stocks();
				if (_.isFunction(cb)) cb.call(this, webpoi.out_of_stocked_inventory);
			});
		} else {
			_check_stocks();
			if (_.isFunction(cb)) cb.call(this, webpoi.out_of_stocked_inventory);
		}*/
		webpoi.getFacilityItems(function() {
			_check_stocks();
			if (_.isFunction(cb)) cb.call(this, webpoi.out_of_stocked_inventory);
		});
	};
	
	webpoi.browseRebateProduct = function() {
		
		webpoi.browseProductModal = b.modal.create({
			title: "Browse Products",
			html: _.template($('#product-grid-template').html(),{}),
			width: 830,
		});
		webpoi.browseProductModal.show();
		
		// display loading
		$('.product-box-grids').hide();
		$('#product-browse-grid-1').html('<div>Loading... <img src="'+webpoi.settings.loading_image_url+'" alt="" /></div>');
		$('#product-browse-grid-1').show();
		
		webpoi.getProducts(function() {
			prod_grid_pages = [];
			prod_grid_pages.push('Rebate Products');
			// display the product types
			//orig
			html = _.template($('#product-grid-items-template').html(), {'grid_type' : 'categories', 'prefix' : 'rebate-', 'items' : webpoi.items.item_categories, 'pages' : prod_grid_pages });
			
			//dynamic
			//html = _.template($('#product-grid-items-template').html(), {'grid_type' : 'rebates', 'prefix' : 'rebate-', 'items' : webpoi.items.products, 'pages' : prod_grid_pages });
			$('#product-browse-grid-1').html(html);
			
			webpoi.updateProductBrowsePager();
		});

	};
	
	webpoi.isCustomerMemberType = function(mem_type) {
		var found = false;
		if (trans.customer.member_types.length > 0) {
			$.each(trans.customer.member_types, function(index, item) {
				if (parseInt(item.member_type_id) == parseInt(mem_type)) {
					found = true;
					return false;
				}
			});
		}
		
		return found;
	};
	
	webpoi.computeProductRebate = function(rebate_products) {
	
		var used_rebate_amount = 0;
		var remaining_rebate_amount = 0;
		var rebate_amount = 0;
		
		$.each(rebate_products, function(index, item) {
			
			var price = 0;
			var vat_amount = 0;
			var rate_id = webpoi.transaction_details.rate_to_use;
			if (rate_id == '2') {
				price = parseFloat(item.member_price);
			} else if (rate_id == '3') {
				price = parseFloat(item.employee_price);
			} else {
				price = parseFloat(item.standard_retail_price);
			}

			used_rebate_amount = used_rebate_amount + (price * parseInt(item.quantity));
		});
		
		if (webpoi.isCustomerMemberType(3) && (trans.summary.sub_total) >= webpoi.settings.stockist_min_amount) {
			var _rebate_percent = 0.05;
			
			rebate_amount = trans.summary.rebate_amount.toFixed(2);
		} else {
			rebate_amount = 0;
		}
		
		remaining_rebate_amount = parseFloat((rebate_amount - used_rebate_amount).toFixed(2));
		
		return {'rebate_amount' : rebate_amount, 'used_rebate_amount' : used_rebate_amount, 'remaining_rebate_amount' : remaining_rebate_amount};
	};
	
	webpoi.refreshDisplay = function() {
		trans = root.trans;
		// refresh customer info
		webpoi.refreshCustomer();

		// render product list
		var html = _.template($('#added-product-row-template').html(), {'products' : trans.products});
		$('#selected-product-container').html(html);
		
		// render rebate product list
		html = _.template($('#added-product-row-template').html(), {'is_rebate' : true, 'products' : trans.rebate_products});
		$('#selected-product-container').append(html);
		
		//render fpv vouchers list
		html = _.template($('#added-product-row-template').html(), {'is_fpv_voucher' : true, 'products' : trans.fpv_vouchers});
		$('#selected-product-container').append(html);
		
		//render mpv vouchers list
		html = _.template($('#added-product-row-template').html(), {'is_mpv_voucher' : true, 'products' : trans.mpv_vouchers});
		$('#selected-product-container').append(html);
		
		//render p2p product list
		html = _.template($('#added-product-row-template').html(), {'is_p2p_voucher' : true, 'products' : trans.p2p_vouchers});
		$('#selected-product-container').append(html);

		var gc_exclusive = 0;
		$.each(trans.products, function(index, item){
			if(item.is_gc_exclusive == 1) gc_exclusive = 1;
		});
		webpoi.is_gc_exclusive = gc_exclusive;

		var _tmp_rate_id = $('#ddRates').val();
		if (_tmp_rate_id != webpoi.transaction_details.rate_to_use)
			$('#ddRates').val(webpoi.transaction_details.rate_to_use);
		
		// render summary
		trans.summary.sub_total = 0;
		trans.summary.vat = 0;
		trans.summary.vatable_discount = 0;
		webpoi.transaction_details.total_vat_amount = 0;
		webpoi.transaction_details.total_discount = 0;
		webpoi.transaction_details.total_payment = 0;
		webpoi.transaction_details.total_due = 0;
		webpoi.transaction_details.amount_due = 0;
		webpoi.transaction_details.change = 0;
		webpoi.transaction_details.remaining_rebate_amount = 0;
		webpoi.transaction_details.used_rebate_amount = 0;
		webpoi.transaction_details.vatable_sales_amount = 0;
		trans.summary.net_max_discount = 0;
		
		var _payment_types = [];
		$.each(trans.summary.payments, function(index, item) {
			_payment_types.push(item.type);
		});
		
		_payment_types = _.uniq(_payment_types);
		is_credit_card_present = _.indexOf(_payment_types, 'card') != -1;
		
		trans.summary.pdv_amount = 0;
		trans.summary.non_discount_amount = 0;
		
		$.each(trans.products, function(index, item) {
			
			var price = 0;
			var vat_amount = 0;
			var vat_sales = 0;
			var rate_id = webpoi.transaction_details.rate_to_use;
			//used for giftcheque transactions
			var giftcheque = "";
			
			if(webpoi.trans_type == 1)
			{
				giftcheque = "giftcheque_";
			}
			
			if (rate_id == '2') {
				price = parseFloat(item[giftcheque+"member_price"]);
			} else if (rate_id == '3') {
				price = parseFloat(item[giftcheque+"employee_price"]);
			} else {
				price = parseFloat(item[giftcheque+"standard_retail_price"]);
			}
			
			if(webpoi.trans_type == 2) {
				price = parseFloat(item["cpoints_value"]);
			}
			
			if (item.is_vatable == '1') {
				vat_sales = parseFloat(price / (webpoi.settings.vat_percent / 100 + 1)).toFixed(2) * parseInt(item.quantity);
				vat_amount = parseFloat((parseFloat((price - parseFloat(price / (webpoi.settings.vat_percent / 100 + 1))).toFixed(4)) * parseInt(item.quantity)).toFixed(2));
			}
			webpoi.transaction_details.vatable_sales_amount += vat_sales;
			trans.summary.vat += vat_amount;
			if(item.product_type_id == webpoi.p2p_package_product_type) {
				trans.summary.sub_total = trans.summary.sub_total + (price);
			} else {
				trans.summary.sub_total = trans.summary.sub_total + (price * parseInt(item.quantity));
			}

			if (typeof(webpoi.items.products[item.product_id]) != 'undefined')
			{
				if (webpoi.items.products[item.product_id].product_type_id == '10') {
					trans.summary.pdv_amount += (price * parseInt(item.quantity));
				}
				else if (webpoi.items.products[item.product_id].product_type_id == '8' || webpoi.items.products[item.product_id].product_type_id == '9' || webpoi.items.products[item.product_id].product_type_id == '12' || webpoi.items.products[item.product_id].product_type_id == '13') {
					//Marketing Materials Pack, Promo Packages, GC Packages, Miscellaneous Products
					trans.summary.non_discount_amount += (price * parseInt(item.quantity));
				}
				else if (webpoi.items.products[item.product_id].product_type_id == '1' && webpoi.items.products[item.product_id].product_line_id == '0') {
					//Marketing Materials
					trans.summary.non_discount_amount += (price * parseInt(item.quantity));
				}
				else
				{
					trans.summary.vatable_discount += vat_amount;
				}
			}
		});
		
		//console.log('PDV = ' + trans.summary.pdv_amount);
		
		$.each(trans.rebate_products, function(index, item) {
			
			var price = 0;
			var rate_id = webpoi.transaction_details.rate_to_use;
			if (rate_id == '2') {
				price = parseFloat(item.member_price);
			} else if (rate_id == '3') {
				price = parseFloat(item.employee_price);
			} else {
				price = parseFloat(item.standard_retail_price);
			}

			webpoi.transaction_details.used_rebate_amount = webpoi.transaction_details.used_rebate_amount + (price * parseInt(item.quantity));
		});
		
		trans.summary.net_max_discount = trans.summary.sub_total - trans.summary.vatable_discount - trans.summary.pdv_amount - trans.summary.non_discount_amount;
		
		if (webpoi.isCustomerMemberType(3) && (trans.summary.sub_total) >= webpoi.settings.stockist_min_amount) {
			var card_payments = 0;
			var other_payments = 0;

			$.each(trans.summary.payments,function(index, item) {
				if(item.type == "card")
				{
					card_payments = parseFloat(card_payments) + parseFloat(item.value);
				}
				else
				{
					other_payments = parseFloat(other_payments) + parseFloat(item.value);
				}
			});

			var _rebate_percent = 0.05;
			var _card_rebate_percent = 0.02;
			var net_max_discount = trans.summary.net_max_discount;
			var rebate_amount = 0;
			//console.log("a"+net_max_discount);
			if(card_payments > 0 && net_max_discount > 0)
			{
				if((net_max_discount - card_payments) > 0)
				{
					rebate_amount = parseFloat((parseFloat(rebate_amount) + (parseFloat(card_payments) * _card_rebate_percent)).toFixed(2));
					net_max_discount = net_max_discount - card_payments;
				}
				else
				{
					rebate_amount = parseFloat((parseFloat(rebate_amount) + (parseFloat(net_max_discount) * _card_rebate_percent)).toFixed(2));
					net_max_discount = 0;
				}
			}
			//console.log("b"+net_max_discount);
			if(other_payments > 0 && net_max_discount > 0)
			{
				if((net_max_discount - other_payments) > 0)
				{
					rebate_amount = parseFloat((parseFloat(rebate_amount) + (parseFloat(other_payments) * _rebate_percent)).toFixed(2));
					net_max_discount = net_max_discount - other_payments;
				}
				else
				{
					rebate_amount = parseFloat((parseFloat(rebate_amount) + (parseFloat(net_max_discount) * _rebate_percent)).toFixed(2));
					net_max_discount = 0;
				}
			}
			//console.log("c"+net_max_discount);
			trans.summary.rebate_amount = parseFloat(rebate_amount.toFixed(2));
		} else {
			trans.summary.rebate_amount = 0;
		}
		
		webpoi.transaction_details.remaining_rebate_amount = parseFloat((trans.summary.rebate_amount - webpoi.transaction_details.used_rebate_amount).toFixed(2));
		
		// get total discount
		//console.log(trans.summary.discounts);
		webpoi.transaction_details.total_discount = 0;
		$.each(trans.summary.discounts, function(index, item) {
			if (item.type == 'amount') {
				webpoi.transaction_details.total_discount += parseFloat(item.value);
			} else {
				webpoi.transaction_details.total_discount += parseFloat((parseFloat(item.amount_to_discount) * (parseInt(item.value) / 100)).toFixed(2));
			}
		});

		$('#discount-items').html(_.template($('#summary-item-list-template').html(), {'type' : 'discount', 'items' : trans.summary.discounts}));

		// get total payment
		webpoi.transaction_details.total_payment = 0;
		$.each(trans.summary.payments, function(index, item) {
			webpoi.transaction_details.total_payment += parseFloat(item.value);
		});
		
		// add voucher's price to summary
		trans.summary.mpv_total = 0;
		trans.summary.fpv_total = 0;
		trans.summary.p2p_total = 0;
		$.each(trans.mpv_vouchers, function(index, item) {
			trans.summary.mpv_total += parseFloat(item.price);
		});
		$.each(trans.fpv_vouchers, function(index, item) {
			trans.summary.fpv_total += parseFloat(item.price);
		});
		$.each(trans.p2p_vouchers, function(index, item) {
			trans.summary.p2p_total += parseFloat(item.price);
		});
		

		//console.log(webpoi.transaction_details.total_payment);
		$('#payment-items').html(_.template($('#summary-item-list-template').html(), {'type' : 'payment', 'items' : trans.summary.payments}));
		
		var vouchers_total = trans.summary.mpv_total + trans.summary.fpv_total + trans.summary.p2p_total;
		var _final_total_due = trans.summary.sub_total + vouchers_total;
		if (webpoi.transaction_details.total_discount > 0) {
			_final_total_due = _final_total_due - trans.summary.vat;
			_final_total_due = _final_total_due - trans.summary.pdv_amount;
			_final_total_due = _final_total_due - webpoi.transaction_details.total_discount;
			_final_total_due += trans.summary.vat;
			_final_total_due += trans.summary.pdv_amount;
		}

		webpoi.transaction_details.total_due = _final_total_due;
		webpoi.transaction_details.amount_due = parseFloat((webpoi.transaction_details.total_due - webpoi.transaction_details.total_payment).toFixed(2));

		if (webpoi.transaction_details.amount_due < 0) {
			webpoi.transaction_details.change = Math.abs(webpoi.transaction_details.amount_due);
			webpoi.transaction_details.amount_due = 0;
		}
		
		// format values
		var sub_total_w_vouchers = trans.summary.sub_total + vouchers_total;
		var formatted_sub_total = numberFormat(sub_total_w_vouchers, 2);
		var formatted_vat_amount = numberFormat(trans.summary.vat, 2);
		var formatted_total = numberFormat(sub_total_w_vouchers, 2);
		var formatted_total_discount = numberFormat(webpoi.transaction_details.total_discount, 2);
		var formatted_total_payment = numberFormat(webpoi.transaction_details.total_payment, 2);
		var formatted_change = numberFormat(webpoi.transaction_details.change, 2);
		var formatted_amount_due = numberFormat(webpoi.transaction_details.amount_due, 2);
		var formatted_total_due = numberFormat(webpoi.transaction_details.total_due, 2);
		var formatted_rebated_value = numberFormat(webpoi.transaction_details.remaining_rebate_amount, 2);
		
                // item sub total
		$('#item-sub-total-value').html(formatted_sub_total);
		
		// total value
		$('#total-value').html(formatted_total);
		
		// total discount
		$('#total-discount-value').html(formatted_total_discount);
		
		// amount due
		$('#total-due-value').html(formatted_total_due);
		$('#amount-due-value').html(formatted_amount_due);
		$('#change-value').html((webpoi.transaction_details.amount_due > 0)?'-'+formatted_change:formatted_change);
		
		// rebate amount
		$('#product-rebate-value').html(formatted_rebated_value);
		
		// display status
		if (trans.status.length > 0) {
			$('#transaction-status').html(trans.status);
			$('#transaction-status').show();
			
			$('#transaction-code').html(trans.transaction_code);
			$('#transaction-code').show();
		} else {
			$('#transaction-status').hide();
			$('#transaction-status').html('');
			
			$('#transaction-code').hide();
			$('#transaction-code').html('');
		}
		
		// check details
		if (trans.summary.rebate_amount > 0) {
			// show rebate container
			$('.product-rebate-box').show();
			b.enableButtons('#btn-add-product-rebate');
		} else {
			$('.product-rebate-box').hide();
			b.disableButtons('#btn-add-product-rebate');
		}

		// set transaction
		/*if((trans.summary.payments.length > 0) && (trans.summary.payments[0].type.toLowerCase() == 'giftcheque' || trans.summary.payments[0].type.toLowerCase() == 'onlinegiftcheque')) {
			$('.tab_transactions[data-type="1"]').click();
		} else if((trans.summary.payments.length > 0) && (trans.summary.payments[0].type.toLowerCase() == 'cash' || trans.summary.payments[0].type.toLowerCase() == 'card' || trans.summary.payments[0].type.toLowerCase() == 'cheque' || trans.summary.payments[0].type.toLowerCase() == 'funds')){
			$('.tab_transactions[data-type="0"]').click();
		}*/
		
		// set button states
		if (trans.status.length > 0 && (trans.status.toLowerCase() == 'released' || trans.status.toLowerCase() == 'cancelled' || trans.status.toLowerCase() == 'failed' )) {
			b.disableButtons('#content button');
			b.enableButtons('#btn-new');
			b.enableButtons('#btn-get-transactions');
			b.enableButtons('#btn-reports');
			b.enableButtons('#item_summary_print');
		} else if (trans.status.toLowerCase() == 'completed') {
			b.disableButtons('#content button');
			b.enableButtons('#btn-new');
			b.enableButtons('#btn-cancel');
			b.enableButtons('#btn-get-transactions');
			b.enableButtons('#btn-reports');
			b.enableButtons('#item_summary_print');
			if(trans.transaction_type != 'OTC' && trans.ar_number == null)
				b.enableButtons('#btn-confirm');	
			/*b.enableButtons('#content button');
			if(trans.summary.payments[0].type.toLowerCase() != 'giftcheque' && trans.summary.payments[0].type.toLowerCase() != 'onlinegiftcheque') {
				b.disableButtons('.btn-payments[data-type="giftcheque"]');
				b.disableButtons('.btn-payments[data-type="onlinegiftcheque"]');
			} else {
				b.disableButtons('.btn-discounts');
				b.disableButtons('.btn-payments');
				b.enableButtons('.btn-payments[data-type="giftcheque"]');
				b.enableButtons('.btn-payments[data-type="onlinegiftcheque"]');
			}*/
		} else if (trans.status.toLowerCase() == 'pending') {
			b.enableButtons('#btn-confirm');
		} else if (trans.status.toLowerCase() == 'waiting') {
			b.disableButtons('#content button');
			b.enableButtons('#btn-new');
			b.enableButtons('#btn-cancel');
			b.enableButtons('#btn-get-transactions');
			b.enableButtons('#btn-reports');
			b.enableButtons('#item_summary_print');
		} else {
			
		}	
		
		webpoi.checkOnHold();	
	};
	
	webpoi.checkIfPackage = function(product, new_quantity, cb) {
		//console.log('new_quantity : ' + new_quantity );
		if (_.size(product.sub_product_groups) > 0) {

			var selected_products = {};
			var selected_groups = {};

			var modal = b.modal.create({
				title: "Package's Product Variants",
				html: _.template($('#product-variant-template').html(), {'product' : product, 'quantity' : new_quantity}),
				width: 890,
				buttons : {
					'Ok' : function() {

						// check if all available variant are used
						var _available_count =0;
						$.each(product.sub_product_groups, function(idx, grp) {
							_available_count += (grp.quantity * new_quantity) - grp.selected_quantity;
						});
						
						if (_available_count > 0) {
							return false;
						}

						modal.hide();
						var _ret_product = _.clone(product);
						$.each(selected_products, function(i, group) {
							$.each(group,function(index,item) {
								console.log(item);
								_ret_product.selected_sub_products.push(_.clone(item));
							});
						});

						if (_.isFunction(cb)) cb.call(this, _ret_product);
					}
				}
			});
			modal.show();
			
			// list the non-swappable items
			var _list_tag = '';
			$.each(product.sub_products, function(index, item) {
				if (item.is_swappable != '1') {
					var product_name = webpoi.items.products[item.child_product_id].product_name;
					var qty = parseInt(item.quantity) * new_quantity;
					_list_tag = _list_tag + '<li ><small>'+qty+' x</small> '+product_name+'</li>';
				}
			});

			$('#prod-variant-preselected-list').html(_list_tag);

			// list out groups
			_list_tag = '';
			$.each(product.sub_product_groups, function(index, item) {
				var qty = item.quantity * new_quantity;
				_list_tag = _list_tag + '<li id="prod-varian-group-'+index+'"><a href="javascript://void(0);" class="prod-varian-group-item" data-index="'+index+'">Group '+index+' <small>(Available: <span class="prod-varian-group-available">'+qty+'</span>)</small></a></li>';
			});

			$('#prod-variant-group-list').html(_list_tag);

			// -----------------------
			// group item event
			$('.prod-varian-group-item').click(function(e) {
				e.preventDefault();
				$(this).parent().siblings().removeClass('active');
				$(this).parent().addClass('active');

				var idx = $(this).data('index');
				_list_tag = '';
				$.each(product.sub_product_groups[idx].products, function(index, item) {
					var product_name = webpoi.items.products[item.child_product_id].product_name;
					var qty = parseInt(item.quantity * new_quantity);
					_list_tag = _list_tag + '<li id="prod-varian-product-'+item.child_product_id+'"><a href="javascript://void(0);" class="prod-varian-product-item" data-index="'+index+'" data-product-id="'+item.child_product_id+'" data-group="'+idx+'"><small><span class="prod-varian-product-available"></span></small> '+product_name+'</a></li>';
				});

				$('#prod-variant-product-list').html(_list_tag);

				// ----------------------
				// product item event
				$('.prod-varian-product-item').click(function(e) {
					e.preventDefault();

					var _idx = $(this).data('index');
					var _product_id = $(this).data('product-id');
					var _group_idx = $(this).data('group');

					var _new_qty = (product.sub_product_groups[_group_idx].quantity * new_quantity) - product.sub_product_groups[_group_idx].selected_quantity;
					if (_new_qty == 0) return; // no more available

					webpoi.showNumpad({'title' : 'Enter Quantity', 'default' : 1, 'with_decimal' : false, 'min' : 1, 'max' : _new_qty, 'skip_if_max' : 1}, function(set_qty) {

						product.sub_product_groups[_group_idx].selected_quantity+=set_qty;
						// update new qty
						_new_qty = (product.sub_product_groups[_group_idx].quantity * new_quantity) - product.sub_product_groups[_group_idx].selected_quantity;

						if(typeof(product.sub_product_groups[_group_idx].selected_products[_product_id]) == 'undefined') {
							product.sub_product_groups[_group_idx].selected_products[_product_id] = _.clone(product.sub_product_groups[_group_idx].products[_idx]);
							product.sub_product_groups[_group_idx].selected_products[_product_id].selected_quantity = set_qty;
						} else {
							product.sub_product_groups[_group_idx].selected_products[_product_id].selected_quantity+=set_qty;
						}

						$('#prod-varian-group-'+_group_idx+' .prod-varian-group-available').html(_new_qty);
						if (typeof(selected_products[_group_idx]) == 'undefined') {
							selected_products[_group_idx] = {};
						}
						if (typeof(selected_products[_group_idx][_product_id]) == 'undefined') {
							selected_products[_group_idx][_product_id] = {};
							selected_products[_group_idx][_product_id].item_id = product.sub_product_groups[_group_idx].selected_products[_product_id].item_id;
							selected_products[_group_idx][_product_id].product_id = product.sub_product_groups[_group_idx].selected_products[_product_id].product_id;
							selected_products[_group_idx][_product_id].child_product_id = product.sub_product_groups[_group_idx].selected_products[_product_id].child_product_id;
							selected_products[_group_idx][_product_id].group = product.sub_product_groups[_group_idx].selected_products[_product_id].group;
							selected_products[_group_idx][_product_id].quantity = set_qty;
							selected_products[_group_idx][_product_id].price = product.sub_product_groups[_group_idx].selected_products[_product_id].standard_retail_price;
						} else {
							selected_products[_group_idx][_product_id].quantity+=set_qty;
						}

						_list_tag = '';
						$.each(selected_products, function(i, group) {
							$.each(group,function(index,item) {
								var product_name = webpoi.items.products[item.child_product_id].product_name;
								var qty = parseInt(item.quantity);
								_list_tag = _list_tag + '<li id="prod-varian-selected-'+item.child_product_id+'"><a href="javascript://void(0);" class="selected-gray prod-varian-selected-item" data-index="'+index+'" data-product-id="'+item.child_product_id+'" data-group="'+item.group+'"><small><span class="prod-varian-selected-available">'+qty+'</span> x</small> '+product_name+'</a></li>';
							});
						});

						$('#prod-variant-selected-list').html(_list_tag);

						$('.prod-varian-selected-item').click(function(e) {
							e.preventDefault();
							var _selected_idx = $(this).data('index');
							var _selected_product_id = $(this).data('product-id');
							var _selected_group_idx = $(this).data('group');

							webpoi.showNumpad({'title' : 'Enter Quantity to Deduct', 'default' : 1, 'with_decimal' : false, 'min' : 1, 'max' : selected_products[_selected_group_idx][_selected_idx].quantity, 'skip_if_max' : 1}, function(set_qty) {

								product.sub_product_groups[_selected_group_idx].selected_quantity-=set_qty;
								// update new qty
								_new_qty = (product.sub_product_groups[_selected_group_idx].quantity * new_quantity) - product.sub_product_groups[_selected_group_idx].selected_quantity;

								$('#prod-varian-group-'+_selected_group_idx+' .prod-varian-group-available').html(_new_qty);

								selected_products[_selected_group_idx][_selected_idx].quantity-=set_qty;

								if (selected_products[_selected_group_idx][_selected_idx].quantity == 0) {
									delete selected_products[_selected_group_idx][_selected_idx];
									$('#prod-varian-selected-'+_selected_product_id+' .prod-varian-selected-item[data-group="'+_selected_group_idx+'"]').parent().remove();
								} else {
									$('#prod-varian-selected-'+_selected_product_id+' .prod-varian-selected-item[data-group="'+_selected_group_idx+'"] .prod-varian-selected-available').html(selected_products[_selected_group_idx][_selected_idx].quantity);
								}


							});

						});

						$('.prod-varian-selected-item').mousedown(function(e) {
							$(this).parent().addClass('active');
						});

						$('.prod-varian-selected-item').mouseup(function(e) {
							$(this).parent().removeClass('active');
						});

					});


				});

				$('.prod-varian-product-item').mousedown(function(e) {
					$(this).parent().addClass('active');
				});

				$('.prod-varian-product-item').mouseup(function(e) {
					$(this).parent().removeClass('active');
				});

			});


		} else {
			if (_.isFunction(cb)) cb.call(this, product);
		}
		
	};
	
	webpoi.addProduct = function(prod) {
		var product = _.clone(prod);
		webpoi.showNumpad({'title' : 'Enter Quantity', 'default' : 1, 'with_decimal' : false, 'min' : 1}, function(set_qty) {
			
			if (typeof(product.selected_sub_products) == 'undefined') product.selected_sub_products = [];
			if (typeof(product.sub_product_groups) == 'undefined') product.sub_product_groups = {};

			if (product.sub_products.length > 0) {

				$.each(product.sub_products, function(index, item) {
					if (item.is_swappable == '1') {
						if (typeof(product.sub_product_groups[item.group]) == 'undefined') {
							product.sub_product_groups[item.group] = {};
							product.sub_product_groups[item.group].quantity = parseInt(item.group_quantity);
							product.sub_product_groups[item.group].selected_quantity = 0;
							product.sub_product_groups[item.group].products = [];
							product.sub_product_groups[item.group].selected_products = {};
						}
						product.sub_product_groups[item.group].products.push(_.clone(item));
					} else {
						var _item = _.clone(item);
						_item.quantity = parseInt(_item.quantity) * set_qty;
							
						product.selected_sub_products.push(_.clone(_item));
					}
				});
			}
			
			
			webpoi.checkIfPackage(_.clone(product), set_qty, function(prod) {
				
				webpoi.getFacilityItems(function() {

					var _out_stocked_products = [];
					var _exceeded_slots = [];
					var qty = set_qty;
					// check if the selected product is a package
					if (prod.selected_sub_products.length > 0) {

						// this is a package product, will iterate the selected sub products
						$.each(prod.selected_sub_products, function(index, item) {

							//var ret = webpoi.isInStock(item.item_id, qty * item.quantity);
							var ret = webpoi.isInStock(item.item_id, item.quantity);
							if (!ret.result) {
								_out_stocked_products.push({'product_id' : item.child_product_id, 'available_quantity' : ret.quantity, 'requesting_quantity' : item.quantity});
							}
						});

					} else {

						// single product
						if (typeof(trans.products[prod.product_id]) != 'undefined')
							qty = parseInt(qty) + parseInt(trans.products[prod.product_id].quantity);

						var ret = webpoi.isInStock(prod.item_id, qty);
						if (!ret.result) {
							_out_stocked_products.push({'product_id' : prod.product_id, 'available_quantity' : ret.quantity, 'requesting_quantity' : qty});
						}
					}
					
					//check for employee slots when employee rate is used
					if(webpoi.transaction_details.rate_to_use == 3)
					{
						var slots = webpoi.hasEnoughSlots(prod.product_id, qty);

						if (!slots.result) {
							_exceeded_slots.push({'product_id' : prod.product_id, 'available_quantity' : slots.quantity, 'requesting_quantity' : qty});
						}

						if (_exceeded_slots.length  > 0) {

							var modal = b.modal.create({
								title: "Employee Slot Check",
								html: _.template($('#check-slot-dialog-template').html(), {'products' : _exceeded_slots}),
								width: 400,
							});
							modal.show();

							return;
						}
					}
					
					if (_out_stocked_products.length  > 0) {
						//console.log(_out_stocked_products);
						var out_of_stock_modal = b.modal.create({
							title: "Product Stock Check",
							html: _.template($('#out-of-stock-dialog-template').html(), {'products' : _out_stocked_products}),
							width: 400,
							disableClose: true,
							buttons: {
								'Close': function(){
									out_of_stock_modal.hide();
									return;
								},
								'Override': function(){
									webpoi.checkAuthority("Override Transactions","AUTHORIZE-OVERRIDE",function(allow) {
										if(allow)
										{
											var override_modal = b.modal.create({
												title: "Override Transaction",
												html: "<div><span>Please Enter Remarks:</span><textarea class='override_remarks' name='override_remarks' style='width:270px;'></textarea><span id='override_remarks_error' class='label label-important' style='display:none;'>Remarks cannot be empty.</span></div>",
												width: 310,
												disableClose : true,
												buttons : {
													'Cancel' : function() {
														override_modal.hide();
														return;
													},
													'Confirm' : function() {
														var override_remarks = $('.override_remarks').val();
														webpoi.override_remarks = override_remarks;
														if(override_remarks == ""){
															$('#override_remarks_error').show();
														}else{
															trans.is_override = 1;
															override_modal.hide();
															out_of_stock_modal.hide();
														
															$.each(_out_stocked_products, function(index, item){
																if(webpoi.out_of_stock_items[item.product_id] != undefined) {
																	webpoi.out_of_stock_items[item.product_id].requesting_quantity = webpoi.out_of_stock_items[item.product_id].requesting_quantity + item.requesting_quantity;
																}else {
																	webpoi.out_of_stock_items[item.product_id] = item;
																}
															});
														
															webpoi.addProductContinue(prod, set_qty);
														}
													}
												}
											});
											override_modal.show();
										}
									});
								}
							}
						});
						out_of_stock_modal.show();
						return;
					} 

					// if there are no errors, will continue here
					webpoi.addProductContinue(prod, set_qty);
				});
				
			});
			
		});

	};
	
	webpoi.addProductContinue = function(prod, set_qty){
		if (typeof(trans.products[prod.product_id]) != 'undefined') {

			var srp = prod.standard_retail_price;
			var member_price = prod.member_price;
			if(prod.product_type_id == webpoi.p2p_package_product_type)
			{
				$.each(prod.selected_sub_products, function(index, item){
					if(item.group != 0){
						var total_amount = parseFloat(item.price) * parseFloat(item.quantity);
						srp = parseFloat(srp) + parseFloat(total_amount);
						member_price = parseFloat(member_price) + parseFloat(total_amount);
					}
				});
				
				trans.products[prod.product_id].standard_retail_price = parseInt(trans.products[prod.product_id].standard_retail_price) + parseInt(srp);
				trans.products[prod.product_id].member_price = parseInt(trans.products[prod.product_id].member_price) + parseInt(member_price);
			}

			trans.products[prod.product_id].quantity = parseInt(trans.products[prod.product_id].quantity) + parseInt(set_qty);;

			if (trans.products[prod.product_id].selected_sub_products.length > 0) {
				//add adding of subproducts here
				$.each(prod.selected_sub_products, function(index, item) {

					var is_not_found = 0;
					$.each(trans.products[prod.product_id].selected_sub_products, function(index, trans_item){
						if(item.child_product_id == trans_item.child_product_id) {
							trans_item.quantity = parseInt(trans_item.quantity) + parseInt(item.quantity);
							is_not_found = 0;
							return false;
						}
						is_not_found = 1;
					});	
					if(is_not_found > 0) {
						trans.products[prod.product_id].selected_sub_products.push(item);
					}
				});
			}
			
			$.each(trans.summary.discounts, function(index, item) {
				if (item.type == 'epc_cash' || item.type == 'epc_card') {
					webpoi.removeDiscount(item.type);
				}
			});
			
			webpoi.refreshDisplay();
		} else {
			webpoi.checkIfVariablePrice(prod.product_id, function(is_variable_price){
				if(is_variable_price)
				{
					webpoi.showNumpad({'title' : "Enter Price", 'default' : 0, 'with_decimal' : true, 'min' : 0}, function(set_value) {
						trans.products[prod.product_id] = {
							'product_id' : prod.product_id,
							'item_id' : prod.item_id,
							'product_code' : prod.product_code,
							'product_name' : prod.product_name,
							'quantity' : set_qty,
							'product_type_id' : prod.product_type_id,
							'standard_retail_price' : set_value,
							'member_price' : set_value,
							'employee_price' : set_value,
							'giftcheque_standard_retail_price' : set_value,
							'giftcheque_member_price' : set_value,
							'giftcheque_employee_price' : set_value,
							'igpsm_points' : prod.igpsm_points,
							'is_vatable' : prod.is_vatable,
							'is_gc_buyable' : prod.is_gc_buyable,
							'is_gc_exclusive' : prod.is_gc_exclusive,
							'sub_products' : prod.sub_products,
							'selected_sub_products' : prod.selected_sub_products,
						};
						
						if(prod.is_gc_exclusive == 1) webpoi.is_gc_exclusive = 1;
						
						$.each(trans.summary.discounts, function(index, item) {
							if (item.type == 'epc_cash' || item.type == 'epc_card') {
								webpoi.removeDiscount(item.type);
							}
						});
						
						webpoi.refreshDisplay();
					});
				}
				else
				{
					//reference here
					var srp = prod.standard_retail_price;
					var member_price = prod.member_price;
					if(prod.product_type_id == webpoi.p2p_package_product_type)
					{
						$.each(prod.selected_sub_products, function(index, item){
							if(item.group != 0){
								var total_amount = parseFloat(item.price) * parseFloat(item.quantity);
								srp = parseFloat(srp) + parseFloat(total_amount);
								member_price = parseFloat(member_price) + parseFloat(total_amount);
							}
						});
					}
					console.log(srp);
					trans.products[prod.product_id] = {
						'product_id' : prod.product_id,
						'item_id' : prod.item_id,
						'product_code' : prod.product_code,
						'product_name' : prod.product_name,
						'quantity' : set_qty,
						'product_type_id' : prod.product_type_id,
						'standard_retail_price' : srp,
						'member_price' : member_price,
						'employee_price' : prod.employee_price,
						'cpoints_value' : prod.cpoints_value,
						'giftcheque_standard_retail_price' : prod.giftcheque_standard_retail_price,
						'giftcheque_member_price' : prod.giftcheque_member_price,
						'giftcheque_employee_price' : prod.giftcheque_employee_price,
						'igpsm_points' : prod.igpsm_points,
						'is_vatable' : prod.is_vatable,
						'is_gc_buyable' : prod.is_gc_buyable,
						'is_gc_exclusive' : prod.is_gc_exclusive,
						'sub_products' : prod.sub_products,
						'selected_sub_products' : prod.selected_sub_products,
					};
					
					if(prod.is_gc_exclusive == 1) webpoi.is_gc_exclusive = 1;
					
					$.each(trans.summary.discounts, function(index, item) {
						if (item.type == 'epc_cash' || item.type == 'epc_card') {
							webpoi.removeDiscount(item.type);
						}
					});
					
					webpoi.refreshDisplay();
				}
				
			});
			
		}
	}
	
	webpoi.addRebateProduct = function(prod) {
		var product = _.clone(prod);
		webpoi.showNumpad({'title' : 'Enter Quantity', 'default' : 1, 'with_decimal' : false, 'min' : 1}, function(set_qty) {
			
			if (typeof(product.selected_sub_products) == 'undefined') product.selected_sub_products = [];
			if (typeof(product.sub_product_groups) == 'undefined') product.sub_product_groups = {};

			if (product.sub_products.length > 0) {

				$.each(product.sub_products, function(index, item) {
					if (item.is_swappable == '1') {
						if (typeof(product.sub_product_groups[item.group]) == 'undefined') {
							product.sub_product_groups[item.group] = {};
							product.sub_product_groups[item.group].quantity = parseInt(item.group_quantity);
							product.sub_product_groups[item.group].selected_quantity = 0;
							product.sub_product_groups[item.group].products = [];
							product.sub_product_groups[item.group].selected_products = {};
						}
						product.sub_product_groups[item.group].products.push(_.clone(item));
					} else {
						var _item = _.clone(item);
						_item.quantity = parseInt(_item.quantity) * set_qty;
						product.selected_sub_products.push(_.clone(_item));
					}
				});
			}
			
			webpoi.checkIfPackage(_.clone(product), set_qty, function(prod) {
				
				webpoi.getFacilityItems(function() {
					var _out_stocked_products = [];
					var qty = set_qty;
					// check if the selected product is a package
					if (prod.selected_sub_products.length > 0) {

						$.each(prod.selected_sub_products, function(index, item) {
							var ret = webpoi.isInStock(item.item_id, item.quantity);
							if (!ret.result) {
								_out_stocked_products.push({'product_id' : item.child_product_id, 'available_quantity' : ret.quantity, 'requesting_quantity' : item.quantity});
							}
						});

					} else {
						// single product
						if (typeof(trans.products[prod.product_id]) != 'undefined')
							qty = parseInt(qty) + parseInt(trans.products[prod.product_id].quantity);

						var ret = webpoi.isInStock(prod.item_id, qty);
						if (!ret.result) {
							_out_stocked_products.push({'product_id' : prod.product_id, 'available_quantity' : ret.quantity, 'requesting_quantity' : qty});
						}
					}

					if (_out_stocked_products.length  > 0) {

						var modal = b.modal.create({
							title: "Product Stock Check",
							html: _.template($('#out-of-stock-dialog-template').html(), {'products' : _out_stocked_products}),
							width: 400,
						});
						modal.show();

						return;
					} 

					var _rebate_products = {};
                                        _rebate_products[prod.product_id] = {
                                                'product_id' : prod.product_id,
                                                'item_id' : prod.item_id,
                                                'product_code' : prod.product_code,
                                                'product_name' : prod.product_name,
                                                'quantity' : set_qty,
                                                'standard_retail_price' : prod.standard_retail_price,
                                                'member_price' : prod.member_price,
                                                'employee_price' : prod.employee_price,
                                                'igpsm_points' : prod.igpsm_points,
                                                'is_vatable' : prod.is_vatable,
                                                'sub_products' : prod.sub_products,
                                                'selected_sub_products' : prod.selected_sub_products,
                                        };


					var _rebate = webpoi.computeProductRebate(_rebate_products);
					if (_rebate.remaining_rebate_amount - webpoi.transaction_details.used_rebate_amount < 0) {

						var modal = b.modal.create({
							title: "Add Rebate Products Error!",
							html: _.template("You have entered too many products amounting of <strong><%= numberFormat(total_rebate, 2) %></strong> that exceeds the remaining rebate amount of <strong><%= numberFormat(rebate_amount, 2) %><strong>.<br/>Please try again. ", {'total_rebate' : _rebate.used_rebate_amount, 'rebate_amount' : webpoi.transaction_details.remaining_rebate_amount }),
							width: 400,
						});
						modal.show();

					} else {

						if (typeof(trans.rebate_products[prod.product_id]) != 'undefined') {
							trans.rebate_products[prod.product_id].quantity = parseInt(trans.rebate_products[prod.product_id].quantity) + parseInt(set_qty);
						} else {
							trans.rebate_products[prod.product_id] = {
								'product_id' : prod.product_id,
								'item_id' : prod.item_id,
								'product_code' : prod.product_code,
								'product_name' : prod.product_name,
								'quantity' : set_qty,
								'standard_retail_price' : prod.standard_retail_price,
								'member_price' : prod.member_price,
								'employee_price' : prod.employee_price,
								'igpsm_points' : prod.igpsm_points,
								'is_vatable' : prod.is_vatable,
								'sub_products' : prod.sub_products,
								'selected_sub_products' : prod.selected_sub_products,
							};
						}

						webpoi.refreshDisplay();
					}

				});
				
			});
			
		});
		
	};
	
	
	webpoi.showNumpad = function(options, cb, on_show, on_hide) {
		if (typeof(options) == 'undefined') options = {'min' : 0, 'max' : -1, 'skip_if_max' : -1, 'default' : 0, 'title' : 'Enter Number', 'with_decimal' : true};
		if (typeof(options.min) == 'undefined') options.min = 0;
		if (typeof(options.max) == 'undefined') options.max = -1;
		if (typeof(options.with_decimal) == 'undefined') options.with_decimal = true;
		if (typeof(options.title) == 'undefined') options.title = 'Enter Number';
		if (typeof(options.skip_if_max) == 'undefined') options.skip_if_max = -1;
		if (typeof(options.default) == 'undefined') options.default = 0;
		if (typeof(options.condition) == 'undefined') options.condition = function() { return true; };
		if (typeof(options.on_condition_fail) == 'undefined') options.on_condition_fail = function() {};
		if (typeof(options.on_condition_success) == 'undefined') options.on_condition_success = function() {};
		if (typeof(options.discount_type) == 'undefined') options.discount_type = 'default';
		
		var _init_value = true;
		var first_click = true;
		
		if (options.max > 0 && options.skip_if_max > 0) {
			if (options.max == options.skip_if_max) {
				if (_.isFunction(cb)) cb.call(this, options.max);
				return;
			}	
		}

		// set previous values for discount_type = percent
		var previousValues = null;
		if(options.discount_type == 'percent') {

			var prev_value_idx = parseInt(String(options.default.current_amount1).indexOf('.'));
			var prev_value = {
				'digits' : options.default.current_amount1,
				'decimal' : -1,
				'decimalOn' : false
			}
			if(prev_value_idx >= 0) {
				prev_value.digits = parseInt(String(options.default.current_amount1).substring(0,prev_value_idx));
				prev_value.decimal = parseInt(String(options.default).substring(prev_value_idx+1));
				prev_value.decimalOn = true;
			}

			var prev_amount_idx = parseInt(String(options.default.current_amount2).indexOf('.'));
			var prev_amount = {
				'digits' : options.default.current_amount2,
				'decimal' : -1,
				'decimalOn' : false
			}
			if(prev_amount_idx >= 0) {
				prev_amount.digits = parseInt(String(options.default.current_amount2).substring(0,prev_amount_idx));
				prev_amount.decimal = parseInt(String(options.default).substring(prev_amount_idx+1));
				prev_amount.decimalOn = true;
			}

			previousValues = {
				'value' : prev_value,
				'amount' : prev_amount
			}

			options.default = options.default.current_amount1;

		}

		//console.log(options.default);

		var _decimal_on = false;
		var _default_decimal = -1;
		var _idx = parseInt(String(options.default).indexOf('.'));

		if (_idx >= 0) {
			_decimal_on = true;
			_default_decimal = parseInt(String(options.default).substring(_idx+1));
			options.default = parseInt(String(options.default).substring(0,_idx));
		}
		
		var _val = {
				'digits' : String(options.default), 
				'decimal' : _default_decimal, 
				'value' : function() { 
					return parseFloat(parseInt(this.digits) + (parseInt(this.decimal) > 0 ? '.'+this.decimal : '')); 
				}
			};

		var _ok_btn = function() {
			// check if there are range set
			var _with_error = false;
			var _error_msg = '';
			
			var amount_value_max = 0;
			var _discount_val = 0;
			var _options_max = options.max;
			
			//console.log(webpoi.numpad_input_box.types);
			//console.log('_discount_val '+_discount_val);
			
			if(options.discount_type == 'percent') {
				amount_value_max = webpoi.numpad_input_box.types.amount.value();
				_discount_val = webpoi.numpad_input_box.types.amount.value() * (webpoi.numpad_input_box.types.value.value() / 100);
				if (typeof trans.summary.discounts[options.discount_type] != 'undefined'){
					//console.log('_percent_val '+_percent_val);
					var _percent_val = trans.summary.discounts[options.discount_type].amount_to_discount * (trans.summary.discounts[options.discount_type].value / 100);
					_options_max = options.max + _percent_val;
				}
			} else {
				amount_value_max = webpoi.numpad_input_box.types.value.value();
				_discount_val = amount_value_max;
				if (typeof trans.summary.discounts[options.discount_type] != 'undefined'){
					//console.log('trans.summary.discounts[options.discount_type]');
					//console.log(trans.summary.discounts[options.discount_type]);
					//console.log('trans.summary.discounts[options.discount_type].value ' + trans.summary.discounts[options.discount_type].value);
					_discount_val = trans.summary.discounts[options.discount_type].value;
					_options_max = options.max + trans.summary.discounts[options.discount_type].value;
				}
			}
			
			//console.log('amount_value_max '+amount_value_max);
			//console.log('_discount_val '+_discount_val);
			//console.log('_options_max '+_options_max);
			//console.log('options.min '+options.min);
			
			if(options.discount_type == 'percent' && webpoi.numpad_input_box.types.value.value() > 100) {
				_with_error = true;
				_error_msg = 'Discount cannot exceed 100%.';
			}
			
			if (_val.value() < options.min) {
				_with_error = true;
				_error_msg = 'Value is lower than min. set value of ' + options.min + '.';
			}
			
			if (_options_max >= options.min && _val.value() > _options_max && !_with_error) {
				_with_error = true;
				_error_msg = 'Value is bigger than max. set value of ' + _options_max + '.';
			}
			
			if (_options_max >= options.min && amount_value_max > _options_max && !_with_error) {
				_with_error = true;
				_error_msg = 'Value is bigger than max. set value of ' + _options_max + '.';
			}
			
			if (_options_max >= options.min && _discount_val > _options_max && !_with_error) {
				_with_error = true;
				_error_msg = 'Value is bigger than max. set value of ' + _options_max + '.';
			}
			
			if (_options_max == 0 && !_with_error) {
				_with_error = true;
				//_error_msg = 'No discountable products available.';
				_error_msg = 'Discount amount exceeded.';
			}
			
			if (_with_error) {
				$('#numpad-status').html(_error_msg);
				return false;
			}
			
			// check if there is a condition
			if (_.isFunction(options.condition)) {
				if (!options.condition.call(this, _val.value())) {
					_with_error = true;
					if (_.isFunction(options.on_condition_fail)) options.on_condition_fail.call(this, _val.value());
				} else {
					if (_.isFunction(options.on_condition_success)) options.on_condition_success.call(this, _val.value());
				}
				
				if (_with_error) return false;
			}
			
			modal.hide();
			$(document).unbind('keydown');
			$(document).unbind('keyup');
			if(options.discount_type == 'percent') {
				var ret = {
					'set_amount' : webpoi.numpad_input_box.types.amount.value(),
					'set_value' : webpoi.numpad_input_box.types.value.value()
				}
				if (_.isFunction(cb)) cb.call(this, ret);
			} else {
				if (_.isFunction(cb)) cb.call(this, _val.value());
			}
		};
		
		var _value = options.min;
		
		var modal = b.modal.create({
			title: options.title,
			html: _.template($('#numpad-template').html(), {'discount_type': options.discount_type}),
			width: 250,
			buttons : {
				'Ok' : function() {
					_ok_btn();
				}
			},
			onHide : function() {
				$(document).unbind('keydown');
				$(document).unbind('keyup');
				if (_.isFunction(on_hide)) on_hide.call(this, modal);
			}
		});
		modal.show();

		webpoi.numpad_input_box = {
			'hook' : '#numpad-value',
			'currentType' : 'value',
			'types' : {
				'value' : {
					'digits' : (previousValues!=null)?previousValues.value.digits:'0',
					'decimal' : (previousValues!=null)?previousValues.value.decimal:-1,
					'decimalOn' : (previousValues!=null)?previousValues.value.decimalOn:false,
					'value' : function() {
						return parseFloat(parseInt(webpoi.numpad_input_box.types.value.digits) + (parseInt(webpoi.numpad_input_box.types.value.decimal) > 0 ? '.'+webpoi.numpad_input_box.types.value.decimal : ''));
					}
				},
				'amount' : {
					'digits' : (previousValues!=null)?previousValues.amount.digits:'0',
					'decimal' : (previousValues!=null)?previousValues.amount.decimal:-1,
					'decimalOn' : (previousValues!=null)?previousValues.amount.decimalOn:false,
					'value' : function() {
						return parseFloat(parseInt(webpoi.numpad_input_box.types.amount.digits) + (parseInt(webpoi.numpad_input_box.types.amount.decimal) > 0 ? '.'+webpoi.numpad_input_box.types.amount.decimal : ''));
					}
				}
			}
		}

		if(options.discount_type == 'percent') {
			$('.numpad-value').click(function(){
				$(this).css('background-color', '#5fbe60').animate({
					backgroundColor: "transparent"
				}, 1000 );
				webpoi.numpad_input_box.hook = '#numpad-'+$(this).attr('type');
				webpoi.numpad_input_box.currentType = $(this).attr('type');
				_val.digits = webpoi.numpad_input_box.types[$(this).attr('type')].digits;
				_val.decimal = webpoi.numpad_input_box.types[$(this).attr('type')].decimal;
				_decimal_on = webpoi.numpad_input_box.types[$(this).attr('type')].decimalOn;
			});
			$('.numpad-value[type="value"]').css('background-color', '#5fbe60').animate({
				backgroundColor: "transparent"
			}, 1000 );
		}

		if (_.isFunction(on_show)) on_show.call(this, modal);
		$('.numpad-box').append("<input type='text' id='_num_focus' style='position:absolute;top:-1000px;' />");
		$('#_num_focus').focus();
		
		if (options.with_decimal) {
			$('.numpad-item-box[data-value="decimal"]').show();
		} else {
			$('.numpad-item-box[data-value="decimal"]').hide();
		}
		
		if(options.discount_type == 'percent') {
			$('#numpad-value').text(numberFormat(webpoi.numpad_input_box.types.value.value(), parseInt(webpoi.numpad_input_box.types.value.decimal) < 0 ? 0 : String(webpoi.numpad_input_box.types.value.decimal).length));
			$('#numpad-amount').text(numberFormat(webpoi.numpad_input_box.types.amount.value(), parseInt(webpoi.numpad_input_box.types.amount.decimal) < 0 ? 0 : String(webpoi.numpad_input_box.types.amount.decimal).length));
		} else {
			$('#numpad-value').text(numberFormat(_val.value(), parseInt(_val.decimal) < 0 ? 0 : String(_val.decimal).length));
		}
		
		$(document).unbind('keydown');
		$(document).bind('keydown', function (e) {
		    if (e.keyCode === 8) {
		        e.preventDefault();
		    }
			else if (e.keyCode === 13) {
		        e.preventDefault();
				return false
		    }
		});
		
		$(document).unbind('keyup');
		$(document).bind('keyup',function(e) {
			e.preventDefault();
			if (e.keyCode == 13) {
				_ok_btn();
			} else if (e.keyCode >= 48 && e.keyCode <= 57) {
				$('.numpad-item-box[data-value="'+(e.keyCode - 48)+'"]').click();
			} else if (e.keyCode >= 96 && e.keyCode <= 105) {
				$('.numpad-item-box[data-value="'+(e.keyCode - 96)+'"]').click();
			} else if (e.keyCode == 67) {
				$('.numpad-item-box[data-value="clear"]').click();
			} else if (e.keyCode == 8) {
				$('.numpad-item-box[data-value="back"]').click();
			} else if (e.keyCode = 190) {
				$('.numpad-item-box[data-value="decimal"]').click();
			}
		});
		
		// numpad event
		$('.numpad-item-box').click(function(e) {
			e.preventDefault();
			$(webpoi.numpad_input_box.hook).focus();
			
			var val = $(this).data('value');
			
			if (val == 'clear') {
				_val.digits = '0';
				_val.decimal = '-1';
				_decimal_on = false;
			} else if (val == 'back') {
				if (_val.value() > 0) {
					if (_decimal_on) {
						if (_val.decimal.toString().length > 0) {
							_val.decimal = _val.decimal.toString().substring(0,_val.decimal.toString().length-1);
						} 
						if (_val.decimal.toString().length == 0) {
							_val.decimal = '-1';
							_decimal_on = false;
						}
					} else {
						if (_val.digits.toString().length > 0) {
							_val.digits = _val.digits.toString().substring(0,_val.digits.toString().length-1);
						} 
						if (_val.digits.toString().length == 0) _val.digits = '0';
					}
				}
				
			} else if (val == 'decimal') {
				_decimal_on = true;
			} else {
				if (_val.value() < 999999999.9999) {
					if (!_init_value && _decimal_on) {
						if (String(_val.decimal).length < 4) {
							if (_val.decimal == '-1') {
								_val.decimal = String(val);
							} else {
								_val.decimal = String(_val.decimal) + val;
							}
						}
					} else {
						if (_val.digits == '0' || _init_value) {
							_val.digits = String(val);
							_val.decimal = '-1'; 
							_decimal_on = false;
						} else {
							_val.digits = String(_val.digits) + val;
						}
					}
				}
					
			}

			webpoi.numpad_input_box.types[webpoi.numpad_input_box.currentType].digits = _val.digits;
			webpoi.numpad_input_box.types[webpoi.numpad_input_box.currentType].decimal = _val.decimal;
			webpoi.numpad_input_box.types[webpoi.numpad_input_box.currentType].decimalOn = _decimal_on;

			if (_init_value) _init_value = false;
			$(webpoi.numpad_input_box.hook).text(numberFormat(_val.value(), parseInt(_val.decimal) < 0 ? 0 : String(_val.decimal).length));
			
			$(webpoi.numpad_input_box.hook).blur();
		});
		
	};
	
	webpoi.removeProduct = function(product_id) {
		if(trans.products[product_id].item_id == 0) { //possibly a package
			$.each(trans.products[product_id].selected_sub_products, function(index, item){
				var child_product_id = item.child_product_id;
				var child_qty = item.quantity;
				if(webpoi.out_of_stock_items[child_product_id] != undefined) {
					webpoi.out_of_stock_items[child_product_id].requesting_quantity = webpoi.out_of_stock_items[child_product_id].requesting_quantity - child_qty;
					if(webpoi.out_of_stock_items[child_product_id].requesting_quantity <= 0) {
						delete webpoi.out_of_stock_items[child_product_id];
					}
				}
			});
		} else { //just a single product
			if(webpoi.out_of_stock_items[product_id] != undefined) {
				webpoi.out_of_stock_items[product_id].requesting_quantity  = webpoi.out_of_stock_items[product_id].requesting_quantity - trans.products[product_id].quantity;
				if(webpoi.out_of_stock_items[product_id].requesting_quantity <= 0) {
					delete webpoi.out_of_stock_items[product_id];
				}
			}
		}
		//console.log(webpoi.out_of_stock_items.length);
		if(webpoi.out_of_stock_items.length <= 0 || webpoi.out_of_stock_items.length == undefined || webpoi.out_of_stock_items == null) {
			trans.is_override = 0;
		}
		
		//delete product in db if transaction is still PENDING
		if(trans.status == 'PENDING')
		{
			if(trans.products[product_id].item_id == 0){
				var is_package = 1;
			}else {
				var is_package = 0;
			}
			hideLoading();
			b.request({
				url: '/webpoi/delete_item_from_db',
				data: {
					'transaction_id' : trans.transaction_id,
					'product_id': product_id,
					'type': 'item',
					'is_package': is_package
				},
				with_overlay: false,
				on_success: function(data){
					//do
					hideLoading();
				}
			});
		}
		
		delete trans.products[product_id];
		
		$.each(trans.summary.discounts, function(index, item) {
			if (item.type == 'epc_cash' || item.type == 'epc_card') {
				webpoi.removeDiscount(item.type);
			}
		});
		
		webpoi.refreshDisplay();
	};
	
	webpoi.removeFPVVoucher = function(voucher_id) {
		delete trans.fpv_vouchers[voucher_id];
		webpoi.refreshDisplay();
	}
	
	webpoi.removeMPVVoucher = function(voucher_id){
		delete trans.mpv_vouchers[voucher_id];
		webpoi.refreshDisplay();
	}
	
	webpoi.removeP2PVoucher = function(voucher_id){
		delete trans.p2p_vouchers[voucher_id];
		webpoi.refreshDisplay();
	}
	
	webpoi.removeRebateProduct = function(product_id) {
		
		//delete product in db if transaction is still PENDING
		if(trans.status == 'PENDING')
		{
			if(trans.rebate_products[product_id].item_id == 0){
				var is_package = 1;
			}else {
				var is_package = 0;
			}
			hideLoading();
			b.request({
				url: '/webpoi/delete_item_from_db',
				data: {
					'transaction_id' : trans.transaction_id,
					'product_id': product_id,
					'type': 'rebate',
					'is_package': is_package
				},
				with_overlay: false,
				on_success: function(data){
					
				}
			});
		}
		
		delete trans.rebate_products[product_id];
		webpoi.refreshDisplay();
	};
	
	webpoi.addProductVoucher = function(voucher_type, voucher_name, voucher_allow) {
		var member_id = trans.customer.member_id;
		
		if(member_id == undefined || member_id == "" || member_id == 0)
		{
			var error_modal = b.modal.create({
				title: 'No Member Selected',
				width: 300,
				html: 'No Member Selected. Cannot add voucher.',
				disableClose: true,
				buttons: {
					'Close': function(){
						error_modal.hide();
					}
				}
			});
			error_modal.show();
			return;
		}
		
		if(trans.customer.is_account_active == false && voucher_allow == 0) {
			var error_modal = b.modal.create({
				title: 'No Active Accounts',
				width: 300,
				html: 'Member has no active accounts. Member cannot redeem vouchers.',
				disableClose: true,
				buttons: {
					'Close': function(){
						error_modal.hide();
					}
				}
			});
			error_modal.show();
			return;
		}
		
		var mpv_products_modal;
		var voucher_modal = b.modal.create({
			title: 'Select Voucher',
			html: _.template($('#voucher-search-template').html(), {'voucher_type_id': voucher_type, 'member_id': member_id}),
			width: 550,
			disableClose: true,
			buttons: {
				'Close': function() {
					voucher_modal.hide();
				},
				"Add Vouchers": function() {
					$('#btn-add_vouchers').click();
				}
			}
		});
		voucher_modal.show();
		$( ".modal-footer a[id$='btn_add_vouchers']" ).hide(); //hide 'Add Vouchers' button
		
		//$('body').on('click', '#voucher_search_button', function(){
		$('#voucher_search_button').click(function(){
			var member_id = $('#member_id').val();
			var voucher_type_id = $('#voucher_type_id').val();
			var voucher_code = $('#voucher_code').val();
			var selected_vouchers = null;
			
			if (voucher_type_id == 1) {
				var selected_vouchers = trans.fpv_vouchers;
			} else if (voucher_type_id == 2) {
				var selected_vouchers = trans.mpv_vouchers;
			} else if (voucher_type_id == 3) {
				var selected_vouchers = trans.p2p_vouchers;
			}
			$( ".modal-footer a[id$='btn_add_vouchers']" ).hide(); //hide 'Add Vouchers' button
			
			b.request({
				url: '/webpoi/get_member_vouchers',
				data: {
					'member_id': member_id,
					'voucher_type_id': voucher_type_id,
					'voucher_code': voucher_code,
					'selected_vouchers': selected_vouchers
				},
				on_success: function(data){
					if(data.status == 'ok') {
						
						if(data.data.is_empty == true){
							$( ".modal-footer a[id$='btn_add_vouchers']" ).hide(); //hide 'Add Vouchers' button
						}
						else{
							$( ".modal-footer a[id$='btn_add_vouchers']" ).show(); //show 'Add Vouchers' button
						}						
						
						$('div.vouchers_table').html("");
						$('div.vouchers_table').html(data.data.voucher_table);
						
						//$('body').on('click', '#check_all_vouchers', function(){
						$('#check_all_vouchers').click(function(){
							if($(this).attr('checked')) $('.voucher-item').attr('checked',true);
							else $('.voucher-item').attr('checked',false);
						});
						$('.voucher-item').click(function(){
							$('#check_all_vouchers').attr('checked',false);
						});
						
						$('#btn-add_vouchers').click(function(){
							var voucher_type_id = $(this).attr('voucher-type-id');
							var selected_vouchers = new Array();
							var selected_voucher_products = new Object();
			
							//get vouchers
							$('input[name=voucher_items]:checked').each(function() {
								var voucher=new Object();
								voucher.voucher_id=$(this).attr('value');
								voucher.voucher_code=$(this).attr('voucher-code');
								voucher.products=new Object();
								voucher.products_list=new Array();
								voucher.price=0;
								voucher.original_price=0;
				
								if(voucher_type_id == 3) { // P2P
								
									var _voucher_products=JSON.parse($(this).attr('voucher-products'));
									
									
										var products_array = new Array();
										var products_price = 0;
										var original_price = 0;
										
										var selected_products = new Array();
										var selected_products_counter = 0;
										$.each(_voucher_products, function(index, item){
											var products_list = {
												'product_id' : item.product_id,
												'child_product_id' : item.product_id,
												'item_id' : item.item_id,
												'price' : item.price,
												'original_price' : item.original_price,
												'product_name' : item.product_name,
												'quantity' : item.qty
											}
											voucher.products_list.push(products_list);
											products_price += item.price;
											original_price += item.original_price;
											
											//selected products
											var _prod_id = parseInt(item.product_id);
											selected_products[selected_products_counter] = new Array();
											$.each(_voucher_products, function(index, item){
												selected_products[selected_products_counter].push(item);
											});
											selected_products_counter++;
										});
										voucher.products = _.clone(voucher.products_list);
										voucher.price += parseFloat(products_price);
										voucher.original_price += parseFloat(original_price);
									
									/*var _voucher_products=JSON.parse($(this).attr('voucher-products'));
									console.log(_voucher_products);
									$.each(_voucher_products, function(i, item) {
										_voucher_products[i].qty = parseInt(_voucher_products[i].qty);
										voucher.products[item.product_id]=_.clone(_voucher_products[i]);
										var products_list = {
											'product_id' : _voucher_products[i].product_id,
											'child_product_id' : _voucher_products[i].product_id,
											'item_id' : _voucher_products[i].item_id,
											'price' : _voucher_products[i].price,
											'original_price' : _voucher_products[i].original_price,
											'product_name' : _voucher_products[i].product_name,
											'quantity' : _voucher_products[i].qty
										};
										voucher.products_list.push(products_list);
										//add to selected products
										if(item.product_id in selected_voucher_products)
											selected_voucher_products[item.product_id].qty += parseInt(_voucher_products[i].qty);
										else
											selected_voucher_products[item.product_id]=_.clone(_voucher_products[i]);
									});*/
								}
				
							    selected_vouchers.push(voucher);
							});
							console.log(selected_vouchers);
							total_selected_vouchers = selected_vouchers.length;
			
							if(total_selected_vouchers > 0)
							{
								if(voucher_type_id == 3){ //P2P
									webpoi.addVoucherProducts(selected_vouchers, selected_voucher_products, voucher_type_id);
									voucher_modal.hide();
								}
								else if(voucher_type_id == 1 || voucher_type_id == 2){ //FPV/MPV
									b.request({
										url: '/webpoi/get_mpv_products',
										data: {
											'voucher_type_id': voucher_type_id
										},
										on_success: function(data) {
											if(data.status == 'ok') {
											
												mpv_products_modal = b.modal.create({
													title: "Choose (" + total_selected_vouchers + ") from the set of products",
													html: data.data.mpv_products,
													width: 450,
													disableClose: true,
													buttons: {
														'Close': function(){
															mpv_products_modal.hide();
															return;
														},
														'Add Products': function(){
															$('#btn-add_mpv_products').click();
															return;
														}
													}
												});
												mpv_products_modal.show();
							
							
												//$('body').on('click', '#btn-add_mpv_products', function(e){
												$('#btn-add_mpv_products').click(function(e){
													e.preventDefault();
													var total_qty = 0;
		
													$('.voucher-mpv-item').each(function() {
														var val = $(this).attr('value');
														if(val=="") val = 0;
														total_qty += parseInt(val);
													});
		
													if(total_selected_vouchers==total_qty){
														
														var selected_products = new Array();
														var selected_product_group_names = new Array();
														var selected_products_counter = 0;
														$('.voucher-mpv-item').each(function() {
															var _qty = parseInt($(this).attr('value'));
															var _prod_id = parseInt($(this).attr('product-id'));
															var _voucher_products=JSON.parse($(this).attr('voucher-products'));
															var _voucher_product_group_name=$(this).attr('voucher-product-group-name');
															
															for(var i=0; i<_qty; i++)
															{
																selected_product_group_names[selected_products_counter] = new Array();
																selected_product_group_names[selected_products_counter].push(_voucher_product_group_name);
																
																selected_products[selected_products_counter] = new Array();
																$.each(_voucher_products, function(index, item){
																	selected_products[selected_products_counter].push(item);
																});
																selected_products_counter++;
															}
														});
														for(var i=0; i<total_selected_vouchers; i++)
														{
															var products_array = new Array();
															var products_price = 0;
															var original_price = 0;
															selected_vouchers[i].products_list = new Array();
															$.each(selected_products[i], function(index, item){
																var products_list = {
																	'product_id' : item.product_id,
																	'child_product_id' : item.product_id,
																	'item_id' : item.item_id,
																	'price' : item.price,
																	'original_price' : item.original_price,
																	'product_name' : item.product_name,
																	'quantity' : item.qty
																}
																	//products_array.push(products_list);
																selected_vouchers[i].products_list.push(products_list);
																//selected_vouchers[i].products = _.clone(products_list);
																products_price += item.price;
																original_price += item.original_price;
															});
															selected_vouchers[i].products = _.clone(selected_vouchers[i].products_list);
															
															selected_vouchers[i].product_group_name = selected_product_group_names[i];
															
															//selected_vouchers[i].products_list.push(products_array);
															selected_vouchers[i].price += parseFloat(products_price);
															selected_vouchers[i].original_price += parseFloat(original_price);
															
															//console.log(selected_vouchers[i]);
															
															//add to selected products
															if(selected_products[i].product_id in selected_voucher_products)
																selected_voucher_products[selected_products[i].product_id].qty += parseInt(selected_products[i].qty);
															else
																selected_voucher_products[selected_products[i].product_id]=_.clone(selected_products[i]);
														}
														voucher_modal.hide();
														mpv_products_modal.hide();
														webpoi.addVoucherProducts(selected_vouchers, selected_voucher_products, voucher_type_id);
													}
													else
													{
														var error_msg='';
														var error_html='';
			
														if(total_qty<total_selected_vouchers){
															var tmp = (total_selected_vouchers-total_qty);
															error_html = "Kindly select a total of " + tmp + " more product" + (tmp>1 ? 's':'');
														}
														else
														{
															var tmp = (total_qty-total_selected_vouchers);
															error_html = "Kindly remove a total of " + tmp + " product" + (tmp>1 ? 's':'');
														}
			
														var error_modal = b.modal.create({
															title: "Error",
															width: 300,
															html: "<p>" + error_html + "</p>",
															disableClose: true,
															buttons: {
																"Okay": function(){
																	error_modal.hide();
																}
															}
														});
														error_modal.show();
													}
												});
											}
											else if (data.status == 'error'){
												var error_modal = b.modal.create({
													title: 'Error in Request',
													html: data.msg,
													width: 300,
												});
												error_modal.show();
											}
										}
									});
								}
							}
							else
							{
								var error_modal = b.modal.create({
									title: "Error",
									width: 300,
									html: "<p>Kindly select at least one voucher.</p>",
									disableClose: true,
									buttons: {
										"Okay": function(){
											error_modal.hide();
										}
									}
								});
								error_modal.show();
							}
			
						});
						
					} else if (data.status == 'error') {
						var error_modal = b.modal.create({
							title: 'Error in request.',
							html: data.msg,
							width: 300
						});
						error_modal.show();
					}
				}
			});
		});
		
		
		
		
		
		//numbers only
		$('body').on('keydown', '.voucher-mpv-item', function(event){
		    // Allow: backspace, delete, tab, escape, and enter
		    if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
		         // Allow: Ctrl+A
		        (event.keyCode == 65 && event.ctrlKey === true) || 
		         // Allow: home, end, left, right
		        (event.keyCode >= 35 && event.keyCode <= 39)) {
		             // let it happen, don't do anything
		             return;
		    }
		    else {
		        // Ensure that it is a number and stop the keypress
		        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
		            event.preventDefault(); 
		        }   
		    }
		});
		
		/*$('body').on('click', '.btn_add_fpv', function(){
			var voucher_id = $(this).parent().parent().data('voucher_id');
			var voucher_code = $(this).parent().parent().data('voucher_code');
			var voucher_products = $(this).parent().parent().data('voucher_products');
			var voucher_type_id = $('#voucher_type_id').val();
			
			webpoi.addVoucherProducts(voucher_id, voucher_code, voucher_products, voucher_type_id);
			voucher_modal.hide();
		});*/
		
		/*$('body').on('click', '.btn_add_mpv', function(){
			var voucher_id = $(this).parent().parent().data('voucher_id');
			var voucher_code = $(this).parent().parent().data('voucher_code');
			var voucher_type_id = $('#voucher_type_id').val();
			
			b.request({
				url: '/webpoi/get_mpv_products',
				data: {
					'voucher_id': voucher_id,
					'voucher_code': voucher_code,
					'voucher_type_id': voucher_type_id
				},
				on_success: function(data) {
					if(data.status == 'ok') {
						mpv_products_modal = b.modal.create({
							title: "Select Product",
							html: data.data.mpv_products,
							width: 450
						});
						mpv_products_modal.show();
					}
				}
			});
		});*/
		
		/*$('body').on('click', '.btn-add-mpv-product', function(){
			var voucher_id = $(this).parent().parent().data('voucher_id');
			var voucher_code = $(this).parent().parent().data('voucher_code');
			var voucher_type_id = $('#voucher_type_id').val();
			var voucher_products = $(this).parent().parent().data('voucher_products');

			webpoi.addVoucherProducts(voucher_id, voucher_code, voucher_products, voucher_type_id);
			voucher_modal.hide();
			mpv_products_modal.hide();
		});*/
		
	}
	
	webpoi.addVoucherProducts = function(vouchers, voucher_products, voucher_type_id) {
		//var voucher_products_list = [];
		var _out_stocked_products = [];
		var total_price = 0;
		
		webpoi.getFacilityItems(function() {

			var _out_stocked_products = [];
			$.each(voucher_products, function(index, prod){
				$.each(prod, function(index, item){
					var product_id = item.product_id;
					var product_name = item.product_name;
					var quantity = item.qty;
					var item_id = item.item_id;
					//total_price += price;

					//check if out of stock
					var ret = webpoi.isInStock(item_id, quantity);
					if (!ret.result) {
						_out_stocked_products.push({'product_id' : product_id, 'available_quantity' : ret.quantity, 'requesting_quantity' : quantity});
					}

					/*var products_list = {
						'product_id' : product_id,
						'child_product_id' : product_id,
						'item_id' : item_id,
						'price' : price,
						'product_name' : product_name,
						'quantity' : quantity
					};
					voucher_products_list.push(products_list);*/
				});
			});
			if (_out_stocked_products.length  > 0) {
				////console.log(_out_stocked_products);
				var out_of_stock_modal = b.modal.create({
					title: "Product Stock Check",
					html: _.template($('#out-of-stock-dialog-template').html(), {'products' : _out_stocked_products}),
					width: 400,
					disableClose: true,
					buttons: {
						'Close': function(){
							out_of_stock_modal.hide();
							return;
						},
						'Override': function(){
							webpoi.checkAuthority("Override Transactions","AUTHORIZE-OVERRIDE",function(allow) {
								if(allow)
								{
									var override_modal = b.modal.create({
										title: "Override Transaction",
										html: "<div><span>Please Enter Remarks:</span><textarea class='override_remarks' name='override_remarks' style='width:270px;'></textarea><span id='override_remarks_error' class='label label-important' style='display:none;'>Remarks cannot be empty.</span></div>",
										width: 310,
										disableClose : true,
										buttons : {
											'Cancel' : function() {
												override_modal.hide();
												return;
											},
											'Confirm' : function() {
												var override_remarks = $('.override_remarks').val();
												webpoi.override_remarks = override_remarks;
												if(override_remarks == ""){
													$('#override_remarks_error').show();
												}else{
													trans.is_override = 1;
													override_modal.hide();
													out_of_stock_modal.hide();
												
													$.each(_out_stocked_products, function(index, item){
														if(webpoi.out_of_stock_items[item.product_id] != undefined) {
															webpoi.out_of_stock_items[item.product_id].requesting_quantity = webpoi.out_of_stock_items[item.product_id].requesting_quantity + item.requesting_quantity;
														}else {
															webpoi.out_of_stock_items[item.product_id] = item;
														}
													});
													webpoi.addVoucherContinue(vouchers, voucher_type_id);
												}
											}
										}
									});
									override_modal.show();
								}
							});
						}
					}
				});
				out_of_stock_modal.show();
				return;
			} 
			// if there are no errors, will continue here
			webpoi.addVoucherContinue(vouchers, voucher_type_id);
		});
	}
	
	webpoi.addVoucherContinue = function(vouchers, voucher_type_id){
		$.each(vouchers, function(index, item){
			var voucher = {
				'product_id' : item.voucher_id,
				'product_name' : item.voucher_code,
				'quantity' : 1,
				'price' : item.price,
				'original_price' : item.original_price,
				'selected_sub_products' :  item.products_list,
				'selected_sub_products_name' :  item.product_group_name,
				'is_vatable': 0
			};
			if(voucher_type_id == 1) //FPV
				trans.fpv_vouchers[item.voucher_id] = voucher;
			else if(voucher_type_id == 2) //MPV
				trans.mpv_vouchers[item.voucher_id] = voucher;
			else if(voucher_type_id == 3) //P2P
				trans.p2p_vouchers[item.voucher_id] = voucher;
		});
		
		webpoi.refreshDisplay();
	}
	
	webpoi.addDiscount = function(discount_type, discount_name) {
		
		var _payment_types = [];
		$.each(trans.summary.payments, function(index, item) {
			_payment_types.push(item.type);
		});
		
		_payment_types = _.uniq(_payment_types);
		is_gc_present = _.indexOf(_payment_types, 'giftcheque') != -1;
		is_ogc_present = _.indexOf(_payment_types, 'onlinegiftcheque') != -1;
		
		if (is_gc_present || is_ogc_present) {
			var _title = "Gift-Cheque";
			if (is_ogc_present) _title = "Online Gift-Cheque";
			b.modal.create({
				title: "Add Discount Error!",
				html: "Sorry, you can't use discounts with '<strong>"+_title+"</strong>'.",
				width: 310,
			}).show();
			
			return false;
		}
		
		var title = 'Amount to Discount';
		
		// selected discount
		if (discount_type == 'epc_cash' || discount_type == 'epc_card') {
			// if current selected member is epc
			if (!webpoi.isCustomerMemberType(2)) {
				b.modal.create({
					title: "Add Discount Error!",
					html: "<p style='font-size:14px;'>You can't add this discount. No EPC member was selected for this transaction.</p>",
					width: 400,
				}).show();
				return;
			}
			
			if(trans.summary.sub_total < webpoi.settings.epc_min_amount)
			{
				b.modal.create({
					title: "EPC Discount",
					html: "<p style='font-size:14px;'>This transaction has not yet reached the <strong>P"+numberFormat(webpoi.settings.epc_min_amount,2)+"</strong> minimum for EPC discounts.</p>",
					width: 350
				}).show();
				return;
			}
		}
		
		if (discount_type == 'percent') title = 'Percent to Discount';
		
		var current_amount1 = 0;
		var current_amount2 = 0;
		if (typeof(trans.summary.discounts[discount_type]) != 'undefined') {
			current_amount1 = trans.summary.discounts[discount_type].value;
			current_amount2 = trans.summary.discounts[discount_type].amount_to_discount;
		}

		if (discount_type == 'percent') {
			current_amount1  = {
				'current_amount1' : current_amount1,
				'current_amount2' : current_amount2
			}
		}
		
		var vouchers_total = trans.summary.mpv_total + trans.summary.fpv_total + trans.summary.p2p_total;
		var max_discount = webpoi.transaction_details.amount_due - vouchers_total;
		
		if (discount_type == 'epc_cash' || discount_type == 'epc_card')
		{
			max_discount = trans.summary.net_max_discount;
                        
			var same_discount = false;
			$.each(trans.summary.discounts, function(index, item) {
				if(discount_type == item.type) 
				{
					same_discount = true;
					return false;
				}
				if (item.type == 'epc_cash' || item.type == 'epc_card') {
					max_discount -= item.amount_to_discount;
				}
			});
			
			if(same_discount)
			{
				b.modal.create({
					title: "EPC Discount :: Error",
					width: 350,
					html: "<p>You already have this <strong>EPC Discount</strong> in the discounts list. Please remove it first before re-entering this <strong>EPC discount</strong>.</p>"
				}).show();
				return;
			}
			
			current_amount1 = max_discount;
			if(max_discount == 0)
			{
				b.modal.create({
					title: "EPC Discount :: Error",
					width: 350,
					html: "<p>The discountable amount is already <strong>zero</strong>. Please remove existing <strong>EPC discounts</strong> if you would like to adjust the discounts.</p>"
				}).show();
				return;
			}
		}
		
		webpoi.showNumpad({'title' : title, 'default' : current_amount1, 'with_decimal' : true, 'min' : 1, 'max' : max_discount, 'discount_type' : discount_type}, function(set_value) {
			
			if (discount_type == 'percent') 
			{
				
					if (typeof(trans.summary.discounts[discount_type]) != 'undefined') {
						trans.summary.discounts[discount_type].amount_to_discount = set_value.set_amount;
						trans.summary.discounts[discount_type].value = set_value.set_value;
					} else {
						trans.summary.discounts[discount_type] = {
							'type' : discount_type,
							'name' : discount_name,
							'amount_to_discount' : set_value.set_amount,
							'value' :  set_value.set_value,
						};
					}

					webpoi.refreshDisplay();				
				
			}
			else if (discount_type == 'epc_cash' || discount_type == 'epc_card') 
			{
				// if current selected member is epc
				if (!webpoi.isCustomerMemberType(2)) {
					b.modal.create({
						title: "Add Discount Error!",
						html: "<p style='font-size:14px;'>You can't add this discount. No EPC member was selected for this transaction.</p>",
						width: 400,
					}).show();
					return;
				}

				if (discount_type == 'epc_cash') {
					_value = webpoi.settings.epc_discount * 100;
				} else if (discount_type == 'epc_card') {
					_value = (webpoi.settings.epc_discount - webpoi.settings.credit_card_and_cheque_discount_reduction) * 100;
				}
				if(trans.summary.sub_total >= webpoi.settings.epc_min_amount)
				{
					trans.summary.discounts[discount_type] = {
						'type' : discount_type,
						'name' : discount_name,
						'amount_to_discount' : set_value,
						'value' :  _value,
					};
				}
				else
				{
					b.modal.create({
						title: "EPC Discount",
						html: "<p style='font-size:14px;'>This transaction has not yet reached the <strong>P"+numberFormat(webpoi.settings.epc_min_amount,2)+"</strong> minimum for EPC discounts.</p>",
						width: 350
					}).show();
					return;
				}

				webpoi.refreshDisplay();
			} else {
				
				if (typeof(trans.summary.discounts[discount_type]) != 'undefined') {
					if (discount_type == 'amount') {
						trans.summary.discounts[discount_type].value = set_value;
					} else {
						trans.summary.discounts[discount_type].amount_to_discount = set_value;
					}
					
				} else {
					var _value = set_value;
					var _amount_to_discount = 0;
					/*if (discount_type == 'epc_cash') {
						_value = webpoi.settings.epc_discount * 100;
						_amount_to_discount = set_value;
					} else if (discount_type == 'epc_card') {
						_amount_to_discount = set_value;
						_value = (webpoi.settings.epc_discount - webpoi.settings.credit_card_and_cheque_discount_reduction) * 100;
					}*/
					
					trans.summary.discounts[discount_type] = {
						'type' : discount_type,
						'name' : discount_name,
						'amount_to_discount' : _amount_to_discount,
						'value' :  _value,
					};
				}

				webpoi.refreshDisplay();
				
			}

		},
		function(numpad_modal) { // on show
			if (discount_type == 'epc_cash' || discount_type == 'epc_card')
			{				
				var pos = $('#'+numpad_modal.id).position();
				var width = $('#'+numpad_modal.id).width();
				var zIndex = $('#'+numpad_modal.id).css('z-index');

				var _left = pos.left + width + 5;

				var _amount_due_html = "<span class='well' style='line-height: 30px; display: block; padding: 10px;font-size: 18px;'>"+_.numberFormat(max_discount, 2)+"</span>";
			
				$('body').append(webpoi.renderPopups('add-payment-amount-due', _left, 'Discountable Amount:', _amount_due_html, zIndex));
			}
		},
		function(numpad_modal) { // on hide
			if (discount_type == 'epc_cash' || discount_type == 'epc_card')
			{
				$('#add-payment-amount-due').remove();
			}
		});
		
	};
	
	webpoi.removeDiscount = function(index) {
		
		if (typeof(trans.summary.discounts[index]) != 'undefined') {
			delete trans.summary.discounts[index];
			webpoi.refreshDisplay();
		}
		
	};
	
	webpoi.addPayment = function(payment_type, is_disable) {
	
		if (is_disable == 1) {
		
			b.modal.create({
				title: "Add Payment Error!",
				html: "<p>Sorry, purchase using <strong>" + payment_type + "</strong> is currently unavailable. Please contact the IT Department at 631-1899 or 0917-5439586.</p>",
				width: 310,
			}).show();
			//modal.hide();
			return false;
		} 
		
		if(payment_type == 'cash') {
			if(webpoi.is_gc_exclusive == 1) {
				var exclusive_modal  = b.modal.create({
					title: "GC Exclusive Products present",
					html: "<p>You cannot use CASH for this transaction. If you wish to use CASH payment, please remove the GC Exclusive products.</p>",
					width: 360,
				});
				exclusive_modal.show();
				return;
			}
		}
		
		var _payment_modal = function(payment_type, cb) {
			
			var payment_state = 1;
			var current_state = 0;
			var _show_state = function() {
				if (payment_state != current_state) {
					current_state = payment_state;
					$('.add-payment-container-boxes').hide();
					if (current_state == 1 && payment_type == 'card') {
						$('#add-payment-card-details').show();
					} else if (current_state == 1 && payment_type == 'cheque') {
						$('#add-payment-cheque-details').show();
					} else if (current_state == 1 && (payment_type == 'funds' || payment_type == 'onlinegiftcheque' || payment_type == 'gcep' || payment_type == 'cpoints')) {
						$('#add-payment-amount-summary').show();
					} else if (current_state == 1 && (payment_type == 'giftcheque')) {
						$('#add-payment-gc-details').show();
					}
				}
			};
			
			var _title = '';
			
			if (payment_type == 'card') _title = 'Credit Card';
			if (payment_type == 'cheque') _title = 'Cheque';
			if (payment_type == 'funds') _title = 'Funds';
			if (payment_type == 'onlinegiftcheque') _title = 'Online Gift Cheque';
			if (payment_type == 'giftcheque') _title = 'Gift Cheque';
			if (payment_type == 'gcep') _title = 'GCEP';
			if (payment_type == 'cpoints') _title = 'C Points';
			
			var _funds_amount = 0;
			var _ogc_amount = 0;
			var _gc_amount = 0;
			var _gcep_amount = 0;
			var _cpoints_amount = 0;

			var modal = b.modal.create({
				title: "Add Payment - " + _title,
				html: _.template($('#add-payment2-template').html(), {'payment_types' : webpoi.settings.payment_methods}),
				width: 350,
				buttons : {
					'Ok' : function() {

						if (current_state == 1) {

							var _with_error = false;
							// process selected payment method and details
							if (payment_type == 'card') {
								
								$('.payment-status').hide();
								$('.payment-status').html('');
								
								// check field values
								var _payment_details = {
									'payment_type' : payment_type,
									reference_no : '',
									reference_detail : '',
									first_name : '',
									middle_name : '',
									last_name : '',
									amount : 0,
								};
								var _card_bank_name = _.clean($('#payment_card_bank_name').val());
								var _card_type = _.clean($('#payment_card_type').val());
								var _card_number = _.clean($('#payment_card_number').val());
								var _card_exp_date = _.clean($('#payment_card_exp_date').val());
								var _card_trace_number = _.clean($('#payment_card_trace_number').val());
								var _card_first_name = _.clean($('#payment_card_first_name').val());
								var _card_last_name = _.clean($('#payment_card_last_name').val());
								
								if (_card_bank_name.length == 0) {
									_with_error = true;
									$('#payment_card_bank_name-status').html('Bank name is required!').css({'display' : 'block'}).show();
								}
								
								if (_card_number.length == 0) {
									_with_error = true;
									$('#payment_card_number-status').html('Credit Card No. is required!').css({'display' : 'block'}).show();
								}
								
								if (_card_trace_number.length == 0) {
									_with_error = true;
									$('#payment_card_trace_number-status').html('Trace Number is required!').css({'display' : 'block'}).show();
								}
								
								if (_card_exp_date.length == 0) {
									_with_error = true;
									$('#payment_card_exp_date-status').html('Expiration Date is required!').css({'display' : 'block'}).show();
								} else {
									// check if value is proper format
									var _regex = new RegExp("[0-9]{2}/[0-9]{4}");
									if (_card_exp_date.match(_regex)) {
										var _tmp = _card_exp_date.split('/');
										if (_tmp.length < 2) {
											_with_error = true;
										} else {
											var _tmp_date = _.join('/', _tmp[0], "01", _tmp[1]);
											_with_error = !b.isValidDate(_tmp_date);
										}
									} else {
										_with_error = true;
									}
									
									
									if (_with_error) {
										$('#payment_card_exp_date-status').html('Invalid format!').css({'display' : 'block'}).show();
									}
								}
								
								if (_card_first_name.length == 0) {
									_with_error = true;
									$('#payment_card_first_name-status').html('First Name is required!').css({'display' : 'block'}).show();
								}
								
								if (_card_last_name.length == 0) {
									_with_error = true;
									$('#payment_card_last_name-status').html('Last Name is required!').css({'display' : 'block'}).show();
								}
								
								if (!_with_error) {
									modal.hide();
									
									 _payment_details = {
										'payment_type' : payment_type,
										reference_no : _card_number,
										reference_detail : _card_bank_name + ', ' + _card_type + ', ' + _card_exp_date + ', Trace No: ' + _card_trace_number,
										first_name : _card_first_name,
										middle_name : '',
										last_name : _card_last_name,
										amount : 0,
									};
									
									if (_.isFunction(cb)) cb.call(this, payment_type, _payment_details);
									return false;
								}
								
							} else if (payment_type == 'cheque') {
								
								$('.payment-status').hide();
								$('.payment-status').html('');
								
								// check field values
								var _payment_details = {
									'payment_type' : payment_type,
									reference_no : '',
									reference_detail : '',
									first_name : '',
									middle_name : '',
									last_name : '',
									amount : 0,
								};
								var _cheque_bank_name = _.clean($('#payment_cheque_bank_name').val());
								var _cheque_number = _.clean($('#payment_cheque_number').val());
								var _cheque_exp_date = _.clean($('#payment_cheque_date').val());
								var _cheque_first_name = _.clean($('#payment_cheque_first_name').val());
								var _cheque_last_name = _.clean($('#payment_cheque_last_name').val());
								
								if (_cheque_bank_name.length == 0) {
									_with_error = true;
									$('#payment_cheque_bank_name-status').html('Bank name is required!').css({'display' : 'block'}).show();
								}
								
								if (_cheque_number.length == 0) {
									_with_error = true;
									$('#payment_cheque_number-status').html('Cheque No. is required!').css({'display' : 'block'}).show();
								}
								
								if (_cheque_exp_date.length == 0) {
									_with_error = true;
									$('#payment_cheque_date-status').html('Date is required!').css({'display' : 'block'}).show();
								} else {
									// check if value is proper format
									var _regex = new RegExp("[0-9]{2}/[0-9]{2}/[0-9]{4}");
									if (_cheque_exp_date.match(_regex)) {
										_with_error = !b.isValidDate(_cheque_exp_date);
									} else {
										_with_error = true;
									}
									
									
									if (_with_error) {
										$('#payment_cheque_date-status').html('Invalid format!').css({'display' : 'block'}).show();
									}
								}
								
								if (_cheque_first_name.length == 0) {
									_with_error = true;
									$('#payment_cheque_first_name-status').html('First Name is required!').css({'display' : 'block'}).show();
								}

								if (_cheque_last_name.length == 0) {
									_with_error = true;
									$('#payment_cheque_last_name-status').html('Last Name is required!').css({'display' : 'block'}).show();
								}
								
								if (!_with_error) {
									modal.hide();
									
									_payment_details = {
										'payment_type' : payment_type,
										reference_no : _cheque_number,
										reference_detail : _cheque_bank_name + ', ' + _cheque_exp_date,
										first_name : _cheque_first_name,
										middle_name : '',
										last_name : _cheque_last_name,
										amount : 0,
									};
									
									if (_.isFunction(cb)) cb.call(this, payment_type, _payment_details);
									return false;
								}
								
							} else if (payment_type == 'funds') {

								if (_funds_amount == 0) {

									var _error = b.modal.create({
										title: "Add Payment Error!",
										html: "<p>No funds available.</p>",
										width: 310,
									});
									_error.show();

									return false;
								}

								var _payment_details = {
									'payment_type' : payment_type,
									reference_no : '',
									reference_detail : '',
									first_name : '',
									middle_name : '',
									last_name : '',
									amount : _funds_amount,
								};

								if (_.isFunction(cb)) {
									modal.hide();
									cb.call(this, payment_type, _payment_details);
									return false;
								} 

							} else if (payment_type == 'gcep') {
								if(_gcep_amount == 0) {
									b.modal.create({
										title: "Add Payment Error!",
										html: "<p>No GCEP Available.</p>",
										width: 310,
									}).show();
									return false;
								}
								
								var _payment_details = {
									'payment_type' : payment_type,
									reference_no : '',
									reference_detail : '',
									first_name : '',
									middle_name : '',
									last_name : '',
									amount : _gcep_amount,
								};

								if (_.isFunction(cb)) {
									modal.hide();
									cb.call(this, payment_type, _payment_details);
									return false;
								}
							} else if (payment_type == 'cpoints') {
								if(_cpoints_amount == 0) {
									b.modal.create({
										title: "Add Payment Error!",
										html: "<p>No C Points Available.</p>",
										width: 310,
									}).show();
									return false;
								}
								
								var _payment_details = {
									'payment_type' : payment_type,
									reference_no : '',
									reference_detail : '',
									first_name : '',
									middle_name : '',
									last_name : '',
									amount : _cpoints_amount,
								};

								if (_.isFunction(cb)) {
									modal.hide();
									cb.call(this, payment_type, _payment_details);
									return false;
								}
							} else if (payment_type == 'onlinegiftcheque') {

								if (_ogc_amount == 0) {

									b.modal.create({
										title: "Add Payment Error!",
										html: "<p>No online Gift-Cheque available.</p>",
										width: 310,
									}).show();

									return false;
								}

								var _payment_details = {
									'payment_type' : payment_type,
									reference_no : '',
									reference_detail : '',
									first_name : '',
									middle_name : '',
									last_name : '',
									amount : _ogc_amount,
								};

								if (_.isFunction(cb)) {
									modal.hide();
									cb.call(this, payment_type, _payment_details);
									return false;
								}

							} else if (payment_type == 'giftcheque') {
								
								$('.payment-status').hide();
								$('.payment-status').html('');
								
								// check field values
								var _payment_details = {
									'payment_type' : payment_type,
									reference_no : _.clean($('#gc_series').val()),
									reference_detail : _.clean($('#gc_quantity').val()),
									first_name : '',
									middle_name : '',
									last_name : '',
									amount : 0,
								};
								
								if (_payment_details.reference_no.length == 0) {
									_with_error = true;
									$('#gc_series-status').html('Gift Cheques is Required!').css({'display' : 'block'}).show();
								}
								
								if (_payment_details.reference_detail.length == 0 || _payment_details.reference_detail == '0') {
									_with_error = true;
									$('#gc_quantity-status').html('Quantity should not be blank or 0.').css({'display' : 'block'}).show();
								}
								
								if (!_with_error) {
									var _val = parseInt($('#ddPaymentGCDenomination').val());
									_payment_details.amount = _updateGCAmount();
									_payment_details.reference_detail = _payment_details.reference_detail + ' x ' + _.numberFormat(_val,2);
									if (_.isFunction(cb)) {
										modal.hide();
										cb.call(this, payment_type, _payment_details);
										return false;
									}
								}
								
							}
							
						} 
						
						_show_state();
					}
				}
			});
			modal.show();
			
			// initialize add payment
			
			if ((payment_type == 'giftcheque' || payment_type == 'onlinegiftcheque' || payment_type == 'gcep') && _.size(trans.summary.discounts) > 0) {
				var _title = "Gift-Cheque";
				if (payment_type == 'onlinegiftcheque') _title = "Online Gift-Cheque";
				if (payment_type == 'gcep') _title = "GCEP";
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You cannot use '<strong>"+_title+"</strong>' for this transaction.<br/>Please remove discount(s) to use '<strong>"+_title+"</strong>' as a payment method.</p>",
					width: 380,
				}).show();
				
				return false;
			}
			
			var _payment_types = [];
			$.each(trans.summary.payments, function(index, item) {
				_payment_types.push(item.type);
			});
			
			_payment_types = _.uniq(_payment_types);
			is_gc_present = _.indexOf(_payment_types, 'giftcheque') != -1;
			is_ogc_present = _.indexOf(_payment_types, 'onlinegiftcheque') != -1;
			is_gcep_present = _.indexOf(_payment_types, 'gcep') != -1;
			is_cpoints_present = _.indexOf(_payment_types, 'cpoints') != -1;
			var _rate_id = parseInt($('#ddRates').val());
			
			/*if (is_gc_present && payment_type != 'giftcheque') {
				
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You can only use Gift-Cheques for this transaction.<br/>If you wish to use other payment methods please remove the previously added Gift-Cheque payment.</p>",
					width: 310,
				}).show();
				modal.hide();
				return false;
				
			} else if (!is_gc_present && _payment_types.length > 0 && payment_type == 'giftcheque') {
				
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You cannot use Gift-Cheques for this transaction.<br/>If you wish to use Gift-Cheques please remove the previously added payments.</p>",
					width: 310,
				}).show();
				modal.hide();
				return false;
			}
			
			if (is_ogc_present && payment_type != 'onlinegiftcheque') {
				
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You can only use Online Gift-Cheques for this transaction.<br/>If you wish to use other payment methods please remove the previously added Online Gift-Cheque payment.</p>",
					width: 360,
				}).show();
				modal.hide();
				return false;
				
			} else if (!is_ogc_present && _payment_types.length > 0 && payment_type == 'onlinegiftcheque') {
				
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You cannot use Online Gift-Cheques for this transaction.<br/>If you wish to use Online Gift-Cheques please remove the previously added payments.</p>",
					width: 360,
				}).show();
				modal.hide();
				return false;
			}
			
			if (is_gcep_present && payment_type != 'gcep') {
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You can only use GCEP for this transaction.<br/>If you wish to use other payment methods please remove the previously added payments.</p>",
					width: 360,
				}).show();
				modal.hide();
				return false;
			} else if (!is_gcep_present && _payment_types.length > 0 && payment_type == 'gcep') {
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You cannot use GCEP for this transaction.<br/>If you wish to use GCEP please remove the previously added payments.</p>",
					width: 360,
				}).show();	
				modal.hide();
				return false;
			}*/
			
			if(is_cpoints_present && payment_type != 'cpoints') {
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You can only use C Points for this transaction.<br/>If you wish to use other payment methods please remove the previously added payments.</p>",
					width: 360,
				}).show();
				modal.hide();
				return false;
			} else if (!is_cpoints_present && _payment_types.length > 0 && payment_type == 'cpoints') {
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>You cannot use C Points for this transaction.<br/>If you wish to use C Points please remove the previously added payments.</p>",
					width: 360,
				}).show();	
				modal.hide();
				return false;
			}
			
			if ((payment_type == 'onlinegiftcheque' || payment_type == 'giftcheque' || payment_type == 'gcep') && _rate_id != 1 ) {
				var _title = "Gift-Cheques";
				if (is_ogc_present) _title = "Online Gift-Cheque";
				b.modal.create({
					title: "Add Payment Error!",
					html: "<p>To use "+_title+" you need to set<br/>the rate to '<strong>Retail Price</strong>'.</p>",
					width: 310,
				}).show();
				modal.hide();
				return false;
			}

			if (payment_type == 'cash') {
				var _payment_details = {
					'payment_type' : payment_type,
					reference_no : '',
					reference_detail : '',
					first_name : '',
					middle_name : '',
					last_name : '',
					amount : 0,
				};
				
				modal.hide();
				if (_.isFunction(cb)) cb.call(this, payment_type, _payment_details);
				return false;
			} else if (payment_type == 'card') {
				$('.payment-status').html('');
				$('.payment-status').hide();
			} else if (payment_type == 'cheque') {
				$('.payment-status').hide();
				$('.payment-status').html('');
			} else if (payment_type == 'funds' || payment_type == 'onlinegiftcheque' || payment_type == 'gcep') {
				
				$('#payment-amount-current').html('0.00');
				
				if (payment_type == 'funds') {
					$('#payment-amount-title').html('Funds:');
				} else if (payment_type == 'gcep') {
					$("#payment-amount-title").html('GCEP:');
				} else {
					$('#payment-amount-title').html('Online Gift-Cheque:');
				}
				
				if (trans.customer.member_id.length == 0 || trans.customer.member_id == 0) {

					b.modal.create({
						title: "Add Payment Error!",
						html: "<p>No member currently selected!</p>",
						width: 310,
					}).show();
					modal.hide();
					return false;
				}
				
				b.request({
					'with_overlay': true,
					url: '/webpoi/get_member_funds',
					data: {'member_id' : trans.customer.member_id, },
					on_success: function(data, status) {
						
						if (data.status == 'ok') {
							_funds_amount = parseFloat((parseFloat(String(data.data.funds))).toFixed(2));
							_ogc_amount = parseInt(String(data.data.gift_cheques));
							_gcep_amount = parseInt(String(data.data.gcep));
							if (payment_type == 'funds') {
								$('#payment-amount-current').html(_.numberFormat(_funds_amount, 2));
							} else if (payment_type == 'gcep') {
								$('#payment-amount-current').html(_.numberFormat(_gcep_amount, 0));
							} else {
								$('#payment-amount-current').html(_.numberFormat(_ogc_amount, 0));
							}
							
							
						} else {
							b.modal.create({
								title: "Add Payment Error!",
								html: "<p>Connection Error!<br/>Please try again in awhile.</p>",
								width: 310,
							}).show();
						}

					}
				});
				
			} else if (payment_type == 'giftcheque') {
				
				// check 
				
			} else if (payment_type == 'cpoints') {
				
				$('#payment-amount-current').html('0.00');
				
				$('#payment-amount-title').html('C Points:');
				
				if (trans.customer.member_id.length == 0 || trans.customer.member_id == 0) {

					b.modal.create({
						title: "Add Payment Error!",
						html: "<p>No member currently selected!</p>",
						width: 310,
					}).show();
					modal.hide();
					return false;
				}
				
				b.request({
					'with_overlay': true,
					url: '/webpoi/get_member_funds',
					data: {'member_id' : trans.customer.member_id, },
					on_success: function(data, status) {
						
						if (data.status == 'ok') {
							_cpoints_amount = parseInt(String(data.data.cpoints));
							console.log(_cpoints_amount);
							$('#payment-amount-current').html(_.numberFormat(_cpoints_amount, 2));
						} else {
							b.modal.create({
								title: "Add Payment Error!",
								html: "<p>Connection Error!<br/>Please try again in awhile.</p>",
								width: 310,
							}).show();
						}

					}
				});
				
			}
			
			// clear values
			$('#payment_reference_no').val('');
			$('#payment_reference_detail').val('');
			$('#payment_first_name').val('');
			$('#payment_last_name').val('');
			
			$('#gc_quantity').val('0');
			$('#gc_series').val('');
			
			// end initialize add payment
			_show_state();

			// update modal button styles
			$('#'+modal.id+'_btn_next').removeClass('btn-primary').addClass('btn-success').css({'float':'left'});
			$('#'+modal.id+'_btn_prev').removeClass('btn-primary').css({'float':'left'});
			
			// place current amount due
			$('.payment-amount-due').html(numberFormat(webpoi.transaction_details.amount_due, 2));
			
			beyond.numericalPad('#gc_quantity');
			
			var _updateGCAmount = function() {
				var _val = $.trim($('#gc_quantity').val());
				var _qty = parseInt(_val.length == 0 ? 0 : _val);
				_val = parseInt($('#ddPaymentGCDenomination').val());
				var _total = _qty * _val;
				$('#payment-gc-amount-current').html(_.numberFormat(_total, 2));
				return _total;
			};
			
			$('#gc_quantity').keyup(function(e) {
				_updateGCAmount();
			});
			
			$('#ddPaymentGCDenomination').change(function(e) {
				_updateGCAmount();
			});
			
		};
		
		_payment_modal(payment_type, function(payment_method, payment_details) {

			var current_amount = webpoi.transaction_details.amount_due;

			if (typeof(trans.summary.payments[payment_method]) != 'undefined') current_amount = trans.summary.payments[payment_method].value;
			
			if (payment_method == 'giftcheque') {
	
				trans.summary.payments.push({
					'type' : payment_method,
					'name' : webpoi.settings.payment_methods[payment_method],
					'value' :  payment_details.amount,
					'reference_no' : payment_details.reference_no,
					'reference_detail' : payment_details.reference_detail,
					'first_name' : payment_details.first_name,
					'last_name' : payment_details.last_name,
					'middle_name' : payment_details.middle_name,
				});
				
				//open cash payments
				b.enableButtons('.btn-payments[data-type="cash"]');
				
				webpoi.refreshDisplay();
			} else {
				var _max = -1;
				if (payment_method == 'funds' || payment_method == 'onlinegiftcheque' || payment_method == 'gcep' || payment_method == 'cpoints') _max = payment_details.amount;
				
				webpoi.showNumpad(
					{
						'title' : 'Payment Amount', 
						'default' : current_amount, 
						'with_decimal' : true, 
						'min' : 0.0001, 
						'max' : _max, 
						'condition' : function(val) {
							if (payment_method == 'onlinegiftcheque') {
								return parseInt(val) % 1 == 0;
							} else {
								return true;
							}
						},
						'on_condition_fail' : function(val) {
							if (payment_method == 'onlinegiftcheque') {
								b.modal.create({
									title: "Add Payment Error!",
									html: "<p>You have entered an invalid amount!<br/>Amount should be divisible by 1.</p>",
									width: 350,
								}).show();
							}
						}
					}, 
					function(set_amount) { // on callback
						var idx = -1;
						
						if (payment_method == 'cash') {
							$.each(trans.summary.payments, function(_index, item) {
								if (item.type == 'cash') {
									idx= _index;
									return false;
								}
							});
						} else if (payment_method == 'funds') {
							$.each(trans.summary.payments, function(_index, item) {
								if (item.type == 'funds') {
									idx= _index;
									return false;
								}
							});
						} else if (payment_method == 'onlinegiftcheque') {
							$.each(trans.summary.payments, function(_index, item) {
								if (item.type == 'onlinegiftcheque') {
									idx= _index;
									return false;
								}
							});
						} else if (payment_method == 'gcep') {
							$.each(trans.summary.payments, function(_index, item){
								if(item.type == 'gcep') {
									idx = _index;
									return false;
								}
							});
						} else if (payment_method == 'cpoints') {
							$.each(trans.summary.payments, function(_index, item){
								if (item.type == 'cpoints') {
									idx = _index;
									return false;
								}
							});
						}
						
						if (idx != -1) {
							trans.summary.payments[idx].value = set_amount;
						} else {
							console.log(webpoi.settings.payment_methods);
							trans.summary.payments.push({
								'type' : payment_method,
								'name' : webpoi.settings.payment_methods[payment_method],
								'value' :  set_amount,
								'reference_no' : payment_details.reference_no,
								'reference_detail' : payment_details.reference_detail,
								'first_name' : payment_details.first_name,
								'last_name' : payment_details.last_name,
								'middle_name' : payment_details.middle_name,
							});
						}
						
						if(payment_method == 'onlinegiftcheque' || payment_method == 'gcep') {
							b.enableButtons('.btn-payments[data-type="cash"]');
						}
						
						webpoi.refreshDisplay();

					}, 
					function(numpad_modal) { // on show
						var pos = $('#'+numpad_modal.id).position();
						var width = $('#'+numpad_modal.id).width();
						var zIndex = $('#'+numpad_modal.id).css('z-index');

						var _left = pos.left + width + 5;

						var _amount_due_html = "<span class='well' style='line-height: 30px; display: block; padding: 10px;font-size: 18px;'>"+_.numberFormat(webpoi.transaction_details.amount_due, 2)+"</span>";
						
						$('body').append(webpoi.renderPopups('add-payment-amount-due', _left, 'Amount Due:', _amount_due_html, zIndex));

						if (payment_method == 'funds' || payment_method == 'onlinegiftcheque' || payment_method == 'gcep') {
							var _title = 'Funds:';
							if (payment_method == 'onlinegiftcheque') _title = 'Online Gift Cheque:';
							if (payment_method == 'gcep') _title = 'GCEP';
							_amount_due_html = "<span class='well' style='line-height: 30px; display: block; padding: 10px;font-size: 18px;'>"+_.numberFormat(payment_details.amount, 2)+"</span>";
							_left = pos.left - (width + 5);
							$('body').append(webpoi.renderPopups('add-payment-funds', _left, _title, _amount_due_html, zIndex));
						}

					},
					function(numpad_modal) { // on hide
						$('#add-payment-amount-due').remove();
						$('#add-payment-funds').remove();
					}
				);
			}
			
		});
		
		return false;
		
	};
	
	webpoi.removePayment = function(index) {
		
		if (typeof(trans.summary.payments[index]) != 'undefined') {
			if(trans.status == 'PENDING')
			{
				//delete from db
				b.request({
					url: '/webpoi/delete_payment_from_db',
					data: {
						'transaction_id' : trans.transaction_id,
						'type': trans.summary.payments[index].type,
						'value' : trans.summary.payments[index].value,
					},
					with_overlay: false,
					on_success: function(data){
						//do
						hideLoading();
					}
				});
			}
			delete trans.summary.payments[index];
			trans.summary.payments = _.compact(trans.summary.payments);

			if(webpoi.trans_type == 1)
			{
				console.log(trans.summary.payments);
				
				var with_gc_payment = 0;
				var with_cash_payment = 0;
				var cash_index = 0;
				$.each(trans.summary.payments, function(index, item){
					if(item.type == 'onlinegiftcheque' || item.type == 'gcep' || item.type == 'giftcheque') {
						with_gc_payment = 1;
					} else if (item.type == 'cash') {
						with_cash_payment = 1;
						cash_index = index;
					}
				});
				if(with_gc_payment == 0) b.disableButtons('.btn-payments[data-type="cash"]');
				if(with_gc_payment == 0 && with_cash_payment == 1) {
					var remove_cash_modal = b.modal.create({
						title: 'CASH payment in GC Transaction',
						html: "<p>GC Transactions must have at least one type of GC payment. CASH payments will be removed.</p>",
						witdh: 350,
						disableClose: true,
						buttons: {
							'Close' : function() {
								delete trans.summary.payments[0];
								trans.summary.payments = _.compact(trans.summary.payments);
								remove_cash_modal.hide();
								webpoi.refreshDisplay();
							}
						}
					});
					remove_cash_modal.show();
				}
			}
			
			webpoi.refreshDisplay();
		}
		
	};
	
	webpoi.getTransaction = function(transaction_code, cb, with_overlay) {
		
		with_overlay = typeof(with_overlay) == 'undefined' ? false : with_overlay;
		
		b.request({
			'with_overlay': with_overlay,
			url: '/webpoi/get_transaction',
			data: {'transaction_code' : transaction_code},
			on_success: function(data, status) {
				if (data.status == 'ok') {
					root.trans = _.clone(data.data);
					webpoi.transaction_details.rate_to_use = data.data.rate_to_use;
					//if (trans.customer.member_id.length > 0) {
						$('#ddRates').val(data.data.rate_to_use);
						$('#ddRates').trigger('change');
					//}
				} else {
					b.modal.create({
						title: "Get Transaction Notice!",
						html: 'Transaction code not found!',
						width: 300,
					}).show();
					return false;
				}
				if (_.isFunction(cb)) cb.call(this, data.status);
			}
		});
		
	};
	
	webpoi.showGetTransaction = function() {
	
		var search_string = "";
		var _get_transactions = function(page, type, search_string) {
			$('#get-transaction-item-listing').html("<tr><td>Loading... <img src='/assets/img/loading2.gif' alt='' /></td></tr>");
			b.request({
				'with_overlay' : false,
				url: '/webpoi/get_transactions/'+page,
				data: {
					'search_string': search_string,
					'type': type
				},
				on_success: function(data, status) {
					if (data.status == 'ok') {
						$('#get-transaction-item-listing').html(_.template($('#get-transaction-item-template').html(), {'transactions' : data.data.transactions}));
						$('#get-transaction-pager').html(data.data.pager_html);
					}
				}
			});


				$("#get_transaction_date").datepicker({
		            'timeFormat': 'hh:mm tt',
					'dateFormat' : "yy-mm-dd",
				});

				$("#get_transaction_date").datepicker("option", "changeMonth", true);
				$("#get_transaction_date").datepicker("option", "changeYear", true);

				$("#from_date_icon").click(function(e) {
					$("#get_transaction_date").datepicker("show");
				});
		};
	
		var modal = b.modal.create({
			title: "Transaction",
			html: _.template($('#get-transaction-template').html(), {}),
			width: 800,
		});
		modal.show();
		
		_get_transactions(1);
		
		$(document).undelegate('#get-transaction-pager a', 'click');
		$(document).delegate('#get-transaction-pager a', 'click', function(e) {
			e.preventDefault();
			
			var _href = $(this).attr('href');
			_href = _href.split("/");
			var _page = _href[_href.length-1];
			
			var type = $.trim($('#get_search_type').val());
			var search_string = "";
			if(type == 'transaction_code')
				search_string = $.trim($('#gt_transaction_code').val());
			else if(type == 'name')
				search_string = $.trim($('#get_transaction_name').val());
			else if(type == 'status')
				search_string = $.trim($('#get_transaction_status').val());
			else if(type == 'date')
				search_string = $.trim($('#get_transaction_date').val());
			
			/*if (search_string.length == 0) {
				return;
			}*/
			
			_get_transactions(_page, type, search_string);
			
		});
		
		$(document).undelegate('.btn-gt-select-transaction', 'click');
		$(document).delegate('.btn-gt-select-transaction', 'click', function(e) {
			e.preventDefault();
			
			var transaction_code = $(this).data('code');
			
			webpoi.getTransaction(transaction_code, function(status) {
				modal.hide();
				webpoi.refreshDisplay();
				// set transaction
				if((trans.summary.payments.length > 0) && (trans.summary.payments[0].type.toLowerCase() == 'giftcheque' || trans.summary.payments[0].type.toLowerCase() == 'onlinegiftcheque' || trans.summary.payments[0].type.toLowerCase() == 'gcep')) {
					$('.tab_transactions[data-type="1"]').click();
				} else if((trans.summary.payments.length > 0) && (trans.summary.payments[0].type.toLowerCase() == 'cash' || trans.summary.payments[0].type.toLowerCase() == 'card' || trans.summary.payments[0].type.toLowerCase() == 'cheque' || trans.summary.payments[0].type.toLowerCase() == 'funds')){
					$('.tab_transactions[data-type="0"]').click();
				} else if((trans.summary.payments.length > 0) && (trans.summary.payments[0].type.toLowerCase() == 'cpoints')) {
					$('.tab_transactions[data-type="2"]').click();
				}
				
				if(trans.status == 'RELEASED' || trans.status == 'CANCELLED') {
					//disable releasing facility change
				}else if(trans.status == 'COMPLETED') {
					//releasing facility can still be changed
					/*$('#change_releasing_facility').attr('style', 'display:none;margin-bottom:10px');
					$('#change_releasing_facility').show();
					b.enableButtons('#change_releasing_facility');*/
					
					if(trans.releasing_facility_id != 0) {
						var rel = trans.releasing_facility_id;
						$("#releasing_facility_id option[value='"+rel+"']").attr('selected', 'selected');
					}
					$('#releasing_facility_id').attr('disabled', 'disabled');
				}else if(trans.status == 'PENDING') {
					if(trans.releasing_facility_id != 0) {
						var rel = trans.releasing_facility_id;
						$("#releasing_facility_id option[value='"+rel+"']").attr('selected', 'selected');
					}
					$('#releasing_facility_id').removeAttr('disabled');
				}else {
					$('#change_releasing_facility').hide();
				}
				
				$.each(trans.customer.member_types,function(index,item){
					if(item.member_type_id == 3 && trans.summary.rebate_amount > 0 && (trans.status == "PENDING" || (trans.status == "COMPLETED" && trans.ar_number == null)))
					{
						b.enableButtons('#btn-add-product-rebate');
						return false;
					}
					else
					{
						b.disableButtons('#btn-add-product-rebate');
					}
				});
			}, true);
			
		});
		
		$(document).undelegate('#btn_get_transaction_search', 'click');
		$(document).delegate('#btn_get_transaction_search', 'click', function(e) {
			e.preventDefault();
			
			var type = $.trim($('#get_search_type').val());
			var search_string = "";
			if(type == 'transaction_code')
				search_string = $.trim($('#gt_transaction_code').val());
			else if(type == 'name')
				search_string = $.trim($('#get_transaction_name').val());
			else if(type == 'status')
				search_string = $.trim($('#get_transaction_status').val());
			else if(type == 'date')
				search_string = $.trim($('#get_transaction_date').val());
			
			if (search_string.length == 0) {
				return;
			}
			
			_get_transactions(1, type, search_string);
			
		});
		
		$('#gt_transaction_code').focus();
		
	};
	
	webpoi.showHoldTransactions = function() {
		
		var modal = b.modal.create({
			title: "Transaction",
			html: _.template($('#hold-transaction-template').html(), {}),
			width: 800,
			buttons: {
				'Clear' : function() {
					
					var _notice_modal = b.modal.create({
						title: "Clear On-Hold Transaction",
						html: 'Are you sure you want to clear on-hold transactions?',
						width: 300,
						disableClose: true,
						buttons : {
							'Yes' : function() {
								
								webpoi.hold_trans = [];
								
								_notice_modal.hide();
							},
							'No' : function() {
								_notice_modal.hide();
							}
						}
					});
					
					_notice_modal.show();
					
				}
			}
		});
		modal.show();
		
		$('#hold-transaction-item-listing').html(_.template($('#hold-transaction-item-template').html(), {'transactions' : webpoi.hold_trans}));
	
		$(document).undelegate('.btn-ht-select-transaction', 'click');
		$(document).delegate('.btn-ht-select-transaction', 'click', function(e) {
			e.preventDefault();

			var _index = $(this).data('index');
			root.trans = _.clone(webpoi.hold_trans[_index]);
			webpoi.hold_trans.splice(_index, 1);
			
			modal.hide();
			
			webpoi.refreshDisplay();
			// set transaction
			if((trans.summary.payments.length > 0) && (trans.summary.payments[0].type.toLowerCase() == 'giftcheque' || trans.summary.payments[0].type.toLowerCase() == 'onlinegiftcheque')) {
				$('.tab_transactions[data-type="1"]').click();
			} else if((trans.summary.payments.length > 0) && (trans.summary.payments[0].type.toLowerCase() == 'cash' || trans.summary.payments[0].type.toLowerCase() == 'card' || trans.summary.payments[0].type.toLowerCase() == 'cheque' || trans.summary.payments[0].type.toLowerCase() == 'funds')){
				$('.tab_transactions[data-type="0"]').click();
			}
		});
	};
	
	webpoi.selectReleasingFacility = function() {
		var facility_select_modal = b.modal.create({
			title: 'Select Facility to Release',
			html: facility_tags,
			width: 300,
			disableClose: true,
			buttons: {
				'Cancel': function() {
					facility_select_modal.hide();
				},
				'Confirm': function(){
					var releasing_facility_id = $('#releasing_facility_id').val();
					facility_select_modal.hide();
					webpoi.setReleasingFacility(releasing_facility_id);
					webpoi.showPreview();
				}
			}
		});
		facility_select_modal.show();
	}
	
	webpoi.showPreview = function() {
		
		var discounts_amount = 0;
		
		if(typeof trans.summary.discounts.amount != 'undefined'){
			discounts_amount = trans.summary.discounts.amount.value;
		}
		
		if(discounts_amount > trans.summary.sub_total){
			
			var error_msg;
			
			if(trans.summary.sub_total == 0){
				error_msg = 'No discountable products available.';
			}
			else{
				error_msg = 'Your entered discount amount exceeds the sub total amount.';
			}
			
			var error_modal = b.modal.create({
				title: 'Error',
				html: error_msg,
				width: 300
			});
			error_modal.show();
		}
		else{
			
			// review transaction modal
			var _review_transaction_modal = function() {
			
				var modal = b.modal.create({
					title: "Review Transaction",
					html: _.template($('#transaction-review-template').html(),{'trans' : trans }),
					width: 900,
					disableClose: true,
					buttons : {
						'Cancel' : function() {
							modal.hide();
						},
						'Proceed' : function() {
						
							$('#trans_review_remarks-help').html('');
							$('#trans_review_remarks-control').removeClass('error');
						
							// check if remarks is empty
							var _remarks = $.trim($('#trans_review_remarks').val());
						
							if (_remarks.length == 0) {
								$('#trans_review_remarks-help').html('Remarks is required!');
								$('#trans_review_remarks-control').addClass('error');
								return false;
							}
							//console.log(trans);
							//console.log(webpoi.transaction_details);
							// do a request to save 
							b.request({
								'with_overlay': true,
								url: '/webpoi/process_transaction',
								data: {'transaction' : trans, 'transaction_details' : webpoi.transaction_details, 'remarks' : _remarks, 'trans_type' : webpoi.trans_type},
								on_success: function(data, status) {
									modal.hide();
									if (data.status == "error") {
										var _error_confirm_modal = b.modal.create({
											title: "Confirm Transaction Notice!",
											html: '<div style="font-size:14px;"><p>'+data.msg+'</p></div>',
											width: 400,
											buttons : {
												'Ok' : function() {
											
													_error_confirm_modal.hide();												
													return false;												
												}
											}
										});								
										_error_confirm_modal.show();
									
									} else {
										var _confirm_modal = b.modal.create({
											title: "Confirm Transaction Notice!",
											html: '<div style="font-size:14px;"><p>Transaction <strong>'+data.data.transaction_code+'</strong> was processed successfully with AR #'+data.data.ar_number+'.</p><p>Click "Print" button to print the transaction details.</p></div>',
											width: 400,
											buttons : {
												'Print' : function() {

													var title = 'Transaction Receipt';
													var url = "/webpoi/transaction_receipt?type=view&transaction_code="+data.data.transaction_code;
													window.open(url,'transaction_print','width=980,height=600,resizable=1,status=1,toolbars=0,location=0,scrollbars=1');
												}
											}
										});
									
										// check if transaction is not gc 
										if (webpoi.trans_type == 0) {
											webpoi.showCardReleasing(data.data.transaction_id, data.data.transaction_code, function() {
												_confirm_modal.show();
											}, '', '', '','', data.data.p2p_counter, 0);
										} else {
											_confirm_modal.show();
										}
									

										webpoi.newTransaction();
									}

								}
							});

						}
					}
				});
				modal.show();
				if(trans.is_override == 1) {
					$('#trans_review_remarks').append(webpoi.override_remarks +"; ");
					$('#trans_review_remarks').append('ITEMS TO FOLLOW: ');
					$.each(webpoi.out_of_stock_items, function(index, item){
						var available_quantity = item.available_quantity;
						if(available_quantity < 0) available_quantity = 0;
						var requesting_quantity = item.requesting_quantity;
						var to_follow_qty = requesting_quantity - available_quantity;
						var product_name = webpoi.items.products[item.product_id].product_name;
						var add_remarks = to_follow_qty + ' x ' + product_name + ', '; 
						$('#trans_review_remarks').append(add_remarks);
					});
				}
			};
		
			// do some checking first
			//if (_.size(trans.products) == 0 && _.size(trans.fpv_vouchers) == 0 && _.size(trans.mpv_vouchers) == 0 && _.size(trans.p2p_vouchers) == 0) {
			if (_.size(trans.products) == 0 && _.size(trans.p2p_vouchers) == 0 && _.size(trans.fpv_vouchers) == 0 && _.size(trans.mpv_vouchers) == 0) {
				b.modal.create({
					title: "Confirm Transaction Notice!",
					html: 'This is an empty transaction.',
					width: 300,
				}).show();
				return;
			}
		
			if (webpoi.transaction_details.amount_due > 0) {
				b.modal.create({
					title: "Confirm Transaction Notice!",
					html: 'Payment is incomplete.<br/>Please complete payment before proceeding.',
					width: 300,
				}).show();
				return;
			}
		
			if (trans.customer.fullname.length == 0) {
				b.modal.create({
					title: "Confirm Transaction Notice!",
					html: 'There is no assigned customer/member.<br/>Please assign a customer before proceeding.',
					width: 300,
				}).show();
				return;
			}
		
			var epc_cash = 0;
			var epc_card = 0;
			var check_payment = false;
			$.each(trans.summary.discounts, function(index, item) {
				if (item.type == 'epc_cash')
				{
					epc_cash = epc_cash + (item.amount_to_discount * (1 - (item.value / 100)));
					check_payment = true;
				} 
				else if (item.type == 'epc_card') 
				{
					epc_card = epc_card + (item.amount_to_discount * (1 - (item.value / 100)));
					check_payment = true;
				}
				else if (item.type == 'amount')
				{
					epc_cash = epc_cash - item.value;
					epc_card = epc_card - item.value;
				}
				else if (item.type == 'percent')
				{
					epc_cash = epc_cash - (item.amount_to_discount * (item.value / 100));
					epc_card = epc_card - (item.amount_to_discount * (item.value / 100));
				}
			});
		
			var for_epc_cash = 0;
			var for_epc_card = 0;
		
			if(check_payment)
			{
				$.each(trans.summary.payments, function(index, item) {
					if(item.type == "card")
					{
						for_epc_card = parseFloat(for_epc_card) + parseFloat(item.value);
					}
					else if(item.type == "funds")
					{
						for_epc_cash = parseFloat(for_epc_cash) + parseFloat(item.value);
					}
					else if(item.type == "cheque")
					{
						for_epc_cash = parseFloat(for_epc_cash) + parseFloat(item.value);
					}
					else
					{
						for_epc_cash = parseFloat(for_epc_cash) + parseFloat(item.value);
					}
				});
				console.log(for_epc_cash);
				for_epc_cash = for_epc_cash.toFixed(2);
				var error_msg = "";

				if(for_epc_cash < (epc_cash))
				{
					error_msg += "<p>The amount that the customer is paying via <strong>Cash/Funds/Cheque</strong> is below the stated <strong>EPC Cash/Funds/Cheque Payment</strong></p>";
				}
			
				if(for_epc_card < (epc_card))
				{
					error_msg += "<p>The amount that the customer is paying via <strong>Card</strong> is below the stated <strong>EPC Card Payment</strong></p>";
				}
			
				if(error_msg != "")
				{
					b.modal.create({
						title: "Payment :: Error",
						width: 350,
						html: error_msg
					}).show();
					return;
				}
			}
		
			// check for remaining rebate amount
			// then if customers in an employee check all products can be accomodated by employee's slots
			// then check if products are available
			webpoi.checkRemainingRebateAmount(function() {

				webpoi.checkEmployeeSlots(function(over_slot) {
				
					if (webpoi.transaction_details.rate_to_use == 3 && over_slot.length > 0) {
						var modal = b.modal.create({
							title: "Employee Slot Check",
							html: _.template($('#check-slot-dialog-template').html(), {'products' : over_slot}),
							width: 410,
						});
						modal.show();
						return;
					}
				
					webpoi.checkStocks(function(out_of_stocked_inventory) {
						//console.log(webpoi.out_of_stocked_inventory);
						if (webpoi.out_of_stocked_inventory.length > 0 && (trans.transaction_type != 'FUNDS' && trans.transaction_type != 'GIFTCHEQUE')) {
							if(trans.is_override != 1)
							{
								var out_of_stock_modal = b.modal.create({
									title: "Product Stock Check",
									html: _.template($('#out-of-stock-dialog-template').html(), {'products' : webpoi.out_of_stocked_inventory}),
									width: 400,
									disableClose: true,
									buttons: {
										'Close': function(){
											out_of_stock_modal.hide();
											return;
										},
										'Override': function(){
											webpoi.checkAuthority("Override Transactions","AUTHORIZE-OVERRIDE",function(allow) {
												if(allow)
												{
													var override_modal = b.modal.create({
														title: "Override Transaction",
														html: "<div><span>Please Enter Remarks:</span><textarea class='override_remarks' name='override_remarks' style='width:270px;'></textarea><span id='override_remarks_error' class='label label-important' style='display:none;'>Remarks cannot be empty.</span></div>",
														width: 310,
														disableClose : true,
														buttons : {
															'Cancel' : function() {
																override_modal.hide();
																return;
															},
															'Confirm' : function() {
																var override_remarks = $('.override_remarks').val();
																webpoi.override_remarks = override_remarks;
																if(override_remarks == ""){
																	$('#override_remarks_error').show();
																} else{
																	trans.is_override = 1;
																	override_modal.hide();
																	out_of_stock_modal.hide();
															
																	$.each(out_of_stocked_inventory, function(index, item){
																		if(webpoi.out_of_stock_items[item.product_id] != undefined) {
																			webpoi.out_of_stock_items[item.product_id].requesting_quantity = webpoi.out_of_stock_items[item.product_id].requesting_quantity + item.requesting_quantity;
																		}else {
																			webpoi.out_of_stock_items[item.product_id] = item;
																		}
																	});
															
																	_review_transaction_modal();
																}
															}
														}
													});
													override_modal.show();
												}
											});
										}
									}
								});
								out_of_stock_modal.show();
								return;
							}
						}
						_review_transaction_modal();
					});		
				});
			});
		}
	};
	
	webpoi.checkRemainingRebateAmount = function(cb) {
		if (webpoi.transaction_details.remaining_rebate_amount > 0) {
			var _rebate_check = b.modal.create({
				title: "Confirm Transaction Notice!",
				html: 'There still '+numberFormat(webpoi.transaction_details.remaining_rebate_amount,2)+' of product rebate remaining.<br/>Do you like to proceed?.',
				width: 300,
				disableClose: true,
				buttons : {
					'No' : function() {
						_rebate_check.hide();
					},
					'Yes' : function() {
						_rebate_check.hide();
						if (_.isFunction(cb)) cb.call(this);
					}
				}
			});
			_rebate_check.show();
		} else if (webpoi.transaction_details.remaining_rebate_amount < 0) {
			b.modal.create({
				title: "Confirm Transaction Error!",
				html: 'You have more than allowed product rebate.<br/>Please remove some selected product rebate before proceeding.',
				width: 300,
			}).show();
			return;
		}
		else
		{
			if (_.isFunction(cb)) cb.call(this);
		}
	}
	
	webpoi.showCardReleasing = function(transaction_id, transaction_code, cb, sp_cards_list, rs_cards_list, rfid_cards_list, pay_cards_list, p2p_packages_count, p2p_cards_count) {
	
		sp_cards_list = typeof(sp_cards_list) != 'undefined' ? sp_cards_list : '';
		rs_cards_list = typeof(rs_cards_list) != 'undefined' ? rs_cards_list : '';
		rfid_cards_list = typeof(rfid_cards_list) != 'undefined' ? rfid_cards_list : '';
		pay_cards_list = typeof(pay_cards_list) != 'undefined' ? pay_cards_list : '';
		p2p_packages_count = typeof(p2p_packages_count) != 'undefined' ? p2p_packages_count : '0';
		p2p_cards_count = typeof(pay_cards_list) != 'undefined' ? p2p_cards_count : '0';
	
		var cards_modal = b.modal.create({
			title: "Card Releasing for " + transaction_code,
			width: 575,
			disableClose: true,
			html: _.template($('#cards_logging_template').html(),{"transaction_code": transaction_code, "sp_cards_list": sp_cards_list, "rs_cards_list": rs_cards_list, "rfid_cards_list": rs_cards_list, "pay_cards_list": pay_cards_list, "p2p_packages_count": p2p_packages_count, "p2p_cards_count": p2p_cards_count}),
			buttons: {
				"Cancel": function(){
					cards_modal.hide();
				},
				"Proceed": function(){
					b.request({
						url: "/webpoi/releasing/check_cards",
						data: {
							"sp_cards_list": $("#sp_cards_list").val(),
							"p2p_cards_list": $("#p2p_cards_list").val(),
							"rs_cards_list": $("#rs_cards_list").val(),
							"rfid_cards_list": $("#rfid_cards_list").val(),
							"pay_cards_list": $("#pay_cards_list").val(),
							"transaction_id": transaction_id
						},
						on_success: function(data) {
							
							if(data.status == "ok")
							{
								var sp_cards = data.data.sp_results;
								var p2p_cards = data.data.p2p_results;
								var rs_cards = data.data.rs_results;
								var rfid_cards = data.data.rf_results;
								var pay_cards = data.data.metrobank_results;
								
								var confirm_modal = b.modal.new({
									title: "Confirmation",
									width: 350,
									html: "Are you sure you want to proceed?",
									disableClose: true,
									buttons: {
										"No": function(){
											confirm_modal.hide();
										},
										"Yes": function(){
											b.request({
												url: "/webpoi/releasing/release_cards",
												data: {
													"transaction_id": transaction_id,
													"cards_list": JSON.stringify({"sp":$("#sp_cards_list").val(),"p2p":$("#p2p_cards_list").val(),"rs":$("#rs_cards_list").val(),"rf":$("#rfid_cards_list").val(),"paycard":$("#pay_cards_list").val()}),
													"cards": JSON.stringify({"rs":rs_cards,"sp":sp_cards,"p2p":p2p_cards,"rf":rfid_cards,"paycard":pay_cards})
												},
												on_success: function(data){
													releasing_modal = b.modal.create({});

													if(data.status == "ok")
													{
														if (_.isFunction(cb)) cb.call(this);
													}
													else
													{
														releasing_modal.init({
															title: "Error Notification",
															html: "<p>"+data.msg+"</p>",
															width: 300,
															disableClose: true,
															buttons: {
																"Close": function(){
																	releasing_modal.hide();
																	if (_.isFunction(cb)) cb.call(this);
																}
															}
														});
														releasing_modal.show();
													}

												}
											});
											confirm_modal.hide();
											cards_modal.hide();
										}
									}
								});

								confirm_modal.show();
							}
							else
							{
								var sp_error_html = "";
								var p2p_error_html = "";
								var rs_error_html = "";
								var rf_error_html = "";
								var pay_error_html = "";
								
								sp_error_html = sp_error_html + "<strong>SP Cards</strong><hr>";
								$.each(data.data.sp_results,function(k,v){
									sp_error_html = sp_error_html + "<span><label>Card ID: "+ v.card_id +" </label> Error: "+ v.error +"</span>";
								})
								sp_error_html = sp_error_html + "<hr>";
								
								p2p_error_html = p2p_error_html + "<strong>P2P Cards</strong><hr>";
								$.each(data.data.p2p_results,function(k,v){
									p2p_error_html = p2p_error_html + "<span><label>Card ID: "+ v.card_id +" </label> Error: "+ v.error +"</span>";
								})
								p2p_error_html = p2p_error_html + "<hr>";
								
								rs_error_html = rs_error_html + "<strong>RS Cards</strong><hr>";
								$.each(data.data.rs_results,function(k,v){
									rs_error_html = rs_error_html + "<span><label>Card ID: "+ v.card_id +" </label> Error: "+ v.error +"</span>";
								});
								rs_error_html = rs_error_html + "<hr>";
								
								rf_error_html = rf_error_html + "<strong>RF ID Cards</strong><hr>";
								$.each(data.data.rf_results,function(k,v){
									rf_error_html = rf_error_html + "<span><label>Card ID: "+ v.card_id +" </label> Error: "+ v.error +"</span>";
								});
								rf_error_html = rf_error_html + "<hr>";
								
								pay_error_html = pay_error_html + "<strong>Metrobank Pay Cards</strong><hr>";
								$.each(data.data.metrobank_results,function(k,v){
									pay_error_html = pay_error_html + "<span><label>Card ID: "+ v.card_id +" </label> Error: "+ v.error +"</span>";
								});
								pay_error_html = pay_error_html + "<hr>";
								
								var error_html = "<div><div>"+sp_error_html+"</div><div>"+p2p_error_html+"</div><div>"+rs_error_html+"</div><div>"+rf_error_html+"</div><div>"+pay_error_html+"</div></div>";
								
								var cards_error_modal = b.modal.create({
									title: "Error Notification",
									width: 500,
									html: error_html
								});
								
								cards_error_modal.show();
							}
						}
					});
				}
			}
		});

		cards_modal.show();
	};
	
	
	webpoi.showARForm = function(cb) {
		
		var modal = b.modal.create({
			title: "Acknowledge Receipt",
			html: _.template($('#ar-form-template').html(),{}),
			width: 350,
			disableClose: true,
			buttons : {
				'Cancel' : function() {
					modal.hide();
				},
				'Submit' : function() {
					$('#ar_number_status').hide();
					$('#ar_date_status').hide();
					
					// ar # is required
					var ar_no = $.trim($('#ar_number').val());
					var ar_date = $.trim($('#ar_date').val());
					var ar_remarks = $.trim($('#ar_remarks').val());
					
					if (ar_no.length == 0) {
						$('#ar_number_status').show();
						return;
					}
					
					if (ar_date.length == 0) {
						$('#ar_date_status').show();
						return;
					}
					
					modal.hide();
					if (_.isFunction(cb)) cb.call(this, {'ar_number' : ar_no, 'ar_remarks' : ar_remarks, 'ar_date' : ar_date});
				}
			}
		});
		modal.show();
		
		$("#ar_date").datepicker({dateFormat:'yy-mm-dd', changeMonth: true, changeYear: true});
		
	};
	
	webpoi.checkIfVariablePrice = function(product_id,cb) {
		if(webpoi.items.products[product_id].is_variable_price == 1)
		{
			if (_.isFunction(cb)) cb.call(this,true);
		}
		else
		{
			if (_.isFunction(cb)) cb.call(this,false);
		}
	};
	
	webpoi.changeReleasingFacility = function(releasing_facility_id) {
		if(releasing_facility_id == 0)
			return;
			
		var confirm_modal = b.modal.create({
			title: 'Change Releasing Facility',
			html: "Are you sure you want to change this transaction's releasing facility?",
			width: 400,
			disableClose: true,
			buttons: {
				'Cancel': function() {
					confirm_modal.hide();
				},
				'Confirm': function() {
					showLoading();
					confirm_modal.hide();
					b.request({
						url: '/webpoi/change_releasing_facility',
						data: {
							'transaction_id': trans.transaction_id,
							'releasing_facility_id': releasing_facility_id
						},
						on_success: function(data) {
							if(data.status == 'ok') {
								$("#releasing_facility_id option[value='"+releasing_facility_id+"']").attr('selected', 'selected');
								var success_modal = b.modal.create({
									title: 'Change Releasing Facility Success',
									html: "You have successfully changed this transaction's releasing facility.",
									width: 400
								});
								success_modal.show();
							}else if(data.status == 'error') {
								var error_modal = b.modal.create({
									title: 'Error in Change',
									html: data.msg,
									width: 300,
								});
								error_modal.show();
							}
						},
						on_error: function(data) {
							var error_modal = b.modal.create({
								title: 'Error in Change',
								html: 'An error occured while processing your request. Please try again.',
								width: 300,
							});
							error_modal.show();
						}
					});
				}
			}
		});
		confirm_modal.show();
	}

	webpoi.checkAuthority = function(action,key,cb) {

		var authModal = b.modal.create({
			title: action+" :: Authorization",
			html: _.template($('#authorize-voiding-template').html(), {}),
			width: 312,
			disableClose: true,
			buttons: {
				"Cancel": function(){
					authModal.hide();
					if (_.isFunction(cb)) cb.call(this,false);
				},
				"OK": function(){
					var auth_username = $.trim($("#auth_username").val());
					var auth_password = $.trim($("#auth_password").val());
					var has_error = false;
					$("#username_error").hide();
					$("#password_error").hide();
					
					if(auth_username === "")
					{
						$("#username_error").show();
						has_error = true;
					}
					
					if(auth_password === "")
					{
						$("#password_error").show();
						has_error = true;
					}

					if(!has_error)
					{
						//check if username/password is authorized for voiding
						b.request({
							url: '/webpoi/authorize_action',
							data: {
								"username" : auth_username,
								"password" : auth_password,
								"key" : key,
								"action" : action
							},
							on_success: function(data) {
								if(data.status == "ok")
								{
									authModal.hide();
									if (_.isFunction(cb)) cb.call(this,true);
								}
								else
								{
									b.modal.create({
										title: "Error Notification",
										html: data.msg,
										width: 310
									}).show();
									if (_.isFunction(cb)) cb.call(this,false);
								}
							}
						});
					}
				}
			}
		});

		authModal.show();
	}

	webpoi.renderPopups = function(id, left, title, html_body, zindex) {
		title = typeof(title) == 'undefined' ? '' : title;
		zindex = typeof(zindex) == 'undefined' ? 1150 : zindex;
		html_body = typeof(html_body) == 'undefined' ? '' : html_body;
		
		var _html = $("<div id='"+id+"' class='modal hide in' style='width: 250px; height: auto; left: "+left+"px; margin: 0px; top: 100px; display: block; z-index: "+zindex+"; '> \
						<div class='modal-header'><h3>"+title+"</h3></div> \
						<div class='modal-body'>"+html_body+"</div> \
						<div class='modal-footer'></div> \
					</div>");
					
		return _html;
	};

	webpoi.hasEnoughSlots = function(product_id,qty) {
		
		qty = parseInt(qty);
		
		if (typeof(trans.customer.employee_slots[product_id]) == 'undefined') {
				return {'result' : false, 'quantity' : 0};
		} else {
			var check_qty = parseInt(trans.customer.employee_slots[product_id].available_qty);
			
			if (check_qty < qty)
				return {'result' : false, 'quantity' : trans.customer.employee_slots[product_id].available_qty};
				
		}
		return {'result' : true, 'quantity' : trans.customer.employee_slots[product_id].available_qty};
		
	}
	
	webpoi.checkEmployeeSlots = function(cb) {

		over_slot = [];
		
		$.each(trans.products, function(index, prod) {
			var ret = webpoi.hasEnoughSlots(prod.product_id, prod.quantity);
			if (!ret.result) {
				over_slot.push({'product_id' : prod.product_id, 'available_quantity' : ret.quantity, 'requesting_quantity' : prod.quantity});
			}
		});
		
		if (_.isFunction(cb)) cb.call(this, over_slot);
	}
	
	webpoi.printItemSummary = function(){
		var releasing_facility_id = $('#releasing_facility_id').val();
		trans.releasing_facility_id = releasing_facility_id;
		
		b.request({
			url: '/webpoi/insert_pending_transaction',
			data: {'transaction' : trans, 'details' : webpoi.transaction_details},
			on_success: function(data){
				if(data.status == 'ok'){
					
					var transaction_code = data.data.transaction_code;
					var transaction_id = data.data.transaction_id;
					//console.log(transaction_code);
					trans.transaction_code = transaction_code;
					trans.transaction_id = transaction_id;

					webpoi.newTransaction();

					var title = 'Item Summary';
					var url = "/webpoi/item_summary_receipt?type=view&transaction_code="+transaction_code;
					window.open(url, 'transaction_print','width=980,height=600,resizable=1,status=1,toolbars=0,location=0,scrollbars=1');
				}
			}
		});
	};
}).call(this);
