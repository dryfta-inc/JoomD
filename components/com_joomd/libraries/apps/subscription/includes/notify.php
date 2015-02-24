<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD Subscription Application
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

mail('danish@skoolsonline.com', 13, 'test');

$my_path = dirname(__file__);

if (file_exists($my_path . "/../../../../../../configuration.php")) {
	
	$absolute_path = dirname($my_path . "/../../../../../../configuration.php");
	require_once ($my_path . "/../../../../../../configuration.php");

} else {
	error_log(JText::_("CONFIG_FILE_NOT_FOUND"));
	exit;
}
mail('danish@skoolsonline.com', 26, 'test');
$absolute_path = realpath($absolute_path);
// Set up the appropriate CMS framework
if (class_exists('JConfig')) {
	mail('danish@skoolsonline.com', 30, 'test');
	define('_JEXEC', 1);
	define('JPATH_BASE', $absolute_path);
	
	// Load the framework
	require_once (JPATH_BASE . '/includes/defines.php');
	require_once (JPATH_BASE . '/includes/framework.php');
	// create the mainframe object
	$mainframe =  JFactory :: getApplication('site');
	// Initialize the framework
	$mainframe->initialise();

	$sitename = $mainframe->getCfg('sitename');
	$mailfrom = $mainframe->getCfg('mailfrom');
	$fromname = $mainframe->getCfg('fromname');
	
	$db=  JFactory::getDBO();
	
	// Require the Library
	require_once( JPATH_ROOT . '/components/com_joomd/libraries/core.php' );
	
	// Initialize the library
	$jdapp = Joomd::getApp();
	
	$lang = JFactory::getLanguage();
	
	$lang->load('com_joomd');
	$lang->load('app_subscription', JPATH_SITE.'/components/com_joomd');
	
	$config = Joomd::getConfig('subscription');
	
	$paypal_email	= $config->paypal_email;
	$test			= $config->sandbox;
	$currency		= $config->currency;
	
	if($test)
		$hostname = 'ssl://www.sandbox.paypal.com';
	else
		$hostname = 'ssl://www.paypal.com';
	
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	
	foreach ($_POST as $key => $value) {
	
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
		
	}
	mail('danish@skoolsonline.com', 79, 'test');
	
	
	// post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ($hostname, 443, $errno, $errstr, 30);
	
	// assign posted variables to local variables
	$invoice = $_POST['invoice'];
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = round($_POST['mc_gross'], 2);
	$mc_amount3 = round($_POST['mc_amount3'], 2);
	$payment_currency = $_POST['mc_currency'];
	$txn_id = $_POST['txn_id'];
	$txn_type = $_POST['txn_type'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];
	
	if (!$fp) {
		
		error_log(JText::_('PAYPAL_SERVER_NOT_CONNECTED'));	
		exit;
	// HTTP ERROR
	} else {
		mail('danish@skoolsonline.com', 107, 'test');
		$qv = "select * FROM `#__joomd_orders` WHERE `order_number`=" . $db->quote($invoice);
		$db->setQuery($qv);
		$results = $db->loadObject();
		
		if(empty($results))	{
			error_log(JText::sprintf('NO_ORDER_WITH_INVOICE', $invoice));
			exit;
		}
		
		$results->payment_price = round($results->payment_price, 2);
		mail('danish@skoolsonline.com', 118, $req);
		
		fputs ($fp, $header . $req);
		
		while (!feof($fp)) {
		
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED") == 0) {
								
				$date =  JFactory::getDate();
				$now = $date->toMySQL();
				
				$query = 'select * from #__joomd_package where id = '.$db->Quote($results->packid);
				$db->setQuery( $query );
				$pack = $db->loadObject();
				
				if($txn_type == "subscr_signup")	{
					
					$query = 'update #__joomd_orders set order_status = "c", recur_date = '.$db->Quote($results->payment_date).' where id = '.$db->Quote($results->id);
					$db->setQuery( $query );
					$db->query();
					
					$msg = JText::sprintf('SUBSCR_SIGNUP_MSG', $invoice, $results->userid, $pack->name);
					
					$subject = JText::sprintf('SUBCREATEDONYOURSITE', $sitename);
					
					Joomd::notify($subject, $msg);
					exit;
					
				}
				
				elseif($txn_type == "subscr_modify")	{
					
					if($receiver_email <> $paypal_email)	{
						
						$msg = JText::sprintf('SUBSCR_MODIFY_ERROR_DIFF_EMAIL', $invoice, $results->userid, $paypal_email, $receiver_email);
											    
						$subject = JText::sprintf('INVALIDTRANONYOURSITE', $sitename);
						
						Joomd::notify($subject, $msg);
						exit;
					}
					
					if($mc_amount3 <> $results->payment_price or $results->payment_currency <> $payment_currency)	{
					   	
						$msg = JText::sprintf('SUBSCR_MODIFY_ERROR_DIFF_AMOUNT', $invoice, $results->userid, $results->payment_price, $mc_amount3);
					    
						$subject = JText::sprintf('INVALIDTRANONYOURSITE', $sitename);
						
						Joomd::notify($subject, $msg);
						exit;
					}
					
										
					$query = 'update #__joomd_orders set order_status = "c", txn_id = "'.$txn_id.'" where id = '.$db->Quote($results->id);
					
					$db->setQuery( $query );
					
					if(!$db->query())	{
						$msg = JText::sprintf("DB_ERROR_MSG", $invoice, $results->userid, $db->getErrorMsg());
					    
						$subject = JText::sprintf('DERRORONUSITE', $sitename);
						
						Joomd::notify($subject, $msg);
					}
					
					$subject = JText::sprintf("PAYPALIPNTRANONUSITE", $sitename);
					
					$msg = JText::sprintf('SUBSCR_MODIFY_MSG', $txn_type, $payer_email, $results->id, $results->userid);
										
					Joomd::notify($subject, $msg);					
					
				}
				
				elseif($txn_type == "subscr_cancel")	{
					
					$query = 'update #__joomd_orders set order_status = "e" where id = '.$db->Quote($results->id);
					$db->setQuery( $query );
					$db->query();
					
					$msg = JText::sprintf('SUBSCR_CANCEL_MSG', $invoice, $results->userid);
					
					$subject = JText::sprintf('SUBSCANCELONYOURSITE', $sitename);
					
					Joomd::notify($subject, $msg);
					exit;
					
				}
				
				elseif($payment_status == "Refunded")	{
										
					$query = 'update #__joomd_orders set order_status = "r" where id = '.$db->Quote($results->id);
					$db->setQuery( $query );
					$db->query();
					
					$query = 'update #__joomd_pusers set packid = 0, credit=0, remaining=0, expiry = '.$db->Quote($db->getNullDate()).' where userid = '.$results->userid;
					$db->setQuery( $query );
					$db->query();
					
					$msg = JText::sprintf('SUBSCR_REFUND_MSG', $invoice, $results->userid);
					
					$subject = JText::sprintf('SUBSREFUNDONYOURSITE', $sitename);
					
					Joomd::notify($subject, $msg);
					exit;
					
				}
					
				elseif($txn_type == "subscr_payment" and $payment_status == "Completed")	{
					
					if($results->txn_id == $txn_id)	{
						
						$msg = JText::sprintf('TRANS_ALREADY_PROCESSED', $invoice, $results->id, $txn_id);
											    
						error_log($msg);
						exit;
					}
					
					if($receiver_email <> $paypal_email)	{
						
						$msg = JText::sprintf('SUBSCR_MODIFY_ERROR_DIFF_EMAIL', $invoice, $results->userid, $paypal_email, $receiver_email);
					    
						$subject = JText::sprintf('INVALIDTRANONYOURSITE', $sitename);
						
						Joomd::notify($subject, $msg);
						exit;
					}
					
					if($payment_amount <> $results->payment_price or $results->payment_currency <> $payment_currency)	{
					   
					   $msg = JText::sprintf('SUBSCR_MODIFY_ERROR_DIFF_AMOUNT', $invoice, $results->userid, $results->payment_price, $mc_amount3);
					    
						$subject = JText::sprintf('INVALIDTRANONYOURSITE', $sitename);
						
						Joomd::notify($subject, $msg);
						exit;
					   
					}
					
										
					$query = 'update #__joomd_orders set txn_id = "'.$txn_id.'" where id = '.$db->Quote($results->id);
					$db->setQuery( $query );
					$db->query();
					
					$query = 'select count(*) from #__joomd_pusers where userid = '.$db->Quote($results->userid);
					$db->setQuery( $query );
					$count = $db->loadResult();
					
					if(!$count)	{
						
						switch($pack->unit)
						{
							
							case 'D':
							$unit = ' day';
							break;
							
							case 'M':
							$unit = ' month';
							break;
							
							case 'W':
							$unit = ' week';
							break;
							
							case 'Y':
							$unit = ' year';
							break;
							
						}
						
						$query = 'insert into #__joomd_pusers (userid, packid, credit, remaining, expiry) values ('.$db->Quote($results->userid).', '.$db->Quote($results->packid).', '.$db->Quote($pack->items).', '.$db->Quote($pack->items).', '.$db->Quote($pack->period).', date_add('.$db->Quote($results->payment_date).', interval '.$pack->period.$unit.'))';
						
						$db->setQuery( $query );
					
						if(!$db->query())	{
							
							$msg = JText::sprintf("DB_ERROR_MSG", $invoice, $results->userid, $db->getErrorMsg());
							
							$subject = JText::sprintf('DERRORONUSITE', $sitename);
							
							Joomd::notify($subject, $msg);
						}
						
					}
										
					$subject = JText::sprintf("PAYPALIPNTRANONUSITE", $sitename);
					
					$msg = JText::sprintf('PAYPAL_IPN_TRANSACTION', $txn_id, $payer_email, $results->id, $payment_status);
										
					Joomd::notify($subject, $msg);
					
					
				}			
								
				
				
				exit;
		
			}
			else if (strcmp ($res, "INVALID") == 0) {
				
				// log for manual investigation
				error_log(JText::_("INVALID_REPONSE_FROM_PAYPAL"));
				exit;
				
			}
			
		}
			
		
			
		fclose ($fp);
	
	}
	
}
else	{
	
	error_log(JText::_("CONFIG_FILE_NOT_FOUND"));
	exit;
	
}

?>