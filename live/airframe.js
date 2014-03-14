function track_airframe(map)
{
	var markersLayer = new OpenLayers.Layer.Markers("Airframe Track");
	var iconSize =  new OpenLayers.Size(20,20);
	var iconOffset = new OpenLayers.Pixel(-(iconSize.w/2), -iconSize.h);

	$.getJSON( "http://10.1.1.25:1978/airframe/position/now", function( data ) {
		alert("response: "+data);
		var items = [];
		var lat = 0.0;
		var lon = 0.0;
		$.each( data, function( key, val ) {
			if(key == 'lat') lat = val;
			if(key == 'lon') lon = val;
		});
		var marker = new OpenLayers.Marker(
			new OpenLayers.LonLat(lon,lat),
			new OpenLayers.Icon("http://10.1.1.25:1978/airframe/icon.png",iconSize,iconOffset)
		);
		markersLayer.addMarker(marker);
	}).fail(function (jqxhr, textStatus, error) { alert("FAILED:" + textStatus + ":" + error); });

	markersLayer.setVisibility(true);
	map.addLayer(markersLayer);
}
