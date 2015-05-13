<div id="" class="ui-widget-content">
	<div style="margin:10px 15px;font-weight: bold;" id="statFilterDiv">
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
		<span>Type:</span> 
			<select id="aveType">
				<option value="daily" <?php echo $aveType == 'daily'?"selected":''?>>Daily Avg</option>
				<option value="weekly" <?php echo $aveType == 'weekly'?"selected":''?>>Weekly Avg</option>
				<option value="monthly" <?php echo $aveType == 'monthly'?"selected":''?>>Monthly Avg</option>
			</select>
		<span>Rank by:</span>
			<select id="groupType">
				<?php foreach($col as $k=>$v):?>
				<option value="<?php echo $k?>"  <?php echo $rankGroup == $k?"selected":''?>><?php echo $v?></option>
				<?php endforeach;?>
				<option value="total" <?php echo $rankGroup == 'total'?"selected":''?>>All</option>
			</select>
			<select id="subGroup">
				<option value="raw" <?php echo $rankSubGroup == 'raw'?"selected":''?>>Raw</option>
				<option class="notTotal <?php echo $rankGroup == 'total'?"hidden":''?>" value="ave" <?php echo $rankSubGroup == 'ave'?"selected":''?>><?php echo isset($col[$rankGroup])?in_array($rankGroup, array('im','ic','om','om'))?strtoupper($rankGroup):substr(ucfirst($col[$rankGroup]),0,1):'T'?>.Avg</option>
				<option class="aveTypeRank" value="groupAve" <?php echo $rankSubGroup == 'groupAve'?"selected":''?>><?php echo ucfirst($aveType)?> Avg</option>				
			</select>
		<button class="triggerAve button button_img"><img src="<?php echo base_url()?>assets/images/icons/filter_panel.gif">Go</button>
		<span id="fbLoader" class="hidden" style="margin-left: 5px;"><img src="<?php echo base_url()?>assets/images/fb-loader.gif"></span>
	<button style="float: right;margin-right: -15px;" type="button" class="ecAll button button_img">+/-</button>
	</div>
	<div id="userStats" class="hidden">
	<?php if(isset($users)): $i=1; foreach ($users as $user):?>
		<h3>
			<a class="user-name" id="<?php echo $user['userID']?>"><span style="font-weight: normal;margin-right: 5px;"><?php echo $i?>)</span> <span class="name"><?php echo $user['name']?></span><?php echo $user['active']?'':"<span class='fadeTextSmall' style='color:#ff0000;margin:0 5px;vertical-align:top;'>disabled</span>"?></a>
		</h3>
		<div>
			<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
				<thead>
					<tr class="headerGroup">
						<td>Group</td>
						<td colspan="3">Win</td>
						<td colspan="3">Pending</td>
						<td colspan="3">Loss</td>
						<td colspan="3">Rejected</td>
						<td colspan="3">In Calls</td>
						<td colspan="3">In Mails</td>
						<td colspan="3">Out Calls</td>
						<td colspan="3">Out Mails</td>
						<td colspan="3">All</td>
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
						<td width="100px"><b><?php echo ucwords($dateType[$selDateType])?></b></td>
						<?php foreach ($user['career'] as $k=>$column):?>
							<td class="<?php echo isset($column['raw']) && $k==$rankGroup && $rankSubGroup == 'raw'?"highlight":''?>" width="39px"style="border-left-width: 1px;border-left-style: groove;"><?php echo isset($column['raw'])?$column['raw']:'' ?></td>
							<?php if(isset($column['ave'])):?><td class="<?php echo isset($column['ave']) && $k==$rankGroup && $rankSubGroup == 'ave'?"highlight":''?>" width="39px"><?php echo $column['ave']?>%</td><?php endif;?>
							<td class="<?php echo isset($column['groupAve']) && $k==$rankGroup && $rankSubGroup == 'groupAve'?"highlight":''?>"><?php echo isset($column['groupAve'])?$column['groupAve']:'' ?></td>
							<?php if(isset($column['divisor'])):?><td title="<?php echo ucfirst($dateType[$selDateType])?> <?php if($aveType=='daily')echo 'days';elseif($aveType=='weekly')echo 'weeks';else echo 'months'?> count" width="39px"><?php echo $column['divisor']?></td><?php endif;?>							
						<?php endforeach;?>
					</tr>					
				</tfoot>
			</table>
		</div>
	<?php $i++; endforeach;endif;?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){

	$('.button').button();
	$('.date').datepicker({
		dateFormat:"M dd, yy",
		minDate: new Date(2011, 7-1, 1),// min July 1, 2011
		maxDate: '+0d',
		beforeShowDay: $.datepicker.noWeekends
	});
	$(".ecAll").bind("click",function(){
		$("h3").each(function(i,e){
			$(e).trigger("click");
		});
	});
	
	$("#userStats").addClass("ui-accordion ui-accordion-icons ui-widget ui-helper-reset")
	  .find("h3")
	    .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
	    .hover(function() { $(this).toggleClass("ui-state-hover"); })
	    .prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>')
	    .click(function() {
	      $(this)
	        .toggleClass("ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom")
	        .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e ui-icon-triangle-1-s").end()
	        .next().slideToggle();
	      return false;
	    })
	    .next()
	      .addClass("ui-accordion-content  ui-helper-reset ui-widget-content ui-corner-bottom")
	      .hide();
    $("#userStats").removeClass('hidden');

	$("#userStats table tr:odd").css('background-color','#ededed');

	$("#dateType").change(function(){
		if($("#dateType").val()=="specificDate"){
			$("#rangeForm").removeClass('hidden');
		}else $("#rangeForm").addClass('hidden');
	});
	
	$("#aveType").change(function(){
		var newStr = $(this).find('option:selected').html();
		//newStr = newStr.charAt(0).toUpperCase() + newStr.slice(1);
		$("#subGroup .aveTypeRank").html(newStr);
	});
	
	$("#groupType").change(function(){
		if($(this).val()=='total'){
			$("#subGroup .notTotal").addClass('hidden');
			$("#subGroup option:first").attr('selected','selected');
		}else{
			$("#subGroup .notTotal").removeClass('hidden');
			var newStr = $(this).val();
			var arr = ["im","ic","om","oc"];
			if($.inArray(newStr,arr)>=0){
				newStr = newStr.toUpperCase();
			}else{
				newStr = newStr.toUpperCase().substr(0,1);
			}
			$("#subGroup .notTotal").html(newStr + '.Avg');
		}
	});
	
	$(".triggerAve").bind("click",function(){
		$("#fbLoader").removeClass('hidden');
		var fdata = { 
				aveType : $("#aveType").val(),
				rankGroup : $("#groupType").val(),
				rankSubGroup : $("#subGroup").val(),
				dateType : $("#dateType").val(),
				dateFrom : $("#dateFrom").val(),
				dateTo : $("#dateTo").val(),
				ajax : 1 
				};
		$.ajax({
				url : "<?php echo site_url('statistics/changeAveType')?>",
				type : 'POST',
				data : fdata,
				success : function(s){
					$("#content").html(s);
					$("#fbLoader").addClass('hidden');
				}
			});
	});

	$(".sortable").tablesorter(); 
});	
</script>