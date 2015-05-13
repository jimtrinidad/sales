<div class="ui-widget-content" style="padding: 10px;">
	<table>
		<tr>
			<?php if(count($result)>0):?>
			<td style="border-bottom: 0">
				<?php foreach ($result as $value):?>
					<div style="margin-bottom:5px;">
						<b>Date : <?php echo date("F j, Y / g:i:s A",strtotime($value['time']));?></b>
							<div style="margin-left:20px;">
								Mode of Communication: <span><?php echo $value['eventType']?></span><br>
								Program: <span><?php echo $value['program']?></span> Result: <span><?php echo $value['remark']=="Rejected"?"Rejected":$value['opportunityType']?></span>
								<?php if($value['opportunityType']=="Pending" && $value['cPercent']!=""):?>Chance: <span><?php echo $value['cPercent']?>%</span><br><?php else:echo "<br>"; endif;?>
								<?php echo $value['note']!=""?"Note: <span>{$value['note']}</span>":""?>
							</div>
					</div>
				<?php endforeach;?>
			</td>
			<?php else:?>
			<td style="border-bottom: 0"><div>No records found.</div></td>
			<?php endif;?>
		</tr>
	</table>
</div>