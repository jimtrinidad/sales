<div class="ui-widget-content list" id="tbdates">
	<table cellspacing="0" cellpadding="0" border="0" class="tableList" width="100%">
	<thead>
		<tr>
		<th width="30px">#</th><th>Dates</th>
		</tr>
	</thead>
	<tbody>
		<?php $i=1+$counter;?>
		<?php foreach ($dates->result_array() as $value):?>
			<tr id="trover">
				<td style="font-size:11px;" width="20px"><?php echo $i;?> )</td>
				<td class="datetrigger" id="<?php echo $value['did']?>"><?php echo date("l, F j, Y",strtotime($value['date']))?></td>
			</tr>
			<?php $i++; ?>
		<?php endforeach;?>
		<tr>
			<th colspan="2" style="border-bottom: 0;padding: 0;"><div style="float: left;margin-top: 2px;" id="datepages"><?php echo $this->ajaxpagination->create_links()?></div>
				<button class="button" style="float: right;margin-top: 3px;" id="showalldays_trigger">Show All</button>
				<div class="clearer"></div>
			</th>
		</tr>	
	</tbody>
	</table>
</div>
<input type="hidden" id="upid" value="<?php echo $upid ?>">
<script type="text/javascript">
$(document).ready(function(){
	$(".datetrigger").click(function(){
		showMyLoader();
		var loc = "<?php echo site_url('main/program');?>";
		var dateID = $(this).attr('id');
		var f_data = {
					id:$("#upid").val(),
					dateID:dateID,
					ajax:1
				};
		$.ajax({
			url:loc,
			data:f_data,
			type:'POST',
			success:function(msg){
					$("#main-content").html(msg);	
					hideMyLoader();
					var sdata = {
							dateid : $("#detailsdateid").val(),
							ajax : '1'
							};
					$("#summary_panel #summary").empty();
					$.ajax({//for summary
						url:"<?php echo site_url('main/getDailySummary');?>",
						data: sdata,
						type:'POST',
						success:function(summsg){
							$("#summary_panel #summary").html(summsg);
							$("#summaryholder").removeClass("hidden");
						},
						error:function(){
							//alert('grrr');
						}
					});	
					var cdata = {
							programtempid : $("#programTempID").val(),
							userid : $("#userid").val(),
							ajax : '1'
							};
					$("#tbinfos").empty().html("<div class='infoloader' style='width:560px;'><img style='left:245px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
					$.ajax({//for list of old records for copy
						url:"<?php echo site_url('main/getoldrecords');?>",
						data: cdata,
						type:'POST',
						success:function(oldrec){
							$("#right_panel #rightcontent").html(oldrec);
							$("#rightpanelholder").removeClass("hidden");
						},
						error:function(msg){
							//alert(msg);
						}
					});
			},
			error:function(){
					//alert('huhu');
			}
		});	
	});
	
	$("#showalldays_trigger").click(function(){
		showMyLoader();
		var loc = "<?php echo site_url('main/program');?>";
		var f_data = {
					id:$("#upid").val(),
					showalldays:1,
					ajax:1
				};
		$.ajax({
			url:loc,
			data:f_data,
			type:'POST',
			success:function(msg){
					$("#main-content").html(msg);
					hideMyLoader();	
					$("#summaryholder").addClass("hidden");	
					var cdata = {
							programtempid : $("#programTempID").val(),
							userid : $("#userid").val(),
							ajax : '1'
							};
					$("#tbinfos").empty().html("<div class='infoloader' style='width:560px;'><img style='left:245px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
					$.ajax({//for list of old records for copy
						url:"<?php echo site_url('main/getoldrecords');?>",
						data: cdata,
						type:'POST',
						success:function(oldrec){
							$("#right_panel #rightcontent").html(oldrec);
							$("#rightpanelholder").removeClass("hidden");
						},
						error:function(msg){
							//alert(msg);
						}
					});	
			},
			error:function(){
					//alert('huhu');
			}
		});	
	});			

	$("#datepages #pagination span").click(function(){
		$.ajax({
			url : $(this).attr("href"),
			type : 'GET',
			success : function(res){
				$("#tbdates").html(res);
			}
		});
	});	

	$("#datepages #pagination span").addClass('button');
	$('.button').button();

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