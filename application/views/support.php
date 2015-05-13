<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Tools</title>
<script src="<?php echo base_url()?>assets/js/jquery-1.6.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/jquery-ui-1.8.12.min.js" type="text/javascript" charset="utf-8"></script>

<link type="text/css" href="<?php echo base_url()?>assets/css/jqueryslidemenu.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/jquery-ui.css" rel="stylesheet" />

<style>
    .styleTable { border-collapse: separate; }
    .styleTable TD { font-weight: normal !important; padding: .4em; border-top-width: 0px !important; }
    .styleTable TH { text-align: center; padding: .8em .4em; }
    .styleTable TD.first, .styleTable TH.first { border-left-width: 0px !important; }
    
    .ui-datepicker {
        font-size: 10px;
     }
</style>

<script type="text/javascript">
    (function ($) {
        $.fn.styleTable = function (options) {
            var defaults = {
                css: 'styleTable'
            };
            options = $.extend(defaults, options);

            return this.each(function () {

                input = $(this);
                input.addClass(options.css);

                input.find("tr").live('mouseover mouseout', function (event) {
                    if (event.type == 'mouseover') {
                        $(this).children("td").addClass("ui-state-hover");
                    } else {
                        $(this).children("td").removeClass("ui-state-hover");
                    }
                });

                input.find("th").addClass("ui-state-default");
                input.find("td").addClass("ui-widget-content");

                input.find("tr").each(function () {
                    $(this).children("td:not(:first)").addClass("first");
                    $(this).children("th:not(:first)").addClass("first");
                });
            });
        };
    })(jQuery);

$(document).ready(function(){
    $('select.program_select').change(function(){
        $.ajax({
            url : '<?php echo site_url('support/get_program_batch')?>',
            type : 'POST',
            data : {program_id : this.value},
            beforeSend: function(){
                $('div.batch_container').html('Loading...');
            },
            success : function($ret){                
                var $json = $.parseJSON($ret);
                var $markup = '<table class="styleTable"><tr><th>Batch</th><th>Start Date</th><th>End Date</th><th></th></tr>';
                $.each($json, function(){
                    $markup += '<tr><td><b> Batch ' + this.batch + '</b></td>' +
                                '<td></label><input class="date" type="text" id="'+this.schedule_id+'" value="'+this.start_date+'"></td>' +
                                '<td>'+this.end_date+'</td>' + 
                                '<td><button type="button" class="save">Save</td>' +
                                '</tr>';
                });
                $markup += '</table>';
                $('div.batch_container').html($markup);
                
                $('.date').datepicker({dateFormat: 'yy-mm-dd'});
                $(".styleTable").styleTable();
            }
        });
    }); 
    
    $('button.save').live('click', function(){
        var $btn = $(this);
        $btn.text('Saving...');
        var $parent = $btn.closest('tr');
        var $field = $parent.find('input');
        $.ajax({
            url : '<?php echo site_url('support/save_start_date')?>',
            type : 'POST',
            data : {schedule_id : $field.attr('id'), start_date : $field.attr('value')},
            success : function(){
                
            },
            error : function(){
                alert('Error on saving..please try again.');
            },
            complete : function(){
                $btn.text('Save');
            }
        });
    })
});
</script>
</head>

<body>
	Select Program
        <select class="program_select">
            <option>Select Program</option>
            <?php
            foreach($programs as $program){
                echo "<option value='{$program['id']}'>{$program['name']} ({$program['title']})</option>";
            }
            ?>
        </select>
        <div class="batch_container">
            
        </div>
</body>
</html> 