<?php require_once('datasources.inc.php'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>OpenLayers Demo</title>
		<style type="text/css">
		html, body, #basicMap {
			width: 100%;
			height: 100%;
			margin: 0;
		}
		</style>
		<script src="openlayers/lib/OpenLayers.js"></script>
		<script>
		function init() {
			map = new OpenLayers.Map("basicMap");
			var local			= new OpenLayers.Layer.OSM("Local Tiles", "http://localhost/cgi-bin/tilecache.cgi/1.0.0/osm/${z}/${x}/${y}.png", {numZoomLevels: 19});
			var fromProjection	= new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
			var toProjection	= new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
			var position		= new OpenLayers.LonLat(172.6,-43.5).transform( fromProjection, toProjection);
			var zoom			= 13; 

			map.addLayer(local);
			map.setCenter(position, zoom );
			



<?php foreach($datasources as $source) { ?>
			var style = new OpenLayers.Style({
                pointRadius: "${radius}",
                fillColor: "<?php echo $source['color']; ?>",
                fillOpacity: 0.8,
                strokeColor: "<?php echo $source['bgcolor']; ?>",
                strokeWidth: 2,
                strokeOpacity: 0.8
            }, {
                context: {
                    radius: function(feature) {
                    return Math.min(feature.attributes.count, 7) + 3;
                    },
                }
            });

            var newLayer = new OpenLayers.Layer.Vector("<?php echo $source['name']; ?>", {
            projection: new OpenLayers.Projection("EPSG:4326"),
            strategies: [
                new OpenLayers.Strategy.BBOX(),
                new OpenLayers.Strategy.Cluster()
            ],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml_data.php?source=<?php echo $source['name']; ?>",  //Note that it is probably worth adding a Math.random() on the end of the URL to stop caching.
                format: new OpenLayers.Format.KML({
                                    extractStyles: true, 
                                    extractAttributes: true
                            }),
            }),
            styleMap: new OpenLayers.StyleMap({
                            "default": style,
                            "select": {
                                fillColor: "#8aeeef",
                                strokeColor: "#32a8a9"
                            }
                    })
            });
            map.addLayer(newLayer);
<?php } ?>

            var switcherControl = new OpenLayers.Control.LayerSwitcher();
            map.addControl(switcherControl);
            switcherControl.maximizeControl();
		}
		</script>
	</head>
		<body onload="init();">
		<div id="basicMap"></div>
	</body>
</html>
