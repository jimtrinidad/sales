<style>
.chartBox{width: 550px;float: left;margin: 4px;position: relative;background: #ffffff;border: 1px solid #aaa;}
.chartButtonDiv{width: 150px;background: #FFFFFF;height: 20px;position: absolute;top: 0px;padding: 2px 0;}
.chartButtonDiv button{background: url('assets/images/arrow_out.png') no-repeat 1px 1px;border: 0;padding: 1px 2px 3px 18px;vertical-align: top;cursor: pointer;font-size: 12px;}
.chartButtonDiv button:HOVER {color: #BBBB00;}

</style>
<div class="membercon">	
	<div class=borders>
		<div class="header" id="resulttab" style="margin-bottom: 5px;">Charts</div>
		
		<div class="chartBox">
			<div class="chartButtonDiv"><button id="alldate">Expand</button></div>
			<?php echo $alldate?>
		</div>	

		<div class="chartBox">
			<div class="chartButtonDiv"><button id="dailyDates">Expand</button></div>
			<?php echo $dailyDates?>
		</div>

		<div class="chartBox">
			<div class="chartButtonDiv"><button id="compareUsers">Expand</button></div>
			<?php echo $compareUsers?>
		</div>
		
		<div class="chartBox">
			<div class="chartButtonDiv"><button id="summary">Expand</button></div>
			<?php echo $summary?>
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