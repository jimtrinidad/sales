<div style="margin-top:16px;">
	<label for="annTitle" style="display: block;">Title:</label>
	<input type="text" id="annTitle" value="<?php  echo isset($announce)?$announce->title:"";?>" style="width: 329px;padding: 2px;">
	<label for="annContent" style="display: block;">Content:</label>
	<textarea id="annContent" style="max-width: 329px;min-width: 329px;padding: 2px;max-height: 150px;min-height: 150px;"><?php echo isset($announce)?$announce->content:""?></textarea>
	<button id="annSave" style="float: right;">Save</button>
	<input type="hidden" id="aID" value="<?php echo isset($announce)?$announce->id:""?>">
</div>

<script type="text/javascript">
$(document).ready(function(){
	$("#annSave").click(function(){
		if(!$("#annSave").hasClass('clicked')){				
			var form_data = {
						title:$("#annTitle").val(),
						content:$("#annContent").val(),
						aID : $("#aID").val(),
						ajax: '1'
					};
			$.ajax({
				url: "<?php echo site_url('main/saveAnnouncement'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						if(msg==""){
							location.reload();
						}else{
							alert(msg);
							$("#annSave").removeClass('clicked');
						}
					}
				});
		}//end of if clicked
		$("#annSave").addClass('clicked');
	});
});
</script>