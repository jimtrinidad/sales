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

	$("#userStats table tr:odd").css('background-color','#ededed');
	$(".sortable").tablesorter(); 

	$(".tableList th").each(function(){			 
		$(this).addClass("ui-state-default").css('font-weight','normal');			 
	});
	
	$(".tableList td").each(function(){
		$(this).addClass("ui-widget-content");
	});
	

	$('.tableList tr td:not(:last-child),.tableList tr th:not(:last-child)').css('border-right', '0');
	$('.tableList tr td').css('border-top', '0');	

	$(".tableList tr").hover(
			function(){
				$(this).children("td").addClass("ui-state-hover");
			},
			function(){
				$(this).children("td").removeClass("ui-state-hover");
			}
		);		
});
</script>
	<div id="userStats">
		<div>
			<table cellpadding="0" cellspacing="0" width="100%" class="sortable tableList">
				<thead>
					<tr class="headerGroup">
						<th>Group</th>
						<th colspan="3">Win</th>
						<th colspan="3">Pending</th>
						<th colspan="3">Loss</th>
						<th colspan="3">Rejected</th>
						<th colspan="3">In Calls</th>
						<th colspan="3">In Mails</th>
						<th colspan="3">Out Calls</th>
						<th colspan="3">Out Mails</th>
						<th colspan="3">All</th>
					</tr>
					<tr class="headerSub">
						<th></th>
						<?php foreach($col as $k=>$v):?>
							<th title="Total <?php echo strtolower($v)?>" style="border-left-width: 1px;border-left-style: groove;">Raw</th>
							<th title="<?php echo ucfirst(strtolower($v))?> over total records"><?php $s = explode(" ", $v);foreach($s as $v){echo substr(ucfirst($v),0,1);}?>.Avg</th>
							<th title="<?php echo ucfirst(strtolower($aveType.' '.$v))?> average"><?php echo substr(ucfirst($aveType),0,1)?>.Avg</th>
						<?php endforeach;?>
						<th title="Total Raw Score" style="border-left-width: 1px;border-left-style: groove;">Raw</th>
						<th title="<?php echo ucfirst($aveType)?> average"><?php echo substr(ucfirst($aveType),0,1)?>.Avg</th>
						<th title="Total <?php if($aveType=='daily')echo 'days';elseif($aveType=='weekly')echo 'weeks';else echo 'months'?> count">T.<?php echo substr(ucfirst($aveType),0,1)?>.C.</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($user['programs'] as $program=>$columns):?>
					<tr id="trover">
						<td width="100px"><?php echo $program?></td>
						<?php foreach ($columns as $k=>$column):?>
							<td title="Total <?php echo isset($col[$k])?strtolower($col[$k]):'';echo " on ".strtolower($program);?>" width="39px"style="border-left-width: 1px;border-left-style: groove;"><?php echo isset($column['raw'])?$column['raw']:'' ?></td>
							<?php if(isset($column['ave'])):?><td title="Average <?php echo strtolower($col[$k])?> over <?php echo strtolower($program);?> total records" width="39px"><?php echo $column['ave']?>%</td><?php endif;?>
							<td title="<?php echo $program." ";echo isset($col[$k])?strtolower($aveType.' '.$col[$k]):'total '.strtolower($aveType)?> average"><?php echo isset($column['groupAve'])?$column['groupAve']:'' ?></td>	
							<?php if(isset($column['divisor'])):?><td title="<?php echo $program;?> total <?php if($aveType=='daily')echo 'days';elseif($aveType=='weekly')echo 'weeks';else echo 'months'?> count" width="39px"><?php echo $column['divisor']?></td><?php endif;?>					
						<?php endforeach;?>
					</tr>
				<?php endforeach;?>
				</tbody>
				<tfoot>
					<tr id="trover" class="career">
						<th width="100px"><b>Career</b></th>
						<?php foreach ($user['career'] as $k=>$column):?>
							<th class="" width="39px"style="border-left-width: 1px;border-left-style: groove;"><?php echo isset($column['raw'])?$column['raw']:'' ?></th>
							<?php if(isset($column['ave'])):?><th class="" width="39px"><?php echo $column['ave']?>%</th><?php endif;?>
							<th class=""><?php echo isset($column['groupAve'])?$column['groupAve']:'' ?></th>
							<?php if(isset($column['divisor'])):?><th title="<?php if($aveType=='daily')echo 'days';elseif($aveType=='weekly')echo 'weeks';else echo 'months'?> count" width="39px"><?php echo $column['divisor']?></th><?php endif;?>							
						<?php endforeach;?>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>