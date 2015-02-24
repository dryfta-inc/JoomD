<?php
defined('_JEXEC') OR die('Access Denied!');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

JApplication::registerEvent('jnewsbot_tagmodule', 'jNews_tagModule' );
//JApplication::registerEvent( 'jnewsbot_transformfinal', 'jNews_loadModule' );
JApplication::registerEvent( 'jnewsbot_transformall', 'jNews_loadModule' );

function jNews_loadModule( $html, $text, $params ) {

	$regex = "#{module *=(.*)}#Ui";
	if(!preg_match_all($regex, $html, $matches)) return;

	$myAcaClass = new jNews_ModuleRender();
	$tagList = array();

	foreach($matches[0] as $num => $tag){
		$modID = intval($matches[1][$num]);
		$myAcaClass->_modules[$tag] = $modID;
		$tagList[$tag]->id = $modID;
	}

	$tags = $myAcaClass->_replaceModuleJoomla($tagList );

	//we check if the tag has something to replace
	foreach($tags AS $key=>$value){
		if(empty($value)){
			jnews::printM('notice' , _JNEWS_TAGMODULE_REPLACE_MESSAGE );
		}
	}

	if(!empty($tags)){
		$html = str_replace(array_keys($tags),$tags,$html);
		$text = str_replace(array_keys($tags),$tags,$text);
	}

}

/**
 * <p>Function to insert a module tag<p>
 * @param string $forms - the start html form
 * @param object $params - the plugin parameters

 */
function jNews_tagModule($forms, $params = null){
		$modAccess = $params->get('moduleaccess', '2');
		$excludedMod = $params->get('modexclude', 'mod_login,mod_breadcrumbs,mod_wrapper,mod_poll');
		$tagsClass = new jNewsmoduleTags();
		$limit = -1;
		$limittotal = $tagsClass->_countJoomlaMod($modAccess);
		$setLimit = jnews::setLimitPagination($limittotal);
		$action = JRequest::getVar('act');
		$task = JRequest::getVar('task');
		$modsearch = JRequest::getVar('modsearch', '' );
		echo $forms['main'];
		$hidden = '<input type="hidden" name="option" value="'.JNEWS_OPTION.'" />';
	   	$hidden .= '<input type="hidden" name="limit" value="'.$limit.'" />';
		$toSearch = null;
		$toSearch->forms = '';
		$toSearch->hidden = $hidden;
		$toSearch->listsearch = $modsearch;
		$toSearch->id = 'modsearch';

	?>

<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
	<?php
	echo jnews::setTop( $toSearch, null);
		$js = 'function insertjnewstag(tag){';
	if( version_compare(JVERSION,'1.6.0','<') ){//1.5
		$js .= ' if(window.top.insertTag(tag)){window.top.document.getElementById(\'sbox-window\').close();}';
	}else{
		$js .= ' if(window.top.insertTag(tag)) window.parent.SqueezeBox.close();';
	}
		$js .= '}';
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	?>
	<table class="joobilist" cellpadding="0" cellspacing="0">
		<tbody>
			<thead>
				<tr>
					<th class="title">
						Tag
					</th>
					<th class="title">
						<?php echo _JNEWS_TAG_MODNAME; ?>
					</th>
					<th width="80px" class="title">
						<?php echo _JNEWS_TAG_ACL; ?>
					</th>
					<th  width="60px" class="title">
						<?php echo _JNEWS_TAG_MODTYPE; ?>
					</th>
					<th  width="40px" class="title">
						<?php echo _JNEWS_TAG_MODPOSITION; ?>
					</th>
				</tr>
			</thead>
			<?php
				$mods = $tagsClass->_getJoomlaMod($modsearch, $setLimit, $modAccess, $excludedMod);
				$k = 0;
				$html = '';
				if ( !empty($mods) ) {
					foreach($mods as $mod){
						if ($mod->access == 0) {
							$mod->access = '<span style="color: green;">'._JNEWS_TAG_PUBLIC.'</span>';
						}elseif($mod->access == 1){
							$mod->access = '<span style="color: red;">'._JNEWS_REGISTERED.'</span>';
						}else{
							$mod->access = '<span style="color: black;">'._JNEWS_TAG_SPECIAL.'</span>';
						}
						$insertTag = '{module='.$mod->id.'}';
						$html .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="insertjnewstag(\''.$insertTag.'\')" ><td><strong>{module='.$mod->id.'}</strong></td><td>'.$mod->title.'</td><td nowrap="nowrap" align="center">'.$mod->access.'</td><td nowrap="nowrap">'.$mod->module.'</td><td nowrap="nowrap">'.$mod->position.'</td></tr>';
						$k = 1-$k;
					}
				}
				echo $html;
			?>
		</tbody>
	</table>
	<?php
		$css = 'margin:auto;';
		echo jnews::setPaginationBot($setLimit, $css);
	?>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
	<input type="hidden" value="<?php echo JNEWS_OPTION; ?>" name="option"/>
	<input type="hidden" value="<?php echo $action; ?>" name="act"/>
	<input type="hidden" value="<?php echo $task; ?>" name="task"/>
  	</form>
<?php

}


/**
 * <p>jNewstags Class</p>

 */
class jNewsmoduleTags {
	/**
	 * <p>Function to get the Joomla Module</p>
	 * @param string $modsearch - the string to be search
	 * @param object $setLimit - limits for the pagination
	 * @return int $modAccess - params from the plugin

	 */
	function _getJoomlaMod($modsearch='', $setLimit = null, $modAccess, $excludedMod=''){

		$db=&JFactory::getDBO();
		$query = 'SELECT id, title, position, access, module FROM #__modules';
		if (!empty($excludedMod)) {
			$excludedMod = explode( ',', trim($excludedMod));
			$where[] = ' `module` NOT IN (\''.implode('\',\'',$excludedMod).'\')';
		}
		if ($modAccess == 0){
			$where[] = ' `access` = '.$modAccess;
		}else{
			if (!empty($modAccess)) $where[] = ' `access` <= '.$modAccess;
		}
		//we dont want to load the admin modules
		$where[] = ' `client_id` = 0 ';
		if (!empty($modsearch)) $where[] = ' (title LIKE \'%' . $modsearch . '%\' OR module LIKE \'%' . $modsearch . '%\') ';
		$query .= (count( $where ) ? " WHERE " . implode( ' AND ', $where ) : "");
		
		$query .= ' ORDER BY `position`,`ordering`';
		if (!is_null($setLimit) && $setLimit->start != -1 && $setLimit->end) $query .= ' LIMIT ' . $setLimit->start . ', ' . $setLimit->end;
		$db->setQuery($query);
		$mods =$db->loadObjectList();
		return $mods;
	}

	/**
	 * <p>Function to count the total number of joomla modules</p>
	 * @param $modAccess

	 */
	 function _countJoomlaMod($modAccess){
		static $db=null;
		if( !isset($db) ) $db=&JFactory::getDBO();
		$query = "SELECT count(`id`) FROM `#__modules`";
		if ($modAccess == 0){
			$query .=' WHERE `access` = '.$modAccess;
		}else{
			if (!empty($modAccess)) $query .= ' WHERE `access` <= '.$modAccess;
		}
		$db->setQuery( $query );
		$result = $db->loadResultArray();
		$count = ( !empty($result) ) ? $result[0] : 0;
		return $count;
	 }


}


class jNews_ModuleRender {
	/**
	 * $modules is a variable to store all the modules in the page so we can load them in 1 query
	 *
	 * @var array $modules
	 */
	var $_modules = array();

	/**
	 * function to replace the module on Joomla 1.5
	 *
	 * @param array $objet Tags with the arguments
	 * @return array $tags tag with equivalent
	 */
	function _replaceModuleJoomla( $objet ) {
		$mainframe = JFactory::getApplication();
		$tags = array();

		if(empty($this->_modules)) return $tags;

		//First we load all the modules
		$db=&JFactory::getDBO();
		$query = 'SELECT * FROM `#__modules` WHERE `id` IN ('.implode(',',$this->_modules).')';
		$db->setQuery($query);
		$myModules = $db->loadObjectList();

		if(empty($myModules)){
			echo '<br/>Could not load the modules '.implode(',',$this->_modules);
			return $tags;
		}

		//Then we generate the content of each module
		foreach($myModules as $module){
			//determine if this is a custom module
			$module->user  	= substr( $module->module, 0, 4 ) == 'mod_' ?  0 : 1;
			$module->name = $module->user ? $module->title : substr( $module->module, 4 );
			$module->style = null;
			$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
			$allModules[$module->id] = $this->_showModuleJoom15( $module );
		}

		foreach($objet as $tag => $arguments){
			$tags[$tag] = '';
			if(isset($allModules[$arguments->id])) $tags[$tag] = $allModules[$arguments->id];
			else{
				echo 'The module ID '.$arguments->id.' could not be found';
			}
		}

		return $tags;
	}

/**
	 * Function _showModuleJoom15 to display a specific module
	 *
	 * @param object $module
	 * @return string content of the module
	 */
	function _showModuleJoom15( $module ){

		$mainframe =& JFactory::getApplication();

		include_once(JPATH_ROOT.DS.'includes'.DS.'application.php');
		jimport( 'joomla.html.parameter' );
		$plugin =& JPluginHelper::getPlugin( 'jnews', 'tagmodule' );

		if ( !empty($plugin->params) ) {
			$params = new JParameter( $plugin->params );
		} else {
			$params = new JParameter( '' );
		}
		
		$config =& JFactory::getConfig();
		$secret = $config->getValue('config.secret');			
		$URLModule = jNews_Tools::completeLink( 'option='.JNEWS_OPTION.'&act=rendermod&id='. $module->id . '&protect=' . time() . '&code='.$secret , false, false, true );
	
		if ( strtolower($module->module) == 'mod_custom' ) {
			$done = false;
		} else {
	
			@ini_set( 'default_socket_timeout', 10 );
			@ini_set( 'user_agent', 'My-Application/2.5' );
			@ini_set( 'allow_url_fopen', 1 );
			
			//Frontend Modules Only
	//		if ($params->get('load_modules','0') == '1'){
				$PATH = JPATH_ROOT.DS.'modules'.DS.$module->module.DS.$module->module.'.php';
				if ( !file_exists($PATH ) ) {
					echo 'The system could not load the file '.$PATH.'<br>' .
							'<br>Make sure the module is not uninstalled!';
							'<br><br>Only Frontend Modules can be loaded based on your settings.<br>';
					return '';
				}
	//		}
	
			//Backend Modules Only
	//		if ($params->get('load_modules','0') == '2'){
	//			$PATHA= JPATH_ROOT.DS.'modules'.DS.$module->module.DS.$module->module.'.php';
	//			if(file_exists($PATHA) ){
	//				echo 'The system could not load the file '.$PATHA.'<br>' .
	//						'Only Backend/Administrator Modules can be loaded based on your settings.<br><br>';
	//				return '';
	//			}
	//		}
	
			$loadmethod = $params->get('loadmethod', 'fileget' );		
			
			$done = false;
			if ( $loadmethod == 'fileget' || $loadmethod == 'filegetcontent' ) {
				if ( ini_get('allow_url_fopen') ) {
					$module->content = file_get_contents( $URLModule );
					$done = true;
				}
			}
			
			if ( !$done && $loadmethod == 'curl' ){
				if ( function_exists('curl_init') ) {
					$CURL = curl_init();
					curl_setopt( $CURL, CURLOPT_URL,$URLModule );
					curl_setopt( $CURL, CURLOPT_FAILONERROR, 1 );
					curl_setopt( $CURL, CURLOPT_RETURNTRANSFER, 1 );
					curl_setopt( $CURL, CURLOPT_TIMEOUT, 10) ;
					$module->content = curl_exec($CURL);
					curl_close( $CURL );
					$done = true;
				}
			}
		
		}
			
		//last chance but only for cron and frotnend sending
		if ( !$done ) {
			$lang =& JFactory::getLanguage();
			$lang->load( $module->module );
			$module->content= JModuleHelper::renderModule( $module, $module->params );			
		}
		
		$LiveURL = str_replace( JNEWS_JPATH_LIVE, '', $URLModule );
		$module->content = str_replace( array($LiveURL,str_replace('&','&amp;',$LiveURL) ), 'index.php', $module->content );
		$module->content = preg_replace( "#(onclick|onfocus|onload|onblur) *= *\"(?:(?!\").)*\"#iU" , '',$module->content );
		$module->content =  preg_replace( "#< *script(?:(?!< */ *script *>).)*< */ *script *>#isU" , '', $module->content );
	
		return $module->content;

	}
}