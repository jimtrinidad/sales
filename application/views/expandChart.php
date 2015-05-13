<script type="text/javascript">
$(document).ready(function(){
	$( "#expandChartheader #close" ).click(function(){
		$( "#expandChartholder" ).fadeOut('fast');
	});

	$("#expandChart_panel").draggable({
		handle:'#expandChartheader',
		scroll: false
	});

	$(".goFilter").bind('click',function(){
		var fdata = {
				chart : $(this).attr('id'),
				user : $('#chartUser').val(),
				eventType : $('#classification').val(),
				ajax : 1
				};
		$(".theChart").empty().html("<img  style='margin:40px 0 30px 435px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		$.ajax({
			url		: '<?php echo site_url('charts/reloadChart')?>',
			type	: 'POST',
			data	: fdata,
			success	: function(chart){
				$(".theChart").html(chart);
				}
		});
	});
});
</script>
<div id="expandChartheader">
	<span style="float:left;margin:5px;padding: 1px;">Chart</span> 
	<span id="close" style="float: right;margin: 5px;padding: 1px;">x</span>
	<div class="clearer"></div>
</div>
<div id="expandChartcontainer">
	<div class="noborderlist">
		<div style="position: relative;">
			<div class="chartButtonDiv" style="width: 300px;">
				<div class="chartFilterDiv">
					<?php if(isset($eventType)):?>
					<select id="classification">
						<option value="">All Activities</option>
						<option value="Won">Won</option>
						<option value="Pending">Pending</option>
						<option value="Loss">Loss</option>
						<option value="Rejected">Rejected</option>														
						<option value="Incoming Call">Incoming Calls</option>
						<option value="Incoming Mail">Incoming Mails</option>														
						<option value="Outgoing Call">Outgoing Calls</option>	
						<option value="Outgoing Mail">Outgoing Mails</option>												
					</select>				
					<?php else:?>
					<select id="chartUser">
						<option value="all">All Users</option>
						<?php foreach ($users as $user):?>
						<option value="<?php echo $user['id']?>"><?php echo $user['name']?></option>
						<?php endforeach;?>
					</select>
					<?php endif;?>
					<input type="button" class="goFilter" id="<?php echo $chartKey?>" value="Filter">
				</div>			
			</div>
			<div class="theChart"><?php echo $chart?></div>
		</div>
	</div>			
</div>
