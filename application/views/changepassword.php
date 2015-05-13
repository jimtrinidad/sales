<script type="text/javascript">
$(document).ready(function(){
	$("#change").click(function(){
		if(!$("#change").hasClass('clicked')){
			$("#change").addClass('clicked');
			var form_data = {
						newp: $("#new").val(),
						con: $("#con").val(),
						uID: $("#uID").val(),
						ajax: '1'
					};
			$.ajax({
				url: "<?php echo site_url('user/change'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						if(msg=='change'){
							myMessageBox('Password has been changed','Error','red',
								function(){
									location.reload(true);
								});								
						}
						else{
							myMessageBox(msg,'Error','red',false);
							$("#change").removeClass('clicked');
						}
					}
				});
			}//end of if clicked		
	});	//end save click

	$('.button').button();
});
</script>
<div id="userEditor" class="contentEditor" style="width: auto">
	<div class="editor-header" >
		Change Password
	</div>
	<div class="editor-content ui-widget-content">
		<form id="editorForm" class="">
			<input type="hidden" id="uID" value="<?php if(isset($uID)):echo $uID;endif; ?>">
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div>
						<label for="new">New Password:</label>
						<input type="password" maxlength="50" style="width: 300px;" name="new" id="new">
					</div>
					<div>
						<label for="con">Retype Password</label>
						<input type="password" maxlength="50" style="width: 300px;" name="con" id="con">
					</div>	   					   		
			   		<div class="clearer"></div>
			   </div>
			</div>
   
			<div id="editorButtonDiv" align="right">
				<button type="button" class="button button_img" id="change" name="change">Save</button>
			</div>
		</form>
	</div>
</div>