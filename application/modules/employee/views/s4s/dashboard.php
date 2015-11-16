<div class="page-header clearfix">
	<center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">S4S <small style="color:#FFFFFF">(System for System)</small></h2></center>
</div>

<fieldset>		
	<div>
		<div class="control-group">					
			<input type="text" class="input-large span11" id="search-data" name='search-data' style='margin-top:10px;' onkeypress="handle(event)" />
			<button class='btn btn-warning' id="search-button" title="Search" style='margin-right: 10px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="icon-search icon-white"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
			<span id="result-count" style="display:none;" class="label">RESULTS</span>				
		</div>									
	</div>			
</fieldset>

<div id="contents"></div>
<div id="pagination"></div>


<script type="text/javascript">
	
	var _current_page = "";

	var loadResults = function(page){

		var _search_data = $("#search-data").val();

		beyond.request({
			url: '/employee/s4s/get_s4s_list',
			data: {
				"page": page,
				"search_data" : _search_data

			},
			on_success: function(data){
				if(data.status) {					
					$("#result-count").html(data.data.result_count);					
					$("#contents").html(data.data.html);
					$("#pagination").html(data.data.pagination);

					$('.goto_page').click(function(e){
						e.preventDefault();
						var new_page = $(this).attr('page');
						_current_page = new_page;
						loadResults(new_page);																
					});
				} else {
					var err_modal = beyond.modal.create({
						title: 'Error :: Error',
						html: data.msg
					});
					err_modal.show();
				}
			}
		});
	};

	

	$(document).ready(function(){
		loadResults(1);
	});

	$("body").on('click', '#search-button', function() {  								
		loadResults(1);
		$("#result-count").show();
	});
	
	function handle(e){
        if(e.keyCode === 13){
           loadResults(1);
           $("#result-count").show();
        }

        return false;
    }

</script>











