<script type="text/javascript">
$(document).ready(function(){

	$(".tableList th").each(function(){
		$(this).addClass("ui-state-default");
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

<div class="list ui-widget-content" style="max-height: 500px;border: 0;">
	<table cellspacing="0" cellpadding="0" border="0" class="tableList">
		<thead>				
			<tr>
			<th width="20px">#</th><th>Date/Time</th><th>Program</th><th>Mode of Comm.</th><th>Status</th><th>Contact Person</th><th>Company Name</th>
			<th>Telephone</th><th>Mobile</th><th>Fax</th>
			</tr>
		</thead>
		<tbody id="tb">
		<?php $i=1?>
		<?php foreach ($records as $value):?>
			<tr id="trover" rowid="<?php echo $value['did']?>" class="trdetails" >
				<td style="font-size:11px;">
				<input type="hidden" value="<?php echo $value['did']?>"><?php echo $i;?> )</td>
				<td><?php echo date("M j, Y / g:i a",strtotime($value['time'])) ?></td>
				<td><?php echo $value['program'] ?></td>
				<td><?php echo $value['eventType'] ?></td>
				<td><?php echo $value['status'] ?></td>						
				<td><?php echo $value['lastname'].", ".$value['firstname'];echo $value['mi']!=""?" ".$value['mi'].".":"" ?></td>
				<td><?php echo $value['companyName'] ?></td>
				<td><?php echo $value['telephone'] ?></td>
				<td><?php echo $value['mobile'] ?></td>	
				<td><?php echo $value['fax'] ?></td>		
			</tr>
			<?php $i++; ?>
		<?php endforeach;?>
		</tbody>
	</table>
</div>		
