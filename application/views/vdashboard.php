<script type="text/javascript">
$(document).ready(function(){
	$(".date").datepicker({
		showWeek: true,
		dateFormat:"MM d, yy",
		firstDay: 1
	});
	
	$(".trigger").click(function(){
		var fdata = {
				type : $("#oppType").val(),
				week : $("#week").val(),
				ajax : '1'
				};
		showMyLoader();
		//$(".dashboard").empty().html("<img style='margin:50px 0 10px 520px	;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		$.ajax({
			url : '<?php echo site_url('dashboard/reload')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){
				$(".dashboard").html(msg);
				hideMyLoader();
			},
			error : function(){
				//alert('g');
			}
		});
	});	

	$('.button').button();

	$(".tableList th").each(function(){			 
		$(this).addClass("ui-state-default");			 
	});

	$(".tableList td").each(function(){
		$(this).addClass("ui-widget-content");
	});

	$('.tableList tr th:not(:last-child)').css('border-right', '0');

	
});
</script>
<div class="list ui-widget-content" style="border: 0;">
	<div class="ui-widget-content" style="padding: 5px 0 2px 7px;">
		<span>Status:</span> <select id="oppType">
				<option value="Won" <?php echo $status=="Won"?"selected='selected'":""?>>Won</option>
				<option value="Pending" <?php echo $status=="Pending"?"selected='selected'":""?>>Pending</option>
				<option value="Loss" <?php echo $status=="Loss"?"selected='selected'":""?>>Loss</option>
				<option value="Rejected" <?php echo $status=="Rejected"?"selected='selected'":""?>>Rejected</option>
				<option value="all" <?php echo $status=="all"?"selected='selected'":""?>>All</option>
			</select>
		<span>Date: </span>
		<input type="text" class="date" id="week" value="<?php echo $dateval?>">	
		<button class="trigger button button_img"><img src="assets/images/icons/filter_panel.gif">Go</button>
	</div>
	<div class="dashboard ui-widget-content">
		<table cellpadding="0" cellspacing="0" id="sortTable" class="tableList">
			<thead>
				<tr>
					<th>Program</th><th>Date Range</th><th>Previous Weeks Totals</th>
					<?php foreach ($th as $head):?>
					<th><?php echo date("l",$head)."<br>".date("n/j/y",$head)?></th>
					<?php endforeach;?>
					<th>Totals</th>
				</tr>
			</thead>
			<tbody >
				<?php foreach ($final as $value):?>
				<tr id="tb" >
					<td>
						<img src="<?php echo file_exists('assets/photos/logo/'.$value['logo']) ? base_url().'assets/photos/logo/'.$value['logo'] : base_url().'assets/photos/logo/blanklogo.png'?>" width="55px" height="40px"><br>
						<b><?php echo $value['program']." ".$value['batch']?></b></td>
					<td class="d"><?php echo date("j M, y",strtotime($value['dateStart']))." - ".date("j M, y",strtotime($value['dateEnd']))?></td>
					<td class="ui-state-highlight n"><?php if(userPrivilege('dashDetail')==1 && $value['lastweeks']>0):?><a class="dashtrigger n"<?php echo "lw='1' date='{$value['lastweeksfilter']['lastweeks']}' program='{$value['lastweeksfilter']['program']}' status='{$value['lastweeksfilter']['status']}'"; ?>><?php echo $value['lastweeks']?></a><?php else: echo $value['lastweeks']; endif;?></td>
					<td class="n"><?php if(userPrivilege('dashDetail')==1 && $value['mon']>0):?><a class="dashtrigger n" <?php echo "date='{$value['monfilter']['day']}' program='{$value['monfilter']['program']}' status='{$value['monfilter']['status']}'"; ?>><?php echo $value['mon']?></a><?php else: echo $value['mon']; endif;?></td>
					<td class="n"><?php if(userPrivilege('dashDetail')==1 && $value['tue']>0):?><a class="dashtrigger n" <?php echo "date='{$value['tuefilter']['day']}' program='{$value['tuefilter']['program']}' status='{$value['tuefilter']['status']}'"; ?>><?php echo $value['tue']?></a><?php else: echo $value['tue']; endif;?></td>
					<td class="n"><?php if(userPrivilege('dashDetail')==1 && $value['wed']>0):?><a class="dashtrigger n" <?php echo "date='{$value['wedfilter']['day']}' program='{$value['wedfilter']['program']}' status='{$value['wedfilter']['status']}'"; ?>><?php echo $value['wed']?></a><?php else: echo $value['wed']; endif;?></td>
					<td class="n"><?php if(userPrivilege('dashDetail')==1 && $value['thu']>0):?><a class="dashtrigger n" <?php echo "date='{$value['thufilter']['day']}' program='{$value['thufilter']['program']}' status='{$value['thufilter']['status']}'"; ?>><?php echo $value['thu']?></a><?php else: echo $value['thu']; endif;?></td>
					<td class="n"><?php if(userPrivilege('dashDetail')==1 && $value['fri']>0):?><a class="dashtrigger n" <?php echo "date='{$value['frifilter']['day']}' program='{$value['frifilter']['program']}' status='{$value['frifilter']['status']}'"; ?>><?php echo $value['fri']?></a><?php else: echo $value['fri']; endif;?></td>
					<td class="n ui-state-highlight"><?php if(userPrivilege('dashDetail')==1 && $value['total']>0):?><a class="dashtrigger n" <?php echo "lw='2' date='{$value['weektotalfilter']['weekfrom']}' program='{$value['weektotalfilter']['program']}' status='{$value['weektotalfilter']['status']}'"; ?>><?php echo $value['total']?></a><?php else: echo $value['total']; endif;?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr class="dashTotal">
					<th colspan="2"><b>Totals</b></th>
					<th class="l n"><?php echo $dailyTotals['prevWeekTot']?></th>
					<th class="n"><?php echo $dailyTotals['monTot']?></th>
					<th class="n"><?php echo $dailyTotals['tueTot']?></th>
					<th class="n"><?php echo $dailyTotals['wedTot']?></th>
					<th class="n"><?php echo $dailyTotals['thuTot']?></th>
					<th class="n"><?php echo $dailyTotals['friTot']?></th>
					<th class="n t"><?php echo $dailyTotals['allWeekTot']?></th>
				</tr>
			</tfoot>
		</table>
	</div>	
</div>
<script type="text/javascript">
$(document).ready(function(){

	$(".dashtrigger").bind("click",function(){
		var fdata = {
					lw : $(this).attr('lw'),
					day : $(this).attr('date'),
					program : $(this).attr('program'),
					status : $(this).attr('status'),
					ajax : 1
				};
			//alert(fdata['day'] + " | "+ fdata['program'] + " | "+ fdata['status']);
			
		myDialogBox('<?php echo site_url('dashboard/getDetails')?>',fdata,'dash_details','Details',{width : 'auto',resizable: true});	
			/*
		$("#dashboarddetailscontainer").empty().html("<img  style='margin:50px 0 30px 590px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		$.ajax({
			url : '<?php echo site_url('dashboard/getDetails')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){
				//alert(msg);
				$("#dashboarddetails_panel").html(msg);
				$("#dashboarddetailsholder").fadeIn('fast');
			},
			error : function(){
				//alert('g');
			}
		});
		*/
	});

	$("#sortTable").tablesorter({
		cssHeader : 'thHeader'
	}); 
});
</script>