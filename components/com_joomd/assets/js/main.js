
var $jd = jQuery.noConflict();

$jd(function()	{

	$jd("body").prepend('<div class="dialogbox"></div>');
	
	$jd("#joomdpanel").prepend('<div class="loadingblock"></div>');
	
	if($jd.isFunction($jd.fn.tipsy))
		$jd('.hasTip, .gridicons, #gridedit, .featuredgrid, #sort').tipsy({live:true, html:true});
				
});

$jd.extend($jd.expr[":"], {
  "containsIgnoreCase": function(elem, i, match, array) {
     return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
}}); 

function openShortwindow(url, post, width, height)
{

if($jd(".dialogbox").dialog('isOpen') === true)	{
	process_dialog(url, post);
	return;
}
else	{

	if(!width)
		width = Number($jd(window).width())-100;
	if(!height)
		height = Number($jd(window).height())-100;
	
	 $jd(".dialogbox").dialog({
		modal:true,
		show:"highlight",
		hide:"fade",
		height:height,
		width:width,
		resizable:false,
		draggable:false,
		open: function(event, ui)	{
			
			process_dialog(url, post);
			
		},
		close: function(event, ui)	{
			$jd(this).dialog('destroy').html('');
		}
	});
}
 
}

function process_dialog(url, post)
{
	
	$jd.ajax({
		  url: url,
		  type: "POST",
		  data: post,
		  beforeSend: function()	{
			$jd(".dialogbox").html('<div class="loadingdisplay"></div>');
		  },
		  success: function(data)	{
		   $jd(".dialogbox").html(data);
		   
		   if(typeof(executenow) == 'function')	{

				executenow();
				
			}
		   $jd(".tipsy").remove();
		  }
		  
	});
	
}

function addWindowLoad(func) {
	
	var oldonload = window.onload;
	
	if (typeof window.onload != 'function') {
	
		window.onload = func;
	
	} else {
		
		window.onload = function() {
		
		if (oldonload) {
			oldonload();
		}
		
		func();
		
		}
	}
	
}

function displayalert(msg, type, popup)
{
	
	if(popup)	{
		 
		if(type == "error")	{
			$jd(".popoup dd.message").not(".error").hide();
			$jd(".popoup dd.error").html("<ul><li>"+msg+"</li></ul>").show();
			$jd(".popoup > #system-message").fadeIn(2000);
		}
		else	{
			$jd(".popoup > #system-message").hide();
			$jd(".popoup dd.error").hide();
			$jd(".popoup dd.message").not(".error").html("<ul><li>"+msg+"</li></ul>").show();
			$jd(".popoup > #system-message").fadeIn(2000);
		}
		
	}
	
	else if($jd("#system-message").size() > 0)	{
						
		$jd("#system-message").hide().html("<dt class=\""+type+"\">"+type+"</dt><dd class=\"message "+type+"\"><ul><li>"+msg+"</li></ul></dd>").fadeIn(2000);
	
	}
	
	else	{
	
		var html = "<div id=\"system-message-container\"><dl id=\"system-message\"><dt class=\""+type+"\">"+type+"</dt><dd class=\"message "+type+"\"></dd></dl>";
		
		if($jd("#submenu-box").length == 0)
			$jd("#joomdpanel").prepend(html);
		else
			$jd("#submenu-box").after(html);
		
		if(type=='error')	{
			$jd("dd.message").not('.error').hide();
			$jd("dd.error").html("<ul><li>"+msg+"</li></ul>").show();
		}
		else	{
			$jd("dd.error").hide();
			$jd("dd.message").not('.error').html("<ul><li>"+msg+"</li></ul>").show();
		}
		$jd("#system-message").slideDown("slow");
		
	}

}

function dopin(form, task)
{
	
	$jd("form[name="+form+"] input[name='task']").val(task);
	
    var data = $jd("form[name="+form+"]").serializeArray();
	
	$jd.ajax({
		  url: "index.php",
		  type: "POST",
		  dataType:"json",
		  data: data,
		  beforeSend: function()	{
			$jd(".loadingblock").show();
		  },
		  complete: function()	{
			$jd(".loadingblock").hide();
		  },
		  success: function(data)	{
			 
			if(data.result=="success")
				displayalert(data.msg, "message");
			else
				displayalert(data.error, "error");
				
		  },
		  error: function(jqXHR, textStatus, errorThrown)	{
			displayalert(textStatus, "error");
		  }
	});
				
}

function ic(checked)
{
	
	$jd("input[name='boxchecked']").attr('checked', checked);
	$jd("input[name='boxchecked']").val($jd("input[name='cid[]']:checked").size());
	
}

function removealert()
{
	
	$jd("#system-message").slideUp("slow");

}

function sorttable(filter_order, filter_order_Dir)
{

	$jd("input[name='filter_order']").val(filter_order);
	$jd("input[name='filter_order_Dir']").val(filter_order_Dir);
		
	loaditems("add");

}

function reorder(ordering)
{

	var titles = $jd("table.adminlist tbody").children();
	var size = titles.size(), k=0, c, order, arr = new Array();

	for(var i =0;i<size;i++)	{
		
		c = "row"+k;
		
		$jd(titles[i]).addClass(c);
		
		k=1-k;
		c = "row"+k;
		
		$jd(titles[i]).removeClass(c);
		
		$jd("#"+titles[i].id + " td:first").html(Number(i)+1);
		
		arr.push($jd("#order_"+i+" input[name='ordering[]']").val());
		
	}
	
	for(i=0;i<arr.length;i++)	{
		
		order = ordering[i].substr(6);
		
		$jd("#order_"+order+" input[name='ordering[]']").val(arr[i])
		
	}

}

function isDate(txtDate) {
    
	var validformat=/^\d{4}\-\d{2}\-\d{2}$/;
	
	if (!validformat.test(txtDate)){
		return false;		
	}
	
	txtDate = txtDate.replace(/-/g, '/');
	
	var objDate,  // date object initialized from the txtDate string
	mSeconds, // txtDate in milliseconds
	day,      // day
	month,    // month
	year;     // year
    	 	
    	// extract month, day and year from the txtDate (expected format is mm/dd/yyyy)
    	// subtraction will cast variables to integer implicitly (needed
    	// for !== comparing)
   	month = txtDate.substring(5, 7) ; // because months in JS start from 0
    day = txtDate.substring(8,10);
   	year = txtDate.substring(0, 4);
   	
	// test year range
	if (year < 1000 || year > 3000) {
		return false;
	}
	// convert txtDate to milliseconds
	var dt = new Date(txtDate);
	
	if(dt.getDate()!=day ){
    	return(false);
    }
   	 else if(dt.getMonth()!=month -1){
   		 //this is for the purpose JavaScript starts the month from 0
        return(false);
	}
	else if(dt.getFullYear()!=year ){
		return(false);
	}
  
	// otherwise return true
	 return true;
	 
}
