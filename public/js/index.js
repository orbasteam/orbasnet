$(function(){

	$("#board-form").ajaxForm({
		beforeSend : function(){
			$("#submit").button('loading');
		},
		success : function(response, statusText, xhr, element){
			if(response.error == 1) {
				Orbas.message('error', response.message);
			} else {
				refreshBoard();
				$("#CONTENT").val('').change();
				$(".gallery").empty();
			}

			$("#submit").button('reset');
		},
		dataType : 'json'
	});

	refreshBoard(function(){
		$("#board").jscroll({
		    loadingHtml: '<img src="/img/loading.gif" alt="Loading" /> Loading...',
		    padding: 20,
		    callback : function() {
		    	pluginLoad();
		    }
		});
	});

	$(document).on('mouseenter', '.media', function(){
		var _sn = $(this).attr('data-sn');
		$("#board-action-" + _sn).show();
	}).on('mouseleave', '.media', function(){
		var _sn = $(this).attr('data-sn');
		$("#board-action-" + _sn).hide();
	});

    $("#board-img-upload").fileupload({
        
    	add: function (e, data) {
            $("#board-progress").show();
            data.submit();
        },
        
        progressall: function (e, data) {

            var progress = parseInt(data.loaded / data.total * 100, 10);
            $("#board-progress .bar").css('width', progress + '%');

            if(progress >= 100) {
            	window.location.reload();
            	/*setTimeout(function(){
            		$("#board-progress").hide();
            	}, 1000);*/
            }
    	},
        done: function (e, data) {
            var response = $.parseJSON(data.jqXHR.responseText);

            if(response.error == 1) {

            	if(response.message instanceof Object) {
                	var _string = '';
                	$.each(response.message, function(i, val){
                    	_string += val;
                    	_string += '<br />';
                    });
            	} else {
                	_string = response.message;
            	}
            	
            	Orbas.message('error', _string);
            } else {
            	
            }
        }
    });


    $(".del-gallery-pic").click(function(e){

    	var $el = $(this);
    	var _fileName = $(this).prev('a').attr('href').split('/').reverse()[0];
    	$.ajax({
    		type : 'delete',
    		url  : '/default/index/remove.picture',
    		data : {
    			filename : _fileName
    		},
    		dataType : 'json'
    	}).done(function(data){
 		    if(data.error == 0) {
 		    	e.preventDefault();
 		        var $parent = $el.parents("li");
 		        $parent.fadeOut(400, function(){
 		            $parent.remove();
 		        });
 		    } else {
 	 		    Orbas.message('error', data.message);
 		    }
    	});
    });

    $("#shorten-url-submit").click(function(){

    	var $this = $(this);
    	var _url = $(this).prev('input').val();

    	$this.closest('.control-group').removeClass('error').find('.error.help-block').addClass('hide');
    	$this.button('loading');
    	
    	var _data = { url : _url};
        $.postq('orbas', '/default/shorten.url/url', _data, function(response){
            if(response.error) {
            	$this.closest('.control-group').addClass('error').find('.error.help-block').removeClass('hide').text(response.error.message);
            } else {

                $("#shorten-url-transformed").val(response.id).closest('.control-group').removeClass('hide');
                $("#shorten-url-transformed")[0].select();
            }

            $this.button('reset');
            
        }, 'json');
    });
});

function pullMessage() {
    $.getJSON('/default/inform/pull.message', function(response){

    	if(typeof(response) == 'object') {
    	    messageInform(response.count);
    	}
    	
    }).always(function(){
    	pullMessage();
    });
}

function showReply(index) {
    var $reply = $("#reply-" + index);

    $reply.toggle();

    if($reply.is(":visible")) {
    	replyLoad(index);
    }
}

function replyLoad(index) {

    var $area = $("#reply-" + index).find('.reply-all');
	
	$area.html('<img src="/img/loading.gif" />');
	$.getq('orbas', '/default/reply/list/board_sn/' + index, function(html){
		$area.html(html);
    	pluginLoad();
    });
}

function refreshBoard(callback) {
	
	$.getq('orbas', '/default/board/list', function(html){
		$("#board").html(html);
		pluginLoad();

		if(typeof(callback) == 'function') {
			callback();
		}
	});
}

function pluginLoad() {
	//prettyPrint();
	URL2Link();

    $("textarea").each(function(){

    	if($(this).data('plugin') != true) {
        	
    		$(this).expandingTextarea();
    		$(this).tabby({
 			   tabString : '    '
			});

			$(this).data('plugin', true);
    	}
    });
}

function URL2Link() {
	var YUD = YAHOO.util.Dom;
	var YUE = YAHOO.util.Event;
	YUE.onDOMReady(function () { 
	  var dMsgs = YUD.get('board');
	  updateURL2Link(dMsgs, {target:'_blank'});
	});
}

function ajaxReply(obj) {
	var sn = $(obj).attr('data-sn');
	var content = $("#reply-textarea-" + sn ).val();

	$("#reply-textarea-" + sn).val('');
	$(obj).button('loading');
	
	$.postq('orbas', '/default/board/submit', {'BOARD_SN' : sn, 'CONTENT' : content}, function(response){

		if(response.error == 1) {
			Orbas.message('error', response.message);
		} else {
			Orbas.message('success', '已送出留言');
			replyLoad(sn);
			$("#reply-textarea-" + sn ).change(); 
		}

		$(obj).button('reset');
		
	}, 'json');
}

function deleteContent(boardSN) {
	$.ajaxq('orbas', {
		type : 'delete',
		url  : '/default/board/remove',
		data : {
			board_sn : boardSN
		},
		dataType : 'json'
	}).done(function(data){

		if(data.error == 1) {
			Orbas.message('Error', data.message);
		} else {
			var _html = '留言已刪除 <button class="btn btn btn-lightred" onclick="recoveryContent(' + boardSN + ')"><i class="icon-reply"></i> <span>復原</span></button>';
			Orbas.message('Info', _html, 'Info', {positionClass : 'toast-bottom-full-width'});
			
			$("#board-" + boardSN).fadeOut();
		}
		
	});
}

function recoveryContent(boardSN) {
	$.postq('orbas', '/default/board/recovery', {board_sn : boardSN}, function(data){

		if(data.error == 1) {
			Orbas.message('Error', data.message);
		} else {
			$("#board-" + boardSN).fadeIn();
		}
		
	}, 'json');
}