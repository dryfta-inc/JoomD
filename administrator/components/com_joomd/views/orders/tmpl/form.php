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

defined('_JEXEC') or die('Restricted access');

if($this->abase)
	require_once(JPATH_ADMINISTRATOR.'/components/com_joomd/helpers/common_header.php');

$tooltip = Joomdui::getTooltip();

$tooltip->initialize('.hasTip');

?>
<div id="element-box">
  <div class="t">
    <div class="t">
      <div class="t"></div>
    </div>
  </div>
<div class="m">

<div id="joomdpanel">
<div class="poploadingbox"></div>

<form action="index.php?option=com_joomd&view=orders" method="post" name="adminform" id="adminform" enctype="multipart/form-data" >
<div class="col101">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'ORDERDETAIL' ); ?></legend>
<table class="admintable">
	
  <tr>
    <td class="key"><?php echo JText::_('USERNAME'); ?>:</td>
    <td colspan="2"><?php echo $this->item->username; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PACKAGENAME'); ?>:</td>
    <td colspan="2"><?php echo $this->item->pname; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('AMOUNT'); ?>:</td>
    <td colspan="2"><?php echo $this->item->payment_price; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PAYMENTCURRENCY'); ?>:</td>
    <td colspan="2"><?php echo $this->item->payment_currency; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('PAYMENTMETHOD'); ?>:</td>
    <td colspan="2"><?php echo $this->item->payment_method; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('ORDERSTATUS'); ?>:</td>
    <td colspan="2"><?php echo $this->item->order_status; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('COUPONREMAINING'); ?>:</td>
    <td colspan="2"><?php echo $this->item->credit; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('CEXPIREDATE'); ?>:</td>
    <td colspan="2"><?php echo $this->item->credit_expiry; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('TRANSID'); ?>:</td>
    <td colspan="2"><?php echo $this->item->txn_id; ?></td>
  </tr>
  <tr>
    <td class="key"><?php echo JText::_('ACCOUNTCREATEDATE'); ?>:</td>
    <td colspan="2"><?php echo $this->item->createdon; ?></td>
  </tr>
    
</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_joomd" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="packages" />
<input type="hidden" name="abase" value="1" />
</form>


<div class="clr"></div>

</div>

</div>
  <div class="b">
    <div class="b">
      <div class="b"></div>
    </div>
  </div>
</div>
<div class="clr"></div>