<script src="<?php echo base_url()?>assets/js/slimScroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/changePhoto.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/autoresize.jquery.min.js" type="text/javascript"></script>
<table cellpadding="0" cellspacing="0" border="0" id="mainTable">
	<tbody>
		<tr>
			<td valign="top" class="content ui-widget-content">
				<table class="homecontenttable" cellpadding="0" cellspacing="" border="0" width="100%">
					<tr>
						<td width="25%">
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Allowed IP
								</div>
								<div class="sidebar-content widget-content" id="ipList">
									<ul style="margin-bottom: 5px;">
										<?php foreach($allowedip as $ip):?>
										<li id="<?php echo $ip['id']?>" style="padding-top: 0;padding-bottom: 0;">
											<div class="list-main-wrapper ui-widget-content" style="position: relative;">
												<div class="list-main-content floatleft">
													<div class="list-content"><?php echo $ip['ip']?></div>
													<div class="list-note"><?php echo $ip['note']?></div>
												</div>
												<div class="floatright">
													<a id="<?php echo $ip['id']?>" class="deleteIP" title="delete ip address">
														<img width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/trash.gif'?>">
													</a>
												</div>
												<div class="clearer"></div>
											</div>
										</li>
										<?php endforeach;?>
									</ul>										
								</div>									
							</div>		
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Add new ip address
								</div>
								<div class="sidebar-content widget-content">
									<form id="editorForm" class="">
										<div id="tabs" class="">
											<div id="tabs-basic" class="">
							
										   		<div class="half halfleft">
													<label for="ip">IP Address</label>
													<input type="text" id="ip" name="ip" value="" style="width: 97%;margin: 1px;">
												</div>
												<div class="half halfleft">
													<label for="note">Description</label>
													<input type="text" id="note" name="note" value="" style="width: 97%;margin: 1px;">
												</div>		
												<div class="clearer"></div>		
																		   		
										   </div>	
										</div>
							   
										<div id="editorButtonDiv">
											<button class="button">Save</button>
										</div>
									</form>
								</div>
							</div>	
						</td>
						<td width="25%">						
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Robot profile
								</div>
								<div class="sidebar-content widget-content">
									<div class="floatleft">
										<img style="width: 80px;height: 80px;" src="<?php echo file_exists('assets/images/userphoto/'.$this->db->where('key','robot_photo')->get('tb_settings')->row()->value) ? base_url().'assets/images/userphoto/'.$this->db->where('key','robot_photo')->get('tb_settings')->row()->value : base_url().'assets/images/userphoto/blank.jpg'?>" width="206px" height="190px">
									</div>
									<div class="floatleft" style="margin-left: 5px;font-size: 13px;">
									Name:
									<b><?php echo $this->db->where('key','robot_name')->get('tb_settings')->row()->value ?></b>
									<br>
									<a id="<?php ?>" class="editmini">Edit</a>
									</div>
									<div class="clearer"></div>
								</div>
							</div>
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Weekly announcement template
								</div>
								<div class="sidebar-content widget-content">
									<form id="postFormTemplate" class="editor">
										<div style="margin-left: 5px;font-size: 11px;padding: 0;font-weight: normal;">
											<label>Title</label>
											<input type="text" name="postTitle" id="postTitle" style="display: block;width: 94%;margin-bottom: 7px;" value="<?php echo $this->db->where('key','weekly_post_top_title')->get('tb_settings')->row()->value ?>">
											<label>Weekly message</label>
											<div style="font-weight: normal;font-size: 9px;float: right;margin-right:5px;"><span style="font-weight: normal;font-size: 9px;vertical-align: -12px;">Keys: {name},{date},{points}</span></div>
											<textarea name="postMessage" id="postMessage" class="messageTextarea" style="display: block;width: 94%;margin-bottom: 7px;" ><?php echo $this->db->where('key','weekly_post_top_content')->get('tb_settings')->row()->value ?></textarea>
											
											<label>Consecutive week</label>
											<div style="font-weight: normal;font-size: 9px;float: right;margin-right:5px;"><span style="font-weight: normal;font-size: 9px;vertical-align: -12px;">Keys: {name},{date},{points}</span></div>
											<textarea name="consecPostMessage" id="consecPostMessage" class="messageTextarea" style="display: block;width: 94%;" ><?php echo $this->db->where('key','weekly_post_consecutive')->get('tb_settings')->row()->value ?></textarea>
											<button class="button saveWeeklyPost">Save</button>
										</div>
										<div class="clearer"></div>
									</form>
								</div>
							</div>								
						</td>
						<td width="25%">
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Success program auto post
								</div>
								<div class="sidebar-content widget-content">
									<form id="programFormTemplate" class="editor">
										<div style="margin-left: 5px;font-size: 11px;padding: 0;font-weight: normal;">
											<label>Title</label>
											<input type="text" name="postTitle" id="postTitle" style="display: block;width: 94%;margin-bottom: 7px;" value="<?php echo $this->db->where('key','success_program_title')->get('tb_settings')->row()->value ?>">
											<label>Success message</label>
											<div style="font-weight: normal;font-size: 9px;float: right;margin-right:5px;"><span style="font-weight: normal;font-size: 9px;vertical-align: -12px;">Keys: {name},{program},{batch},{wins}</span></div>
											<textarea name="postMessage" id="postMessage" class="messageTextarea" style="display: block;width: 94%;margin-bottom: 7px;" ><?php echo $this->db->where('key','success_program_content')->get('tb_settings')->row()->value ?></textarea>																			
											<button class="button saveWeeklyPost">Save</button>
										</div>
										<div class="clearer"></div>
									</form>
								</div>
							</div>							
						</td>
						<td></td>
					</tr>
				</table>
			</td>			
		</tr>
	</tbody>
</table>
<script type="text/javascript">
$(document).ready(function(){

	$(".button").button();
        
        $(".editmini").bind("click",function(){
		var fdata = {
				ajax : 1,
				id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('settings/editRobot')?>',fdata);
	});

	$(".messageTextarea").autoResize({extraSpace:10});
	$("#postFormTemplate").bind("submit",function(){
		var fdata = $(this).serialize();
		$.ajax({
			url : '<?php echo site_url('settings/savePostTemplate')?>',
			type : 'POST',
			data : fdata,
			success : function (msg){
					result = $.parseJSON(msg);
					if(result.status == "error"){
						alert(result.message);
					}else{
						location.reload();
					}
				}
		});
		return false;
	});	

	$("#programFormTemplate").bind("submit",function(){
		var fdata = $(this).serialize();
		$.ajax({
			url : '<?php echo site_url('settings/programSuccessSaveTemplate')?>',
			type : 'POST',
			data : fdata,
			success : function (msg){
					result = $.parseJSON(msg);
					if(result.status == "error"){
						alert(result.message);
					}else{
						location.reload();
					}
				}
		});
		return false;
	});		
	
	$('#ipList').slimScroll({
		  height: '205px'
	});
		
	$(".addIP").click(function(){
		var fdata = {
				ajax:1
				};
		ajaxCallBoxOpen('<?php echo site_url('settings/ipEditor')?>',fdata);
	});	

	$("#editorForm").bind("submit",function(){
		var fdata = $(this).serialize();
		$.ajax({
			url : '<?php echo site_url('settings/saveIP')?>',
			type : 'POST',
			data : fdata,
			success : function (msg){
					result = $.parseJSON(msg);
					if(result.status == "error"){
						alert(result.message);
					}else{
						location.reload();
					}
				}
		});
		return false;
	});	

	$(".deleteIP").click(function(){
		$obj = $(this);
		var fdata = {
				ipID: $(this).attr('id')
				};
		confirm('Are you sure you want to delete this ip address?',
				function(){
					$.ajax({
						url: '<?php echo site_url('settings/deleteIP')?>',
						type: 'POST',
						data: fdata,
						success : function (){
							$obj.closest('li').remove();
						}
					});
				});
	});	
});
</script>	