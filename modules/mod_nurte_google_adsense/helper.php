<?php
/*
* @Author: Nurte
* @package www.nurte.pl Nurte Google AdSense Module
* @copyright Copyright (C) 2010 Nurte sp. z o.o. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @Version 1.2.0.0
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class ModNurteGoogleAdsense
{
    function mod_nurte_google_adsense( $params )   {

	$nga_ip_array = explode( ';', trim( $params->get( 'nga_ip_list' ) ) );
	$flag = 1;	
	$result = '';
	$userIP = ModNurteGoogleAdsense::getip();	

	if($nga_ip_array){
		if( is_array( $nga_ip_array ) ) {        
			foreach($nga_ip_array as $ip_address) { 
				if ($userIP == trim($ip_address)) {
					$flag = 0;
				}
			}
		} else {	
			if ($userIP == trim($nga_ip_array)) {
				$flag = 0;
			}
		}	
	}

	if ( $flag ){ 	
		$result  = trim($params->get('nga_adscode'))
			  ." <noscript>JavaScript must be enabled in order for you to use Nurte Google AdSense Module. However, it seems JavaScript is either disabled or not supported by your browser. To use Nurte Google AdSense Module, enable JavaScript by changing your browser options, then <a href=\"\">try again.</a></noscript>";
	}else{
		$result  = trim($params->get('nga_alternate_content'));
	}

	return $result;

    }

    function validip($ip) {
	if (!empty($ip) && ip2long($ip)!=-1) {
	 	$reserved_ips = array (
 			array('0.0.0.0','2.255.255.255'),
 			array('10.0.0.0','10.255.255.255'),
	 		array('127.0.0.0','127.255.255.255'),
	 		array('169.254.0.0','169.254.255.255'),
	 		array('172.16.0.0','172.31.255.255'),
	 		array('192.0.2.0','192.0.2.255'),
	 		array('192.168.0.0','192.168.255.255'),
	 		array('255.255.255.0','255.255.255.255')
 		);
		foreach ($reserved_ips as $r) {
 			$min = ip2long($r[0]);
	 		$max = ip2long($r[1]);
 			if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
 		}
 		return true;
 	} else {
 		return false;
 	}
    }
 
    function getip() {
	if (isset($_SERVER["HTTP_CLIENT_IP"])){
		if (ModNurteGoogleAdsense::validip($_SERVER["HTTP_CLIENT_IP"])) {
 			return $_SERVER["HTTP_CLIENT_IP"];
 		}
	}
	
 	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
		foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
			if (ModNurteGoogleAdsense::validip(trim($ip))) {
 				return $ip;
 			}
 		}
	}

 	if (isset($_SERVER["HTTP_X_FORWARDED"])){
		if (ModNurteGoogleAdsense::validip($_SERVER["HTTP_X_FORWARDED"])) {
 			return $_SERVER["HTTP_X_FORWARDED"];
 		} 
	}

 	if (isset($_SERVER["HTTP_FORWARDED_FOR"])){
		if (ModNurteGoogleAdsense::validip($_SERVER["HTTP_FORWARDED_FOR"])) {
 			return $_SERVER["HTTP_FORWARDED_FOR"];
 		}
	}
	
	if (isset($_SERVER["HTTP_FORWARDED"])){
		if (ModNurteGoogleAdsense::validip($_SERVER["HTTP_FORWARDED"])) {
 			return $_SERVER["HTTP_FORWARDED"];
 		}
	}

	return $_SERVER["REMOTE_ADDR"];
    }

}

?>