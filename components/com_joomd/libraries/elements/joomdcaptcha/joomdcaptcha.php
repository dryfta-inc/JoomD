<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

define('JD_MAX', 3);
define('JD_WAIT', 45);

class Joomdcaptcha 
{
	
	var $length;
	var $width;
	var $height;
	var $code;
	var $fonts = array();

	var $chars = "abcdefhijkmnprstuvwx2345678";
	
	// Function wrappers for TriggerEvent usage
	function onJDCaptchaDisplay()
	{
		$session =& JFactory::getSession();

		
		$txtcolor =  null;
		$bgcolor = null;
		
		if (!empty($txtcolor))
		{
			$txtcolor = explode(',', $txtcolor);
		}
		if (!empty($bgcolor))
		{
			$bgcolor = explode(',', $bgcolor);
		}
       		
		$session->set( 'joomdcapt_uid', md5($this->get()) );
		
		$this->draw($txtcolor, $bgcolor);
		exit();
	}

	function onCaptchaCheck($word, &$return)
	{
		// $return = false if wrong; true if correct; int if many attempts 
		$mainframe =& JFactory::getApplication();

		$session =& JFactory::getSession();
		
		$tries = $session->get('joomdcapt_att', 1);
		$lasttry = $session->get('joomdcapt_tim', 0);
		
		$now = time();

		if ($tries > JD_MAX)		// many attempts
		{
			if ( ($diff = JD_WAIT - ($now - $lasttry)) > 0 )		// if so fast
			{
				$return = (int)$diff;
				return false;
			}
			else				// continue if sleep enough
			{
				$tries = 0;
			}
		}

		$session->set('joomdcapt_att', ++$tries);
		$session->set('joomdcapt_tim', $now);
		
		if (empty($word))
		{
			$return = false;
		}
		else
		{
	  		$correct = $session->get('joomdcapt_uid');
	  		
	  		if (md5($word) == $correct)
			{
	  			$session->set('joomdcapt_att', 1);
	  			$return = true;
	  		}
			else
			{
				$return = false;
			}
		}

		return $return;
	}
    
	function check($word)
	{
		$mainframe =& JFactory::getApplication();
		$return = false;
		$mainframe->triggerEvent('onCaptchaCheck', array($word, &$return));
		
		// parse language
		if ($return === false)
		{
            $return = JText::_('TIN_WRONG');
        }
        else if (is_int($return))
        {
            $return = sprintf( JText::_('TIN_MAX_ATTEMPTS'), $return );
		}

		return $return;
	}
	
	function Joomdcaptcha()
	{
		$this->length = 4;
		$this->size = 20;

		$this->width = ($this->length + 1) * $this->size;
		$this->height = $this->size * 2;

		$this->_getfonts();
		$this->_generate();
	}
	
	function _generate()
	{
		$this->code = '';
		for($i = 0, $len = strlen($this->chars) - 1; $i < $this->length; $i++)
		{
			$this->code .= $this->chars{ mt_rand(0, $len) };
		}
	}

	function _getfonts()
	{
		$dir = dirname(__FILE__).DS.'fonts';
		$handle = opendir($dir);
		
		while ( ($name = readdir($handle)) !== false )
		{
			if (preg_match('#.+\.ttf$#i', $name))
			{
				$this->fonts[] = $dir.DS.'monofont.ttf';
			}
		}
		closedir($handle);
	}
	
	function _randfont()
	{
		return $this->fonts[ mt_rand(0, count($this->fonts) - 1) ];
	}
	
	function draw($color = null, $bg_color = null)
	{
		$image = ImageCreateTrueColor($this->width, $this->height) or die('Cannot Initialize new GD image stream');

		if (empty($color))
		{
			$color = array( mt_rand(0, 80), mt_rand(0, 80), mt_rand(0, 80) );
		}
		
		if (empty($bg_color))
		{
			$bg_color = array( mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255) );
		}
		
		$borc = imagecolorallocate($image,0,0,0);

		ImageFilledRectangle( $image, 0, 0, $this->width, $this->height, ImageColorAllocate($image, $bg_color[0], $bg_color[1], $bg_color[2]) );
		$txt_color = ImageColorAllocate( $image, $color[0], $color[1], $color[2] );
		$shadow_color = ImageColorAllocate( $image, $bg_color[0] - 50, $bg_color[1] - 50, $bg_color[2] - 50 );
 		
 		$size1 = (int)($this->size / 5);
		$size2 = (int)($this->size / 2);
		$size3 = (int)($this->size * 1.5);
		
		$pos_x = $size2;
		
		for ($i = 0; $i < $this->length; $i++)
		{
			$x = $pos_x + mt_rand(-$size1, $size1);
			$y = mt_rand($this->size, $size3);

			imagettftext($image, $this->size, mt_rand(-15, 15), $x + mt_rand(-$size2, $size2), $y + mt_rand(-$size2, $size2), $shadow_color, $this->_randfont(), $this->code{$i});
			imagettftext($image, $this->size, mt_rand(-15, 15), $x, $y, $txt_color, $this->_randfont(), $this->code{$i});
			
			$pos_x += $this->size;
		}

		$this->_display($image);
	}

	function _display($img)
	{
		if ( function_exists('imagejpeg') )
		{
			header('Content-Type: image/jpeg');
			header('Cache-control: no-cache, no-store');
			imagejpeg($img);
		}
		else if ( function_exists('imagegif') )
		{
			header('Content-Type: image/gif');
			header('Cache-control: no-cache, no-store');
			imagegif($img);
		}
		else if ( function_exists('imagepng') )
		{
			header('Content-Type: image/x-png');
			header('Cache-control: no-cache, no-store');
			imagepng($img);
		}

	}
	
	function get()
	{
		return $this->code;
	}
	
}
