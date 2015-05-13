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
<div id="programtemp_panel">
	<div class="header">
	<span style="float:left;font-weight:bold"><?php echo isset($progID)?"Edit Program Template":"New Program Template";?></span> 
	<div class="clearer"></div>
	</div>
	<div>
	<div id="im_container" style="float:left;">
		<div>
			<?php if(isset($result)):?><img width="100px" height="100px" src="<?php echo base_url()?>assets/photos/logo/<?php echo $result->logo?>"><?php endif;?>
		</div>
	</div>
	<br><br><br><br>
	<div id="if_container" style="float:left">
	<span id="cerror" style="display:none"></span>
	<iframe src="<?php echo base_url()?>assets/change-upload.php" frameborder="0" scrolling="no"></iframe>
	</div>
	<div class="clearer"></div>
	<br>
	<label for="title">Title: </label><input style="width:183px" id="title" type="text" value="<?php echo isset($result)?$result->title:""?>">
	<label for="pointRef">Point Reference: </label><input style="width:119px;margin: 3px 0;" id="pointRef" type="text" value="<?php echo isset($result)?$result->pointReference:""?>">
		<div align="right">
			<button type="submit" id="ptempsave"><img src="assets/images/icons/save_edit.png">Save</button>
		</div>										
	</div>
	<input type="hidden" id="progID" value="<?php echo isset($progID)?$progID:"";?>">
<script type="text/javascript">
$(document).ready(function(){

	$("#ptempsave").click(function(){		
		if(!$("#progsave").hasClass('clicked')){
			var form_data = {
						title : $("#programtemp_panel #title").val(),
						pointReference : $("#programtemp_panel #pointRef").val(),
						logo : $(".pic").attr('src'),
						progID : $("#progID").val(),
						ajax: '1'
					};
			$.ajax({
				url: "<?php echo site_url('administrator/saveprogramtemplate'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						if(msg==''){
								//alert('A new alumni has been added.');
								location.reload(true);							
						}
						else{
							alert(msg);
							$("#ptempsave").removeClass('clicked');
						}
				},
				error:function(){
					//alert('haha');
					$("#ptempsave").removeClass('clicked');
				}
				});
		}//end if has class clicked
		$("#ptempsave").addClass('clicked');
	});
			
});
</script>	
</div>