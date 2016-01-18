<div class='alert alert-info'><h3>Repair Code : <?= $repair_summary_details->repair_code ?><a class='btn return-btn add-close' style='float:right;margin-right:-30px;' >Back to Repair List</a></h3></div>
<?php
	$requester_name = "N/A";
	if ($repair_summary_details->branch_id <> 0) {
		$requester_summary = $this->human_relations_model->get_branch_by_id($repair_summary_details->branch_id);
		$requester_name = $requester_summary->branch_name;
	} else {
		$requester_summary = $this->human_relations_model->get_employment_information_view_by_id($repair_summary_details->id_number);
		$requester_name = $requester_summary->complete_name;
	}

?>

<table class="table table-bordered table-condensed">
	<thead>
		<tr>			
		</tr>
	</thead>	
	<tbody>
		<tr>
			<th style="width:20em;">Customer</th>
			<td><?=$requester_name?></td>
		</tr>	
		<tr>	
			<th>Reported Concern</th>
			<td><?=$repair_summary_details->reported_concern?></td>
		</tr>	
		<tr>
			<th>Date Received</th>
			<td><?=$repair_summary_details->date_received?></td>
		</tr>	
		<tr>
			<th>Overall Status</th>
			<td><span id="overall_status_caption"><?=$repair_summary_details->overall_status?></span></td>
		</tr>	
		<tr>
			<th>Insert Timestamp</th>
			<td><?=$repair_summary_details->insert_timestamp?></td>
		</tr>	
	</tbody>
</table>	

<div>
	<?= $html ?>
</div>	

<script type="text/javascript">

	$(document).ready(function(){


	});


	$(".post_to_history").live('click', function(e){
		e.preventDefault();

		var _repairDetailId = $(this).attr("data");

		var _addRemarkVal = $("#add_remark_" +_repairDetailId ).val();
		var _addStatusVal = $("#add_status_" +_repairDetailId ).val();

		var _hasError = 0;
		var input_errors = "";

		var _isBranchEspense = 0;

		if (_addStatusVal == 2) {
			var _trNumberIn = $("#add_tr_number_in_" +_repairDetailId ).val();

			if ($.trim(_trNumberIn) == "") {				
				_hasError = 1;
				input_errors += "The TR Number (IN) field is required. ";				
			}
		}

		if (_addStatusVal == 10) {
			var _trNumberOut = $("#add_tr_number_out_" +_repairDetailId ).val();

			if ($.trim(_trNumberOut) == "") {				
				_hasError = 1;
				input_errors += "The TR Number (OUT) field is required. ";				
			}
		}

		if (_addStatusVal == 7) {
			var _poPrice = $("#add_po_price_" +_repairDetailId ).val();
			var _approvedBy = $("#approved_by").val();

			if ($.trim(_poPrice) == "") {				
				_hasError = 1;			
				input_errors += "The Approved Amount field is required. ";
			}

			if ($.trim(_approvedBy) == 0) {				
				_hasError = 1;			
				input_errors += "The Approved By field is required. ";
			}
		}

		if (_addStatusVal == 6) {
			var _proposedPrice = $("#add_proposed_price_" +_repairDetailId ).val();

			if ($.trim(_proposedPrice) == "") {				
				_hasError = 1;					
				input_errors += "The Proposed Amount field is required. ";
			}
		}

		if ($.trim(_addRemarkVal) == "") {		
			_hasError = 1;
			input_errors += "The Remarks field is required. ";
		}
		
		if (_hasError == 1) {
			$('#input_errors_' +_repairDetailId ).html('<p>'+input_errors+'</p>');
			return;
		}

		if (document.getElementById('is_branch_expense_' + _repairDetailId).checked) {
			_isBranchEspense = 1;
		} else {
			_isBranchEspense = 0;
		}

		b.request({
			url: "/information_technology/repairs/add_remark",
			data: {
				"repair_status_id": $("#add_status_" +_repairDetailId ).val(),
				"remarks" : $("#add_remark_" +_repairDetailId ).val(),
				"repair_detail_id" : _repairDetailId,
				"tr_number_in" : $("#add_tr_number_in_" +_repairDetailId ).val(),
				"tr_number_out" : $("#add_tr_number_out_" +_repairDetailId ).val(),
				"po_price" : $("#add_po_price_" +_repairDetailId ).val(),
				"approval_number" : $("#add_approval_number_" +_repairDetailId ).val(),
				"authority_number" : $("#add_authority_number_" +_repairDetailId ).val(),
				"proposed_price" : $("#add_proposed_price_" +_repairDetailId ).val(),
				"is_branch_expense" : _isBranchEspense,
				"approved_by" : $("#approved_by").val(),
				"date_approved" : $("#date_approved").val(),


			},
			on_success: function(data) {

				successModal = b.modal.new({
					title: data.data.title,
					width:450,
					disableClose: true,
					html: data.data.html,
					buttons: {
						'Ok' : function() {							
							successModal.hide();
						}
					}
				});
				successModal.show();

				$("#add_remark_" +_repairDetailId ).val(""),
				$("#body_remarks_" + _repairDetailId).html(data.data.remarks_html);
				$("#add_tr_number_out_" + _repairDetailId).val("");
				$("#add_po_price_" + _repairDetailId).val("");
				$("#add_proposed_price_" + _repairDetailId).val("");
				$("#add_approval_number_" + _repairDetailId).val("");
				$("#add_authority_number_" + _repairDetailId).val("");

				$("#overall_status_caption").html(data.data.overall_status);

			},
			on_error: function(data) {
				// TODO"
				
			}
		});

		return false;
	});

	$(".add_status").live('change', function(e){
		e.preventDefault();

		var _detailId = $(this).attr("data");

		$('#input_errors').html('');

		$("#is_branch_expense_" + _detailId).prop('checked', false);
		if ($(this).val() == '2') {			
			// RECEIVE FROM BRANCH
			$("#add_tr_number_in_" + _detailId).show();
			$("#add_tr_number_out_" + _detailId).hide();
			$("#add_po_price_" + _detailId).hide();
			$("#add_po_price_" + _detailId).val("");
			$("#add_approval_number_" + _detailId).hide();
			$("#add_approval_number_" + _detailId).val("");
			$("#add_authority_number_" + _detailId).hide();
			$("#add_authority_number_" + _detailId).val("");
			$("#add_proposed_price_" + _detailId).hide();
			$("#add_proposed_price_" + _detailId).val("");
			$("#approval_container_" + _detailId).hide();
			$("#approved_by").val(0);						
			$('#add_remark_' + _detailId).addClass('span7').removeClass('span9');
		} else if ($(this).val() == '10') {			
			// FOR DELIVERY
			$("#add_tr_number_in_" + _detailId).hide();
			$("#add_tr_number_out_" + _detailId).show();
			$("#add_po_price_" + _detailId).hide();
			$("#add_po_price_" + _detailId).val("");
			$("#add_approval_number_" + _detailId).hide();
			$("#add_approval_number_" + _detailId).val("");
			$("#add_authority_number_" + _detailId).hide();
			$("#add_authority_number_" + _detailId).val("");
			$("#add_proposed_price_" + _detailId).hide();
			$("#add_proposed_price_" + _detailId).val("");
			$("#approval_container_" + _detailId).hide();
			$("#approved_by").val(0);						
			$('#add_remark_' + _detailId).addClass('span7').removeClass('span9');
		} else if ($(this).val() == '7') {
			// FOR P.O.
			$("#add_tr_number_in_" + _detailId).hide();
			$("#add_tr_number_out_" + _detailId).hide();
			$("#add_po_price_" + _detailId).show();
			$("#add_approval_number_" + _detailId).show();
			$("#add_authority_number_" + _detailId).show();
			$("#add_proposed_price_" + _detailId).hide();
			$("#add_proposed_price_" + _detailId).val("");
			$("#is_branch_expense_container_" + _detailId).show();			
			$("#approval_container_" + _detailId).show();
			$('#add_remark_' + _detailId).addClass('span5').removeClass('span7').removeClass('span9');
		} else if ($(this).val() == '6') {
			$("#add_tr_number_in_" + _detailId).hide();
			$("#add_tr_number_out_" + _detailId).hide();
			$("#add_po_price_" + _detailId).hide();
			$("#add_po_price_" + _detailId).val("");
			$("#add_approval_number_" + _detailId).hide();
			$("#add_approval_number_" + _detailId).val("");
			$("#add_authority_number_" + _detailId).hide();
			$("#add_authority_number_" + _detailId).val("");
			$("#add_proposed_price_" + _detailId).show();
			$("#is_branch_expense_container_" + _detailId).hide();
			$("#approval_container_" + _detailId).hide();
			$("#approved_by").val(0);
			$('#add_remark_' + _detailId).addClass('span7').removeClass('span9');	
		} else {
			$("#add_tr_number_in_" + _detailId).hide();
			$("#add_tr_number_out_" + _detailId).hide();			
			$("#add_po_price_" + _detailId).hide();
			$("#add_po_price_" + _detailId).val("");
			$("#add_approval_number_" + _detailId).hide();
			$("#add_approval_number_" + _detailId).val("");
			$("#add_authority_number_" + _detailId).hide();
			$("#add_authority_number_" + _detailId).val("");
			$("#add_proposed_price_" + _detailId).hide();
			$("#add_proposed_price_" + _detailId).val("");
			$("#is_branch_expense_container_" + _detailId).hide();
			$("#approval_container_" + _detailId).hide();
			$("#approved_by").val(0);
			$('#add_remark_' + _detailId).addClass('span9').removeClass('span7');
		}

	});

	$('.number').keypress(function(event) {
		if ((event.which != 0) && (event.which != 8) && (event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        	event.preventDefault();
    	}
    
    	/*var text = $(this).val();
	    if ((text.indexOf('.') != -1) && (text.substring(text.indexOf('.')).length > 2)) {
	        event.preventDefault();
	    }*/
	});

	$(".add-close").live('click',function(){
		window.location.href = '/information_technology/repairs/listing';
		return false;
	});

</script>