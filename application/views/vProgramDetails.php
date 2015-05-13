<div class="ui-widget-content">
	
	<div class="widget-content post-main-wrapper">
		<div style="position: relative;">
			<img style="width: 100px;height: 75px;float: left;" title="<?php echo $program->name?>" class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.$program->logo) ? base_url().'assets/photos/logo/'.$program->logo : base_url().'assets/photos/logo/blanklogo.png'?>">													
		
			<div style="float: left;margin-left: 5px;width: 173px;">														
				<div class="post-author" style="font-size: 12px;" title="<?php echo $program->name?>"><?php echo $program->title?></div>
			</div>
		</div>
		<div class="clearer"></div>
	</div>

</div>

<script type="text/javascript">
$(document).ready(function(){
	$(".editProgramSchedule").click(function(){
		var fdata = {
				ajax:1,
				schedule_id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/schedule_editor')?>',fdata);
	});	
});
</script>
