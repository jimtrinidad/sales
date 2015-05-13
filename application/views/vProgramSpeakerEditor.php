<div id="userEditor" class="contentEditor" style="width: auto">
	<div class="editor-header" >
		Speaker Editor
	</div>
	<div class="editor-content ui-widget-content">
		<form id="editorForm" class="">
			<?php if(isset($speaker_id)):?>
				<input type="hidden" name="speaker_id" id="speaker_id" value="<?php echo $speaker_id?>">
			<?php endif;?>
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div>
						<label for="firstname">Firstname</label>
						<input type="text" maxlength="50" style="width: 300px;" name="firstname" id="firstname" value="<?php echo isset($firstname)?$firstname:""?>">
					</div>
					<div>
						<label for="lastname">Lastname</label>
						<input type="text" maxlength="50" style="width: 300px;" name="lastname" id="lastname" value="<?php echo isset($lastname)?$lastname:""?>">
					</div>
					<div class="half halfleft">
						<label for="contact">Contact</label>
						<input style="width: 200px;" maxlength="50" type="text" name="contact" id="contact" value="<?php echo isset($contact)?$contact:""?>">
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
	
	$("#editorForm").bind("submit",function(){
		if(!$(this).hasClass('submitted'))
		{
			$(this).addClass('submitted');
			var fdata = $(this).serialize();
			showMyLoader();
			$.ajax({
				url : '<?php echo site_url('schedule/save_speaker')?>',
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
		}
		return false;
	});
	$("#cancel").bind("click",function(){$.fancybox.close();});
});
</script>
