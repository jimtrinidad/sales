<script type="text/javascript">
$(document).ready(function(){
	
	//var bot = 30-parseInt($("#bottom_panel").css('height')) + 'px';
	//$("#bottom_panel").css('bottom',bot);
	
	var side_panel = false;
	$(".imgdiv").bind("click",function(){
		if(!side_panel){
			$("#side_panel").animate({"left": "0px"}, "fast");
			side_panel = true;
		}else{
			$("#side_panel").animate({"left": "-241px"}, "fast");
			side_panel = false;
		}
	});

	var right_panel = false;
	$("#righthandle").bind("click",function(){
		if(!right_panel){
			$("#right_panel").animate({"right": "0px"}, "fast");
			$("#history_panel").animate({"left": "-430px"}, "fast");
			$("#moredetailspanel").animate({"right": "-520px"}, "fast");
			right_panel = true;
		}else{
			$("#right_panel").animate({"right": "-600px"}, "fast");
			$("#history_panel").animate({"left": "-430px"}, "fast");
			$("#copyevent_panel").fadeOut('fast');
			right_panel = false;
		}
	});	

	var bottom_panel = false;
	$(".bottomimgdiv").bind("click",function(){
		if(!bottom_panel){
			$("#bottom_panel").animate({"bottom": "0px"}, "fast");
			bottom_panel = true;
		}else{
			$("#bottom_panel").animate({"bottom": "-208px"}, "fast");
			bottom_panel = false;
		}
	});

	var summary_panel = false;
	$(".summaryimgdiv").bind("click",function(){
		if(!summary_panel){
			$("#summary_panel").animate({"bottom": "0px"}, "fast");
			summary_panel = true;
		}else{
			$("#summary_panel").animate({"bottom": "-133px"}, "fast");
			summary_panel = false;
		}
	});

	var programsummary_panel = false;
	$(".programsummaryimgdiv").bind("click",function(){
		if(!programsummary_panel){
			$("#programsummary_panel").animate({"bottom": "0px"}, "fast");
			programsummary_panel = true;
		}else{
			$("#programsummary_panel").animate({"bottom": "-133px"}, "fast");
			programsummary_panel = false;
		}
	});	
	<?php if(my_session_value('userprogID')):?>	//aulomatically load the content on refresh if the user program id is set..
	refreshmaincontent(<?php echo my_session_value('userprogID'); ?>,<?php echo my_session_value('dateID'); ?>,<?php echo my_session_value('showalldays')?"'".my_session_value('showalldays')."'":"''"?>);
	<?php endif;?>	

	$(".prog").bind("click",function(){
		showMyLoader();
		var loc = "<?php echo site_url('main/program');?>";
		var id = $(this).attr('id');//user program id
		var f_data = {
					id:id,
					ajax:1
				};
		$.ajax({
			url:loc,
			data:f_data,
			type:'POST',
			success:function(msg){
					hideMyLoader();
					$("#main-content").empty().html(msg);
					$(".imgdiv").click();
					$.ajax({//for previous days
						url:"<?php echo site_url('main/getPrevDays');?>",
						data: f_data,
						type:'POST',
						success:function(prevmsg){
							$("#prevdays").html(prevmsg);
							$("#bottompanelholder").removeClass("hidden");			
						},
						error:function(msg){
							//alert('error');
						}
					});
					$("#programsummary_panel #programsummary").empty();
					$.ajax({//for program summary
						url:"<?php echo site_url('main/getProgramSummary');?>",
						data: f_data,
						type:'POST',
						success:function(prosum){
							$("#programsummary_panel #programsummary").html(prosum);
							$("#programsummaryholder").removeClass("hidden");			
						},
						error:function(msg){
							//alert('error');
						}
					});		
					//for daily summary	
					var sdata = {
							dateid : $("#detailsdateid").val(),
							ajax : '1'
							};
					$("#summary_panel #summary").empty();
					$.ajax({//for daily summary
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
					$("#right_panel #rightcontent").empty();
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

	$('.program_scroll').slimScroll({height: '320px',alwaysVisible: true});
	
});	

function refreshmaincontent(id,dateid,showalldays)
{
	$("#main-content").empty();
	showMyLoader();
	var loc = "<?php echo site_url('main/program');?>";
	var f_data = {
				id:id,
				dateID:dateid,
				showalldays: showalldays,
				ajax:1
			};
	$.ajax({
		url:loc,
		data:f_data,
		type:'POST',
		success:function(msg){
				hideMyLoader();
				$("#main-content").empty().html(msg);
				$.ajax({//for days
					url:"<?php echo site_url('main/getPrevDays');?>",
					data: f_data,
					type:'POST',
					success:function(prevmsg){
						$("#prevdays").html(prevmsg);
						$("#bottompanelholder").removeClass("hidden");
						$("#rightpanelholder").removeClass("hidden");
					},
					error:function(){
						//alert('grrr');
					}
				});

				$("#programsummary_panel #programsummary").empty();
				$.ajax({//for program summary
					url:"<?php echo site_url('main/getProgramSummary');?>",
					data: f_data,
					type:'POST',
					success:function(prosum){
						$("#programsummary_panel #programsummary").html(prosum);
						$("#programsummaryholder").removeClass("hidden");			
					},
					error:function(msg){
						//alert('error');
					}
				});		
				
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
						if(showalldays==""){
						$("#summaryholder").removeClass("hidden");
						}
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
				$("#right_panel #rightcontent").empty();
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
}
</script>
<?php if(isset($programs) && count($programs)>0):?>
<div id="side_panel" class="ui-widget-content
	<?php 
		if(!$show_on_home){
			if( ! my_session_value('userprogID'))
			{
				echo 'hidden';
			}	
		}
	?>">
	<div class="programlist">
		<div class="program_scroll">
		<?php foreach ($programs as $value):?>
			<div style="margin: 5px 0;">
				<img class="prog" id="<?php echo $value['id']?>" alt="<?php echo $value['title'];?>" src="<?php echo base_url()?>assets/photos/logo/<?php echo $value['logo']?>"
				 style="float:left;width: 100px;height: 100px;border: 0">
				<div style="float:left;padding:5px;font-size: 10px;">
						<span id="<?php echo $value['id']?>" class="title prog"><b><?php echo $value['title'].' '.$value['batch']?></b></span><br>
						<span>Start Date: <br><b><?php echo date("M j, Y",strtotime($value['dateStart']))?></b></span><br>
						<span>End Date: <br><b><?php echo date("M j, Y",strtotime($value['dateEnd']))?></b></span>	
				</div>	
			</div>
			<div class="clearer"></div>		
		<?php endforeach;?>
		</div>
	</div>
	<div class="imgdiv"><!-- THIS IS THE HANDLE -->
	<img style="width: 30px;margin-top:20px;" alt="" src="<?php echo base_url()?>/assets/images/programlabel.png">
	</div>
</div>
<?php endif;?>

<script type="text/javascript">
$(document).ready(function(){
	$("#announceNav span").click(function(){
		$("#announceContent").fadeOut('300');
		$.ajax({
			url : $(this).attr("href"),
			type : 'GET',
			success : function(res){
				$("#announcedetailsholder").fadeOut('fast');
				$("#announceContent").fadeIn('fast').html(res);
			}
		});
	});	

	$("#announceContent .aTrigger").live("click",function(){
		var fdata = {
				aID : $(this).attr('id'),
				isA : '<?php echo isset($isAdmin)?$isAdmin:'0'?>',
				ajax : '1'
				};
		$.ajax({
			url:"<?php echo site_url('main/getAnnouncement');?>",
			data: fdata,
			type:'POST',
			success:function(ann){
				$("#announcedetailsholder #announceDetails").html(ann);
				$("#announcedetailsholder").fadeIn('fast');
			},
			error:function(msg){
				//alert(msg);
			}
		});	
	});

	$("#announceContent .newann").live("click",function(){
		var fdata = {
				ajax : '1'
				};
		$.ajax({
			url:"<?php echo site_url('main/addAnnouncement');?>",
			data: fdata,
			type:'POST',
			success:function(ann){
				$("#announcedetailsholder #announceDetails").html(ann);
				$("#announcedetailsholder").fadeIn('fast');
			},
			error:function(msg){
				//alert(msg);
			}
		});	
	});	

	$("#main-content .editicon").live("click",function(){
		var fdata = {
				aID : $(this).attr('id'),
				ajax : '1'
				};
		$.ajax({
			url:"<?php echo site_url('main/editAnnouncement');?>",
			data: fdata,
			type:'POST',
			success:function(ann){
				$("#announcedetailsholder #announceDetails").html(ann);
				$("#announcedetailsholder").fadeIn('fast');
			},
			error:function(msg){
				//alert(msg);
			}
		});	
	});		

	$("#announcedetailsholder #close").click(function(){
		$("#announcedetailsholder").fadeOut('fast');
	});
	$("#announcedetailsholder").draggable();

	$(".annTitle").live("mouseover",function(){
		$(".editicon").removeClass("hidden");
	});
	$(".annTitle").live("mouseout",function(){
		$(".editicon").addClass("hidden");
	});

});
</script>
<div class="ui-widget-content">
<?php if(my_session_value('userprogID') OR (isset($show_on_home) && $show_on_home) ):?>
	<div id="main-content"><!-- MAIN CONTENT-->	
		<div class="">
			<img style="margin-left:-52px;width:110%;height:500px;margin-top:-20px;margin-bottom:-120px;" align="middle" alt="" src="<?php echo base_url()?>assets/images/homeBg-Nature.png">
			<?php if(isset($announce) || $isAdmin==1)://do not display if empty announcement but show if admin to be able to add?>
			<div id="announcementholder">
				<img style="width:651px;height: 400px;" align="middle" alt="" src="<?php echo base_url()?>assets/images/announcementBg2.png">
				<div id="announceContent">
					<?php if(isset($announce)):?>
						<h2 class="annTitle"><?php echo $announce->title?>
							<?php if($isAdmin==1):?>
							<span id="<?php echo $announce->id?>" class="editicon hidden" style="cursor: pointer;">
								<img style="width:18px;height: 18px;" align="top" title="Edit" src="<?php echo base_url()?>assets/images/edit-icon.png">
							</span>
							<?php endif;?>
						</h2>
						<p><?php echo $announce->preview?></p>
						<div id="footnote">
							<span id="postdate"><?php echo date("g:i a - l, F j, Y",strtotime($announce->postDate))?></span>
							<br>
							<span id="postby"><?php echo $announce->postBy?></span>
						</div>
						<div id="announceNav"><?php echo $this->ajaxpagination->create_links()?></div>
					<?php endif;?>
					<?php if($isAdmin==1):?>
						<div id="announceNew"><a href="javascript:void()" class="newann">New</a> </div>
					<?php endif;?>
				</div>
			</div>
			<div class="hidden" id="announcedetailsholder">
				<span id="close"><img title="Close" src="<?php echo base_url()?>/assets/images/cross.png"></span>
				<div id="announceDetails" style="margin-top: -13px;margin-bottom: 5px;"></div>
			</div>
			<?php endif;?>
		</div>
	</div><!-- END CLASS MAIN CONTENT-->
<?php else:?>
	<div>
		<?php $this->load->view('v_main_content')?>
	</div>
<?php endif;?>
</div>

<div id="rightpanelholder" class="hidden">
	<div id="right_panel">
		<div id="righthandle"><img style="height: 30px;margin-left:3px;width: 104px" alt="" src="<?php echo base_url()?>/assets/images/archivelabel.png"></div>
		<div id="rightbackground">
			<div id="rightcontent">
				
			</div>
		</div>		
	</div>	
</div>

<div id="bottompanelholder" class="hidden">
	<div id="bottom_panel">
		<div class="bottomimgdiv" align="center"><!-- THIS IS THE HANDLE -->
		<img style="height: 30px;margin-left:10px;" alt="" src="<?php echo base_url()?>/assets/images/previousdaylabel.png">
		</div>
		<div id="prevdays">
		</div>
	</div>
</div>
<div id="programsummaryholder" class="hidden">
	<div id="programsummary_panel">
		<div class="programsummaryimgdiv" align="center"><!-- THIS IS THE HANDLE -->
		<img style="height: 30px;margin-left:2px;" alt="" src="<?php echo base_url()?>/assets/images/programsummarylabel.png">
		</div>
		<div id="programsummary">

		</div>
	</div>
</div>
<div id="summaryholder" class="hidden">
	<div id="summary_panel">
		<div class="summaryimgdiv" align="center"><!-- THIS IS THE HANDLE -->
		<img style="height: 30px;" alt="" src="<?php echo base_url()?>/assets/images/dailysummarylabel.png">
		</div>
		<div id="summary">

		</div>
	</div>
</div>