<style>
#if_container iframe {
	border-width: 0px;
	overflow: hidden;
	height: 50px;
	float: left;
	width:240px;
	margin-left:5px;
}
#if_container .hidden {
	visibility: hidden;
	width:0px;
	height:0px;
}

#im_container div {
	width: 100px;
	height: 100px;
	border-style: solid;
	border-width: 1px;
	border-color: #DEDFDE;
	overflow: hidden;
	float: left;
}

#im_container .pic {
	width: 100px;
	height: 100px;
}
#cerror {
	color:#FF0000;
}
</style>
<div id="userEditor" class="contentEditor">
	<div class="editor-header" >
		<?php echo isset($editorTitle)?$editorTitle:"Editor"?>
	</div>
	<div class="editor-content ui-widget-content">
		<form id="editorForm" class="">
			<?php if(isset($id)):?>
				<input type="hidden" name="id" id="id" value="<?php echo $id?>">
			<?php endif;?>
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div>
						<div id="im_container" style="float:left;">
							<div>
								<img width="100px" height="100px" src="<?php echo isset($logo) ? base_url().'assets/photos/logo/'.$logo : ''?>">
							</div>
						</div>
						<div id="if_container" style="float:left">
							<span id="cerror" style="display:none"></span>
							<iframe src="<?php echo base_url()?>assets/change-upload.php" frameborder="0" scrolling="no"></iframe>
						</div>
					</div>
					<div class="clearer"></div>
					<div class="half halfleft" style="width: 320px;">
						<label for="name">Program</label>
						<input type="text" maxlength="50" style="width: 320px;" name="name" id="name" value="<?php echo isset($name)?$name:""?>">
					</div>
					<div class="half" style="width: 100px">
						<label for="title">Acronym</label>
						<input type="text" maxlength="50" style="width: 100px;" name="title" id="title" value="<?php echo isset($title)?$title:""?>">
					</div>
					<div class="half halfleft">
						<label for="pointReference">Point reference</label>
						<input type="text" maxlength="50" name="pointReference" id="pointReference" value="<?php echo isset($pointReference)?$pointReference:""?>">
					</div>
					<div class="half">
			   			<label for="time_span">Marketing span <span>(weeks)</span></label>
						<input type="text" maxlength="25" name="time_span" id="time_span" value="<?php echo isset($time_span)?$time_span:""?>">
			   		</div>
					<div class="half halfleft">
						<label for="yearly_run">Run for the year</label>
						<input type="text" maxlength="50" name="yearly_run" id="yearly_run" value="<?php echo isset($yearly_run)?$yearly_run:""?>">
					</div>
					<div class="half">
			   			<label for="run_session">Program sessions</label>
						<input type="text" maxlength="25" name="run_session" id="run_session" value="<?php echo isset($run_session)?$run_session:""?>">
			   		</div>
			   		<div class="half halfleft">
						<label for="session_interval">Sessions interval</label>
						<select name="session_interval" id="session_interval" style="width: 222px;">
							<option value="">Select Interval</option>
							<option value="1" <?php echo isset($session_interval) && $session_interval == 1 ? "selected='selected'" : ""?>>Daily</option>
							<option value="7" <?php echo isset($session_interval) && $session_interval == 7 ? "selected='selected'" : ""?>>Weekly</option>
						</select>							
					</div>
					<div class="half">
						<label for="prefer_day">Prefered session day</label>
						<select name="prefer_day" id="prefer_day" style="width: 222px;">
							<?php foreach($days as $k=>$day):?>
							<option value="<?php echo $k?>" <?php echo isset($prefer_day) && $prefer_day == $k ? "selected='selected'" : ""?>><?php echo $day?></option>
							<?php endforeach;;?>
						</select>	
			   		</div>
			   		<?php if( !isset($next_date) OR (isset($next_date) AND $next_date == '0000-00-00')):?>	
					<div class="half halfleft">
			   			<label for="next_batch">Next batch</label>
						<input type="text" maxlength="25" name="next_batch" id="next_batch" value="<?php echo isset($next_batch) ? $next_batch : ""?>">
			   		</div>
					<div class="half date">
						<label for="tempDate">Start of first session</label>
						<input type="hidden" name="next_date" id="next_date" value="<?php echo isset($next_date) ? $next_date : ''?>">
						<input autocomplete="off" type="text" id="tempDate" value="<?php echo isset($next_date) AND $next_date != '0000-00-00' ? date("M d, Y",strtotime($next_date)) : ''?>">
					</div>
					<?php endif;?>
					<div class="half halfleft">
						<label for="default_speaker">Default speaker</label>
						<select name="default_speaker" id="default_speaker" style="width: 222px;">
							<option value="">Select Speaker</option>
							<?php foreach($speakers as $speaker):?>
							<option value="<?php echo $speaker['speaker_id']?>" <?php echo isset($default_speaker) && $default_speaker == $speaker['speaker_id'] ? "selected='selected'" : ""?>><?php echo $speaker['firstname'].' '.$speaker['lastname']?></option>
							<?php endforeach;;?>
						</select>	
			   		</div>
			   		<div class="half">
						<label for="default_venue">Default venue</label>
						<select name="default_venue" id="default_venue" style="width: 222px;">
							<option value="">Select Venue</option>
							<?php foreach($venues as $venue):?>
							<option value="<?php echo $venue['venue_id']?>" <?php echo isset($default_venue) && $default_venue == $venue['venue_id'] ? "selected='selected'" : ""?>><?php echo $venue['venue_name']?></option>
							<?php endforeach;;?>
						</select>	
			   		</div>
                                        <?php if( !isset($next_date) OR (isset($next_date) AND $next_date == '0000-00-00')):?>	
                                        <div class="half halfleft radio" style="padding-top: 1px;">
                                            <input type="radio" id="auto" value="auto" name="generate_type" checked="checked">
                                            <label for="auto">Automatically generate schedule</label>
                                            <input type="radio" id="limit" name="generate_type" value="limited">
                                            <label for="limit">Limit generated schedule</label>
                                        </div>
                                        <div id="until_date_div" class="half hidden">
			   			<label for="tempDateUntil">Generate limit date</label>
						<input type="hidden" maxlength="25" name="until_date" id="until_date" value="<?php echo isset($until_date)?$until_date:""?>">
                                                <input autocomplete="off" type="text" id="tempDateUntil" value="<?php echo isset($until_date) AND $until_date != '0000-00-00' ? date("M d, Y",strtotime($until_date)) : ''?>">
			   		</div>
                                        <?php endif;?>
			   		<div class="clearer"></div>
			   </div>
			</div>
   
			<div id="editorButtonDiv">
				<button class="button"><?php echo isset($id)?"Save Changes":"Save"?></button>
                                <button type="button" class="button" id="cancel">Cancel</button>
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
        
        $("#tempDateUntil").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:"M dd, yy",
		altFormat: "yy-mm-dd",
		altField: "#until_date",
		yearRange:"c-10:c+10"
	});
        
        $('input[name=generate_type]').click(function(){
           if(this.value == 'limited'){
               $('#until_date_div').show();
           }else{
               $('#until_date_div').hide();
           } 
        });
	
	$("#editorForm").bind("submit",function(){
		if(!$(this).hasClass('submitted'))
		{
			$(this).addClass('submitted');
			var fdata = $(this).serialize();
			$('#editorForm input[disabled]').each( function() {
				fdata = fdata + '&' + $(this).attr('name') + '=' + $(this).val();
	         });
			showMyLoader();
			$.ajax({
				url : '<?php echo site_url('schedule/save_program')?>',
				type : 'POST',
				data : fdata,
				success : function (msg){
					//alert(msg);return false;
						result = $.parseJSON(msg);
						if(result.status == "error"){
							myMessageBox(result.message,'Error','red',false);
							$("#editorForm").removeClass('submitted');
						}else{
							myConfirmBox('Success','Your actions has been successfully executed. Do you want to reload the page to see the changes?',
									function(){
										location.reload();
									},
								"Reload","Later");	
							$.fancybox.close();	
						}
					}
			});
		}
		return false;
	});
	$("#cancel").bind("click",function(){$.fancybox.close();});
});
</script>
