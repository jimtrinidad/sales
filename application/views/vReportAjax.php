<script type="text/javascript">
function getDetails(id){
	var fdata = {
			id:id,
			ajax:'1'
			};
	$("#moredetailspanel").empty().html("<div class='infoloader ui-widget-content'><img  src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
	$.ajax({
		url : '<?php echo site_url('reports/getDetails')?>',
		data : fdata,
		type : 'POST',
		success : function(msg){
			$("#moredetailspanel").html(msg);
		},
		error : function(){
			//alert(id);
		}
	});
	var fdata = {
			id:id,
			showall : 1,
			ajax:'1'
			};
	$("#history_panel").empty();
	$.ajax({
		url : '<?php echo site_url('main/getHistory')?>',
		data : fdata,
		type : 'POST',
		success : function(msg){
			$("#history_panel").html(msg);
		},
		error : function(){
			//alert(id);
		}
	});		
}

$(document).ready(function(){
	$(".date").datepicker({
		showWeek: true,
		dateFormat:"MM d, yy",
		firstDay: 1
	});

	var row = null;
	var moredetailspanel = false;
	$(".trdetails").bind("click",function(){
		if(row){
			if(moredetailspanel && $(this).attr('rowid')==row){
				$("#history_panel").animate({"left": "-430px"}, "fast");
				$("#moredetailspanel").animate({"right": "-520px"}, "fast");
				moredetailspanel = false;
			}else if(moredetailspanel){
				$("#history_panel").animate({"left": "-430px"}, "fast");
				$("#moredetailspanel").animate({"right": "-520px"}, "fast");
				$("#moredetailspanel").animate({"right": "0px"}, "fast");
				getDetails($(this).attr('rowid'));
				moredetailspanel = true;
			}else{
				$("#moredetailspanel").animate({"right": "0px"}, "fast");
				getDetails($(this).attr('rowid'));
				moredetailspanel = true;				
			}
		}
		else
		{
			if(!moredetailspanel){
				$("#moredetailspanel").animate({"right": "0px"}, "fast");
				getDetails($(this).attr('rowid'));
				moredetailspanel = true;
			}
		}
		row = $(this).attr('rowid');		
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

	$("#report_pages #pagination a,#report_pages #pagination strong").addClass('button');

	$('.button').button();
	
	$("#report_pages #pagination strong").addClass('ui-state-disabled');	
		
});
</script>
<table cellpadding="0" cellspacing="0" border="0" class="tableList" width="100%">
	<thead>
		<tr>
			<th width="30px">#</th><th>Date/Time</th><th>Prog</th><th>Mode of Comm.</th><th>Remark</th><th>Status</th><th>Contact Person</th><th>Company Name</th><th>Position</th><th>Representative</th>
		</tr>
	</thead>
	<tbody id="tb">	
	<?php $i=1+$counter;?>
	<?php foreach ($results->result_array() as $value):?>
		<tr id="trover" rowid="<?php echo $value['did']?>" class="trdetails">
			<td style="font-size:11px;"><?php echo $i;?> )</td>
			<td><?php echo date("M d, Y / g:i A",strtotime($value['time']))?></td>
			<td><?php echo $value['program']?></td>
			<td><?php echo $value['eventType']?></td>
			<td><?php echo $value['remark']?></td>
			<td><?php echo $value['opportunityType']?></td>
			<td><?php echo $value['lastname'].", ".$value['firstname'] ?></td>
			<td><?php echo $value['companyName']?></td>
            <td><?php echo $value['position']?></td>
			<td><?php echo $value['user']?></td>
		</tr>
		<?php $i++; ?>
	<?php endforeach;?>
	</tbody>
	<tfoot>		
		<tr>
			<th class="cols" colspan="9">
				<div id="report_pages"><?php echo $this->pagination->create_links()?></div>
			</th>
			<th align="right" style='font-size: 9px;'><?php echo $msg?></th>
		</tr>				
	</tfoot>					
</table>