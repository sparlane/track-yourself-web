<html>
<head>
    <title>OSM Local Tiles</title>
    <link rel="stylesheet" href="style.css" type="text/css" />
    <!-- bring in the OpenLayers javascript library
         (here we bring it from the remote site, but you could
         easily serve up this javascript yourself) -->
    <script src="http://localhost/live/OpenLayers.js"></script>
 
    <!-- bring in the OpenStreetMap OpenLayers layers.
         Using this hosted file will make sure we are kept up
         to date with any necessary changes -->
    <script src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>
 
    <script type="text/javascript">
// Start position for the map (hardcoded here for simplicity)
        var lat=<?php echo $_GET['lat']; ?>;
        var lon=<?php echo $_GET['lon']; ?>;
        var zoom=12;
 
        var map; //complex object of type OpenLayers.Map
 
        //Initialise the 'map' object
        function init() {
 
            map = new OpenLayers.Map ("map", {
                maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
                maxResolution: 156543.0339,
                numZoomLevels: 19,
                units: 'm',
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326")
            } );
 
            var point = new OpenLayers.LonLat(lon, lat).transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    map.getProjectionObject()
                );

            // This is the layer that uses the locally stored tiles
            var newLayer = new OpenLayers.Layer.OSM("Local Tiles", "http://localhost/cgi-bin/tilecache.cgi/1.0.0/osm/${z}/${x}/${y}.png", {numZoomLevels: 19});
            map.addLayer(newLayer);

            map.setCenter(point, zoom);

            var colors = ["black", "blue", "green", "red"];
 
            var style = new OpenLayers.Style({
                pointRadius: "${radius}",
                fillColor: "red",
                fillOpacity: 0.8,
                strokeColor: "#ff5555",
                strokeWidth: 2,
                strokeOpacity: 0.8
            }, {
                context: {
                    radius: function(feature) {
                    return Math.min(feature.attributes.count, 7) + 3;
                    },
                }
            });


            var petrol_stations = new OpenLayers.Layer.Vector("Petrol", {
            projection: new OpenLayers.Projection("EPSG:4326"),
            strategies: [
                new OpenLayers.Strategy.BBOX(),
                new OpenLayers.Strategy.Cluster()
            ],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml_fuelstations.php",  //Note that it is probably worth adding a Math.random() on the end of the URL to stop caching.
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
            map.addLayer(petrol_stations);


            var aerodromes = new OpenLayers.Layer.Vector("Aerodromes", {
            projection: new OpenLayers.Projection("EPSG:4326"),
            strategies: [
                new OpenLayers.Strategy.BBOX(),
                new OpenLayers.Strategy.Cluster()
            ],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml_aerodromes.php",  //Note that it is probably worth adding a Math.random() on the end of the URL to stop caching.
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
            map.addLayer(aerodromes);
 
            var switcherControl = new OpenLayers.Control.LayerSwitcher();
            map.addControl(switcherControl);
            switcherControl.maximizeControl();
        }

    </script>
</head>
 
<!-- body.onload is called once the page is loaded (call the 'init' function) -->
<body onload="init();">
 
    <!-- define a DIV into which the map will appear. Make it take up the whole window -->
    <div style="width:100%; height:100%" id="map"></div>
 
</body>
 
</html>
