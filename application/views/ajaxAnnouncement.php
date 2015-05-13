			<h2 class="annTitle"><?php echo $announce->title?>
				<?php if($isAdmin==1):?>
				<span id="<?php echo $announce->id?>" class="editicon hidden" style="cursor: pointer;">
					<img style="width:18px;height: 18px;" align="top" alt="" src="<?php echo base_url()?>assets/images/edit-icon.png">
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
			<?php if($isAdmin==1):?>
				<div id="announceNew"><a href="javascript:void()" class="newann">New</a> </div>
			<?php endif;?>
			
			<script type="text/javascript">
			$(document).ready(function(){
				$("#announceNav span").click(function(){
					$("#announceContent").fadeOut('300');
					$.ajax({
						url : $(this).attr("href"),
						type : 'GET',
						success : function(res){
							$("#announcedetailsholder").fadeOut('1000');
							$("#announceContent").fadeIn('300').html(res);
						}
					});
				});	
			});
			</script>