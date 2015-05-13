<script src="<?php echo base_url()?>assets/js/slimScroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/autoresize.jquery.min.js" type="text/javascript"></script>
<table cellpadding="0" cellspacing="0" border="0" id="mainTable">
	<tbody>
		<tr>
			<td valign="top" class="content ui-widget-content">
				<table class="homecontenttable" cellpadding="0" cellspacing="" border="0" width="100%">
					<tr>
						<td width="45%">
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Programs
									<span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="addNewProgram">add program</a></span>
								</div>
                                                                <div class="sidebar-content widget-content" id="programList" style="height: 463px;overflow-y: auto;">
									<ul class="topList" style="margin: 0 auto;width: 460px;padding-left: 10px;">
										<?php foreach($programs as $program):?>
										<li style="width: auto;padding: 0 4px;">
											<div>
												<div class="programDetails" id="<?php echo $program['id']?>" style="cursor: pointer;" title="<?php echo $program['name']?>">
													<img style="width: 100px;height: 75px;" title="<?php echo $program['name']?>" class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.$program['logo']) ? base_url().'assets/photos/logo/'.$program['logo'] : base_url().'assets/photos/logo/blanklogo.png'?>">													
												</div>
												<div>
													<div align="center" class="post-message">
														<b><?php echo $program['title']?></b><br>
                                                                                                                <?php 
                                                                                                                if(userPrivilege('isAdmin')){
                                                                                                                    if($program['status'] == 1) {
                                                                                                                        ?>
                                                                                                                        <a class="editProgramSetting" id="<?php echo $program['id']?>">edit</a> | 
                                                                                                                        <a class="disableProgramSetting" id="<?php echo $program['id']?>">disable</a> 
                                                                                                                        <?php
                                                                                                                    } else {
                                                                                                                        ?>
                                                                                                                        <a class="enableProgramSetting" id="<?php echo $program['id']?>">re-enable</a>
                                                                                                                        <?php 
                                                                                                                    }
                                                                                                                }
                                                                                                                ?>														
													</div>						
												</div>
											</div>
										</li>
										<?php endforeach;?>
									</ul>										
								</div>									
							</div>
						</td>
						<td width="30%">						
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Program venues
									<span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="addVenue">add venue</a></span>
								</div>
								<div class="sidebar-content widget-content" id="venueList">
									<ul style="margin-bottom: 5px;">
										<?php foreach($venues as $venue):?>
										<li id="<?php echo $venue['venue_id']?>" style="padding-top: 0;padding-bottom: 0;">
											<div class="list-main-wrapper ui-widget-content" style="position: relative;">
												<div class="list-main-content floatleft">
													<div class="list-content"><?php echo $venue['venue_name']?></div>
													<div class="list-note">Address : <?php echo $venue['venue_address']?></div>
													<div class="list-note">Limit : <?php echo $venue['limit']?></div>
												</div>
												<div class="floatright">
													<a id="<?php echo $venue['venue_id']?>" class="editVenue" title="edit venue">
														<img width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/edit.png'?>">
													</a>
													<a id="<?php echo $venue['venue_id']?>" class="disableVenue" title="disable venue">
														<img width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/cancel.png'?>">
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
									Program speakers
									<span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="addSpeaker">add speaker</a></span>
								</div>
								<div class="sidebar-content widget-content" id="speakerList">
									<ul style="margin-bottom: 5px;">
										<?php foreach($speakers as $speaker):?>
										<li id="<?php echo $speaker['speaker_id']?>" style="padding-top: 0;padding-bottom: 0;">
											<div class="list-main-wrapper ui-widget-content" style="position: relative;">
												<div class="list-main-content floatleft">
													<div class="list-content"><?php echo $speaker['firstname'].' '.$speaker['lastname']?></div>
													<div class="list-note">Contact : <?php echo $speaker['contact']?></div>
												</div>
												<div class="floatright">
													<a id="<?php echo $speaker['speaker_id']?>" class="editSpeaker" title="edit speaker">
														<img width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/edit.png'?>">
													</a>
													<a id="<?php echo $speaker['speaker_id']?>" class="disableSpeaker" title="disable speaker">
														<img width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/cancel.png'?>">
													</a>
												</div>
												<div class="clearer"></div>
											</div>
										</li>
										<?php endforeach;?>
									</ul>
								</div>
							</div>															
						</td>
						<td>
							<div class="sidebar-container ui-widget-content">
								<div class="ui-widget-header widget-title">
									Holidays
									<span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="addHoliday">add holiday</a></span>
								</div>
								<div class="sidebar-content widget-content" id="holidayList">
									<ul style="margin-bottom: 5px;">
										<?php foreach($holidays as $holiday):?>
										<li id="<?php echo $holiday['holiday_id']?>" style="padding-top: 0;padding-bottom: 0;">
											<div class="list-main-wrapper ui-widget-content" style="position: relative;">
												<div class="list-main-content floatleft">
													<div class="list-content">
														<?php 
															echo $months[$holiday['date']['month']].' '.$holiday['date']['day'];
															echo isset($holiday['date']['year']) ? ', '.$holiday['date']['year'] : '';
														?>
													</div>
													<div class="list-note"><?php echo $holiday['description'] . ' ('.$holiday['holiday_type'].')'?></div>
												</div>
												<div class="floatright">
													<a id="<?php echo $holiday['holiday_id']?>" class="editHoliday" title="edit holiday">
														<img width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/edit.png'?>">
													</a>
													<a id="<?php echo $holiday['holiday_id']?>" class="removeHoliday" title="remove holiday">
														<img width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/cancel.png'?>">
													</a>
												</div>
												<div class="clearer"></div>
											</div>
										</li>
										<?php endforeach;?>
									</ul>
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

	$(".button").button();

	/*
	$('#programList').slimScroll({
		  height: '473px',
		  alwaysVisible: true
	});
*/
	$('#venueList').slimScroll({
		  height: '220px',
		  alwaysVisible: true
	});

	$('#speakerList').slimScroll({
		  height: '220px',
		  alwaysVisible: true
	});

	$('#holidayList').slimScroll({
		  height: '473px',
		  alwaysVisible: true
	});

	<?php if(userPrivilege('isAdmin')):?>
	$(".programDetails").bind("click",function(){
		var fdata = {
				ajax : 1,
				id : this.id
				};
		myDialogBox('<?php echo site_url('schedule/program_details')?>',fdata,this.id,this.title,{width : '400',height : '300',modal : false});
	    return false;
	});
	<?php endif;?>

	$(".editProgramSetting").bind("click",function(){
		var fdata = {
				ajax : 1,
				id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/program_editor')?>',fdata);
	});
        
        $(".disableProgramSetting").click(function(){
		$obj = $(this);
                var id = this.id;
		myConfirmBox('Disable','Are you sure you want disable this program?',
				function(){                                                                    
                                        window.location = "<?php echo site_url('schedule/program_disable')?>" + '/' + id;
				});
	});
        
        $(".enableProgramSetting").click(function(){
		$obj = $(this);
		var id = this.id;
		myConfirmBox('Enable','Are you sure you want re-enable this program?',
				function(){
                                        window.location = "<?php echo site_url('schedule/program_enable')?>" + '/' + id;
				});
	});

	$(".button").button();	
	
	$(".addNewProgram").click(function(){
		var fdata = {
				ajax:1
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/add_program')?>',fdata);
	});	

	$('.editVenue').click(function(){
		var fdata = {
					venue_id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/edit_venue')?>',fdata);
	});

	$('.addVenue').click(function(){
		var fdata = {};
		ajaxCallBoxOpen('<?php echo site_url('schedule/add_venue')?>',fdata);
	});

	$('.editSpeaker').click(function(){
		var fdata = {
					speaker_id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/edit_speaker')?>',fdata);
	});

	$('.addSpeaker').click(function(){
		var fdata = {};
		ajaxCallBoxOpen('<?php echo site_url('schedule/add_speaker')?>',fdata);
	});

	$(".disableVenue").click(function(){
		$obj = $(this);
		var fdata = {
				venue_id: this.id
				};
		myConfirmBox('Remove','Are you sure you want remove this venue in the list?',
				function(){
					$.ajax({
						url: '<?php echo site_url('schedule/delete_venue')?>',
						type: 'POST',
						data: fdata,
						success : function (){
							$obj.closest('li').remove();
						}
					});
				});
	});	

	$(".disableSpeaker").click(function(){
		$obj = $(this);
		var fdata = {
				speaker_id: this.id
				};
		myConfirmBox('Remove','Are you sure you want remove this speaker in the list?',
				function(){
					$.ajax({
						url: '<?php echo site_url('schedule/delete_speaker')?>',
						type: 'POST',
						data: fdata,
						success : function (){
							$obj.closest('li').remove();
						}
					});
				});
	});

	$('.editHoliday').click(function(){
		var fdata = {
					holiday_id: this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/edit_holiday')?>',fdata);
	});

	$('.addHoliday').click(function(){
		var fdata = {};
		ajaxCallBoxOpen('<?php echo site_url('schedule/add_holiday')?>',fdata);
	});

	$(".removeHoliday").click(function(){
		$obj = $(this);
		var fdata = {
				holiday_id: this.id
				};
		myConfirmBox('Remove','Are you sure you want remove this holiday?',
				function(){
					$.ajax({
						url: '<?php echo site_url('schedule/delete_holiday')?>',
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