<?php
/**
 * @version   1.9 February 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokNavMenu2XRenderer extends RokMenuDefaultRenderer
{
    public function renderHeader(){
        parent::renderHeader();
        $doc = &JFactory::getDocument();

        foreach($this->layout->getScriptFiles() as $script){
            $doc->addScript($script['relative']);
        }

        foreach($this->layout->getStyleFiles() as $style){
            $doc->addStyleSheet($style['relative']);
        }
        $doc->addScriptDeclaration($this->layout->getInlineScript());
        $doc->addStyleDeclaration($this->layout->getInlineStyle());
    }
}
