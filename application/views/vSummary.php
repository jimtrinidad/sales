<script type="text/javascript">
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
				remark : $(".query #remark").val(),
				statusR : $(".query #opptype").val(),
				qsearchkey : $(".query #searchkey").val(),
				qsearchval : $(".query #searchval").val(),
				orderby : $(".query #orderby").val(),
				ordertype : $(".query #ordertype").val(),
				ajax : '1'
				};
		//$(".report-results").empty().html("<img style='margin:50px 0 50px 519px	;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		showMyLoader();
		$.ajax({
			url : '<?php echo site_url('reports/filterSummary')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){
				location.reload();
			},
			error : function(){
			}
		});
		return false;
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

	$(".accordionmember").accordion({
		active: false,
		collapsible: true,
		autoHeight: false
	}).removeClass('notvisible');

	$("#summury_pages #pagination a,#summury_pages #pagination strong").addClass('button');

	$('.button').button();
	
	$("#summury_pages #pagination strong").addClass('ui-state-disabled');
});
</script>

<div class=ui-widget-content>
	<div class="ui-widget-header widget-title">Queries</div>
	<div class="sidebar-content widget-content" id="querydiv">
		<form id="reportsFilterForm" class="query">
			<span>					
				<label for="searchval" style="margin-left: 4px;">Group by: </label><select id="searchkey">
													<option value="name" <?php echo $filters['qsearchkey']=="name"?"selected='selected'":""?>>Name</option>
													<option value="email" <?php echo $filters['qsearchkey']=="email"?"selected='selected'":""?>>Email</option>
													<option value="companyName" <?php echo $filters['qsearchkey']=="companyName"?"selected='selected'":""?>>Company</option>
												</select>
												<input id="searchval" style="width:200px;" value="<?php echo $filters['qsearchval']?>">
				<label for="orderby" style="margin-left: 4px;">Order By: </label><select id="orderby">
													<option value="total" <?php echo $filters['orderby']=="total"?"selected='selected'":""?>>Total</option>
													<option value="key" <?php echo $filters['orderby']=="key"?"selected='selected'":""?>>Group</option>															
												</select>	
												<select id="ordertype">															
													<option value="ASC" <?php echo $filters['ordertype']=="ASC"?"selected='selected'":""?>>Ascending</option>
													<option value="DESC" <?php echo $filters['ordertype']=="DESC"?"selected='selected'":""?>>Descending</option>
												</select>
				<label for="selectuser">User: </label><select id="selectuser" style="width:145px;">
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
				<label for="remark">Remark: </label><select id="remark">
													<option value="" <?php echo $filters['remark']==""?"selected='selected'":""?>>Any</option>
													<option value="Opportunity" <?php echo $filters['remark']=="Opportunity"?"selected='selected'":""?>>Opportunity</option>
													<option value="Rejected" <?php echo $filters['remark']=="Rejected"?"selected='selected'":""?>>Rejected</option>
												</select>
				<label for="opptype">Status: </label><select id="opptype">															
													<option value="Won" <?php echo $filters['statusR']=="Won"?"selected='selected'":""?>>Won</option>
													<option value="Loss" <?php echo $filters['statusR']=="Loss"?"selected='selected'":""?>>Loss</option>
													<option value="Pending" <?php echo $filters['statusR']=="Pending"?"selected='selected'":""?>>Pending</option>
												</select>																																		
				<button type="submit" class="trigger button button_img"><img src="assets/images/icons/find.png">Go</button>																													
			</span>
		</form>									
	</div>
</div>

<div class=ui-widget-content>
	<div class="ui-widget-header widget-title">Results</div>
	<div class="sidebar-content widget-content" id="resultdiv" style="min-height: 300px;">
		<div class="accordionDiv accordionmember members-list notvisible">
		<?php 
			$i=1 + $counter; //start is the current offset
			foreach ($results as $value):
		?>						
				<h3>
					<a class="user-name" id="mlist_<?php echo $value['did']?>">
						<span style="font-weight: normal;margin: 0 5px;"><?php echo $i?>)</span> 								
					 	<span class="name">
					 		<?php 
					 			switch($filters['qsearchkey'])
					 			{
					 				case 'email':
					 					echo !empty($value['email']) ? $value['email'] : 'not available';break;
					 				case 'companyName':
					 					echo !empty($value['companyName']) ? $value['companyName'] : 'not available';break;
					 				default:
					 					echo $value['lastname'].", ".$value['firstname'];
					 			}
					 		?>
					 	</span>
					 	<span class="floatright fadeTextSmall" style="padding: 0;"><?php echo $value['total']?> records</span>
					</a>
				</h3>
				<div>
					<table class="tableList transactionTable" width="100%" cellpadding="0" cellspacing="0" style="border: 0;-moz-box-shadow: none;-webkit-box-shadow: none;box-shadow: none;">
						<thead>
							<tr>
								<th width="30px">#</th><th>Date/Time</th><th>Prog</th><th>Status</th><th>Contact Person</th><th>Company Name</th><th>Representative</th>
							</tr>
						</thead>
						<tbody id="tb">	
						<?php $c=1;?>
						<?php foreach ($value['details'] as $d):?>
							<tr id="trover">
								<td style="font-size:11px;"><?php echo $c;?> )</td>
								<td><?php echo date("M j, Y / g:i A",strtotime($d['time']))?></td>
								<td><?php echo $d['program'].' '.$d['batch']?></td>
								<td><?php echo $d['opportunityType']?></td>
								<td><?php echo $d['lastname'].", ".$d['firstname'] ?></td>
								<td><?php echo $d['companyName']?></td>
								<td><?php echo $d['user']?></td>
							</tr>
							<?php $c++; ?>
						<?php endforeach;?>
						</tbody>					
					</table>					
				</div>
			<?php $i++; endforeach;?>
			<?php if(count($results)==0):?>
				<div class="no-record-notice-div" style="padding: 5px 0;margin: 5px 0;float: left;">
					<span style="margin: 10px;font-size: 14px;">No records found...</span>
				</div>										
			<?php endif;?>
		</div>			

		<table class="tableList" width="100%">
			<thead>
				<tr>
					<th align="left">
						<div id="summury_pages"><?php echo $this->pagination->create_links()?></div>	
					</th>
					<th align="right">
							<b style="margin-right: 5px;"><?php echo $msg?></b>	
					</th>
				</tr>
			</thead>
		</table>
		<div class="clearer"></div>		
	</div>
</div>
