<script id='edit_raffle_product_template' type='text/template'>
	<form id='form_raffle_product' onsubmit='return false;'>
		<div>
			<label>Is Active</label>
			<select id='is_active' name='is_active'>
				<option value='1'>Yes</option>
				<option value='0'>No</option>
			</select>
		</div>
		<div>
			<label>Number needed to Generate Entries:</label>
			<input type='text' id='qty_needed' name='qty_needed' class='input-xlarge' placeholder="Enter number of products needed to generate a raffle entry" value="<%= qty_needed %>" />
			<p id='qty_needed_error' class='help-block' style='display:none';></p>
		</div>
		<div>
			<label>No. of Raffle Entries Generated</label>
			<input type='text' id='qty_generated' name='qty_generated' class='input-xlarge' placeholder="Enter number of entries generated per product" value='<%= qty_generated %>' />
			<p id='qty_generated_error' class='help-block' style='display:none';></p>
		</div>
	</form>
	
	<%
	var active  = is_active;
	$('#is_active option[value="'+active+'"]').attr("selected", "selected");
	%>
</script>

