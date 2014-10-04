<?php
/**
 * 單一上傳
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_SingleUpload extends Zend_View_Helper_Abstract
{
    /**
     * 初始化標記
     *
     * @var boolean
     */
    protected $_initalize = false;
    
    public function singleUpload($name, $value, $attr)
    {
        if(!$this->_initalize) {
            $this->_setup();
        }
        
        $attr['class'] = 'file';
        $attr['data-uploader'] = $name . 'Uploader';
        
        $file =  $this->view->formFile($name, $attr);
        
        $html = <<<HTML
        <button id="{$name}Uploader" type="button" class="uiButton uiButtonIcon upload"><span><img src="/img/icons/14x14/upload3.png"></span>上傳</button>
HTML;
        if(!$value) {
            $value = '/img/noimage.png';
        }
        
        $html .= '<div style="margin-top:10px"><img src="' . $value . '" class="img-polaroid" id="' . $name . 'Downloader" /></div>';
        $html .= <<<HTML
        <div class="progress progress-striped hide" id="{$name}UploaderProgressbar">
            <div class="bar" style="width: 10%;"></div>
        </div>   
HTML;
        
        return $file . $html;
    }
    
    protected function _setup()
    {
        $js = <<<'JS'
       $(function(){
        $('.file').fileupload({
    		done: function (e, data) {
    		  
    			if(data.result.error == 0){
    				successMsg('上傳完成');
    			} else {
    				errorMsg(data.result.message);
    			}
    
    			$(data.fileInput).val('');
    			var $progressbar = ObGetProgressbar(data.fileInput);
    			var $uploader = ObGetUploader(data.fileInput);
    			$uploader.unbind('click');
    			$progressbar.fadeOut();
        
                var _name = data.fileInput.attr('name');
                var $downloader = $("#" + _name + "Downloader");
                $downloader.attr('src', data.result.file); 
                
    		},
    		add: function (e, data) {
                var $uploader = ObGetUploader(e.target);
                $uploader.unbind('click').bind('click', function(){
                    data.submit();
    			});
            },
    		progressall: function (e, data) {
                var $progressbar = ObGetProgressbar(e.target);
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $progressbar.show().find('.bar').css('width', progress + '%');
        	},
        	dropZone  : null,
        	pasteZone : null,
        	replaceFileInput : false
    	});
    	
    });
    
    function ObGetUploader(fileInput) {
        var _uploaderId = $(fileInput).attr('data-uploader');
        return $("#" + _uploaderId);
    }
    
    function ObGetProgressbar(fileInput) {
        var _id = $(fileInput).attr('data-uploader');
        return $("#" + _id + "Progressbar");
    }
JS;
        
        $this->view->headScript()->appendFile('/js/fileupload/jquery.iframe-transport.js');
        $this->view->headScript()->appendFile('/js/fileupload/jquery.fileupload.js');
        $this->view->headScript()->appendFile('/js/fileupload/jquery.fileupload-fp.js');
        $this->view->headScript()->appendFile('/js/fileupload/jquery.fileupload-ui.js');
        
        $this->view
             ->headScript()
             ->appendScript($js);
        
        $this->_initalize = true;
    }
}
?>