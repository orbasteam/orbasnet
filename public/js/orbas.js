var Orbas = function () {

	var toastrOptions = {
		"closeButton": true,
		"debug": false,
		"positionClass": "toast-top-full-width",
		"onclick": null,
		"showDuration": "1000",
		"hideDuration": "1000",
		"timeOut": "10000",
		"extendedTimeOut": "3000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};
	
    // public functions
    return {

        message : function(type, msg, title, option) {
    
        	if(title == undefined) title = type;
        	type = type.toLowerCase();

        	toastr.options = $.extend({}, toastrOptions, option);
        	toastr[type](msg, title);
        },
    
    	like : function(obj) {
    		
    		var boardSN = $(obj).attr('data-sn');
    		
    		$.getJSON('/default/like/like/boardSN/' + boardSN, function(response){

    			if(response.on == 1) {
    			    $(obj).addClass('btn-red');
    		    } else {
    			    $(obj).removeClass('btn-red');
    		    }

    		    $(obj).find('span').text(response.count);
    			
    		});
    	},
    	
    	dislike : function(obj) {
    		var boardSN = $(obj).attr('data-sn');
    		$.getJSON('/default/like/dislike/boardSN/' + boardSN, function(response){
    			if(response.on == 1) {
    			    $(obj).addClass('btn-red');
    		    } else {
    			    $(obj).removeClass('btn-red');
    		    }

    		    $(obj).find('span').text(response.count);
    		});
    	},
    	
    	textareaKeyup : function(obj, submitBtnId){
    		
    		if($(obj).val() != '') {
		    	$("#" + submitBtnId).prop('disabled', false);
		    } else {
		    	$("#" + submitBtnId).prop('disabled', true);
		    }
    	},
    	
    	notification : function(message, callback) {
    		
    		if(!window.Notification) {
    			return;
    		}
    		
    		var notification = new window.Notification('Orbas 通知', {
    			icon : 'img/logo.png',
    			body : message
    		});
			
	        if(typeof(callback) == 'object') {
	        	$.each(callback, function(i, val){
	        		if(typeof(val) == 'function') {
	        			notification[i] = val;
	        		}
	        	});
	        }
    	}
    };

}();