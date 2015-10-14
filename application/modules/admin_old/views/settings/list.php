<div class='alert alert-info'><h2>Settings <a class='btn btn-small' href='/admin/setting/add' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>


<div >
	<form id='search_details' method='get' action ='/admin/setting'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="name">Name/Slug</option>
		</select>                 
	
		<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

		<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
		<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>
	
		<br/>
		<span id="search_error" class="label label-important" style="display:none">Search String must not be empty.</span>	
	
		<?php
		if ($search_text == "") {
		?>	
			<div id="search_summary" style="display:none;">
		<?php
		} else {
		?>	
			<div id="search_summary">
		<?php
		};
		?>		
		
			<span class="label label-info">Search Results for:</span>
			<span class="label label-success"><?= $search_by ?></span>
			<span class="label label-success"><?= $search_text ?></span>
		</div>		
	</form>
</div>

<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Name</th>
			<th style='width:400px;'>Value</th>
			<th style='width:400px;'>Default</th>
			<th style='width:80px;'>System</th>
			<th style='width:125px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($settings)): ?>
		<tr><td colspan='5' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($settings as $setting): ?>
		<tr>
			<td><?= $setting->slug; ?></td>
			<td>
				<div id="<?= $setting->slug; ?>_value" style="overflow: auto; min-height: 56px; max-height: 400px;">
					<pre class='prettyprint'><?= $setting->value; ?></pre>
				</div>
			</td>	
			<td>
				<div id="<?= $setting->slug; ?>_default" style="overflow: auto; min-height: 56px; max-height: 400px;">
					<pre class='prettyprint'><?= (empty($setting->default))? "&nbsp;" : $setting->default; ?></pre>
				</div>
			</td>
			<td><?= $setting->system_code; ?></td>
			<td>
				<a href='/admin/setting/edit/<?= $setting->slug ?>' class='btn btn-small btn-primary'><i class="icon-pencil icon-white"></i><span> Edit</span></a>
				<a href='/admin/setting/delete/<?= $setting->slug ?>' class='btn btn-small btn-danger'><i class="icon-remove icon-white"></i><span> Delete</span></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>
<script type="text/javascript" charset="utf-8">
	$("#button_search").live("click",function() {
		var _search_string = $.trim($("#search_string").val());
        var _search_option = $("#search_option").val();
		
		if (_search_string == '') {
			$("#search_error").show();
			$("#search_summary").hide();
		} else {
			$("#search_details").submit();
			$("#search_error").hide(); 
			$("#search_summary").show();           
		}
		
		return false;
	});
	
	$("#button_refresh").live("click",function(e) {
		e.preventDefault();
		redirect('/admin/setting');
	});
	
	$(document).ready(function(){
		$.each($(".show-more"),function(){
			var classes = $(this).attr("class").split(" ");
			var slug =  classes[2];
			var height = $("#"+slug).find(".prettyprint").css("height");
			
			if(parseInt(height) > 400)
			{
				$(this).css("display","");
			}
		});
	});
	
	$(".show-more").click(function(e){
		
		var classes = $(this).attr("class").split(" ");
		var status = classes[1];
		var slug =  classes[2];
		
		if(status == "less")
		{
			$(this).attr("class","show-more more "+slug);
			$("#"+slug).css("overflow","auto");
			$(this).text("Less..")
		}
		else if(status == "more")
		{
			$(this).attr("class","show-more less "+slug);
			$("#"+slug).css("overflow","hidden");
			$(this).text("More..")
		}
		e.preventDefault();
	});
</script>