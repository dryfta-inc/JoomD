/* An InfoBox is like an info window, but it displays
 * under the marker, opens quicker, and has flexible styling.
 * @param {GLatLng} latlng Point to place bar at
 * @param {Map} map The map on which to display this InfoBox.
 * @param {Object} opts Passes configuration options - content,
 *   offsetVertical, offsetHorizontal, className, height, width
 */
function InfoBox(opts) {
  google.maps.OverlayView.call(this);
  this.latlng_ = opts.latlng;
  this.map_ = opts.map;
  this.offsetVertical_ = -(Number(opts.height)+40);
  this.offsetHorizontal_ = 12;
  this.height_ = opts.height;
  this.width_ = opts.width;
  this.content_ = opts.content;
  this.class_ = opts.cssclass;

  var me = this;
  this.boundsChangedListener_ =
    google.maps.event.addListener(this.map_, "bounds_changed", function() {
      return me.panMap.apply(me);
    });

  // Once the properties of this OverlayView are initialized, set its map so
  // that we can display it.  This will trigger calls to panes_changed and
  // draw.
  this.setMap(this.map_);
}

/* InfoBox extends GOverlay class from the Google Maps API
 */
InfoBox.prototype = new google.maps.OverlayView();

/* Creates the DIV representing this InfoBox
 */
InfoBox.prototype.remove = function() {
  if (this.div_) {
    this.div_.parentNode.removeChild(this.div_);
    this.div_ = null;
  }
};

/* Redraw the Bar based on the current projection and zoom level
 */
InfoBox.prototype.draw = function() {
  // Creates the element if it doesn't exist already.
  this.createElement();
  if (!this.div_) return;

  // Calculate the DIV coordinates of two opposite corners of our bounds to
  // get the size and position of our Bar
  var pixPosition = this.getProjection().fromLatLngToDivPixel(this.latlng_);
  if (!pixPosition) return;

  // Now position our DIV based on the DIV coordinates of our bounds
  this.div_.style.width = this.width_ + "px";
  this.div_.style.left = (pixPosition.x + this.offsetHorizontal_) + "px";
  this.div_.style.height = this.height_ + "px";
  this.div_.style.top = (pixPosition.y + this.offsetVertical_) + "px";
  this.div_.style.display = 'block';
};

/* Creates the DIV representing this InfoBox in the floatPane.  If the panes
 * object, retrieved by calling getPanes, is null, remove the element from the
 * DOM.  If the div exists, but its parent is not the floatPane, move the div
 * to the new pane.
 * Called from within draw.  Alternatively, this can be called specifically on
 * a panes_changed event.
 */
InfoBox.prototype.createElement = function() {
  var panes = this.getPanes();
  var div = this.div_;
  if (!div) {
    // This does not handle changing panes.  You can set the map to be null and
    // then reset the map to move the div.
    div = this.div_ = document.createElement("div");
	div.className = this.class_;
    div.style.position = "absolute";
    div.style.width = this.width_ + "px";
    div.style.height = this.height_ + "px";
    var contentDiv = document.createElement("div");
    contentDiv.className = "inner_overlay"
    contentDiv.innerHTML = this.content_;

    var topDiv = document.createElement("div");
    topDiv.style.textAlign = "right";
	topDiv.className = 'windowclose_button';
   /* var closeImg = document.createElement("img");
    closeImg.style.width = "25px";
	closeImg.style.float = "right";
	closeImg.style.margin = "0px 5px 0px 0px";
    closeImg.style.height = "25px";
    closeImg.style.cursor = "pointer";
	closeImg.style.zIndex = "999999999";
    closeImg.src = "modules/mod_joomd_map/images/windowclose.gif";
    topDiv.appendChild(closeImg);
*/
    function removeInfoBox(ib) {
      return function() {
        ib.setMap(null);
      };
    }

    google.maps.event.addDomListener(topDiv, 'click', removeInfoBox(this));

    div.appendChild(topDiv);
    div.appendChild(contentDiv);
    div.style.display = 'none';
    panes.floatPane.appendChild(div);
    this.panMap();
  } else if (div.parentNode != panes.floatPane) {
    // The panes have changed.  Move the div.
    div.parentNode.removeChild(div);
    panes.floatPane.appendChild(div);
  } else {
    // The panes have not changed, so no need to create or move the div.
  }
}

/* Pan the map to fit the InfoBox.
 */
InfoBox.prototype.panMap = function() {
  // if we go beyond map, pan map
  var map = this.map_;
  var bounds = map.getBounds();
  if (!bounds) return;

  // The position of the infowindow
  var position = this.latlng_;

  // The dimension of the infowindow
  var iwWidth = this.width_;
  var iwHeight = this.height_;

  // The offset position of the infowindow
  var iwOffsetX = this.offsetHorizontal_;
  var iwOffsetY = this.offsetVertical_;

  // Padding on the infowindow
  var padX = 40;
  var padY = 40;

  // The degrees per pixel
  var mapDiv	= map.getDiv();
  var mapWidth	= mapDiv.offsetWidth;
  var mapHeight	= mapDiv.offsetHeight;
  var boundsSpan = bounds.toSpan();
  var longSpan	= boundsSpan.lng();
  var latSpan	= boundsSpan.lat();
  var degPixelX = longSpan / mapWidth;
  var degPixelY = latSpan / mapHeight;
  /*
  var fulllat = isFullLat(map, iwHeight, degPixelY);
  var fulllng = isFullLng(map);
  
  // The bounds of the map
  var mapWestLng = fulllng ? -180 :bounds.getSouthWest().lng();
  var mapEastLng = fulllng ? 180 :bounds.getNorthEast().lng();
  var mapNorthLat = fulllat ? 90 :bounds.getNorthEast().lat();
  var mapSouthLat = fulllat ? -90 :bounds.getSouthWest().lat();


  // The bounds of the infowindow
  var iwWestLng = position.lng() + (iwOffsetX - padX) * degPixelX;
  var iwEastLng = position.lng() + (iwOffsetX + iwWidth + padX) * degPixelX;
  var iwNorthLat = position.lat() - (iwOffsetY - padY) * degPixelY;
  var iwSouthLat = position.lat() - (iwOffsetY + iwHeight + padY) * degPixelY;

  // calculate center shift
  var shiftLng =
      (iwWestLng < mapWestLng ? mapWestLng - iwWestLng : 0) +
      (iwEastLng > mapEastLng ? mapEastLng - iwEastLng : 0);
  var shiftLat =
      (iwNorthLat > mapNorthLat ? mapNorthLat - iwNorthLat : 0) +
      (iwSouthLat < mapSouthLat ? mapSouthLat - iwSouthLat : 0);

  // The center of the map
  var center = map.getCenter();

  // The new map center
  var centerX = center.lng() - shiftLng;
  var centerY = center.lat() - shiftLat;
*/
  // center the map to the new shifted center
  //map.setCenter(new google.maps.LatLng(centerY, centerX));
  map.setCenter(position);

  // Remove the listener after panning is complete.
  google.maps.event.removeListener(this.boundsChangedListener_);
  this.boundsChangedListener_ = null;
  
  
  function isFullLng(map) {
        var scale = Math.pow(2, map.getZoom()),
		bounds = map.getBounds(),
		ne = bounds.getNorthEast(),
		sw = bounds.getSouthWest(),
		lat = (ne.lat() <= 0 && sw.lat() >= 0) || (ne.lat() >= 0 && sw.lat() <= 0) ? 0 : Math.min(Math.abs(ne.lat()), Math.abs(sw.lat())), // closest latitude to equator
		deg1 = new google.maps.LatLng(lat, 0),
		deg2 = new google.maps.LatLng(lat, 1),
		coord1 = map.getProjection().fromLatLngToPoint(deg1),
		coord2 = map.getProjection().fromLatLngToPoint(deg2);
        // distance for one long degree in pixels for this zoom level
        var pixelsPerLonDegree = (coord2.x - coord1.x) * scale;
        // width of map's holder should be <= 360 (deg) * pixelsPerLonDegree if full map is displayed
        
        return pixelsPerLonDegree * 360 <= width;

    }
	
	function isFullLat(map, iwHeight, degPixelY) {
        var bounds = map.getBounds(),
            ne = bounds.getNorthEast(),
            sw = bounds.getSouthWest(),
            maxLat = 85-(iwHeight*degPixelY); // max lat degree
        return ne.lat() >= maxLat && sw.lat() <= -maxLat;
    }
  
};