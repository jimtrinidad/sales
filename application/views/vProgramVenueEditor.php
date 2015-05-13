<div id="userEditor" class="contentEditor" style="width: auto">
	<div class="editor-header" >
		Venue Editor
	</div>
	<div class="editor-content ui-widget-content">
		<form id="editorForm" class="">
			<?php if(isset($venue_id)):?>
				<input type="hidden" name="venue_id" id="venue_id" value="<?php echo $venue_id?>">
			<?php endif;?>
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div>
						<label for="venue_name">Venue name</label>
						<input type="text" maxlength="50" style="width: 400px;" name="venue_name" id="venue_name" value="<?php echo isset($venue_name)?$venue_name:""?>">
					</div>
					<div>
						<label for="venue_address">Address</label>
						<input type="text" maxlength="50" style="width: 400px;" name="venue_address" id="venue_address" value="<?php echo isset($venue_address)?$venue_address:""?>">
					</div>
					<div class="half halfleft">
						<label for="limit">Limit</label>
						<input style="width: 50px;" type="text" maxlength="3" name="limit" id="limit" value="<?php echo isset($limit)?$limit:""?>">
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
				url : '<?php echo site_url('schedule/save_venue')?>',
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
