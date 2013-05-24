<?php
require_once('db.4wd.inc.php');

if(isset($_POST['request']))
{
	$res = pg_query($db, "SELECT T.*,ST_AsText(T.the_geom),ST_Length(T.the_geom),ST_AsText(ST_PointN(T.the_geom,1)) FROM nz_walking_and_vehicle_tracks AS T WHERE T.track_use = '".$_POST['tuse']."' AND T.the_geom && setSRID(ST_MakeBox2D(setSRID(ST_MakePoint(".$_POST['llon'].",".$_POST['blat']."), 4326), setSRID(ST_MakePoint(".$_POST['rlon'].",".$_POST['tlat']."),4326)),4326) ORDER BY ST_Length(T.the_geom) DESC;");

	echo "<h1>".pg_num_rows($res)." Results</h1>";

	while($row = pg_fetch_row($res))
	{
		$txt = substr($row[10],6);
		$etxt = strstr($txt, ' ');
		$txt = substr_replace($txt, '', strpos($txt, ' '));
		$etxt = substr_replace($etxt, '', -1);
		$etxt = substr_replace($etxt, '', 0, 1);
		$lat = $etxt;
		$lon = $txt;
?>
		<p>
		<?php echo $row[1]." ".($row[9]*100)." ".$row[8]; ?>
		<a href="googlemap.php?lon=<?php echo $lon; ?>&lat=<?php echo $lat; ?>&hut=Start&track=<?php echo $row[0]; ?>">Map</a>
		<a href="doc_track.php?tuse=<?php echo $_POST['tuse']; ?>&track=<?php echo $row[0]; ?>">Nearby Tracks</a>
		</p>
<?php
	}
} else {
?>
<form action="doc_tracks.php" method="post">
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
