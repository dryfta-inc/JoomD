// JavaScript Document

window.onload = loadcats;

function loadXMLDoc(url,cfunc)
{
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=cfunc;
	xmlhttp.open("POST",url,true);
	xmlhttp.send();
}


function loadcats()
{

	var type = document.getElementById('jform_request_typeid');
	var cat = document.getElementById('jform_request_catid');
	
	var url = "index.php?option=com_joomd&view="+type.className+"&task=loadcats&typeid="+type.value;
	
	if(cat.value)
		url += "&catid="+cat.value;
	
	url += '&abase=1';
	
	loadXMLDoc(url, function()
	{											
	  if(xmlhttp.readyState==4)
	  {
	   if(xmlhttp.status==200) 
		{
		 var response = xmlhttp.responseText;
		 
		 document.getElementById('jform_request_catid').innerHTML = response;
		}
	   }
	});
}