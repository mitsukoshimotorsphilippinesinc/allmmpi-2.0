
<div>

<?php
	if (count($getmotordetail) > 0) {
		$newsku = $getmotordetail[0]->NewSKU;			
		$model = $getmotordetail[0]->Model;
		$modelclass = $getmotordetail[0]->UnitClass;
		$color = $getmotordetail[0]->Color;
		$brand = $getmotordetail[0]->Brand;				
	} else {
		$newsku = "";
	}
?>

<h2 style = "font-weight:bold; font-family:Tw Cen MT;">
	Engine and Chassis Matching
</h2>
<div Class = "body">

<div>

<form ID = "search-record"
	Method = "GET" Action = "">
<div class = "span12">
<p style = "font-weight:bold;font-family:Trebuchet MS;">Shipment No.
<INPUT Class = "tb5"
	   style = "width: 200px;"
	   TYPE = "TEXT" ID = "txtshipment" name ="txtshipment" onClick="SelectAll('txtshipment');">

<Button Class = "btn1" name="btn-action" style = "width:95px;" ID = "tagno" Type= "Submit" Value = "Search">Get Tag No
</Button>
Tag No	   
<INPUT Class = "tb5"
	   style = "margin-left:30px;width: 300px;"
	   TYPE = "TEXT" ID = "txttagno" name ="txttagno" onClick="SelectAll('txttagno');" Value ="<?=$matchedckdcount ?>" 
	   Disabled = 'Disabled'>

<p style = "font-weight:bold;font-family:Trebuchet MS;">Engine No.
<INPUT Class = "tb5"
	   style = "margin-left:15px;width: 300px;"
	   TYPE = "TEXT" ID = "txtengine" name ="txtengine" onClick="SelectAll('txtengine');">
Chassis No.
<INPUT Class = "tb5"
	   style = "margin-left:5px;width: 300px;"
	   TYPE = "TEXT" ID = "txtchassis" name ="txtchassis" onClick="SelectAll('txtchassis');">

<p style = "font-weight:bold;font-family:Trebuchet MS;">Old SKU
<INPUT Class = "tb5"
	   style = "margin-left:30px;width: 200px;"
	   TYPE = "TEXT" ID = "txtoldsku" name ="txtoldsku" Value = "<?=$OldSKU ?>" onClick="SelectAll('txtoldsku');">
<Button Class = "btn1" name="btn-action" style = "width:95px;" ID = "getsku" Type= "Submit" Value = "getsku">Get Details
</Button>
New SKU
<INPUT Class = "tb5"
	   style = "margin-left:18px;width: 300px;"
	   TYPE = "TEXT" ID = "txtnewsku" name	 ="txtnewsku" onClick="SelectAll('txtnewsku');"
	   Disabled = 'Disabled' value="">

<p style = "font-weight:bold;font-family:Trebuchet MS;">Model
<INPUT Class = "tb5"
	  	   style = "width: 300px;margin-left:39px;"
	       TYPE = "TEXT" ID = "txtmodel" name ="txtmodel"
	       Disabled = 'Disabled'>
	Brand
<INPUT Class = "tb5"
	  	   style = "width: 300px;margin-left:35px;"
	       TYPE = "TEXT" ID = "txtbrand" name ="txtbrand"
	       Disabled = 'Disabled'>	

<p style = "font-weight:bold;font-family:Trebuchet MS;">Color
<INPUT Class = "tb5"
	  	   style = "width: 300px;margin-left:45px;"
	       TYPE = "TEXT" ID = "txtcolor" name ="txtcolor"
	       Disabled = 'Disabled'>	
Class
<INPUT Class = "tb5"
	  	   style = "width: 300px;margin-left:40px;"
	       TYPE = "TEXT" ID = "txtclass" name ="txtclass"
	       Disabled = 'Disabled'>	
<br/>

<Button Class = "btn" name="btn-action" style = "margin-left:5px;width:100px;" ID = "save" Type ="Submit" Value = "Save">SAVE
</Button>
</form>
</div>

<?php
if ((count($getmotordetail) == 0) && ($Save == "getsku")){
	?>
	<label ID = "errormessage"><?=$errormessage ?>
	</label>
<?php
}
?>

<div class="clearfix"></div>

<div>
<table  class = "table table-bordered table-condensed"
	style="width:100%">
	<thead>
  		<tr>
  			<th style = "text-align:center;">Tag No</th>
    		<th style = "text-align:center;">Shipment</th>
    		<th style = "text-align:center;">Engine No</th>		
    		<th style = "text-align:center;">Chassis No</th>
    		<th style = "text-align:center;">SKU</th>
    		<th style = "text-align:center;">Model</th>
    		<th style = "text-align:center;">Color</th>
    		<th style = "text-align:center;">Action/Event</th>
  		</tr>
  	</thead>
  	<tbody>
  		 <?php if(empty($transfers)):?>
			<tr><td colspan='9' style='text-align:center;'><strong>No Records Found</strong></td></tr>
			<?php else: ?>
			<?php foreach ($transfers as $t): ?>
			<tr>
				<td style = "text-align:right;"><?= $t->TagNo; ?></td>
				<td><?= $t->ShipmentNo; ?></td>
				<td><?= $t->Engine; ?></td>
				<td><?= $t->Chassis; ?></td>
				<td style = "text-align:right;"><?= $t->OldSKU; ?></td>
				<td style = "text-align:center;"><?= $t->Model; ?></td>
				<td style = "text-align:center;"><?= $t->Color; ?></td>
				<td>
					<button class = "btn1"><span>Disassemble</span></button>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
  	</tbody>	
</table>
</div>
<div class="clearfix"></div>
<div>
	<?= $this->pager->create_links($search_url);  ?>
</div>
</div>

</div>
</div>
<br/>
<div class="clearfix"></div>

 <script type="text/javascript">

	$(document).ready(function(){
                
	var _ShipmentNo = '<?= $ShipmentNo; ?>';
	$("#txtshipment").val(_ShipmentNo);

	var _newsku = '<?= $newsku; ?>';
	$("#txtnewsku").val(_newsku);

	var _model = '<?= $model; ?>';
	$("#txtmodel").val(_model);

	var _brand = '<?= $brand; ?>';
	$("#txtbrand").val(_brand);

	var _modelclass = '<?= $modelclass; ?>';
	$("#txtclass").val(_modelclass);

	var _brand = '<?= $brand; ?>';
	$("#txtbrand").val(_brand);

	var _color = '<?= $color; ?>';
	$("#txtcolor").val(_color);

	});

	
</script>