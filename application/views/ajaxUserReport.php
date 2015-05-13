<script type="text/javascript">
$(document).ready(function(){
	var maxZ = 1;
	$(".per_userdiv").bind("mousedown",function(){
		
		$("#userrank").children().each(function(el, i) {
				if(maxZ <= $(i).css("z-index")) {
					maxZ = $(i).css("z-index");
					maxZ++;
				}
		});
		$(this).css('z-index',maxZ);
	});
	//$(".per_userdiv").draggable({handle:'.header'});

	$(".tableList th").each(function(){			 
		$(this).addClass("ui-state-default");			 
	});

	$('.tableList tr th:not(:last-child)').css('border-right', '0');
		
});
</script>
	<div class="title">Ranking</div>
	<?php $i = 1; foreach ($users as $user):?>
		<!-- 
		<div style="z-index:<?php echo $i;?>;" id="per_userdiv" class="per_userdiv" >
			<div class="header"><?php echo $user['name'] ?></div>
			<div  class="noborderlist">
				<table cellpadding="0" cellspacing="0"  style="background: #fcfcfc">
					<thead>
						<tr>
							<th style="text-align: left;">Program</th><th>Closed Deals</th><th>Point Reference</th><th>Points</th>
						</tr>
					</thead>
					<tbody id="<?php echo $user['userid']?>" class="usertbody">
						<?php $totalWon = 0; foreach ($user['programs'] as $program):?>
						<tr>
							<td class="n"><?php echo $program['title']?></td>
							<td class="d"><?php if(userPrivilege('dashDetail')==1 && $program['closeDealCount']>0):?><a class="detailTrigger d" <?php echo "pid='{$program['pid']}' userid='{$user['userid']}' "?> ><?php echo $program['closeDealCount']?></a><?php else: echo $program['closeDealCount']; endif;?></td>
							<td class="d"><?php echo round($program['pointReference'],2)?></td>
							<td class="d"><?php echo $program['points']?></td>
						</tr>
						<?php $totalWon+=$program['closeDealCount']; endforeach;?>
						
						<tr class="inhouse_tr <?php echo $user['inhouse']==0?"hide":""?>">
							<td class="n">In-House</td>
							<td colspan="2"></td>
							<td class="d">
								<span id="inhouse_noteditable" class="d" ><?php echo $user['inhouse'] ?></span>
							</td>
						</tr>
						<tr class="userbonus inhouse_editable hidden">
							<td class="n">In-House</td>
							<td colspan="2"></td>
							<td class="d">
								<input type="text" class="d" id="inhouse" userid="<?php echo $user['userid']?>" style="width:50px;font-size: 14px;padding:0;" value="<?php echo $user['inhouse'] ?>" >
							</td>
						</tr>			
									
						<tr class="bonus_tr <?php echo $user['adjustment']==0?"hide":""?>">
							<td class="n">Adjustment/Bonus</td>
							<td colspan="2"></td>
							<td class="d">
								<span id="bonus_noteditable" class="d" ><?php echo $user['adjustment'] ?></span>
							</td>
						</tr>
						<tr class="userbonus bonus_editable hidden">
							<td class="n">Adjustment/Bonus</td>
							<td colspan="2"></td>
							<td class="d">
								<input type="text" class="d" id="adjustment" userid="<?php echo $user['userid']?>" style="width:50px;font-size: 14px;padding:0;" value="<?php echo $user['adjustment'] ?>" >
							</td>
						</tr>			
									
						<tr  class="t">
							<td colspan="" align="right"><span>Total</span></td>
							<td align="center" ><b><?php echo $totalWon?></b></td>
							<td></td>
							<td align="center"><b><?php echo $user['totalPoints']?></b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		 -->
		<div id="per_userdiv" class="list per_userdiv sidebar-container ui-widget-content">
			<div class="ui-widget-header widget-title">
			<?php echo $user['name'] ?>
			</div>
			<div class="sidebar-content widget-content">
				<table class="tableList" cellpadding="2" cellspacing="0" border="0" width="100%">
					<thead>
						<tr>
							<th style="text-align: left;">Program</th><th>Closed Deals</th><th>Point Reference</th><th>Points</th>
						</tr>
					</thead>
					<tbody id="<?php echo $user['userid']?>" class="usertbody">
						<?php $totalWon = 0; foreach ($user['programs'] as $program):?>
						<tr>
							<td class="n"><?php echo $program['title']?></td>
							<td class="d"><?php if(userPrivilege('dashDetail')==1 && $program['closeDealCount']>0):?><a class="detailTrigger d" <?php echo "pid='{$program['pid']}' userid='{$user['userid']}' "?> ><?php echo $program['closeDealCount']?></a><?php else: echo $program['closeDealCount']; endif;?></td>
							<td class="d"><?php echo round($program['pointReference'],2)?></td>
							<td class="d"><?php echo $program['points']?></td>
						</tr>
						<?php $totalWon+=$program['closeDealCount']; endforeach;?>
						
						<tr class="inhouse_tr <?php echo $user['inhouse']==0?"hide":""?>">
							<td class="n">In-House</td>
							<td colspan="2"></td>
							<td class="d">
								<span id="inhouse_noteditable" class="d" ><?php echo $user['inhouse'] ?></span>
							</td>
						</tr>
						<tr class="userbonus inhouse_editable hidden">
							<td class="n">In-House</td>
							<td colspan="2"></td>
							<td class="d">
								<input type="text" class="d" id="inhouse" userid="<?php echo $user['userid']?>" style="width:50px;font-size: 14px;padding:0;" value="<?php echo $user['inhouse'] ?>" >
							</td>
						</tr>			
									
						<tr class="bonus_tr <?php echo $user['adjustment']==0?"hide":""?>">
							<td class="n">Adjustment/Bonus</td>
							<td colspan="2"></td>
							<td class="d">
								<span id="bonus_noteditable" class="d" ><?php echo $user['adjustment'] ?></span>
							</td>
						</tr>
						<tr class="userbonus bonus_editable hidden">
							<td class="n">Adjustment/Bonus</td>
							<td colspan="2"></td>
							<td class="d">
								<input type="text" class="d" id="adjustment" userid="<?php echo $user['userid']?>" style="width:50px;font-size: 14px;padding:0;" value="<?php echo $user['adjustment'] ?>" >
							</td>
						</tr>			
					</tbody>
					<tfoot>
						<tr  class="t">
							<th colspan="" align="right" style="text-align: left;"><span>Total</span></th>
							<th align="center" ><b><?php echo $totalWon?></b></th>
							<th></th>
							<th align="center"><b><?php echo $user['totalPoints']?></b></th>
						</tr>
						<?php if(userPrivilege('isAdmin') AND in_array($selDateType,array('career','thisYear','prevYear'))):?>
						<tr>
							<th colspan="4" style="font-weight: normal;text-align: left;font-size: 11px;">
								<div>No. of months <b><?=$user['months']?></b><br> Avg points per month <b><?=$user['monthly_ave']?></b></div>
							</th>
						</tr>
						<?php endif;?>												
					</tfoot>
				</table>
			</div>
		</div>
	<?php $i++; endforeach;?>
	<script type="text/javascript">
$(document).ready(function(){

	$(".detailTrigger").bind("click",function(){
		var fdata = {
					dateType : $("#dateType").val(),
					dateFrom : $("#dateFrom").val(),
					dateTo : $("#dateTo").val(),
					programid : $(this).attr('pid'),
					userid : $(this).attr('userid'),
					ajax : 1
				};

		
		myDialogBox('<?php echo site_url('ranking/getDetails')?>',fdata,'rank_details','Details',{width : 'auto',resizable: true});	
		
		/*
			//alert(fdata['day'] + " | "+ fdata['program'] + " | "+ fdata['status']);
		$("#rankdetailscontainer").empty().html("<img  style='margin:50px 0 30px 520px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		$.ajax({
			url : '<?php echo site_url('ranking/getDetails')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){
				$("#rankdetails_panel").html(msg);
				$("#rankdetailsholder").fadeIn('fast');
			},
			error : function(){
				alert('g');
			}
		});
		*/
	});
	
});
</script>