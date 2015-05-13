		<table cellspacing="0" cellpadding="0" border="0" class="tableList" width="100%">
			<thead>
				<tr>
				<th width="15px">#</th><th>Name</th><th>Status</th><th colspan="2">Company</th>
				</tr>
			</thead>
			<tbody>
			<?php $i=1+$counter;?>
			<?php foreach ($results->result_array() as $value):?>
				<tr id="<?php echo $value['infoid']?>">
					<td style="font-size:11px;" width="20px"><?php echo $i;?> )</td>
					<td class="info"><?php echo $value['name']?></td>
					<td><?php echo $value['status']?></td>
					<td><?php echo $value['companyName']?></td>
					<td align="right">
						<a id="<?php echo $value['did']?>" class="history">Details</a>&nbsp;&nbsp;<?php if(my_session_value('showalldays')==""):?><a id="<?php echo $value['infoid']?>" class="copyinfo">Copy</a><?php endif;?>
					</td>
				</tr>
				<?php $i++; ?>
			<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5" style="border-bottom: 0" valign="middle">
						<div style="float: left;" id="infopages"><?php echo $this->ajaxpagination->create_links()?></div>
						<span style="float: right;font-weight: bold;font-size: 9px;padding-top: 3px;"><?php echo $msg?></span>
					</th>
				</tr>
			</tfoot>
		</table>
	
<script type="text/javascript">

function getHistory(id){
	var fdata = {
			id:id,
			showall : 1,
			ajax:'1'
			};
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
	$("#infopages #pagination span").click(function(){
		$("#tbinfos").empty().html("<div class='infoloader' style='width:560px;'><img style='left:245px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
		$.ajax({
			url : $(this).attr("href"),
			data : ajax = '1',
			type : 'GET',
			success : function(res){
				$("#tbinfos").html(res);
			}
		});
	});	

	$(".copyinfo").bind("click",function(){
		var fdata = {id:this.id, ajax:'1'};
		ajaxCallBoxOpen('<?php echo site_url('main/getOldRecForEditor')?>',fdata);	
	});
	
	var history = false;
	var hisRow = null;
	$(".history").bind("click",function(){
		/*
		if(hisRow){
			if(history && $(this).attr('id')==hisRow){
				$("#history_panel").animate({"left": "-430px"}, "fast");
				history = false;
			}else if(history){
				$("#history_panel").animate({"left": "-430px"}, "fast");
				getHistory($(this).attr('id'));
				$("#history_panel").animate({"left": "0px"}, "fast");
				history = true;
			}else{
				$("#history_panel").animate({"left": "0px"}, "fast");
				getHistory($(this).attr('id'));
				history = true;				
			}
		}
		else
			*/
		{
			//if(!history){
				$("#history_panel").animate({"left": "0px"}, "fast");
				//getHistory($(this).attr('id'));
			//	history = true;
			//}
		}
		hisRow = $(this).attr('id');
		getHistory($(this).attr('id'));
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

	$("#infopages #pagination span,#infopages #pagination strong").addClass('button');
	$('.button').button();
	$("#infopages #pagination strong").addClass('ui-state-disabled');	
	
});
</script>		