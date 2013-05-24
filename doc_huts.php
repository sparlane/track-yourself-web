<?php
require_once('db.4wd.inc.php');

if(isset($_POST['request']))
{

$res = pg_query($db, "SELECT D.*,ST_AsText(D.the_geom) FROM doc_huts AS D WHERE contains(ST_MakeBox2D(ST_MakePoint(".$_POST['llon'].",".$_POST['blat']."), ST_MakePoint(".$_POST['rlon'].",".$_POST['tlat'].")), D.the_geom);");

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
$hutname = trim($row[2]);
$hutid = $row[0];
?>
<p>
<?php echo "<h2>".$hutname."</h2>".$row[6]; ?>
<?php
$track_res = pg_query($db, "SELECT T.*,ST_Length(T.the_geom),Distance(T.the_geom,GeomFromText('".$row[6]."')) FROM nz_walking_and_vehicle_tracks AS T WHERE T.the_geom && expand(GeomFromText('".$row[6]."'),1) AND T.track_use = '".$_POST['tuse']."' ORDER BY Distance(T.the_geom,GeomFromText('".$row[6]."')) ASC LIMIT 10;");
?>
<ul>
<li><a href="googlemap.php?lat=<?php echo $lat; ?>&lon=<?php echo $lon; ?>&hut=<?php echo $hutname; ?>">Map</a></li>
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
<form action="doc_huts.php" method="post">
<table>
	<tr>
		<td>Left:</td><td><input name="llon" value="172.00"/></td>
		<td>Top:</td><td><input name="tlat" value="-43.40"/></td>
	</tr>
	<tr>
		<td>Right:</td><td><input name="rlon" value="173.20"/></td>
		<td>Bottom:</td><td><input name="blat" value="-43.90"/></td>
	</tr>
	<tr>
		<td>Track Types:</td><td>
			<select id="tuse" name="tuse">
				<option value="vehicle">Vechile</option>
				<option value="foot">Walking</option>
			</select>
		</td>
		<td></td><td><input type="submit" name="request" value="Get" /></td>
	</tr>
</table>
</form>
<?php
}
?>
