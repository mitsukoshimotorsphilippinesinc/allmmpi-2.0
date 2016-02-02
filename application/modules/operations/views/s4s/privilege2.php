<?php

	$positions_tag = "<select id='available_positions' style='width:90%;height:100%;' multiple>";
	foreach($positions as $p){
	    $positions_tag .= "<option value='{$p->position_id}' title='{$title}'>{$p->position_name}</option>";
	}
	$positions_tag .= "</select>";

	$s4s_positions_tag = "<select id='s4s_positions' style='width:90%;height:100%;' multiple>";
	foreach($s4s_positions as $p){
	    $s4s_positions_tag .= "<option value='{$p->position_id}' title='{$title}'>{$p->position_name}</option>";
	}
	$s4s_positions_tag .= "</select>";

?>

<h2>S4S Privileges <a href='/operations/s4s' class='btn btn-small' style="float:right">Back</a></h2>
<hr/>
<h3><?= $s4s->pp_name ?></h3>
<br/>
<center>
<table class='static' style='width:80%;text-align:center;'>
    
    <thead>
    <tr>
        <th>Positions</th>
        <th>&nbsp;</th>
        <th>Positions Allowed</th>
    </tr>
    </thead>    
    <tr>
        <td style='width:45%;height:300px;' ><?=$positions_tag?></td>
        <td style='width:10%;height:300px;' ><br />
            <a href='#' id="priv_add" class='btn btn-primary'><span>&raquo;</span></a><br /><br />
            <a href='#' id="priv_remove" class='btn btn-primary'><span>&laquo;</span></a>
        </td>
        <td style='width:45%;height:300px;' ><?=$s4s_positions_tag?></td>
    <tr>
</table>
</center>
<div class='clearfix' >&nbsp;</div>
<center>
    <a id='save_privileges' class='btn btn-primary'><span>Save</span></a>&nbsp;
    <a href="<?=$this->config->item('base_url')?>/admin/users" class='btn'><span>Back</span></a>
</center>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){

        $('#priv_add').click(function(){
            var _selected = $('#available_positions').val();
            
            $('#available_positions option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
                    $('#s4s_positions').append($(this));                    
                }                
            });
            
            return false;        
        });
        
        $('#priv_remove').click(function(){            
            var _selected = $('#s4s_positions').val();
            
            $('#s4s_positions option').each(function(){                
                if($(this).attr("selected") == "selected"){                    
                    $('#available_positions').append($(this));                    
                }                
            });
            
            return false;        
        });

		$("#save_privileges").click(function(){
			
			var _selected_privs = [];
			
            $('#s4s_positions option').each(function(){                
	            _selected_privs.push($(this).val());
            });

			b.request({
		        url: '/operations/s4s/privilege',
		        data: {
					"s4s_id": parseInt('<?= $s4s->s4s_id ?>'),
					"positions":_selected_privs
				},
		        on_success: function(data, status) {
					if (data.status==1) redirect("/operations/s4s");
		        }
		    });
            
            return false;			
		});
		
		$(".return-btn").click(function(){
			redirect('/operations/s4s');	
			return false;
		});
		
	});
</script>