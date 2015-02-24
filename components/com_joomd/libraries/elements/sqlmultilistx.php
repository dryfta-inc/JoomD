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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');
 
class JFormFieldSQLMultiListX extends JFormField
{
        
        protected $type = 'SQLMultiListX';
		
		public function getLabel() {
			return parent::getLabel();
		}
 
        public function getInput()
        {		
				
				$doc =  JFactory::getDocument();
				 
                // Construct the various argument calls that are supported.
                $attribs       = ' ';
                if ($v = (string)$this->element[ 'size' ]) {
                        $attribs       .= 'size="'.$v.'"';
                }
                if ($v = (string)$this->element[ 'class' ]) {
                        $attribs       .= 'class="'.$v.'"';
                } else {
                        $attribs       .= 'class="inputbox"';
                }
                if ($m = (string)$this->element[ 'multiple' ])
                {
                        $attribs       .= ' multiple="multiple"';
                }
				if ($v = (string)$this->element[ 'onchange' ]) {
                        $attribs       .= 'onchange="'.$v.'"';
                }
				
				if ($v = (string)$this->element[ 'script' ]) {
                	$doc->addScript($v);
                }
 
                // Query items for list.
                $db =  JFactory::getDBO();
                $db->setQuery((string)$this->element['query']);
                $key = (string)($this->element['key_field'] ? $this->element['key_field'] : 'id');
                $val = (string)($this->element['value_field'] ? $this->element['value_field'] : 'value');

                $options = array ();
				
				foreach ($this->element->children() as $option)	{
					if ($option->getName() == 'option')
					{
						$options[]= array($key=> $option['value'],$val => JText::_($option[0]));
					}
				}
				
                $rows = $db->loadAssocList();
                foreach ($rows as $row){
                    $options[]=array($key=>$row[$key],$val=>$row[$val]);
                }
                if($options){
                	return JHTML::_('select.genericlist',$options, $this->name, $attribs, $key, $val, $this->value, $this->id);
                }
        }
}
