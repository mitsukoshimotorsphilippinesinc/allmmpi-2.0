<?php

	$facilities_tag = "<select id='available_facilities' style='width:90%;height:100%;' multiple>";
	foreach($facilities as $p){
	    $facilities_tag .= "<option value='{$p->facility_id}'>{$p->facility_name}</option>";
	}
	$facilities_tag .= "</select>";

	$user_facilities_tag = "<select id='user_facilities' style='width:90%;height:100%;' multiple>";
	foreach($user_facilities as $p){
		if ($p->is_default) $default = " [DEFAULT]";
		else $default = "";
	    $user_facilities_tag .= "<option value='{$p->facility_id}'>{$p->facility_name}{$default}</option>";
	}
	$user_facilities_tag .= "</select>";

?>

<div class='alert alert-info'><h3>User Facilities for <?= $user->first_name . " " . $user->last_name?>  <a class='btn return-btn' style='float:right;margin-right:-30px;' >Back to Users Dashboard</a></h3></div>

<center>
<table class='static' style='width:80%;text-align:center;'>
    
    <thead>
    <tr>
        <th>Available facilities</th>
        <th>&nbsp;</th>
        <th>User facilities</th>
    </tr>
    </thead>    
    <tr>
        <td style='width:45%;height:300px;' ><?=$facilities_tag?></td>
        <td style='width:10%;height:300px;' ><br />
            <a href='#' id="priv_add" class='btn btn-primary'><span>&raquo;</span></a><br /><br />
            <a href='#' id="priv_remove" class='btn btn-primary'><span>&laquo;</span></a>
        </td>
        <td style='width:45%;height:300px;' ><?=$user_facilities_tag?></td>
    <tr>
</table>
</center>
<div class='clearfix' >&nbsp;</div>
<center>
    <a id='save_facilities' class='btn btn-primary'><span>Save</span></a>&nbsp;
    <a href="<?=$this->config->item('base_url')?>/admin/users" class='btn'><span>Back</span></a>
</center>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){

        $('#priv_add').click(function(){
            var _selected = $('#available_facilities').val();
            
            $('#available_facilities option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
                    $('#user_facilities').append($(this));                    
                }                
            });
            
            return false;        
        });
        
        $('#priv_remove').click(function(){            
            var _selected = $('#user_facilities').val();
            
            $('#user_facilities option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
					if ($(this).html().indexOf("[DEFAULT]")>0)
					{
						var _option = "<option value='"+ $(this).val() +"'>" + $(this).html().replace("[DEFAULT]","") + "</option>";
						$('#available_facilities').append(_option);                    
						$(this).remove();                    
					}
 					else 
						$('#available_facilities').append($(this));                    
                }                
            });
            
            return false;        
        });

		$('#user_facilities option').live("click",function(){
            $('#user_facilities option').each(function(){
				var _html = $(this).html();
				$(this).html(_html.replace("[DEFAULT]",""));
			});
			
			var html = $(this).html();
			$(this).html(html + " [DEFAULT]");			
		});

		$("#save_facilities").click(function(){
			
			var _selected_privs = [];			
			var default_facility_id;
			
            $('#user_facilities option').each(function(){		
				var _name = $(this).html();
				var _facility_id = $(this).val();
				if (_name.indexOf("[DEFAULT]")>0) default_facility_id = _facility_id;
	            _selected_privs.push(_facility_id);
            });

			b.request({
		        url: '/admin/users/facilities',
		        data: {
					"user_id": parseInt('<?=$user->user_id?>'),
					"facilities":_selected_privs,
					"default":default_facility_id
				},
		        on_success: function(data, status) {
					if (data.status==1) redirect("/admin/users");
		        }
		    });
		});
		
		$(".return-btn").click(function(){
			redirect('/admin/users');	
			return false;
		});
		
	});
</script>