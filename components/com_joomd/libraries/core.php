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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//Contains the function to initialize all the classes

class Joomd	{
	
	public static $app = null;
	
	//returns application instance since 2.3
	public static function getApp()
	{
		if (!self::$app)
		{
			require_once( JPATH_ROOT . '/components/com_joomd/libraries/app.php' );
			
			self::$app = new JoomdApp();
		}

		return self::$app;
		
	}
	
	//returns app array of objects
	public static function getApps()
	{
		
		$db =  JFactory::getDBO();
		
		$query = 'select * from #__joomd_apps where published = 1 order by ordering asc';
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		return $items;
	
	}
	
	//returns theme name
	function get($item)
	{
				
		$db =  JFactory::getDBO();
		
		$query = 'select name from #__joomd_apps where type = 1 and published = 1 order by prio asc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		foreach($apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					$method = 'get'.ucfirst($item);
					
					if(method_exists($class, $method))	{
						
						$theme = $class->$method();
						return $theme;
						
					}
				
				}
				
			}
		
		}
		
		return null;
	
	}
	
	//returns toolbar object
	public static function getToolbar()
	{
	
		// check whether it is already loaded
		static $loader;
		
		if($loader)
			return;
			
		$loader = true;
		
		require_once(JPATH_ROOT.DS.'components/com_joomd/libraries/html/toolbar.php');
		
		$toolbar = new JoomdToolbar();
		
		return $toolbar;
	
	}
	
	//return config object based on all the apps
	public static function getConfig($apps = null)
	{
		
		$db =  JFactory::getDBO();
		
		$query = 'select i.*, t.name as theme from #__joomd_config as i left join #__joomd_templates as t on i.template=t.id';
		$db->setQuery( $query );
		$config = $db->loadAssoc();
		
		$registry = new JRegistry;
		$registry->loadString($config['social']);
		$config['social'] = $registry;
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'getConfig'))	{
						
						$cconfig = $class->getConfig();
						if(isset($cconfig->copyright)) unset($cconfig->copyright);
						$config = array_merge($config, $cconfig);
						
					}
				
				}
				
			}
		
		}
		
		$config = (object)$config;
		
		return $config;
	
	}
	
	//returns user object with all the available elements in all the apps if exists some
	public static function getUser($apps = null)
	{
		
		$user =  JFactory::getUser();
		
		$db =  JFactory::getDBO();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'getUser'))	{
						
						$puser = $class->getUser();
						
						unset($puser['id']);
						unset($puser['username']);
						unset($puser['email']);
						unset($puser['password']);
						unset($puser['gid']);
						unset($puser['usertype']);
						
						foreach((array)$puser as $k=>$v)
							$user->$k = $v;
						
					}
				
				}
				
			}
		
		}
		
		return $user;
		
	}
	
	//checks the type access and return the type object
	public static function getType($typeid = null)
	{
		
		$app = JFactory::getApplication();
		$jdapp = Joomd::getApp();
		$user =  JFactory::getUser();
		$db =  JFactory::getDBO();
		
		
		$typeid = empty($typeid)?JRequest::getInt('typeid', 0):(int)$typeid;
		
		if(!$typeid)
			$jdapp->redirect('index.php', JText::_('TYPENOTFOUND'));
		
		if($app->isSite())
			$query = 'select i.*, a.name as app from #__joomd_types as i join #__joomd_apps as a on (i.appid=a.id and a.published=1) where i.published = 1 and i.id = '.$typeid;
		else
			$query = 'select i.*, a.name as app from #__joomd_types as i join #__joomd_apps as a on i.appid=a.id where i.id = '.$typeid;
			
		$db->setQuery( $query );
		$type = $db->loadObject();
		
		if(empty($type))
			$jdapp->redirect('index.php', JText::_('TYPENOTFOUND'));
		
		if(!Joomd::CanAccessItem($type))	{
			if($user->get('guest'))	{
				$jdapp->redirect(JRoute::_('index.php?option=com_users&view=login'), JText::_('PLSLOGTOACC'));
				return;
			}
			else	{
				$jdapp->redirect('index.php', $jdapp->getError());
				return;
			}
			
		}
		
		JRequest::setVar('typeid', $type->id);
		
		$config = Joomd::getConfig($type->app);
				
		if(empty($type->config))	{
			$type->config	= $config->config;
			$type->config->set('template', $config->template);
		}
		else	{
			$registry = new JRegistry;
			$registry->loadString($type->config);
			$type->config = $registry;
		}
		
		if(empty($type->acl))
			$type->acl	= $config->acl;
		else	{
			$registry = new JRegistry;
			$registry->loadString($type->acl);
			$type->acl = $registry;
		}
		
		if(empty($type->listconfig))
			$type->listconfig	= $config->listconfig;
		else	{
			$registry = new JRegistry;
			$registry->loadString($type->listconfig);
			$type->listconfig = $registry;
		}
		
		if(empty($type->detailconfig))
			$type->detailconfig	= $config->detailconfig;
		else	{
			$registry = new JRegistry;
			$registry->loadString($type->detailconfig);
			$type->detailconfig = $registry;
		}
		
		return $type;
		
	}
	
	//returns the template path from all the available path
	public static function getTemplatePath($view, $file)
	{
		
		$mainframe =  JFactory::getApplication();
		
		$theme = Joomd::get('Theme');
		
		$template = $mainframe->getTemplate();
		
		$paths = array();
		
		$paths[] = JPATH_SITE.'/components/com_joomd/templates'.DS.$theme.DS.$view;
		$paths[] = JPATH_THEMES.DS.$template.DS.'html'.DS.'com_joomd'.DS.$view;
		$paths[] = JPATH_SITE.'/components/com_joomd/views/'.$view.'/tmpl';
		
		jimport('joomla.filesystem.path');
		
		$template = JPath::find($paths, $file);
		
		return $template;
		
	}
	
	//system notifications sent from all joomd
	public static function notify($subject, $body, $params=array())	{
		
		$mainframe = JFactory::getApplication();
		$db		=  JFactory::getDBO();
		$mail = JFactory::getMailer();
		
		$params['mode'] = isset($params['mode'])?$params['mode']:true;
		$params['from'] = isset($params['from'])?$params['from']:null;
		$params['to'] = isset($params['to'])?$params['to']:null;
		
		
		//assign sender based on the parameters
		if(empty($params['from']))	{
			
			$mailfrom 	= $mainframe->getCfg( 'mailfrom' );
			$fromname 	= $mainframe->getCfg( 'fromname' );
		
		}
		else	{
			
			if(is_array($params['from']))	{
				$mailfrom 	= $params['from'][0];
				$fromname 	= $params['from'][1];
			}
			else	{
				$mailfrom 	= $params['from'][0];
				$fromname 	= JText::_('ANONYMOUS');
			}
			
		}
				
		//get all super users with system email enabled
		$query = 'SELECT name, email' .
				' FROM #__users' .
				' WHERE block = 0 and sendEmail = 1';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// assign the same sender if not exists
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}
		
		if(empty($params['to']))	{
			$config = Joomd::getConfig();
		
			$recipient = empty($config->email)?array():explode(',', $config->email);
		}
		else	{
			$recipient = (array)$params['to'];
		}
		
		if(count($recipient))	{
			
			foreach ( $recipient as $email )	{
				ob_start();
				$result = $mail->sendMail($mailfrom, $fromname, $email, $subject, $body, $params['mode'], null, null, null, $mailfrom, $fromname);
				ob_end_clean();
				if(is_object($result))
					return $result;
				
			}
			
		}
		
		else	{
		
			// get superadministrators id
			foreach ( $rows as $row )
			{
				ob_start();
				$result = $mail->sendMail($mailfrom, $fromname, $row->email, $subject, $body, $params['mode']);
				ob_end_clean();
				if(is_object($result))
					return $result;
				
			}
		
		}
		
		return true;
		
	}
	
	//to create the thumbnail in the thumbs directory
	public static function create_scaled_image($file_path, $options) {
		
        $new_file_path = dirname($file_path).'/thumbs/'.basename($file_path);
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		JFolder::create(dirname($new_file_path));
			
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
       
	   $wr = $options['max_width'] / $img_width;
		$hr = $options['max_height'] / $img_height;
		
		if($wr and $hr)	{
			$scale = min(
				$wr,
				$hr
			);
		}
		else	{
			$scale = max(
				$wr,
				$hr
			);
		}
	   
        if ($scale > 1) {
            $scale = 1;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr(basename($file_path), '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                break;
            default:
                $src_img = $image_method = null;
        }
        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            0, 0, 0, 0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }
	
	//display footer e.g. version info
	public static function displayfooter()
	{
		
		echo '<div class="comfooter"><a href="http://www.joomla6teen.com/JoomD-for-Joomla.html" target="_blank">JoomD 2.3.0</a></div>';
		
	}
	
	public static function onBeforeDisplay()
	{
		
		$db =  JFactory::getDBO();
	
		$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by ordering asc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		ob_start();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'onBeforeDisplay'))	{
						
						$class->onBeforeDisplay();
						
					}
				
				}
				
			}
		
		}
		
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	public static function onAfterDisplay()
	{
		
		$db =  JFactory::getDBO();
	
		$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by ordering asc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		ob_start();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'onAfterDisplay'))	{
						
						$class->onAfterDisplay();
						
					}
				
				}
				
			}
		
		}
		
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	public static function onBeforeStore()
	{
		
		$db =  JFactory::getDBO();
	
		$query = 'select name from #__joomd_apps where published = 1 and type in(1,2) order by ordering asc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		ob_start();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'onBeforeStore'))	{
						
						$class->onBeforeStore();
						
					}
				
				}
				
			}
		
		}
		
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	public static function onAfterStore($row)
	{
		
		$db =  JFactory::getDBO();
	
		$query = 'select name from #__joomd_apps where published = 1 and type in (1, 2) order by ordering asc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		ob_start();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'onAfterStore'))	{
						
						$class->onAfterStore($row);
						
					}
				
				}
				
			}
		
		}
		
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	public static function onBeforeDelete($row)
	{
		
		$db =  JFactory::getDBO();
	
		$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by ordering asc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		ob_start();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'onBeforeDelete'))	{
						
						$class->onBeforeDelete($row);
						
					}
				
				}
				
			}
		
		}
		
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	public static function onAfterDelete($row)
	{
		
		$db =  JFactory::getDBO();
	
		$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by ordering asc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		ob_start();
		
		foreach((array)$apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'onAfterDelete'))	{
						
						$class->onAfterDelete($row);
						
					}
				
				}
				
			}
		
		}
		
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	//copyright text
	function copyright()
	{
		$class = array('j','d','_','c','o','p','y','r','i','g','h','t');
		echo '<div class="'.implode('', $class).'">'.JText::_('COPYRIGHT_TEXT').'</div>';
	
	}
	
	function isAuthorised($action)
	{
		
		if($action == 'manage')
			$actions = array('editaccess', 'editall', 'deleteaccess', 'deleteall', 'stateaccess', 'stateall');
		elseif($action == 'manageall')
			$actions = array('editall', 'deleteall', 'stateall');
		else
			$actions = $action;
		
		settype($actions, 'array');
		
		$jdapp = Joomd::getApp();
		$user = Joomd::getUser();
		$type = Joomd::getType();
		
		$user_groups = $user->getAuthorisedGroups();
		$auth_groups = array();

		foreach($actions as $action)	{
			$temparr = (array)$type->acl->get($action);
			if(count($temparr) and count($auth_groups))
				array_merge($auth_groups, $temparr);
			elseif(count($temparr))
				$auth_groups = $temparr;
		}

		foreach($user_groups as $group)	{
			
			if(in_array($group, $auth_groups))	{
				
				$db = JFactory::getDBO();
				$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by prio asc';
				$db->setQuery( $query );
				$apps = $db->loadResultArray();
				
				foreach($apps as $app)	{
				
					if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
					
						require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
							
						$class = "JoomdApp".ucfirst($app);
						
						if(class_exists($class))	{
						
							$class = new $class;
							
							if(method_exists($class, 'isAuthorised'))	{
								
								if($class->isAuthorised($auth_groups, $action) === false)	{
									$jdapp->setError($class->getError());
									return false;
								}
								
							}
						
						}
						
					}
					
				}
				
				return true;
			}
			
		}
		
		return false;
		
	}
	
	function canDo($row, $action)
	{
		
		$user = Joomd::getUser();
		$type = Joomd::getType();
		
		$user_groups = $user->getAuthorisedGroups();
		$auth_groups = (array)$type->acl->get($action.'all');
		
		$return = false;
		$method = 'can'.ucfirst($action);
		
		foreach($user_groups as $group)	{
			
			if(in_array($group, $auth_groups))	{
				$return = true;
				break;
			}
			
		}
		
		$auth_groups = (array)$type->acl->get($action.'access');

		foreach($user_groups as $group)	{
			
			if(in_array($group, $auth_groups) and $user->id==$row->created_by)	{
				$return = true;
				break;
			}
			
		}
		
		if($return === true)	{
			
			$db = JFactory::getDBO();
			$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by prio asc';
			$db->setQuery( $query );
			$apps = $db->loadResultArray();
			
			foreach($apps as $app)	{
			
				if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
				
					require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
						
					$class = "JoomdApp".ucfirst($app);
					
					if(class_exists($class))	{
					
						$class = new $class;
						
						if(method_exists($class, $method))	{
							
							if($class->$method($auth_groups) === false)
								return false;
							
						}
					
					}
					
				}
				
			}
			
			return true;
			
		}
		
		return false;
		
	}
	
	function canEdit($row)
	{
		
		return Joomd::canDo($row, 'edit');
		
	}
	
	function canDelete($row)
	{
		
		return Joomd::canDo($row, 'delete');
		
	}
	
	function canState($row)
	{
		
		return Joomd::canDo($row, 'state');
		
	}
	
	function canFeature($row)
	{
		
		return Joomd::canDo($row, 'feature');
		
	}
	
	public static function CanAccessItem($item)
	{
		
		$user =  JFactory::getUser();
			
		if(!in_array($item->access, $user->getAuthorisedViewLevels()))
			return false;
					
		return true;
		
	}
	
	function getCategoryFilter()
	{
		
		$db = JFactory::getDBO();
		$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by prio desc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		$where = array();
		
		foreach($apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
			
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'getCategoryFilter'))	{
						
						$filter = $class->getCategoryFilter();
						
						if(!empty($filter))
							array_push($where, $filter);
						
					}
				
				}
				
			}
			
		}
		
		return $where;
		
	}
	
	function getItemFilter()
	{
		
		$db = JFactory::getDBO();
		$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by prio desc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		$where = array();
		
		foreach($apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
			
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'getItemFilter'))	{
						
						$filter = $class->getItemFilter();
						
						if(!empty($filter))
							array_push($where, $filter);
						
					}
				
				}
				
			}
			
		}
		
		return $where;
		
	}
	
	function getFieldFilter()
	{
		
		$db = JFactory::getDBO();
		$query = 'select name from #__joomd_apps where published = 1 and type in (1,2) order by prio desc';
		$db->setQuery( $query );
		$apps = $db->loadResultArray();
		
		$where = array();
		
		foreach($apps as $app)	{
		
			if(!empty($app) and is_file(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php'))	{
			
				require_once(JPATH_SITE.'/components/com_joomd/libraries/apps/'.$app.'/app_'.$app.'.php');
					
				$class = "JoomdApp".ucfirst($app);
				
				if(class_exists($class))	{
				
					$class = new $class;
					
					if(method_exists($class, 'getFieldFilter'))	{
						
						$filter = $class->getFieldFilter();
						
						if(!empty($filter))
							array_push($where, $filter);
						
					}
				
				}
				
			}
			
		}
		
		return $where;
		
	}
	
}