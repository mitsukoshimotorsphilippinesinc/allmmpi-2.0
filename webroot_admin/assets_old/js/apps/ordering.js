// ordering system js

var getDetails = function(type,id,qty)
{
	if (type=='packages')
	{
		$.each(packages,function(k,v){
			if (v.package_id == id) viewPackageDetails(v.package_id,qty);
		});
	}
	else
	{
		$.each(products,function(k,v){
			if (v.product_id == id) viewProductDetails(v.product_id);
		});
	}
}


var viewPackageDetails = function(package_id,set_quantity)
{

	var _package = getPackage(package_id);
	
	var _srp = numberFormat(parseFloat(_package.standard_retail_price),2);
	
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
				variant_items = variant_items + "<a class='group_"+key+" swappable_item add prod-swappable-link' href='#' data='" + product_id + "'>" + val.item_name[k] + "</a><br/>";
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

	var viewDetailsModal = b.modal.new({
		title: _package.package_name,
		width: 780,
		disableClose: true,
		html: html,
		buttons: {
			'Close' : function() {
				viewDetailsModal.hide();
			},
			'Add To Cart' : function() {
				if (swappable_ctr==0) pack_swap_list = [];

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
					var item = {"package_id":package_id,"quantity":set_quantity,"swappable_list":pack_swap_list};
					addToCart(item);
					renderCartItems();
					if(!_.isEqual(cart.discount,{})) applyDiscount();
					viewDetailsModal.hide();
				}
			}
		}
	});

	if (swappable_ctr==0)
	{
		pack_swap_list = [];
		var item = {"package_id":package_id,"quantity":set_quantity,"swappable_list":pack_swap_list};
		addToCart(item);
		renderCartItems();
		if(!_.isEqual(cart.discount,{})) applyDiscount();
	}
	else
	{
		viewDetailsModal.show();
	}
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

				var item = {"product_id":product_id};					
				addToCart(item);
				
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
	if (typeof(item.isRebate) == "undefined") item.isRebate = false;
	
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
			if (v.type=='product' && v.product_id==item.product_id && (v.isRebate == item.isRebate))
			{
				v.quantity = (v.quantity * 1) + (item.quantity * 1);
				_found = 1;
				return false;
			}
		});
		
		if (_found==0) 
		{
			entry_count++;
			var _item = {"type":"product","product_id":item.product_id,"quantity":item.quantity,"isRebate":item.isRebate};
			cart[entry_count] = _item;
		}			

	}
	
	return;
}

var clearCart = function()
{
	cart = {};
	cart.discount = {};
	cart.creditCharges = {};
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
		if(k=='discount' || k=='creditCharges')
		{
			return;
		}
		
		if (v.type=='product')
		{
			var product = getProduct(v.product_id);
			var _new_quantity = $(".item_" + product.item_id+"[entry='"+k+"']").find(".qty").val();
		}
		else if (v.type=='package')
		{
			var _new_quantity = $(".pack_" + v.package_id+"[entry='"+k+"']").find(".qty").val();
		}
		
		if (_new_quantity>0) 
			v.quantity = _new_quantity
		else
			_remove_items.push(k);
	});

	$.each(_remove_items,function(k,v) {
		delete cart[v]; 
	});
	
	cartCount();

	renderCartItems();
}

var renderCartItems = function() 
{	
	$("#cart-list").html("");
	var discount_html = "";
	var credit_html = "";
	var rebate_html = "";
	var total_product_rebates = 0;
	rebateAmount = 0;
	$.each(cart,function(k,v){
		
		var _quantity = parseInt(v.quantity);
		if (v.type=="product")
		{
			var _product = getProduct(v.product_id); 				
			var title = _product.item_name;
			var quantity = numberFormat(_quantity);
			var amount = 0
			var unit_price = 0;
			
			if (order_type=='member' || order_type=='EPC' || order_type=='stockist')
			{
				amount = parseFloat(_product.member_price) * _quantity;
				unit_price = _product.member_price;
			}
			else if (order_type=='employee')
			{
				amount = parseFloat(_product.employee_price) * _quantity;
				unit_price = _product.employee_price;
			}
			else
			{
				amount = parseFloat(_product.standard_retail_price) * _quantity;
				unit_price = _product.standard_retail_price;
			}
			
			if(!v.isRebate)
			{
				var pretty_amount = numberFormat(amount,2);
				var html = "<tr class='item item_"+_product.item_id+" product' entry='"+k+"' data='"+_product.product_id+"'><td>&nbsp;</td><td colspan='2' class='name'>" + title + "</td><td><input type='text' class='qty' style='width:45px;' value='" + _quantity + "'></td><td style='text-align:right;'><span class='pretty_amount'>" + pretty_amount + "</span><input type='hidden' class='price' value='" + unit_price + "'><input type='hidden' class='amount' value='" + amount + "'></td><td><a class='btn btn-danger rmv-entry' entry='"+k+"'><i class='icon-white icon-remove'></i></a></td></tr>";
			}
			else
			{
				total_product_rebates = total_product_rebates + amount;
				var temp_amount = numberFormat(amount,2);
				amount = 0;
				unit_price = 0;
				var pretty_amount = numberFormat(amount,2);
				
				rebate_html = rebate_html + "<tr class='item item_"+_product.item_id+" product rebate' entry='"+k+"' data='"+_product.product_id+"'><td>&nbsp;</td><td colspan='2' class='name'>" + title + " ("+temp_amount+") <span class='label label-important'>P</span></td><td>" + _quantity + "<input type='hidden' class='qty' style='width:45px;' value='" + _quantity + "' readonly></td><td style='text-align:right;'><span class='pretty_amount'>" + pretty_amount + "</span><input type='hidden' class='price' value='" + unit_price + "'><input type='hidden' class='amount' value='" + amount + "'></td><td><a class='btn btn-danger rmv-entry' entry='"+k+"'><i class='icon-white icon-remove'></i></a></td></tr>";
			}
			
			

		}
		else if (v.type=="package")
		{
			
			var package_product_id = [];
			var package_product_qty = [];
			
			var _package = getPackage(v.package_id);
			var title = _package.package_name;
			var quantity = numberFormat(_quantity);
			
			var no_swap = true;
			
			if (order_type=='member' || order_type=='EPC' || order_type=='stockist')
			{
				var amount = parseFloat(_package.member_price) * _quantity;
				var unit_price = _package.member_price;
			}
			if (order_type=='employee')
			{
				var amount = parseFloat(_package.employee_price) * _quantity;
				var unit_price = _package.employee_price;
			}
			else
			{
				var amount = parseFloat(_package.standard_retail_price) * _quantity;
				var unit_price = _package.standard_retail_price;
			}
			
			var pretty_amount = numberFormat(amount,2);

			//var html = "<tr class='row-package' data='" + v.package_id + "'><td class='package' width='10px;'><span class='icon-minus'></span></td><td colspan='2'>" + title + " (PACKAGE)</td><td class='number'><input id='quantity-package-" + v.package_id + "' class='item-quantity' type='text' value='" + _quantity + "'></td><td class='number'>" + pretty_amount + "<input class='amount' type='hidden' value='" + amount + "'></td></tr>";
			
			var html = "";
			
			$.each(_package.products,function(i,j){					
				if (j.is_swappable==0) 
				{
					var _j_quantity = parseInt(j.quantity);
					var product_name = j.item_name;
					var product_quantity = numberFormat(_j_quantity * _quantity);
					html = html + "<tr class='pack_"+v.package_id+" pack_prod_"+j.item_id+" nonswap' entry='"+k+"'><td>&nbsp;</td><td>&nbsp;</td><td colspan='1' class='name'>" + product_name + "</td><td class='prod_qty'>" + product_quantity + "</td><td colspan='1' style='text-align:right;'>&nbsp;</td><td>&nbsp;</td></tr>";
					package_product_id.push(j.item_id);
					package_product_qty.push(_j_quantity*_quantity);
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
						html = html + "<tr class='pack_"+v.package_id+" pack_prod_"+_swappable.item_id+" swap' entry='"+k+"'><td>&nbsp;</td><td>&nbsp;</td><td colspan='1' class='name'>" + swappable_name + " <span class='label label-important'>S</span></td><td class='prod_qty'>" + swappable_quantity + "</td><td colspan='1' style='text-align:right;'>&nbsp;</td><td>&nbsp;</td></tr>";
						package_product_id.push(_swappable.item_id);
						package_product_qty.push(_n_quantity);
					}
					
				});
			});
			
			package_product_id = package_product_id.join("|");
			package_product_qty = package_product_qty.join("|");
			
			var new_pack_html = "<tr class='item pack_"+v.package_id+" pack' entry='"+k+"' data='"+v.package_id+"'><td class='pack_details' width='10px;'><span class='icon-minus'></td><td colspan='2' class='name'><div>" + title + "</div></td><td><input type='text' class='qty' style='width:45px;' value='" + _quantity + "'></td><td style='text-align:right;'><span class='pretty_amount'>" + pretty_amount + "</span><input type='hidden' class='price' value='" + unit_price + "'><input type='hidden' class='amount' value='" + amount + "'><input type='hidden' class='pack_"+v.package_id+" pack_prod' value='"+package_product_id+"' ><input type='hidden' class='pack_"+v.package_id+" pack_prod_qty' value='"+package_product_qty+"' ></td><td><a class='btn btn-danger rmv-entry' entry='"+k+"'><i class='icon-white icon-remove' ></i></a></td></tr>";
			
			if(!no_swap)
			{
				new_pack_html = "<tr class='item pack_"+v.package_id+" pack' entry='"+k+"' data='"+v.package_id+"'><td class='pack_details' width='10px;'><span class='icon-minus'></td><td colspan='2' class='name'><div>" + title + "</div></td><td>" + _quantity + "<input type='hidden' class='qty' style='width:45px;' value='" + _quantity + "' readonly='readonly'></td><td style='text-align:right;'><span class='pretty_amount'>" + pretty_amount + "</span><input type='hidden' class='price' value='" + unit_price + "'><input type='hidden' class='amount' value='" + amount + "'><input type='hidden' class='pack_"+v.package_id+" pack_prod' value='"+package_product_id+"' ><input type='hidden' class='pack_"+v.package_id+" pack_prod_qty' value='"+package_product_qty+"' ></td><td><a class='btn btn-danger rmv-entry' entry='"+k+"'><i class='icon-white icon-remove' ></i></a></td></tr>";
			}
			
			html = new_pack_html+""+ html;
		}
		else if (v.type=="discount")
		{
			var check_result = "true";
			var check1 = "";
			var check2 = "";
			
			if(!_.isNull(v.condition1) && !_.isEmpty(v.condition1))
			{
				check1 = v.total_amount+""+v.condition1+""+v.amount1;
			}
			
			if(!_.isNull(v.condition2) && !_.isEmpty(v.condition2))
			{
				check2 = " && "+v.total_amount+""+v.condition2+""+v.amount2;
			}

			if(!_.isEmpty(check1))
			{
				check_result = check1+check2;
			}
			
			
			if(eval(check_result) && (!_.isNull(v.discount) && !_.isEmpty(""+v.discount)))
			{
				var discount_amount =  (-1) * v.discount * v.total_amount;
				var pretty_discount_amount = numberFormat((-1) * discount_amount,2);
				var pretty_discount = v.discount * 100;
				discount_html = "<tr id='discount-row'><td>&nbsp;</td><td colspan='2' class='name'>Additional " + v.discount_name + " ("+ pretty_discount +"%)</td><td>&nbsp</td><td style='text-align:right;'><span class='pretty_amount' style='color: #F00;'>(" + pretty_discount_amount + ")</span><input id='discount_amount' type='hidden' class='amount extra_amount' value='" + discount_amount + "'></td><td><a class='btn btn-danger rmv-discount'><i class='icon-white icon-remove' ></i></a></td></tr>";
			}
		}
		else if (v.type=="rebate")
		{
			var check_result = "true";
			var check1 = "";
			var check2 = "";
			
			if(!_.isNull(v.condition1) && !_.isEmpty(v.condition1))
			{
				check1 = v.total_amount+""+v.condition1+""+v.amount1;
			}
			
			if(!_.isNull(v.condition2) && !_.isEmpty(v.condition2))
			{
				check2 = " && "+v.total_amount+""+v.condition2+""+v.amount2;
			}

			if(!_.isEmpty(check1))
			{
				check_result = check1+check2;
			}
			
			
			if(eval(check_result) && (!_.isNull(v.discount) && !_.isEmpty(""+v.discount)))
			{
				var discount_amount =  v.discount * v.total_amount;
				var pretty_discount_amount = numberFormat(discount_amount,2);
				var pretty_discount = v.discount * 100;
				rebateAmount = discount_amount;
				discount_html = "<tr id='discount-row'><td>&nbsp;</td><td colspan='2' class='name'>" + v.discount_name + " ("+ pretty_discount +"%)<br>Total: " + pretty_discount_amount + "</td><td>&nbsp</td><td style='text-align:right;'><span class='pretty_amount'>&nbsp;</span><input id='rebate_amount' type='hidden' class='rebate_amount' value='" + discount_amount + "'></td><td><a class='btn btn-danger rmv-discount'><i class='icon-white icon-remove' ></i></a></td></tr>";
				/*calculate products bought with product rebates*/
			}
		}
		else if(v.type == "creditCharges")
		{
			var credit_amount =  (1) * v.adjustment * v.total_amount;
			var pretty_credit_amount = numberFormat((1) * credit_amount,2);
			var pretty_credit = v.adjustment * 100;
			
			
			credit_html = "<tr id='credit-row'><td>&nbsp;</td><td colspan='2' class='name'>Additional Credit Card Charge ("+ pretty_credit +"%)</td><td>&nbsp</td><td style='text-align:right;'><span class='pretty_amount'>" + pretty_credit_amount + "</span><input id='credit_charge_amount' type='hidden' class='amount extra_amount' value='" + credit_amount + "'></td><td><a class='btn btn-danger rmv-credit-charge'><i class='icon-white icon-remove' ></i></a></td></tr>";
		}
		
		$("#cart-list").append(html);
	});
	
	rebateAmount = Math.max(0,rebateAmount - total_product_rebates);
	$("#cart-list").append(discount_html);
	$("#cart-list").append(rebate_html);
	$("#cart-list").append(credit_html);
	
	addExtraRows();
	
	getVAT();
	
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
		total_amount = total_amount + parseFloat($(this).val());
	});
	
	pretty_total_amount = numberFormat(total_amount,2);

	var html = "<tr id='total-amount'><td colspan='3'><strong>TOTAL AMOUNT</strong></td><td></td><td class='number' style='text-align:right'><strong>" + pretty_total_amount + "</strong><input id='total-amount' type='hidden' value='" + total_amount + "' ></td><td>&nbsp;</td></tr>";
	$("#cart-list").append(html);		
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
								html: "<center><h1>" + data.transaction_code +  " " + data.cart_id + "</h1></center>",
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
				if(member_type == 1)
				{
					order_type = 'member'
				}
				else if(member_type == 2)
				{
					order_type = 'EPC'
				}
				else if(member_type == 3)
				{
					order_type = 'stockist'
				}
				else if(member_type == 4)
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

var discountRadioButtons = function(apply_discount){
	if($(apply_discount).attr("checked") == "checked")
	{
		var non_checked = true;
		$(".discount_types").each(function(){
			$(this).removeAttr("disabled");
			
			if($(this).attr("checked") == "checked") non_checked = false;
		});
		
		if(non_checked)
		{
			$(".discount_types").first().attr("checked","checked");
		}
		
	}
	else
	{
		$(".discount_types").attr("disabled","disabled");
		$(".discount_types").each(function(){ $(this).removeAttr("checked");});
	}
}

var applyDiscount = function()
{
	var discount = discount_types[member_type];
	var total_amount = 0;
	var appliedDiscount = discount.discount;
	

	$(".amount").not(".extra_amount").each(function(){
		total_amount = total_amount + parseFloat($(this).val());
	});

	if($("#payment_method").val() == "Credit Card") appliedDiscount = (Math.floor(appliedDiscount * 10000) - Math.floor(creditCharges * 10000)) / 10000;
	
	
	cart.discount = {
		type: discount.discount_type,
		discount_id: discount.discount_id,
		discount_name: discount.discount_name,
		discount: appliedDiscount,
		condition1: discount.condition1,
		amount1: discount.amount1,
		condition2: discount.condition2,
		amount2: discount.amount2,
		total_amount: total_amount
	};
	
	renderCartItems();
}

var removeDiscount = function()
{
	$("#discount-row").remove();

	cart.discount = {};
	
	renderCartItems();
}

var applyCreditCharges = function()
{
	var total_amount = 0;

	$(".amount").not("#credit_charge_amount").each(function(){
		total_amount = total_amount + parseFloat($(this).val());
	});

	cart.creditCharges = {
		type: "creditCharges",
		total_amount: total_amount,
		adjustment: creditCharges
	};

	renderCartItems();
}

var removeCreditCharges = function()
{
	$("#credit-row").remove();

	cart.creditCharges = {};
	renderCartItems();
}

var getVAT = function(){
	
	if(!_.isEqual(cart.discount,{}) && cart.discount.type == "discount")
	{
		var vat_html = "";
		var vattable = 0;
		var total = 0;
		$.each(cart,function(k,v){
			
			var _quantity = parseInt(v.quantity);
			
			if (v.type=="package")
			{
				var _package = getPackage(v.package_id);
				if(_package.package_type_id == 1)
				{
					if (order_type=='member' || order_type=='EPC' || order_type=='stockist')
					{
						var amount = parseFloat(_package.member_price) * _quantity;
					}
					if (order_type=='employee')
					{
						var amount = parseFloat(_package.employee_price) * _quantity;
					}
					else
					{
						var amount = parseFloat(_package.standard_retail_price) * _quantity;
					}

					total = total+amount;
				}
			}
		});
		
		vattable = numberFormat(total / 1.12,2);
		vat_html = "<tr><td width='10px'>&nbsp;</td><td colspan='2'>Vatable</td><td></td><td style='text-align:right;'>("+ vattable +")</td><td>&nbsp;</td></tr>";
		if(parseFloat(vattable) > 0)
		{
			$("#cart-list").append(vat_html);
		}
	}
	
}