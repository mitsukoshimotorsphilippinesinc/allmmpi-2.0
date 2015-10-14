<div id="calendar_of_events">
</div>
<script type="text/javascript">
	var eventsArray = <?= json_encode($eventsArray) ?>;
	
	$(document).ready(function(){
		$("#calendar_of_events").datepicker({
			'dateFormat' : "yy-mm-dd",
			beforeShowDay: function(date){
				var d = new Date(date);
				if(_.contains(eventsArray,d.getFullYear()+"-"+(parseInt(d.getMonth()) + 1)+"-"+d.getDate())) 
					return [true];
				else
					return [false];
			},
			onChangeMonthYear: function(year,month){
				b.request({
					url: "/workbench/calendar/calendar_of_events",
					data: {
						"year": year,
						"month": month
					},
					on_success: function(data){
						if(data.status == "ok")
						{
							eventsArray = data.data.eventsArray;
							$("#calendar_of_events").datepicker("refresh");
						}
					}
				});
			},
			onSelect: function(dateText,inst){
				redirect("/main/news/calendar/"+dateText);
			}
		});
	});
</script>