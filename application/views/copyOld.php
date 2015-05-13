<script type="text/javascript">

function getHistory(id){
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

	$(".copyinfo").bind("click",function(){
		var fdata = {id:this.id, ajax:'1'};
		ajaxCallBoxOpen('<?php echo site_url('main/getOldRecForEditor')?>',fdata);	
	});

	
	var hisRow = null;
	$(".history").bind("click",function(){
		$("#history_panel").animate({"left": "0px"}, "fast");
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
			
});
</script>


<script type="text/javascript">
$(document).ready(function(){

	$("#archiveFilterForm").bind("submit",function(){
		var sdata = {
				searchkey : $("#searchkey").val(),
				searchval : $("#searchval").val(),
				searchprog : $("#searchprog").val(),
				searchstatus : $("#searchstatus").val(),
				userid : $("#userid").val(),
				programtempid : $("#programtempid").val(),
				ajax : 1
			};
		$("#tbinfos").empty().html("<div class='infoloader' style='width:560px;'><img style='left:245px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
		$.ajax({
			url : "<?php echo site_url('main/ajaxoldrecords')?>",
			data : sdata,
			type : 'POST',
			success : function(sres){
				$("#tbinfos").html(sres);
			}
		});	
		return false;	
	});	
		
	$("#infopages #pagination span").click(function(){
		$("#tbinfos").empty().html("<div class='infoloader' style='width:560px;'><img style='left:245px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
		$.ajax({
			url : $(this).attr("href"),
			type : 'GET',
			success : function(res){
				$("#tbinfos").html(res);
			}
		});
	});	

	$("#infopages #pagination span,#infopages #pagination strong").addClass('button');
	$('.button').button();
	$("#infopages #pagination strong").addClass('ui-state-disabled');		

});
</script>	
<div class="list ui-widget-content" style="padding-top:2px;">
<form id="archiveFilterForm">
	<div style="padding:4px 4px 0 6px;">
		<select id="searchkey" style="width: 90px;">
			<option value="lastname" <?php echo $filter['searchkey']=="lastname"?"selected='selected'":""?>>Lastname</option>
			<option value="firstname" <?php echo $filter['searchkey']=="firstname"?"selected='selected'":""?>>Firstname</option>
			<option value="companyName" <?php echo $filter['searchkey']=="companyName"?"selected='selected'":""?>>Company</option>
		</select>
		<input type="text" id="searchval" style="width: 150px;"  value="<?php echo $filter['searchval']?>">
		<select id="searchprog" style="width: 100px">
			<option value="" <?php echo $filter['searchprog']==""?"selected='selected'":""?>>Programs</option>
			<?php foreach ($programs as $program):?>
			<option value="<?php echo $program['pid']?>" <?php echo $filter['searchprog']==$program['pid']?"selected='selected'":""?>><?php echo $program['program']?></option>
			<?php endforeach;?>
		</select>
		<select id="searchstatus" style="width: 80px;">
			<option value="all" <?php echo $filter['searchstatus']=="all"?"selected='selected'":""?>>Status</option>
			<option value="Won" <?php echo $filter['searchstatus']=="Won"?"selected='selected'":""?>>Won</option>
			<option value="Pending" <?php echo $filter['searchstatus']=="Pending"?"selected='selected'":""?>>Pending</option>
			<option value="Loss" <?php echo $filter['searchstatus']=="Loss"?"selected='selected'":""?>>Loss</option>
			<option value="Rejected" <?php echo $filter['searchstatus']=="Rejected"?"selected='selected'":""?>>Rejected</option>
		</select>		
		<button class="button button_img" type="submit" id="gosearch"><img src="assets/images/icons/find.png">Go</button>
	</div>
</form>
	<div id="tbinfos" >
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
						<a id="<?php echo $value['did']?>" class="history">Details</a>&nbsp;&nbsp;
						<?php if(my_session_value('showalldays')==""):?><a id="<?php echo $value['infoid']?>" class="copyinfo">Copy</a><?php endif;?>
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
	</div>
</div>
<input type="hidden" value="<?php echo $userid?>" id="userid">
<input type="hidden" value="<?php echo $programtempid?>" id="programtempid">