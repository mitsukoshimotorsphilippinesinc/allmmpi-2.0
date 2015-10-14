// ordering system js

var getDetails = function(type,id)
{
	if (type=='packages')
	{
		$.each(packages,function(k,v){
			if (v.package_id == id) viewPackageDetails(v.package_id,1,1);
		});
	}
	else
	{
		$.each(products,function(k,v){
			if (v.product_id == id) viewProductDetails(v.product_id);
		});
	}
}


var viewPackageDetails = function(package_id,set_quantity,pass)
{

	var _package = getPackage(package_id);
	
	var _srp = numberFormat(parseFloat(_package.standard_retail_price),2);
	var _mp = numberFormat(parseFloat(_package.member_price),2);
	
	var products = 'This package includes:<br/>\n\
					<table class="table table-striped table-bordered table-condensed modal_package_products">\n\
						<thead>\n\
							<th width="300px;">Item Name</th>\n\
							<th width="50px;">Qty</th>\n\
							<th width="36px;">&nbsp;</th>\n\
						</thead>\n\
						<tbody id="cart-list">';
	var swappable = "Please choose from any of the following variants:<br/>";
	var swappable_ctr = 0;
	
	temp_swap_list = [];
	pack_swap_list = [];
	
	variant_items_list = {};
	
	$.each(_package.products,function(k,v){

		var quantity_text = 1 * set_quantity;

		if (v.quantity>1) quantity_text = v.quantity * set_quantity;
		
		if (v.is_swappable==1)
		{
			if(_.isUndefined(temp_swap_list[v.group]))
			{
				temp_swap_list[v.group] = {"group_qty": v.group_qty * set_quantity,"available": v.group_qty * set_quantity,"product_id": [],"item_name": []};
			}

			temp_swap_list[v.group].product_id.push(v.product_id);
			temp_swap_list[v.group].item_name.push(v.item_name);

			swappable_ctr++;
		}
		else 
		{
			products = products + "<tr><td>"+v.item_name +"</td><td>"+ quantity_text + "</td><td>&nbsp;</td></tr>";
		}
	});

	products = products + '</tbody></table>';
	
	var swappable_item = "swappable_item";
	var disabled = "";
	
	//define modal buttons first before the modal so that we can change the property name dynamically
	var viewDetailsModalButtons = {
		'Close' : function() {
			viewDetailsModal.hide();
		},
		'Add To Cart' : function() {
			if (swappable_ctr==0) pack_swap_list = [];

			if(pass == 1 && swappable_ctr>0) swappable_ctr = 0;

			var not_allocated = 0;

			$.each(temp_swap_list,function(k,v){
				if(k == 0) return;
				not_allocated = not_allocated + v.available;
			});

			if (swappable_ctr>0 && not_allocated > 0) 
			{
				alert("Please select from the list of items.");
			}
			else
			{
				viewDetailsModal.hide();

				var _package = getPackage(package_id);
				if(pass == 1)
				{
					poiQuantity({"type":"pack","id":package_id,"name":_package.package_name},1,function(result){
						if(_.isEmpty(result)) return false;
						if(result.quantity == 0) return false;

						if(pass == 1 && !_.isEmpty(temp_swap_list))
						{
							viewPackageDetails(package_id,result.quantity,2);
							return false;
						}	

						var item = {"package_id":result.package_id,"quantity":result.quantity,"swappable_list":pack_swap_list}
						addToCart(item);

					});
				}
				else if(pass == 2)
				{
					var item = {"package_id":package_id,"quantity":set_quantity,"swappable_list":pack_swap_list}
					addToCart(item);
				}

			}
		}
	};
	
	//this packages has swappable items therefore change flow of process.
	//make the swappable items disabled and let the user set the qty first before letting the user choose the swappable items
	if(pass == 1 && !_.isEmpty(temp_swap_list))
	{
		swappable_item = "_swappable_item";
		disabled = "disabled";
		viewDetailsModalButtons["Set Quantity"] = viewDetailsModalButtons["Add To Cart"];
		delete viewDetailsModalButtons["Add To Cart"];
	}
	
	
	if (swappable_ctr==0) swappable = '';		
	else
	{
		swappable = swappable + '<div id="variants" style="position: relative; overflow: hidden; overflow-y: scroll; width: 260px; height: 200px; border: solid 1px #ccc;">';
		swappable = swappable + '<div id="variant-groups" style="position: absolute; top: 0; left: 0; width: 258px; min-height: 198px;">';
		$.each(temp_swap_list,function(key,val){
			if(key == 0) return;

			swappable = swappable + "<a class='btn btn-success btnVariant' data-group-id='"+key+"' style='width: 221px;'>Variant Group "+key+": (Available: <span class='swappable group_"+key+"_qty'>"+val.available+"</span>)</a><br/>";
			var variant_items = "Variant Group "+key+": (Available: <span class='swappable group_"+key+"_qty'>"+val.available+"</span>)<br>";
			$.each(val.product_id,function(k,product_id){
				variant_items = variant_items + "<a class='group_"+key+" "+swappable_item+" add prod-swappable-link "+disabled+"' href='#' data='" + product_id + "'>" + val.item_name[k] + "</a><br/>";
			});
			variant_items_list[key] = variant_items;
		});
		swappable = swappable + '</div>';
		
		//window for the items
		swappable = swappable + "\
		<div id='variant-items' style='position: absolute; top: 0; left: 260px; width: 258px; min-height: 198px; margin-left:4px;'>\n\
			<button id='btnBack' class='btn'>Back</button>\n\
			<div id='items_container'>\n\
			</div>\n\
		</div>"
		swappable = swappable + '</div>';
		
	}
	
	var html = "<div style='float:left;'><img src='" + img_url + "' style='border:solid 1px #CCC; width:100px; height: 100px;'></div><div style='float:left;margin-left:15px;width:260px;'>" + _package.package_description + "<br/>Standard Retail Price: <strong>" + _srp + "</strong><br/><br/><span class='package-item'>" + swappable + "</span></div><div style='float:left;margin-left:15px;width:350px;'><span class='package-item'>" + products + "</span></div><div style='clear:both;'></div>";

	viewDetailsModal = b.modal.new({
		title: _package.package_name,
		width: 780,
		disableClose: true,
		html: html,
		buttons: viewDetailsModalButtons
	});

	
	viewDetailsModal.show();
}

var viewProductDetails = function(product_id)
{
	
	var _product = getProduct(product_id);
	var _srp = numberFormat(parseFloat(_product.standard_retail_price),2);
	var _mp = numberFormat(parseFloat(_product.member_price),2);
	
	var html = "<div style='float:left;'><img src='" + img_url + "' width='200' style='border:solid 1px #CCC;'></div><div style='float:left;margin-left:15px;width:350px;'>" + _product.item_description + "<br/>Standard Retail Price: " + _srp + "<br/>Member Price: " + _mp + "<br/></div><div style='clear:both;'></div>";

	var viewDetailsModal = b.modal.new({
		title: _product.item_name,
		width: 650,
		disableClose: true,
		html: html,
		buttons: {
			'Close' : function() {
				viewDetailsModal.hide();
			},
			'Add To Cart' : function() {
				viewDetailsModal.hide();

				var _product = getProduct(product_id);

				poiQuantity({"type":"product","id":product_id,"name":_product.item_name},1,function(result){
					if(_.isEmpty(result)) return false;
					if(result.quantity == 0) return false;
					var item = result;
					addToCart(item);
				});
				
			}
		}
	});

	
	viewDetailsModal.show();
}	

var addToCart = function(item)
{
	
	if (typeof(item.package_id) == "undefined") item.package_id = 0;
	if (typeof(item.swappable_list) == "undefined") item.swappable_list = [];
	if (typeof(item.product_id) == "undefined") item.product_id = 0;
	if (typeof(item.quantity) == "undefined") item.quantity = 1;
	
	// if package
	if (item.package_id>0)
	{
		var _found = 0;
		$.each(cart,function(k,v){
			if (v.type=='package' && v.package_id==item.package_id)
			{
				v.quantity = (v.quantity * 1) + (item.quantity * 1);
				_found = 1;

				//add selected swappable items to swappable list of package
				if(!_.isEmpty(item.swappable_list))
				{
					var swappables = v.swappables;

					$.each(item.swappable_list,function(key,group){
						if(key == 0) return;

						$.each(group,function(id,qty){
							if(_.isUndefined(id)) return;
							if (parseInt(id)>0 && parseInt(qty)>0)
							{
								//check if variant id in group exists
								if(_.isUndefined(swappables[key][id]))
								{
									swappables[key][id] = {"swappable_id":id,"quantity":qty};
								}
								else
								{
									swappables[key][id].quantity = swappables[key][id].quantity + qty;
								}

							}
						});
					});
					v.swappables = swappables;
				}
				//renderCartItems();
				return false;
			}
		});
		
		if (_found==0)
		{
			var swappables = [];

			$.each(item.swappable_list,function(key,group){
				if(key == 0) return;
				$.each(group,function(k,v){
					if(_.isUndefined(v)) return;
					if (parseInt(k)>0 && parseInt(v)>0)
					{
						if(_.isUndefined(swappables[key])) swappables[key] = [];
						swappables[key][k] = {"swappable_id":k,"quantity":v};
					}
				});
			});

			entry_count++;

			var _item = {"type":"package","package_id":item.package_id,"swappables":swappables,"quantity":item.quantity};
			cart[entry_count] = _item;
			cart_count = cart_count + 1;
		}

	}

	// if product
	if (item.product_id>0) 
	{
		var _found = 0;
		$.each(cart,function(k,v){
			if (v.type=='product' && v.product_id==item.product_id) 
			{
				v.quantity = (v.quantity * 1) + (item.quantity * 1);
				_found = 1;
				return false;
			}
		});
		
		if (_found==0) 
		{
			entry_count++;
			var _item = {"type":"product","product_id":item.product_id,"quantity":item.quantity};
			cart[entry_count] = _item;
		}			

	}
	
	return cartCount();
}

var clearCart = function()
{
	cart = {};
	var html = "<tr id='total-amount'><td colspan='3'><strong>TOTAL AMOUNT</strong></td><td></td><td class='number' style='text-align:right'><strong>0.00</strong></td><td>&nbsp;</td></tr>";
	$("#cart-list").html(html);
	return cartCount();
}

var getPackage = function(package_id)
{
	var _package;
	
	$.each(packages,function(k,v){
		if (v.package_id == package_id) 
		{
			_package = v;				
			return false;
		}
	});

	return _package;
}

var getProduct = function(product_id)
{
	var _product

	$.each(products,function(k,v){
		if (v.product_id == product_id) 
		{
			_product = v;
			return false;
		}
	});

	return _product;
}

var cartCount = function()
{
	var counter = 0;
	
	$.each(cart,function(k,v){ 
		counter = counter + parseInt(v.quantity); 
	});
	
	$("#shopping-cart-count").html(counter);
	
	return counter;
}

var updateCart = function()
{
	var _remove_items = [];
	
	$.each(cart,function(k,v){
		if (v.type=='product')
			var _new_quantity = $("#quantity-product-" + v.product_id+"[entry='"+k+"']").val();
		else if (v.type=='package')
			var _new_quantity = $("#quantity-package-" + v.package_id+"[entry='"+k+"']").val();

		if (_new_quantity>0) 
			v.quantity = _new_quantity
		else
			_remove_items.push(k);
	});
	
	$.each(_remove_items,function(k,v) {
		console.log(v);
		delete cart[v]; 
	});

	cartCount();

	renderCartItems();		
}

var renderCartItems = function() 
{
	$("#step-one").hide();		
	$("#step-two").show();

	$("#member-name").html(member_name);
	$("#account-number").html(account_id);
	
	$("#cart-list").html("");
				
	$.each(cart,function(k,v){
		
		var _quantity = parseInt(v.quantity);
		
		if (v.type=="product")
		{
			var _product = getProduct(v.product_id); 				
			var title = _product.item_name;
			var quantity = numberFormat(_quantity);
			
			if (order_type=='member')
				var amount = parseFloat(_product.member_price) * _quantity;
			else if (order_type=='employee')
				var amount = parseFloat(_product.employee_price) * _quantity;
			else
				var amount = parseFloat(_product.standard_retail_price) * _quantity;
			
			
			var pretty_amount = numberFormat(amount,2);

			var html = "<tr class='row-product' data='" + v.product_id + "'><td width='10px;'></td><td colspan='2'>" + title + "</td><td><input id='quantity-product-" + v.product_id + "' entry='"+k+"' class='item-quantity numeric-entry' type='text' value='" + _quantity + "'></td><td class='number'>" + pretty_amount + "<input class='amount' type='hidden' value='" + amount + "'></td><td><a class='btn btn-danger rmv-entry' entry='"+k+"'><i class='icon-white icon-remove'></i></a></td></tr>";

		}
		else if (v.type=="package")
		{
			var _package = getPackage(v.package_id); 				
			var title = _package.package_name;
			var quantity = numberFormat(_quantity);
			var amount = parseFloat(_package.member_price) * _quantity;
			var pretty_amount = numberFormat(amount,2);
			
			var no_swap = true;
			
			//no swappable items, can edit the package qty anytime
			var pack_html = "<tr class='row-package' data='" + v.package_id + "' entry='"+k+"'><td class='package' width='10px;'><span class='icon-minus'></span></td><td colspan='2'>" + title + " (PACKAGE)</td><td class='number'><input id='quantity-package-" + v.package_id + "' entry='"+k+"' class='item-quantity' type='text' value='" + _quantity + "'></td><td class='number'>" + pretty_amount + "<input class='amount' type='hidden' value='" + amount + "'></td><td><a class='btn btn-danger rmv-entry' entry='"+k+"'><i class='icon-white icon-remove'></i></a></td></tr>";
			
			var det_html = ""
			$.each(_package.products,function(i,j){
				if (j.is_swappable==0) 
				{
					var _j_quantity = parseInt(j.quantity);
					var product_name = j.item_name;
					var product_quantity = numberFormat(_j_quantity * _quantity);
					det_html = det_html + "<tr class='row-package-" + v.package_id + "' entry='"+k+"'><td></td><td></td><td>" + product_name + "</td><td class='number'>" + product_quantity + "</td><td></td><td>&nbsp;</td></tr>";
				}
			});

			$.each(v.swappables,function(m,group){
				if(m == 0) return;
				$.each(group,function(id,n){
					if(_.isUndefined(n)) return;
					if (n.swappable_id>0)
					{
						no_swap = false;
						var _n_quantity = parseInt(n.quantity);
						var _swappable = getProduct(n.swappable_id);
						var swappable_name = _swappable.item_name;
						var swappable_quantity = numberFormat(_n_quantity);
						det_html = det_html + "<tr class='row-package-" + v.package_id + "' entry='"+k+"'><td></td><td></td><td>" + swappable_name + " <span class='label label-important'>SELECTED OPTION</span></td><td class='number'>" + swappable_quantity + "</td><td></td><td>&nbsp;</td></tr>";
					}
				});
			});
		}
		if(!no_swap)
		{
			pack_html = "<tr class='row-package' data='" + v.package_id + "' entry='"+k+"'><td class='package' width='10px;'><span class='icon-minus'></span></td><td colspan='2'>" + title + " (PACKAGE)</td><td class='number'>" + _quantity + "<input id='quantity-package-" + v.package_id + "' entry='"+k+"' class='item-quantity' type='hidden' value='" + _quantity + "' readonly='readonly'></td><td class='number'>" + pretty_amount + "<input class='amount' type='hidden' value='" + amount + "'></td><td><a class='btn btn-danger rmv-entry' entry='"+k+"'><i class='icon-white icon-remove'></i></a></td></tr>";
		}
		$("#cart-list").append(pack_html+""+det_html);		
	});
			
	addExtraRows();
	
	getTotalAmount();		
}


var addExtraRows = function()
{
	var _html = "<tr><td width='10px'>&nbsp;</td><td width='10px'></td><td></td><td></td><td></td><td>&nbsp;</td></tr><tr><td width='10px'>&nbsp;</td><td width='10px'></td><td></td><td></td><td></td><td>&nbsp;</td></tr>";
	$("#cart-list").append(_html);		
}

var getTotalAmount = function()
{
	var total_amount = 0;
	
	$(".amount").each(function(){
		total_amount = total_amount + parseInt($(this).val());
	});
	
	pretty_total_amount = numberFormat(total_amount,2);

	var html = "<tr id='cart-row-total'><td colspan='3'><strong>TOTAL AMOUNT</strong></td><td></td><td class='number'><strong>" + pretty_total_amount + "</strong><input id='total-amount' type='hidden' value='" + total_amount + "' ></td><td>&nbsp;</td></tr>";
	$("#cart-list").append(html);		
}

var addMoreItemsToCart = function()
{
	$("#step-one").show();
	$("#step-two").hide();		
}

var returnToCart = function()
{
	$("#step-one").hide();
	$("#step-two").show();		
	renderCartItems();
}


var enterMemberDetails = function ()
{
	var _total_amount = $("#total-amount").val();
	var _cart = cart;				
	
	var img_src = '/assets/img/ajax-loader.gif';
	
	var _html = "<h3>For Members</h3><form><fieldset><div class='control-group'><label class='control-label' for='account-id'>Enter valid Account ID:</label><div class='controls'><input id='account-id' class='input-medium numeric-entry' type='text' placeholder='65xxxxxxxx' value=''> <img class='loader' src='" + img_src + "'><span class='help-inline'></span></div></div></fieldset></form><hr><h3>For Non-members</h3><form><fieldset><div class='control-group'><label class='control-label' for='last-name'>Enter Name</label><div class='controls'><input id='last-name' class='input-medium' type='text' placeholder='lastname' value=''> <input id='first-name' class='input-medium' type='text' placeholder='firstname' value=''> <input id='middle-initial' class='input-small' style='width:20px;' type='text' placeholder='mi' value=''><span class='help-inline'></span></div></div></fieldset></form>";	

	var enterMemberDetailsModal = b.modal.new({
		title: "Please Enter the Following Details",
		width: 500,
		disableClose: true,
		html: _html,
		buttons: {
			'Cancel' : function() {
				enterMemberDetailsModal.hide();
			},
			'Confirm Details' : function() {

				var _account_id = $("#account-id").val();

				var _last_name = $("#last-name").val();
				var _first_name = $("#first-name").val();
				var _middle_initial = $("#middle-initial").val();

				var _member_name = _last_name + ", " + _first_name + " " + _middle_initial;

				var _member_name = _member_name.toUpperCase();

				if (_account_id.length==0 && (_last_name.length==0 && _first_name.length==0 && _middle_initial.length==0))
				{
					//error
					$("#account-id").parent().parent().addClass("error");
					$("#account-id").parent().parent().find(".help-inline").html("Invalid");	

					$("#last-name").parent().parent().addClass("error");
					$("#middle-initial").parent().parent().find(".help-inline").html("Enter");

				}
				else if (_account_id.length>0) // if member
				{
					$(".loader").show();

					b.request({
						url: '/ordering/check_account_number',
						data: {'account_id' : _account_id},
						on_success: function(data, status) {
							if (data.status==1) 
							{
								//success
								$("#account-id").parent().parent().addClass("success");
								$("#account-id").parent().parent().find(".help-inline").html("Valid");

								//hide modal
								enterMemberDetailsModal.hide();

								// set account and member_id
								if (data.member.is_account_active==1) 
								{
									account_id = _account_id; // + " <span class='label label-success'>ACTIVE</span>";
								}
								else 
								{
									account_id = _account_id; // + " <span class='label label-error'>INACTIVE</span>";
								}

								member_name = data.member.last_name + ", " + data.member.first_name + " " + data.member.middle_initial;						

								order_type = 'member';

								// render Cart
								renderCartItems();
							}
							else if (data.status==2)
							{
								//error
								$("#account-id").parent().parent().addClass("error");
								$("#account-id").parent().parent().find(".help-inline").html("Invalid");
							}
							$(".loader").hide();
						}
					});		
				}
				else // if non-member
				{
					if (_last_name.length==0 && _first_name.length==0  && _middle_initial.length==0) 
					{
						$("#last-name").parent().parent().addClass("error");
						$("#middle-initial").parent().parent().find(".help-inline").html("Enter");
					}
					else
					{
						enterMemberDetailsModal.hide();

						// set member name and account_id
						account_id = "NONE";
						member_name = _member_name;

						// set order type
						order_type = 'non-member';

						// render Cart
						renderCartItems();							
					}

				}
			}
		}
	});


	enterMemberDetailsModal.show();
}		
	
var confirmCartOrder = function ()
{

	var confirmCartOrderModal = b.modal.new({
		title: "Confirmation",
		width: 500,
		disableClose: true,
		html: "<h3>Are you sure you want to confirm your order?</h3>",
		buttons: {
			'No' : function() {
				confirmCartOrderModal.hide();
			},
			'Yes' : function() {
				b.request({
					url: '/ordering/insert_to_cart',
					data: {
						'member_name' : member_name,
						'account_id' : account_id,
						'cart':cart
					},
					on_success: function(data, status) {
						if (data.status==1)
						{
							// successfully created order
							var viewTransactionCode = b.modal.new({
								title: "Transaction Code",
								width: 400,
								disableClose: true,
								html: "<center><h1>" + data.transaction_code +  "" + data.cart_id + "</h1></center>",
								buttons: {
									'Close' : function() {
										viewTransactionCode.hide();
										clearCart();
										account_id = '';
										member_name = '';

										addMoreItemsToCart();
									},
									'Print' : function() {
										viewTransactionCode.hide();
										clearCart();
										account_id = '';
										member_name = '';

										addMoreItemsToCart();
									}
								}
							});

							viewTransactionCode.show();
						}
					}
				});
				confirmCartOrderModal.hide();
			}
		}
	});

	confirmCartOrderModal.show();
}

var getMemberType = function(callback)
{
	b.request({
		url: "/webpoi/get_member_type",
		data: {
			'member_id': $("#member_id").val()
		},
		on_success: function(data){
			member_type = 0;
			order_type = 'non-member';
			if(data.status == "ok")
			{
				member_type = parseInt(data.data.member_type_id);
				if(member_type >= 1 && member_type <= 3)
				{
					order_type = 'member'
				}
				else
				{
					order_type = 'employee'
				}
			}
			if(_.isFunction(callback)) callback.call(this,member_type);

		},
		on_error: function(data){
			member_type = 0;
			order_type = 'non-member';
			if(_.isFunction(callback)) callback.call(this,0);
		}
	});
}