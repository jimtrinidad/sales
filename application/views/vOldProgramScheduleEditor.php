<div id="userEditor" class="contentEditor" style="width: 275px">
	<div class="editor-header" >
		Editor
	</div>
	<div class="editor-content ui-widget-content">
		<form id="editorForm" class="">
			<?php if(isset($id)):?>
				<input type="hidden" name="id" id="id" value="<?php echo $id?>">
			<?php endif;?>
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="half date">
						<label for="tempDate">End of marketing period</label>
						<input type="hidden" name="dateEnd" id="end_date" value="<?php echo isset($dateEnd)?$dateEnd:date("Y-m-d",strtotime(NOW))?>">
						<input autocomplete="off"  type="text" id="tempDate" value="<?php echo isset($dateEnd)?date("M d, Y",strtotime($dateEnd)):date("M d, Y",strtotime(NOW))?>">
					</div>
					<div class="half">
						<label for="target">Target</label>
						<input autocomplete="off" name="target" type="text" id="target" value="<?php echo isset($target)? $target : ''?>">
					</div>			   					   		
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
	
	$("#editorForm").bind("submit",function(){
		var fdata = $(this).serialize();
		showMyLoader();
		$.ajax({
			url : '<?php echo site_url('schedule/save_old_program_schedule')?>',
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
