<h2>View Announcement With Comments <a href='/operations/announcement/comments' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -10px;">Back</a></h2>

<hr/>
<?php if (empty($announcement)): ?>
	<h3>Announcement not found.</h3>
<?php else: ?>
<form action='' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='announcement_id' name='announcement_id' value='<?= $announcement->announcement_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Title</label>
		<div class="controls">
			<label class='data'><?= $announcement->title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls"  style="width: 797px;">
			<label class='data'><?= $announcement->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_published">Published?</label>
		<div class="controls">
			<label class='data'><?= ($announcement->is_published) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
</fieldset>
</form>

<div>
	<h3>Comments</h3>
	<hr/>
	<table class='table table-striped table-bordered'>
		<thead>
			<tr>
				<th>Sender</th>
				<th>Message</th>
				<th style="width:50px;">Is Removed?</th>
				<th style='width:80px;'>&nbsp;</th>	
			</tr>
		</thead>
		<tbody>
		<?php if(empty($comments)): ?>
			<tr><td colspan='5' style='text-align:center;'><strong>No Comment for this Announcement yet.</strong></td></tr>
		<?php else: ?>
		<?php foreach ($comments as $c): ?>
			<tr>				
				<?php
				$employee_information_view_details = $this->human_relations_model->get_employment_information_view_by_id($c->from_id_number);

				if (empty($employee_information_view_details))
					echo "<td><strong>ADMIN</strong></td>";
				else 
					echo "<td>{$employee_information_view_details->complete_name}</td>";

				if ($c->is_removed == 0)
					echo "<td>{$c->message}</td>";
				else
					echo "<td><s>{$c->message}</s></td>";
				?>
				
				<td><?= ($c->is_removed) ? 'Yes' : 'No'; ?></td>
				<td>

					<a class='btn btn-small btn-success reply-admin' data='<?= $c->announcement_message_id ?>' data-idnum='<?= $c->from_id_number ?>' title="Reply as Admin"><i class="icon-pencil icon-white"></i></a>
					
					<?php
					if ($c->is_removed == 0)
						echo "<a class='btn btn-small btn-danger remove-admin' data='<?= $c->announcement_message_id ?>' data-idnum='<?= $c->from_id_number ?>' title='Delete Comment'><i class='icon-remove icon-white'></i></a>";					
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>	
</div>	

<?php endif; ?>

<div>
<?= $this->pager->create_links();  ?>
</div>


<script type="text/javascript">

	$(".reply-admin").click(function(){
		var _announcement_message_id = $(this).attr('data');

		b.request({
			url : '/operations/announcement/reply_comment',
			data : {
				'announcement_message_id' : _announcement_message_id
				},										
			on_success : function(data) {
				
				if (data.status == "1")	{
				
					var proceedRemoveModal = b.modal.new({
						title: 'Post Admin Comment :: ' + _announcement_message_id,
						width: 450,						
						html: data.data.html,
						buttons: {																
							'Post' : function() {

								var _admin_message = $("#admin_comment_box").val();


								if (_admin_message == "") {
									$("#admin_comment_box-error").show();
									return;
								} else {
									proceedRemoveModal.hide();

									b.request({
										url : '/operations/announcement/reply_comment_proceed',
										data : {
											'announcement_message_id' : _announcement_message_id,
											'admin_message' : _admin_message,
											},										
										on_success : function(data) {
											
											if (data.status == "1")	{
											
												var proceedRemoveModal = b.modal.new({
													title: 'Post Admin Comment :: Successful',
													width: 450,
													disableClose: true,
													html: data.data.html,
													buttons: {																
														'Ok' : function() {
															proceedRemoveModal.hide();
															redirect('/operations/announcement/comments');
														}
													}
												});
												proceedRemoveModal.show();						
											
											} else {
												var errorRemoveCommentModal = b.modal.new({
													title: 'Post Admin Comment :: Error',
													width: 450,
													disableClose: true,
													html: data.data.html,
													buttons: {																
														'Ok' : function() {
															errorRemoveCommentModal.hide();
														}
													}
												});
												errorRemoveCommentModal.show();
											}
										}
									})	
								}
							}
						}
					});
					proceedRemoveModal.show();						
				
				} else {
					var errorRemoveCommentModal = b.modal.new({
						title: 'Funds To Paycard :: Error',
						width: 450,
						disableClose: true,
						html: data.data.html,
						buttons: {																
							'Ok' : function() {
								errorRemoveCommentModal.hide();
							}
						}
					});
					errorRemoveCommentModal.show();
				}
			}
		})	
		

	});

	$(".remove-admin").click(function(){
		var _announcement_message_id = $(this).attr('data');

		var removeCommentModal = b.modal.new({
			title: 'Remove Comment :: ' + _announcement_message_id,
			width:400,			
			html: "Are your sure you want to remove this message in the thread?",
			buttons: {								
				'Proceed' : function() {											
					removeCommentModal.hide();
					b.request({
						url : '/operations/announcement/delete_comment',
						data : {
							'announcement_message_id' : _announcement_message_id
							},										
						on_success : function(data) {
							
							if (data.status == "1")	{
							
								var proceedRemoveModal = b.modal.new({
									title: 'Remove Comment :: Successful',
									width: 450,
									disableClose: true,
									html: data.data.html,
									buttons: {																
										'Ok' : function() {
											proceedRemoveModal.hide();
											redirect('/operations/announcement/comments');
										}
									}
								});
								proceedRemoveModal.show();						
							
							} else {
								var errorRemoveCommentModal = b.modal.new({
									title: 'Remove :: Error',
									width: 450,
									disableClose: true,
									html: data.data.html,
									buttons: {																
										'Ok' : function() {
											errorRemoveCommentModal.hide();
										}
									}
								});
								errorRemoveCommentModal.show();
							}
						}
					})				
				}
			}
		});
		
		removeCommentModal.show();	

	});

</script>