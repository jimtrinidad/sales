			<h2 class="annTitle"><?php echo $announce->title?>
				<?php if($isAdmin==1):?>
				<span id="<?php echo $announce->id?>" class="editicon hidden" style="cursor: pointer;">
					<img style="width:18px;height: 18px;" align="top" alt="" src="<?php echo base_url()?>assets/images/edit-icon.png">
				</span>
				<?php endif;?>
			</h2>
			<p><?php echo $announce->content?></p>
			<script type="text/javascript">
			$(document).ready(function(){
				$("#announceNav span").click(function(){
					$("#announceContent").fadeOut('300');
					$.ajax({
						url : $(this).attr("href"),
						type : 'GET',
						success : function(res){
							$("#announceContent").fadeIn('300').html(res);
						}
					});
				});	
			});
			</script>