<div class="ui-widget-content">
	
	<div class="widget-content post-main-wrapper">
		<div style="position: relative;">
			<img style="width: 100px;height: 75px;float: left;" title="<?php echo $program->name?>" class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.$program->logo) ? base_url().'assets/photos/logo/'.$program->logo : base_url().'assets/photos/logo/blanklogo.png'?>">													
		
			<div style="float: left;margin: 3px;width: 213px;">														
				<fieldset class="ui-widget-content" style="padding-bottom: 4px;">
					<legend style="font-size: 12px;font-weight: bold;"><?php echo $program->title.' '.$schedule->batch?> Session Details</legend>				
					<div class="post-author" style="font-size: 11px;"><h5>Date</h5> : <?php echo date('l, M j, Y',strtotime($session->session_date))?></div>
					<div class="post-author" style="font-size: 11px;"><h5>Venue</h5> : <?php echo get_venue($session->session_venue)->venue_name?></div>
					<div class="post-author" style="font-size: 11px;"><h5>Speaker</h5> : <?php echo get_speaker($session->session_speaker)->name?></div>
					<?php if( date('Y',strtotime($session->session_date)) >= date('Y',strtotime(NOW)) AND (userPrivilege('change_session_details') OR userPrivilege('isAdmin'))):?>		
					<a class="editProgramSession" id="<?php echo $session->session_id?>">edit</a>
					<?php endif;?>
				</fieldset>
			</div>
		</div>
		<div class="clearer"></div>
	</div>

</div>

<script type="text/javascript">
$(document).ready(function(){
	$(".editProgramSession").click(function(){
		var fdata = {
				ajax:1,
				session_id : this.id
				};
		ajaxCallBoxOpen('<?php echo site_url('schedule/session_editor')?>',fdata);
	});	
});
</script>
