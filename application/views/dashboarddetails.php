<script type="text/javascript">
$(document).ready(function(){

	$(".sendemail").bind("click",function(){
		var fdata = {
				detailsid : $(this).attr('id'),
				ajax : 1
				};
		myDialogBox('<?php echo site_url('main/emaileditor')?>',fdata,'emailer','Email Editor',{width : 'auto'});
	});	

	$(".semail").bind("click",function(){
		var fdata = {
				dashboard:1,
				ajax : 1
				};
		myDialogBox('<?php echo site_url('main/emaileditor')?>',fdata,'emailer','Email Editor',{width : 'auto'});
	    return false;
	});		

	$("#sortTable").tablesorter({
		cssHeader : 'thHeader'
	}); 

	
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
<div class="list ui-widget-content" style="max-height: 500px;border: 0">
<?php if(userPrivilege('canSendM')==1):?>
	<a style="padding:3px;float:right;margin-right:5px;" class="semail"><b>Email All</b></a>
	<div class="clearer"></div>
<?php endif;?>		
	<table cellspacing="0" cellpadding="0" border="0" id="sortTable" class="tableList">
		<thead>				
			<tr>
			<th width="20px">#</th><th>Date/Time</th><th>Mode of Comm.</th><th>Status</th><th>Contact Person</th><th>Company Name</th>
			<th>Telephone</th><th>Mobile</th><th>Fax</th><th>Added by</th><th></th>
			</tr>
		</thead>
		<tbody id="tb">
		<?php $i=1?>
		<?php foreach ($records as $value):?>
			<tr id="trover" rowid="<?php echo $value['did']?>" class="trdetails" >
				<td style="font-size:11px;">
				<input type="hidden" value="<?php echo $value['did']?>"><?php echo $i;?> )</td>
				<td><?php echo date("M j, y / g:i a",strtotime($value['time'])) ?></td>
				<td><?php echo $value['eventType'] ?></td>
				<td><?php echo $value['status'] ?></td>						
				<td><?php echo $value['firstname']." ".$value['lastname'];?></td>
				<td><?php echo $value['companyName'] ?></td>
				<td><?php echo $value['telephone'] ?></td>
				<td><?php echo $value['mobile'] ?></td>	
				<td><?php echo $value['fax'] ?></td>
				<td><?php echo $value['user'] ?></td>	
					<td align="right">
						<?php if(userPrivilege('canSendM')==1):?>
							<?php if(!empty($value['email'])):?>
							<a class="sendemail" id="<?php echo $value['did']?>">Send Email</a> &nbsp;
							<?php endif;?>
						<?php endif;?>
					</td>		
			</tr>
			<?php $i++; ?>
		<?php endforeach;?>
		</tbody>
	</table>
</div>
<input type="hidden" value="<?php echo $filters['lw']?>" id="lw">
<input type="hidden" value="<?php echo $date?>" id="day">
<input type="hidden" value="<?php echo $filters['program']?>" id="program">
<input type="hidden" value="<?php echo $filters['status']?>" id="status">