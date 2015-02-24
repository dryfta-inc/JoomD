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


//Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

require_once(JPATH_SITE.'/components/com_joomd/libraries/app.php');

class JoomdAppSubscription extends JoomdApp {
	
	
	function __construct()
	{
		
		$app = JFactory::getApplication();
		
		parent::__construct();
		
		$this->initialize();
				
	}
	
	function initialize()
	{
		
		static $init = false;
		
		if($init)
			return;
				
		$this->loadLanguage();
		$this->loadAssets();
		
		if($this->isSite())
			$this->isExpired();
		
		$init = true;
		
	}
	
	function loadLanguage()
	{
		
		static $loaded = false;
		
		if($loaded)
			return true;
		
		$lang = JFactory::getLanguage();
		
		$lang->load('app_subscription', JDPATH_BASE);
		
		$loaded = true;
		
		return true;
		
	}
	
	function loadAssets()
	{
		
		$doc = JFactory::getDocument();
		
		$doc->addStyleSheet('components/com_joomd/assets/css/subscription.css');
		
	}
	
	function add_submenu()
	{
		$view = JRequest::getCmd('view', '');
		
		$active = $view == 'packages';
	
		JSubMenuHelper::addEntry( '<span class="hasTip" title="'.JText::_('SUBMENU_PACKAGES_DESCR').'">'.JText::_('PACKAGES').'</span>' , 'index.php?option=com_joomd&view=packages' , $active );
		
		$active = $view == 'orders';
	
		JSubMenuHelper::addEntry( '<span class="hasTip" title="'.JText::_('SUBMENU_ORDERS_DESCR').'">'.JText::_('ORDERS').'</span>' , 'index.php?option=com_joomd&view=orders' , $active );
		
	}
	
	function icon_display()
	{
		
		$html = '<div class="icon" title="'.JText::_('SUBMENU_PACKAGES_DESCR').'"><a href="index.php?option=com_joomd&view=packages&task=add&cid[]=0"><img src="components/com_joomd/assets/images/icon-48-package-add.png" alt="Packages" /><span>'. JText::_('ADDPACKAGE').'</span></a></div>';
		
		return $html;
		
	}
	
	function getConfig()
	{
		$query = 'select * from #__joomd_packagesconfig';
		$this->_db->setQuery( $query );
		$config = $this->_db->loadAssoc();
		
		return $config;
		
	}
	
	function getUser()
	{
		
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		$now = $date->toMySQL();
		
		$query = 'select * from #__joomd_pusers where userid = '.$user->id.' and datediff('.$this->_db->Quote($now).', expiry) < 1 or expiry = "0000-00-00 00:00:00"';
		$this->_db->setQuery( $query );
		$data = $this->_db->loadAssoc();
		
		if(empty($data))	{
			
			$data['userid'] = null;
			$data['packid'] = null;
			$data['credit'] = null;
			$data['remaining'] = null;
			$data['expiry'] = null;
			$data['free'] = null;
			
		}
		
		return $data;
		
	}
	
	function getPack()
	{
		
		$data = self::getUser();
		
		if($data['packid'])	{
		
			$query = 'select * from #__joomd_package where id = '.(int)$data['packid'];
			$this->_db->setQuery( $query );
			$pack = $this->_db->loadObject();
			
			$registry = new JRegistry();
			$registry->loadString($pack->params);
			$pack->params = $registry;
			
			return $pack;
		
		}
		
		return null;
		
	}
	
	function config_display()
	{
				
		$query = 'select * from #__joomd_packagesconfig';
		$this->_db->setQuery( $query );
		$config = $this->_db->loadObject();
		
		
		ob_start();
		
		?>
        	<fieldset class="adminform">
				<legend><?php echo JText::_( 'SUB_CONFIG' ); ?></legend>
            
                <table class="admintable">
                <tr>
                    <td class="key" style="width:200px;"><label class="hasTip" title="<?php echo JText::_('CONFIG_PACKAGE_MODE'); ?>"><?php echo JText::_('TESTMODE'); ?></label></td>
                    <td><input type="checkbox" name="sandbox" id="sandbox" value="1" <?php if($config->sandbox) echo 'checked="checked"'; ?> /></td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_PACKAGE_PAYPAL_EMAIL'); ?>"><?php echo JText::_('PAYPALEMAIL'); ?></label></td>
                    <td><input type="text" name="paypal_email" id="paypal_email" value="<?php echo $config->paypal_email; ?>" size="40" /></td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_PACKAGE_GRACE_PERIOD'); ?>"><?php echo JText::_('GRACEPERIOD'); ?></label></td>
                    <td><input type="text" name="grace_period" id="grace_period" value="<?php echo $config->grace_period; ?>" size="5" /></td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_PACKAGE_CURRENCY'); ?>"><?php echo JText::_('CURRENCY'); ?></label></td>
                    <td><input type="text" name="currency" id="currency" value="<?php echo $config->currency; ?>" size="5" /></td>
                </tr>
                <tr>
                    <td class="key"><label class="hasTip" title="<?php echo JText::_('CONFIG_PACKAGE_ENABLE_EDITING'); ?>"><?php echo JText::_('ENABLEEDITINGAFTEREXPIRY'); ?></label></td>
                    <td><input type="checkbox" name="entryedit" id="editentryedit" value="1" <?php if($config->entryedit) echo 'checked="checked"'; ?> /></td>
                </tr>
                </table>
        	</fieldset>
        <?php
		
		$html = ob_get_contents();
		
		ob_end_clean();
		
		return $html;
		
	}
	
	function config_save($post, $parent)
	{
				
		$sandbox		= JRequest::getInt('sandbox', 1);
		$paypal_email	= JRequest::getVar('paypal_email', '');
		$grace_period	= JRequest::getInt('grace_period', 0);
		$entryedit		= JRequest::getInt('entryedit', 0);
		$currency		= JRequest::getVar('currency', 0);
		
		$insert = new stdClass();
		
		$insert->id				= 1;
		$insert->sandbox		= $sandbox;
		$insert->paypal_email	= $paypal_email;
		$insert->grace_period	= $grace_period;
		$insert->entryedit		= $entryedit;
		$insert->currency		= $currency;
		
		if(!$this->_db->updateObject('#__joomd_packagesconfig', $insert, 'id'))	{
			$parent->setError($this->_db->stderr());
			return false;
		}
		
		return true;
		
	}
	
	function front_display()
	{
		
		$user = Joomd::getUser();
		
		$query = 'select * from #__joomd_pusers where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$data = $this->_db->loadAssoc();
		
		settype($data, 'object');
		
		if($data->packid)	{
			
			$pack = self::getPack();
			
			if(!empty($pack))	{
				
				 $html = '<div class="item_block">'.JText::_('SUB_PACK').': '.$pack->name.'</div>';
				 $html .= '<div class="item_block">'.JText::_('TOTAL_ITEMS').': '.(($data->credit==0)?JText::_('UNLIMITED'):$data->credit).'</div>';
				 $html .= '<div class="item_block">'.JText::_('REMAIN_ITEMS').': '.(($data->credit==0)?JText::_('UNLIMITED'):$data->remaining).'</div>';
				 $html .= '<div class="item_block">'.JText::_('RENEW_DATE').': '.date('d F, Y H:i', strtotime($data->expiry)).'</div>';
				 
				
			}
			
		}
		
		ob_start();
		
		$this->displaysubscribewidget();
		
		$html .= ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	function displaysubscribewidget($options=array())
	{
		
		$user = Joomd::getUser();
		
		if($user->get('guest'))	{
			
			echo JText::sprintf('LOGINTOACCESS', JRoute::_('index.php?option=com_users&view=login'));
			
		}
		
		else	{
		
			$query = 'select * from #__joomd_pusers where userid = '.$user->id;
			$this->_db->setQuery( $query );
			$data = $this->_db->loadAssoc();
			
			$value = isset($data['packid'])?JText::_('CHANGE'):JText::_('SUBSCRIBE');
			
			$query = 'select * from #__joomd_package where published = 1 order by name asc';
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			
		?>
        
        <script type="text/javascript">
		
			$jd(function()	{
						 
				$jd('form[name="subscribe_form"]').live('submit', function(event)	{
					
					event.preventDefault();
										
					if(event.target.packid.value == 0)	{
						alert('<?php echo JText::_('PLZ_SELECT_PACK'); ?>');
						return false;
					}
					
					if(event.target.packid.value == <?php echo (int)$data['packid']; ?>)	{
						alert('<?php echo JText::_('ALREADY_SAME_PACK'); ?>');
						return false;
					}
					
					var data = $jd(this).serializeArray();
					
					$jd.ajax({
						  url: "<?php echo JURI::root(); ?>index.php",
						  type: "post",
						  dataType:"json",
						  data: data,
						  beforeSend: function()	{
							$jd(".subscribe_widget .loadingblock").show();
						  },
						  complete: function()	{
							$jd(".subscribe_widget .loadingblock").hide();
						  },
						  success: function(data)	{
							  
							if(data.result == "success")	{
								$jd(".subscribe_widget .system_message").html(data.msg).slideDown().delay(2500).slideUp();
							}
							else if(data.result == "redirect")
								window.location = data.url;
							else
								alert(data.error);
								
						  },
						  error: function(jqXHR, textStatus, errorThrown)	{
							alert(textStatus);
						  }
					});
					
				});
						 
			});
		
		</script>
        
        <?php
			
			echo '<div class="subscribe_widget">
			<div class="system_message"></div>
			<div class="loadingblock"></div>';
			
			echo '<form action="'.JURI::root().'index.php" method="post" name="subscribe_form">';
			
				echo '<div class="field_label">'.JText::_('PACKAGE').'</div>';
				echo '<div class="field_value">';
				echo '<select name="packid"><option value="0">'.JText::_('SELECT_PACK').'</option>';
				for($i=0;$i<count($items);$i++)	{
					echo '<option value="'.$items[$i]->id.'"';
					if($items[$i]->id==$data['packid'])
						echo ' selected="selected"';
					echo '>'.$items[$i]->name.'</option>';
				}
				echo '</select></div>';
				
				echo '<div class="field_label"></div><div class="field_value"><input type="submit" name="subs" value="'.$value.'" /></div>';
			
			
			echo JHTML::_( 'form.token' );
			echo '
					<input type="hidden" name="abase" value="1">
					<input type="hidden" name="option" value="com_joomd">
					<input type="hidden" name="task" value="app_task">
					<input type="hidden" name="action" value="subscription-subscribe">';
			echo '</form>';
			
			echo '</div>';
		
		}
		
	}
	
	function subscribe()
	{
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('{"result":"error", "error":"Invalid Token"}') );
		
		$mainframe =  JFactory::getApplication();
		$user = Joomd::getUser();
		
		$obj = new stdClass();
		
		$obj->result = 'error';
		
		if($user->get('guest'))	{
			$obj->error = JText::_('PLSLOGTOACC');
			return $obj;
		}
		
		$packid = JRequest::getInt('packid', 0);
		
		$query = 'select * from #__joomd_pusers where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$data = $this->_db->loadAssoc();
		
		if(!$packid)	{
			$obj->error = JText::_('PLZ_SELECT_PACK');
			return $obj;
		}
		
		if($packid == $data['packid'])	{
			$obj->error = JText::_('ALREADY_SAME_PACK');
			return $obj;
		}
		
		$query = 'select * from #__joomd_package where published = 1 and id = '.$packid;
		$this->_db->setQuery( $query );
		$pack = $this->_db->loadObject();
		
		if(empty($pack))	{
			$obj->error = JText::_('PACK_NOT_FOUND');
			return $obj;
		}
		elseif($pack->amount > 0)	{
			
			$config = self::getConfig();
			
			$paypal_email	= $config['paypal_email'];
			$test			= $config['sandbox'];
			$currency		= $config['currency'];
			
			if(!empty($paypal_email))	{
				
				$date =  JFactory::getDate();
							
				$query = 'select * from #__joomd_orders where userid = '.$user->id;
				$this->_db->setQuery( $query );
				$order = $this->_db->loadObject();
				
				if(empty($order))	{
					
					$orderid = null;
					$order_number = $this->getOrder_number();
					$modify = 0;
					$free = 1;
					
				}
				else	{
					$orderid = $order->id;
					$order_number = $order->order_number;
					$modify = $order->order_status=="c"?2:0;
					$free = empty($data['free'])?1:$data['free'];
				}
				
				$insert = new stdClass();
				
				$insert->id = $orderid;
				$insert->userid = $user->id;
				$insert->order_number = $order_number;
				$insert->packid = $pack->id;
				$insert->payment_date = $date->toMySQL();
				$insert->payment_price = $pack->amount;
				$insert->payment_currency = $currency;
				$insert->payment_method = 'paypal';
				$insert->order_status = 'P';
				
				$insert->created = $date->toMySQL();
				
				if(empty($orderid))	{
					if(!$this->_db->insertObject('#__joomd_orders', $insert, 'id'))	{
						$obj->error = $this->_db->stderr();
						return $obj;
					}
					
					$orderid = $this->_db->insertid();
				}
				else	{
					if(!$this->_db->updateObject('#__joomd_orders', $insert, 'id'))	{
						$obj->error = $this->_db->stderr();
						return $obj;
					}
					
				}
				
				if($test)
					$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
				else
					$url = "https://www.paypal.com/cgi-bin/webscr";
											
				$post_variables = array(
					"business" => $paypal_email, 
					"cmd" => "_xclick-subscriptions", 
					"item_name" => JText::_('Order ID:')." " . $orderid . " - " . $pack->name, 
					"order_id" => $orderid, 
					"item_number" => $pack->id, 
					"invoice" => $order_number, 
					
					"address_override" => "1",
					"first_name" => $user->name,
					"email" => $user->email,
					"return" => JURI::base() . "index.php?option=com_joomd",  
					"notify_url" => JURI::base() . "components/com_joomd/libraries/apps/subscription/includes/notify.php", 
					"cancel_return" => JURI::base() . "index.php?option=com_joomd&task=app_task&action=subscription-success", 
					"a3" => round($pack->amount, 2),
					"p3" => $pack->period, 
					"t3" => $pack->unit, 
					"src" => "1", 
					"sra" => "1",
					"modify" => $modify,
					"no_note" => "1", 
					"currency_code" => $currency, 
			//		"cpp_header_image" => $vendor_image_url, 
					"page_style" => "primary" );
					
					$obj->url = $url.'?';
					
					foreach ($post_variables as $name => $value)
						$obj->url  .= $name. "=" . urlencode($value) ."&";
										
					$obj->result = 'redirect';
							
			}
			
			else	{
				$obj->error = JText::_('ERROR_CONTACT_ADMIN');
				return $obj;
			}
			
		}
		else	{
			
			if($data['free']===0)	{
				$obj->error = JText::_('ALREADY_USED_FREE');
				return $obj;
			}
			
			$insert = new stdClass();
			
			$insert->userid = $user->id;
			$insert->packid = $pack->id;
			$insert->credit = $pack->items;
			$insert->remaining = $pack->items;
			$insert->free = 0;
			
			switch($pack->unit)
			{
				
				case 'D':
				$unit = ' days';
				break;
				
				case 'M':
				$unit = ' months';
				break;
				
				case 'W':
				$unit = ' weeks';
				break;
				
				case 'Y':
				$unit = ' years';
				break;
				
			}
			
			$edate =  JFactory::getDate('+'.$pack->period.' '.$unit);
			$insert->expiry = $edate->toMySQL();
			
			if(empty($data['userid']))	{
				
				if(!$this->_db->insertObject('#__joomd_pusers', $insert, 'userid'))	{
					$obj->error = $this->_db->stderr();
					return $obj;
				}
				
			}
			else	{
				
				if(!$this->_db->updateObject('#__joomd_pusers', $insert, 'userid'))	{
					$obj->error = $this->_db->stderr();
					return $obj;
				}
				
			}
			
			$obj->result = 'success';
			$obj->msg = JText::_('SUBSCRIPTION_SUCCESS');
			
		}
		
		return $obj;
		
	}
	
	function getOrder_number($chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		
		$length = 15;
		
		$chars_length = (strlen($chars) - 1);
   		$string = $chars {rand(0, $chars_length)};
    	for ($i = 1; $i < $length; $i = strlen($string))
    	{
        	$r = $chars {rand(0, $chars_length)};
        	if ($r != $string {$i - 1})
        	$string .= $r;
    	}
    	return $string;
		
	}
	
	function isExpired()
	{
		
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		$now = $date->toMySQL();
		
		$query = 'select count(*) from #__joomd_pusers where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count < 1)	{
			$this->setError(JText::_('SUBSCRIBE_FIRST'));
			return true;
		}
		
		$query = 'select count(*) from #__joomd_pusers where userid = '.$user->id.' and datediff('.$this->_db->Quote($now).', expiry) > 0 and expiry <> "0000-00-00 00:00:00"';
		$this->_db->setQuery( $query );
		$count = $this->_db->loadResult();
		
		if($count)	{
			jexit();
			$query = 'select * from #__joomd_orders where userid '.$user->id.' and order_status = '.$this->_db->Quote('c');
			$this->_db->setQuery( $query );
			$order = $this->_db->loadObject();
			
			if(!empty($order))	{
				
				$query = 'select * from #__joomd_package where id = '.$order->packid;
				$this->_db->setQuery( $query );
				$pack = $this->_db->loadObject();
				
				if(empty($pack))	{
					$this->setError(JText::_('ERROR_CONTACT_ADMIN'));
					return true;
				}
				
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
				
				$query = 'update #__joomd_pusers set packid = '.$pack->id.', credit = '.$pack->items.', remaining = '.$pack->items.', expiry = date_add('.$this->_db->Quote($order->recur_date).', interval '.$pack->period.$unit.') where userid = '.$user->id;
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return true;
				}
				
			}
			else	{
				$this->setError(JText::_('ERROR_CONTACT_ADMIN'));
				return true;
			}
		
		}
		
		$data = self::getUser();
		
		if(empty($data))	{
			$this->setError(JText::_('MEMBERSHIP_EXPIRED'));
			return true;
		}
		elseif($data['credit'] > 0 and $data['remaining'] < 1)	{
			$this->setNotice(JText::_('NOT_ENOUGH_CREDIT'));
		}
		
		return $data['credit']==0?1:$data['remaining'];
		
	}
	
	function isAuthorised($groups, $action)
	{
		
		$config = $this->getConfig();
		$expired = $this->isExpired();
		
		$type = Joomd::getType();
		
		$pack = self::getPack();
		
		if($expired === true)	{
			
			if($action == "addaccess")	{
				return false;
			}
			
			elseif($action == 'editaccess' or $action == 'editall')	{
				if(!$config['entryedit'])	{
					return false;
				}
			}
			
		}
		elseif($expired < 1 and $action == "addaccess")	{
			$this->setError(JText::_('NOT_ENOUGH_CREDIT'));
			return false;
		}
		
		if(empty($pack) or !in_array($type->id, $pack->params->get('types')))	{
			$this->setError(JText::_('AUTH_NOACCESS'));
			return false;
		}
		
		return true;
		
	}
	
	function canEdit($groups)
	{
		
		$expired = $this->isExpired();
		$config = $this->getConfig();
		
		if($expired === true)	{
			if($config['entryedit'])	{
				$this->setNotice(JText::_('MEMBERSHIP_EXPIRED'));
			}
			else	{
				$this->setError(JText::_('MEMBERSHIP_EXPIRED'));
				return false;	
			}
		}
		
		return true;
		
	}
	
	function canAdd($groups)
	{
		
		$expired = $this->isExpired();
		
		if($expired === true)	{
			$this->setError(JText::_('MEMBERSHIP_EXPIRED'));
			return false;
		}
		elseif($expired < 1)	{
			$this->setError(JText::_('NOT_ENOUGH_CREDIT'));
			return false;			
		}
		
		return true;
		
	}
	
	function canFeature($groups)
	{
		
		$data = self::getUser();
				
		if($data['packid'])	{
			
			$pack = self::getPack();
			
			if(!$pack->params->get('featured'))
				return false;
			
		}
		else
			return false;
		
		$expired = $this->isExpired();
		$config = $this->getConfig();
		
		if($expired === true)	{
			if($config['entryedit'])	{
				$this->setNotice(JText::_('MEMBERSHIP_EXPIRED'));
			}
			else	{
				$this->setError(JText::_('MEMBERSHIP_EXPIRED'));
				return false;	
			}
		}
		
		return true;
		
	}
	
	function getCategoryFilter()
	{
		
		$data = self::getUser();
		
		$filter = null;
		
		if($data['packid'])	{
			
			$pack = self::getPack();
			$cats = $pack->params->get('cats');
			if(count($cats))
				$filter = 'i.id in ('.implode(',', $cats).')';
			
		}
		
		return $filter;
		
	}
	
	function onAfterStore($row)
	{
		
		$id = JRequest::getInt('id', 0);
		
		if(!$id)	{
			
			$data = self::getUser();
			
			if(!empty($data['userid']) and $data['credit'] > 0)	{
				
				$query = 'update #__joomd_pusers set remaining = remaining-1 where userid = '.(int)$data['userid'];
				$this->_db->setQuery( $query );
				if(!$this->_db->query())	{
					throw new Exception($this->_db->getErrorMsg());
					return false;
				}
				
			}	
			
		}
		
		return true;
		
	}
	
}