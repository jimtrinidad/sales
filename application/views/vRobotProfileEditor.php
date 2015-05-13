<style>
#if_container iframe {
	border-width: 0px;
	overflow: hidden;
	height: 40px;
	float: left;
	width:100%;
}
#if_container .hidden {
	visibility: hidden;
	width:0px;
	height:0px;
}

#im_container div {
	margin:2px;
	width: 215px;
	height: 215px;
	border-style: solid;
	border-width: 1px;
	border-color: #DEDFDE;
	overflow: hidden;
	float: left;
}

#im_container .pic {
	width: 215px;
	height: 215px;
}
#cerror {
	color:#FF0000;
}
</style>
<div class="contentEditor ui-widget-content" style="width: 244px;">
	<div class="editor-header" >
		Change Photo
	</div>
	<div class="editor-content">
		<div style="width: 220px;">
			<div id="if_container">
			<span id="cerror" style="display:none"></span>
			<iframe src="<?php echo base_url()?>assets/changePhoto.php" frameborder="0" scrolling="no"></iframe>
			</div>
			<div id="im_container">
				<div>
					<img class="pic" src="assets/images/userphoto/<?php echo $this->db->where('key','robot_photo')->get('tb_settings')->row()->value?>">
				</div>
			</div>
			<br>
			<label for="robotName">Name</label>
			<input type="text" name="robotName" style="width: 210px;" id="robotName" value="<?php echo $this->db->where('key','robot_name')->get('tb_settings')->row()->value ?>">
			<div class="clearer"></div>
			<div id="editorButtonDiv" style="margin: 2px;">
				<button class="button" id="savep">Save</button>
			</div>	
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	
	$("#savep").click(function(){
		if(!$("#savep").hasClass('clicked')){
			if($("#robotName").val() != ""){
			$("#savep").addClass('clicked');
			var form_data = {
						robotName: $("#robotName").val(),
						photo : $(".pic").attr('src'),
						ajax: '1'
					};
			$.ajax({
				url: "<?php echo site_url('settings/saveRobot'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						location.reload();			
					}
				});
			}else{
				alert('Name field is required');
			}
		}//end if has class clicked
		
	});

});
</script>