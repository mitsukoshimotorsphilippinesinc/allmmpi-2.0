<div class='alert alert-danger'><h2>Add New Item</h2></div>
<span id= "order_code" name = "order_code" style = "color:red; display:none;"><?=$order_code?></span>
<span>Search Item</span>
<input id = "search_part" name = "search_part" placeholder="Enter Search"  TYPE = "TEXT">
<span>Discount</span>
<Select style = "width:60px;" id = "discount_option" name = "discount_option">
	<?php
		for($i=0;$i<=30;$i++){
			echo "
			<option value = '{$i}'>$i</option>";
		}
	?>
</Select>
<span>QTY</span>
<input style = "width:60px; text-align:right;" id = "quantity" name = "quantity" placeholder="0"  TYPE = "TEXT">
<button id="search_item" name="search_item" class='btn' style="margin-top:-10px;float:right;"><span>Search</span></button>
<span id = "Error_Message_Part" style = "color:red; display:none;">No Keywords or Phrase to search...</span>
<span id = "Error_Message_QTY" style = "color:red; display:none;">Invalid Quantity Value...</span>
<table id = "parts_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>SKU</th>
			<th>Description</th>
			<th>Model</th>
			<th>Part No.</th>
			<th>Unit</th>
			<th>QTY</th>
			<th>SRP</th>
			<th>Res</th>
			<th>Location</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody id = "record_data">
	</tbody>
<table>
	<div id = "record_result">
	</div>

<SCRIPT TYPE = "text/javascript">

	
	$("#quantity").keypress(function (e) {
	if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
	return false;
	}
	});

	$('#quantity').keypress(function(){
		if (_.isNumber($('#quantity').val() * 1)){
			$('#Error_Message_QTY').hide();
			return;
		}
		$('#Error_Message_QTY').show();
	});


	$('#search_part').keypress(function(){		
		$('#Error_Message_Part').hide();
	});

	$('#search_item').click(function(){

		if ($('#search_part').val() == ""){
			$('#Error_Message_Part').show();
			return;
		}else{
			b.request({
				url: "/spare_parts/walk_in/search_item",
				data:{
					"search_by": $('#search_part').val()
				},
				on_success: function(data){
					$('#record_data').html(data.data.html);
				}
			});
		}

	});

	$('.add_item').live('click',function(){
		//alert($(this).attr('data'));
		if ($('#quantity').val() <= 0){
			$('#Error_Message_QTY').show();
			return;
		}
		var $item_id = $(this).attr('data');
		var $srp = $(this).attr('data2');
		proceedAddNewModal = b.modal.new({
		title: "Add New Item",
		width:450,
		disableClose: false,
		html: "Are you sure you want to save this Item?",
		buttons: {
			'Ok' : function() {
				proceedAddNewModal.hide();
				b.request({
					url:"/spare_parts/walk_in/add_new_parts",
					data:{
						"order_code": $('#order_code').text(),	
						"item_id": $item_id,
						"discount": $('#discount_option').val(),
						"quantity": $('#quantity').val(),
						"srp": $srp
					},
					on_success: function(data){
						hideLoading();
						redirect('spare_parts/walk_in/add_new_sales/'+ $('#order_code').text());
					}
				})
				}
			}
		})
		proceedAddNewModal.show();
	});

</SCRIPT>
