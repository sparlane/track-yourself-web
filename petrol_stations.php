<head>
	<script src="http://localhost/cgi-bin/start_pos.py"></script>
</head>
<body>
<?php
require_once('db.4wd.inc.php');

if(isset($_POST['request']))
{

$pt = "ST_GeomFromText('Point (".$_POST['lon']." ".$_POST['lat'].")',4326)";

$query = "SELECT P.*,ST_AsText(P.geom),ST_Distance(".$pt.", P.geom) FROM nz_petrol_stations AS P ORDER BY ST_Distance(".$pt.", P.geom) LIMIT 20;";

$res = pg_query($db, $query);

echo "<h1>".pg_num_rows($res)." Results</h1>";

while($row = pg_fetch_row($res))
{
$txt = substr($row['12'],6);
$etxt = strstr($txt, ' ');
$txt = substr_replace($txt, '', strpos($txt, ' '));
$etxt = substr_replace($etxt, '', -1);
$etxt = substr_replace($etxt, '', 0, 1);
$lat = $etxt;
$lon = $txt;
$name = trim($row[2]);
$number = trim($row[6]);
$address = trim($row[5]);
$hours = trim($row[7]);
$psid = $row[0];
?>
<p>
<?php echo "<h2>".$name."</h2>".$row[6]; ?>
<ul>
<li>Address: <?php echo $address; ?></li>
<li>Hours: <?php echo $hours; ?></li>
<li>Number: <?php echo $number; ?></li>
<li><a href="live/slippymap.php?lat=<?php echo $lat; ?>&lon=<?php echo $lon; ?>&name=<?php echo $name; ?>">Map</a></li>
</ul>
</p>
<?php
}
} else {
?>
<form action="petrol_stations.php" method="post">
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
