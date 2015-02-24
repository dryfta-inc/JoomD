<?php
defined('_JEXEC') OR defined('_VALID_MOS') OR die('...Direct Access to this location is not allowed...');
### Copyright (c) 2006-2012 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co

JApplication::registerEvent( 'jnewsbot_fwdtofriend', 'fwdtofriend' );
JApplication::registerEvent( 'jnewsbot_sendtofriend', 'sendtofriend' );
JApplication::registerEvent( 'jnewsbot_fwdtofriendTransform', 'fwdtofriendTransform' );
//JApplication::registerEvent( 'jnewsbot_transformall', 'fwdtofriendTransform' );

/**
 * <p>Function to the view for the forward to friend<p>
 * @param object $params - the plugin parameters
 */
function fwdtofriend($params = null){
	$action = JRequest::getVar('act');
	$task = JRequest::getVar('task');
	$frmname = base64_decode(JRequest::getVar('frmname'));
	$frmemail = base64_decode(JRequest::getVar('frmemail'));
	$mailingID = JRequest::getInt('mailingid');
	$listID = JRequest::getInt('listid');
	$html = JRequest::getInt('html');

	if ( $params->get( 'viewtemplate', 'notemplate' ) == 'notemplate' ) {
		$linkForm = jNews_Tools::completeLink('option='.JNEWS_OPTION.'&act=fwdtofriend',false, false, true);
	} else {
		$linkForm = jNews_Tools::completeLink('option='.JNEWS_OPTION.'&act=fwdtofriend',false, false );
	}

    $fwdMessage = $params->get('fwdmessage', 'This email was forwarded to you by [FWDEMAIL].');
	?>
<script type="text/javascript">
	function submitfwdtofriend(pressbutton) {
		if (pressbutton != "fwdtofriendForm") return false;
		var formF = document.fwdtofriendForm;
		var tableF = $("jnewsfwdtofriend");
		var rowCountF = tableF.rows.length;

		if ($("fromName").value == "") {
			alert( "<?php echo _JNEWS_FWDTOFRIEND_ALRT_UNAME;?>" );return false;
		}

		var placeEmail = $("fromEmail").value.indexOf("@",1);
		var pointEmail = $("fromEmail").value.indexOf(".",placeEmail+1);

		if (($("fromEmail").value == "") || (placeEmail <= -1) || ($("fromEmail").value.length <= 2) || (pointEmail <= 1)) {
			alert( "<?php echo _JNEWS_FWDTOFRIEND_ALRT_UEMAIL;?>" );return false;
		}

		for(var i=0; i<rowCountF; i++){
			if ($("toName"+i).value == "") {
				alert( "<?php echo _JNEWS_FWDTOFRIEND_ALRT_FNAME;?>" );return false;
			}

			var placeEmailF = $("toEmail"+i).value.indexOf("@",1);
			var pointEmailF = $("toEmail"+i).value.indexOf(".",placeEmailF+1);

			if(($("toEmail"+i).value == "") || (placeEmailF <= -1) || ($("toEmail"+i).value.length <= 2) || (pointEmailF <= 1)){
				alert( "<?php echo _JNEWS_FWDTOFRIEND_ALRT_FEMAIL; ?>" );return false;
			}

		}

		formF.submit();return true;
	}
	function addNewRow(tableID){
		var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var inputName = rowCount;
	    var row = table.insertRow(rowCount);

	            var cell1 = row.insertCell(0);
	            var element1 = document.createElement("input");
	            var br1=document.createElement("br")
	            element1.type = "text";
	            element1.size = "22";
				element1.name = "toName["+inputName+"]";
				element1.id = "toName"+inputName;
				element1.value = "";
				cell1.innerHTML = "Name";
	            cell1.appendChild(br1);
				cell1.appendChild(element1);

	            var cell2 = row.insertCell(1);
	            var element2 = document.createElement("input");
	            var br2=document.createElement("br");
	            element2.type = "text";
	            element2.size = "22";
	            element2.name = "toEmail["+inputName+"]";
	            element2.id = "toEmail"+inputName;
	            element2.value = "";
	            cell2.innerHTML = "Email";
	            cell2.appendChild(br2);
	            cell2.appendChild(element2);
	}

	</script>
<form action="<?php echo $linkForm; ?>" method="post" name="fwdtofriendForm" enctype="multipart/form-data">
<table style="margin: auto;" cellspacing="0" cellpadding="3" border="0" width="415px">
	<tbody>
	<tr>
		<td class="jpretxt" colspan="2"><?php echo $params->get('pretext');?></td>
	</tr>
	<tr>
		<td><br><strong><?php echo _JNEWS_FWD_MYDETAILS;?>:</strong></td>
	</tr>
	<tr>
		<td nowrap="" class=""><?php echo _JNEWS_INPUT_NAME;?>:<br/><input type="text" size="22" value="<?php echo $frmname;?>" id="fromName" name="fromName"></td>
		<td width="100%" class=""><?php echo _JNEWS_INPUT_EMAIL;?>:<br/><input type="text" size="22" value="<?php echo $frmemail;?>" id="fromEmail" name="fromEmail"></td>
	</tr>
	<tr>
		<td colspan="2"><br><strong><?php echo _JNEWS_FWD_WANTTO;?>:</strong><br></td>
	</tr>
	<t/body>
</table>
<table id="jnewsfwdtofriend" style="margin: auto;" cellspacing="0" cellpadding="3" border="0" width="415px">
	<tbody>
	<?php
		$fields = $params->get('numaddfriendfld', 1);
		for($i=1; $i <= $fields; $i++){
	?>
	<tr>
		<td class=""><?php echo _JNEWS_INPUT_NAME;?>:<br/><input type="text" size="22" value="" id="toName<?php echo $i-1; ?>" name="toName[<?php echo $i-1; ?>]"></td>
		<td class=""><?php echo _JNEWS_INPUT_EMAIL;?>:<br/><input type="text" size="22" value="" id="toEmail<?php echo $i-1;?>" name="toEmail[<?php echo $i-1;?>]"></td>
	</tr>
	<?php
		}
	?>
	</tbody>
</table>
<table style="margin: auto;" cellspacing="0" cellpadding="3" border="0" width="415px">
	<tbody>
	<tr>
		<td colspan="2">
		<a href="javascript:void(0);" onclick="addNewRow('jnewsfwdtofriend')"><?php echo _JNEWS_FWD_ADDFIELD; ?></a>
		</td>
	</tr>
	<tr>
		<td colspan="2"><br><strong><?php echo _JNEWS_FWD_MESSAGE;?>:</strong>
		<br><textarea style="width: 100%;" rows="4" cols="50" name="message"><?php echo $params->get('defaultmessage');?></textarea></td>
	</tr>

	<tr>
		<td><br><input type="submit" onclick="return submitfwdtofriend('fwdtofriendForm')" class="button" name="sendtofriend" value="<?php echo _JNEWS_FWD_SENDEMAIL;?>"></td>
	</tr>
	<tr>
		<td colspan="2" class="jposttxt"><?php echo $params->get('posttext');?></td>
	</tr>
    <input type="hidden" value="<?php echo $fwdMessage;?>" name="inEmailMessage">
	</tbody></table>
	<input type="hidden" value="<?php echo JNEWS_OPTION; ?>" name="option"/>
	<input type="hidden" value="fwdtofriend" name="act"/>
	<input type="hidden" value="sendtofriend" name="task"/>
	<input type="hidden" value="<?php echo $mailingID;?>" name="mailingid"/>
	<input type="hidden" value="<?php echo $listID;?>" name="listid"/>
	<input type="hidden" value="<?php echo $html;?>" name="html"/>
  	</form>
<?php
}

/**
 * <p>Function to send/forward the mailings to a the friends</p>
 * @param object $mailing - the mailing object that will be sent. Its from a query
 * @param object $messagePlgin  - the messages in the parameter of the plugin
 * @param array $receiversNames - the name to whom the mail to be forwarded
 * @param array $receiversEmails - the emails where the mail to be forwarded
 * @param object $list - the list where the mailing belongs.

 */
function sendtofriend($mailing=null, $messagePlgin=null, $receiversNames='', $receiversEmails='', $list = null){

	$message= '';
	$receivers=null;
	$inEmailMessageHTML = '<br/><div align="left" style="border: 1px solid rgb(239, 236, 186); padding: 5px; background-color: rgb(251, 250, 231); text-align: left; font-family: tahoma; font-size: 11px;">';
	$replaceWhat = array('[FWDEMAIL]','[FWDNAME]');
	$replaceBy = array($mailing->fromemail, $mailing->name);
	$inEmailMessageHTML .= str_replace($replaceWhat,$replaceBy, $messagePlgin->inEmail);
	$inEmailMessageHTML .= '</div><br/>';

	$body = $messagePlgin->dflt.'<br/>'.$inEmailMessageHTML.$mailing->htmlcontent;
	$textonly = $messagePlgin->dflt.$messagePlgin->inEmail.$mailing->textonly;
	$mailing->htmlcontent = $body;
	$mailing->textonly = $textonly;
	$receivers->receive_html = $list->html;
	$status = true;
	$successM = '';

	static $mailerC = null;
    if (!isset($mailerC)) $mailerC = new jNews_ProcessMail();
    foreach ($receiversNames as $key => $receiversName) {
		$receivers->name = $receiversName;
		$receivers->email= $receiversEmails[$key];

//		$reWhat = array('[FWDEMAIL]','[FWDNAME]', '[NAME]','[FIRSTNAME]','[USERNAME]');
    	$reWhat = array('[FWDEMAIL]','[FWDNAME]', '{tag:name}','{tag:firstname}','{tag:username}');
    
		$reBy = array($mailing->fromemail, $mailing->name, $receiversName,$receiversName,$receiversName);
		$mailing->htmlcontent = str_replace($reWhat,$reBy, $mailing->htmlcontent);
		$mailing->textonly = str_replace($reWhat,$reBy, $mailing->textonly);
		if (!$mailerC->send( $mailing, $receivers)) $status = false;

		if ($status){
			$successM .= _JNEWS_FWDTOFRIEND_SUCCESS.$receiversName.'.<br>';
		}else{
			$successM .= _JNEWS_FWDTOFRIEND_FAILED.$receiversName.'.<br>';
		}
    }

	$url = JURI::current().'?option='.JNEWS_OPTION.'&act=fwdtofriend&frmname='.base64_encode($mailing->fromname).'&frmemail='.base64_encode($mailing->fromemail).'&mailingid='.$mailing->id;
	jNews_Tools::redirect($url, $successM);
}

/**
 * <p>Function to replace {fwdtofriend:Forward to a friend.} tag to a forward to a friend link</p>
 * @param string $content - the html/text version of mailing
 * @param object $receiver - receiver of the newsletter
 */
function fwdtofriendTransform( $content, $fwdObj=null ) {

	if ( empty($fwdObj->subscriber) ) {
		$my = JFactory::getUser();
		$fwdObj->subscriber->name = $my->name;
		$fwdObj->subscriber->email = $my->email;
		$fwdObj->subscriber->receive_html = true;
	}
	JPluginHelper::importPlugin( 'jnews' );
	$plugin =& JPluginHelper::getPlugin('jnews', 'forwardtofriend');
	jimport('joomla.html.parameter');
	$params = new JParameter( $plugin->params );
	$paramView = $params->get( 'viewtemplate', 'notemplate' );	
	if( version_compare(JVERSION,'1.6.0','<') ){ //j15
		$viewtemp = $paramView == 'notemplate' ? true: false;
	}else{ //j16
		$viewtemp = false;
	}	
	
	$Itemid = $GLOBALS[JNEWS.'itemidAca'];
	$fwdtofriendhtml='';
	$fwdtofriendtext='';

	##catches all the viewonline tags on the newsletter html and text body
// 	preg_match_all('#{fwdtofriend:.{3,}#', $content, $tags);
//	preg_match_all('#\{fwdtofriend:.{3,}\}#', $content, $tags);
//	preg_match_all('#\{tag:fwdtofriend.{3,}\}#', $content, $tags);
	preg_match_all('#\{tag:fwdtofriend.{3,}?\}#', $content, $tags);

	$replace = array();
 	$replacebyHTML = array();
 	$replacebyText = array();

 	if(!empty($tags[0])){
 		##create the link
	 	$frmEmail = base64_encode($fwdObj->subscriber->email);
		$frmName = base64_encode($fwdObj->subscriber->name);
		$link = 'option='.JNEWS_OPTION.'&act=fwdtofriend&frmname='.$frmName.'&frmemail='.$frmEmail.'&html='.$fwdObj->subscriber->receive_html.'&mailingid='.$fwdObj->mailingid.'&listid='.$fwdObj->listid.'&Itemid='.$Itemid;
		$link = jNews_Tools::completeLink($link,false,$GLOBALS[JNEWS.'use_sef'], $viewtemp );

 		foreach ($tags[0] as $tag){
//			$isolate = explode(':',$tag);
			$isolate = explode('tag:fwdtofriend name=',$tag);
			$details = explode('}',$isolate[1]);

			if(!empty($replace[$tag])) continue;
			$replace[$tag] = $tag;
			$fwdtofriendhtml.= '<a href="' . $link . '"><span class="aca_fwdtofriend">'.$details[0].'</span></a>';
			$fwdtofriendtext.= "".' * '.$details[0].' ( '. $link . ' )';

			$replacebyHTML[$tag] = $fwdObj->subscriber->receive_html ? $fwdtofriendhtml : $fwdtofriendtext;
		}
 	}
	##replace the tag with the exact link
 	$content = str_replace($replace,$replacebyHTML,$content);
 }
