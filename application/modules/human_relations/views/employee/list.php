<?php
	//$upload_url = $this->config->item("media_url") . "/agents";
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>

<h2>Employee List <a href='/hr/employee' class='btn btn-small'  style='float:right;'><i class="icon-plus"></i><span> Add New</span></a></h2>
<hr/>

<form id='search_details' method='get' action =''>

	<strong>Search By:&nbsp;</strong>
	<select name="search_option" id="search_option" style="width:150px;" value="<?= $search_by ?>">		
		<option value="id">ID</option>
		<option value="complete_name">Complete Name</option>
		<option value="department">Department</option>
		<option value="postion">Position</option>

	</select>                 

	<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

	<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
	<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>

	<br/>
	<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>	

	</div>		
</form>

<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style="width: 5em;">Image</th>
			<th style="width: 15em;">ID</th>
			<th style="width: 15em;">Complete Name</th>
			<th style="width: 15em;">Department</th>
			<th style="width: 15em;">Position</th>
			<th style="width: 15em;">Is_Employeed</th>
			<th style='width: 10em;'>&nbsp;</th>
		</tr>
	</thead>