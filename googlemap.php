<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
  function initialize() {
    var latlng = new google.maps.LatLng(<?php echo $_GET['lat']; ?>, <?php echo $_GET['lon']; ?>);
    var myOptions = {
      zoom: 15,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.SATELLITE
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);

    var hut = new google.maps.Marker({
    	position: latlng,
	map: map,
    	title:"<?php echo $_GET['hut']; ?>"
    });

<?php
if(isset($_GET['track'])) {
	require_once('db.4wd.inc.php');

	$res = pg_query($db, "SELECT ST_AsText(ST_PointN(T.the_geom, generate_series(1, ST_NPoints(T.the_geom)))) FROM nz_walking_and_vehicle_tracks AS T WHERE T.gid = ".$_GET['track'].";");
?>
    var track_path = [
<?php
	while($row = pg_fetch_row($res))
	{
	$txt = substr($row[0],6);
	$etxt = strstr($txt, ' ');
	$txt = substr_replace($txt, '', strpos($txt, ' '));
	$etxt = substr_replace($etxt, '', -1);
	$etxt = substr_replace($etxt, '', 0, 1);
	$lat = $etxt;
	$lon = $txt;
?>
	new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $lon; ?>),
<?php
	}
?>
   ];
   var track = new google.maps.Polyline({
       path: track_path,
       strokecolor: "#FF0000",
       strokeopacity: 1.0,
       strokeweight: 2,
       title: "track",
       map: map
   });
   
   track.setMap(map);

<?php
}
?>
    
  }

</script>
</head>
<body onload="initialize()">
  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>
