<?php echo css('jquery-ui/jquery-ui-1.9.1.css');?>
<h2 style="color: #558033;font-weight: normal;margin-bottom: 16px;">Calendar of Events</h2>
<div id="calendar_of_events">
</div>
<script type="text/javascript">
	var eventsArray = <?= json_encode($eventsArray) ?>;

	$(document).ready(function(){
		var seg = (b.uri.path).split("/");
		
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
					url: "/main/calendar/calendar_of_events",
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
		if(seg[2] == "news" && seg[3] == "calendar")
		{
			$("#calendar_of_events").datepicker("setDate", seg[4]);
			//$("#calendar_of_events").datepicker("refresh");
		}
	});
</script>