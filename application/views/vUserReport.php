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

	$('.date').datepicker({
		dateFormat:"d M, yy",
		minDate: new Date(2011, 7-1, 1),// min July 1, 2011
		maxDate: '+0d',
		beforeShowDay: $.datepicker.noWeekends
	});

	$("#dateType").change(function(){
		if($("#dateType").val()=="specificDate"){
			$("#rangeForm").removeClass('hidden');
		}else $("#rangeForm").addClass('hidden');
	});	

	var arr = ["career","thisYear","prevYear"];
	
	$("#dateType").change(function(){
		if($.inArray(this.value,arr)>=0){
			$("#sort_type_block").removeClass('hidden');
		}else $("#sort_type_block").addClass('hidden');
	});		

	var edit = false;
	$(".trigger").click(function(){
		var f_data = {
				dateType : $("#dateType").val(),
				dateFrom : $("#dateFrom").val(),
				dateTo : $("#dateTo").val(),
				sort_type : $("#sort_type").val(),
				ajax : '1'
				};
		//$("#userrank").empty().html("<img style='margin:50px 0 90px 525px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		showMyLoader();
		$.ajax({
			url : '<?php echo site_url('ranking/ajaxranking')?>',
			type : 'POST',
			data : f_data,
			success : function(msg){
				$("#userrank").html(msg);		
					
				if($("#dateType").val()=="career"){
					$(".edittriggerdiv").removeClass('hidden');
				}else{
					$(".edittriggerdiv").addClass('hidden');
				}
				$(".edittriggerdiv").html('<button class="editbonustrigger button button_img"><img src="assets/images/icons/edit_user.png">Edit</button>');
				$('.button').button();
				edit = false;
				hideMyLoader();
			},
			error : function(){
				//alert('g');
			}
		});
	});	
	$(".edittriggerdiv").bind("click",function(){
		if(!edit){
			$(".edittriggerdiv").html('<button class="editbonustrigger button button_img"><img src="assets/images/icons/save_edit.png">Save</button>');
			$('.button').button();
			$(".inhouse_editable").removeClass("hidden");
			$(".bonus_editable").removeClass("hidden");
			$(".inhouse_tr").addClass("hidden");
			$(".bonus_tr").addClass("hidden");
			edit = true;
		}else{
			
			var bonus = new Array();			
			$(".userbonus input").each(function(i,v){
				var pairs = new Array();
				pairs[0]= $(v).attr('userid');
				pairs[1]= $(v).attr('id');
				pairs[2]= $(v).val();
				bonus.push(pairs);
			});
			
			var fdata = {
					dateType : $("#dateType").val(),
					userbonus : bonus,
					ajax : 1
					};
			//$("#userrank").empty().html("<img style='margin:50px 0 10px 430px	;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
			showMyLoader();
			$.ajax({
				url:'<?php echo site_url('ranking/editUserPoints')?>',
				type: 'POST',
				data:fdata,
				success:function(msg){
					$("#userrank").html(msg);
					$(".edittriggerdiv").html('<button class="editbonustrigger button button_img"><img src="assets/images/icons/edit_user.png">Edit</button>');
					$('.button').button();
					hideMyLoader();
				}
				
			});
			
			edit = false;
		}
	});

	$('.button').button();

	$(".tableList th").each(function(){			 
		$(this).addClass("ui-state-default");			 
	});

	$('.tableList tr th:not(:last-child)').css('border-right', '0');
		
});
</script>
<div id="" class="ui-widget-content">
	<div style="margin:10px 15px;font-weight: bold;">
		<span>Range:</span>
			<select id="dateType">
			<?php foreach($dateType as $k=>$v):?>
				<option value="<?php echo $k?>" <?php echo isset($selDateType) && $selDateType == $k ? "selected" : '';?>><?php echo $v?></option>
			<?php endforeach;?>
			</select>
		<span id="rangeForm" class="<?php echo isset($selDateType) && $selDateType == 'specificDate' ? '' : 'hidden'?>">
			<label for="dateFrom">From</label>
			<input class="date" type="text" id="dateFrom" value="<?php echo isset($dateFrom) ? $dateFrom : ''; ?>"  style="width: 90px;">
			<label for="dateTo">To</label>
			<input class="date" type="text" id="dateTo" value="<?php echo isset($dateTo) ? $dateTo : ''; ?>" style="width: 90px;">
		</span>
		<?php if(userPrivilege('isAdmin')):?>
		<span id="sort_type_block" class="<?php echo isset($selected_sort_type) && in_array($selected_sort_type, array('career')) ? '' : 'hidden'?>">
			Sort by
			<select id="sort_type">
			<?php foreach($sort_type as $k=>$v):?>
				<option value="<?php echo $k?>" <?php echo isset($selected_sort_type) && $selected_sort_type == $k ? "selected" : '';?>><?php echo $v?></option>
			<?php endforeach;?>
			</select>
		</span>
		<?php endif;?>
		<button class="trigger button button_img"><img src="assets/images/icons/filter_panel.gif">Go</button>
		<?php if(userPrivilege('isAdmin')==1):?><div style="float: right" class="edittriggerdiv hidden"><button class="editbonustrigger button button_img"><img src="assets/images/icons/edit_user.png">Edit</button></div><?php endif;?>
	</div>
	<div id="userrank">
	<div class="title">Ranking</div>
	<?php $i = 1; foreach ($users as $user):?>
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
						<tr class="t">
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
<!-- 									
		<div style="z-index:<?php echo $i;?>;" id="per_userdiv" class="per_userdiv ui-widget-content" >
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
	<?php $i++; endforeach;?>
	</div>
	<?php if(userPrivilege('isAdmin')==1):?><div style="float: right;margin:5px 15px;" class="edittriggerdiv hidden"><button class="editbonustrigger button button_img"><img src="assets/images/icons/edit_user.png">Edit</button></div><?php endif;?>
	<div class="clearer"></div>
</div>
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