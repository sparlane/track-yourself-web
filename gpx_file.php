<?php
require_once('db.4wd.inc.php');

$res = pg_query($db, "SELECT ST_AsText(ST_PointN(T.wkb_geometry, 1)) FROM nz_walking_and_vehicle_tracks AS T WHERE T.ogc_fid = ".$_GET['track'].";");

$row = pg_fetch_row($res);

$txt = substr($row[0],6);
$etxt = strstr($txt, ' ');
$txt = substr_replace($txt, '', strpos($txt, ' '));
$etxt = substr_replace($etxt, '', -1);
$etxt = substr_replace($etxt, '', 0, 1);
$lat = $etxt;
$lon = $txt;


?>
<?xml version="1.0" encoding="UTF-8"?>
<gpx
  version="1.0"
  creator="GPSBabel - http://www.gpsbabel.org"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xmlns="http://www.topografix.com/GPX/1/0"
  xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd">
<trk>
  <name><?php echo $_GET['track']; ?></name>
<trkseg>
<?php

$res = pg_query($db, "SELECT ST_AsText(ST_PointN(T.wkb_geometry, generate_series(1, ST_NPoints(T.wkb_geometry)))) FROM nz_walking_and_vehicle_tracks AS T WHERE T.ogc_fid = ".$_GET['track'].";");

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
<trkpt lat="<?php echo $lat; ?>" lon="<?php echo $lon; ?>">
</trkpt>
<?php
}
?>
</trkseg>
</trk>
</gpx>
