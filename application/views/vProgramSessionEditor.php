<div id="userEditor" class="contentEditor" style="width: 275px">
	<div class="editor-header" >
		<?php echo isset($program_setting_id) ? getProgramDetails($program_setting_id)->name .' '.$batch : "Editor"?>
	</div>
	<div class="editor-content ui-widget-content">
		<form id="editorForm" class="">
			<?php if(isset($session_id)):?>
				<input type="hidden" name="session_id" id="session_id" value="<?php echo $session_id?>">
				<input type="hidden" name="schedule_id" id="schedule_id" value="<?php echo $schedule_id?>">
			<?php endif;?>
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="half date">
						<label for="tempDate">Session date</label>
						<input type="hidden" name="session_date" id="session_date" value="<?php echo isset($session_date)?$session_date:date("Y-m-d",strtotime(NOW))?>">
						<input autocomplete="off" type="text" id="tempDate" value="<?php echo isset($session_date)?date("M d, Y",strtotime($session_date)):date("M d, Y",strtotime(NOW))?>">
					</div>
			   		<div class="half">
						<label for="session_venue">Session venue</label>
						<select name="session_venue" id="session_venue" style="width: 222px;">
							<?php foreach($venues as $venue):?>
							<option value="<?php echo $venue['venue_id']?>" <?php echo isset($session_venue) && $session_venue == $venue['venue_id'] ? "selected='selected'" : ""?>><?php echo $venue['venue_name']?></option>
							<?php endforeach;;?>
						</select>	
			   		</div>						
					<div class="half">
						<label for="session_speaker">Session speaker</label>
						<select name="session_speaker" id="session_speaker" style="width: 222px;">
							<?php foreach($speakers as $speaker):?>
							<option value="<?php echo $speaker['speaker_id']?>" <?php echo isset($session_speaker) && $session_speaker == $speaker['speaker_id'] ? "selected='selected'" : ""?>><?php echo get_speaker($speaker['speaker_id'])->name?></option>
							<?php endforeach;;?>
						</select>	
			   		</div>	   					   		
			   		<div class="clearer"></div>
			   </div>
			</div>
   
			<div id="editorButtonDiv">
				<button class="button"><?php echo isset($program_id)?"Save Changes":"Save"?></button>
				<a class="button" id="cancel">Cancel</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#tempDate").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:"M dd, yy",
		altFormat: "yy-mm-dd",
		altField: "#session_date",
		yearRange:"c-10:c+10"
	});
	
	$("#editorForm").bind("submit",function(){
		var fdata = $(this).serialize();
		showMyLoader();
		$.ajax({
			url : '<?php echo site_url('schedule/save_program_session')?>',
			type : 'POST',
			data : fdata,
			success : function (msg){
				//alert(msg);
				//hideMyLoader();/*
					result = $.parseJSON(msg);
					if(result.status == "error"){
						myMessageBox(result.message,'Error','red',false);
					}else{
						myConfirmBox('Success','Your actions has been successfully executed. Do you want to reload the page to see the changes?',
								function(){
									location.reload();
								},
							"Reload","Later");	
						$.fancybox.close();	
					}
				//*/
				}
		});
		return false;
	});
	$("#cancel").bind("click",function(){$.fancybox.close();});
});
</script>
