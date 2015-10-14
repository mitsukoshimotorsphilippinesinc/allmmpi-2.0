<label>Change Card Status from <strong><?= $card->status ?></strong> to:</label>

<?= form_dropdown("status_option",array("ACTIVE" => "ACTIVE", "INACTIVE" => "INACTIVE", "USED" => "USED"),$card->status,'id="status_option" style="width:150px;"')?>
<br/>
<label>Remarks:</label>
<textarea id="status_remarks" style="width:250px;" class="span4"></textarea>