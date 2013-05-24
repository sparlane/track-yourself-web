<?php
require_once('db.4wd.inc.php');

$res = pg_query($db, "SELECT ST_AsText(ST_PointN(T.geom, 1)) FROM nz_walking_and_vehicle_tracks AS T WHERE T.gid = ".$_GET['track'].";");

$row = pg_fetch_row($res);

$txt = substr($row[0],6);
$etxt = strstr($txt, ' ');
$txt = substr_replace($txt, '', strpos($txt, ' '));
$etxt = substr_replace($etxt, '', -1);
$etxt = substr_replace($etxt, '', 0, 1);
$lat = $etxt;
$lon = $txt;


?>
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
    var latlng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>);
    var myOptions = {
      zoom: 13,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.SATELLITE
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);

<?php 
if(ISSET($_GET['hut']))
{
	$res = pg_query($db, "SELECT descriptio,ST_AsText(geom) FROM doc_huts WHERE gid = ".$_GET['hut']);
	while($row = pg_fetch_row($res))
	{
	$temp = preg_replace('/POINT\(/','', $row[1]);
	$data = preg_replace('/\)/', '', $temp);
	$point = explode(' ', $data);
?>
	var hutPoint = new google.maps.Marker({
		position: new google.maps.LatLng(<?php echo $point[1]; ?>,<?php echo $point[0]; ?>),
		map: map,
		title: "<?php echo trim($row[0]); ?>"
	});

<?php
	}
}

?>

    var track_path = [
<?php

$res = pg_query($db, "SELECT ST_AsText(ST_PointN(T.geom, generate_series(1, ST_NPoints(T.geom)))) FROM nz_walking_and_vehicle_tracks AS T WHERE T.gid = ".$_GET['track'].";");

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
       strokeColor: "#00FF00",
       strokeOpacity: 1.0,
       strokeWeight: 2,
       title: "track",
       map: map
   });
   
   track.setMap(map);

<?php

$res = pg_query($db, "SELECT T.*,ST_Length(T.geom),ST_AsText(ST_PointN(T.geom,1)),Distance(T.geom,T2.geom) FROM nz_walking_and_vehicle_tracks AS T, nz_walking_and_vehicle_tracks AS T2 WHERE T.track_use = '".$_GET['tuse']."' AND T2.gid = ".$_GET['track']." AND T.gid != T2.gid AND Distance(T.geom,T2.geom) < 0.1 ORDER BY Distance(T.geom,T2.geom) ASC;");
$i = 0;

while($row = pg_fetch_row($res))
{
	$tuid = $row[0];
	$tname = $row[1];
	$track_res = pg_query($db, "SELECT ST_AsText(ST_PointN(T.geom, generate_series(1, ST_NPoints(T.geom)))) FROM nz_walking_and_vehicle_tracks AS T WHERE T.gid = ".$row[0].";");
?>
    var track_path<?php echo $i; ?> = [
<?php
	while($track_row = pg_fetch_row($track_res))
	{
	$txt = substr($track_row[0],6);
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

	var trackPoint = new google.maps.Marker({
		position: new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $lon; ?>),
		map: map,
		title: "<?php echo $tuid; ?> (<?php echo $tname; ?>)"
	});
   var track<?php echo $i; ?> = new google.maps.Polyline({
       path: track_path<?php echo $i; ?>,
       strokeColor: "#FF0000",
       strokeOpacity: 1.0,
       strokeWeight: 2,
       title: "track<?php echo $i; ?>",
       map: map
   });
   
   track<?php echo $i; ?>.setMap(map);
<?php
$i++;
}
?>
   }
</script>
</head>
<body onload="initialize()">
  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>
