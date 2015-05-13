<div id="userEditor" class="contentEditor" style="width: 274px;">
	<div class="editor-header" >
		<?php echo isset($target) ? 'Edit Batch '.$batch : $title.' Next Batch'?>
	</div>
	<div style='padding: 10px;' class="ui-widget-content">
		<form id="editorForm" class="">

			<?php if(isset($id) AND isset($target)):?>
			<input type="hidden" name="id" id="id" value="<?php echo $id?>">
			<?php else:?>
			<input type="hidden" name="schedule_id" id="schedule_id" value="<?php echo $schedule_id?>">
			<input type="hidden" name="programTempID" id="programTempID" value="<?php echo isset($program_setting_id) ? $program_setting_id : $programTempID?>">
			<?php endif;?>
			
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="half" style="width: 115px;">
						<label for="batch">Batch</label>
						<input disabled="disabled" type="text" id="batch" name="batch" value="<?php echo isset($batch) ? $batch : ''?>" style="width: 94px;"> 
					</div>
					<div class="half"  style="width: 105px;">
						<label for="target">Target</label>
						<input type="text" id="target" name="target" value="<?php echo isset($target) ? $target : ''?>" style="width: 94px;">
					</div>
					<div class="date">
						<label for="tempStartDate">Marketing start date</label>
						<input type="hidden" name="dateStart" id="start_date" value="<?php echo isset($start_date)?$start_date: $dateStart?>">
						<input class="tempDate" disabled="disabled" style="width: 210px;" autocomplete="off"  type="text" id="tempStartDate" value="<?php echo isset($start_date)?date("M d, Y",strtotime($start_date)):date("M d, Y",strtotime($dateStart))?>">
					</div>
					<div class="half date">
						<label for="tempDate">Marketing end date</label>
						<input type="hidden" name="dateEnd" id="end_date" value="<?php echo isset($end_date)?$end_date:$dateEnd?>">
						<input class="tempDate" disabled="disabled" style="width: 210px;" autocomplete="off"  type="text" id="tempDate" value="<?php echo isset($end_date)?date("M d, Y",strtotime($end_date)):date("M d, Y",strtotime($dateEnd))?>">
					</div>
					<?php if(!isset($target)):?>
					<div class="radio">
						<input type="checkbox" value="1" id="isActive" name="isActive" checked="checked">
						<label for="isActive"><span>Set as active program</span></label>
					</div>
					<?php endif;?>			   					   		
			   		<div class="clearer"></div>
			   </div>
			</div>
   
			<div id="editorButtonDiv">
				<button class="button"><?php echo isset($id)?"Save Changes":"Save"?></button>
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
		altField: "#end_date",
		yearRange:"c-10:c+10"
	});
	$("#tempStartDate").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:"M dd, yy",
		altFormat: "yy-mm-dd",
		altField: "#start_date",
		yearRange:"c-10:c+10"
	});
		
	
	$("#editorForm").bind("submit",function(){
		if(!$(this).hasClass('submitted'))
		{
			$(this).addClass('submitted');
			var fdata = $(this).serialize();
			$('#editorForm input[disabled]:not(".tempDate")').each( function() {
				fdata = fdata + '&' + $(this).attr('name') + '=' + $(this).val();
	         });
			showMyLoader();
			$.ajax({
				url : '<?php echo site_url('administrator/save_program')?>',
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
