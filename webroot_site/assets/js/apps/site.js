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
		gcep: 0,
		on_hold_funds: 0,
		selected_account_id: 0,
		member_type_id: 0,
		is_paycard_corpo: 0
	};
	
	vitalc.settings = {
		stockist_min_amount: 0,
		vat_percent: 0,
		loading_image_url: ""
	}

	/***************************************************************************/
	// C Points
	vitalc.cpoints = {};
	vitalc.cpoints.converter = function(admin, member_id) {
		conv_data = (admin)?{member_id: member_id}:{};
		console.log(conv_data);
		admin_url = (admin)?'/admin':'/members';
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
	// SHOPPING CART
	vitalc.cart = {};
	vitalc.cart.product_view_modal = '';
	
	vitalc.cart.initialize = function() {
		$('.btn_add_to_cart').on('click', function(e){
			e.preventDefault();
			var _product_id = parseInt($(this).data("id"));
			var _quantity = parseInt($('#product_quantity_'+_product_id).val());
			if(_quantity==0) _quantity = 1;
			vitalc.cart.add(_product_id, _quantity);
			return false;
		});
		$('.btn_remove_from_cart').on('click', function(e){
			e.preventDefault();
			var _cart_product_id = parseInt($(this).data("id"));
			vitalc.cart.remove(_cart_product_id);
			return false;
		});
		$("#button_update_cart").on("click",function(e){
			e.preventDefault();
			vitalc.cart.update();
			return false;
		});
		$("#button_clear_cart").on("click", function(e){
			e.preventDefault();
			vitalc.cart.clear();
			return false;
		});
	};
	
	vitalc.cart.add = function(product_id, quantity) {
		if(typeof(product_id)=="undefined") return false;
		if(typeof(quantity)=="undefined") quantity = 1;
		if(product_id==0) return false;
		
		if(vitalc.member.member_id==0){
			redirect('/members/signin');
			return false;
		}
		
		if(vitalc.member.is_paycard_corpo==1)
		{
			//modal
			var corpo_modal = b.modal.create({
				title: 'Unable to make Online Transactions',
				html: 'You are not allowed to make online transactions. Please contact the IT Department at <em>631-1899</em> or <em>0917-5439586</em> for further information.',
				width: 500,
			});
			corpo_modal.show();
			return false;
		}
		
		beyond.request({
			url : '/cart/add',
			data : {
				'product_id' : product_id,
				'quantity' : quantity
			},
			on_success : function(data) {
				hideLoading();
				if (parseInt(data.status) == 1)	{ // product added
					var success_modal = b.modal.create({
						title: "Add To Cart Success",
						width: 400,
						disableClose: true,
						html: "<p>"+data.msg+"</p>",
						buttons: {
							"Continue Shopping": function(){
								success_modal.hide();
								vitalc.cart.product_view_modal.hide();
							},
							"Go To Shopping Cart": function(){
								redirect('/cart');
							}
						}
					});
					success_modal.show();
				}else if (parseInt(data.status) == -1) {
					redirect('/members/signin');
				}else{
					var _error_modal = b.modal.create({
						title: "Add To Cart Error",
						width: 300,
						html: data.msg
					});
					_error_modal.show();
				}
			}
		});
	};
	
	vitalc.cart.remove = function(cart_product_id) {
		if(typeof(cart_product_id)=="undefined") return false;
		if(cart_product_id==0) return false;
		
		var confirm_modal = b.modal.create({
			title: "Confirmation",
			width: 300,
			html: "<p>Are you sure you want to remove this item from your cart?</p>",
			disableClose: true,
			buttons: {
				"Confirm": function() {
					confirm_modal.hide();
					b.request({
						url: '/cart/remove',
						data : {
							'cart_product_id': cart_product_id
						},
						on_success: function(data) {
							
							if (data.status == 1) {
								b.modal.create({
									title: "Cart Notification",
									width: 300,
									html: "<p>"+data.msg+"</p>",
									disableClose: true,
									buttons: {
										"Close": function(){
											redirect("/cart");
										}
									}

								}).show();
								
							} else {
								b.modal.create({

									title: "Cart Notification Error",
									width: 300,
									html: "<p>"+data.msg+"</p>"

								}).show();								
							}
						}
					});
				},
				"Cancel": function(){
					confirm_modal.hide();
				}
			}
		});
		confirm_modal.show();
		
	};
	
	vitalc.cart.clear = function(){
		
		var confirm_modal = b.modal.create({
			title: "Confirmation",
			width: 300,
			html: "<p>Are you sure you want to clear shopping cart?</p>",
			disableClose: true,
			buttons: {
				"Confirm": function() {
					confirm_modal.hide();
					b.request({
						url: '/cart/clear',
						data : {},
						on_success: function(data) {
							
							if (data.status == 1) {
								b.modal.create({
									title: "Cart Notification",
									width: 300,
									html: "<p>"+data.msg+"</p>",
									disableClose: true,
									buttons: {
										"Close": function(){
											redirect("/cart");
										}
									}

								}).show();
								
							} else {
								b.modal.create({

									title: "Cart Notification Error",
									width: 300,
									html: "<p>"+data.msg+"</p>",

								}).show();								
							}
						}
					});
				},
				"Cancel": function(){
					confirm_modal.hide();
				}
			}
		});
		confirm_modal.show();
		
	};
	
	vitalc.cart.update = function() {
		var cart_array = new Array();
		$(".cart_quantity").each(function(index) {
			cart_array[index] = {"cart_product_id":$(this).attr('data'),"quantity":$(this).val()};
		});

		var json_data = JSON.stringify(cart_array);

		b.request({
			url: '/cart/update',
			data : {
				'json_data': json_data
			},
			on_success: function(data) {
				if (data.status == 1) {
					var success_modal = b.modal.create({
						title: "Cart Update Success",
						width: 300,
						html: "<p>"+data.msg+"</p>",
						disableClose: true,
						buttons: {
							"Close": function(){
								redirect("/cart");
							}
						}
					});
					success_modal.show();
				} else {
					b.modal.create({
						title: "Cart Notification Error",
						width: 300,
						html: "<p>"+data.msg+"</p>"
					}).show();								
				}
			}
		});
	};
	/***************************************************************************/
	// Checkout
	vitalc.checkout = {
		member_status_id: 0,
		member_type_id: 0,
		hash: "",
		is_cart: 1,
		cart_id: 0,
		packages: {},
		has_packages: false,
		products: {},
		selected_products: {},
		rebate_products: {},
		vouchers: {},
		selected_vouchers: {},
		voucher_types:{},
		skipProductVariants: false,
		items: {},
		facility_items: {},
		summary: {
			sub_total: 0,
			rebate_amount: 0,
			used_rebate_amount: 0,
			remaining_rebate_amount: 0,
			vat: 0,
			vatable_discount: 0,
			pdv_amount: 0,
			misc_amount: 0
		},
		vatable_sales_amount: 0,
		p2p_package_id: 0,
		success: false
	};
	
	vitalc.checkout.summary.sub_total = 0;
	vitalc.checkout.summary.vat = 0;
	vitalc.checkout.summary.vatable_discount = 0;
	vitalc.checkout.summary.rebate_amount = 0;
	vitalc.checkout.summary.pdv_amount = 0;
	vitalc.checkout.summary.misc_amount = 0;
	
	vitalc.checkout.view = function(is_cart, product_id) {
		if(typeof(is_cart)=="undefined") is_cart = 1;
		var checkoutData = {
			'is_cart': is_cart,
			'payment_type': $('#payment_type').val(),
			'facility_id': $('#facility_id').val()
		};
		vitalc.checkout.is_cart = is_cart;
		if(checkoutData.is_cart==0) checkoutData.product_id = product_id;
		b.request({
			url: '/cart/checkout/view',
			data: checkoutData,
			on_success: function(data) {
				if(parseInt(data.status)==1){
					vitalc.checkout.member_status_id = data.data.member_status_id;
					vitalc.checkout.member_type_id = data.data.member_type_id;
					vitalc.checkout.member_select_account = data.data.select_account_html;
					vitalc.checkout.cart_id = data.data.cart_id;
					vitalc.checkout.hash = data.data.hash;
					vitalc.checkout.selected_products = data.data.selected_products;
					vitalc.checkout.products = data.data.products;
					vitalc.checkout.has_packages = data.data.has_packages;
					vitalc.checkout.computeSummary();
					
					if(_.isEmpty(vitalc.checkout.packages))
						vitalc.checkout.packages = data.data.packages;
					
					var _rebate = vitalc.checkout.computeProductRebate(vitalc.checkout.rebate_products);
					
					vitalc.checkout.summary.rebate_amount = _rebate.rebate_amount;
					vitalc.checkout.summary.used_rebate_amount = _rebate.used_rebate_amount;
					vitalc.checkout.summary.remaining_rebate_amount = parseFloat(_rebate.rebate_amount - _rebate.used_rebate_amount).toFixed(2);
					$("#product-rebate-value").html(numberFormat(vitalc.checkout.summary.remaining_rebate_amount,2));
					
					if($("#payment_type").val() != "funds") 
						$("#rebate-section").hide();
					else 
						$("#rebate-section").show();

					$("#cart-details").html(data.data.html);
					
					vitalc.checkout.displayVariants();
					
					if($('#payment_type').val() == "funds")
					{
						b.enableButtons("#btn-add-product-rebate");
					}
					else
					{
						b.disableButtons("#btn-add-product-rebate");
					}
					
					//vouchers
					if($("#payment_type").val() == "giftcheque" || $("#payment_type").val() == "gcep" || $("#payment_type").val() == 'cpoints') 
					{
						$("#vouchers-section").hide();
						b.disableButtons("#btn-add-voucher");
						vitalc.checkout.vouchers = data.data.vouchers;
						vitalc.checkout.selected_vouchers = data.data.selected_vouchers;
					}
					else 
					{
						if(_.isEmpty(vitalc.checkout.selected_vouchers)){
							vitalc.checkout.vouchers = data.data.vouchers;
							vitalc.checkout.selected_vouchers = data.data.selected_vouchers;
						}
						$("#vouchers-section").show();
						b.enableButtons("#btn-add-voucher");
					}
					vitalc.checkout.displayProductVouchers();
					
					console.log(vitalc.checkout.vouchers);
					
					$(document).undelegate("#proceed_with_checkout","click");
					$(document).delegate("#proceed_with_checkout","click",function(e){
						e.preventDefault();
						vitalc.checkout.skipProductVariants = false;
						vitalc.checkout.confirm();
						return false;
					});
					
					$(document).undelegate('.package-swappables',"click");
					$(document).delegate('.package-swappables',"click",function(e){
						e.preventDefault();
						var product_id = $(this).data("id");
						vitalc.checkout.swap.initialize(product_id);
						return false;
					});
					
					$('body').undelegate('.prod-varian-group-item',"click");
					$('body').delegate('.prod-varian-group-item',"click",function(e){
						e.preventDefault();
						$(".prod-variant-list").removeClass("active");
						$(this).parent().addClass("active");
						$('.box2 .selection-item-container').html('');

						var selected_group = $(this).data("index");

						var _selection = "<ul class='nav nav-pills nav-stacked selection-item-list' id='prod-variant-product-list'>";
						var ctr = 0;
						for(var key in vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].groups[selected_group].products){
							ctr++;
							_selection += "<li id='prod-varian-product-"+key+"' class=''><a data-group='"+selected_group+"' data-product-id='"+key+"' data-index='"+ctr+"' class='prod-varian-product-item' href='javascript://void(0);'><small><span class='prod-varian-product-available'></span></small> "+vitalc.checkout.products[key].product_name+"</a></li>";
						}
						_selection += "</ul>";
						$('.box2 .selection-item-container').html(_selection);

						return false;
					});
					
					$('body').undelegate('.prod-varian-product-item',"click");
					$('body').delegate('.prod-varian-product-item',"click",function(e){
						e.preventDefault();
						
						//var group = $(this).data("group");
						var _idx = $(this).data('index');
						var _product_id = $(this).data('product-id');
						var _group_idx = $(this).data('group');
						
						
						var _new_qty = parseInt($('#prod-varian-group-'+_group_idx+' a small span.prod-varian-group-available').html());

						if(_new_qty==0) return false;
						vitalc.showNumpad({'title' : 'Enter Quantity', 'default' : 1, 'with_decimal' : false, 'min' : 1, 'max' : _new_qty, 'skip_if_max' : 1}, function(set_qty) {
							// update new qty
							_new_qty -= set_qty;

							$('#prod-varian-group-'+_group_idx+' .prod-varian-group-available').html(_new_qty);
							if (typeof(selected_products[_group_idx]) == 'undefined') {
								selected_products[_group_idx] = {};
							}
							if (typeof(selected_products[_group_idx][_product_id]) == 'undefined') {
								selected_products[_group_idx][_product_id] = {};
								selected_products[_group_idx][_product_id].child_product_id = _product_id;
								selected_products[_group_idx][_product_id].group = _group_idx;
								selected_products[_group_idx][_product_id].quantity = set_qty;
							} else {
								selected_products[_group_idx][_product_id].quantity+=set_qty;
							}
							
							_list_tag = '';
							$.each(selected_products, function(i, group) {
								$.each(group,function(index,item) {
									var product_name = vitalc.checkout.products[item.child_product_id].product_name;
									var srp = parseFloat(vitalc.checkout.products[item.child_product_id].standard_retail_price);
									var qty = parseInt(item.quantity);
									_list_tag = _list_tag + '<li id="prod-varian-selected-'+item.child_product_id+'"><a href="javascript://void(0);" class="selected-gray prod-varian-selected-item" data-price="'+srp+'" data-index="'+index+'" data-product-id="'+item.child_product_id+'" data-group="'+item.group+'"><small><span class="prod-varian-selected-available">'+qty+'</span> x</small> '+product_name+'</a></li>';
								});
							});

							$('#prod-variant-selected-list').html(_list_tag);

							$('body').undelegate('.prod-varian-selected-item',"click");
							$('body').delegate('.prod-varian-selected-item',"click",function(e){
								e.preventDefault();
								var _selected_idx = $(this).data('index');
								var _selected_product_id = $(this).data('product-id');
								var _selected_group_idx = $(this).data('group');
								var available = parseInt($('#prod-varian-group-'+_selected_group_idx+' a small span.prod-varian-group-available').html());
								
								vitalc.showNumpad({'title' : 'Enter Quantity to Deduct', 'default' : 1, 'with_decimal' : false, 'min' : 1, 'max' : selected_products[_selected_group_idx][_selected_idx].quantity, 'skip_if_max' : 1}, function(set_qty) {
									// update new qty
									_new_qty = parseInt(available) + parseInt(set_qty);

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
						
						$('.prod-varian-product-item').mousedown(function(e) {
							$(this).parent().addClass('active');
						});

						$('.prod-varian-product-item').mouseup(function(e) {
							$(this).parent().removeClass('active');
						});
						
						return false;
					});
					var releasing_facility_id = $('#facility_id').val();
					if(releasing_facility_id != 0)
						vitalc.checkout.check_quantities(releasing_facility_id);
				}else{
					b.modal.create({
						title: "Checkout Notification Error",
						width: 300,
						html: "<p>"+data.msg+"</p>",
						disableClose: true,
						buttons: {
							'Close': function(){ redirect('/main/products'); }
						}
					}).show();
				}
			}
		});
	};
	
	vitalc.checkout.check_quantities = function(facility_id){
		if(facility_id != 0) {
			var facility_items = vitalc.checkout.facility_items[facility_id];
			//console.log(facility_items);
			var products = vitalc.checkout.selected_products;
			$.each(products, function(index, item){
				var item_id = vitalc.checkout.products[index].item_id;
				if(item_id != 0) {
					//check qty
					var product_qty = item.qty;
					var facility_item = facility_items[item_id];
					var facility_item_qty = facility_item.qty - facility_item.qty_pending;
					if(facility_item_qty <= 0) facility_item_qty = 0;
					$(".product_"+index).html("In Stock: " + facility_item_qty);
					$(".product_"+index).attr('style', 'color:blue;display:block;');
					$(".product_"+index).show();
				}
			});
			
			var packages = vitalc.checkout.packages;
			$.each(packages, function(index, item){
				var qty = item.qty;
				var predefined_products = item.predefined_products;
				var selected_variants = item.selected_variants;
				
				$.each(predefined_products, function(product_index, product){
					var item_id = vitalc.checkout.products[product_index].item_id;
					if(item_id != 0) {
						//check qty
						var product_qty = item.qty;
						var facility_item = facility_items[item_id];
						var facility_item_qty = facility_item.qty - facility_item.qty_pending;
						$(".product_"+product_index).html("In Stock: " + facility_item_qty);
						$(".product_"+product_index).attr('style', 'color:blue;display:block;');
						$(".product_"+product_index).show();
					}
				});
				$.each(selected_variants, function(variant_index, variant){
					var item_id = vitalc.checkout.products[variant.product_id].item_id;
					if(item_id != 0) {
						//check qty
						var product_qty = item.qty;
						var facility_item = facility_items[item_id];
						var facility_item_qty = facility_item.qty - facility_item.qty_pending;
						$(".product_"+variant.product_id).html("In Stock: " + facility_item_qty);
						$(".product_"+variant.product_id).attr('style', 'color:blue;display:block;');
						$(".product_"+variant.product_id).show();
					}
				});
			});
			
			var rebates = vitalc.checkout.rebate_products;
			$.each(rebates, function(index, item){
				var item_id = item.item_id;
				if(item_id != 0) {
					//check qty
					var facility_item = facility_items[item_id];
					var facility_item_qty = facility_item.qty - facility_item.qty_pending;
					$(".product_"+item.product_id).html("In Stock: " + facility_item_qty);
					$(".product_"+item.product_id).attr('style', 'color:blue;display:block;');
					$(".product_"+item.product_id).show();
				}
			});

			var vouchers = vitalc.checkout.selected_vouchers;
			if(vouchers != undefined)
			{
				$.each(vouchers, function(index, item){
					var voucher_id = item.voucher_id;
					var products = item.products;
				
					$.each(products, function(vp_index, vprod){
						var vprod_id = vprod.product_id;
						var item_id = vprod.item_id;
					
						if(item_id != 0) {
							//check qty
							var vprod_qty = vprod.quantity;
							var facility_item = facility_items[item_id];
							var facility_item_qty = facility_item.qty - facility_item.qty_pending;
							$(".product_"+vprod_id).html("In Stock: " + facility_item_qty);
							$(".product_"+vprod_id).attr('style', 'color:blue;display:block;');
							$(".product_"+vprod_id).show();
						}
					});
				});
			}
		}
	}
	
	vitalc.checkout.confirm = function(){
		var payment_type = $('#payment_type').val();
		var facility_id = $('#facility_id').val();
        var total_amount = parseFloat($("#cart_total_amount_hidden").val());
		var is_on_hold = $('#test').html();
		var funds_variance = $('#funds_variance').html();
		var gift_cheques_variance = $('#gift_cheques_variance').html();
		var gcep_variance = $('#gcep_variance').html();
		var on_hold_funds = $('#on_hold_funds').html();
		var enable_commission_variance_checking = $('#enable_commission_variance_checking').html();
		var enable_commission_on_hold_checking = $('#enable_commission_on_hold_checking').html();
		var member_email = $('#member_email').val();
	
		if(facility_id == 0)
		{
			var facility_select_modal = b.modal.create({
				title: 'No Depot Selected',
				html: 'Please select a Depot location to pick up your items.',
				width: 350
			});
			facility_select_modal.show();
			return;
		}
		
		if(member_email == '' && payment_type == 'bdo')
		{
			var bdo_error_modal = b.modal.create({
				title: 'Credit Card Payment Error',
				html: 'Email is needed for this transaction!',
				width: 480
			});

			bdo_error_modal.show();
			return;
		}

		/*if(payment_type == 'bdo')
		{
			var bdo_form_modal = b.modal.create({
				title: 'Credit Card Details',
				html: $('#bdo-form-template').html(),
				width: 480,
				buttons: {
					'Continue': function(){
						var payment_option_error = b.modal.create({
							title: 'Payment Option is Not Yet Available',
							html: 'This payment option is not yet available. Please select other payment options.',
							width: 350
						});
						payment_option_error.show();
						return;
					}
				}

			});
			bdo_form_modal.show();
			return;
		}
		
		if(payment_type == 'bdo')
		{
			var payment_option_error = b.modal.create({
				title: 'Payment Option is Not Yet Available',
				html: 'This payment option is not yet available. Please select other payment options.',
				width: 350
			});
			payment_option_error.show();
			return;
		}*/
		
		var vouchers = vitalc.checkout.selected_vouchers;
		if(vouchers != undefined)
		{
			$.each(vouchers, function(index, item){
				total_amount += parseFloat(item.price);
			});
		}
		
		var pretty_total_amount = numberFormat(total_amount,2);
        var variants_html = "";
		if(vitalc.checkout.has_packages && !vitalc.checkout.skipProductVariants)
		{
			for(var package_product_id in vitalc.checkout.packages)
			{
				var groupCount = 0;
				var groupAvailableCount = 0;
				for(var group in vitalc.checkout.packages[package_product_id].groups){
					groupCount++;
					groupAvailableCount += parseInt(vitalc.checkout.packages[package_product_id].groups[group].qty);
				}

				// if(groupCount != vitalc.checkout.packages[package_product_id].selected_variants.length)
				if(vitalc.checkout.packages[package_product_id].selected_variants.length == 0 && groupAvailableCount > 0){
					//console.log(vitalc.checkout.packages[package_product_id].selected_variants);
					variants_html += "<li>"+vitalc.checkout.products[package_product_id].product_name+"</li>";
				}				
			}
			
			if(variants_html != "")
			{
				var productVariantsModal = b.modal.create({
					title: 'Product Variants',
					width: 450,
					html: "<p>You still have not chosen product variants for the following package/s:</p><ul>"+variants_html+"</ul>"
				});	
				productVariantsModal.show();
				return false;
			}
		}
		vitalc.checkout.checkRemainingRebateAmount(function(_continue) 
		{
			if(_continue)
			{ 
			
				if (((payment_type=="gcep") || (payment_type=="giftcheque") || (payment_type=="funds")) && (is_on_hold == 1)) {
				
					if (payment_type=="giftcheque") payment_type_name = "gift cheques";
					if (payment_type=="funds") payment_type_name = "funds";
					if (payment_type=="gcep") payment_type_name = "gcep";
				
					b.modal.create({
						title: payment_type_name.toUpperCase() + ' Payment',
						width: 400,
						html: '<p>Sorry, your commissions are <strong>ON HOLD</strong>. You are not allowed to purchase online using ' + payment_type_name.toUpperCase() + ' as payment.</p><p>For more information, kindly contact the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com</p>'
					}).show();
					return false;
				
				} else {
			
					var payment_type_name = payment_type;
					if (payment_type=="bdo") payment_type_name = "credit card";
					if (payment_type=="otc") payment_type_name = "over the counter";
					if (payment_type=="giftcheque") payment_type_name = "gift cheques";
					if (payment_type=="gcep") payment_type_name = "gcep";

					if (payment_type=="giftcheque"){
						if(total_amount > vitalc.member.gift_cheques){
							b.modal.create({
								title: payment_type_name.toUpperCase() + ' Payment',
								width: 300,
								html: 'Not enough Gift Cheques.'
							}).show();
							return false;
						}
						
						if (enable_commission_variance_checking) {
							if (gift_cheques_variance < 0) {
								b.modal.create({
									title: payment_type_name.toUpperCase() + ' Payment :: On-Hold',
									width: 450,
									html: 'You are not allowed to proceed with the Purchase using <strong>GIFT CHEQUES</strong>. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.'
								}).show();
								return false;
							}
						}						
					}
					
					if (payment_type=="gcep"){
						if(total_amount > vitalc.member.gcep){
							b.modal.create({
								title: payment_type_name.toUpperCase() + ' Payment',
								width: 300,
								html: 'Not enough GCEP.'
							}).show();
							return false;
						}
						
						if (enable_commission_variance_checking) {													
							if (gcep_variance < 0) {
								b.modal.create({
									title: payment_type_name.toUpperCase() + ' Payment :: On-Hold',
									width: 450,
									html: 'You are not allowed to proceed with the Purchase using <strong>GCEP</strong>. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.'
								}).show();
								return false;
							}
						}						
					}

					if (payment_type=="funds"){
					
						if(total_amount > vitalc.member.funds){
							b.modal.create({
								title: payment_type_name.toUpperCase() + ' Payment',
								width: 300,
								html: 'Not enough Funds.'
							}).show();
							return false;
						}
						
						if (enable_commission_variance_checking) {													
							if (funds_variance < 0) {
								b.modal.create({
									title: payment_type_name.toUpperCase() + ' Payment :: On-Hold',
									width: 450,
									html: 'You are not allowed to proceed with the Purchase using <strong>FUNDS</strong>. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.'
								}).show();
								return false;
							}
						}
						
						//alert(total_amount + "|" + vitalc.member.funds + "|" + vitalc.member.on_hold_funds);
						
						// -- START 20130819
						if (enable_commission_on_hold_checking) {
							if ((vitalc.member.funds - total_amount) < Math.abs(vitalc.member.on_hold_funds)) {
							
								// transaction exceeds on hold funds value
								b.modal.create({
									title: payment_type_name.toUpperCase() + ' Payment :: On-Hold Funds',
									width: 450,
									html: 'You are not allowed to proceed with the Purchase using <strong>FUNDS</strong> - will exceed On-Hold FUNDS. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.'
								}).show();
								return false;								
							}
						}	
						// -- END	
					}

					if(payment_type == "giftcheque")
					{
						//amount paid should be visible by 1
						var min_gc = 1;
						var gc_amount = 0;

						gc_amount = Math.ceil(total_amount);

						if(gc_amount % min_gc != 0) gc_amount = parseInt((Math.floor(gc_amount / min_gc) * min_gc)) + parseInt(min_gc);

						var pretty_gc_amount = numberFormat(gc_amount);
						if(gc_amount > vitalc.member.gift_cheques){
							b.modal.create({
								title: payment_type_name.toUpperCase() + ' Payment',
								width: 400,
								html: '<p>Amount to be used: '+pretty_gc_amount+'</p><p>Not enough Gift Cheques.</p>'
							}).show();
							return false;
						}


					}
					
					if(payment_type == "gcep")
					{
						//amount paid should be visible by 1
						var min_gc = 1;
						var gc_amount = 0;

						gc_amount = Math.ceil(total_amount);

						if(gc_amount % min_gc != 0) gc_amount = parseInt((Math.floor(gc_amount / min_gc) * min_gc)) + parseInt(min_gc);

						var pretty_gc_amount = numberFormat(gc_amount);
						if(gc_amount > vitalc.member.gcep){
							b.modal.create({
								title: payment_type_name.toUpperCase() + ' Payment',
								width: 400,
								html: '<p>Amount to be used: '+pretty_gc_amount+'</p><p>Not enough GCEP.</p>'
							}).show();
							return false;
						}


					}

					var _html = "<p>You are checking out through <b>" + payment_type_name.toUpperCase() + "</b>. The Total Order Amount is <b>"+ pretty_total_amount +"</b>.</p>";

					if((payment_type == "giftcheque") || (payment_type == "gcep"))
					{
						_html = _html + "<p>An amount of <strong>"+pretty_gc_amount+"</strong> shall be deducted from your "+payment_type_name.toUpperCase()+"</p>";
					}
					_html = _html + "<p>Do you want to proceed?</p>";

					_html = _html + "<p><strong>NOTE: Products are subject to availability upon confirmation of Cashier.</strong></p>";

					var checkoutConfirmModal = b.modal.create({
						title: 'Checkout Confirmation',
						width: 450,
						disableClose: true,
						html: _html,
						buttons: {				
							'Yes' : function() {
								checkoutConfirmModal.hide();	
								var gc_buyables = 1;
								if(payment_type == 'giftcheque')
								{
									vitalc.checkout.check_gc_buyable(payment_type,function(gc_buyables){
										if(gc_buyables == 1) vitalc.checkout.process(payment_type, facility_id);
									});
								}
								else if(payment_type == 'gcep')
								{
									vitalc.checkout.check_gc_buyable(payment_type,function(gc_buyables){
										if(gc_buyables == 1) vitalc.checkout.process(payment_type, facility_id);
									});
								}
								else if(payment_type == 'funds')
								{
									vitalc.checkout.check_gc_exclusive(payment_type,function(gc_exclusive){
										if(gc_exclusive == 1) vitalc.checkout.process(payment_type, facility_id);
									});
								}
								else if(payment_type == 'cpoints')
								{
									vitalc.checkout.check_cpoints_buyable(payment_type,function(cpoints_buyables){
										if(cpoints_buyables == 1) vitalc.checkout.process(payment_type, facility_id);
									})
								}
								else
								{

									//vitalc.checkout.process(payment_type, facility_id);

									vitalc.checkout.check_mixing(payment_type,function(mixing){
										if(mixing == 1) vitalc.checkout.process(payment_type, facility_id);
									});

								}
							},
							'No' : function() {
								checkoutConfirmModal.hide();
							}
						}
					});	
					checkoutConfirmModal.show();
				}
			}
		});
				
		
		
	};
	
	vitalc.checkout.check_gc_buyable = function(payment_type,cb){
		showLoading();
		beyond.request({
			with_overlay: false,
			url: '/cart/checkout/check_gc_buyable',
			data: {
				'is_cart': 0,
				'payment_type': payment_type,
				'packages': vitalc.checkout.packages,
				'hash':vitalc.checkout.hash,
			},
			on_success: function(data) {
				if(data.status == 1)
				{ 
					// all buyable by gc
					//continue
					if (_.isFunction(cb)) cb.call(this, 1);
				} else if(parseInt(data.status) == 0) { //at least one is buyable

					hideLoading();
					if(data.data.error == "gc_buyable")
					{
						var gc_buyable_modal = b.modal.create({
							title: 'Notification',
							width: 500,
							html: data.msg,
							disableClose: true,
							buttons: {
								'Confirm' : function() {
									//remove from cart
									var product_ids = data.data.product_ids;
									$.each(product_ids, function(index, item) {
										beyond.request({
											url: '/cart/remove',
											data: {
												'cart_product_id' : item
											},
											on_success: function(data) {
												if (_.isFunction(cb)) cb.call(this, 1);
											}
										});

									});
									gc_buyable_modal.hide();	
								},
								'Cancel' : function() {
									gc_buyable_modal.hide();
									if (_.isFunction(cb)) cb.call(this, 0);
								}
							}
						});
						gc_buyable_modal.show();
					}
					else
					{
						var errorModal = b.modal.create({
							title: 'Error Notification',
							width: 350,
							html: data.msg
						}).show();
					}
				}
			}
		});
	}
	
	vitalc.checkout.check_gc_exclusive = function(payment_type,cb){
		showLoading();
		beyond.request({
			with_overlay: false,
			url: '/cart/checkout/check_gc_exclusive',
			data: {
				'is_cart': 0,
				'payment_type': payment_type,
				'packages': vitalc.checkout.packages,
				'hash':vitalc.checkout.hash,
			},
			on_success: function(data) {
				if(data.status == 1)
				{ 
					// no gc exclusive
					//continue
					if (_.isFunction(cb)) cb.call(this, 1);
				} else if(parseInt(data.status) == 0) { 
					//at least one is gc exclusive
					hideLoading();
					if(data.data.error == "gc_exclusive")
					{
						var gc_exclusive_modal = b.modal.create({
							title: 'Notification',
							width: 500,
							html: data.msg,
							disableClose: true,
							buttons: {
								'Confirm' : function() {
									//remove from cart
									var product_ids = data.data.product_ids;
									$.each(product_ids, function(index, item) {
										beyond.request({
											url: '/cart/remove_gc_exclusive',
											data: {
												'cart_product_id' : item
											},
											on_success: function(data) {
												if (_.isFunction(cb)) cb.call(this, 1);
											}
										});
									});
									gc_exclusive_modal.hide();	
								},
								'Cancel' : function() {
									gc_exclusive_modal.hide();
									if (_.isFunction(cb)) cb.call(this, 0);
								}
							}
						});
						gc_exclusive_modal.show();
					}
					else
					{
						var errorModal = b.modal.create({
							title: 'Error Notification',
							width: 350,
							html: data.msg
						}).show();
					}
				}
			}
		});
	}
	
	vitalc.checkout.check_cpoints_buyable = function(payment_type, cb){
		showLoading();
		beyond.request({
			with_overlay: false,
			url: '/cart/checkout/check_cpoints_buyable',
			data: {
				'is_cart' : 0,
				'payment_type' : payment_type,
				'packages' : vitalc.checkout.packages,
				'hash': vitalc.checkout.hash,
			},
			on_success: function(data) {
				if(data.status == 1) {
					// no mixed products
					// continue
					if(_.isFunction(cb)) cb.call(this, 1);
				} else if(parseInt(data.status) == 0) {
					//there are non-cpoints products
					hideLoading();
					if(data.data.error == "non_cpoints")
					{
						var cpoints_exclusive_modal = b.modal.create({
							title: 'Notification',
							width: 500,
							html: data.msg,
							disableClose: true,
							buttons: {
								'Confirm' : function() {
									//remove from cart
									var product_ids = data.data.product_ids;
									$.each(product_ids, function(index, item) {
										beyond.request({
											url: '/cart/remove_non_cpoints',
											data: {
												'cart_product_id' : item
											},
											on_success: function(data) {
												if (_.isFunction(cb)) cb.call(this, 1);
											}
										});
									});
									cpoints_exclusive_modal.hide();	
								},
								'Cancel' : function() {
									cpoints_exclusive_modal.hide();
									if (_.isFunction(cb)) cb.call(this, 0);
								}
							}
						});
						cpoints_exclusive_modal.show();
					}
					else
					{
						var errorModal = b.modal.create({
							title: 'Error Notification',
							width: 350,
							html: data.msg
						}).show();
					}
				}
			}
		});
	}
	
	vitalc.checkout.check_mixing = function(payment_type,cb){
		showLoading();
		beyond.request({
			with_overlay: false,
			url: '/cart/checkout/check_mixing',
			data: {
				'is_cart': 0,
				'payment_type': payment_type,
				'packages': vitalc.checkout.packages,
				'hash':vitalc.checkout.hash,
			},
			on_success: function(data) {
				if(data.status == 1)
				{ 
					// no mixed products
					//continue
					if (_.isFunction(cb)) cb.call(this, 1);
				} else if(parseInt(data.status) == 0) { 
					//at least one is gc exclusive 
					hideLoading();
					if(data.data.error == "gc_mixing")
					{
						var gc_exclusive_modal = b.modal.create({
							title: 'Notification',
							width: 500,
							html: data.msg,
							disableClose: true,
							buttons: {
								'Back to Cart Editing' : function() {
									redirect("/cart");
									if (_.isFunction(cb)) cb.call(this, 0);
								}
							}
						});
						gc_exclusive_modal.show();
					}
					else
					{
						var errorModal = b.modal.create({
							title: 'Error Notification',
							width: 350,
							html: data.msg
						}).show();
					}
				}
			}
		});
	}
	
	vitalc.checkout.process = function(payment_type, facility_id){
		showLoading();
		beyond.request({
			with_overlay: false,
			url : '/cart/checkout/process',
			data : {
				'is_cart': vitalc.checkout.is_cart,
				'payment_type': payment_type,
				'facility_id': facility_id,
				'account_id': vitalc.checkout.account_id,
				'hash': vitalc.checkout.hash,
				'member_status_id': vitalc.checkout.member_status_id,
				'member_type_id': vitalc.checkout.member_type_id,
				'packages': vitalc.checkout.packages,
				'rebate_products': vitalc.checkout.rebate_products,
				'vouchers': vitalc.checkout.selected_vouchers
			},
			on_success : function(data) {
				hideLoading();
				if (parseInt(data.status) == 1)	{
					if (payment_type=="paypal") {
                        showLoading();
						$('body').append(_.template($('#paypal-form-template').html(), {'html' : data.data.html}));
						$("#paypal_form").submit();
                    }else if (payment_type=="bdo") {
						showLoading();
						$('body').append(_.template($('#bdo-form-template').html(), {'html' : data.data.html}));
						$("#bdo_form").submit();
	                } else {
                        var paymentModal = b.modal.create({
							title: payment_type.toUpperCase() + ' Payment',
							width: 450,
							disableClose: true,
							html: data.data.html,
							buttons: {
								'Close' : function() {
									paymentModal.hide();
									vitalc.checkout.success = true;
									redirect("/members/orders");
								}
							}
						});
						paymentModal.show();
                    }
						
                } else if(parseInt(data.status) == 0) {
					b.modal.create({
						title: 'Notification',
						width: 300,
						html: 'ERROR :: ' + data.msg
					}).show();
					
                } else if(parseInt(data.status) == 2) {
					var stocks_modal = b.modal.create({
						title: data.msg,
						width: 500,
						html: data.data.html
					});
					stocks_modal.show();
				}
			}
		});
		
	};
	
	vitalc.checkout.swap = {};
	vitalc.checkout.swap.current_package_product_id = 0;
	var selected_products = {};
	vitalc.checkout.swap.initialize = function(product_id){
		
		vitalc.checkout.swap.current_package_product_id = product_id;
		selected_products = {};
		
		$.each(vitalc.checkout.packages[product_id].selected_variants,function(index,item) {
			if (typeof(selected_products[item.group]) == 'undefined') {
				selected_products[item.group] = {};
			}
			if (typeof(selected_products[item.group][item.product_id]) == 'undefined') {
				selected_products[item.group][item.product_id] = {};
				selected_products[item.group][item.product_id].child_product_id = item.product_id;
				selected_products[item.group][item.product_id].group = item.group;
				selected_products[item.group][item.product_id].quantity = item.qty;
			}
		});
		
		var variant_modal = b.modal.create({
			title: 'Package\'s Product Variants',
			width: 890,
			html: _.template($('#package-variant-template').html(), vitalc.checkout.packages[product_id]),
			buttons: {
				'Save': function(){
					// check if all available variant are used
					var _available_count = 0;
					$("a.prod-varian-group-item small span.prod-varian-group-available").each(function(idx){
						_available_count += parseInt($(this).html());
					});
					
					if (_available_count > 0) {
						b.modal.create({
							title: "Package's Product Variants",
							html: "You still have some products that have not been allocated yet.",
							width: 400
						}).show();
						return false;
					}
					
					vitalc.checkout.swap.save();
					variant_modal.hide();
				}
			}
		});
		variant_modal.show();
		
	};
	
	vitalc.checkout.swap.save = function(){
		
		$('.prod-variant-list a small span').each(function(index, elem){
			
			var _group = $(elem).data("group");
			var _qty = parseInt($(elem).html());
			
			vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].groups[_group].qty = _qty;
			
		});
		
		vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].selected_variants = [];
		
		var total_price = 0;
		$('#prod-variant-selected-list li a').each(function(index, elem){
			
			var _product_id = $(elem).data("index");
			var _group = $(elem).data("group");
			var _qty = $(elem).find('.prod-varian-selected-available').html();
			var _price = $(elem).data("price");
			
			total_price = parseFloat(total_price) + parseFloat(_price) * parseFloat(_qty);
			
			vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].selected_variants.push({'product_id':_product_id,'qty':_qty,'group':_group,'price':_price});
		});
		
		if(vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].product_type_id == vitalc.checkout.p2p_package_id) {			
			var current_product_amount = $("tr[data-product_id='"+vitalc.checkout.swap.current_package_product_id+"']").children().children('.amount_after_vat');
			var current_product_html = current_product_amount.html().replace(',',"");
			
			//update total amount
			var total_amount = $('#cart_total_amount_hidden').val();
			var original_amount = parseFloat(total_amount) - parseFloat(current_product_html);
			
			total_amount = parseFloat(original_amount) + parseFloat(total_price);
			total_amount = parseFloat(total_amount).toFixed(2);
			$('#cart_total_amount_hidden').val(total_amount);
		
			var total_amount_display = ReplaceNumberWithCommas(total_amount);
		
			$('#cart_total_amount').html(total_amount_display);
			
			//update per item amount
			var product_row_html = $("tr[data-product_id='"+vitalc.checkout.swap.current_package_product_id+"']").children().children('.amount_after_vat');
			var product_row = $("tr[data-product_id='"+vitalc.checkout.swap.current_package_product_id+"']").children().children('.amount_after_vat_hidden');
			var amount_after_vat = product_row.val();
			var new_product_price = parseFloat(amount_after_vat) + parseFloat(total_price);
			new_product_price = new_product_price.toFixed(2);
			new_product_price = ReplaceNumberWithCommas(new_product_price);
			product_row_html.html(new_product_price);
		}

		if(vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].selected_variants.length > 0){
			$('#btn-product-variants-'+vitalc.checkout.swap.current_package_product_id).html(' Edit Product Variants');
		}else{
			$('#btn-product-variants-'+vitalc.checkout.swap.current_package_product_id).html(' Add Product Variants');
		}
		
		$('#package-product-variants-'+vitalc.checkout.swap.current_package_product_id).html('');
		$.each(vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].selected_variants, function(index, item){
			var variant_product_id = item.product_id;
			$('#package-product-variants-'+vitalc.checkout.swap.current_package_product_id).append("<dd>"+vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].selected_variants[index].qty+" x "+vitalc.checkout.products[vitalc.checkout.packages[vitalc.checkout.swap.current_package_product_id].selected_variants[index].product_id].product_name+"<span class='product_"+variant_product_id+"' style='display:none;color:green'>In Stock:</span></dd>");
		});
		var releasing_facility_id = $('#facility_id').val();
		if (releasing_facility_id != 0)
			vitalc.checkout.check_quantities(releasing_facility_id);
	}
	
	vitalc.checkout.browseRebateProduct = function() {
		
		vitalc.checkout.browseProductModal = b.modal.create({
			title: "Browse Products",
			html: _.template($('#product-grid-template').html(),{}),
			width: 830,
		});
		vitalc.checkout.browseProductModal.show();

		// display loading
		$('.product-box-grids').hide();
		$('#product-browse-grid-1').html('<div>Loading... <img src='+vitalc.settings.loading_image_url+' alt="" /></div>');
		$('#product-browse-grid-1').show();
		
		vitalc.checkout.getProducts(function() {
			prod_grid_pages = [];
			prod_grid_pages.push('Rebate Products');
			// display the product types
			//orig
			html = _.template($('#product-grid-items-template').html(), {'grid_type' : 'categories', 'prefix' : 'rebate-', 'items' : vitalc.checkout.items.item_categories, 'pages' : prod_grid_pages });
			
			$('#product-browse-grid-1').html(html);
			
			vitalc.checkout.updateProductBrowsePager();
			
		});

	};
	
	vitalc.checkout.updateProductBrowsePager = function() {
		// update pager
		var html = _.template($('#product-grid-pager-template').html(), {'pages' : prod_grid_pages});
		$('#product-browse-pager').html(html);
		
		$('.product-browse-pager-item').click(function(e) {
			e.preventDefault();
			var idx = $(this).data('index');
			idx = parseInt(idx) + 1;
			$('.product-box-grids').hide();
			$('#product-browse-grid-'+idx).show();
			$( ".modal-footer a[id$='btn_add_vouchers']" ).hide(); //hide 'Add Vouchers' button
			prod_grid_pages.splice(idx);
			vitalc.checkout.updateProductBrowsePager();
		});
	};
	
	vitalc.checkout.computeSummary = function()
	{
		vitalc.checkout.summary.pdv_amount = 0;
		vitalc.checkout.summary.non_discount_amount = 0;
		vitalc.checkout.summary.vatable_discount = 0;
		vitalc.checkout.summary.vat = 0;
		vitalc.checkout.summary.sub_total = 0;
		
		$.each(vitalc.checkout.selected_products, function(index, item) {
			
			var price = 0;
			var vat_amount = 0;
			var vat_sales = 0;
			var product = vitalc.checkout.products[item.product_id];

			price = parseFloat(product.member_price);
			
			if (product.is_vatable == '1') {
				vat_sales = parseFloat(price / (vitalc.settings.vat_percent / 100 + 1)).toFixed(2) * parseInt(item.qty);
				vat_amount = parseFloat((parseFloat((price - parseFloat(price / (vitalc.settings.vat_percent / 100 + 1))).toFixed(4)) * parseInt(item.qty)).toFixed(2));
			}
			
			vitalc.checkout.summary.vat += vat_amount;
			vitalc.checkout.summary.sub_total = vitalc.checkout.summary.sub_total + (price * parseInt(item.qty));
			
			if (typeof(vitalc.checkout.products[item.product_id]) != 'undefined')
			{
				if (vitalc.checkout.products[item.product_id].product_type_id == '10') {
					vitalc.checkout.summary.pdv_amount += (price * parseInt(item.qty));
				}
				else if (vitalc.checkout.products[item.product_id].product_type_id == '8' || vitalc.checkout.products[item.product_id].product_type_id == '9' || vitalc.checkout.products[item.product_id].product_type_id == '12' || vitalc.checkout.products[item.product_id].product_type_id == '13') {
					//Marketing Materials Pack, Promo Packages, GC Packages, Miscellaneous Products
					vitalc.checkout.summary.non_discount_amount += (price * parseInt(item.qty));
				}
				else if (vitalc.checkout.products[item.product_id].product_type_id == '1' && vitalc.checkout.products[item.product_id].product_line_id == '0') {
					//Marketing Materials
					vitalc.checkout.summary.non_discount_amount += (price * parseInt(item.qty));
				}
				else
				{
					vitalc.checkout.summary.vatable_discount += vat_amount;
				}
			}
		});
	}
	
	vitalc.checkout.computeProductRebate = function(rebate_products) {
	
		var used_rebate_amount = 0;
		var remaining_rebate_amount = 0;
		var rebate_amount = 0;
		
		$.each(rebate_products, function(index, item) {
			
			var price = 0;
			var vat_amount = 0;
			var rate_id = 2;
			price = parseFloat(item.member_price);

			used_rebate_amount = used_rebate_amount + (price * parseInt(item.quantity));
		});
				
		if (vitalc.member.member_type_id == 3 && (vitalc.checkout.summary.sub_total) >= vitalc.settings.stockist_min_amount) {
			var _rebate_percent = 0.05;
			rebate_amount = parseFloat(((vitalc.checkout.summary.sub_total - vitalc.checkout.summary.pdv_amount - vitalc.checkout.summary.non_discount_amount - vitalc.checkout.summary.vatable_discount) * 0.05).toFixed(2));

		} else {
			rebate_amount = 0;
		}
		
		remaining_rebate_amount = parseFloat((rebate_amount - used_rebate_amount).toFixed(2));
		
		return {'rebate_amount' : rebate_amount, 'used_rebate_amount' : used_rebate_amount, 'remaining_rebate_amount' : remaining_rebate_amount};
	};
	
	vitalc.checkout.checkRemainingRebateAmount = function(cb) {
		if(vitalc.checkout.summary.remaining_rebate_amount > 0 && vitalc.member.member_type_id == 3)
		{
			var html = "<p>You still have <strong>"+ numberFormat(vitalc.checkout.summary.remaining_rebate_amount,2) +"</strong> worth of product rebate points remaining.</p><p>Do you want to proceed?</p>";
			
			if($("#payment_type").val() == "cash") {
				html = "<p>You have <strong>"+ numberFormat(vitalc.checkout.summary.remaining_rebate_amount,2) +"</strong> worth of product rebate points. You can use the points in any Vital C Depot when you process this transaction.</p><p>Do you want to proceed?</p>";
			}
			
			var rebate_modal = b.modal.create({
				title: "Product Rebate Notification",
				html: html,
				width: 350,
				disableClose: true,
				buttons: {
					"Yes": function() {
						rebate_modal.hide();
						if (_.isFunction(cb)) cb.call(this,true);
					},
					"No": function() {
						rebate_modal.hide();
						if (_.isFunction(cb)) cb.call(this,false);
					}
				}
			});
			
			rebate_modal.show();
		}
		else
		{
			if (_.isFunction(cb)) cb.call(this,true);
		}
	};
	
	vitalc.checkout.getProducts = function(cb) {
	
		b.request({
			with_overlay: true,
			url: '/cart/checkout/get_product_list',
			data: {},
			on_success: function(data, status) {
				vitalc.checkout.items = data.data;

				// do a little data processing
				vitalc.checkout.items.product_types.products = [];

				$.each(vitalc.checkout.items.products, function(index, item) {

					if (typeof(vitalc.checkout.items.item_categories[item.item_sub_type_id]) != 'undefined') {
						if (typeof(vitalc.checkout.items.item_categories[item.item_sub_type_id].products) == 'undefined')
							vitalc.checkout.items.item_categories[item.item_sub_type_id].products = [];

						vitalc.checkout.items.item_categories[item.item_sub_type_id].products.push(_.clone(item));
					}


					if (typeof(vitalc.checkout.items.product_types[item.product_type_id]) != 'undefined') {
						if (typeof(vitalc.checkout.items.product_types[item.product_type_id].products) == 'undefined')
							vitalc.checkout.items.product_types[item.product_type_id].products = [];

						vitalc.checkout.items.product_types[item.product_type_id].products.push(_.clone(item));
					}
						

				});

				if (_.isFunction(cb)) cb.call(this);
			}
		});
		
	};
	
	vitalc.checkout.getProductImage = function(product_id) {
		
		var url = '';
		if (typeof(vitalc.checkout.items.products[product_id]) != 'undefined') {
			if (typeof(vitalc.checkout.items.products[product_id].image_filename) != 'undefined') {
				var _images = $.parseJSON(vitalc.checkout.items.products[product_id].image_filename);
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
	
	vitalc.checkout.addRebateProduct = function(prod) {
		var product = prod;
		vitalc.showNumpad({'title' : 'Enter Quantity', 'default' : 1, 'with_decimal' : false, 'min' : 1}, function(set_qty) {
			
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
			
			vitalc.checkout.checkIfPackage(product, set_qty, function(prod) {
					var qty = set_qty;
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


					var _rebate = vitalc.checkout.computeProductRebate(_rebate_products);
					
					if (_rebate.remaining_rebate_amount - vitalc.checkout.summary.used_rebate_amount < 0) {

						var modal = b.modal.create({
							title: "Add Rebate Products Error!",
							html: _.template("<p>You have entered too many products amounting of <strong><%= numberFormat(total_rebate, 2) %></strong> that exceeds the remaining rebate amount of <strong><%= numberFormat(rebate_amount, 2) %><strong>.</p><p>Please try again.</p>", {'total_rebate' : _rebate.used_rebate_amount, 'rebate_amount' : vitalc.checkout.summary.remaining_rebate_amount }),
							width: 440,
						});
						modal.show();

					} else {

						if (typeof(vitalc.checkout.rebate_products[prod.product_id]) != 'undefined') {
							vitalc.checkout.rebate_products[prod.product_id].quantity = parseInt(vitalc.checkout.rebate_products[prod.product_id].quantity) + parseInt(set_qty);
						} else {
							vitalc.checkout.rebate_products[prod.product_id] = {
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
						vitalc.checkout.summary.rebate_amount = _rebate.rebate_amount;
						vitalc.checkout.summary.remaining_rebate_amount = _rebate.remaining_rebate_amount - vitalc.checkout.summary.used_rebate_amount;
						vitalc.checkout.summary.used_rebate_amount = parseFloat(vitalc.checkout.summary.used_rebate_amount) + parseFloat(_rebate.used_rebate_amount);
						$("#product-rebate-value").html(numberFormat(vitalc.checkout.summary.remaining_rebate_amount,2));

						
						
						
						
						vitalc.checkout.displayProductRebates();
						
					}
			});
		});
	};
	
	vitalc.checkout.checkIfPackage = function(product, new_quantity, cb) {

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
					var product_name = vitalc.checkout.items.products[item.child_product_id].product_name;
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
					var product_name = vitalc.checkout.items.products[item.child_product_id].product_name;
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

					vitalc.checkout.showNumpad({'title' : 'Enter Quantity', 'default' : 1, 'with_decimal' : false, 'min' : 1, 'max' : _new_qty, 'skip_if_max' : 1}, function(set_qty) {

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
						} else {
							selected_products[_group_idx][_product_id].quantity+=set_qty;
						}

						_list_tag = '';
						$.each(selected_products, function(i, group) {
							$.each(group,function(index,item) {
								var product_name = vitalc.checkout.items.products[item.child_product_id].product_name;
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

							vitalc.checkout.showNumpad({'title' : 'Enter Quantity to Deduct', 'default' : 1, 'with_decimal' : false, 'min' : 1, 'max' : selected_products[_selected_group_idx][_selected_idx].quantity, 'skip_if_max' : 1}, function(set_qty) {

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
	
	
	vitalc.checkout.displayProductRebates = function() {
		var rebate_html = "";
		
		if(_.isEmpty(vitalc.checkout.rebate_products))
		{
			$("#cart-rebate-details").html("<tr><td colspan='5'>&nbsp;</td></tr>");
			return;
		}
		
		$.each(vitalc.checkout.rebate_products,function(index,item) {
			var amount = item.quantity * item.member_price;
			var rebate_product_id = item.product_id;
			rebate_html += "<tr><td>"+item.product_name+"<span class='product_"+rebate_product_id+"' style='display:none;color:green'>In Stock:</span></td><td>"+item.quantity+"</td><td>"+item.member_price+"</td><td>"+amount.toFixed(2)+"</td><td><button class='btn btn-tiny btn-item-action btn-item-remove' data-id='"+item.product_id+"'><i class='icon-remove'></i></button></td></tr>";
		});
		$("#cart-rebate-details").html(rebate_html);
		var releasing_facility_id = $('#facility_id').val();
		if(releasing_facility_id != 0)
			vitalc.checkout.check_quantities(releasing_facility_id);
	};
	
	vitalc.checkout.removeRebateProduct = function(product_id) {
		
		delete vitalc.checkout.rebate_products[product_id];
		var _rebate = vitalc.checkout.computeProductRebate(vitalc.checkout.rebate_products);
		$("#product-rebate-value").html(_rebate.remaining_rebate_amount.toFixed(2));
		vitalc.checkout.summary.rebate_amount = _rebate.rebate_amount;
		vitalc.checkout.summary.used_rebate_amount = _rebate.used_rebate_amount;
		vitalc.checkout.summary.remaining_rebate_amount = _rebate.remaining_rebate_amount;
		vitalc.checkout.displayProductRebates();
	};

	vitalc.checkout.displayVariants = function(){
		
		$.each(vitalc.checkout.packages,function(index,item) {
			if(!_.isEmpty(item.selected_variants))
			{
				$('#package-product-variants-'+index).html("");
				$.each(item.selected_variants,function(i,v) {
					var variant_product_id = v.product_id;
					$('#package-product-variants-'+index).append("<dd>"+vitalc.checkout.packages[index].selected_variants[i].qty+" x "+vitalc.checkout.products[v.product_id].product_name+"<span class='product_"+variant_product_id+"' style='display:none;color:green'>In Stock:</span></dd>");
				});
			}
		});
		var releasing_facility_id = $('#facility_id').val();
		if(releasing_facility_id != 0)
			vitalc.checkout.check_quantities(releasing_facility_id);
	}
	
	
	vitalc.checkout.getVoucherTypes = function(account_status) {
		b.request({
			with_overlay: true,
			url: '/cart/checkout/get_voucher_types',
			data: { account_status: account_status },
			on_success: function(data, status) {
				if(data.data.types != '' || account_status == 1)
				{
					vitalc.checkout.browseVoucherModal = b.modal.create({
						title: "Redeem Vouchers",
						html: _.template($('#voucher-type-template').html(),{ 'voucher_types' : data.data.types}),
						width: 800,
						disableClose: true,
						buttons: {
							"Add Vouchers": function() {
								$('#btn-add_vouchers').click();
							},
							'Close': function() {
								vitalc.checkout.browseVoucherModal.hide();
							}
						}
					});
					vitalc.checkout.browseVoucherModal.show();
					var modal_id = vitalc.checkout.browseVoucherModal.id;
					$('#'+modal_id).css('margin-top', '-45px');
					$( ".modal-footer a[id$='btn_add_vouchers']" ).hide(); //hide 'Add Vouchers' button
					
					// update breadcrumb
					prod_grid_pages=['Select Voucher Type'];
					vitalc.checkout.updateProductBrowsePager();
					
					// load vouchers on click
					$('.btn-voucher-type').click(function(e){ 
						e.preventDefault();

						var _voucher_type_id = $(this).attr('voucher-type-id');
						var _voucher_type_name= $(this).text();
						
						// update breadcrumb
						prod_grid_pages.push(_voucher_type_name);
						vitalc.checkout.updateProductBrowsePager();
						
						vitalc.checkout.browseVoucher(_voucher_type_id);
					});
				}
				else
				{
					var error_modal = b.modal.create({
						title: 'No Active Accounts',
						width: 300,
						html: 'You have no active accounts. You cannot redeem your vouchers.',
						disableClose: true,
						buttons: {
							'Close': function(){
								error_modal.hide();
							}
						}
					});
					error_modal.show();
				}
			}
		});
	};
	
	
	vitalc.checkout.getVouchers = function(search, voucher_type_id) {
		$( ".modal-footer a[id$='btn_add_vouchers']" ).hide(); //hide 'Add Vouchers' button
		
		var selected_vouchers_count = 0;
		if(vitalc.checkout.selected_vouchers != undefined)
		{
			$.each(vitalc.checkout.selected_vouchers, function(index, item){
				if(item.voucher_type_id == voucher_type_id) {
					selected_vouchers_count++;
				}
			});
		}
		
		//console.log('/cart/checkout/get_voucher_list_by_voucher_code/'+voucher_type_id+'/'+search);
		b.request({
			with_overlay: true,
			url: '/cart/checkout/get_voucher_list_by_voucher_code/'+voucher_type_id+'/'+search,
			data: {
				'selected_vouchers' : selected_vouchers_count,
			},
			on_success: function(data, status) {
				
				if(data.status == 'error') {
					var error_modal = b.modal.create({
						title: 'Error in request.',
						html: data.msg,
						width: 300
					});
					error_modal.show();
					return;
				}
				
				var items = data.data.items;
				var _html = '';
				
				if(_.size(items)>0 && _.size(vitalc.checkout.vouchers)>0) {
					_html+="<div class='table_scroll_container'><table class='table table-striped table-bordered'><thead><tr><th style='width:" + (voucher_type_id==3?120:664) +"px'>Voucher Code</th>";
					
					if(voucher_type_id == 3) _html+="<th style='width:527px;'>Products</th>"; //show 'Products' column if P2P
					
					_html+="<th style='width:64px;text-align:center;'>Check All<br><input type=\"checkbox\" id=\"check_all_vouchers\" /></th></tr></thead><tbody>";
				
					var count = 0;
					$.each(items, function(index, item) {
						if (typeof(vitalc.checkout.selected_vouchers) == 'undefined' || (vitalc.checkout.selected_vouchers[item.voucher_id] == 'undefined')) 
							vitalc.checkout.selected_vouchers = {};
							
						if(!(item.voucher_id in vitalc.checkout.selected_vouchers))
						{
							++count;
							
							_html+='<tr><td style="width:' + (voucher_type_id==3?120:664) +'px">'+item.voucher_code+'</td>';
							
							//show products if P2P
							if(voucher_type_id == 3)
							{
								_html+='<td style="width:527px;">';
								$.each(item.products, function(_index, _item) {
									_html += "<dd>"+_item.quantity+" x "+_item.product_name+"<span class=\"product_"+_item.product_id+"\" style=\"color:blue;display:none;\"></span>"+"</dd>";
								});
								_html+='</td>';
							}
				
							_html+='<td style="width:64px;text-align:center;"><input type="checkbox" name="voucher_items" voucher-id="'+item.voucher_id+'" voucher-type-id="'+voucher_type_id+'" voucher-code="'+item.voucher_code+'" value="'+item.voucher_id+'" class="voucher-item"/></td></tr>';
						}
					});
					
					_html+="</tbody></table></div>";
					
					if(count == 0){
						_html="<p>No vouchers available.</p>";
					}
					else
					{
						_html+='<button id="btn-add_vouchers" voucher-type-id="'+voucher_type_id+'" class="btn btn-info" style="margin-bottom:10px;float:right;display:none;">Add Vouchers</button>';
						$( ".modal-footer a[id$='btn_add_vouchers']" ).show(); //show 'Add Vouchers' button
					}
					
				}else{
					_html="<p>No vouchers available.</p>";
				}
				
				$('.voucher-list').html(_html);
				
				$('#check_all_vouchers').click(function(){
					if($(this).attr('checked')) $('.voucher-item').attr('checked',true);
					else $('.voucher-item').attr('checked',false);
				});
				$('.voucher-item').click(function(){
					$('#check_all_vouchers').attr('checked',false);
				});
				$('#btn-add_vouchers').click(function(e){ 
					e.preventDefault();
					
					var _voucher_type_id = $(this).attr('voucher-type-id');
					var _selected_vouchers = new Array();
					
					$('input[name=voucher_items]:checked').each(function() {
					    _selected_vouchers.push($(this).attr('value'));
					});
					var _selected_vouchers_total = _selected_vouchers.length;
					
					if(_selected_vouchers_total>0)
					{
						if(_voucher_type_id == 3) //P2P vouchers
						{
							for(var i=0; i<_selected_vouchers_total; i++)
							{
								var _voucher_id = _selected_vouchers[i];
								
								if (typeof(vitalc.checkout.selected_vouchers) == 'undefined') {
									vitalc.checkout.selected_vouchers = {};
								}
								vitalc.checkout.selected_vouchers[_voucher_id] = {};
								vitalc.checkout.selected_vouchers[_voucher_id] = _.clone(vitalc.checkout.vouchers[_voucher_id]);
								delete vitalc.checkout.vouchers[_voucher_id];
							}
					
							vitalc.checkout.displayProductVouchers();
						}
						else //FPV/MPV vouchers
						{
							b.request({
								with_overlay: true,
								url: '/cart/checkout/get_voucher_product_list/' + _voucher_type_id,
								data: {},
								on_success: function(data, status) {
									
									if(data.status == 'error'){
										var error_modal = b.modal.create({
											title: 'No Results Found',
											html: data.msg,
											width: 350
										});
										error_modal.show();
										return;
									}
									
									vitalc.checkout.browseVoucherProductsModal = b.modal.create({
										title: "Choose (" + _selected_vouchers_total + ") from the set of products",
										html: _.template($('#voucher-products-template').html(),{ 'items' : data.data.items, 'voucher_type_id' : _voucher_type_id}),
										width: 830,
										disableClose: true,
										buttons: {
											'Add Products': function(){
												$('#btn-add_voucher_products').click();
												return;
											},
											'Close': function(){
												vitalc.checkout.browseVoucherProductsModal.hide();
												return;
											}
										}
									});
									vitalc.checkout.browseVoucherProductsModal.show();
								
									$('#btn-add_voucher_products').click(function(e){ 
										e.preventDefault();
										var total_qty = 0;
										
										$('.voucher-product-item').each(function() {
											var val = $(this).attr('value');
											if(val=="") val = 0;
											total_qty += parseInt(val);
										});
										
										
										if(_selected_vouchers_total==total_qty){
											
											var _selected_product_groups = new Array();
											$('.voucher-product-item').each(function() {
												var _qty = parseInt($(this).attr('value'));
												var _product_group_id = $(this).attr('data-product_group_id');
												var _product_group_name = $(this).attr('data-product_group_name');
												
												for(var i=0; i<_qty; i++)
												{
													_selected_product_groups.push({'product_group_id' : _product_group_id, 'product_group_name' : _product_group_name});
												}
											});
											//console.log('data.data.items');
											console.log(data.data.items);
											
											for(var i=0; i<_selected_vouchers_total; i++)
											{
												var _voucher_id = _selected_vouchers[i];
												var _product_group_id = _selected_product_groups[i].product_group_id;
												var _product_group_name = _selected_product_groups[i].product_group_name;
												vitalc.checkout.vouchers[_voucher_id].products = {};
												vitalc.checkout.vouchers[_voucher_id].products_group_name = _product_group_name;
												
												$.each(data.data.items[_product_group_id].products,function(idx,prod) {
													var product_id = prod.product_id;
													vitalc.checkout.vouchers[_voucher_id].products[product_id] = {};
													//vitalc.checkout.vouchers[_voucher_id].products[product_id] = _.clone(prod);
													vitalc.checkout.vouchers[_voucher_id].products[product_id].original_price = prod.original_price;
													vitalc.checkout.vouchers[_voucher_id].products[product_id].price = prod.price;
													vitalc.checkout.vouchers[_voucher_id].products[product_id].product_id = prod.product_id;
													vitalc.checkout.vouchers[_voucher_id].products[product_id].product_name = prod.product_name;
													vitalc.checkout.vouchers[_voucher_id].products[product_id].quantity = prod.quantity;
													vitalc.checkout.vouchers[_voucher_id].products[product_id].item_name = prod.item_name;
													vitalc.checkout.vouchers[_voucher_id].products[product_id].item_id = prod.item_id;
												});
												
												vitalc.checkout.vouchers[_voucher_id].original_price = data.data.items[_product_group_id].total_original_price;
												vitalc.checkout.vouchers[_voucher_id].price = data.data.items[_product_group_id].total_price;
												vitalc.checkout.vouchers[_voucher_id].quantity = 1;
									
												if (typeof(vitalc.checkout.selected_vouchers) == 'undefined') {
													vitalc.checkout.selected_vouchers = {};
												}
												vitalc.checkout.selected_vouchers[_voucher_id] = {};
												vitalc.checkout.selected_vouchers[_voucher_id] = _.clone(vitalc.checkout.vouchers[_voucher_id]);
												delete vitalc.checkout.vouchers[_voucher_id];
											}
					
											vitalc.checkout.browseVoucherProductsModal.hide();
											vitalc.checkout.displayProductVouchers();
											
										}
										else
										{
											var error_msg='';
											var error_html='';
											
											if(total_qty<_selected_vouchers_total){
												var tmp = (_selected_vouchers_total-total_qty);
												error_html = "Kindly select a total of " + tmp + " more product" + (tmp>1 ? 's':'');
											}
											else
											{
												var tmp = (total_qty-_selected_vouchers_total);
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
									
									$(".voucher-product-item").keydown(function(event) {
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
								}
							});
						}
					}
					else
					{
						var error_modal = b.modal.create({
							title: "Error: No Voucher",
							width: 300,
							html: "<p>No voucher selected. <br>Kindly select at least one voucher.</p>",
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
		});
	};
	
	vitalc.checkout.browseVoucher = function(voucher_type_id) {
		$('#product-browse-grid-2').html(_.template($('#voucher-browse-items-template').html(),{ 'items' : vitalc.checkout.vouchers}));
		$('.product-box-grids').hide();$('#product-browse-grid-2').show();
		
		$('#btn-voucher_code_search').click(function(e) {
			e.preventDefault();
			if( $(this).is(':disabled') ) { return false; }
	
			var search_code = $('#voucher_code').val();

			vitalc.checkout.getVouchers(search_code,voucher_type_id);
		});
	};
	
	vitalc.checkout.displayProductVouchers = function() {
		var voucher_html = "";
		
		if(_.isEmpty(vitalc.checkout.selected_vouchers))
		{
			$("#cart-voucher-details").html("<tr><td colspan='6'>&nbsp;</td></tr>");
			return;
		}
		else
		{		
			$.each(vitalc.checkout.selected_vouchers,function(index,item) {
				voucher_html += "<tr><td>"+item.voucher_code+"<dl>";
				
				if(!(item.products_group_name == null || item.products_group_name == '')) voucher_html += "<dd>"+item.products_group_name+"</dd>";
				
				// product details
				$.each(item.products,function(_index,_item) {
					voucher_html += "<dd>&nbsp;&nbsp;&nbsp;"+_item.quantity+" x "+_item.product_name+"<span class=\"product_"+_item.product_id+"\" style=\"color:blue;display:none;\"></span>"+"</dd>";
				});
				
				voucher_html+= "</dl></td><td>"+item.voucher_type_code+"</td>";
				voucher_html+="<td>"+item.quantity+"</td><td>"+_.numberFormat(item.original_price,2)+"</td><td>"+_.numberFormat(item.price,2)+"</td>";
				voucher_html+="<td><button class='btn btn-tiny btn-item-action btn-item-remove' data-id='"+item.voucher_id+"' voucher-type-id='"+item.voucher_type_id+"'><i class='icon-remove'></i></button></td></tr>";
			});
		}
		
		$("#cart-voucher-details").html(voucher_html);
		
		//on click remove voucher item
		$("#cart-voucher-details .btn-item-remove").click(function(){
			var voucher_id = $(this).attr('data-id');
			var voucher_type_id = $(this).attr('voucher-type-id');
			
			vitalc.checkout.vouchers[voucher_id] = {};
			vitalc.checkout.vouchers[voucher_id] = _.clone(vitalc.checkout.selected_vouchers[voucher_id]);
			if(voucher_type_id == 2)
			{
				vitalc.checkout.vouchers[voucher_id].products = {}
			}
			delete vitalc.checkout.selected_vouchers[voucher_id];
			
			vitalc.checkout.displayProductVouchers();
			
		});
		
		var releasing_facility_id = $('#facility_id').val();
		if (releasing_facility_id != 0)
			vitalc.checkout.check_quantities(releasing_facility_id);
		
		
		vitalc.checkout.browseVoucherModal.hide();
	};
	
	
	/***************************************************************************/
	
	/***************************************************************************/
	// Member > Repeat Sales
	vitalc.encodeSales = {};
	vitalc.encodeSales.initialize = function(){
		b.request({
            url : '/members/encoding/get_account_points',
            data : {
                '_account_id' : vitalc.member.selected_account_id
            },
            on_success : function(data) {
                if (data.status == "1")	{
                    $("#account_details_container").html();
                    $("#account_details_container").html(data.html);

                } else {
                    var errorModal = b.modal.create({
                        title: 'Get Account Points :: Error',
                        disableClose: true,
                        html: data.html,
                        width: 400,
                        buttons: {
                            'Close' : function() {
                                errorModal.hide();
                            }
                        }
                    });
                    errorModal.show();
                }
            } // end on_success
        });
		
		$(".account_selector").on("click",function(e){
			
			e.preventDefault();
			
			var _account_id = $(this).attr('data-id');

	        b.request({
	            url: '/members/select_account',
	            data: {'account_id' : _account_id},
	            on_success: function(data, status) {
	                // do a page refresh
	                $("#selected_account").html(_account_id);
	                location.reload();
	            }
	        });
	        return false;
	    });
		
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
		
	};
	
	vitalc.encodeSales.checkIfRSCard = function(card_id, card_code, callback) {	
		var m_result = {is_valid_rs: 0, message: '',error_in: ''};
		b.request({
			url : '/members/encoding/check_rs',
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
	
	
	vitalc.encodeSales.checkLimit73 = function(card_id, _account_id, _maintenance_period, callback) {	
		var m_result = {has_limit: 0, msg: ''};		
		b.request({
			url : '/members/encoding/check_limit_73',
			data : {					
				'_card_id' : card_id,
				'_maintenance_period' : _maintenance_period,
				'_account_id' : _account_id
			},	
			
			on_success : function(data) {
				if (data.status == 1){
					m_result.has_limit = 1;
		            m_result.msg = data.msg;
				} else {
					m_result.has_limit = 0;
		            m_result.msg = data.msg;					
				}
				if(_.isFunction(callback)) callback.call(this,m_result);
			} // end on_success			
		});			
	};
	
	vitalc.encodeSales.encode = function(){
		var _position = $('#position');
		var _card_code = $('#card_code');
		var _card_id = $('#card_id');
		var _maintenance_period = $('#maintenance_period');
		var hasError = false;
		var hasLimit = 0;

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

		//// check if RS card is series 73
		//var card_series = _card_id.val().substring(0, 2);
		
		//if (card_series == "73") {
		
		vitalc.encodeSales.checkLimit73($.trim(_card_id.val()), $.trim(vitalc.member.selected_account_id),$.trim(_maintenance_period.val()),function(var_rslimit) {	
			
			//alert(var_rslimit.has_limit);
			
			if (var_rslimit.has_limit == 1) {										
				if (_maintenance_period.val() == 'monthly') {
					$("#series_73_info_monthly").attr("style","text-align:right;background-color: #FFCCCB");
				} else {							
					$("#series_73_info_annual").attr("style","text-align:right;background-color: #FFCCCB");
				}	
				
				var errorMaintenanceModal = b.modal.create({
					title: 'Card Code Verification :: Alert',
					disableClose: true,
					html: var_rslimit.msg,
					buttons: {							
						'Ok' : function() {
							errorMaintenanceModal.hide();
						}
					}
				});
				errorMaintenanceModal.show();					
				
			} else {
			
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

					if (vitalc.member.selected_account_id == '') {
						hasError = true;
						vitalc.encodeSales.setFormControlErrorMsg(vitalc.member.selected_account_id,"Account ID is required",1);
					}

					if (hasError == false) {		
							// confirmation
						beyond.request({
							url : '/members/encoding/confirm_credit',
							data : {
								'_position' : _position.val(),
								'_card_code' : _card_code.val(),
								'_card_id' : _card_id.val(),
								'_maintenance_period' : _maintenance_period.val(),
								'_account_id' : vitalc.member.selected_account_id
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
												vitalc.encodeSales.creditCardPoints(vitalc.member.member_id, vitalc.member.selected_account_id, _card_id.val(), _position.val(), _card_code.val(), _maintenance_period.val());
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
			
			}
		});
		
		
	};
	
	vitalc.encodeSales.setFormControlErrorMsg = function(elem,msg,is_child) {
		if (is_child == 1) {
			elem.parent().parent().addClass("error");
			elem.parent().parent().find(".help-inline").remove();			
			elem.parent().append("<span class='help-inline'>" + msg + "</span>");			
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
			url : '/members/encoding/credit_points',
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
						title: 'Card Encode :: Successful',
						disableClose: true,
						html: data.html,
						buttons: {						
							'Close' : function() {
								creditPointsModal.hide();
								redirect("members/encoding");
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
	
	vitalc.showNumpad = function(options, cb, on_show, on_hide) {
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
			if (_val.value() < options.min) {
				_with_error = true;
				_error_msg = 'Value is lower then min. set value of ' + options.min + '.';
			}
			
			if (options.max >= options.min && _val.value() > options.max && !_with_error) {
				_with_error = true;
				_error_msg = 'Value is bigger then max. set value of ' + options.max + '.';
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
					'set_amount' : vitalc.numpad_input_box.types.amount.value(),
					'set_value' : vitalc.numpad_input_box.types.value.value()
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

		vitalc.numpad_input_box = {
			'hook' : '#numpad-value',
			'currentType' : 'value',
			'types' : {
				'value' : {
					'digits' : (previousValues!=null)?previousValues.value.digits:'0',
					'decimal' : (previousValues!=null)?previousValues.value.decimal:-1,
					'decimalOn' : (previousValues!=null)?previousValues.value.decimalOn:false,
					'value' : function() {
						return parseFloat(parseInt(vitalc.numpad_input_box.types.value.digits) + (parseInt(vitalc.numpad_input_box.types.value.decimal) > 0 ? '.'+vitalc.numpad_input_box.types.value.decimal : ''));
					}
				},
				'amount' : {
					'digits' : (previousValues!=null)?previousValues.amount.digits:'0',
					'decimal' : (previousValues!=null)?previousValues.amount.decimal:-1,
					'decimalOn' : (previousValues!=null)?previousValues.amount.decimalOn:false,
					'value' : function() {
						return parseFloat(parseInt(vitalc.numpad_input_box.types.amount.digits) + (parseInt(vitalc.numpad_input_box.types.amount.decimal) > 0 ? '.'+vitalc.numpad_input_box.types.amount.decimal : ''));
					}
				}
			}
		}

		if(options.discount_type == 'percent') {
			$('.numpad-value').click(function(){
				$(this).css('background-color', '#5fbe60').animate({
					backgroundColor: "transparent"
				}, 1000 );
				vitalc.numpad_input_box.hook = '#numpad-'+$(this).attr('type');
				vitalc.numpad_input_box.currentType = $(this).attr('type');
				_val.digits = vitalc.numpad_input_box.types[$(this).attr('type')].digits;
				_val.decimal = vitalc.numpad_input_box.types[$(this).attr('type')].decimal;
				_decimal_on = vitalc.numpad_input_box.types[$(this).attr('type')].decimalOn;
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
			$('#numpad-value').text(numberFormat(vitalc.numpad_input_box.types.value.value(), parseInt(vitalc.numpad_input_box.types.value.decimal) < 0 ? 0 : String(vitalc.numpad_input_box.types.value.decimal).length));
			$('#numpad-amount').text(numberFormat(vitalc.numpad_input_box.types.amount.value(), parseInt(vitalc.numpad_input_box.types.amount.decimal) < 0 ? 0 : String(vitalc.numpad_input_box.types.amount.decimal).length));
		} else {
			$('#numpad-value').text(numberFormat(_val.value(), parseInt(_val.decimal) < 0 ? 0 : String(_val.decimal).length));
		}
		
		$(document).unbind('keydown');
		$(document).bind('keydown', function (e) {
		    if (e.keyCode === 8) {
		        e.preventDefault();
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
			var val = $(this).data('value');
			
			if (val == 'clear') {
				_val.digits = '0';
				_val.decimal = '-1';
				_decimal_on = false;
			} else if (val == 'back') {
				if (_val.value() > 0) {
					if (_decimal_on) {
						if (_val.decimal.length > 0) {
							_val.decimal = _val.decimal.substring(0,_val.decimal.length-1);
						} 
						if (_val.decimal.length == 0) {
							_val.decimal = '-1';
							_decimal_on = false;
						}
					} else {
						if (_val.digits.length > 0) {
							_val.digits = _val.digits.substring(0,_val.digits.length-1);
						} 
						if (_val.digits.length == 0) _val.digits = '0';
					}
				}
				
			} else if (val == 'decimal') {
				_decimal_on = true;
			} else {
				if (_val.value() < 999999999.9999) {
					if (_decimal_on) {
						if (String(_val.decimal).length < 4) {
							if (_val.decimal == '-1') {
								_val.decimal = String(val);
							} else {
								_val.decimal = String(_val.decimal) + val;
							}
						}
					} else {
						if (_val.digits == '0'  || _init_value) {
							_val.digits = String(val);
						} else {
							_val.digits = String(_val.digits) + val;
						}
					}
				}
					
			}

			vitalc.numpad_input_box.types[vitalc.numpad_input_box.currentType].digits = _val.digits;
			vitalc.numpad_input_box.types[vitalc.numpad_input_box.currentType].decimal = _val.decimal;
			vitalc.numpad_input_box.types[vitalc.numpad_input_box.currentType].decimalOn = _decimal_on;

			if (_init_value) _init_value = false;
			$(vitalc.numpad_input_box.hook).text(numberFormat(_val.value(), parseInt(_val.decimal) < 0 ? 0 : String(_val.decimal).length));
			
		});
		
		
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
});


function ReplaceNumberWithCommas(yourNumber) {
    //Seperates the components of the number
    var n= yourNumber.toString().split(".");
    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //Combines the two sections
    return n.join(".");
}
