<style>
.chartBox{margin: 4px;position: relative;background: #ffffff;border: 1px solid #aaa;}
.chartButtonDiv{width: 150px;background: #FFFFFF;height: 20px;position: absolute;top: 0px;padding: 2px 0;}
.chartButtonDiv button{background: url('assets/images/arrow_out.png') no-repeat 1px 1px;border: 0;padding: 1px 2px 3px 18px;vertical-align: top;cursor: pointer;font-size: 12px;}
.chartButtonDiv button:HOVER {color: #BBBB00;}

</style>
<div class="ui-widget-content">
	<div class="ui-widget-header widget-title">
		Programs Bar Graph
		<span class="floatright" style="margin-top: -4px;margin-right: 5px;">
	 		<select id="graphTimeForm" style="font-size: 10px;">
	 			<option value="active" <?php echo $program_type == 'active' ? 'selected="selected"' : ''?>>Active</option>
	 			<option value="inactive" <?php echo $program_type == 'inactive' ? 'selected="selected"' : ''?>>Inactive</option>
	 			<option value="both" <?php echo $program_type == 'both' ? 'selected="selected"' : ''?>>Both</option>
	 		</select>
		</span>					
	</div>
	<div class="sidebar-content widget-content">
		<div class="chartBox">
			<div class="chartButtonDiv"></div>
			<?php echo $all_programs?>
		</div>			
			
		
			<div class="sidebar-content widget-content my-widget" style="padding-left: 4px;padding-right: 0;">
				<ul class="topList" style="margin: 0 auto;width: 1065px;">
					<?php foreach($programs as $p):?>
					<li style="width: 60px;height: 75px;">
						<div title="<?php echo $p['title']?>" align="center" id="<?php echo $p['id']?>" class="programDetails" style="cursor: pointer;">
							<div>
								<img class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.$p['logo']) ? base_url().'assets/photos/logo/'.$p['logo'] : base_url().'assets/photos/logo/blanklogo.png'?>">													
							</div>
							<div>
								<b style="font-size: 10px;"><?php echo $p['title']?></b>
							</div>
						</div>
					</li>
					<?php endforeach;?>
				</ul>
			</div>
			
			<div class="clearer"></div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){

	$(".chartButtonDiv button").bind("click",function(){
		var fdata = {
				chart : $(this).attr('id'),
				ajax : 1
				};
		$("#expandChartholder").fadeIn('fast');
		$("#expandChartcontainer").empty().html("<img  style='margin:40px 0 30px 435px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		$.ajax({
			url : '<?php echo site_url('charts/expand')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){
				$("#expandChart_panel").html(msg);
			},
			error : function(){
				//alert('g');
			}
		});
	});	

	$("#graphTimeForm").change(function(){
		window.location = "<?php echo site_url('charts/changeprogramtime')?>" + "/" + this.value;
	});

	$('.programDetails').click(function(){
		var fdata = {};
		myDialogBox('<?php echo site_url('charts/program_batch_bar')?>' + '/' + this.id,fdata,'program_details','Program Details',{width : '600',height : '410'});	
	});
	
});

function amGraphHide(chart_id, index, title)
{
	var arr = ["compareUsers","expandCompareUsers"];
	//alert(chart_id);
	if(index == 1 && $.inArray(chart_id,arr)<0){
		$("#chart_"+chart_id+"_flash").get(0).hideGraph(9);
	}

	if(index == 0 && $.inArray(chart_id,arr)>=0){
		$("#chart_"+chart_id+"_flash").get(0).hideGraph(1);//hide the guide on total target of all users
	}
	
}
function amGraphShow(chart_id, index, title)
{
	//alert(chart_id);
	var arr = ["compareUsers","expandCompareUsers"];
	if(index == 1 && $.inArray(chart_id,arr)<0){
		$("#chart_"+chart_id+"_flash").get(0).showGraph(9);
	}
	if(index == 0 && $.inArray(chart_id,arr)>=0){
		$("#chart_"+chart_id+"_flash").get(0).showGraph(1);//show the guide on total target of all users
	}
}
</script>