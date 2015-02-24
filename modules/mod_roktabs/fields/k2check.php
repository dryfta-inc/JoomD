<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


// no direct access
defined('_JEXEC') or die();

/**
 * @package    RocketTheme
 * @subpackage roktabs.fields
 */
class JFormFieldK2check extends JFormField
{
    /**
     * @var string
     */
    public $type = 'K2Check';

    /**
     * @return string
     */
    public function getInput()
    {
        if (defined('K2_CHECK')) return;

        $k2       = JPATH_SITE . DS . "components" . DS . "com_k2" . DS . "k2.php";
        $document =& JFactory::getDocument();

        if (!file_exists($k2)) {

            define('K2_CHECK', 0);
            $warning_style = "style='background: #FFF3A3;border: 1px solid #E7BD72;color: #B79000;display: block;padding: 8px 10px;'";

            $list   = '#k2-label, #paramscatfilter0, #paramscategory_id, #paramsFeaturedItems, #paramsitemImgSize, #paramsk2_check-lbl';
            $script = "
				window.addEvent('domready', function() {
					var option = $$('#paramscontent_type option[value=k2]');
					if (option.length) option[0].remove();
					$$('$list').each(function(el) {
						el.getParent().getParent().remove();
					});
					var x = function() {
						$$('.jpane-slider')[0].setStyle('height', '');
					};
					x.delay(600);
				});
			";

            $document->addScriptDeclaration($script);
            return "<span $warning_style><strong>K2 Component</strong> Not Found. In order to use the <strong>K2 Content</strong> type, you will need to <a href=\"http://k2.joomlaworks.gr\" target=\"_blank\">download and install it</a>.</span>";
        } else {
            define('K2_CHECK', 1);
            $success_style = "style='background: #d2edc9;border: 1px solid #90e772;color: #2b7312;display: block;padding: 8px 10px;'";

            $script = "
				var \$flatten = function(el){
					var array = [];
					for (var i = 0, l = el.length; i < l; i++){
						var type = \$type(el[i]);
						if (!type) continue;
						array = array.concat((type == 'array' || type == 'collection' || type == 'arguments') ? \$flatten(el[i]) : el[i]);
					}
					return array;
				}
				
				window.addEvent('domready', function() {
					var joomla = $('joomla-label');
					var k2 = $('k2-label');
					var selector = $('paramscontent_type');
					var label = $('paramscontent_type-lbl');
					if (!joomla || !k2 || !selector || !label) return;
					
					var joomla_bits = {'items': [], 'titles': []}, k2_bits = {'items': [], 'titles': []};
					var enabledColor = label.getStyle('color'), disabedColor = '#ccc';
					
					var next = joomla.getParent().getParent().getNext();
					while (next.getFirst().getNext().getFirst().id != 'k2-label') {
						joomla_bits['titles'].push(next.getFirst().getFirst());
						joomla_bits['items'].push(next.getFirst().getNext().getChildren());
						next = next.getNext();
					}
					
					var next = k2.getParent().getParent().getNext();
					while (next.getFirst().getNext().getFirst().id != 'content-label') {
						k2_bits['titles'].push(next.getFirst().getFirst());
						k2_bits['items'].push(next.getFirst().getNext().getChildren());
						next = next.getNext();
					}
					
					joomla_bits['items'] = \$flatten(joomla_bits['items']);
					k2_bits['items'] = \$flatten(k2_bits['items']);
					selector.addEvent('change', function() {
						switch(this.value) {
							case 'joomla':
								$$(joomla_bits['titles']).setStyle('color', enabledColor);
								$$(k2_bits['titles']).setStyle('color', disabedColor);
								
								$$(joomla_bits['items']).setProperty('disabled', '');
								$$(k2_bits['items']).setProperty('disabled', 'disabled');
								break;
							case 'k2':	
								$$(k2_bits['titles']).setStyle('color', enabledColor);
								$$(joomla_bits['titles']).setStyle('color', disabedColor);
								
								$$(k2_bits['items']).setProperty('disabled', '');
								$$(joomla_bits['items']).setProperty('disabled', 'disabled');
								break;
						}
					}).fireEvent('change');
				});
			";
            $document->addScriptDeclaration($script);
            return "<span $success_style><strong>K2 Component</strong> has been found and is available to use.</span>";
        }
    }
}