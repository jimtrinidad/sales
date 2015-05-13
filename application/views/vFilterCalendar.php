<div id="userEditor" class="contentEditor" style="width: auto;">
	<div class="editor-content ui-widget-content">
		<form id="editorForm" method="post" action="<?php echo site_url('schedule/save_filter/'.$segment)?>">
				<div class="half halfleft">
					<label for="session_speaker">Session speaker</label>
					<select name="session_speaker" id="session_speaker" style="width: 255px;">
						<option value="">Any Speaker</option>
						<?php foreach($speakers as $speaker):?>
						<option value="<?php echo $speaker['speaker_id']?>" <?php echo isset($filter['session_speaker']) && $filter['session_speaker'] == $speaker['speaker_id'] ? "selected='selected'" : ""?>><?php echo $speaker['firstname'].' '.$speaker['lastname']?></option>
						<?php endforeach;;?>
					</select>	
		   		</div>
		   		<div class="half">
					<label for="session_venue">Session venue</label>
					<select name="session_venue" id="session_venue" style="width: 255px;">
						<option value="">Any Venue</option>
						<?php foreach($venues as $venue):?>
						<option value="<?php echo $venue['venue_id']?>" <?php echo isset($filter['session_venue']) && $filter['session_venue'] == $venue['venue_id'] ? "selected='selected'" : ""?>><?php echo $venue['venue_name']?></option>
						<?php endforeach;;?>
					</select>	
		   		</div>					   					   		
		   		<div class="clearer"></div>
   
			<div id="editorButtonDiv">
				<button type="submit" class="button">Filter</button>
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
		altField: "#next_date",
		yearRange:"c-10:c+10"
	});
	

});
</script>
