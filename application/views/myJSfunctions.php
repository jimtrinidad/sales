<script type="text/javascript">

function showMyLoader()
{
	$("#modal").removeClass('hidden');
	$("#div-loader").removeClass('hidden');
}
function hideMyLoader()
{
	$("#modal").addClass('hidden');
	$("#div-loader").addClass('hidden');	
}

function myDialogBox($location,$data,$element_id,$title,$options)
{
    var dialog = $( "#dialog-" + $element_id );
    if ($("#dialog-" + $element_id).length == 0) {
        dialog = $('<div id="dialog-' + $element_id + '" style:padding-top:15px;></div>').appendTo('body');
    }

    var defaults = {
            zIndex:1001,
            resizable: false,
            width : '300',
            minHeight : '80',	               
            modal: true,
            dialogClass: 'dialogWithDropShadow',
            position: ['auto','auto'],
            open: function (){ 
                	$('.ui-widget-overlay').css('opacity','0.6').css('background','#777').css('width','100%');
	                $('.ui-dialog').css('paddingLeft','0').css('paddingRight','0').removeClass('ui-corner-all').css('borderTop','0').css('overflow','visible');
	                $('.ui-dialog-titlebar').css('padding','0').css('marginTop','-3px').css('border','0');
	                $('#ui-dialog-title-dialog-' + $element_id).css('width','100%').css('minWidth','300px').css('margin','0').html('<div class="contentEditor" style="width:auto;"><div class="editor-header" >' + $title + '</div></div>');
	                $('.ui-dialog-titlebar-close').css('margin','0').css('padding','1px').css('z-index','1005').css('right','-9px').css('top','-9px').css('background','url("<?php echo base_url()?>assets/images/fancy_close.png") no-repeat scroll 0 0 transparent').hover(function(){$(this).removeClass('ui-state-hover');}).find('span').css('background','none');
            }
        
        };
     var settings = $.extend({}, defaults, $options);

	showMyLoader();	
	$.ajax({
		url : $location,
		data : $data,
		type : 'POST',
		success : function (responce){
				dialog.html(responce).dialog(settings);				
	            hideMyLoader();
			}
	});	
}

function ajaxCallBoxOpen(location,fdata)
{
	//var fdata = {ajax	:'1'};
	showMyLoader();
	$("#fancybox-content").html(' ');
	$.ajax({
		url: location,
		type: 'POST',
		data: fdata,
		success: function(data){
			lightBox(data);
			$("#fancybox-content").find("input:visible[value='']:enabled:first").focus();
		},error: function(){myMessageBox('<p>Unable to open page. Please reload and try again.</p>','Content Error','red');}
	});	
}

function toObject(arr) {
	  var obj = {};
	  for (var i = 0; i < arr.length; ++i)
	    if (arr[i] !== undefined) obj[i] = arr[i];
	  return obj;
	}

function lightBox(data)
{
	$.fancybox({
		'padding'			: 0,
		'overlayOpacity'	: .7,
		'centerOnScroll'	: false,
		'autoScale'			: true,
		'autoDimensions'	: true,
		'hideOnOverlayClick': false,
		'content' 			: data,
		'speedIn'			: 100,
		'speedOut'			: 100,
		'onComplete'		:	function(){
									if($('.editor-header:visible').length != 0){
										$("#fancybox-outer").css('backgroundColor','transparent').draggable({handle : '.editor-header'});
									}
									$.fancybox.center();
									hideMyLoader();
								},
		'onClosed'			: 	function(){
									if(CKEDITOR.instances['message'] && $.browser.webkit){ // kasi ayaw mag pa destroy ng DOM sa chrome!!
										//showMyLoader();
										//location.reload(); 
									}
								}
	});		
}


function myMessageBox(content,title,color,callBack)
{
	$("#fancybox-content input").blur();
	$("#div-loader").addClass('hidden');
	$("#modal").removeClass('hidden');
	var msg = $("#mymessagebox");
	$("#mymessagebox .message-content").html(content);
	$("#mymessagebox .header .title").html(title).css('color',color);
	$("#mymessagebox .closebutton").button();
	
	var w = '-'+ ($("#mymessagebox").width()/2) +'px';
	var h = '-'+ ($("#mymessagebox").height()/2) +'px';
	
	msg.css('margin-left',w)
	.css('margin-top',h)
	.draggable({handle:'.header'})
	.removeClass('hidden');

	$(document).one("keydown",function(e) {
	    if(e.keyCode == 13) { //enter
	    	$("#mymessagebox .closebutton").click();
	    	return false;
	    }
	});
	$(document).one("keydown",function(e) {
	    if(e.keyCode == 27) { // esc
	    	$("#mymessagebox .header-close,#mymessagebox .closebutton").click();
	    	return false;
	    }
	});	
	
	$("#mymessagebox .header-close,#mymessagebox .closebutton").click(function(){
		$("#modal").addClass('hidden');$("#mymessagebox").addClass('hidden');
		if($.isFunction(callBack)){
			callBack.apply();
			callBack = false;
		}
		$("#fancybox-content").find("input:visible[value='']:enabled:first").focus();
	});
}

function myConfirmBox(title,content,callBack,yes,no,reload)
{
	$("#fancybox-content input").blur();
	$("#div-loader").addClass('hidden');
	$("#modal").removeClass('hidden');
	var msg = $("#myConfirmBox");
	$("#myConfirmBox .message-content").html(content).css('max-width','500px');
	$("#myConfirmBox .header .title").html(title);
	$("#myConfirmBox .closebutton").button();
	$("#myConfirmBox #yes").html(yes?yes:'Yes');
	$("#myConfirmBox #no").html(no?no:'No');
	
	var w = '-'+ ($("#myConfirmBox").width()/2) +'px';
	var h = '-'+ ($("#myConfirmBox").height()*2) +'px';
	
	msg.css('margin-left',w)
	.css('margin-top',h)
	.draggable({handle:'.header'})
	.removeClass('hidden').focus();

	$(document).one("keydown",function(e) {
	    if(e.keyCode == 27) { // esc
	    	$("#myConfirmBox .header-close,#myConfirmBox #no").click();
	    	return false;
	    }
	});					

	$("#myConfirmBox .header-close,#myConfirmBox #no").one("click",function(){
		$("#modal").addClass('hidden');
		$("#myConfirmBox").addClass('hidden');
		callBack = false;
		if(reload){
			location.reload();
		}
	});

	$("#myConfirmBox #yes").one("click",function(){
		$("#modal").addClass('hidden');
		$("#myConfirmBox").addClass('hidden');
		if($.isFunction(callBack)){
			callBack.apply();
			callBack = false;
		}		
	});
}
</script>