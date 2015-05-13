<div id="userEditor" class="contentEditor" style="width: 300px;">
	<div class="editor-header" >
		Select visible months
	</div>
	<div class="editor-content ui-widget-content">
		<form id="columnsForm" class="" method="post" action="<?php echo site_url('schedule/generate_pdf') ?>">
			<input type="hidden" value="<?php echo $selected_year?>" name="selected_year">
	   		<table cellpadding="4" cellspacing="0" width="100%" border="0" class="checkBoxList">
	   			<tr>
	   				<td width="50%">
				   		<ul>
						<?php foreach ($right as $key=>$label):?>
							<li>
								<input type="checkbox" value="<?php echo $key?>" id="id_<?php echo $key?>" name="months[]" <?php echo key_exists($key, $checked) ? "checked" : "";?>>
								<label style="display: inline;" for="id_<?php echo $key?>"><span><?php echo $label?></span></label>
							</li>
						<?php endforeach;?>
						</ul>
	   				</td>
	   				<td width="50%" align="left">
				   		<ul>
						<?php foreach ($left as $key=>$label):?>
							<li>
								<input type="checkbox" value="<?php echo $key?>" id="id_<?php echo $key?>" name="months[]" <?php echo key_exists($key, $checked) ? "checked" : "";?>>
								<label style="display: inline;" for="id_<?php echo $key?>"><span><?php echo $label?></span></label>
							</li>
						<?php endforeach;?>
						</ul>		   				
	   				</td>
	   			</tr>
	   		</table>
	   		   
			<div id="editorButtonDiv">
				<button class="button">Continue</button>
				<a class="button" id="cancel">Cancel</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();

	$("#columnsForm").bind("submit",function(){
		var $fields = $(this).find('input[type="checkbox"]:checked');
        if (!$fields.length) {
        	myMessageBox('You must check at least one box.','Error','red',false);
            return false; // The form will *not* submit
        }else{
        	$.fancybox.close();
        }
	});
	$("#cancel").bind("click",function(){$.fancybox.close();});
});
</script>
