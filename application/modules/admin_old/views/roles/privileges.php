<?php

	$privileges_tag = "<select id='available_privileges' style='width:90%;height:100%;' multiple>";
	foreach($privileges as $p){
	    $privileges_tag .= "<option value='{$p->privilege_id}' title='{$title}'>{$p->privilege_code}</option>";
	}
	$privileges_tag .= "</select>";

	$user_role_privileges_tag = "<select id='user_role_privileges' style='width:90%;height:100%;' multiple>";
	foreach($user_role_privileges as $p){
	    $user_role_privileges_tag .= "<option value='{$p->privilege_id}' title='{$title}'>{$p->privilege_code}</option>";
	}
	$user_role_privileges_tag .= "</select>";

?>

<h2>User Role Privileges for <?= $user_role->user_role ?> </h2>
<hr/>
<center>
<table class='static' style='width:80%;text-align:center;'>
    
    <thead>
    <tr>
        <th>Available Privileges</th>
        <th>&nbsp;</th>
        <th>User Role Privileges</th>
    </tr>
    </thead>    
    <tr>
        <td style='width:45%;height:300px;' ><?=$privileges_tag?></td>
        <td style='width:10%;height:300px;' ><br />
            <a href='#' id="priv_add" class='btn btn-primary'><span>&raquo;</span></a><br /><br />
            <a href='#' id="priv_remove" class='btn btn-primary'><span>&laquo;</span></a>
        </td>
        <td style='width:45%;height:300px;' ><?=$user_role_privileges_tag?></td>
    <tr>
</table>
</center>
<div class='clearfix' >&nbsp;</div>
<center>
    <a id='save_privileges' class='btn btn-primary'><span>Save</span></a>&nbsp;
    <a href="<?=$this->config->item('base_url')?>/admin/roles" class='btn'><span>Back</span></a>
</center>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){

        $('#priv_add').click(function(){
            var _selected = $('#available_privileges').val();
            
            $('#available_privileges option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
                    $('#user_role_privileges').append($(this));                    
                }                
            });
            
            return false;        
        });
        
        $('#priv_remove').click(function(){            
            var _selected = $('#user_role_privileges').val();
            
            $('#user_role_privileges option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
                    $('#available_privileges').append($(this));                    
                }                
            });
            
            return false;        
        });

		$("#save_privileges").click(function(){
			
			var _selected_privs = [];
			
            $('#user_role_privileges option').each(function(){                
	            _selected_privs.push($(this).val());
            });

			b.request({
		        url: '/admin/roles/privileges',
		        data: {
					"role_id": parseInt('<?=$user_role->role_id?>'),
					"privileges":_selected_privs
				},
		        on_success: function(data, status) {
					if (data.status==1) redirect("/admin/roles");
		        }
		    });
            
            return false;			
		});
		
	});
</script>