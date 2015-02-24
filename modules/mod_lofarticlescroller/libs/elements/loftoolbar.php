<?php 

/**
 * $ModDesc
 * 
 * @version   $Id: $file.php $Revision
 * @package   modules
 * @subpackage  $Subpackage.
 * @copyright Copyright (C) November 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */
 
// no direct access
defined('_JEXEC') or die;
/**
 * Get a collection of categories
 */
class JFormFieldLoftoolbar extends JFormField {
	
	/*
	 * Category name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'fgroup'; 	
	
	/**
	 * fetch Element 
	 */
	protected function getInput(){		
		// echo '<pre>'.print_r($this->element,1);  
	  ?> 
	<ul class="lof-toolbar-items">
    	<?php foreach( $this->element->children() as $option ) { ?>
    	<li><?php echo $option->data();?></li>
        <?php } ?>
    </ul>	
		
<?php 
	}
	function getLabel(){
		return ;	
	}
}

?>
