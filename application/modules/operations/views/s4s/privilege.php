<div class="alert alert-danger">
    <h2>Privileges</h2>
</div>
<hr/>

<p>View Privilege Page:</p>
<select name='view_status' id='view_status' style='width:250px;'>
    <option value='position'>by Job Position</option>                       
    <option value='s4s'>by S4S</option>         
</select>

<button id="button_go" class="btn btn-primary" style="margin-top:-10px;margin-left:5px;">
<span>Go</span>
</button>

<script type="text/javascript">
  //<![CDATA[
    $("#button_go").live("click",function() {
        var _s4sId = $(this).attr('data');  

        if ($("#view_status").val() == "position") {
            redirect('operations/s4s/position_view');
        } else {
            redirect('operations/s4s/s4s_view');
        }   

    });


</script>   