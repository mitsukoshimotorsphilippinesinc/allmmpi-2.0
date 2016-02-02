<div class="page-header clearfix">
	<center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">S4S <small style="color:#FFFFFF">(System for System)</small></h2></center>
</div>

<h3 id="department_name_title"></h3>

<fieldset>		
	<div>
		<div class="control-group">					
			<input type="text" placeholder="Type your search text here..." class="input-large span11" id="search-data" name='search-data' style='margin-top:10px;' onkeypress="handle(event)" />
			<button class='btn btn-warning' id="search-button" title="Search" style='margin-right: 10px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="icon-search icon-white"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
			<span id="result-count" style="display:none;" class="label">RESULTS</span>				
		</div>									
	</div>			
</fieldset>

<div id="contents"></div>
<div id="pagination"></div>


<script type="text/javascript">
	
	var _current_page = "";
	var _segment = "<?= $segment ?>";

	var loadResults = function(page){

		var _search_data = $("#search-data").val();

		beyond.request({
			url: '/employee/s4s/get_s4s_list',
			data: {
				"page": page,
				"segment": _segment,
				"search_data" : _search_data

			},
			on_success: function(data){
				if(data.status) {					
					$("#result-count").html(data.data.result_count);					
					$("#contents").html(data.data.html);
					$("#department_name_title").html(data.data.department_name);
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

    $(".link-s4s").live("click", function(){
    	var _s4sId = $(this).attr("data");
    	
    	// check if already accepted or not
    	beyond.request({
			url: '/employee/s4s/check_acceptance',
			data: {
				"s4sId": _s4sId				

			},
			on_success: function(data){
				if(data.status == "1") {					
						
					if (data.data.is_accepted == 0) {
						// display modal
						// show add form modal					
						viewAcceptanceModal = b.modal.new({
							title: data.data.title,
							width: 800,							
							html: data.data.html,
							buttons: {
								'Do Not Accept' : function() {
									viewAcceptanceModal.hide();
									logS4sAcceptance(0, _s4sId);
								},
								'Accept' : function() {
									viewAcceptanceModal.hide();
									logS4sAcceptance(1, _s4sId);
								}									
							}
						});
						viewAcceptanceModal.show();

					} else {
						// proceed to pdf loading						
						//window.open("s4s/view/" + _s4sId);
						window.open("view/" + _s4sId);
					}

				} else {
					var err_modal = beyond.modal.create({
						title: 'Error :: Error',
						html: data.msg
					});
					err_modal.show();
				}
			}
		});

    });

	var logS4sAcceptance = function(_is_accepted, _s4sId) {
		
		beyond.request({
			url : '/employee/s4s/log_acceptance',
			data : {
				's4s_id' : _s4sId,
				'is_accepted' : _is_accepted,				
			},
			on_success : function(data) {
				if (data.status == "1")	{									
					var acceptanceModal = b.modal.new({
						title: data.data.title,
						disableClose: true,
						html: data.data.html,
						buttons: {							
							'Ok' : function() {																
								acceptanceModal.hide();

								if (_is_accepted == 1) {
									$(".acceptance-date-" + _s4sId).html(data.data.date_accepted);
									window.open("view/" + _s4sId);
								}
							}
						}
					});
					acceptanceModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};

</script>











