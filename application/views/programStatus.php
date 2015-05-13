<script type="text/javascript">
$(document).ready(function(){
	$('.programstatus_content').slimScroll({
		  height: '500px',
		  alwaysVisible: true
	});

	$(".tableList th").each(function(){			 
		$(this).addClass("ui-state-default");			 
	});


	$('.tableList tr th:not(:last-child)').css('border-right', '0');
	
});
</script>
<div id="programstatus_container" class="contentEditor ui-widget-content" style="width: auto;">
	<div class="editor-header" >
		<?php echo $title?>
	</div>
	<div class="list programstatus_content">
		<table cellpadding="0" cellspacing="0" class="tableList">
			<thead>
				<tr>
					<th></th><th>Weekly Target</th><th>Weekly Won</th><th>Weely Percent</th>
					<th>Accum. Target</th><th>Accum. Won</th><th>Accumulated Percent</th>
				</tr>
			</thead>
			<tbody id="tb" >
				<?php $i=1; foreach ($weeks as $v):?>
				<tr 
					<?php if(date("Y-W",strtotime(NOW))>$v['weekNo'] && $i<$middle && $v['weeklyPercent']<=50):?>style="background: #d7d511"<?php endif;?>
					<?php if(date("Y-W",strtotime(NOW))>$v['weekNo'] && $i>=$middle && $v['accumPercent']<=50):?>style="background: #df7300"<?php endif;?>
				>
					<td class="w" <?php echo date("Y-W",strtotime(NOW))==$v['weekNo']?"style='font-size:15px;background: #11abd7;'":"";?>><?php echo "Week {$i}"?></td>
					<td class="n"><?php echo $v['tPerWeek']?></td>
					<td class="n"><?php echo $v['wonPerWeek']?></td>
					<td class="d n"><?php echo $v['weeklyPercent']?>%</td>	
					<td class="n"><?php echo $v['accumTargetPerWeek']?></td>
					<td class="n"><?php echo $v['accumWonPerWeek']?></td>				
					<td class="d n"><?php echo $v['accumPercent']?>%</td>
				</tr>
				<?php $i++; endforeach;?>
				<tr class="t">
					<th colspan="2" style="text-align: left;">
						<span>Final Result</span> 
					</th>
					<th colspan="5" style="text-align: right;">
						<span>Target: </span><b style="margin-right:10px;"><?php echo $totals['target']?></b> 				
						<span>Won: </span><b><?php echo $totals['totalwon']?></b>
						<b>(<?php echo round($totals['totalwon']/($totals['target']!=0?$totals['target']:1)*100,2)?>%)</b>
					</th>
				</tr>
			</tbody>
		</table>
	</div>
</div>