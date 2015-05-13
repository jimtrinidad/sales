<style>
#if_container iframe {
	border-width: 0px;
	overflow: hidden;
	height: 50px;
	float: left;
	width:220px;
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
<div id="program_panel">
	<div class="header">
	<span style="float:left;margin:5px;font-weight:bold"><?php echo isset($progID)?"Edit Program":"New Program";?></span> 
	<span id="close" style="float: right;margin: 5px;font-weight: bold">x</span>
	<div class="clearer"></div>
	</div>
	<div style="padding:10px;">
	<div class="clearer"></div>
	<label>Program</label>
		<select id="programtemp" style="width:90px;">
			<?php foreach ($ptemps as $ptemp):?>
			<option value="<?php echo $ptemp['id']?>" <?php echo isset($result) && $result->programTempID == $ptemp['id']?"selected='selected'":""?>><?php echo $ptemp['title']?></option>
			<?php endforeach;?>
		</select>
	<label for="batch">Batch: </label><input style="width:45px" id="batch" type="text" value="<?php echo isset($result)?$result->batch:""?>">
	<label for="target">Target: </label><input style="width:45px" id="target" type="text" value="<?php echo isset($result)?$result->target:""?>"><br>
	<label for="details">Details: </label><input style="width:279px" id="details" type="text" value="<?php echo isset($result)?$result->details:""?>"><br>	
	<label for="dateStart">Start Date: </label><input style="width:261px" class="date" id="dateStart" type="text" value="<?php echo isset($result)&& $result->dateStart!='0000-00-00'?date("M j, Y",strtotime($result->dateStart)):""?>"><br>	
	<label for="dateEnd">End Date: </label><input style="width:267px" id="dateEnd" class="date" type="text" value="<?php echo isset($result)&& $result->dateEnd!='0000-00-00'?date("M j, Y",strtotime($result->dateEnd)):""?>"><br>	
		<div align="right">
			<?php if(isset($progID)):?><span style="padding:2px;margin:2px;"><input type="checkbox" id="isActive" value="1" name="isActive" <?php echo !empty($result->isActive)?"checked='checked'":""?>><label for="isActive">isActive</label></span><?php else:?><input name="isActive" type="hidden" value="1" id="isActive"><?php endif;?><button type="submit" id="progsave"><img src="assets/images/icons/save_edit.png">Save</button>
		</div>										
	</div>
	<input type="hidden" id="progID" value="<?php echo isset($progID)?$progID:"";?>">
<script type="text/javascript">
$(document).ready(function(){
	$('#addprogram_panel #close').bind('click',function(e){
		$('#addprogram_panel').fadeOut(500);
		addprogram=false;
	});
	
	$(".date").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:"M d, yy",		
		yearRange:"2000:2020"
	});
	$("#progsave").click(function(){		
		if(!$("#progsave").hasClass('clicked')){
			var form_data = {
						programTempID : $("#addprogram_panel #programtemp").val(),
						batch : $("#addprogram_panel #batch").val(),
						target : $("#addprogram_panel #target").val(),
						details : $("#addprogram_panel #details").val(),
						dateStart : $("#addprogram_panel #dateStart").val(),
						dateEnd : $("#addprogram_panel #dateEnd").val(),
						isActive : <?php if(isset($progID)):?>$("#addprogram_panel input:[name='isActive']:checked").val(),<?php else:?>$("#addprogram_panel #isActive").val(),<?php endif;?>
						progID : $("#progID").val(),
						ajax: '1'
					};
			$.ajax({
				url: "<?php echo site_url('administrator/saveprogram'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						if(msg==''){
								//alert('A new alumni has been added.');
								location.reload(true);							
						}
						else{
							alert(msg);
							$("#progsave").removeClass('clicked');
						}
				},
				error:function(){
					alert('haha');
					$("#progsave").removeClass('clicked');
				}
				});
		}//end if has class clicked
		$("#progsave").addClass('clicked');
	});
			
});
</script>	
</div>