<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if(!class_exists('Joomd'))
	require_once(JPATH_SITE.'/components/com_joomd/libraries/core.php');

class Joomdui
{
	
	var $_id = null;
	
	var $_params = array();
	
	//Loads the Joomd html class
	public static function getHTML()
	{
		
		if(!class_exists('HTML'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/html.php');
	
		$html = new HTML();
		
		return $html;
		
	}
	
	//Loads the javascript sortable class
	
	public static function getSortable()
	{
		
		if(!class_exists('Sortable'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/sortable.php');
		
		$sortable = new Sortable();
		
		return $sortable;
		
	}
	
	//Loads the javascript accordion class
	
	public static function getAccordion()
	{
		
		if(!class_exists('Accordian'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/accordion.php');
		
		$accordion = new Accordion();
		
		return $accordion;
		
	}
	
	//Loads the javascript Tabs class
	
	public static function getTabs()
	{
		
		if(!class_exists('Tabs'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/tabs.php');
		
		$tabs = new Tabs();
		
		return $tabs;
		
	}
	
	//Loads the javascript tooltip class
	
	public static function getTooltip()
	{
		
		if(!class_exists('Tooltip'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/tooltip.php');
		
		$tooltip = new Tooltip();
		
		return $tooltip;
		
	}
	
	//Loads the javascript datepicker class
	
	public static function getDatepicker()
	{
		
		if(!class_exists('Datepicker'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/datepicker.php');
		
		$datepicker = new Datepicker();
		
		return $datepicker;
		
	}
	
	//Loads the javascript draggable class
	
	public static function getDraggable()
	{
		
		if(!class_exists('Draggable'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/draggable.php');
			
		$draggable = new Draggable();
		
		return $draggable;
		
	}
	
	//Loads the javascript autocomplete class
	
	public static function getAutocomplete()
	{
		
		if(!class_exists('Autocomplete'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/autocomplete.php');
		
		$autocomplete = new Autocomplete();
		
		return $autocomplete;
		
	}
	
	public static function getDialog()
	{
		
		if(!class_exists('dialog'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/dialog.php');

		$dialog = new dialog();

		return $dialog;

	}
	
	//Loads the javascript sortable class
	
	public static function getMultiselect()
	{
		
		if(!class_exists('Multiselect'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/multiselect.php');
		
		$multiselect = new Multiselect();
		
		return $multiselect;
		
	}
	
	public static function getEditor()
	{
		
		if(!class_exists('Editor'))
			require_once(JPATH_SITE.DS.'components/com_joomd/libraries/html/editor.php');
		
		$editor = new Editor();
		
		return $editor;
		
	}
	
	//Loads the javascript button class
	public static function iButton($id, $params=array())
	{
		
		$doc = JFactory::getDocument();
		
		//button type
		$params['type'] = isset($params['type'])?$params['type']:'radio';
		
		//button type
		$params['checked'] = isset($params['checked'])?$params['checked']:false;
		
		//set the on text
		$params['labelOn'] = isset($params['labelOn'])?$params['labelOn']:JText::_('YS');
		
		//set the off text
		$params['labelOff'] = isset($params['labelOff'])?$params['labelOff']:JText::_('NS');
		
		$doc->addScript(JURI::root().'components/com_joomd/assets/js/jquery.ibutton.js');
		
		$doc->addStyleSheet(JURI::root().'components/com_joomd/assets/css/jquery.ibutton.css');
		
		$js = '$jd(function() {';
		
		$js .= '$jd("'.$id.'").iButton({
					labelOn: "'.$params["labelOn"].'"
			 		, labelOff: "'.$params["labelOff"].'"
				});';
		
		$js .= '});';
		
							   
		$doc->addScriptDeclaration($js);
		
	/*	$html = '<input type="'.$params['type'].'" name="'.$params['name'].'"';
		
		if($params['checked'])
			$html .= ' checked="checked"';
		
		$html .= ' />';
		
		return $html;*/
		
	}
	
	//Ajax calls are made through this function
	public static function ajax($params = array())
	{
		
		$doc = JFactory::getDocument();
		
		//set the url to call default this.href
		$params['element'] = isset($params['element'])?$params['element']:null;
		
		//set true to prevent default operation of the element
		$params['event'] = isset($params['event'])?$params['event']:'click';
		
		//set the url to call default this.href
		$params['url'] = isset($params['url'])?$params['url']:'';
		
		//set true to prevent default operation of the element
		$params['preventDefault'] = isset($params['preventDefault'])?$params['preventDefault']:false;
		
		//set true to set asynchronous mode
		$params['async'] = isset($params['async'])?$params['async']:false;
		
		//set the request type
		$params['type'] = isset($params['type'])?$params['type']:'post';
		
		//set datatype to get the response in
		$params['dataType'] = isset($params['dataType'])?$params['dataType']:'html';
		
		//data to send if any
		$params['data'] = isset($params['data'])?$params['data']:'';
		
		//set to true if want to block ui during the request/response
		$params['uiblock'] = isset($params['uiblock'])?$params['uiblock']:'';
		
		//id to display the loading icon
		$params['before'] = isset($params['before'])?$params['before']:'';
		
		//function to call after success
		$params['after'] = isset($params['after'])?$params['after']:'';
		
		$js = '$jd(function() {';
			
		if($params['element'])	{
			$js .= '$jd("'.$params['element'].'").live("'.$params['event'].'", function(event)	{';
			
			if($params['preventDefault'])
				$js .= 'event.preventDefault();';
				
		}
		
			
			$js .= '$jd.ajax({';
			
			if($params['url'] == "")
				$js .= 'url: this.href';
			else
				$js .= 'url: "'.$params['url'].'"';
		
			$js .= ', type: "'.$params['type'].'"';
			$js .= ', dataType: "'.$params['dataType'].'"';
			
			if($params['async'])
				$js .= ', async: true';
			
			if($params['type'])
				$js .= ', type: "'.$params['type'].'"';
				
			if($params['data'] <> "")
				$js .= ', data: '.$params['data'].'';
			
			if($params['blockui'])	{
				
				$js .= ', beforeSend: function(){ 
					$jd.blockUI({
					message: \'<img src="'.JURI::root().'/components/com_joomd/assets/images/loading.gif" border="0" width="16" alt="loading.." />\',
					css: { 
						border: "none", 
						padding: "15px", 
						backgroundColor: "#000", 
						opacity: .6, 
						color: "#fff" 
					}
				});
				}';
			}
				
			elseif($params['before'] <> "")
				$js .= ', beforeSend: function(){ $jd("'.$params['before'].'").html(\'<img src="'.JURI::root().'/components/com_joomd/assets/images/loading.gif" alt="loading.." />\'); }';
				
			if($params['after'] <> "" or $params['before'] <> "" or $params['blockui'])	{
				
				$js .= ', success: function(res){';
				
				if($params['blockui'])
					$js .= '$jd.unblockUI();';
				elseif($params['before'] <> "")
					$js .= '$jd("'.$params['before'].'").html("");';
					
				if($params['after'] <> "")
					$js .= $params['after'];

				$js .= '}';
			
			}
			
			$js .= '});';
			
			if($params['element'])
				$js .= '
				});';
		
		$js .= '});';
		
		$doc->addScriptDeclaration($js);
	
	}
	
	public static function toggleClass( $id, $buttonId, $params = array() )
	{
		
		$doc = JFactory::getDocument();
		
		$this->_id = $id;
		//Fixes display duration.
		$params['displaytime']		= isset($params['displaytime'])?$params['displaytime']:1000;
		
		//Fixes callback duration.
		$params['callbacktime']	= isset($params['callbacktime'])?$params['callbacktime']:1500;
		
		//Enables callback functionality.
		$params['callback']	    = isset($params['callback'])?'callback':'';
	
		$this->_params = $params;
				
		$js = '$jd(function() {
				
				$jd( "#'.$buttonId.'" ).click(function(){';
                 if($params['callback'] <> ''){
				 $js .='$jd( "#'.$id.'" ).toggleClass( "newClass",'.$params['displaytime'].','.$params['callback'].' );
			           return false;';
		           }
				 else{
	            $js .='$jd( "#'.$id.'" ).toggleClass( "newClass",'.$params['displaytime'].' );
			           return false;';
				   }
		        $js .= '});';

	            $js .='function callback(){
			        setTimeout(function() {
                    $jd( "#'.$id.'"  ).removeClass( "newClass" );
			          }, '.$params['callbacktime'].' );
		           }';

		$js .='});';
		
		$doc->addScriptDeclaration($js);
		
	}
	
	public static function effect( $id, $buttonId, $params = array() )
	{
		
		$doc = JFactory::getDocument();
		
		//Fixes display duration.
		$params['displaytime']	= isset($params['displaytime'])?$params['displaytime']:1000;
		//Fixes callback duration.
		$params['callbacktime']	= isset($params['callbacktime'])?$params['callbacktime']:1000;
		//Enables callback functionality.
		$params['callback']	    = isset($params['callback'])?'callback':'';
	    //Fixes effect type you want. like drop, explode, blind etc
		$params['selectedEffect']	= isset($params['selectedEffect'])?$params['selectedEffect']:'drop';
		
		//element to send into for transfer
		$params['to']	= isset($params['to'])?$params['to']:$buttonId;
		
		//percent to which the element is to scale in scale effect
		$params['percent']	= isset($params['percent'])?$params['percent']:0;
		
		//width for size effect
		$params['width']	= isset($params['width'])?$params['width']:200;
		$params['height']	= isset($params['height'])?$params['height']:40;
			
		$file = $params['selectedEffect'];
		
		if($params['selectedEffect'] == 'size')
			$file = 'scale';
		
		loader::effect($file);
				
		$js = '$jd(function() {
				
				$jd( "'.$buttonId.'" ).live("click", function(){
					
					$jd(this).toggleClass("open");
																 
				';
					
				if($params['selectedEffect'] == 'transfer')	{
					$js .= 'options = { to: "'.$params['to'].'", className: "ui-effects-transfer" };';
					$css = '.ui-effects-transfer { border: 2px dotted gray; }';
					$doc->addStyleDeclaration($css);
				}
				elseif($params['selectedEffect'] == 'scale')
					$js .= 'options = { percent: '.$params['percent'].' };';
				elseif($params['selectedEffect'] == 'size')
					$js .= 'options = { to: { width: '.$params['width'].', height: '.$params['height'].' } };';
				else
					$js .= 'var options = {};';
						
				$js .= '$jd( "'.$id.'"  ).toggle( "'.$params['selectedEffect'].'", options, '.$params['displaytime'];
				
				if($params['callback']<>'')
					$js .= ', '.$params['callback'];
					
				$js .= ');';
				
				if($params['callback']<>'')
					$js .='function '.$params["callback"].'() {
						setTimeout(function() {
						$jd("'.$id.'").removeAttr( "style" ).hide().fadeIn();
						}, '.$params['callbacktime'].');
						};';
		   	 
			$js .='return false;';

		        $js .= '});'; 
			 
	
		$js .='});';
		
		$doc->addScriptDeclaration($js);
		
	}
	
	public static function animate( $id, $buttonId, $params = array() )
	{
		
		$doc = JFactory::getDocument();
		
		$this->_id = $id;
		//fixes display duration.
		$params['displaytime']		= isset($params['displaytime'])?$params['displaytime']:500;
		//Fixes callback display duration.
		$params['callbacktime']	= isset($params['callbacktime'])?$params['callbacktime']:1000;
		// Background color before animation. 
	  	$params['bcolor']	    = isset($params['bcolor'])?$params['bcolor']:'fff';
		//Background color after animation.
		$params['bcolor1']	    = isset($params['bcolor1'])?$params['bcolor1']:'aa0000';
		//font color  
		$params['color']	= isset($params['color'])?$params['color']:'000';
		//font color after animation
		$params['color1']		= isset($params['color1'])?$params['color1']:'fff';
		//Fixes minimum width 
		$params['width']	= isset($params['width'])?$params['width']:'240';
		//Fixes maximum width 
		$params['width1']	    = isset($params['width1'])?$params['width1']:'500';
	
		$this->_params = $params;
				
		$js = '
		$jd(function() {
				
				$jd( "'.$buttonId.'" ).toggle(';
                
				$js .='function(){
					$jd( "'.$id.'" ).animate({
							backgroundColor: "#'.$params['bcolor1'].'",
							color: "#'.$params['color1'].'",
							width: '.$params['width1'].'
						}, '.$params['callbacktime'].');
					},';
					
				$js .='function(){
						$jd( "#'.$id.'" ).animate({
							backgroundColor: "#'.$params['bcolor'].'",
							color: "#'.$params['color'].'",
							width: '.$params['width'].'
						}, '.$params['displaytime'].' );
					}';	
		
		        $js .= ');';

	
		$js .='});';
		
		$doc->addScriptDeclaration($js);
		
	}
		
	public static function progressbar( $id, $params = array() )
	{
		
		$doc = JFactory::getDocument();
		
		$this->_id = $id;
		//value refers to shed region in progressbar 100 is max.
		$params['value']	  = isset($params['value'])?$params['value']:'45';
		//we can resize the width of progressbar when this parameter is enabled.fix id here.
		$params['resize'] = isset($params['resize'])?$params['resize']:'';
	
		$this->_params = $params;
				
		$js = '$jd(function(){
				
				$jd( "#'.$id.'" ).progressbar({
				
				value:'.$params['value'].'';
		
		        $js .= '});';
				
			 if( $params['resize'] <> ''){
			 
				$js .='$jd( "#'.$params['resize'].'" ).resizable();';
				}
	
		$js .='});';
		
		$doc->addScriptDeclaration($js);
		
	}
	
	public static function uploadfile($id, $params = array())
	{
		
		$params['url']				= isset($params['url'])?$params['url']:'index.php';
		//set the class for button bar container
		$params['buttonbar']		= isset($params['buttonbar'])?$params['buttonbar']:'fileupload-buttonbar';
		$params['inputbutton']		= isset($params['inputbutton'])?$params['inputbutton']:'fileinput-button';
		$params['buttontext']		= isset($params['buttontext'])?$params['buttontext']:JText::_('ADDFILE');
		$params['fieldname']		= isset($params['fieldname'])?$params['fieldname']:'file';
		$params['multiple']			= isset($params['multiple'])?$params['multiple']:false;
		$params['filelist']			= isset($params['filelist'])?$params['filelist']:'fileupload-content';
		$params['tableclass']		= isset($params['tableclass'])?$params['tableclass']:'files';
		$params['pbarclass']		= isset($params['pbarclass'])?$params['pbarclass']:'';
		$params['maxNumberOfFiles']	= isset($params['maxNumberOfFiles'])?$params['maxNumberOfFiles']:1;
		$params['namespace']		= isset($params['namespace'])?$params['namespace']:'fileupload';
		$params['files']			= isset($params['files'])?$params['files']:array();
		
		
		$add = $params['multiple']?'[]':'';
		
		static $loader;
		
		$doc =  JFactory::getDocument();
		
		$html = '<div class="'.$params['buttonbar'].'"><label class="'.$params['inputbutton'].'"><span>'.$params['buttontext'].'</span><input type="file" name="'.$params['fieldname'].$add.'" id="fileinput"></label></div><div class="'.$params['filelist'].'"><table class="uploadtableclass '.$params['tableclass'].'">';
		
		if(count($params['files']))	{
			
			for($i=0;$i<count($params['files']);$i++)	{
				
				$file = $params['files'][$i];
				
				$file->name = isset($file->name)?$file->name:null;
				$file->url = isset($file->url)?$file->url:null;
				$file->thumbnail_url = isset($file->thumbnail_url)?$file->thumbnail_url:null;
				$file->delete_url = isset($file->delete_url)?$file->delete_url:null;
								
				$html .= '<tr class="template-download">';
				
				if(empty($file->url))	{
					
					if(!empty($file->thumbnail_url))
						$html .= '<td class="preview"><img src="'.$file->thumbnail_url.'" width="50" /></td>';
						
					$html .= '<td class="name">'.$file->name.'</td>';
				}
				else	{
					if(!empty($file->thumbnail_url))
						$html .= '<td class="preview"><a href="'.$file->url.'" target="_blank"><img src="'.$file->thumbnail_url.'" width="50" /></a></td>';
					$html .= '<td class="name"><a href="'.$file->url.'" target="_blank">'.$file->name.'</a></td>';
				}
				
				if(!empty($file->delete_url))
					$html .= '<td class="delete"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" data-url="'.$file->delete_url.'" data-type="POST" role="button" aria-disabled="false" title="'.JText::_('DELETE').'">				<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">'.JText::_('DELETE').'</span></button></td>';
				
				$html .= '</tr>';
								
			}
			
		}
		
		$html .= '</table>';
		
		if($params['pbarclass'] <> "")
			$html .= '<div class="'.$params['pbarclass'].'"></div>';
		
		$html .= '</div>';
		
		$script = '$jd(function(){
						var html = \''.$html.'\';
						$jd("'.$id.'").html(html);
						$jd("'.$id.'").addClass("fileupload");
					});';
		
		$doc->addScriptDeclaration($script);
		
		if(!$loader)	{
			
			ob_start();
		
		?>
			
			<script id="template-upload" type="text/x-jquery-tmpl">
				<tr class="template-upload{{if error}} ui-state-error{{/if}}">
					<td class="preview"></td>
					<td class="name">{{if name}}${name}{{else}}Untitled{{/if}}</td>
					{{if error}}
						<td class="error" colspan="2">Error:
							{{if error === 'maxFileSize'}}File is too big
							{{else error === 'minFileSize'}}File is too small
							{{else error === 'acceptFileTypes'}}Filetype not allowed
							{{else error === 'maxNumberOfFiles'}}Max number of files exceeded
							{{else}}${error}
							{{/if}}
						</td>
					{{else}}
						<td class="progress"><div></div></td>
					{{/if}}
					<td class="cancel"><button><?php echo JText::_('CANCEL'); ?></button></td>
				</tr>
			</script>
			<script id="template-download" type="text/x-jquery-tmpl">
				<tr class="template-download{{if error}} ui-state-error{{/if}}">
					{{if error}}
						<td></td>
						{{if name}}
							<td class="name">${name}</td>
						{{/if}}
						{{if error}}
						<td class="error" colspan="2"><?php echo JText::_('ERROR'); ?>:${error}</td>
						{{/if}}
						{{if delete_url}}
						<td class="delete">
							<button data-type="${delete_type}" data-url="${delete_url}"><?php echo JText::_('DELETE'); ?></button>
						</td>
						{{/if}}
					{{else}}
						<td class="preview">
							{{if thumbnail_url}}
								{{if url}}
									<a href="${url}" target="_blank"><img src="${thumbnail_url}" width="50"></a>
								{{else}}
									<img src="${thumbnail_url}" width="50">
								{{/if}}
							{{/if}}
						</td>
						{{if name}}
						<td class="name">
							{{if url}}
							<a href="${url}"{{if thumbnail_url}} target="_blank"{{/if}}>${name}</a>
							{{else}}
							${name}
							{{/if}}
						</td>
						{{/if}}
						<td colspan="2"></td>
						{{if delete_url}}
						<td class="delete">
							<button data-type="${delete_type}" data-url="${delete_url}"><?php echo JText::_('DELETE'); ?></button>
						</td>
						{{/if}}
					{{/if}}
				</tr>
			</script>
		
		<?php
        
			$script = ob_get_contents();
			ob_end_clean();
			
			$doc->addCustomTag($script);
		
		}
		
		$js = '
		
		$jd(function () {
			"use strict";
		
			// Initialize the jQuery File Upload widget:
			
			$jd("'.$id.'").fileupload({
				  buttonbar: "'.$params['buttonbar'].'"
				, inputbutton: "'.$params['inputbutton'].'"
				, filelist: "'.$params['filelist'].'"
				, namespace: "'.$params['namespace'].'"
				';
				
		if($params['pbarclass'])
			$js .= ', pbarclass: "'.$params['pbarclass'].'"';
			
		if($params['maxNumberOfFiles'])
			$js .= ', maxNumberOfFiles: '.$params['maxNumberOfFiles'];
										 
		$js .= ', add: function(e, data)	{
					
					var that = $jd(this).data("fileupload");
					that._adjustMaxNumberOfFiles(-data.files.length);
					data.isAdjusted = true;
					data.isValidated = that._validate(data.files);
					data.context = that._renderUpload(data.files)
						.appendTo($jd(this).find(".files")).fadeIn(function () {
							// Fix for IE7 and lower:
							$jd(this).show();
						}).data("data", data);
					
					if ((that.options.autoUpload || data.autoUpload) &&
							data.isValidated) {
						data.jqXHR = data.submit();
					}
					
					data.files[0].paramName = "'.$params['fieldname'].'";
					data.context.num = files_to_upload.length;
					files_to_upload.push(data);
						
			},
			done: function(e, data)	{
				var result = data.result;
				var fileresult = data.result.file;
				
				data.result = fileresult;
				
				var that = $jd(this).data("fileupload");
				if (data.context) {
					data.context.each(function (index) {
						
						var file = ($jd.isArray(data.result) &&
								data.result[index]) || {error: "emptyResult"};
						if (file.error) {
							that._adjustMaxNumberOfFiles(1);
						}
						$jd(this).fadeOut(function () {
							that._renderDownload([file])
								.css("display", "none")
								.replaceAll(this)
								.fadeIn(function () {
									// Fix for IE7 and lower:
									$jd(this).show();
								});
						});
						
					});
				} else {
					that._renderDownload(data.result)
						.css("display", "none")
						.appendTo($jd(this).find(".files"))
						.fadeIn(function () {
							// Fix for IE7 and lower:
							$jd(this).show();
						});
				}
				
				files_to_upload = new Array();
				
				savesuccess(result);
				
			}';

		
		$js .= '});
			
			$jd("'.$id.'").data("fileupload")._adjustMaxNumberOfFiles(-'.count($params['files']).');
			
			});';
		
		
		$doc->addScriptDeclaration($js);
		
	}
	
	public static function button( $id )
	{
		
		$doc = JFactory::getDocument();
		
		$this->_id = $id;
				
		$js = '$jd(function(){
				
				$jd( "#'.$id.'" ).buttonset();';

		$js .='});';
		
		$doc->addScriptDeclaration($js);
		
	}
	
	public static function displayicons($item)
	{
		
		$jdapp = Joomd::getApp();
		
		$user = Joomd::getUser();
		
		$icon = '';
		
		if(Joomd::canEdit($item))	{
			
			$icon = JHTML::_( 'jdgrid.fedit', $item->id, 900, 600 ).'&nbsp;';
			
		}
		
		echo $icon;
		
	}
	
	public static function displayaddicon()
	{
		
		$user = Joomd::getUser();
		$typeid = JRequest::getInt('typeid', 0);
		
		$icon = '<div class="toolbaricon"><a class="icon" href="javascript:$jd(\'input#cid\').val(0);openAddDiag(\'add\', 900, 600);">'.JText::_('ADD').'</a></div>';
		
		$doc = JFactory::getDocument();
		
		$js = 'function openAddDiag(task, width, height)
				{
		
					var post = {"option":"com_joomd", "view":"itempanel", "task":"add", "typeid":'.$typeid.', "cid[]":"0", "abase":1};
					
					openShortwindow("'.JURI::root().'", post, width, height);
					
				}';
		
		$doc->addScriptDeclaration($js);
        
		echo $icon;
	
	}
	
	public static function createform($array = array())
	{
		
		$mainframe =  JFactory::getApplication();
		$doc = JFactory::getDocument();
		
		//form name
		$array['formname'] = isset($array['formname'])?$array['formname']:'adminlist';
		
		//list selector
		$array['list'] = isset($array['list'])?$array['list']:'table.adminlist tbody';
		
		//form name
		$array['editform'] = isset($array['editform'])?$array['editform']:'adminform';
		
		//list selector
		$array['edittable'] = isset($array['edittable'])?$array['edittable']:'table.admintable tbody';
		
		$view	= JRequest::getVar('view', '');
		$layout	= JRequest::getVar('layout', '');
		
		$url = $mainframe->isSite()?(JURI::root().'index.php'):'index.php';
		
		ob_start();
		
		?>
        
        $jd(function() {
        
            $jd("form[name='<?php echo $array['editform']; ?>']").submit(function() {
              return false;
            });
        
        });
        
        var files_to_upload = new Array();
        
        function save(task)
        {
            
            if(typeof(validateit) == 'function')	{
                
                if(!validateit())
                    return;
                
            }
            
            $jd("form[name='<?php echo $array['editform']; ?>'] input[name='task']").val(task);            
            
            if(files_to_upload.length > 0)	{
            	
                data = files_to_upload[0];
		
                for(var j=1;j < files_to_upload.length;j++)	{
                    data.context.push(files_to_upload[j].context);
                    data.files.push(files_to_upload[j].files[0]);
                }
                
                var jqXHR = $jd('.fileupload').fileupload('send', data);
             
            }
            
            else	{
                        
                var data = $jd("form[name='<?php echo $array['editform']; ?>']").serializeArray();
                
                $jd.ajax({
                      url: "<?php echo $url; ?>",
                      type: "POST",
                      dataType:"json",
                      data: data,
                      beforeSend: function()	{
                        $jd(".poploadingbox").show();
                      },
                      complete: function()	{
                        $jd(".poploadingbox").hide();
                      },
                      success: function(res)	{
                        
                        savesuccess(res);
                            
                      },
                      error: function(jqXHR, textStatus, errorThrown)	{
                        alert(textStatus);                 
                      }
                });
            
            }
            
        }
        
        function savesuccess(res)	{
	
            var task = $jd("form[name='<?php echo $array['editform']; ?>'] input[name='task']").val();
            var abase = <?php echo JRequest::getInt('abase', 0); ?>;
            var popup=<?php echo $mainframe->isSite()?'true':'false';?>;
            
            if(res.result == "success")	{
                
                if(res.alias)
                   	$jd("form[name='<?php echo $array['editform']; ?>'] input[name='alias']").val(res.alias)
                
                $jd("form[name='<?php echo $array['formname']; ?>'] input[name='task']").val('loaditems');
                var vars = $jd("form[name='<?php echo $array['formname']; ?>']").serializeArray();
                
                if(typeof(loaditems) == 'function')	{
                	//loaditems("filtersuccess", vars);
                }
                
                if(task == "apply")	{
                   	if(res.id)
	                	$jd("form[name='<?php echo $array['editform']; ?>'] input[name='id']").val(res.id);
                    
                    if(abase)
                    	displayalert(res.msg, 'message', true);
                    else
                    	displayalert(res.msg, 'message', popup);
                    
                }
                else	{
                	$jd('.dialogbox').dialog('destroy').html('');
                        
                    if(!abase)	{
                    	if(res.id)
	                		$jd("form[name='<?php echo $array['editform']; ?>'] input[name='id']").val(res.id);
                    }
                    
                    if(abase)
                    	displayalert(res.msg, 'message', false);
                    else
                    	displayalert(res.msg, 'message', popup);
                }
                
                if(res.html)
                    $jd("<?php echo $array['edittable']; ?>").html(res.html);
                
            }
            else	{
                if(abase)
                    displayalert(res.error, 'error', true);
                else
                    displayalert(res.error, 'error', popup);
            }
                        
        }
        
        <?php
		
		$js = ob_get_contents();
		
		ob_end_clean();
		
		$doc->addScriptDeclaration($js);
		
	}
	
	public static function createlist($params, $array = array())
	{
		$doc = JFactory::getDocument();
		
		//Get General Configuration for scroll
		$config = Joomd::getConfig();
		
		//form name
		$array['formname'] = isset($array['formname'])?$array['formname']:'adminlist';
		
		//list selector
		$array['list'] = isset($array['list'])?$array['list']:'table.adminlist tbody';
		
		//form method
		$array['type'] = isset($array['type'])?$array['type']:'POST';
		
		//column to enable reordering of rows
		$array['order'] = isset($array['order'])?$array['order']:'ordering';
		
		//whether to reorder the adminlist
		$array['reorder'] = isset($array['reorder'])?$array['reorder']:false;
		
		//enable dialog box
		$array['dialog'] = isset($array['dialog'])?$array['dialog']:true;
		
		//enable sortable
		$array['sortable'] = isset($array['sortable'])?$array['sortable']:true;
		
		//enable access levels
		$array['access'] = isset($array['access'])?$array['access']:false;
		
		//enable pagination
		$array['pagination'] = isset($array['pagination'])?$array['pagination']:'.pagination';
		
		//enable sorting
		$array['sort'] = isset($array['sort'])?$array['sort']:true;
		
		//enable filtering
		$array['filterlist'] = isset($array['filterlist'])?$array['filterlist']:true;
		
		$mainframe =  JFactory::getApplication();
		$db =  JFactory::getDBO();
		
		$view = JRequest::getVar('view', '');
		$sortable = Joomdui::getSortable();
		$limit = $params->limit;
						
		$reorder = $array['reorder']?'reorder':'';
		
		$url = $mainframe->isSite()?(JURI::root().'index.php'):'index.php';
		
		if($array['sortable'])	{
		
		echo $sortable->initialize($array['list'], array('axis'=>'y', 'handle'=>'.sort_handle_s, .sort_handle_l', 'disabled'=>$params->filter_order<>$array['order'], 'post'=>true, 'postdata'=> array('option'=>'com_joomd', 'view'=>$view, 'task'=>'reorder', 'abase'=>1, jutility::getToken()=>1), 'postvars'=>array('limit'=>'$jd("form[name=\''.$array['formname'].'\'] input[name=\'limit\']").val()'), 'url'=>'index.php', 'revert'=>true, 'return'=>'execute', 'selection'=>false));
		
		}
		
		ob_start();
		
		if($array['dialog'])	{
		
		loader::effect('highlight');
		loader::effect('fade');
		
		?>
        
        function openDiag(task, width, height)
        {
            
            if($jd(".dialogbox").html('').dialog('isOpen'))
				$jd(".dialogbox").dialog('close');
            
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='task']").val(task);
            
            if(task == "add")	{
                 $jd("form[name='<?php echo $array['formname']; ?>'] input[name='cid[]']").each(function() {
                
                    $jd(this).attr('checked', false);
                
                });
            }
            var url = "<?php echo $url; ?>";
            var post = $jd("form[name='<?php echo $array['formname']; ?>']").serializeArray();
            
            openShortwindow(url, post, width, height);
            
        }
        
        <?php	}	?>

		function execute(data)	{
							
			callback['successreloadmsg'](data);
			
			
		//	setTimeout("removealert()", 3000);
		
		}
		
		var callback = {
			successreload: function(data)
			{
				if(data.result == "success")	{
					data.html = data.html?data.html:'';
                    $jd("<?php echo $array['list']; ?>").html(data.html);
                }
				else
					displayalert(data.error, "error");
				
			},
            successreloadmsg: function(data)
			{
				if(data.result == "success")	{
                	data.html = data.html?data.html:'';
					$jd("<?php echo $array['list']; ?>").html(data.html);
                    displayalert(data.msg, "message");
                }
				else
					 displayalert(data.error, "error");
				
			},
			scrollsuccess: function(data)
			{
				
				if(data.result == "success")	{
                	data.html = data.html?data.html:'';
					$jd("<?php echo $array['list']; ?>").append(data.html.replace("\\", ""));
                    
					var count = Number($jd("form[name='<?php echo $array['formname']; ?>'] input[name='count']").val())+Number(data.count);
                    var total = data.total;
                    
					$jd("form[name='<?php echo $array['formname']; ?>'] input[name='count']").val(count);
                    $jd("form[name='<?php echo $array['formname']; ?>'] input[name='total']").val(total);
                    
                    if(Number(total) > Number(count))
                    	$jd("<?php echo $array['pagination']; ?>").html('<a href="javascript:void(0);" onclick="startscroll();" class="loadmore">'+$jd(".loadmore").html()+'</a>').show();
                    else
                    	$jd("<?php echo $array['pagination']; ?>").html('<span class="loadmore">'+$jd(".loadmore").html()+'</span>').hide();
											
				}
				else
					alert(data.error);
                    
                 setTimeout("scrollalert()", 1500);
					
			},
			filtersuccess: function(data)
			{
				
				if(data.result == "success")	{
					
                    data.html = data.html?data.html:'';
                    $jd("<?php echo $array['list']; ?>").html(data.html.replace("\\", ""));
				
					if(data.total > data.count)
                    	$jd("<?php echo $array['pagination']; ?>").html('<a href="javascript:void(0);" onclick="startscroll();" class="loadmore">'+$jd(".loadmore").html()+'</a>').show();
                    else
                    	$jd("<?php echo $array['pagination']; ?>").html('<span class="loadmore">'+$jd(".loadmore").html()+'</span>').hide();
                    
					$jd("form[name='<?php echo $array['formname']; ?>'] input[name='count']").val(data.count);
                    $jd("form[name='<?php echo $array['formname']; ?>'] input[name='total']").val(data.total);
                    
				}
				else
					alert(data.error);
				
			},
            donothing: function()
            {}
			
		};
                
        function listItemTask(task, e)
        {
        
            if(task == "delete")	{
            
            	var check = confirm('<?php echo JText::_('AREUSUREUWANTTODEL'); ?>');
            	
                if(!check)
                	return;
                
            }
            
            if(e)	{
            	$jd("form[name='<?php echo $array['formname']; ?>'] input[name='cid[]']").attr("checked", false);
				$jd("#"+e).attr("checked", true);
            }
            
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='task']").val(task);
            
            var data = $jd("form[name='<?php echo $array['formname']; ?>']").serializeArray();
            
            loaditems("successreloadmsg", data);
            
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='toggle']").attr("checked", false);
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='cid[]']").attr("checked", false);
        	
        }
		
		$jd(function() {
        
        $jd("form[name='<?php echo $array['formname']; ?>']").submit(function() {
          return false;
        });
                
        $jd("form[name='<?php echo $array['formname']; ?>'] input[name='total']").val(<?php echo $params->total; ?>);
        $jd("form[name='<?php echo $array['formname']; ?>'] input[name='count']").val(<?php echo $params->limit; ?>);
        $jd("form[name='<?php echo $array['formname']; ?>'] input[name='limitstart']").val(<?php echo $params->limitstart; ?>);
        $jd("form[name='<?php echo $array['formname']; ?>'] input[name='limit']").val(<?php echo $params->limit; ?>);
        
        
        $jd("form[name='<?php echo $array['formname']; ?>'] .featuredgrid").live('click', function()	{
        	
			var cb = $jd(this).attr('data-cb');
            var task = $jd(this).attr('data-task');
                                    
            var that = this;
            
            $jd.ajax({
                  url: "<?php echo $url; ?>",
                  type: "POST",
                  dataType:"json",
                  data: {'option':'com_joomd', 'view':'<?php echo $view; ?>', 'task':task, 'cid[]':$jd("#"+cb).val(), 'i':cb.substring(2), 'abase':1, '<?php echo jutility::getToken(); ?>':1},
                  success: function(data)	{
                    
                    if(data.result=="success")	{
                        $jd(that).parent().html(data.output);
                    }
                    else
                        displayalert(data.error, "error");
                        
                  },
                  error: function(jqXHR, textStatus, errorThrown)	{
                    displayalert(textStatus, "error");
                  }
            });
        
       	});
        
		<?php if($mainframe->isAdmin() or $config->scroll)	{	?>
		scrollalert();
        <?php	}	?>
        
        $jd("form[name='<?php echo $array['formname']; ?>'] input[name='toggle']").live('click', function()	{
        
        	if($jd(this).attr('checked'))
            	var checked = true;
            else
            	var checked = false;
                
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='cid[]']").each(function() {
            
            	$jd(this).attr('checked', checked);
            
            });
            
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='boxchecked']").val($jd("form[name='<?php echo $array['formname']; ?>'] input[name='cid[]']:checked").size());
        
        });
		
        <?php	if($array['sort'])	{	?>
        
        $jd("<?php echo $array['list']; ?>").sortable("option", "disabled", <?php echo ($params->filter_order==$array['order'])?'false':'true'; ?>);
        
		$jd("#sort").live("click", function()	{
		
			var classes = $jd(this).attr("class").split(" ");
			
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='task']").val("loaditems");
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='limitstart']").val(0);
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='filter_order']").val(classes[0]);
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='filter_order_Dir']").val(classes[1]);
			
			
			var data = $jd("form[name='<?php echo $array['formname']; ?>']").serializeArray();
						
			loaditems("successreload", data);
			
			$jd(this).toggleClass("asc");
			$jd(this).toggleClass("desc");
			
			var src = "<?php echo JURI::root(); ?>components/com_joomd/assets/images/sort_"+classes[1]+".png";
			
			if($jd("img", this).size() > 0)	{
				$jd(this).find("img").attr("src", src);
			}
			else	{
				$jd("#sort img").remove();
				
				$jd(this).append('<img src="'+src+'" alt="" />');
			}
			
			if(classes[0] == "<?php echo $array['order']; ?>")	{
            	$jd('#gridsaveorder').show();
				$jd("<?php echo $array['list']; ?>").sortable("option", "disabled", false);
            }
			else	{
            	$jd('#gridsaveorder').hide();
				$jd("<?php echo $array['list']; ?>").sortable("option", "disabled", true);
            }
		
		});
        
        <?php	}	?>
        
        $jd("#gridedit").live("click", function()	{
        	var classes = $jd(this).attr("class").split(" ");
			
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='toggle']").attr("checked", false);
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='cid[]']").attr("checked", false);
            
			$jd("#"+classes[0]).attr("checked", true);
                        
            openDiag(classes[1]);
            
        });
        
        $jd("#gridsaveorder").live("click", function(){
        
        	$jd("form[name='<?php echo $array['formname']; ?>'] input[name='cid[]']").each(function() {
            
            	$jd(this).attr('checked', true);
            
            });
            
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='task']").val($jd(this).attr('class'));
            
            var data = $jd("form[name='<?php echo $array['formname']; ?>']").serializeArray();
            
            loaditems('successreloadmsg', data);
            
        
        });
		
		});
		
        <?php if($mainframe->isAdmin() or $config->scroll)	{	?>
        
		function scrollalert()	{
			
            var settime = true;
            
			if(Number($jd("form[name='<?php echo $array['formname']; ?>'] input[name='total']").val()) > Number($jd("form[name='<?php echo $array['formname']; ?>'] input[name='count']").val()))	{
			
				var scrolltop=$jd(window).scrollTop();
				var scrollheight=$jd(document).height();
				var windowheight=$jd(window).height();
				var scrolloffset=50;
				
				if(scrollheight != windowheight && scrolltop>=(scrollheight-(windowheight+scrolloffset)))
				{
					settime = false;
					startscroll();
				}
			
			}
            
            if(settime)
	            setTimeout("scrollalert()", 1500);
		
		}
        
        <?php	}	?>
		
		function startscroll()
		{
			
            $jd("form[name='<?php echo $array['formname']; ?>'] input[name='limitstart']").val($jd("form[name='<?php echo $array['formname']; ?>'] input[name='limit']").val());
			
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='limit']").val('<?php echo $limit; ?>')
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='task']").val("loaditems");
			
			var data = $jd("form[name='<?php echo $array['formname']; ?>']").serializeArray();
			
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='limit']").val(Number('<?php echo $limit; ?>')+Number($jd("form[name='<?php echo $array['formname']; ?>'] input[name='limitstart']").val()))
			
			loaditems("scrollsuccess", data);
			
		}
		
        <?php	if($array['filterlist'])	{	?>
        
		function filterlist(e)
		{
			
			$jd("form[name='<?php echo $array['formname']; ?>'] input[name='task']").val("loaditems");
			var data = $jd("form[name='<?php echo $array['formname']; ?>']").serializeArray();
			
			loaditems("filtersuccess", data);
			
		}
		
        <?php	}	?>
        
		function loaditems(op, data)
		{
			            
			$jd.ajax({
				  url: "<?php echo $url; ?>",
				  type: "<?php echo $array['type']; ?>",
                  dataType:"json",
				  data: data,
                  beforeSend: function()	{
                  	$jd("#joomdpanel .loadingblock").show();
                  },
                  complete: function()	{
                  	$jd("#joomdpanel .loadingblock").hide();
                  },
				  success: function(data)	{
					  
					callback[op](data);
						
				  },
                  error: function(jqXHR, textStatus, errorThrown)	{
                  	displayalert(textStatus, "error");
                  }
			});
            			
		}
		
		<?php
		
		$js = ob_get_contents();
		
		ob_end_clean();
		
		$doc->addScriptDeclaration($js);
	
	}
	
}
