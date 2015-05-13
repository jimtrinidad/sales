<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo $title?></title>
<script src="<?php echo base_url()?>assets/js/jquery-1.6.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/jquery-ui-1.8.12.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/ckeditor/ckeditor.js" type="text/javascript" ></script>
<script src="<?php echo base_url()?>assets/js/noSelect.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/jqueryslidemenu.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/jquery.paginate.js" type="text/javascript" charset="utf-8"></script>	
<script src="<?php echo base_url()?>assets/js/jquery.tablesorter.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/pm.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/change-pm.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/slimScroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/jquery.fancybox-1.3.4.pack.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/jquery.combobox.js" type="text/javascript" charset="utf-8"></script>	
<script src="<?php echo base_url()?>assets/js/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/themeswitchertool.js" type="text/javascript" charset="utf-8"></script>


<base href="<?php echo base_url() ?>" />
<script type="text/javascript" src="<?php echo base_url()?>assets/js/swfobject.js"></script>

<link type="text/css" href="<?php echo base_url()?>assets/css/styles.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/jqueryslidemenu.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/overlay-apple.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/redmond/jquery-ui.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/jPagination.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" />

<?php $this->load->view('myJSfunctions')?>


<script type="text/javascript">
function noselect(){
	<?php if(userPrivilege('canCopy')!=1):?>
	$("#main-container,.contentWrap").disableTextSelect();
	<?php endif; ?>		
}

var serverTime = new Date('<?=date('r',strtotime(NOW))?>');
var current = new Date();
//updateClock();
function updateClock ()
{
    var now = new Date();
    var diff = now.getTime() - current.getTime();
    var interval = parseInt(serverTime.getTime() + diff);

    var currentTime = new Date (interval);
    //alert(currentTime);
    var currentHours = currentTime.getHours ( );
    var currentMinutes = currentTime.getMinutes ( );
    var currentSeconds = currentTime.getSeconds ( );
 
    // Pad the minutes and seconds with leading zeros, if required
    currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
    currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
 
    // Choose either "AM" or "PM" as appropriate
    var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
 
    // Convert the hours component to 12-hour format if needed
    currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
 
    // Convert an hours component of "0" to "12"
    currentHours = ( currentHours == 0 ) ? 12 : currentHours;
 
    // Compose the string for display
    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
     
     
    $("#clock").html(currentTimeString); 
}

$(document).ready(function(){

	hideMyLoader();
	$("a").click(function(){$(this).blur();});
	$('.logoutButton').button();
	//$('#themeroller').themeswitcher();

	$(".semail").bind("click",function(){
		var fdata = {
				ajax : 1
				};
		myDialogBox('<?php echo site_url('main/emaileditor')?>',fdata,'emailer','Email Editor',{width : 'auto'});
	    return false;
	});	

    setInterval('updateClock()',1000);
});
</script>
</head>

<body onload="noselect()">
	<div class="" id="modal">
		<div id="div-loader" class="ui-corner-all ui-widget-content">
			<table cellpadding="5" cellspacing="5" align="center" border="0">
				<tr>
					<td valign="top"><img src="<?php echo base_url()?>assets/images/ajax-loader.gif"/></td>
					<td style="vertical-align: middle;">Loading, please wait...</td>
				</tr>
			</table>	
		</div>
		<div class="hidden ui-widget-content ui-corner-top" id="mymessagebox">
			<div class="header ui-widget-header ui-corner-top" >
				<span class="title">Message</span>
				<span class="header-close"></span>
			</div>
			<div class="message-content">
			</div>
			<div class="closebutton">Close</div>
		</div>
		<div class="hidden ui-widget-content ui-corner-top" id="myConfirmBox">
			<div class="header ui-widget-header ui-corner-top">
				<span class="title">Confirm</span>
				<span class="header-close"></span>
			</div>
			<div class="message-content">
			</div>
			<div class="closebutton" id="no" style="padding:0 10px;font-size: 11px;">No</div>
			<div class="closebutton" id="yes" style="padding:0 10px;font-size: 11px;">Yes</div>
		</div>	
	</div>
	<div id="themeroller" style="position: fixed;bottom: 1px;right: 1px;"></div>
	<div id="main-container">
		<div id="container">
			<div id="main-header">
				<div class="logoDiv">
					 <div class="img"><a href="<?php echo site_url()?>"><img src="<?php echo base_url()?>assets/images/solidground-logo.png" alt="" /></a></div>
				</div>	
				<div id="menuTabs">
					<div id="myslidemenu" class="jqueryslidemenu">
						<ul>
							<li class="first_child"><a class="first" href="<?php echo site_url('main/home')?>"><img src="<?php echo base_url()?>assets/images/icons/home.png"/>Home</a></li>	
							
							<?php if(userPrivilege('isAdmin')==0 && userPrivilege('canSendM')==1):?>
							<li class="first_child"><a class="semail"><img src="<?php echo base_url()?>assets/images/icons/email.png"/>Email</a></li>
							<?php endif;?>	
							
							<?php if(userPrivilege('dashboard')==1):?>
							<li class="first_child"><a href="<?php echo site_url('dashboard')?>"><img src="<?php echo base_url()?>assets/images/icons/grid.png"/>Dashboard</a>
								<ul>
									<li><a href="<?php echo site_url('dashboard')?>"><img src="<?php echo base_url()?>assets/images/icons/bricks.png"/>Program Sales</a></li>
									<li><a href="<?php echo site_url('dashboard/programprogress')?>"><img src="<?php echo base_url()?>assets/images/icons/datashowchart.png"/>Program Progress</a></li>
								</ul>
							</li>
							<?php endif;?>	
							<?php if(userPrivilege('program')==1 || userPrivilege('programStatus')==1):?>
							<li class="first_child"><a href="<?php echo site_url('administrator')?>"><img src="<?php echo base_url()?>assets/images/icons/book_addresses.png"/>Programs</a></li>
							<?php endif;?>
							<?php if(userPrivilege('schedule') OR userPrivilege('isAdmin')):?>													
							<li class="first_child"><a href="<?php echo site_url('schedule')?>"><img src="<?php echo base_url()?>assets/images/icons/calendar_list.png"/>Schedules</a>						
								<ul>
									<li><a href="<?php echo site_url('schedule')?>"><img src="<?php echo base_url()?>assets/images/icons/calendar_list.png"/>Monthly View</a></li>
									<li><a href="<?php echo site_url('schedule/calendar')?>"><img src="<?php echo base_url()?>assets/images/icons/calendar_view.png"/>Calendar View</a></li>
									<li><a href="<?php echo site_url('schedule/by_program')?>"><img src="<?php echo base_url()?>assets/images/icons/book_addresses.png"/>Program View</a></li>
									<?php if(userPrivilege('isAdmin') || userPrivilege('program_misc_setting')):?>					
									<li><a href="<?php echo site_url('schedule/settings')?>"><img src="<?php echo base_url()?>assets/images/icons/settings.png"/>Settings</a></li>
									<?php endif;?>
								</ul>						
							</li>
							<?php endif;?>						
							<?php if(userPrivilege('statistics')==1):?>
							<li class="first_child"><a href="<?php echo site_url('statistics')?>"><img src="<?php echo base_url()?>assets/images/icons/stats.png"/>User Stats</a></li>
							<?php endif;?>
							<?php if(userPrivilege('ranking')==1):?>													
							<li class="first_child"><a href="<?php echo site_url('ranking')?>"><img src="<?php echo base_url()?>assets/images/icons/trophy.png"/>User Ranking</a></li>
							<?php endif;?>					
							<?php if(userPrivilege('reports')==1):?>
							<li class="first_child"><a href="<?php echo site_url('reports')?>"><img src="<?php echo base_url()?>assets/images/icons/reports.png"/>Reports</a>
								<ul>
									<li><a href="<?php echo site_url('reports')?>"><img src="<?php echo base_url()?>assets/images/icons/report_excel.png"/>Reports</a></li>
									<li><a href="<?php echo site_url('reports/summary')?>"><img src="<?php echo base_url()?>assets/images/icons/pie.png"/>Summary</a></li>
									<li><a href="<?php echo site_url('charts')?>"><img src="<?php echo base_url()?>assets/images/icons/chart_line.png"/>Charts</a></li>
									<li><a href="<?php echo site_url('charts/programs')?>"><img src="<?php echo base_url()?>assets/images/icons/reports.png"/>Program Graph</a></li>
                                                                        <li><a href="<?php echo site_url('reports/deleted')?>"><img src="<?php echo base_url()?>assets/images/icons/report_excel.png"/>Deleted Won</a></li>
								</ul>
							</li>
							<?php endif;?>
							
							<?php if(userPrivilege('isAdmin')==1):?>
							<li class="first_child"><a href="<?php echo site_url('settings')?>"><img src="<?php echo base_url()?>assets/images/icons/settings.png"/>Settings</a></li>
							<?php endif;?>
																			
							<?php if(userPrivilege('isAdmin')==1):?>
							<li class="first_child"><a href="<?php echo site_url('user/manage')?>"><img src="<?php echo base_url()?>assets/images/icons/users.png"/>User</a></li>
							<?php endif;?>
						</ul>
					</div>
	
					<div class="clearer"></div>
				</div>
				<div class="clearer"></div>			
				<div class="loggedUser">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td align="right" valign="top">
								<div style="font-size: 11px;">Welcome <?php if(isset($uname)):if(userPrivilege('isAdmin')==1):echo "Administrator";else:echo "User";endif;endif; ?></div>
								<div class="name"><?php echo getUserData(my_session_value('uid'))->name?></div>
								<div><a class="button logoutButton" href="<?php echo site_url('user/logout')?>">logout</a></div>
							</td>
							<td width="50px;">
								<img class="userProfilePicThumb" alt="" src="<?php echo file_exists('assets/images/userphoto/'.getUserData(my_session_value('uid'))->photo) ? base_url().'assets/images/userphoto/'.getUserData(my_session_value('uid'))->photo : base_url().'assets/images/userphoto/blank.jpg'?>"/>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="clearer"></div>	
					
			<div id="content">
				<?php $this->load->view($content)?>
			</div>
			
            <div id="main-footer" style="position: relative;">               
                <div>
                    Solid Ground Consulting Ltd.<br/>
                    5th Floor, East Tower Philippine Stock Exchange, Tektite Tower Exchange Road, Ortigas Center, Pasig City
                </div>
                 <div style="position: absolute;right: 1px;top: 5px;font-size: 11px;font-weight: bold;">
                     <?php echo date("D,  M j, Y ",strtotime(NOW))?><span id="clock"><?php echo date("g:i:s A",strtotime(NOW))?></span>
                 </div>
			</div>
		</div>		
	</div>
<?php if(isset($notes)):?>
	<?php foreach ($notes as $note):?>		
	<div id="stickynote" class="stickynote dragnote" style="<?php echo $note['xpos'] != 0 && $note['ypos'] != 0 ? "top:".$note['ypos']."px;left:".$note['xpos']."px;z-index:".$note['zIndex'].";":"z-index:".$note['zIndex'].";"?>" noteid="<?php echo $note['id']?>">
		<div class="top"><span id="close" class="<?php echo $note['id']?>"><img class="hidden" title="Remove" src="<?php echo base_url()?>assets/images/cross.png"/></span></div>
		<div class="content"><?php echo $note['note']?></div>
		<div class="bot">
			<div id="footnote">
				<span id="postdate"><?php echo date("g:i a - D, M-j-y",strtotime($note['postDate']))?></span>
				<br/>
				<span id="postby"><?php echo $note['postBy']?></span>	
			</div>
		</div>
	</div>
	<?php endforeach;?>
<script type="text/javascript">
var zIndex = 510; 
function make_draggable(elements)
{
    /* Elements is a jquery object: */
    elements.draggable({
        start:function(e,ui){ ui.helper.css('z-index',++zIndex); },
        stop:function(e,ui){
    		var fdata = {
					zIndex: zIndex,
					xpos :$(this).css('left'),
					ypos :$(this).css('top'),
					id  : $(this).attr('noteid'),
					ajax:1
				};
    		$.ajax({
    			url:'<?php echo site_url('user/saveNotePos')?>',
    			type:'POST',
    			data:fdata,
    			success : function(msg){
    				//alert(msg);
    			}
    		});
        }
    });
}
$(document).ready(function(){
	
	$(".stickynote").live("mouseover",function(){
		$(this).find("img").removeClass("hidden");
	});
	$(".stickynote").live("mouseout",function(){
		$(this).find("img").addClass("hidden");
	});
	
	make_draggable($(".stickynote"));

	$("#stickynote #close").live("click",function(){
		var note = $(this).parent().parent();
		note.remove();
		var fdata = {
					id : $(this).attr('class'),
					ajax : 1
				};
		$.ajax({
			url:'<?php echo site_url('user/removeNote')?>',
			type:'POST',
			data:fdata,
			success : function(msg){
			}
		});
	});
});

</script>	
<?php endif;?>	
</body>
</html> 