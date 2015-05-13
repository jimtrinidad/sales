<script src="<?php echo base_url()?>assets/js/changePhoto.js" type="text/javascript"></script>
<table cellpadding="0" cellspacing="0" border="0" id="mainTable">
	<tbody>
		<tr>
			<td valign="top" class="content ui-widget-content">
				<table class="homecontenttable" cellpadding="0" cellspacing="" border="0" width="100%">
					<tr>
						<td class="left" width="210px" align="left">
							<div class="sidebar-container ui-widget-content ui-corner-top">	
								<div class="sidebar-content widget-content">
									<div class="profilePic">
										<img id="userProfilePhoto" src="<?php echo file_exists('assets/images/userphoto/'.getUserData($userid)->photo) ? base_url().'assets/images/userphoto/'.getUserData($userid)->photo : base_url().'assets/images/userphoto/blank.jpg'?>" width="206px" height="190px">
									</div>
									<div class="profileInfoBox">
										<span class="fadeText">Welcome,</span> <b><?php echo $uname;?></b>
										<br>
										<?php if(userPrivilege('isAdmin') && !isset($userWall)):?>
										<a id="<?php echo $userid?>" class="changePhoto">Change picture</a>
										<?php endif;?>
									</div>
								</div>
							</div>
							<?php if(isset($stats) && is_array($stats)):?>
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									My Stats
									<span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="statDetails">details</a></span>
								</div>
								<div class="sidebar-content widget-content my-widget">
									<table cellpadding="1" cellspacing="1" width="100%">
										<tr>
											<td>Won : <b><?php echo $stats['career']['won']['raw']?></b></td>
											<td>Pending : <b><?php echo $stats['career']['pending']['raw']?></b></td>											
										</tr>
										<tr>
											<td>Loss : <b><?php echo $stats['career']['loss']['raw']?></b></td>
											<td>Reject : <b><?php echo $stats['career']['rejected']['raw']?></b></td>
										</tr>
										<tr>
											<td colspan="2"><b>Programs :</b> &nbsp;&nbsp;
												Active: <b><?php echo $stats['activePrograms']?></b>&nbsp;&nbsp;
												Handled: <b><?php echo count($stats['programs'])?></b>
											</td>											
										</tr>
							
									</table>
								</div>										
							</div>
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									My Rankings									
								</div>
								<div class="sidebar-content widget-content my-widget">
									<table cellpadding="1" cellspacing="1" width="100%">
										<tr>
											<td></td>
											<td align="center">Won</td>
											<td align="center">Leads</td>											
										</tr>
										<tr>
											<td>Last Month</td>
											<td align="center"><b><?php echo $rank['monthly']['won']['rank']?></b></td>
											<td align="center"><b><?php echo $rank['monthly']['pending']['rank']?></b></td>
										</tr>
										<tr>
											<td>Year To Date</td>
											<td align="center"><b><?php echo $rank['yearToDate']['won']['rank']?></b></td>
											<td align="center"><b><?php echo $rank['yearToDate']['pending']['rank']?></b></td>
										</tr>
										<tr>
											<td>Career</td>
											<td align="center"><b><?php echo $rank['career']['won']['rank']?></b></td>
											<td align="center"><b><?php echo $rank['career']['pending']['rank']?></b></td>
										</tr>							
									</table>
								</div>										
							</div>							
							<?php endif;?>
							<?php if(isset($programs) && count($programs)>0):?>
							<div class="sidebar-container ui-widget-content ui-corner-top">	
								<div class="ui-widget-header widget-title ui-corner-top">
									 My Programs								 
								</div>
								<div class="sidebar-content widget-content" id="user_programs" style="max-height: 187px;overflow: auto;">									
									<div class="programlist">
									<?php foreach ($programs as $value):?>
										<div class="program" id="<?php echo $value['id']?>" style="cursor: pointer;margin: 5px 0;">
											<img alt="<?php echo $value['title'];?>" src="<?php echo base_url()?>assets/photos/logo/<?php echo $value['logo']?>" style="float:left;width: 90px;height: 80px;border: 0">
											<div style="float:left;padding:5px;font-size: 10px;">
													<span class="title"><b><?php echo $value['title'].' '.$value['batch']?></b></span><br>
													<span>Start Date: <br><b><?php echo date("M j, Y",strtotime($value['dateStart']))?></b></span><br>
													<span>End Date: <br><b><?php echo date("M j, Y",strtotime($value['dateEnd']))?></b></span>	
											</div>
											<div class="clearer"></div>	
										</div>
									<?php endforeach;?>
									</div>									
								</div>
							</div>
							<?php endif;?>
							<?php if(isset($totalGraph)):?>
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									Weekly Totals Sales	
									<span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="totalGraphMore">more</a></span>	 
								</div>
								<div class="sidebar-content widget-content my-widget" style="height: auto;">
									<div class="chartBox">
										<div class="chartButtonDiv"></div>
										<?php echo $totalGraph?>
									</div>	
								</div>										
							</div>								
							<?php endif;?>
						</td>								
						<td>
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									 Announcements
									  <?php if(userPrivilege('post')):?><span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="addPost">Post</a></span><?php endif;?>							 
								</div>								
								<div class="sidebar-content widget-content" style="max-height: 239px;overflow: auto;">
									<div class="hidden" id="messageBoxDiv">
										<form id="messageForm">
											<div class="sidebar-container ui-widget-content" style="margin: 5px 2px;">
												<label style="margin: 2px;">Title:</label><span class="floatright title_error_box"></span>
												<input type="text" name="title" style="resize: none;width: 99.4%;margin: 0;border: 0;padding: 2px;" value="<?php echo set_value('title'); ?>">
											</div>
											<div class="sidebar-container ui-widget-content" style="margin: 5px 2px;">
												<label style="margin: 2px;">Content:</label><span class="floatright content_error_box"></span>
												<textarea name="content" id="messageBox" rows="3" style="resize: none;width: 98.8%;margin: 0;border: 0;padding: 4px;" ><?php echo set_value('content'); ?></textarea>
												<div align="right" class="buttons_div ui-widget-header" style="border: 0;padding: 1px 2px 0;"><button type="submit" class="button">Post</button></div>
											</div>
										</form>
									</div>	
									<ul id="announcementDiv">
										<?php foreach($announcements->result() as $announce):?>
											<?php if($announce->postBy == 0):?>
												<li id="<?php echo $announce->id?>">											
													<div class="post-main-wrapper ui-widget-content" style="position: relative;">
														<div class="post-user-photo">
															<img src="<?php echo base_url().'assets/images/userphoto/'.$this->db->where('key','robot_photo')->get('tb_settings')->row()->value?>">
														</div>
														<div class="post-main-content">
															<div class="post-author"><?php echo $announce->title?>
																<?php if($isAdmin==1):?>
																<span id="<?php echo $announce->id?>" class="editicon hidden" style="cursor: pointer;">
																	<img style="width:18px;height: 18px;" align="top" title="Edit" src="<?php echo base_url()?>assets/images/edit-icon.png">
																</span>
																<?php endif;?>
															</div>												
															<div class="post-message"><?php echo str_replace("\n", '<br>', $announce->content)?></div>
															<div class="post-time">
																<div style="float: left"><?php echo get_date_diff($announce->postDate)?></div>
																<div style="float: right"><?php echo $this->db->where('key','robot_name')->get('tb_settings')->row()->value;?></div>
															</div>
														</div>
														<?php if(userPrivilege('isAdmin') OR $announce->postBy == my_session_value('uid')):?>
															<div class="removePostDiv hidden" style="position: absolute;right: 1px;top: 1px;">																												
																<a id="<?php echo $announce->id?>" class="removePost" title="remove post"><img style="margin-right: 1px;" width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/trash.gif'?>"></a>
															</div>
														<?php endif;?>
														<div class="clearer"></div>
													</div>
												</li>											
											<?php else:?>
												<li id="<?php echo $announce->id?>">											
													<div class="post-main-wrapper ui-widget-content" style="position: relative;">
														<div class="post-user-photo">
															<img src="<?php echo file_exists('assets/images/userphoto/'.getUserData($announce->postBy)->photo) ? base_url().'assets/images/userphoto/'.getUserData($announce->postBy)->photo : base_url().'assets/images/userphoto/blank.jpg'?>">
														</div>
														<div class="post-main-content">
															<div class="post-author"><?php echo $announce->title?>
																<?php if($isAdmin==1):?>
																<span id="<?php echo $announce->id?>" class="editicon hidden" style="cursor: pointer;">
																	<img style="width:18px;height: 18px;" align="top" title="Edit" src="<?php echo base_url()?>assets/images/edit-icon.png">
																</span>
																<?php endif;?>
															</div>												
															<div class="post-message"><?php echo str_replace("\n", '<br>', $announce->content)?></div>
															<div class="post-time">
																<div style="float: left"><?php echo get_date_diff($announce->postDate)?></div>
																<div style="float: right"><?php echo getUserData($announce->postBy)->name?></div>
															</div>
														</div>
														<?php if(userPrivilege('isAdmin') OR $announce->postBy == my_session_value('uid')):?>
															<div class="removePostDiv hidden" style="position: absolute;right: 1px;top: 1px;">																												
																<a id="<?php echo $announce->id?>" class="removePost" title="remove post"><img style="margin-right: 1px;" width="12px" height="12px" src="<?php echo base_url().'assets/images/icons/trash.gif'?>"></a>
															</div>
														<?php endif;?>
														<div class="clearer"></div>
													</div>
												</li>
											<?php endif;?>	
										<?php endforeach;?>									
									</ul>																	
								</div>																	
							</div>
							
							<div class="ui-widget-content sidebar-container ui-corner-top" style="float: left;width: 49%;">
								<div class="ui-widget-header widget-title ui-corner-top">
									 Who's Leading
									 <?php if(isset($weektodate) && is_array($weektodate)):?>
									 <span class="" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-left: 5px;">
									 	( <?php 
									 		echo date('M j',$weektodate['start']);
									 		echo ' to ';
									 		echo date('M j',$weektodate['end']);
									 	?> )
									 </span>
									 <?php endif;?>							 
								</div>
								<div class="sidebar-content widget-content my-widget" style="padding-left: 4px;padding-right: 0;">
									<ul class="topList" style="margin: 0 auto;width: 285px;">
										<?php if(isset($weektodate) && is_array($weektodate)):?>
										<?php foreach($weektodate['users'] as $k=>$v):?>
										<li>
											<?php if(userPrivilege('ranking')):?><a href="<?php echo site_url('ranking')?>"><?php endif;?>
											<div title="<?php echo $v['name']?>">
												<div>
													<div class="rankNumber"><h1><?php echo $k+1?></h1></div>
													<img class="rankPhoto" src="<?php echo file_exists('assets/images/userphoto/'.getUserData($v['userid'])->photo) ? base_url().'assets/images/userphoto/'.getUserData($v['userid'])->photo : base_url().'assets/images/userphoto/blank.jpg'?>">													
												</div>
												<div class="rankLabel">
													<div align="center" class="post-message"><b><?php echo $v['totalPoints']?> Points</b></div>
													<div align="center" class="post-message"><?php echo $v['firstname']?></div>
												</div>
											</div>
											<?php if(userPrivilege('ranking')):?></a><?php endif;?>
										</li>
										<?php endforeach;?>
										<?php endif;?>
									</ul>
								</div>										
							</div>	
							<div class="ui-widget-content sidebar-container ui-corner-top" style="float: right;width: 49%;">
								<div class="ui-widget-header widget-title ui-corner-top">
									 Honor Roll
									 <?php if(isset($lastweek) && is_array($lastweek)):?>
									 <span class="" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-left: 5px;">
									 	( <?php 
									 		echo date('M j',$lastweek['start']);
									 		echo ' to ';
									 		echo date('M j',$lastweek['end']);
									 	?> )
									 </span>
									 <?php endif;?>							 
								</div>
								<div class="sidebar-content widget-content my-widget" style="padding-left: 4px;padding-right: 0;">
									<ul class="topList" style="margin: 0 auto;width: 285px;">
										<?php if(isset($lastweek) && is_array($lastweek)):?>
										<?php foreach($lastweek['users'] as $k=>$v):?>
										<li>
											<?php if(userPrivilege('ranking')):?><a href="<?php echo site_url('ranking')?>"><?php endif;?>
											<div title="<?php echo $v['name']?>">
												<div>													
													<div class="rankNumber"><h1><?php echo $k+1?></h1></div>
													<?php if( $k == 0 && $v['id'] == $secondlastweeks['users'][0]['id'] &&  $v['id'] == $thirdlastweeks['users'][0]['id'] ):?>
														<div style="position: relative;">
															<img style="width: 32px;height: 30px;position: absolute;top: -6px;right: 6px;" src="<?php echo base_url().'assets/images/crown_red.gif'?>">
														</div>
													<?php elseif( $k == 0 && $v['id'] == $secondlastweeks['users'][0]['id']):?>
														<div style="position: relative;">
															<img style="width: 32px;height: 30px;position: absolute;top: -6px;right: 6px;" src="<?php echo base_url().'assets/images/crown_yellow.gif'?>">
														</div>
													<?php endif;?>
													<img class="rankPhoto" src="<?php echo file_exists('assets/images/userphoto/'.getUserData($v['userid'])->photo) ? base_url().'assets/images/userphoto/'.getUserData($v['userid'])->photo : base_url().'assets/images/userphoto/blank.jpg'?>">													
												</div>
												<div class="rankLabel">
													<div align="center" class="post-message"><b><?php echo $v['totalPoints']?> Points</b></div>
													<div align="center" class="post-message"><?php echo $v['firstname']?></div>
												</div>
											</div>
											<?php if(userPrivilege('ranking')):?></a><?php endif;?>
										</li>
										<?php endforeach;?>
										<?php endif;?>
									</ul>
								</div>										
							</div>	
							
							<div class="clearer"></div>
							
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									 Energy Level
									 <?php if(isset($graphTime)):?>
									 <span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;">
									 	<form action="<?php echo site_url()?>" method="post" id="graphTimeForm">
									 		<input type="hidden" value="<?php echo $graphTime?>" name="graphTime">
									 		<a class="submitGrapthTime">switch to <?php echo strtolower($graphTime)?></a>
									 	</form>
									</span>	
									<?php endif;?>						 
								</div>
								<div class="sidebar-content widget-content my-widget" style="height: auto;">
									<?php if(isset($graphTime)):?>
									<div class="chartBox">
										<div class="chartButtonDiv"></div>
										<?php echo $userPending?>
									</div>	
									<?php endif;?>			
								</div>										
							</div>										 
						</td>
						<td width="311px">
							<?php if(isset($top_programs) && count($top_programs)):?>										
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									 Top Programs							 
								</div>
								<div class="sidebar-content widget-content my-widget">
									<ul style="padding: 0;">
										<?php foreach($top_programs as $k=>$v):?>
										<li class="ui-widget-content post-main-wrapper">
											<div>
												<div style="position: relative;">
													<div class="rankNumber"><h1><?php echo $k+1?></h1></div>
													<img title="<?php echo $v['title'].' '.$v['batch']?>" class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.$v['logo']) ? base_url().'assets/photos/logo/'.$v['logo'] : base_url().'assets/photos/logo/blanklogo.png'?>">													
												
													<div style="position: absolute;top: 3px;left: 90px;">														
														<div class="post-author" style="font-size: 10px;"><?php echo $v['title'].' '.$v['batch']?></div>
														<div class="post-author" style="font-size: 10px;"><?php echo $v['programPercent']?>% on target</div>
														<div class="post-author" style="font-size: 10px;"><?php echo week_before($v['weekBefore'])?></div>
													</div>
													<div style="position: absolute;top: 0px;right: 5px;">
														<?php foreach($v['users'] as $user):?>
														<img title="<?php echo getUserData($user['userID'])->name?>" class="floatright" style="width: 36px;height: 36px;" src="<?php echo file_exists('assets/images/userphoto/'.$user['photo']) ? base_url().'assets/images/userphoto/'.$user['photo'] : base_url().'assets/images/userphoto/blank.jpg'?>">
														<?php endforeach;;?>
													</div>
												</div>
											</div>
										</li>										
										<?php endforeach;?>
									</ul>
								</div>										
							</div>
							<?php endif;?>
							<?php if(isset($critical_programs) && count($critical_programs)):?>	
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									Danger Zone
								</div>
								<div class="sidebar-content widget-content my-widget">
									<ul style="padding: 0;">
										<?php foreach($critical_programs as $k=>$v):?>
										<li class="ui-widget-content post-main-wrapper">
											<div>
												<div style="position: relative;">
													<div class="rankNumber"><h1><?php echo $k+1?></h1></div>
													<img title="<?php echo $v['title'].' '.$v['batch']?>" class="rankPhoto" src="<?php echo file_exists('assets/photos/logo/'.$v['logo']) ? base_url().'assets/photos/logo/'.$v['logo'] : base_url().'assets/photos/logo/blanklogo.png'?>">													
												
													<div style="position: absolute;top: 3px;left: 90px;">														
														<div class="post-author" style="font-size: 10px;"><?php echo $v['title'].' '.$v['batch']?></div>
														<div class="post-author" style="font-size: 10px;"><?php echo $v['programPercent']?>% on target</div>
														<div class="post-author" style="font-size: 10px;"><?php echo week_before($v['weekBefore'])?></div>
													</div>
													<div style="position: absolute;top: 0px;right: 5px;">
														<?php foreach($v['users'] as $user):?>
														<img title="<?php echo getUserData($user['userID'])->name?>" class="floatright" style="width: 20px;height: 20px;" src="<?php echo file_exists('assets/images/userphoto/'.$user['photo']) ? base_url().'assets/images/userphoto/'.$user['photo'] : base_url().'assets/images/userphoto/blank.jpg'?>">
														<?php endforeach;;?>
														<div style="position: relative;">
															<img style="width: 24px;height: 24px;position: absolute;top: 25px;right: -2px;" src="<?php echo base_url().'assets/images/th_bombpow.gif'?>">
														</div>
													</div>
												</div>
											</div>
										</li>	
										<?php endforeach;?>
									</ul>
									<?php if(count($critical_programs) == 0):?>
									<div align="center">Nothing</div>
									<?php endif;?>
								</div>										
							</div>
							<?php endif;?>
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									 Who's Hot
									 <?php if(isset($hot_cold) && is_array($hot_cold)):?>
									  <span class="" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-left: 5px;">
									 	( <?php 
									 		echo date('M j',strtotime(end($hot_cold['date'])));
									 		echo ' to ';
									 		echo date('M j',strtotime($hot_cold['date'][0]));
									 	?> )
									 </span>	
									 <?php endif;?>							 
								</div>
								<div class="sidebar-content widget-content my-widget">
									<ul class="topList">
										<?php if(isset($hot_cold) && is_array($hot_cold)):?>
										<?php foreach($hot_cold['hot'] as $k=>$v):?>
										<li>
											<div title="<?php //echo $v['name']." got ".$v['totals']." deligates for the last three weeks"?>">
												<div>
													<div class="rankNumber"><h1><?php echo $k+1?></h1></div>
													<img class="rankPhoto" src="<?php echo file_exists('assets/images/userphoto/'.getUserData($v['id'])->photo) ? base_url().'assets/images/userphoto/'.getUserData($v['id'])->photo : base_url().'assets/images/userphoto/blank.jpg'?>">													
												</div>
												<div class="rankLabel" style="position: relative;">
													<div align="center" class="post-message"><b><?php //echo $v['totals']?></b></div>
													<div align="center" class="post-message"><?php echo $v['firstname']?></div>
													<div style="position: absolute;top: 4px;">
														<img style="width: 60px;height: 20px;" src="<?php echo base_url().'assets/images/fire.gif'?>">
													</div>
												</div>
											</div>
										</li>
										<?php endforeach;?>
										<?php endif;?>
									</ul>
									<?php if(isset($hot_cold) && count($hot_cold['hot']) == 0):?>
									<div align="center">No one</div>
									<?php endif;?>
									<div class="clearer"></div>
								</div>										
							</div>	
							<div class="ui-widget-content sidebar-container ui-corner-top">
								<div class="ui-widget-header widget-title ui-corner-top">
									 Who's Cold
									 <?php if(isset($hot_cold) && is_array($hot_cold)):?>
									 <span class="" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-left: 5px;">
									 	( <?php 
									 		echo date('M j',strtotime(end($hot_cold['date'])));
									 		echo ' to ';
									 		echo date('M j',strtotime($hot_cold['date'][0]));
									 	?> )
									 </span>
									 <?php endif;?>							 
								</div>
								<div class="sidebar-content widget-content my-widget">
									<ul class="topList">
										<?php if(isset($hot_cold) && is_array($hot_cold)):?>
										<?php foreach($hot_cold['cold'] as $k=>$v):?>
										<li>
											<div title="<?php //echo $v['name']." got ".$v['totals']." deligates for the last three weeks"?>">
												<div>
													<div class="rankNumber"><h1><?php echo $k+1?></h1></div>
													<img class="rankPhoto" src="<?php echo file_exists('assets/images/userphoto/'.getUserData($v['id'])->photo) ? base_url().'assets/images/userphoto/'.getUserData($v['id'])->photo : base_url().'assets/images/userphoto/blank.jpg'?>">													
												</div>
												<div class="rankLabel" style="position: relative;">
													<div align="center" class="post-message"><b><?php //echo $v['totals']?></b></div>
													<div align="center" class="post-message"><?php echo $v['firstname']?></div>
													<div style="position: absolute;top: -50px;left: -1px;">
														<img style="width: 60px;height: 50px;" src="<?php echo base_url().'assets/images/snowflkesfllng.gif'?>">
													</div>
												</div>
											</div>
										</li>
										<?php endforeach;?>
										<?php endif;?>
									</ul>
									<?php if(isset($hot_cold) && count($hot_cold['cold']) == 0):?>
									<div align="center">No one</div>
									<?php endif;?>
									<div class="clearer"></div>
								</div>										
							</div>		
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
$(document).ready(function(){

	$(".program").bind("click",function(){
		//$("#main-content").empty().html("<img class='bigloader' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		showMyLoader();
		var loc = "<?php echo site_url('main/program');?>";
		var id = $(this).attr('id');//user program id
		var f_data = {
					id:id
				};
		$.ajax({
			url:loc,
			data:f_data,
			type:'POST',
			success:function(msg){
				location.reload();
			}
		});
	});	

	$(".button").button();
	
    /*
	$('#user_programs').slimScroll({
		  height: '187px',
		  alwaysVisible: true
	});
    */
	$('#announcementDiv').slimScroll({
		  height: '239px',
		  alwaysVisible: true
	});

	$(".post-main-wrapper").mouseover(function(){$(this).find('.removePostDiv').removeClass('hidden');}).mouseout(function(){$(this).find('.removePostDiv').addClass('hidden');});

	$('.addPost').click(function(){
		$('#messageBoxDiv').slideToggle(300,function(){
			$(this).find('textarea').val('');
			$(this).find('input[type=text]').val('');
		});
	});

	$("#messageForm").bind("submit",function(){		
		if( ! $("#messageForm").hasClass('submitted'))
		{
			$("#messageForm").addClass('submitted');
			var fdata = $(this).serialize();
			$.ajax({
				url : '<?php echo site_url('main/saveAnnouncement')?>',
				type : 'POST',
				data : fdata,
				success : function (msg){
					result = $.parseJSON(msg);
					if(result.status == "error"){
						$('#messageForm .title_error_box').html(result.title);
						$('#messageForm .content_error_box').html(result.content);
						$("#messageForm").removeClass('submitted');
					}else{
						location.reload();		
					}
				},
				error : function(){
						$("#messageForm").removeClass('submitted');
					}
			});			
		}
		return false;
	});

	$(".removePost").click(function(){
		var $obj = $(this).closest('li');
		$.ajax({	
				url		: '<?php echo site_url('main/removePost')?>',
				type	: 'POST',
				data	: {postID : $(this).attr('id')},
				success	: function(){
							$obj.remove();
						}			
			});
	});

	$(".submitGrapthTime").click(function(){
		$("#graphTimeForm").trigger('submit');
	});

	$('.totalGraphMore').bind("click",function(){
		var fdata = {
				ajax : 1
				};
		ajaxCallBoxOpen('<?php echo site_url('main/total_graph_big')?>',fdata);
	    return false;

	});	

	$(".statDetails").bind("click",function(){
		var fdata = {
				ajax : 1
				};
		myDialogBox('<?php echo site_url('main/statDetails')?>',fdata,'my_stats','My Statistics',{width : '1300'});
	    return false;
	});	

	$('.changePhoto').bind("click",function(){
		var fdata = {
				refid : this.id,
				ajax : 1
				};
		ajaxCallBoxOpen('<?php echo site_url('user/changePhoto')?>',fdata);
	    return false;
	});	


	
	/*
	$(".totalGraphMore").bind("click",function(){
		var fdata = {
				ajax : 1
				};
		$("#expandChartholder").fadeIn('fast');
		$("#expandChartcontainer .noborderlist").empty().html("<img  style='margin:40px 0 30px 435px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		$.ajax({
			url : '<?php echo site_url('main/total_graph_big')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){
				$("#expandChartheader").find('span:first').html('Weekly Total Sales');
				$("#expandChartcontainer .noborderlist").html(msg);
			},
			error : function(){
				//alert('g');
			}
		});
	});
	

	$(".statDetails").bind("click",function(){
		var fdata = {
				ajax : 1
				};
		$("#expandChartholder").fadeIn('fast');
		$("#expandChartheader").find('span:first').html('My Statistics');		
		$("#expandChartcontainer .noborderlist").empty().html("<img  style='margin:40px 0 30px 611px;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
		$("#expandChart_panel").width('1340px').css('left','520px').css('top','108px');
		//$("#expandChart_panel").center();
		$.ajax({
			url : '<?php echo site_url('main/statDetails')?>',
			type : 'POST',
			data : fdata,
			success : function(msg){								
				$("#expandChartcontainer .noborderlist").html(msg);
			},
			error : function(){
				//alert('g');
			}
		});
	});	
	*/			
	
});
</script>