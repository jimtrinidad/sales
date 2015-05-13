<div style='padding: 2px;width: 317px;' class="ui-widget-content">
	
	<div class="widget-content post-main-wrapper">
		<div style="position: relative;">
			<img style="width: 100px;height: 75px;float: left;" title="<?php echo getProgramDetails($program_setting_id)->name.' '.$batch?>" class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.getProgramDetails($program_setting_id)->logo) ? base_url().'assets/photos/logo/'.getProgramDetails($program_setting_id)->logo : base_url().'assets/photos/logo/blanklogo.png'?>">													
		
			<div style="float: left;margin-left: 5px;width: 173px;">														
				<div class="post-author" style="font-size: 12px;" title="<?php echo getProgramDetails($program_setting_id)->name?>"><?php echo getProgramDetails($program_setting_id)->title.' '.$batch?></div>
				<fieldset class="ui-widget-content">
					<legend style="font-size: 10px">Marketing Period</legend>				
					<div class="post-author" style="font-size: 10px;"><h5>From</h5> : <?php echo date('F j, Y',strtotime($start_date))?></div>
					<div class="post-author" style="font-size: 10px;"><h5>To</h5> : <?php echo date('F j, Y',strtotime($end_date))?></div>
					<?php if( date('Y',strtotime($end_date)) >= date('Y',strtotime(NOW)) AND (userPrivilege('change_marketing_period') OR userPrivilege('isAdmin'))):?>		
					<a class="editProgramSchedule" id="<?php echo $schedule_id?>">edit</a>
					<?php endif;?>
                    <?php if(my_session_value('uid') == 1):?>		
                        <a class="deleteSchedule" id="<?php echo $schedule_id?>">delete</a>
					<?php endif;?>
				</fieldset>
			</div>
		</div>
		<div class="clearer"></div>
		<fieldset class="ui-widget-content" style="margin-top: 5px;padding: 2px;">
			<legend style="font-size: 10px">Sessions</legend>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%" align="center" valign="top">
						<ul>
							<?php 
							foreach($left as $session):
								$cssClass = "";
						
								if($session['speaker_counter'] > 1 AND $session['counter'] > $session['limit'])
								{
									$cssClass =  "both_alert ";				
								}
								elseif($session['counter'] > $session['limit'])
								{
									$cssClass =  "venue_alert ";			
								}
								elseif($session['speaker_counter'] > 1)
								{
									$cssClass =  "speaker_alert ";			
								}
							?>
							<li>
								<a class="session_details <?php echo $cssClass?>" id="<?php echo $session['session_id']?>"><b style="font-size: 10px;"><?php echo  date("l, M d, Y",strtotime($session['session_date']));?></b></a>
							</li>
							<?php endforeach;?>
						</ul>	
					</td>				
					<td align="center" valign="top">
						<ul>
							<?php 
							foreach($right as $session):
								$cssClass = "";
						
								if($session['speaker_counter'] > 1 AND $session['counter'] > $session['limit'])
								{
									$cssClass =  "both_alert ";				
								}
								elseif($session['counter'] > $session['limit'])
								{
									$cssClass =  "venue_alert ";			
								}
								elseif($session['speaker_counter'] > 1)
								{
									$cssClass =  "speaker_alert ";			
								}							
							?>
							<li>
								<a class="session_details <?php echo $cssClass?>" id="<?php echo $session['session_id']?>"><b style="font-size: 10px;"><?php echo  date("l, M d, Y",strtotime($session['session_date']));?></b></a>
							</li>
							<?php endforeach;?>
						</ul>						
					</td>
				</tr>
			</table>						
		</fieldset>
	</div>

</div>

<script type="text/javascript">
$(document).ready(function(){
	$(".editProgramSchedule").click(function(){
		var fdata = {
				ajax:1,
				schedule_id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/schedule_editor')?>',fdata);
	});	

	$(".session_details").bind("click",function(){
		var fdata = {
				ajax : 1,
				session_id : this.id
				};
		myDialogBox('<?php echo site_url('schedule/session_details')?>',fdata,'session_details','Session Details',{width : '350'});
	    return false;
	});	

    //sakin lang to.. delekado
    <?php if(my_session_value('uid') == 1){ ?>
    $(".deleteSchedule").click(function(){
        var sche_id = this.id;
		myConfirmBox('Confirm','Are you sure you want to remove this record?',
				function(){
					var fdata = {
							schedule_id : sche_id,
							ajax : '1'
							};
					$.ajax({
						url : '<?php echo site_url('schedule/delete_schedule')?>',
						data : fdata,
						type : 'POST',
						success : function(msg){
							location.reload();
						}
					});
				},
			"Yes","No");
	});
    <?php } ?>
});
</script>
