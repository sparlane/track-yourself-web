<?php
require_once('db.4wd.inc.php');

$res = pg_query($db, "SELECT name,ST_AsText(D.wkb_geometry) FROM doc_huts AS D WHERE D.ogc_fid = ".$_GET['hut'].";");

$row = pg_fetch_row($res);

$txt = substr($row[1],6);
$etxt = strstr($txt, ' ');
$txt = substr_replace($txt, '', strpos($txt, ' '));
$etxt = substr_replace($etxt, '', -1);
$etxt = substr_replace($etxt, '', 0, 1);
$lat = $etxt;
$lon = $txt;
$hutname = $row[0];

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

	var hutPoint = new google.maps.Marker({
		position: new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $lon; ?>),
		map: map,
		title: "<?php echo trim($hutname); ?>"
	});

<?php 
	$res = pg_query($db, "SELECT D.ogc_fid,D.name,ST_AsText(D.wkb_geometry) FROM doc_backcountry_huts AS D, doc_backcountry_huts AS D2 WHERE D2.ogc_fid = ".$_GET['hut']." AND Distance(D.wkb_geometry, D2.wkb_geometry) < 0.1 AND D.ogc_fid != D2.ogc_fid");
	while($row = pg_fetch_row($res))
	{
	$temp = preg_replace('/POINT\(/','', $row[2]);
	$data = preg_replace('/\)/', '', $temp);
	$point = explode(' ', $data);
?>
	var hutPoint = new google.maps.Marker({
		position: new google.maps.LatLng(<?php echo $point[1]; ?>,<?php echo $point[0]; ?>),
		map: map,
		title: "<?php echo trim($row[1])."[".$row[0]."](".$point[1].",".$point[0].")"; ?>"
	});	
<?php
	}
?>

<?php

$res = pg_query($db, "SELECT T.ogc_fid,Distance(T.wkb_geometry,D.wkb_geometry) FROM nz_walking_and_vehicle_tracks AS T, doc_backcountry_huts AS D WHERE T.track_use = 'foot' AND D.ogc_fid = ".$_GET['hut']." AND Distance(T.wkb_geometry,D.wkb_geometry) < 0.1 ORDER BY Distance(T.wkb_geometry,D.wkb_geometry) ASC;");

$i = 0;

while($row = pg_fetch_row($res))
{
	$track_res = pg_query($db, "SELECT ST_AsText(ST_PointN(T.wkb_geometry, generate_series(1, ST_NPoints(T.wkb_geometry)))) FROM nz_walking_and_vehicle_tracks AS T WHERE T.ogc_fid = ".$row[0].";");
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
