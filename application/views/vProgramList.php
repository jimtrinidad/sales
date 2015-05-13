<div class="ui-widget-content">
	<div class="widget-content post-main-wrapper">
		<ul class="topList" style="margin: 0 auto;width: 573px;padding-left: 9px;">
			<?php foreach($programs as $program):?>
			<li style="width: auto;padding: 0 4px;">
				<div>
					<div class="programDetails" id="<?php echo $program['id']?>" style="cursor: pointer;" title="<?php echo $program['name']?>">
						<img style="width: 100px;height: 75px;" title="<?php echo $program['name']?>" class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.$program['logo']) ? base_url().'assets/photos/logo/'.$program['logo'] : base_url().'assets/photos/logo/blanklogo.png'?>">													
					</div>
					<div>
						<div align="center" class="post-message">
							<b><?php echo $program['title']?></b><br>
							<?php if(userPrivilege('isAdmin')):?><a class="editProgramSetting" id="<?php echo $program['id']?>">edit</a><?php endif;?>
						</div>						
					</div>
				</div>
			</li>
			<?php endforeach;?>
			<div class="clearer"></div>
		</ul>				
	</div>
</div>
<button class="button addNewProgram" style="margin-top: 2px;">add program</button>

<script type="text/javascript">
$(document).ready(function(){
	$(".editProgramSchedule").click(function(){
		var fdata = {
				ajax:1,
				schedule_id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/schedule_editor')?>',fdata);
	});	

	$('.topList').slimScroll({
		  height: '300px',
		  alwaysVisible: true
	});

	<?php if(userPrivilege('isAdmin')):?>
	$(".programDetails").bind("click",function(){
		var fdata = {
				ajax : 1,
				id : this.id
				};
		myDialogBox('<?php echo site_url('schedule/program_details')?>',fdata,this.id,this.title,{width : '400',height : '300',modal : false});
	    return false;
	});
	<?php endif;?>

	$(".editProgramSetting").bind("click",function(){
		var fdata = {
				ajax : 1,
				id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/program_editor')?>',fdata);
	});			

	$(".button").button();	
	
	$(".addNewProgram").click(function(){
		var fdata = {
				ajax:1
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/add_program')?>',fdata);
	});
	
});
</script>
