<?php
/*
* @Author: Nurte
* @package www.nurte.pl Nurte Google AdSense Module
* @copyright Copyright (C) 2010 Nurte sp. z o.o. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @Version 1.2.0.0
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).DS.'helper.php');

$mod_nurte_google_adsense = ModNurteGoogleAdsense::mod_nurte_google_adsense( $params );

echo $mod_nurte_google_adsense;

?>
