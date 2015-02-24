/**
 * This code runs at page load
 */
$(document).ready(function() {
	//addAccordion();
});

var abi_current_tmp = '';
var abi_current_log = '';
var abi_default_tmp = '';
var abi_default_log = '';

/**
 * Adds the accordion effect
 */
function addAccordion()
{
	$("#accordion").accordion({
		header: 'h3'
	});	
}

/**
 * Controls whether the Restoration Progress Dialog can be closed.
 * It's only set to true when the restoration finishes normally or
 * with an error.
 */
var canCloseDialog = false;

/**
 * When it's true, the database dialogs auto-close
 */
var hasAutomation = false;

function parseResponse(msg)
{
	var response = new Object;
	response.status = true;
	
	var junk = null;
	var message = "";
	
	// Get rid of junk before the data
	var valid_pos = msg.indexOf('###<?xml');
	if( valid_pos == -1 ) {
		response.status = false;
		response.error = 'Invalid AJAX data: ' + msg;
	} else if( valid_pos != 0 ) {
		// Data is prefixed with junk
		response.junk = msg.substr(0, valid_pos);
		message = msg.substr(valid_pos);
	}
	else
	{
		message = msg;
	}
	message = message.substr(3); // Remove triple hash in the beginning
	
	// Get of rid of junk after the data
	var valid_pos = message.lastIndexOf('###');
	message = message.substr(0, valid_pos); // Remove triple hash in the end
	
	// Is there more junk added after the </restoredata> and befere the triple hash?
	// This is used to prune forced analytics
	var valid_pos = message.lastIndexOf('</restoredata>');
	var last_pos = valid_pos + 14;
	if( last_pos < message.length )
	{
		message = message.substr(0, last_pos);
	}
	
	response.message = message;
	
	if(response.status && ($.browser.msie) )
	{
		xml = new ActiveXObject("Microsoft.XMLDOM");
		xml.async = false;
		xml.loadXML(message);
		response.message = xml;
	}
	
	return response;
}

/**
 * Hijacks the click event of the Next button for use in the DB
 * restoration page
 */
function hijackDBNext()
{
	// Hijack Next button's click
	$('#nextButton').click(dbnext_click);
	// Setup progress dialog display
	$("#dialog").dialog({
		autoOpen: false,
		closeOnEscape: false,
		height: 200,
		width: 600,
		hide: 'slide',
		modal: true,
		position: 'center',
		show: 'slide',
		beforeclose: function(event, ui) {
			return canCloseDialog;
		}
	});
	// Create a progress bar
	$("#progressbar").progressbar({
		value: 0
	});
	$('#dberrorhelp').css('display','none');
	// Handle warning dialog
	$("#warndialog").dialog({
		autoOpen: false,
		closeOnEscape: false,
		height: 300,
		width: 600,
		hide: 'slide',
		modal: true,
		position: 'center',
		show: 'slide'
	});
}

/**
 * Callback function for the click event of the Next button in the
 * DB restoration page
 * @param event
 * @return
 */
function dbnext_click(event)
{
	event.preventDefault(); // Prevent default click handling
	// Open a dialog box to render the progress
	$('#progresstext').removeClass("error");
	$('#progresstext').html('');
	$('#progressbar').show();
	$('#dberrorhelp').css('display','none');
	$('#dialog').dialog('option', 'buttons', '');
	$('#dialog').dialog('open');
	// Run the restoration using jQuery-powered AJAX
	stepThroughRestoration();
	// Next click will not call this callback again
	$('#nextButton').unbind('click', dbnext_click);
}

/**
 * Callback function for the click event of the Delete link in the
 * final page
 * @param event
 * @return
 */
function deleteself_click(event)
{
	event.preventDefault(); // Prevent default click handling
	// Open a dialog box to render the progress
	$('#errortext').hide();
	$('#oktext').hide();
	$('#waittext').show();
	$('#dialog2').dialog('option', 'buttons', '');
	$('#dialog2').dialog('open');
	$.ajax({
		method: 'get',
		url: 'index.php',
		cache: false,
		data: {
			task: 'ajax',
			act: 'deleteself'
		},
		dataType: 'text',
		timeout: 120000,
		success: function(datext, textStatus)
		{
			var response = parseResponse(datext);
			if(!response.status)
			{
				// An AJAX error occured. Show the error message
				$('#errortext').show();
				$('#oktext').hide();
				$('#waittext').hide();
				$('#errortext').html('');
				$('#errortext').append('<p><strong>AJAX error</strong>:<br/>');
				$('#errortext').append('<tt>'+response.error+'</tt></p>');
				$('#errortext').addClass("error");
				canCloseDialog = true;
				$('#dialog2').dialog('option', 'buttons', { "OK": function() { $(this).dialog("close"); } });
				$('#dialog2').dialog('option', 'closeOnEscape', true);
			}
			else
			{
				var daxml = response.message;
			}
			
			var error = $('error', daxml).text();
			if(error == '')
			{
				// Show "done" message
				$('#errortext').hide();
				$('#oktext').show();
				$('#waittext').hide();
				// Show an OK button which shuts down the window
				$('#dialog2').dialog('option', 'buttons', {
				"OK": function() {
						$(this).dialog("close");
						window.location = '../index.php';
					}
				});
			}
			else
			{
				// An error has occured. Show the message.
				$('#errortext').show();
				$('#oktext').hide();
				$('#waittext').hide();
				$('#errortext').html(error);
				$('#errortext').show();
				canCloseDialog = true;
				$('#dialog2').dialog('option', 'buttons', { "OK": function() { $(this).dialog("close"); } });
				$('#dialog2').dialog('option', 'closeOnEscape', true);
			}
		},
		error: function(XHR, textStatus, errorThrown){
			// An AJAX error occured. Show the error message
			$('#errortext').show();
			$('#oktext').hide();
			$('#waittext').hide();
			$('#errortext').html('');
			$('#errortext').append('<p><strong>AJAX error</strong>:<br/>');
			$('#errortext').append('<tt>'+textStatus+'</tt>('+errorThrown+')</p>');
			$('#errortext').append('<p><strong>Raw Data</strong>:<pre>'+htmlentities(XHR.responseText)+'</pre></p>');
			$('#errortext').addClass("error");
			canCloseDialog = true;
			$('#dialog2').dialog('option', 'buttons', { "OK": function() { $(this).dialog("close"); } });
			$('#dialog2').dialog('option', 'closeOnEscape', true);
		}

	});
	return false;
}

/**
 * Automatically progresses through the restoration steps, using jQuery-powered
 * AJAX calls.
 */
function stepThroughRestoration()
{
	$.ajax({
		method: 'get',
		url: 'index.php',
		cache: false,
		data: {
			task: 'restore',
			dbtype: $('#dbtype').val(),
			dbhost: $('#dbhost').val(),
			dbuser: $('#dbuser').val(),
			dbpass: $('#dbpass').val(),
			dbname: $('#dbname').val(),
			prefix: $('#prefix').val(),
			existing: $("input[@name='existing']:checked").val(),
			replacesql: $("#replacesql").is(':checked') ? 1 : 0,
			forceutf8: $("#forceutf8").is(':checked') ? 1 : 0,
			checkbox: $('#suppressfk').val(),
			maxtime: $('#maxtime').val()
			/* -- OBSOLETE --
			maxchunk: $('#maxchunk').val(),
			maxqueries: $('#maxqueries').val()
			*/
		},
		dataType: 'text',
		timeout: 120000,
		success: function(datext, textStatus){
			var response = parseResponse(datext);
			if(!response.status)
			{
				// An error has occured. Hide the progress bar and show the message.
				$('#progressbar').hide('fast');
				$('#progresstext').html(response.error);
				$('#progresstext').addClass("error");
				$('#dberrorhelp').show('fast');
				canCloseDialog = true;
				$('#dialog').dialog('option', 'buttons', { "OK": function() { $(this).dialog("close"); } });
				$('#dialog').dialog('option', 'closeOnEscape', true);
				// Hijack Next button's click
				$('#nextButton').click(dbnext_click);
			}
			else
			{
				var daxml = response.message;
			}

			// Check if we have an error
			var error = '';
			try {
				var error = $('error', daxml).text();
			} catch(e) {}
			if(error == '')
			{
				// Update display when no error are present
				var percent = $('percent', daxml).text();
				var message = $('message', daxml).text();
				$('#dberrorhelp').css('display','none');
				$('#progresstext').html(message);
				$('#progressbar').progressbar('option', 'value', percent);
				if( $('done', daxml).text() == 0 )
				{
					// If we are not done, requery server
					stepThroughRestoration();
				}
				else
				{
					// When we are done, allow user to close the dialog box. Closing the
					// dialog box, auto-clicks the Next button. Nice, huh?
					canCloseDialog = true;
					$('#dialog').dialog('option', 'buttons', { "OK": function() { $(this).dialog("close"); } });
					$('#dialog').bind('dialogclose', function(event, ui) {
						// Get the HREF of the nextButton, remove the 'javascript:' part and execute the remaining stuff
						var href = $('#nextButton').attr('href');
						eval(href.replace('/javascript:/i', ''));
					});
					$('#dialog').dialog('option', 'closeOnEscape', true);
					if(hasAutomation)
					{
						// Auto-close the dialog in automation cases
						$('#dialog').dialog('close');
					}
				}
			}
			else
			{
				// An error has occured. Hide the progress bar and show the message.
				$('#progressbar').hide('fast');
				$('#progresstext').html(error);
				$('#progresstext').addClass("error");
				$('#dberrorhelp').show('fast');
				canCloseDialog = true;
				$('#dialog').dialog('option', 'buttons', { "OK": function() { $(this).dialog("close"); } });
				$('#dialog').dialog('option', 'closeOnEscape', true);
				// Hijack Next button's click
				$('#nextButton').click(dbnext_click);
			}
		},
		error: function(XHR, textStatus, errorThrown){
			// An AJAX error occured. Show the error message
			$('#progressbar').hide('fast');
			$('#progresstext').html('');
			$('#progresstext').append('<p><strong>AJAX error</strong>:<br/>');
			$('#progresstext').append('<tt>'+textStatus+'</tt>('+errorThrown+')</p>');
			$('#progresstext').append('<p><strong>Raw Data</strong>:<pre>'+htmlentities(XHR.responseText)+'</pre></p>');
			$('#progresstext').addClass("error");
			$('#dberrorhelp').show('fast');
			canCloseDialog = true;
			$('#dialog').dialog('option', 'buttons', { "OK": function() { $(this).dialog("close"); } });
			$('#dialog').dialog('option', 'closeOnEscape', true);
			// Hijack Next button's click
			$('#nextButton').click(dbnext_click);
		}
	});
}

/**
 * Submits the installForm to the server, using the specified task
 * @param action string The task to pass to the form
 */
function submitForm(action)
{
	frm = document.getElementById("installForm");
	frm.task.value = action;
	frm.submit();
}

/**
 * Hijacks the click event of the Next button for use in the DB
 * restoration page
 */
function hijackSetupNext()
{
	// Hijack Next button's click
	$('#nextButton').click(setupnext_click);
	// Setup error dialog
	$("#dialog").dialog({
		autoOpen: false,
		closeOnEscape: true,
		show: 'slide',
		hide: 'slide',
		width: 400,
		height: 130,
		modal: true,
		position: 'center'
	});
	// Setup success dialog
	$("#okdialog").dialog({
		autoOpen: false,
		closeOnEscape: true,
		show: 'slide',
		hide: 'slide',
		width: 400,
		height: 130,
		modal: true,
		position: 'center'
	});
}

function btnFindFTPRootClick()
{
	//$.blockUI({ message: '<h1><img src="css/img/busy.gif" /> Loading...</h1>' });
	$.ajax({
		method: 'get',
		url: 'index.php',
		cache: false,
		data: {
			task: 'ajax',
			act: 'findFtpRoot',
			ftp_host: $('#ftp_host').val(),
			ftp_port: $('#ftp_port').val(),
			ftp_user: $('#ftp_user').val(),
			ftp_pass: $('#ftp_pass').val()
		},
		dataType: 'text',
		timeout: 120000,
		success: function(datext, textStatus){
			var response = parseResponse(datext);
			if(!response.status)
			{
				// An error has occured. Hide the progress bar and show the message.
				$('#progressbar').hide('fast');
				$('#progresstext').html(response.error);
				$('#progresstext').addClass("error");
				$('#dialog').dialog('open');
				// Clear checkbox and FTP root textbox
				$('#ftp_enable').removeAttr('checked');
				$('#ftp_root').val('');
			}
			else
			{
				var daxml = response.message;
			}
			
			//$.unblockUI();
			// Check if we have an error
			var error = $('error', daxml).text();
			if(error == '')
			{
				$('#ftp_root').val($('root', daxml).text());
				$('#ftp_enable').attr('checked','checked');
			}
			else
			{
				// An error has occured. Show the message.
				$('#progresstext').html('<p>'+error+'</p>');
				$('#progresstext').addClass("error");
				$('#dialog').dialog('open');
				// Clear checkbox and FTP root textbox
				$('#ftp_enable').removeAttr('checked');
				$('#ftp_root').val('');
			}
		},
		error: function(XHR, textStatus, errorThrown){
			//$.unblockUI();
			// An AJAX error occured. Show the error message.
			$('#progressbar').hide('fast');
			$('#progresstext').html('');
			$('#progresstext').append('<p><strong>AJAX error</strong>:<br/>');
			$('#progresstext').append('<tt>'+textStatus+'</tt>('+errorThrown+')</p>');
			$('#progresstext').append('<p><strong>Raw Data</strong>:<pre>'+htmlentities(XHR.responseText)+'</pre></p>');
			$('#progresstext').addClass("error");
			$('#dialog').dialog('open');
			// Clear checkbox and FTP root textbox
			$('#ftp_enable').removeAttr('checked');
			$('#ftp_root').val('');
		}
	});
}

function btnFTPCheckClick()
{
	//$.blockUI({ message: '<h1><img src="css/img/busy.gif" /> Loading...</h1>' });
	$.ajax({
		method: 'get',
		url: 'index.php',
		cache: false,
		data: {
			task: 'ajax',
			act: 'checkFtp',
			ftp_host: $('#ftp_host').val(),
			ftp_port: $('#ftp_port').val(),
			ftp_user: $('#ftp_user').val(),
			ftp_pass: $('#ftp_pass').val(),
			ftp_root: $('#ftp_root').val()
		},
		dataType: 'text',
		timeout: 120000,
		success: function(datext, textStatus){
			var response = parseResponse(datext);
			if(!response.status)
			{
				// An error has occured. Hide the progress bar and show the message.
				$('#progressbar').hide('fast');
				$('#progresstext').html(response.error);
				$('#progresstext').addClass("error");
				$('#dialog').dialog('open');
				// Clear checkbox and FTP root textbox
				$('#ftp_enable').removeAttr('checked');
				$('#ftp_root').val('');
			}
			else
			{
				var daxml = response.message;
			}

			//$.unblockUI();
			// Check if we have an error
			var error = $('error', daxml).text();
			if(error == '')
			{
				// Everything's fine. Notify user
				$('#ftp_enable').attr('checked','checked');
				$('#okdialog').dialog('open');
			}
			else
			{
				// An error has occured. Show the message.
				$('#progresstext').html('<p>'+error+'</p>');
				$('#progresstext').addClass("error");
				$('#dialog').dialog('open');
				// Clear checkbox and FTP root textbox
				$('#ftp_enable').removeAttr('checked');
			}
		},
		error: function(XHR, textStatus, errorThrown){
			//$.unblockUI();
			// An AJAX error occured. Show the error message.
			$('#progressbar').hide('fast');
			$('#progresstext').html('');
			$('#progresstext').append('<p><strong>AJAX error</strong>:<br/>');
			$('#progresstext').append('<tt>'+textStatus+'</tt>('+errorThrown+')</p>');
			$('#progresstext').append('<p><strong>Raw Data</strong>:<pre>'+htmlentities(XHR.responseText)+'</pre></p>');
			$('#progresstext').addClass("error");
			$('#dialog').dialog('open');
			// Clear checkbox and FTP root textbox
			$('#ftp_enable').removeAttr('checked');
		}
	});
}

function setupnext_click(event)
{
	var allChecks = true;
	var errors = Array();
	i = 0; // Counter used to add items to errors array

	// Site name, email, sender must be non-blank
	var emailaddress = $('#mailfrom').val();
	if( $('#sitename').val().replace(/^\s*/, "").replace(/\s*$/, "") == '' )
	{
		allChecks = false;
		errors[i++] = errorstrings['sitename'];
	}
	if(!echeck(emailaddress))
	{
		allChecks = false;
		errors[i++] = errorstrings['siteemail'];
	}
	if( $('#fromname').val().replace(/^\s*/, "").replace(/\s*$/, "") == '' )
	{
		allChecks = false;
		errors[i++] = errorstrings['fromname'];
	}
	
	// Super admin passwords must match (exactly) and email must be non-blank
	var sapass1 = $('#sapass1').val();
	var sapass2 = $('#sapass2').val();
	if(sapass1 !== sapass2)
	{
		allChecks = false;
		errors[i++] = errorstrings['sapass'];
	}
	emailaddress = $('#saemail').val();
	if(!echeck(emailaddress))
	{
		allChecks = false;
		errors[i++] = errorstrings['saemail'];
	}

	if(!allChecks) {
		event.preventDefault();
		// Show error dialog
		var message = '';
		for(msg in errors)
		{
			message += '<li>'+errors[msg]+'</li>'+"\n";
		}		
		$('#progresstext').html('<ul>'+message+'</ul>');
		$('#progresstext').addClass("error");
		$('#dialog').dialog('open');
	}
}

/**
 * Event handler for the checkbox which automatically assigns the temp and log paths
 * @return
 */
function onOverridePaths() {
	var checked = !($('#overridepaths:checked').val() == null);
	
	if(checked)
	{
		$('#tmp_path').val(abi_default_tmp);
		$('#log_path').val(abi_default_log);
	}
	else
	{
		$('#tmp_path').val(abi_current_tmp);
		$('#log_path').val(abi_current_log);
	}
}

/**
 * Checks the Live Site textbox for validity and automatically adjusts it
 * @return
 */
function checkLiveSite()
{
	var livesite = $('#live_site').val();
	if(livesite == '') return;
	if( (livesite.substr(0,7) != 'http://') && (livesite.substr(0,8) != 'https://') ) {
		livesite = 'http://' + livesite;
	}
	if( livesite.substr(-1) == '/' ) {
		livesite = livesite.substr(0, livesite.length - 1);
	}
	$('#live_site').val(livesite);
}

/* ======================================================================= *
 * UTILITY FUNCTIONS                                                       *
 * ======================================================================= */
/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
function echeck(str) {
	var at="@";
	var dot=".";
	var lat=str.indexOf(at);
	var lstr=str.length;
	var ldot=str.indexOf(dot);
	
	if (str.indexOf(at)==-1){
		return false;
	}
	
	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		return false;
	}
	
	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		return false;
	}
	
	if (str.indexOf(at,(lat+1))!=-1){
		 return false;
	}
	
	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		return false;
	}
	
	if (str.indexOf(dot,(lat+2))==-1){
		 return false;
	}
	
	if (str.indexOf(" ")!=-1){
		return false;
	}
	
	return true;					
}

function get_html_translation_table(table, quote_style) {
    // http://kevin.vanzonneveld.net
    var entities = {}, histogram = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};
    
    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';
 
    useTable     = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';
 
    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw Error("Table: "+useTable+' not supported');
        // return false;
    }
 
    // ascii decimals for better compatibility
    entities['38'] = '&amp;';
    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#039;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';
 
    if (useTable === 'HTML_ENTITIES') {
      entities['160'] = '&nbsp;';
      entities['161'] = '&iexcl;';
      entities['162'] = '&cent;';
      entities['163'] = '&pound;';
      entities['164'] = '&curren;';
      entities['165'] = '&yen;';
      entities['166'] = '&brvbar;';
      entities['167'] = '&sect;';
      entities['168'] = '&uml;';
      entities['169'] = '&copy;';
      entities['170'] = '&ordf;';
      entities['171'] = '&laquo;';
      entities['172'] = '&not;';
      entities['173'] = '&shy;';
      entities['174'] = '&reg;';
      entities['175'] = '&macr;';
      entities['176'] = '&deg;';
      entities['177'] = '&plusmn;';
      entities['178'] = '&sup2;';
      entities['179'] = '&sup3;';
      entities['180'] = '&acute;';
      entities['181'] = '&micro;';
      entities['182'] = '&para;';
      entities['183'] = '&middot;';
      entities['184'] = '&cedil;';
      entities['185'] = '&sup1;';
      entities['186'] = '&ordm;';
      entities['187'] = '&raquo;';
      entities['188'] = '&frac14;';
      entities['189'] = '&frac12;';
      entities['190'] = '&frac34;';
      entities['191'] = '&iquest;';
      entities['192'] = '&Agrave;';
      entities['193'] = '&Aacute;';
      entities['194'] = '&Acirc;';
      entities['195'] = '&Atilde;';
      entities['196'] = '&Auml;';
      entities['197'] = '&Aring;';
      entities['198'] = '&AElig;';
      entities['199'] = '&Ccedil;';
      entities['200'] = '&Egrave;';
      entities['201'] = '&Eacute;';
      entities['202'] = '&Ecirc;';
      entities['203'] = '&Euml;';
      entities['204'] = '&Igrave;';
      entities['205'] = '&Iacute;';
      entities['206'] = '&Icirc;';
      entities['207'] = '&Iuml;';
      entities['208'] = '&ETH;';
      entities['209'] = '&Ntilde;';
      entities['210'] = '&Ograve;';
      entities['211'] = '&Oacute;';
      entities['212'] = '&Ocirc;';
      entities['213'] = '&Otilde;';
      entities['214'] = '&Ouml;';
      entities['215'] = '&times;';
      entities['216'] = '&Oslash;';
      entities['217'] = '&Ugrave;';
      entities['218'] = '&Uacute;';
      entities['219'] = '&Ucirc;';
      entities['220'] = '&Uuml;';
      entities['221'] = '&Yacute;';
      entities['222'] = '&THORN;';
      entities['223'] = '&szlig;';
      entities['224'] = '&agrave;';
      entities['225'] = '&aacute;';
      entities['226'] = '&acirc;';
      entities['227'] = '&atilde;';
      entities['228'] = '&auml;';
      entities['229'] = '&aring;';
      entities['230'] = '&aelig;';
      entities['231'] = '&ccedil;';
      entities['232'] = '&egrave;';
      entities['233'] = '&eacute;';
      entities['234'] = '&ecirc;';
      entities['235'] = '&euml;';
      entities['236'] = '&igrave;';
      entities['237'] = '&iacute;';
      entities['238'] = '&icirc;';
      entities['239'] = '&iuml;';
      entities['240'] = '&eth;';
      entities['241'] = '&ntilde;';
      entities['242'] = '&ograve;';
      entities['243'] = '&oacute;';
      entities['244'] = '&ocirc;';
      entities['245'] = '&otilde;';
      entities['246'] = '&ouml;';
      entities['247'] = '&divide;';
      entities['248'] = '&oslash;';
      entities['249'] = '&ugrave;';
      entities['250'] = '&uacute;';
      entities['251'] = '&ucirc;';
      entities['252'] = '&uuml;';
      entities['253'] = '&yacute;';
      entities['254'] = '&thorn;';
      entities['255'] = '&yuml;';
    }
    
    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        histogram[symbol] = entities[decimal];
    }
    
    return histogram;
}

function htmlentities (string, quote_style) {
    // http://kevin.vanzonneveld.net
    var histogram = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();
    
    if (false === (histogram = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }
    
    for (symbol in histogram) {
        entity = histogram[symbol];
        tmp_str = tmp_str.split(symbol).join(entity);
    }
    
    return tmp_str;
}
