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
	$(".query #programType").change(function(){
		var fdata ={
				programType : $(".query #programType").val(),
				ajax : 1
				};
		$.ajax({
			url : '<?php echo site_url('reports/getProgramAjax')?>',
			data : fdata,
			type : 'POST',
			success : function(prog){
					$(".query #program").html(prog);
				}
		});
	});

	$("#reportsFilterForm").bind("submit",function(){
		var fdata = {
				user : $(".query #selectuser").val(),
				programType : $(".query #programType").val(),
				program : $(".query #program").val(),
				etype : $(".query #etype").val(),
				remark : $(".query #remark").val(),
				statusR : $(".query #opptype").val(),
				date : $(".query #date").val(),
				latest : $(".query input[name='latest']:checked").val(),
				qsearchkey : $(".query #searchkey").val(),
				qsearchval : $(".query #searchval").val(),
				orderby : $(".query #orderby").val(),
				ordertype : $(".query #ordertype").val(),
				ajax : '1'
				};
		//$(".report-results").empty().html("<img style='margin:50px 0 50px 519px	;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		showMyLoader();
		$.ajax({
			url : '<?php echo site_url('reports/filter')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){
				$(".report-results").html(msg);
				hideMyLoader();
			},
			error : function(){
			}
		});
		return false;
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
<div id="eventdetailsholder" style="top:80px;position: relative;" ><!-- CONTENT BOX FOR EVENT DETAILS  -->
	<div id="moredetailspanel" style="z-index:110;">
	</div>
</div>

<div id="historyholder" style="top:80px;position: relative;">
	<div id="history_panel">
		
	</div>
</div>

<div class='ui-widget-content'>
	<div class="ui-widget-header widget-title" id="resulttab">Queries</div>
	<div class="sidebar-content widget-content" id="querydiv">
		<div class="query">
			<form id="reportsFilterForm" class="query">
				<span style="margin-left: 29px;">
					<select id="programType" style="width:135px;">
						<option value="active" <?php echo $filters['programType']=="active"?"selected='selected'":""?>>Active Program</option>
						<option value="inactive" <?php echo $filters['programType']=="inactive"?"selected='selected'":""?>>Inactive Program</option>
						<option value="both" <?php echo $filters['programType']=="both"?"selected='selected'":""?>>Both Programs</option>
					</select>
					<input style="position: relative;top: 2px;" type="checkbox" id="latest" name="latest" value="1" <?php echo $filters['latest']=="1"?"checked='checked'":""?>><label for="latest">Show follow ups</label>
				</span><br>
				<span><label for="selectuser">User: </label><select id="selectuser" style="width:145px;">
														<option value="" <?php echo $filters['user']==""?"selected='selected'":""?>>All</option>
														<optgroup label="Active users">
															<?php foreach ($activeusers as $user):?>
															<option value="<?php echo $user['id']?>"  <?php echo $filters['user']==$user['id']?"selected='selected'":""?>><?php echo $user['name']?></option>
															<?php endforeach;?>
														</optgroup>
														<optgroup label="Inctive users">
															<?php foreach ($inactiveusers as $user):?>
															<option value="<?php echo $user['id']?>"  <?php echo $filters['user']==$user['id']?"selected='selected'":""?>><?php echo $user['name']?></option>
															<?php endforeach;?>
														</optgroup>
													</select>
					<label for="program">Program: </label><select id="program" style="width:85px;">
														<?php echo $programs?>													
													</select>
					<label for="etype">Mode of Comm: </label><select id="etype" style="max-width:110px;">
														<option value="" <?php echo $filters['etype']==""?"selected='selected'":""?>>Any</option>
														<?php foreach ($events as $k=>$v):?>
														<option value="<?php echo $k?>" <?php echo $filters['etype']==$k?"selected='selected'":""?>><?php echo $v?></option>
														<?php endforeach;?>	
													</select>													
					<label for="remark">Remark: </label><select id="remark">
														<option value="" <?php echo $filters['remark']==""?"selected='selected'":""?>>Any</option>
														<option value="Opportunity" <?php echo $filters['remark']=="Opportunity"?"selected='selected'":""?>>Opportunity</option>
														<option value="Rejected" <?php echo $filters['remark']=="Rejected"?"selected='selected'":""?>>Rejected</option>
													</select>
					<label for="opptype">Status: </label><select id="opptype">
														<option value="" <?php echo $filters['statusR']==""?"selected='selected'":""?>>Any</option>
														<option value="Won" <?php echo $filters['statusR']=="Won"?"selected='selected'":""?>>Won</option>
														<option value="Loss" <?php echo $filters['statusR']=="Loss"?"selected='selected'":""?>>Loss</option>
														<option value="Pending" <?php echo $filters['statusR']=="Pending"?"selected='selected'":""?>>Pending</option>
													</select>
					<label for="date">Date: </label><input type="text" class="date" id="date" style="width:110px;" value="<?php echo $filters['date']?>">
					<br><label for="searchval" style="margin-left: 4px;">Contact Person: </label><select id="searchkey">
														<option value="name" <?php echo $filters['qsearchkey']=="name"?"selected='selected'":""?>>Name</option>
														<option value="email" <?php echo $filters['qsearchkey']=="email"?"selected='selected'":""?>>Email</option>
														<option value="companyName" <?php echo $filters['qsearchkey']=="companyName"?"selected='selected'":""?>>Company</option>
														<option value="position" <?php echo $filters['qsearchkey']=="position"?"selected='selected'":""?>>Position</option>
													</select>
													<input id="searchval" style="width:200px;" value="<?php echo $filters['qsearchval']?>">
					<label for="orderby" style="margin-left: 4px;">Order By: </label><select id="orderby">
														<option value="d.time" <?php echo $filters['orderby']=="d.time"?"selected='selected'":""?>>Time</option>
														<option value="i.lastname" <?php echo $filters['orderby']=="i.lastname"?"selected='selected'":""?>>Lastname</option>
														<option value="i.firstname" <?php echo $filters['orderby']=="i.firstname"?"selected='selected'":""?>>Firstname</option>
													</select>	
													<select id="ordertype">
														<option value="DESC" <?php echo $filters['ordertype']=="DESC"?"selected='selected'":""?>>Descending</option>
														<option value="ASC" <?php echo $filters['ordertype']=="ASC"?"selected='selected'":""?>>Ascending</option>
													</select>																			
					<button type="submit" class="trigger button button_img"><img src="assets/images/icons/find.png">Go</button>																													
				</span>
			</form>									
		</div>
	</div>
</div>
	
<div class='ui-widget-content'>
	<div class="ui-widget-header widget-title" id="resulttab">
		Results
		<span class="floatright" style="margin-right: 5px;"> <a href="<?php echo site_url('reports/excel')?>"><b>Download Excel</b></a>	</span>
	</div>
	<div class="list sidebar-content widget-content" id="resultdiv">
		<div class="report-results" style="min-height: 300px;">
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
						<td><?php echo date("M j, Y / g:i A",strtotime($value['time']))?></td>
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
		</div>
	</div>
</div>
<!-- 
	<script type="text/javascript">
		
		OFC = {};
		
		OFC.jquery = {
		    name: "jQuery",
		    version: function(src) { return $('#'+ src)[0].get_version(); },
		    rasterize: function (src, dst) { $('#'+ dst).replaceWith(OFC.jquery.image(src)); },
		    image: function(src) { return "<img src='data:image/png;base64," + $('#'+src)[0].get_img_binary() + "' />"; },
		    popup: function(src) {
		        var img_win = window.open('', 'Charts: Export as Image');
		        with(img_win.document) {
		            write('<html><head><title>Charts: Export as Image<\/title><\/head><body>' + OFC.jquery.image(src) + '<\/body><\/html>'); }
				// stop the 'loading...' message
				img_win.document.close();
		     }
		};
	</script> 	
<script type="text/javascript">
$(document).ready(function(){	

	var all = true;
	$("#gograph").click(function(){
		$("#graphtype").change();
	});
	$("#graphtype").change(function(){
		if($("#graphtype").val()=="graph"){
			$("#ifuserper").removeClass('hidden');
			$("#ifmonthly").addClass('hidden');
			$("#classifhold").addClass('hidden');
			$("#ifrange").addClass('hidden');
			$("#programhideall").removeClass('hidden');
			if(!all){
				$("#programhideall").attr("selected","selected");
				all = true;
			}
		}else if($("#graphtype").val()=="monthly"){
			$("#ifmonthly").removeClass('hidden');
			$("#ifuserper").addClass('hidden');
			$("#ifrange").addClass('hidden');
			$("#classifhold").removeClass('hidden');
			$("#programhideall").removeClass('hidden');
			if(!all){
				$("#programhideall").attr("selected","selected");
				all = true;
			}
		}else if($("#graphtype").val()=="perprogram"){
			$("#ifmonthly").addClass('hidden');
			$("#ifuserper").addClass('hidden');
			$("#ifrange").addClass('hidden');
			$("#classifhold").removeClass('hidden');
			if($("#graphprogram").val()=="all"){
				$(".1").attr("selected","selected");
				all = false;
			}
			$("#programhideall").addClass('hidden');
		}else if($("#graphtype").val()=="rangedate"){
			$("#ifrange").removeClass('hidden');
			$("#ifmonthly").addClass('hidden');
			$("#ifuserper").addClass('hidden');	
			$("#classifhold").removeClass('hidden');
			$("#programhideall").removeClass('hidden');
			if(!all){
				$("#programhideall").attr("selected","selected");
				all = true;
			}
		}
		
		var form_data = {
				graphtype : $("#graphtype").val(),
				user : $("#graphuser").val(),
				program : $("#graphprogram").val(),
				month : $("#month").val(),
				year : $("#year").val(),
				classification : $("#classification").val(),
				startdate : $("#startdate").val(),
				enddate : $("#enddate").val(),
				ajax : 1
			};

		$.ajax({
			url : '<?php echo site_url('reports/opengraph')?>',
			type : 'POST',
			data : form_data,
			success : function(result){
					$("#graphdiv").html(result);
				},
			error : function(){
					//alert("sad");
				}
			});
	});	
});
</script>
    <script type="text/javascript">
            swfobject.embedSWF(
              "assets/swf/open-flash-chart.swf", "graphresult",
              "<?= $chart_width ?>", "<?= $chart_height ?>",
              "9.0.0", "expressInstall.swf",
              {"data-file":"<?= urlencode($data_url) ?>","loading":"Loading... Please wait.."},{"wmode":"transparent"}
            );
            
            function save_image() { OFC.jquery.popup('graphresult'); }
    </script> 	
	<div class=borders style="z-index: 1;">
		<div class="header" id="graphtab">Graph 
		<select style="float:right;" id="graphtype">
			<option value="graph">Users Performance</option>
			<option value="perprogram">Program Graph</option>
			<option value="monthly">Monthly Report</option>
			<option value="rangedate">Date Range</option>
		</select>
		<div class="clearer"></div></div>
		<div class="list" id="graphdiv">		
			<div id="graphresult"></div>
		</div>
		<div class="list">
			<span id="ifuserper">
				<span style="margin:2px 5px;padding:2px 10px;background: #63be3f;color:#fff">Won</span>	
				<span style="margin:2px 5px;padding:2px 10px;background: #3f84be;color:#fff">Pending</span>	
				<span style="margin:2px 5px;padding:2px 10px;background: #bda11d;color:#fff">Loss</span>	
				<span style="margin:2px 5px;padding:2px 10px;background: #bd1d1d;color:#fff">Rejected</span>			
				<span>
					<label for="graphuser">User: </label>
						<select id="graphuser">
							<option value="all">All</option>
							<?php foreach ($users as $user):?>
							<option value="<?php echo $user['id']?>"><?php echo $user['name']?></option>
							<?php endforeach;?>
						</select>																																		
				</span>	
			</span>
					<label for="graphprogram">Program: </label>
						<select id="graphprogram">

							 <?php echo $programsGraph?>														
						</select>
					<span id="classifhold" class="hidden">
						<label for="classification">Classification: </label>
							<select id="classification">
								<option value="Won" style="color: #63be3f;">Won</option>
								<option value="Pending" style="color: #3f84be;">Pending</option>
								<option value="Loss" style="color: #bda11d;">Loss</option>
								<option value="Rejected" style="color: #bd1d1d;">Rejected</option>														
								<option value="IC" style="color: #b418c7;">Incoming Calls</option>
								<option value="IM" style="color: #14413d;">Incoming Mails</option>														
								<option value="OC" style="color: #27d073;">Outgoing Calls</option>	
								<option value="OM" style="color: #aed027;">Outgoing Mails</option>												
							</select>
					</span>																		
			<span id="ifmonthly" class="hidden">
				<span>			
					<label for=month>Month: </label><select id="month">
														<?php foreach ($month as $k=>$v):?>
														<option value="<?php echo $k?>" <?php if(date('n',strtotime(NOW))==$k):echo "selected='selected'";endif;?>><?php echo $v?></option>
														<?php endforeach;?>
													</select>
					<label for="year">Year: </label><select id="year">
														<?php for($i=2011;$i<=2020;$i++):?>
														<option value="<?php echo $i?>" <?php if(date('Y',strtotime(NOW))==$i):echo "selected='selected'";endif;?>><?php echo $i ?></option>
														<?php endfor;?>														
													</select>
				</span>				
			</span>	
			<span id="ifrange" class="hidden">
				<span>
					Start Date: <input type="text" class="date" id="startdate" value="<?php echo date("F j, Y",mktime(1,0,0,date("n",strtotime(NOW)),1,date("Y",strtotime(NOW))))?>">
					End Date: <input type="text" class="date" id="enddate" value="<?php echo date("F j, Y",mktime(1,0,0,date("n",strtotime(NOW)),date("t",strtotime(NOW)),date("Y",strtotime(NOW))))?>">
				</span>
			</span>	
			<button id="gograph">Go</button>		
		</div>
	</div>
	 -->	