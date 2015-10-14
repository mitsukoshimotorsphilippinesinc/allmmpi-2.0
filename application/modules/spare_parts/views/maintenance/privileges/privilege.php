<?php

	$privileges_tag = "<select id='available_privileges' style='width:90%;height:100%;' multiple>";
	foreach($privileges as $p){
	    $privileges_tag .= "<option value='{$p->privilege_id}' title='{$title}'>{$p->privilege_code}</option>";
	}
	$privileges_tag .= "</select>";

	$user_privileges_tag = "<select id='user_privileges' style='width:90%;height:100%;' multiple>";
	foreach($user_privileges as $p){
	    $user_privileges_tag .= "<option value='{$p->privilege_id}' title='{$title}'>{$p->privilege_code}</option>";
	}
	$user_privileges_tag .= "</select>";

    $return_url = $this->config->item('base_url') . "/spare_parts/maintenance/privileges";

    $breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-warning'><h3>User Privileges for <?= $employment_information_details->first_name . " " . $employment_information_details->last_name?> <a href='<?= $return_url; ?>' class='btn' style='float:right;margin-right:-30px;' >Back to Users List</a></h3></div>
<hr/>
<center>
<table class='static' style='width:80%;text-align:center;'>
    
    <thead>
    <tr>
        <th>Available Privileges</th>
        <th>&nbsp;</th>
        <th>User Privileges</th>
    </tr>
    </thead>    
    <tr>
        <td style='width:45%;height:300px;' ><?=$privileges_tag?></td>
        <td style='width:10%;height:300px;' ><br />
            <a href='#' id="priv_add" class='btn btn-primary'><span>&raquo;</span></a><br /><br />
            <a href='#' id="priv_remove" class='btn btn-primary'><span>&laquo;</span></a>
        </td>
        <td style='width:45%;height:300px;' ><?=$user_privileges_tag?></td>
    <tr>
</table>
</center>
<div class='clearfix' >&nbsp;</div>
<center>
    <a style='margin-left:-30px;' id='save_privileges' class='btn btn-primary'><span>Save Privileges</span></a>&nbsp;
</center>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){

        $('#priv_add').click(function(){
            var _selected = $('#available_privileges').val();
            
            $('#available_privileges option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
                    $('#user_privileges').append($(this));                    
                }                
            });
            
            return false;        
        });
        
        $('#priv_remove').click(function(){            
            var _selected = $('#user_privileges').val();
            
            $('#user_privileges option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
                    $('#available_privileges').append($(this));                    
                }                
            });
            
            return false;        
        });

		$("#save_privileges").click(function(){
			
			var _selected_privs = [];
			
            $('#user_privileges option').each(function(){                
	            _selected_privs.push($(this).val());
            });

			b.request({
		        url: '/spare_parts/maintenance/edit_privilege',
		        data: {
					"user_id": parseInt('<?=$user->user_id?>'),
					"privileges":_selected_privs
				},
		        on_success: function(data, status) {
					if (data.status==1) redirect("/spare_parts/maintenance/privileges");
		        }
		    });
            
            return false;			
		});
		
	});
</script>