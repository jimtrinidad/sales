		<table cellspacing="0" cellpadding="0" border="0" class="tableList">
			<thead>				
				<tr>
				<th width="30px">#</th><th><?php echo $showalldays==""?"Time":"Date/Time";?></th><th>Mode of Comm.</th><th>Contact Person</th><th>Company Name</th>
				<th>Telephone</th><th>Remark</th><th>Status</th><th></th>
				</tr>
			</thead>
			<tbody id="tb">
			<?php $i=1+$counter?>
			<?php foreach ($results as $value):?>
				<tr id="trover" rowid="<?php echo $value['did']?>" class="trdetails" <?php echo $value['latest']==0?"style='background:#dadadb'":""?>>
					<td style="font-size:11px;">
					<input type="hidden" value="<?php echo $value['did']?>"><?php echo $i;?> )</td>
					<td><?php echo date("M j, Y/g:i A",strtotime($value['time']));//$showalldays==""?date("g:i A",strtotime($value['time'])):date("M j/g:i A",strtotime($value['time'])) ?></td>
					<td><?php echo $value['eventType'] ?></td>					
					<td><?php echo $value['lastname'].", ".$value['firstname'];echo $value['mi']!=""?" ".$value['mi'].".":"" ?></td>
					<td><?php echo $value['companyName'] ?></td>
					<td><?php echo $value['telephone'] ?></td>
					<td><?php echo $value['remark'] ?></td>
					<td><?php echo $value['opportunityType'] ?></td>
						<td align="right" class="opt">
							<?php if(userPrivilege('canSendM')==1):?>
								<?php if(!empty($value['email'])):?>
								<a class="sendemail" id="<?php echo $value['did']?>" title="Send email"><img src="assets/images/icons/send_email_user.png"></a> &nbsp;
								<?php endif;?>
							<?php endif;?>
						</td>					
				</tr>
				<?php $i++; ?>
			<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5">
						<div id="resultpages"><?php echo $this->ajaxpagination->create_links()?></div>
					</th>
					<?php echo "<th style='text-align:right;border-left: 0;font-size: 9px;' colspan='4'>$msg</th>";?>
				</tr>
			</tfoot>
		</table>
	<input type="hidden" id="detailsupid" value="<?php echo $upid?>">
	<input type="hidden" id="detailsdateid" value="<?php echo $dateid?>">
	<input type="hidden" id="showalldays" value="<?php echo $showalldays?>">
	
<script type="text/javascript">
function getDetails(id){
	var fdata = {
			id:id,
			ajax:'1'
			};
	$("#moredetailspanel").empty().html("<div class='infoloader'><img  src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
	$.ajax({
		url : '<?php echo site_url('main/getDetails')?>',
		data : fdata,
		type : 'POST',
		success : function(msg){
			$("#moredetailspanel").html(msg);
		},
		error : function(){
			//alert(id);
		}
	});
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
	$("#resultpages #pagination span").click(function(){
		showMyLoader();
		$("#moredetailspanel").animate({"right": "-520px"}, "fast");
		$('#editevent_panel').hide();
		var fdata = {
				searchkeyD : $("#searchkeyD").val(),
				searchvalD : $("#searchvalD").val(),
				etypeD : $("#etypeD").val(),
				remarkD : $("#remarkD").val(),
				statusD : $("#opptypeD").val(),
				showalldays : $("#showalldays").val(),
					upid : $("#detailsupid").val(),
					dateid : $("#detailsdateid").val()
				};
		$.ajax({
			url : $(this).attr("href"),
			data : fdata,
			type : 'POST',
			success : function(res){
				$("#recordDetails").html(res);
				hideMyLoader();
			}
		});
	});	

	$(".trdetails").bind("click",function(){
		$('#editevent_panel #close').bind('click',function(e){
			$('#editevent_panel').fadeOut('fast');
			editevent=false;
		});
	});		
	
	var row = null;
	var moredetailspanel = false;
	$(".trdetails td:not(.opt)").bind("click",function(){
		var rowid = $(this).parent(".trdetails").attr('rowid');
		$('#editevent_panel').hide();
		if(row){
			if(moredetailspanel && rowid==row){
				$("#history_panel").animate({"left": "-430px"}, "fast");
				$("#moredetailspanel").animate({"right": "-520px"}, "fast");
				moredetailspanel = false;
			}else if(moredetailspanel){
				$("#history_panel").animate({"left": "-430px"}, "fast");
				$("#moredetailspanel").animate({"right": "-520px"}, "fast");
				$("#moredetailspanel").animate({"right": "0px"}, "fast");
				getDetails(rowid);
				moredetailspanel = true;
			}else{
				$("#copyevent_panel").fadeOut('fast');
				$("#right_panel").animate({"right": "-600px"}, "fast");
				$("#moredetailspanel").animate({"right": "0px"}, "fast");
				getDetails(rowid);
				moredetailspanel = true;				
			}
		}
		else
		{
			if(!moredetailspanel){
				$("#copyevent_panel").fadeOut('fast');
				$("#right_panel").animate({"right": "-600px"}, "fast");
				$("#moredetailspanel").animate({"right": "0px"}, "fast");
				getDetails(rowid);
				moredetailspanel = true;
			}
		}
		row = rowid;		
	});	

	$(".sendemail").bind("click",function(){
		var fdata = {
				detailsid : $(this).attr('id'),
				ajax : 1
				};
		$.ajax({
				url:'<?php echo site_url('main/emaileditor')?>',
				type: 'POST',
				data: fdata,
				success: function(msg){
						$("#emaileditor_panel").html(msg);
						$("#emaileditorholder").fadeIn('fast');
					}
			});
	});	

	var side_panel = false;
	$("#history").bind("click",function(){
		if(!side_panel){
			$("#history_panel").animate({"left": "0px"}, "fast");
			side_panel = true;
		}else{
			$("#history_panel").animate({"left": "-430px"}, "fast");
			side_panel = false;
		}
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

	$("#resultpages #pagination span,#resultpages #pagination strong").addClass('button').button();
	$("#resultpages #pagination strong").addClass('ui-state-disabled');
				
			
});
</script>