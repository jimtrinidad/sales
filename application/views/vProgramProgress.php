	<div class="programstatus_content list ui-widget-content">
		<table cellpadding="0" cellspacing="0" id="sortTable" class="tableList">
			<thead>
				<tr>
					<th>Program</th>
					<th>Date Range</th>
					<th>Total Weeks</th>
					<th>Current Week</th>
					<th>Time Progress</th>					
					<th title="Current week target">Week Target</th>
					<th title="Total won">Won</th>
					<th title="Current week percentage">Week Percent</th>
					<th>Program Target</th>
					<th>Program Percent</th>
				</tr>
			</thead>
			<tbody id="tb" >
			<?php foreach($programs as $value):?>
				<tr
					<?php if($value['alertLevel'] == '2'):?>style="background: #d7d511"
					<?php elseif($value['alertLevel'] == '3'):?>style="background: #df7300"
					<?php elseif($value['alertLevel'] == '0'):?>style="background: #64c200"
					<?php endif;?>			
				>
					<td>
						<img src="<?php echo base_url()?>assets/photos/logo/<?php echo $value['logo']?>" width="55px" height="40px"><br>
						<b><?php echo $value['title']." ".$value['batch']?></b></td>
					<td class="d" style="font-size: 13px;width: 180px;"><?php echo date("j M, y",strtotime($value['dateStart']))." - ".date("j M, y",strtotime($value['dateEnd']))?></td>
					<td class="n"><?php echo $value['totalWeeks']?></td>
					<td class="d" style="font-size: 13px;"><?php echo $value['weekNo']?></td>
					<td class="d" style="font-size: 13px;"><?php echo $value['weekProgress']?>%</td>
					<td class="n"><?php echo floor($value['currentWeekTarget'])?></td>
					<td class="l n" style="font-size: 20px;"><?php echo $value['won']?></td>
					<td class="n d"><?php echo $value['accumPercent']?>%</td>
					<td class="n"><?php echo $value['target']?></td>
					<td class="n t d"><?php echo $value['programPercent']?>%</td>
				</tr>				
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#sortTable").tablesorter({
		cssHeader : 'thHeader'
	}); 
	$(".tableList th").each(function(){
		$(this).addClass("ui-state-default");
	});
});
</script>