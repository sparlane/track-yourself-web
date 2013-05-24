<head>
	<script src="http://localhost/cgi-bin/start_pos.py"></script>
</head>
<body>
<?php
require_once('db.4wd.inc.php');

if(isset($_POST['request']))
{

$pt = "ST_MakePoint(".$_POST['lon'].",".$_POST['lat'].")";

$res = pg_query($db, "SELECT D.*,ST_AsText(D.the_geom),Distance(".$pt.", D.the_geom) FROM doc_campsites AS D ORDER BY Distance(".$pt.", D.the_geom) LIMIT 10;");

echo "<h1>".pg_num_rows($res)." Results</h1>";

while($row = pg_fetch_row($res))
{
$txt = substr($row['6'],6);
$etxt = strstr($txt, ' ');
$txt = substr_replace($txt, '', strpos($txt, ' '));
$etxt = substr_replace($etxt, '', -1);
$etxt = substr_replace($etxt, '', 0, 1);
$lat = $etxt;
$lon = $txt;
$campname = trim($row[2]);
$campid = $row[0];
$status = trim($row[1]);
$cat = trim($row[3]);
$type = trim($row[4]);
?>
<p>
<?php echo "<h2>".$campname."</h2>".$row[6]; ?>
<?php
$track_res = pg_query($db, "SELECT T.*,ST_Length(T.the_geom),Distance(T.the_geom,GeomFromText('".$row[6]."')) FROM improved_nz_road_centrelines AS T WHERE T.the_geom && expand(GeomFromText('".$row[6]."'),1)  ORDER BY Distance(T.the_geom,GeomFromText('".$row[6]."')) ASC LIMIT 10;");
?>
<ul>
<li><a href="googlemap.php?lat=<?php echo $lat; ?>&lon=<?php echo $lon; ?>&hut=<?php echo $hutname; ?>">Map</a></li>
<li>Status: <?php echo $status; ?></li>
<li>Category: <?php echo $cat; ?></li>
<li>Type: <?php echo $type; ?></li>
<?php
while($track_row = pg_fetch_row($track_res))
{
$len = $track_row[8] * 100;
$dis = $track_row[9] * 100;
?>
<li>
<?php echo $track_row[5]." ".$track_row[3]." ".$len."km ".$dis;
?>
 <a href="googlemap.php?lat=<?php echo $lat; ?>&lon=<?php echo $lon; ?>&hut=<?php echo $hutname; ?>&track=<?php echo $track_row['0']; ?>">Map</a>
 <a href="doc_track.php?tuse=<?php echo $_POST['tuse']; ?>&track=<?php echo $track_row[0]; ?>&hut=<?php echo $hutid; ?>">Nearby Tracks</a>
</li>
<?php
}
?>
</ul>
</p>
<?php
}
} else {
?>
<form action="doc_campsites.php" method="post">
<table>
	<tr>
		<td>Longitude:</td><td><input name="lon"/></td>
		<td>Latitude:</td><td><input name="lat"/></td>
	</tr>
	<tr>
		<td/>
		<td></td><td><input type="submit" name="request" value="Get" /></td>
	</tr>
</table>
</form>
<?php
}
?>
</body>
