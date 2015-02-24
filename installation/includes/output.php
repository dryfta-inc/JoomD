<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer output class
 */

defined('_ABI') or die('Direct access is not allowed');

class ABIOutput
{
	/** @var string The active step (index, db, setup, finish) */
	var $_activeStep = 'index';
	/** @var string Javascript the Next button calls */
	var $_nextButton = 'submitForm(\'db\')';
	/** @var string Javascript the Previous button calls */
	var $_prevButton = '';
	/** @var string The main content of the page */
	var $_content = '';
	/** @var string Any error message to output to the browser */
	var $_error_message;
	/** @var string The rendering mode to use. Use 'html' to output the full interface or 'raw' to echo only the content set, if any */
	var $mode = 'html';
	/** @var string A URL to redirect to instead of displaying the page output */
	var $_redirection = '';
	/** @var string The Javascript to run automatically on page load */
	var $_automationJS = '';

	/**
	 * Constructor
	 * @return ABIOutput
	 */
	function ABIOutput()
	{
		// No initialization required
	}

	/**
	 * Singleton implementation
	 * @return ABIOutput
	 */
	static function &getInstance()
	{
		static $instance;

		if(!is_object($instance))
		{
			$instance = new ABIOutput();
		}

		return $instance;
	}

	/**
	 * Sets the active step
	 * @param $step string The active step
	 */
	function setActiveStep($step)
	{
		switch($step)
		{
			case "index":
			case "db":
			case "setup":
			case "finish":
				$this->_activeStep = $step;
				break;

			default:
				die('Invalid step');
				break;
		}
	}

	/**
	 * Sets the Javascript for the Next and Previous buttons.
	 * @param $prev mixed Use null to hide the Previous button or the Javascript you want to be executed when the button is clicked.
	 * @param $next mixed Use null to hide the Next button or the Javascript you want to be executed when the button is clicked.
	 */
	function setButtons($prev, $next)
	{
		$this->_nextButton = $next;
		$this->_prevButton = $prev;
	}

	/**
	 * Sets the main body content of the output
	 * @param $content string The main body content
	 */
	function setContent($content)
	{
		$this->_content = $content;
	}

	/**
	 * Sets the automation Javascript, to be executed upon page load
	 * @param string $script The Javascript to run when the document object is ready
	 */
	function setAutomation($script)
	{
		$this->_automationJS = $script;
	}

	/**
	 * Sets the output mode. Use 'html' to display the full interface, or 'raw' to
	 * output the content alone, e.g. when using AJAX calls.
	 * @param $mode string Output mode
	 */
	function setMode($mode)
	{
		switch($mode)
		{
			case 'html':
			case 'raw':
				$this->mode = $mode;
				break;

			default:
				die('Invalid output mode "'.$mode.'"');
		}
	}

	/**
	 * Sets an error message to be displayed with the output
	 * @param $message string The error message to show
	 * @param $overwrite bool Should this error message overwrite any previous error messages? Defaults to true.
	 */
	function setError($message, $overwrite = true)
	{
		if($overwrite)
		{
			$this->_error_message = $message;
		}
		else
		{
			$this->_error_message .= $message;
		}
	}

	function output()
	{
		if(!empty($this->_redirection))
		{
			header('Location: '.$this->_redirection);
			return;
		}

		if($this->mode != 'html')
		{
			if(!empty($this->_content)) echo $this->_content;
		}
		else
		{
			?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Akeeba Backup Installer <?php echo AKEEBA_VERSION ?></title>
	<link rel="shortcut icon" href="../images/favicon.ico" />
	<link rel="stylesheet" href="css/install.css" type="text/css" />
	<link rel="stylesheet" href="css/redmond/jquery-ui-redmond.css" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/install.js"></script>
	<script type="text/javascript" src="js/jquery.blockUI.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			<?php echo $this->_automationJS; ?>

		});
	</script>
</head>

<body>
<form action="index.php" method="post"
	enctype="application/x-www-form-urlencoded" id="installForm"><input
	type="hidden" name="task" id="task" value="" />
<div id="header"><img id="logo" src="css/img/logo.png" border="0"
	alt="JPI Logo" />
<h1>Akeeba Backup Installer <?php echo AKEEBA_VERSION ?></h1>
<div id="buttonbar">
	<?php if(!empty($this->_nextButton)): ?>
	<span class="next"><a href="javascript:<?php echo $this->_nextButton ?>" id="nextButton"><?php echo ABIText::_('NEXT') ?></a></span>
	<?php endif; ?>
	<?php if(!empty($this->_prevButton)): ?>
	<span class="prev"><a href="javascript:<?php echo $this->_prevButton ?>" id="prevButton"><?php echo ABIText::_('PREV') ?></a></span>
	<?php endif; ?>
</div>
</div>
<div id="stepbar">
<div class="center"><span
	<?php echo $this->_activeStep == 'index' ? 'class="active"' : '' ?>><?php echo ABIText::_('STEP_INDEX') ?></span>
<span <?php echo $this->_activeStep == 'db' ? 'class="active"' : '' ?>><?php echo ABIText::_('STEP_DB') ?></span>
<span
	<?php echo $this->_activeStep == 'setup' ? 'class="active"' : '' ?>><?php echo ABIText::_('STEP_INFO') ?></span>
<span
	<?php echo $this->_activeStep == 'finish' ? 'class="active"' : '' ?>><?php echo ABIText::_('STEP_FINISH') ?></span>
</div>
</div>
<?php if(!empty($this->_error_message)):?>
<div id="errormessage">
	<?php echo $this->_error_message?>
</div>
<?php endif; ?>
<div id="main"><?php echo $this->_content ?></div>
<div id="footer">
<p><?php echo ABIText::_('FOOTER') ?></p>
</div>

</form>
</body>
</html>
	<?php
		}
	}

}