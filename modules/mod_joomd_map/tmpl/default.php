<?php

/*------------------------------------------------------------------------
# mod_joomd_map - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2011 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php

$resize = $params->get('resize', 1);
$getdir = $params->get('getdir', 1);

$height = (int)$params->get('height', 350);
$width = (int)$params->get('width', 600);

$maxheight = (int)$params->get('maxheight', 900);
$maxwidth = (int)$params->get('maxwidth', 500);

$do = 0;

?>

	<script type="text/javascript">
	
	$jd(function()	{
		initialize();
	});
		
	var jd_map = null;
	var jd_geocoder = null;
	var jd_markers=new Array();
	var jd_your_lat;
	var jd_your_lng;
	var jd_lat=<?php echo $params->def('lat', 34.124); ?>;
	var jd_lng=<?php echo $params->def('lng', -118.249); ?>;
	var jd_zoom=<?php echo $params->def('zoom', 13); ?>;
	var jd_latlng=null;
	var jd_browserSupportFlag =  new Boolean();
	var width = <?php echo $width; ?>;
	var height = <?php echo $height; ?>;
	
	var jd_directionsService = new google.maps.DirectionsService();
	var jd_directionsDisplay = new google.maps.DirectionsRenderer();
	
	// these variables are not required by LegendControl	

    function initialize() {
		
		jd_geocoder = new google.maps.Geocoder();
		
		jd_latlng = new google.maps.LatLng(jd_lat, jd_lng);
		
		var myOptions = {
			zoom: jd_zoom,
			center: jd_latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false,
			panControl: false,
			panControlOptions: {
				position: google.maps.ControlPosition.TOP_LEFT
			},
			zoomControl: true,
			zoomControlOptions: {
			style: google.maps.ZoomControlStyle.LARGE,
				position: google.maps.ControlPosition.LEFT_TOP
			},
			scaleControl: true,
			scaleControlOptions: {
				position: google.maps.ControlPosition.BOTTOM_LEFT
			},
			streetViewControl: true,
			streetViewControlOptions: {
				position: google.maps.ControlPosition.TOP_LEFT
			}
		
		};
		
        jd_map = new google.maps.Map(document.getElementById("jd_map"), myOptions);	
		
		google.maps.event.addListenerOnce(jd_map, 'bounds_changed', get_jd_markers);
		
		google.maps.event.addListener(jd_map, 'zoom_changed', get_jd_markers);
		google.maps.event.addListener(jd_map, 'dragend', get_jd_markers);
		
		<?php if($getdir)	{	?>
		
		if(navigator.geolocation)	{
			jd_browserSupportFlag = true;
			navigator.geolocation.getCurrentPosition(function(position) {
			  
			  jd_your_lat = position.coords.latitude;
			  jd_your_lng = position.coords.longitude;
			  
			  var temp = {you:1, lat:jd_your_lat, lng:jd_your_lng, html:"<?php echo $user->id?$user->name:JText::_('You'); ?>"};
			  
			  var marker = createMarker(temp);
			
			  initjd_markers();
			
			}, function() {
				initjd_markers();
				handleNoGeolocation(jd_browserSupportFlag);
			});
			
		}
		
		<?php	}	?>
			
	}
	
	function handleNoGeolocation(errorFlag) {
		if (errorFlag == true) {
		//	alert('nogeo');
		}
		
	}
	
	function get_jd_markers() {
			
		
		var bounds = jd_map.getBounds();
		
		var fulllat = isFullLat();
		var fulllng = isFullLng();
		
		min_lat = fulllat ? -90 : bounds.getSouthWest().lat();
		max_lat = fulllat ? 90 : bounds.getNorthEast().lat();
		
		min_lng = fulllng ? -180 : bounds.getSouthWest().lng();
		max_lng = fulllng ? 180 : bounds.getNorthEast().lng();
		
		var coordinate =min_lat+','+max_lat+','+min_lng+','+max_lng;
		
		$jd.ajax({
			  url: "<?php echo JURI::root(); ?>",
			  type: "POST",
			  dataType:"json",
			   data: {'option':'com_joomd', 'task':'mod_task', 'action':'joomd_map-loadmarker', 'latlngbound':coordinate, 'Itemid':"<?php echo JRequest::getVar('Itemid', ''); ?>", 'abase':1, '<?php echo jutility::getToken(); ?>':1},
			  success: function(data)	{
				  
				if(data.result == "success")	{
					jd_markers = data.data;
					initjd_markers();
				}
				else
					alert(data.error);
					
			  }
		});
		
    }
	
	function isFullLng() {
        var scale = Math.pow(2, jd_map.getZoom()),
		bounds = jd_map.getBounds(),
		ne = bounds.getNorthEast(),
		sw = bounds.getSouthWest(),
		lat = (ne.lat() <= 0 && sw.lat() >= 0) || (ne.lat() >= 0 && sw.lat() <= 0) ? 0 : Math.min(Math.abs(ne.lat()), Math.abs(sw.lat())), // closest latitude to equator
		deg1 = new google.maps.LatLng(lat, 0),
		deg2 = new google.maps.LatLng(lat, 1),
		coord1 = jd_map.getProjection().fromLatLngToPoint(deg1),
		coord2 = jd_map.getProjection().fromLatLngToPoint(deg2);
        // distance for one long degree in pixels for this zoom level
        var pixelsPerLonDegree = (coord2.x - coord1.x) * scale;
        // width of map's holder should be <= 360 (deg) * pixelsPerLonDegree if full map is displayed
        
        return pixelsPerLonDegree * 360 <= width;

    }
	
	function isFullLat() {
        var bounds = jd_map.getBounds(),
            ne = bounds.getNorthEast(),
            sw = bounds.getSouthWest(),
            maxLat = 85; // max lat degree
        return ne.lat() >= maxLat && sw.lat() <= -maxLat;
    }
	
	function initjd_markers() {
			
		// create arrays of markers of the various types
		for (var i = 0; i < jd_markers.length; i++) {
			
			var marker = createMarker(jd_markers[i]);
						
		}
			
	}
	
	/**
	 * Create a marker
	 * @param object obj Object literal specifying marker attributes
	 * @return GMarker
	 */
	function createMarker(obj) {
		
		if(obj.you)	{
			var icon = '<?php echo JURI::root(); ?>modules/mod_joomd_map/images/icon_you.png';
			var cssclass = 'infobox_overlay user_overlay';
			
			var w = 150;
			var h = 100;
		}
		else	{
			var cssclass = 'infobox_overlay';
			<?php if($getdir)	{	?>
			obj.html += '<div class="gdir" rel="'+obj.lat+', '+obj.lng+'"><?php echo JText::_('GETDIRECTION'); ?></div>';
			<?php	}	?>
			
			var icon = '<?php echo JURI::root(); ?>modules/mod_joomd_map/images/icon.png';
			
			var w = 266;
			var h = 165;
			
		}
		
		var image = new google.maps.MarkerImage(icon,
		// This marker is 20 pixels wide by 32 pixels tall.
		new google.maps.Size(20, 32),
		// The origin for this image is 0,0.
		new google.maps.Point(0,0),
		// The anchor for this image is the base of the flagpole at 0,32.
		new google.maps.Point(0, 32));
		
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(obj.lat, obj.lng),
			map: jd_map,
			icon: image
		});
		
		google.maps.event.addListener(marker, 'click', function(e) {
			var infoBox = new InfoBox({latlng: marker.getPosition(), map: jd_map, content:obj.html, cssclass: cssclass, width:w, height:h});
		});
		
		return marker;
		
	}
			
	//map size controller start
	<?php if($resize)	{	?>
	
	$jd('#jd_resizecontrol').live('click', function()	{
		
		$jd(this).toggleClass('jd_map_collapse');
		
		if($jd(this).hasClass('jd_map_collapse'))	{
			height = '<?php echo $maxheight; ?>';
			width = '<?php echo $maxwidth; ?>';
		}
		else	{
			height = '<?php echo $height; ?>';
			width = '<?php echo $width; ?>';
		}
		
		$jd("#jd_map").animate({
			
			width: width,
			height: height
			
		});
		width = width-20;
		$jd(this).animate({
			
			left: width
			
		}, function()	{
			google.maps.event.trigger(jd_map, 'resize'); 
		});
				
	});
	<?php	}	?>
	//map size controller end
	
	//get direction start
	<?php if($getdir)	{	?>
	$jd(".gdir").live('click', function()	{
							   
		jd_directionsDisplay.setMap(jd_map);
		jd_directionsDisplay.setPanel(document.getElementById('jd_directions'));
		
		var request = {
		  origin: jd_your_lat + ", " + jd_your_lng,
		  destination: $jd(this).attr('rel'),
		  travelMode: google.maps.DirectionsTravelMode.DRIVING
		};
		
		jd_directionsService.route(request, function(response, status) {
		  if (status == google.maps.DirectionsStatus.OK) {
			jd_directionsDisplay.setDirections(response);
			
			$jd("#jd_directions").css({'overflow':'scroll'});
		
			$jd("#jd_directions").show();
			
			$jd("#jd_directions").animate({'height':<?php echo $height-15; ?>, 'width':400, 'padding-top':20});
			
			$jd('#jd_dir_close').show();
			
		  }
		  
		  else if (status == google.maps.DirectionsStatus.ZERO_RESULTS) {
			
			alert('<?php echo JText::_('CANTGETROUTE'); ?>');
			  
		  }
		  
		});
		
	});
		
	$jd("#jd_dir_close").live('click', function()	{
										
		$jd("#jd_directions").toggle();
		
		$jd(this).toggleClass('hide_route');
		
	});
	<?php	}	?>
	//get direction end

</script>


<div id="jd_map" style="z-index:5;width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;"></div>
<?php if($resize)	{	?>
<div id="jd_resizecontrol" class="resize_control" style="left: <?php echo $width-20; ?>px;">resize</div>
<?php	}	?>
<?php if($getdir)	{	?>
<div id="jd_dir_close" class="close_jd_dir" style="margin-top:-<?php echo $height; ?>px;">show/hide</div>
<div id="jd_directions" class="jd_dir_panel" style="margin-top:-<?php echo $height; ?>px;"></div>
<?php	}	?>