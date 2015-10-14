<div style="margin-bottom: 10px;">
<label style="display:inline;">Transaction ID: </label>
<input id="transaction_id" name="transaction_id" value="" type="text" style="width:200px;">
</div>
<div style="margin-bottom: 10px;">
<label style="display:inline;">Released To: </label>
<input id="released_to" name="released_to" value="" type="text" style="width:200px;">
</div>
<div style="margin-bottom: 10px;">
<label style="display:inline;">SP Cards: </label>
<input id="sp_cards_list" name="sp_cards_list" value="" type="text" style="width:200px;">
</div>
<div style="margin-bottom: 10px;">
<label style="display:inline;">RS Cards: </label>
<input id="rs_cards_list" name="rs_cards_list" value="" type="text" style="width:200px;">
</div>
<div style="margin-bottom: 10px;">
<label style="display:inline;">RF ID Cards: </label>
<input id="rfid_cards_list" name="rfid_cards_list" value="" type="text" style="width:200px;">
</div>
<div style="margin-bottom: 10px;">
<label style="display:inline;">Metrobank Pay Cards: </label>
<input id="pay_cards_list" name="pay_cards_list" value="" type="text" style="width:200px;">
</div>
<a id="release_cards" class="btn">release</a>
<script type="text/javascript">
	$("#release_cards").click(function(){
		b.request({
			url: "/workbench/cards_releasing/release_cards",
			data: {
				"transaction_id": $("#transaction_id").val(),
				"released_to": $("#released_to").val(),
				"sp_cards_list": $("#sp_cards_list").val(),
				"rs_cards_list": $("#rs_cards_list").val(),
				"rfid_cards_list": $("#rfid_cards_list").val(),
				"pay_cards_list": $("#pay_cards_list").val()
			},
			on_sucess: function(data){
				
			}
		});
	});
</script>