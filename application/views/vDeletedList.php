<script type="text/javascript">
    $(document).ready(function(){
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
    });
</script>
<div class=ui-widget-content>
	<div class="ui-widget-header widget-title">Results</div>
	<div class="sidebar-content widget-content" id="resultdiv" style="min-height: 300px;">
		<div class="accordionDiv accordionmember members-list notvisible">
		<?php 
			foreach ($results as $value):
		?>						
				<h3>
					<a class="user-name" id="mlist_<?php echo $value['name']?>">					
					 	<span class="name">
                                                    <?php echo $value['name']?>
					 	</span>
					</a>
				</h3>
				<div>
                                        <?php if(count($value['records']) > 0):?>
					<table class="tableList transactionTable" width="100%" cellpadding="0" cellspacing="0" style="border: 0;-moz-box-shadow: none;-webkit-box-shadow: none;box-shadow: none;">
						<thead>
							<tr>
								<th width="30px">#</th><th>Date deleted</th><th>Name</th><th>Type</th><th>Program</th><th>Batch</th><th>Restored</th>
							</tr>
						</thead>
						<tbody id="tb">	
						<?php $c=1;?>
						<?php foreach ($value['records'] as $d):?>
							<tr id="trover">
								<td style="font-size:11px;"><?php echo $c;?> )</td>
								<td><?php echo date("M j, Y / g:i A",strtotime($d['date_deleted']))?></td>
								<td><?php echo $d['lastname'].", ".$d['firstname'] ?></td>
								<td><?php echo $d['event_type']?></td>
								<td title="<?php echo $d['name'] ?>"><?php echo $d['title'] ?></td>
								<td><?php echo $d['batch']?></td>
								<td><?php echo is_null($d['restored_date']) ? 'No' : date("M j, Y / g:i A",strtotime($d['restored_date']))?></td>
							</tr>
							<?php $c++; ?>
						<?php endforeach;?>
						</tbody>					
					</table>
                                        <?php else:?>
                                            No deleted won records
                                        <?php endif;?>
				</div>
			<?php endforeach;?>
			<?php if(count($results)==0):?>
				<div class="no-record-notice-div" style="padding: 5px 0;margin: 5px 0;float: left;">
					<span style="margin: 10px;font-size: 14px;">No records found...</span>
				</div>										
			<?php endif;?>
		</div>			
</div>
