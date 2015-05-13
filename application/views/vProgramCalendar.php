<link rel="stylesheet" href="<?= base_url();?>assets/css/calendar.css" type="text/css" media="screen" charset="utf-8" />
<!-- <script src="<?= base_url();?>assets/js/coda.js" type="text/javascript"> </script> -->
<script type="text/javascript">
$(document).ready(function(){
	$("#calendarTable th").each(function(){			 
		$(this).addClass("ui-widget-content").css('border','0');			 
	});
	
	$("#calendarTable td").each(function(){
		$(this).addClass("ui-state-default");
	});
	
	$("#calendarTable td").hover(
		function(){
			$(this).addClass("ui-state-hover");
		},
		function(){
			$(this).removeClass("ui-state-hover");
		}
	);

	$("#calendarTable tfoot th a").addClass('button');
	
	//$('#calendarTable tr td:not(:last-child),#calendarTable tr th:not(:last-child)').css('border-right', '0');
	//$('#calendarTable tr td,#calendarTable tr th').css('border-top','0');

	$(".sessionDetails").bind("click",function(){
		var fdata = {
				ajax : 1,
				session_id : this.id
				};
		myDialogBox('<?php echo site_url('schedule/session_details')?>',fdata,'session_details','Session Details',{width : '350'});
	    return false;
	});	

	$('.jumpTime').submit(function(){
		window.location = "<?php echo site_url('schedule/jump_time')?>" + '/' + $('#time_month').val() + '/' + $('#time_year').val();
		return false;
	});	

	$('.button').button();	

	$('.filterResult').click(function(){
		var fdata = {segment : '<?php echo $time?>'};
		myDialogBox('<?php echo site_url('schedule/filter_calendar')?>',fdata,'filter_result','Filter Calendar',{width : '300'});
	});
	
});
</script>
<table cellpadding="0" cellspacing="0" border="0" id="mainTable">
	<tbody>
		<tr>
			<td valign="top" class="content ui-widget-content">
				<table class="homecontenttable" cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td>
							<div class="ui-widget-content">
								<div class="ui-widget-header widget-title" style="text-align: center;font-size: 13px;">
									<?=$current_month_text?>
									<span class="floatright" style="font-weight: normal;font-size: 9px;margin-top: 1px;margin-right: 5px;"><a class="filterResult">filter</a></span>									
								</div>
								<div class="sidebar-content widget-content">
									<div id="calmain">
										<table id="calendarTable" cellspacing="4" class="">
											<thead>
												<tr>
													<th>Sunday</th>
													<th>Monday</th>
													<th>Tuesday</th>
													<th>Wednesday</th>
													<th>Thursday</th>
													<th>Friday</th>
													<th>Saturday</th>
												</tr>
											</thead>
												<tr>
													<?php
													for($i=0; $i< $total_rows; $i++)
													{
														for($j=0; $j<7;$j++)
														{
															$day++;					
															
															if($day>0 && $day<=$total_days_of_current_month)
															{
																//YYYY-MM-DD date format
																$date_form = "$current_year/$current_month/$day";
																
																echo '<td';
																
																//check if the date is today
																if($date_form == $today)
																{
																	echo ' id="today" class=" ui-state-highlight"';
																}
																
																//check if any event stored for the date
																if(array_key_exists($day,$events))
																{
																	//adding the date_has_event class to the <td> and close it
																	echo ' class="date_has_event ui-state-active"> <div class="label">'.$day.'</div>';
																	
																	//adding the eventTitle and eventContent wrapped inside <span> & <li> to <ul>
																	echo '<div class="events"><ul>';
																	
																	foreach ($events as $key=>$event)
																	{
																		if ($key == $day)
																		{
																	  		foreach ($event as $single)
																	  		{	
																	  			//$cssClass = $single->counter > $single->limit ? "alertBlock" : "";
																	  			$cssClass = "";
																				
																				if($single->speaker_counter > 1 AND $single->counter > $single->limit)
																				{
																					$cssClass =  "both_alert ";
																				}
																				elseif($single->counter > $single->limit)
																				{
																					$cssClass =  "venue_alert ";
																				}
																				elseif($single->speaker_counter > 1)
																				{
																					$cssClass =  "speaker_alert ";
																				}
																																									
																				echo  '<li>';																				
																				echo '<a class="sessionDetails '.$cssClass.'" id="'.$single->session_id.'"><span class="title" title="'.$single->name.'">'.$single->title.' '.$single->batch.'</span></a>';
																				echo '</li>'; 
																			} // end of for each $event
																		}
										  								
																	} // end of foreach $events
										
																	echo '</ul></div>';
																} // end of if(array_key_exists...)
																else 
																{
																	//if there is not event on that date then just close the <td> tag
																	echo '> <div class="label">'.$day.'</div>';
																}
																echo "</td>";
															}
															else 
															{
																//showing empty cells in the first and last row
																echo '<td class="padding">&nbsp;</td>';
															}
														}
														echo "</tr><tr>";
													}
													
													?>
												</tr>
										
											<tfoot>	
												<tr>	
													<th colspan="7">
													<?php echo anchor('schedule/calendar/'.$previous_year,'&laquo;&laquo;', array('title'=>$previous_year_text));?>
													<?php echo anchor('schedule/calendar/'.$previous_month,'&laquo;', array('title'=>$previous_month_text));?>
													<?php echo anchor('schedule/calendar/'.$next_month,'&raquo;', array('title'=>$next_month_text));?>
													<?php echo anchor('schedule/calendar/'.$next_year,'&raquo;&raquo;', array('title'=>$next_year_text));?>													
													<form class="jumpTime" style="display: inline;">
														<div class="fadeTextSmall" style="display: inline;padding: 0 3px 0 10px;">Jump</div>
														<select id="time_month" style="padding: 1px 0;">
															<?php foreach($months as $k=>$v):?>
															<option value="<?php echo $k?>" <?php echo $current_month == $k ? 'selected="selected"' : ''?>><?php echo $v?></option>
															<?php endforeach;?>
														</select>
														<select id="time_year"  style="padding: 1px 0;">
															<?php for($i = 2010;$i <= 2050;$i++):?>
															<option value="<?php echo $i?>" <?php echo $current_year == $i ? 'selected="selected"' : ''?>><?php echo $i?></option>
															<?php endfor;?>
														</select>
														<button type="submit" class="button">Go</button>
													</form>
													</th>
												</tr>		
											</tfoot>
										</table>
									</div>
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