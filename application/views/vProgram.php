<table cellpadding="0" cellspacing="0" border="0" id="mainTable">
	<tbody>
		<tr>
			<td valign="top" class="content ui-widget-content">
				<table class="homecontenttable" cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td>
							<div class="ui-widget-content">
								<div class="ui-widget-header widget-title" style="font-size: 13px;">
									Program Lists									
								</div>
								<div class="sidebar-content widget-content">
									<div class="accordionDiv accordionProgram examinees-list notvisible">
										<?php foreach($programs as $program):?>
										<h3>
											<a class="program-name" id="prog_<?php echo $program['id']?>">
												<img style="width: 40px;height: 35px;" title="<?php echo $program['name']?>" class="" src="<?php echo file_exists('assets/photos/logo/'.$program['logo']) ? base_url().'assets/photos/logo/'.$program['logo'] : base_url().'assets/photos/logo/blanklogo.png'?>">
												<span style="vertical-align: 10px;"><?php echo $program['title']?>
													<?php echo !empty($program['name']) ? ' - '.$program['name'] : ''?>
												</span>
											</a>
											<?php if(userPrivilege('program')):?>
											<span class="accordionHeaderMenu" onclick="return false;">
												<button type="button" title="Add new batch for this program" id="<?php echo $program['id']?>" class="addBatch"><img src="<?php echo base_url()?>assets/images/icons/add_program.png"><span></span></button>																
											</span>
											<?php endif;?>
										</h3>
										<div>
											<div class="accordionDiv batchListDiv">
												<?php foreach($program['batches'] as $batch):?>
												<h3 class="<?php echo $batch['isActive'] ? 'ui-state-highlight' : ''?>">
													<a class="" id="bat_<?php echo $batch['id']?>">
														<span class="name">Batch <?php echo $batch['batch']?></span>
														<?php echo !$batch['isActive'] ? "<span class='fadeTextSmall' style='color:red;margin: 0 5px;'>( inactive batch )</span>" : '';?>
													</a>
														<?php 
															$set_active = '<button type="button" title="Set this batch to active state"  id="'.$batch['id'].'" class="setActive"><img src="'.base_url().'assets/images/icons/set_active.png"></button>';
															$set_inactive = '<button type="button" title="Close this batch"  id="'.$batch['id'].'" class="setInActive"><img src="'.base_url().'assets/images/icons/set_inactive.png"></button>';
															$edit = '<button type="button" title="Edit batch details" id="'.$batch['id'].'" class="editBatch"><img src="'.base_url().'assets/images/icons/edit.png"></button>';
															$status = '<button type="button" title="Program batch status"  id="'.$batch['id'].'" class="programStatus"><img src="'.base_url().'assets/images/icons/columns.png"></button>';
															if($batch['isActive']){
																echo '<span class="accordionHeaderMenu" onclick="return false;">';
																
																if(userPrivilege('program')) echo $edit;
																echo $status;
																if(userPrivilege('program')) echo $set_inactive;
																																		
																echo '</span>';
															}
															else{																
																echo '<span class="accordionHeaderMenu" onclick="return false;">';
																
																echo $status;
																if(userPrivilege('program')) echo $set_active;
																								
																echo '</span>';																
															}
														
														?>													
												</h3>
												<div class="<?php echo $batch['isActive'] ? 'ui-state-highlight' : ''?>">
													<div class="profileInfo">
														<span class="fadeTextSmall">Batch:</span> <h5><?php echo $batch['batch'];?></h5><br>
														<span class="fadeTextSmall">Target:</span> <h5><?php echo $batch['target']?></h5><br>
														<span class="fadeTextSmall">Start date:</span> <h5><?php echo myDate($batch['dateStart'])?></h5><br>
														<span class="fadeTextSmall">End date:</span> <h5><?php echo myDate($batch['dateEnd'])?></h5><br>
														<span class="fadeTextSmall">Date created:</span> <h5><?php echo myDate($batch['dateCreated'])?></h5><br>
													</div>
												</div>
												<?php endforeach;?>
											</div>
										</div>
										<?php endforeach;?>
									</div>
								</div>									
							</div>
						</td>
					</tr>
				</table>
			</td>			
		</tr>
	</tbody>
</table>

<script type="text/javascript">
$(document).ready(function(){

	$(".accordionProgram").accordion({
		active: false,
		collapsible: true,
		autoHeight: false,
		icons: '',
		change: function(event,ui) {
			var hid = ui.newHeader.children('a').attr('id');
			if (hid === undefined) {
				$.cookie('accordionProgramState', null,{path: '/'});
			} else {
				$.cookie('accordionProgramState', hid, {path: '/',expires: 1 });
			}
		}		
	}).removeClass('notvisible');
	if($.cookie('accordionProgramState')) {
		$(".accordionProgram").accordion('option', 'animated', false);
		$(".accordionProgram").accordion('activate', $('#' + $.cookie('accordionProgramState')).parent('h3'));
		$(".accordionProgram").accordion('option', 'animated', 'slide');
	}
	
	$(".batchListDiv").accordion({
		collapsible: true,
		autoHeight: false,
		change: function(event,ui) {
			var hid = ui.newHeader.children('a').attr('id');
			if (hid === undefined) {
				$.cookie('accordionBatchState', null,{path: '/'});
			} else {
				$.cookie('accordionBatchState', hid, {path: '/',expires: 1 });
			}
		}
	});
	if($.cookie('accordionBatchState')) {
		$(".batchListDiv").accordion('option', 'animated', false);
		$(".batchListDiv").accordion('activate', $('#' + $.cookie('accordionBatchState')).parent('h3'));
		$(".batchListDiv").accordion('option', 'animated', 'slide');
	}	

	$(".button").button();	
	$(".accordionHeaderMenu").click(function(){return false;});

	$('.addBatch').click(function(){
		var fdata = {
				ajax : 1,
				program_id : this.id
				};
		//ajaxCallBoxOpen('<?php echo site_url('administrator/new_batch')?>',fdata);
		
		showMyLoader();
		$.ajax({
			url : '<?php echo site_url('administrator/new_batch')?>',
			type : 'POST',
			data : fdata,
			success : function (msg){
				//alert(msg);
				//hideMyLoader();/*
					result = $.parseJSON(msg);
					if(result.status == "error"){
						myMessageBox(result.message,'Error','red',false);
						$("#editorForm").removeClass('submitted');
					}else{
						lightBox(result.message);
					}
				//*/
				}
		});		
	    return false;
	});

	$('.editBatch').click(function(){
		var fdata = {
				ajax : 1,
				program_id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('administrator/edit_batch')?>',fdata);
	});	

	$(".batchListDiv .setInActive").click(function(){
		var name = $(this).parent().parent().find('span.name').text();
		var fdata = {
				program_id : $(this).attr('id'),
				status : 0,
				ajax : 1
				};
		myConfirmBox('Close Batch','Are you sure you want to change '+ name +' state to inactive?',
				function(){
					showMyLoader();
					$.ajax({
						url: '<?php echo site_url('administrator/update_program_batch_status')?>',
						type: 'POST',
						data: fdata,
						success : function (){
							location.reload();
						}
					});
				});
	});

	$(".batchListDiv .setActive").click(function(){
		var name = $(this).parent().parent().find('span.name').text();
		var fdata = {
				program_id : $(this).attr('id'),
				status : 1,
				ajax : 1
				};
		myConfirmBox('Set Active Batch','Are you sure you want to restore '+name+'?',
				function(){
					showMyLoader();
					$.ajax({
						url: '<?php echo site_url('administrator/update_program_batch_status')?>',
						type: 'POST',
						data: fdata,
						success : function (){
							location.reload();
						}
					});
				});
	});	


	$('.programStatus').click(function(e){	
		var fdata = {
				progid : $(this).attr('id'),
				ajax:'1'
				};
		ajaxCallBoxOpen('<?php echo site_url('administrator/showstatus')?>',fdata);
	});	
});
</script>