<?php/** * @package 	mod_bt_contentslider - BT ContentSlider Module * @version		1.1 * @created		Oct 2011 * @author		BowThemes * @email		support@bowthems.com * @website		http://bowthemes.com * @support		Forum - http://bowthemes.com/forum/ * @copyright	Copyright (C) 2011 Bowthemes. All rights reserved. * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL * */// no direct accessdefined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldAbout extends JFormField {
	protected $type = 'About';
	protected function getInput() {
		return '<div id="bt-about">					<div class="bt-desc"><a href="http://bowthemes.com" target="_blank"><img src="'.JURI::root().$this->element['path'].'/logo.png"></a>						'.JText::_("BT_ABOUT_DESC").'												<p>						<a class="social-f" href="https://www.facebook.com/bowthemes" target="_blank">facebook</a><a class="social-t" href="http://twitter.com/BowThemes" target="_blank">twitter</a> <a class="social-rss" href="#">rss</a><a class="social-g" href="http://bowthemes.com" target="_blank">group</a></p>					</div>					<br clear="both">					<div class="bt-license">'.JText::_("BT_ABOUT_LICENSE").'</div>				</div>';
	}
}
/* eof */?>