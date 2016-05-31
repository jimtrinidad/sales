<script type="text/javascript">
$(document).ready(function(){
	$('#trigger').click(function(){
		var $type = $("#group_type");
		var fdata = {
					group_type : $type.val(),
                                        date_from : $('#dateFrom').val(),
                                        date_to : $('#dateTo').val(),
                                        months : $('#month_val').find('option:selected').attr('value'),
					graph_only : 1
				};
		showMyLoader();
		$.ajax({
			url		: '<?php echo site_url('main/total_graph_big')?>',
			data	: fdata,
			type	: 'POST',
			success	: function(content){
					$('#graph_div').html(content);
					$('.editor-header .title-type').html($type.val());
					hideMyLoader();
				}
		});
	});
        
        $("#group_type").change(function(){
            var $type = $(this).val();
            if($type == 'YTD'){
                $('span.range_calendar').hide();
                $('span.months').show();
            }else {
                $('span.range_calendar').show();
                $('span.months').hide();
            }
        });
        
        $('.date').datepicker({
            changeMonth: true,
            changeYear: true
        });
});
</script>
<div id="" class="contentEditor" style="width: 1200px;">
	<div class="editor-header" >
		<span class="title-type">Weekly</span> Total Sales
                <span class="floatright" style="vertical-align: top">
			<select id="group_type" style="padding: 1px 2px;font-size: 10px;vertical-align: top">
				<option value="Weekly">Weekly</option>
				<option value="Monthly">Monthly</option>
				<option value="Yearly">Yearly</option>
                                <option value="YTD">YTD</option>
			</select>
                    <span class="range_calendar" style="vertical-align: top">
			From
			<input class="date" type="text" id="dateFrom" value="<?php echo date('m/d/Y', strtotime(date('Y') . '-07-07')) ?>"  style="width: 60px;padding: 2px 2px;font-size: 10px;">
			To
			<input class="date" type="text" id="dateTo" value="<?php echo date('m/d/Y', strtotime(NOW)) ?>" style="width: 60px;padding: 2px 2px;font-size: 10px;">                        
                    </span>
                    <span class="months hidden">
                        <select id="month_val" style="padding: 1px 2px;font-size: 10px;vertical-align: top">
                            <?php
                            foreach($months as $k=>$v){
                                echo "<option value='{$k}'>{$v}</option>";
                            }
                            ?>
                        </select>
                    </span>
                    <input type="button" id="trigger" value="Go" style="padding: 0px 2px;font-size: 10px;vertical-align: top">
		</span>
	</div>
	<div class="editor-content ui-widget-content" id="graph_div" style="min-height: 200px;">
		<?php echo $graph?>
	</div>
</div>