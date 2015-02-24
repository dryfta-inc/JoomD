<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD Field Application
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//Address Field type since JoomD 2.3

class JoomdAppFieldAddress	{
	
	protected $parent;
	
	function __construct($parent)
	{
		
		$this->parent = $parent;
		
	}
	
	//Creates a new field
	function addfield($id)
	{
		
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' add column `field_'.$id.'` int(11) not null';
			
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
		
	}
	
	//Updates the already existing field
	function updatefield($id)
	{
	
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' change `field_'.$id.'` `field_'.$id.'` int(11) not null';
			
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
	
	}
	
	//Deletes the field
	function deletefield($id)
	{
	
		$query = 'select `field_'.$id.'` from #__joomd_type'.$this->parent->_typeid.' where `field_'.$id.'` <> ""';
		$this->parent->_db->setQuery( $query );
		$values = (array)$this->parent->_db->loadColumn();
		
		if(count($values))	{
			
			$query = 'delete from #__joomd_field_address where id in ('.implode(',', $values).')';
			$this->parent->_db->setQuery( $query );
			$this->parent->setError($this->parent->_db->getErrorMsg());
			
		}
		
		$query = 'alter table #__joomd_type'.$this->parent->_typeid.' drop `field_'.$id.'`';
			
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
	
	}
	
	//Loads the field in Item add/edit form
	function loadeditform($fieldid, $itemid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = json_decode($this->getfieldvalue($itemid, $field->id));
		
		$custom = json_decode($field->custom);
		
		$document = JFactory::getDocument();
		
		$width = isset($custom->width)?$custom->width:400;
		$height = isset($custom->height)?$custom->height:300;
		$lt 	= isset($custom->lat)?$custom->lat:34.124;
		$ln 	= isset($custom->lng)?$custom->lng:-118.249;
		$zoom 	= (int)isset($custom->zoom)?$custom->zoom:13;
						
		$lat = isset($value->lat)?$value->lat:$lt;
		$lng = isset($value->lng)?$value->lng:$ln;
		$zoom = isset($value->zoom)?$value->zoom:$zoom;
		$address = isset($value->address)?$value->address:null;
		
		$js = '$jd(function()	{
							
					initialize_'.$field->id.'();
					
				});
		
				var map_'.$field->id.';
					var geocoder_'.$field->id.';
					var centerChangedLast_'.$field->id.';
					var reverseGeocodedLast_'.$field->id.';
					var currentReverseGeocodeResponse_'.$field->id.';
					
					function initialize_'.$field->id.'() {
						var latlng = new google.maps.LatLng('.$lat.','.$lng.');
						var myOptions = {
						  zoom: '.$zoom.',
						  center: latlng,
						  mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						map_'.$field->id.' = new google.maps.Map(document.getElementById("map_canvas_'.$field->id.'"), myOptions);
						geocoder = new google.maps.Geocoder();
					
					
						setupEvents_'.$field->id.'();
						centerChanged_'.$field->id.'();
					  }
					
					  function setupEvents_'.$field->id.'() {
						reverseGeocodedLast_'.$field->id.' = new Date();
						centerChangedLast_'.$field->id.' = new Date();
					
						setInterval(function() {
						  if((new Date()).getSeconds() - centerChangedLast_'.$field->id.'.getSeconds() > 1) {
							if(reverseGeocodedLast_'.$field->id.'.getTime() < centerChangedLast_'.$field->id.'.getTime())
							  reverseGeocode_'.$field->id.'();
						  }
						}, 1000);
						
						google.maps.event.addListener(map_'.$field->id.', "zoom_changed", function() {
						  $jd("input[name=\'field_'.$field->id.'[zoom]\']").val(map_'.$field->id.'.getZoom());
						});
						
						google.maps.event.addListener(map_'.$field->id.', "center_changed", centerChanged_'.$field->id.');
					
						google.maps.event.addDomListener(document.getElementById("crosshair_'.$field->id.'"),"dblclick", function() {
						   map_'.$field->id.'.setZoom(map_'.$field->id.'.getZoom() + 1);
						});
					
					  }
					
					  function getCenterLatLngText_'.$field->id.'() {
						var latlng = {lat:map_'.$field->id.'.getCenter().lat(), lng:map_'.$field->id.'.getCenter().lng()};
						
						return latlng;
					  }
					
					  function centerChanged_'.$field->id.'() {
						centerChangedLast_'.$field->id.' = new Date();
						var latlng = getCenterLatLngText_'.$field->id.'();
						$jd("input[name=\'field_'.$field->id.'[lat]\']").val(latlng.lat);
						$jd("input[name=\'field_'.$field->id.'[lng]\']").val(latlng.lng);
						$jd("input[name=\'field_'.$field->id.'[address]\']").val();
						currentReverseGeocodeResponse_'.$field->id.' = null;
					  }
					
					  function reverseGeocode_'.$field->id.'() {
						reverseGeocodedLast_'.$field->id.' = new Date();
						geocoder.geocode({latLng:map_'.$field->id.'.getCenter()},reverseGeocodeResult_'.$field->id.');
					  }
					
					  function reverseGeocodeResult_'.$field->id.'(results, status) {
						currentReverseGeocodeResponse_'.$field->id.' = results;
						if(status == "OK") {
						  if(results.length > 0) {
							$jd("input[name=\'field_'.$field->id.'[address]\']").val(results[0].formatted_address);
						  }
						} else {
						  alert("Error");
						}
					  }
					
					  function geocode_'.$field->id.'() {
						var address = $jd("input[name=\'field_'.$field->id.'[address]\']").val();
						geocoder.geocode({
						  "address": address,
						  "partialmatch": true}, geocodeResult_'.$field->id.');
					  }
					
					  function geocodeResult_'.$field->id.'(results, status) {
						if (status == "OK") {
							if(results.length == 0)	{
								$jd("input[name=\'field_'.$field->id.'[lat]\']").val("");
								$jd("input[name=\'field_'.$field->id.'[lng]\']").val("");
							}
							else
								map_'.$field->id.'.fitBounds(results[0].geometry.viewport);
						} else {
							$jd("input[name=\'field_'.$field->id.'[lat]\']").val("");
							$jd("input[name=\'field_'.$field->id.'[lng]\']").val("");
						//  alert("Geocode was not successful for the following reason: " + status);
						}
					  }
				
			   ';
			   
		$document->addScriptDeclaration($js);
			   
		$html = '<input type="text" id="field_'.$field->id.'_address" name="field_'.$field->id.'[address]" value="'.$address.'" onblur="geocode_'.$field->id.'();" /><br />
				 <div class="map" style="width:'.$width.'px;"><div id="map_canvas_'.$field->id.'" style="width:'.$width.'px; height:'.$height.'px"></div>
				 <div class="crosshair" id="crosshair_'.$field->id.'"></div></div>
				 <p>'.JText::_('LAT').': <input type="text" name="field_'.$field->id.'[lat]" value="'.$lat.'" readonly="readonly" />&nbsp; '.JText::_('LNG').': <input type="text" name="field_'.$field->id.'[lng]" value="'.$lng.'" readonly="readonly" />&nbsp; '.JText::_('ZOOM').': <input type="text" name="field_'.$field->id.'[zoom]" value="'.$zoom.'" readonly="readonly" size="3" /></p>';
		
		return $html;
		
	}
	
	//Loads the field in search form
	function loadsearchform($fieldid, $itemid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
				
		$html = '<input type="text" name="field_'.$field->id.'" id="field_'.$field->id.'" class="search_'.$field->cssclass.'" value="" />';
		
		return $html;
		
	}
	
	//Loads the Field validation
	function loadvalidation($fieldid, $params)
	{
		
		$field = $this->parent->getField($fieldid);
		$js = '';
		
		if($field->required)
			$js .= 'if($jd(\'form[name="'.$params['form'].'"] input[name="field_'.$field->id.'[address]"]\').val() == "") { alert("'.JText::sprintf('SIAREQF', $field->name).'"); return false; }';
		
		return $js;
		
	}
	
	//display the field value in front end based on parameters
	function displayfieldvalue($itemid, $fieldid, $params)
	{
		
		$document = JFactory::getDocument();
		
		$params['short'] = isset($params['short'])?$params['short']:false;
		
		$field = $this->parent->getField($fieldid);
		$custom = json_decode($field->custom);
		
		$value = json_decode($this->getfieldvalue($itemid, $field->id));
		
		$width		= isset($custom->width)?$custom->width:400;
		$height		= isset($custom->height)?$custom->height:300;
		$zoom		= isset($custom->zoom)?$custom->zoom:13;
			
		$lat = isset($value->lat)?$value->lat:null;
		$lng = isset($value->lng)?$value->lng:null;
		$zoom = isset($value->zoom)?$value->zoom:$zoom;
		$address = isset($value->address)?$value->address:null;
		
		if(empty($address))
			return '';
		
		if($params['short'] or empty($lat) or empty($lng))
			return $address;
					
		$js = '$jd(function()	{
							
				initialize_'.$field->id.'();
				
			});
	
			function initialize_'.$field->id.'() {
				var latlng = new google.maps.LatLng('.$lat.','.$lng.');
				var myOptions = {
				  zoom: '.$zoom.',
				  center: latlng,
				  mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map_'.$field->id.' = new google.maps.Map(document.getElementById("field_canvas_'.$field->id.'"), myOptions);
				geocoder = new google.maps.Geocoder();
				
				var marker = new google.maps.Marker({
					position: latlng,
					map: map_'.$field->id.'
				});
				
				 var infowindow = new google.maps.InfoWindow({ content: "'.$address.'" });

				 google.maps.event.addListener(marker, "click", function() {
				  infowindow.open(map_'.$field->id.', marker);
				 });
				
			  }
		
		';
		
		$document->addScriptDeclaration($js);
		
		$html = '<div id="field_canvas_'.$field->id.'" style="width:'.$width.'px;height:'.$height.'px;"></div>';
		
		return $html;
				
	}
	
	//returns the field value
	function getfieldvalue($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$query = 'select field_'.$field->id.' from #__joomd_type'.$this->parent->_typeid.' where itemid = '.$itemid;
		
		$this->parent->_db->setQuery( $query );
		$value = $this->parent->_db->loadResult();
		
		$query = 'select * from #__joomd_field_address where id = '.(int)$value;
		$this->parent->_db->setQuery( $query );
		$json = $this->parent->_db->loadObject();
		
		$json->lat		= isset($json->lat)?$json->lat:null;
		$json->lng		= isset($json->lng)?$json->lng:null;
		$json->zoom		= isset($json->zoom)?$json->zoom:null;
		$json->address	= isset($json->address)?$json->address:null;
		
		if(empty($json->address))
			return null;

		return json_encode($json);
			
	}

	
	//update the field value
	function updatefieldvalue($itemid, $fieldid, $value='')
	{
		
		$field = $this->parent->getField($fieldid);
		
		$query = 'select field_'.$field->id.' from #__joomd_type'.$this->parent->_typeid.' where itemid = '.$itemid;
		$this->parent->_db->setQuery( $query );
		$afid = $this->parent->_db->loadResult();
		
		$json = json_decode($value);
		
		$lat = isset($json->lat)?$json->lat:null;
		$lng = isset($json->lng)?$json->lng:null;
		$zoom = isset($json->zoom)?$json->zoom:null;
		$address = isset($json->address)?$json->address:null;
		
		$query = 'update #__joomd_field_address set lat = '.$this->parent->_db->Quote($lat).', lng = '.$this->parent->_db->Quote($lng).', zoom = '.$zoom.', address = '.$this->parent->_db->Quote($lat).' where id = '.(int)$afid;
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		return true;
		
	}
	
	//checks the field value before storing
	function checkField($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = JRequest::getVar('field_'.$field->id, array(), 'post', 'array');
			
		if($field->required and !isset($value['address']) and empty($value['address']))	{
			$this->parent->setError(JText::sprintf('SIAREQF', $field->name));
			return false;
		}
		
		if(count($value) <> 4 or !array_key_exists('address', $value) or !array_key_exists('lat', $value) or !array_key_exists('lng', $value) or !array_key_exists('zoom', $value))	{
			$this->parent->setError(JText::sprintf('PLSENTVALID', $field->name));
			return false;
		}
		
		return true;
		
	}
	
	//saves the field value
	function saveField($itemid, $fieldid)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$value = JRequest::getVar('field_'.$fieldid, array(), 'post', 'array');
		
		$lat = isset($value['lat'])?$value['lat']:null;
		$lng = isset($value['lng'])?$value['lng']:null;
		$zoom = isset($value['zoom'])?$value['zoom']:null;
		$address = isset($value['address'])?$value['address']:null;
	
			
		$query = 'select f.id from #__joomd_type'.$this->parent->_typeid.' as i join #__joomd_field_address f on i.field_'.$field->id.'=f.id where i.itemid = '.$itemid;
		$this->parent->_db->setQuery( $query );
		$pfid = (int)$this->parent->_db->loadResult();

		
		if($pfid)
			$query = 'update #__joomd_field_address set lat='.$this->parent->_db->Quote($lat).', lng='.$this->parent->_db->Quote($lng).', zoom='.$this->parent->_db->Quote($zoom).', address='.$this->parent->_db->Quote($address).' where id = '.$pfid;
		else
			$query = 'insert into #__joomd_field_address (lat, lng, zoom, address) values('.$this->parent->_db->Quote($lat).', '.$this->parent->_db->Quote($lng).', '.$this->parent->_db->Quote($zoom).', '.$this->parent->_db->Quote($address).')';
		
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
		
		if(!$pfid)
			$pfid = $this->parent->_db->insertid();
			
		$query = 'update #__joomd_type'.$this->parent->_typeid.' set field_'.$field->id.' = '.$this->parent->_db->Quote($pfid).' where itemid = '.$itemid;
		$this->parent->_db->setQuery( $query );
		
		if(!$this->parent->_db->query())	{
			$this->parent->setError($this->parent->_db->getErrorMsg());
			return false;
		}
				
		return true;
		
	}
	
	//loads field options for customizations
	function loadfieldoptions($id, $type)
	{
		
		$field = $this->parent->getField($fieldid);
		
		$custom = json_decode($field->custom);
		
		$lat = isset($custom->lat)?$custom->lat:34.124;
		$lng = isset($custom->lng)?$custom->lng:-118.249;
		$zoom = isset($custom->zoom)?$custom->zoom:13;
		$width = isset($custom->width)?$custom->width:400;
		$height = isset($custom->height)?$custom->height:300;
	
		$html = '<fieldset class="adminform">

		<legend>'.JText::_('FIELD_OPTIONS').'</legend>
		
		<table class="admintable">
			<tbody>
			<tr>
			<td class="key">'.JText::_('WIDTH').':</td>
			<td colspan="2"><input type="text" name="custom[width]" id="customwidth" value="'.$width.'" size="3" /> <span class="hasTip" title="'.JText::_('FIELDMAPWIDTH').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('HEIGHT').':</td>
			<td colspan="2"><input type="text" name="custom[height]" id="customheight" value="'.$height.'" size="3" /> <span class="hasTip" title="'.JText::_('FIELDMAPHEIGHT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('LAT').':</td>
			<td colspan="2"><input type="text" name="custom[lat]" id="customlat" value="'.$lat.'" size="40" /> <span class="hasTip" title="'.JText::_('FIELDLAT').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('LNG').':</td>
			<td colspan="2"><input type="text" name="custom[lng]" id="customlng" value="'.$lng.'" size="10" /> <span class="hasTip" title="'.JText::_('FIELDLNG').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  <tr>
			<td class="key">'.JText::_('ZOOM').':</td>
			<td colspan="2"><input type="text" name="custom[zoom]" id="customzoom" value="'.$zoom.'" size="40" /> <span class="hasTip" title="'.JText::_('FIELDZOOM').'"><img src="'.JURI::root().'components/com_joomd/assets/images/icon-16-info.png" border="0" alt="info" align="top" /></span></td>
		  </tr>
		  </tbody>
			</table>
			
		</fieldset>';
		
		return $html;
		
	}
	
}