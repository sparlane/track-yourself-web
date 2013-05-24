<?php
require_once('db.4wd.inc.php');

if(!isset($_POST['gpxdata']))
{
?>
<form action="" method="post">
GPX Data: <textarea name="gpxdata"></textarea><br />
<input type="submit" value="Upload" />
</form>
<?php
} else {
$data = simplexml_load_string($_POST['gpxdata']);

//print_r($data->trk->trkseg->trkpt);

$i = 0;

foreach ($data->trk->trkseg as $d)
{
	$pointdata = "";
	$j = 0;
	foreach($d->trkpt as $tp)
	{
		if($j >= 1) $pointdata .= ", ";
		$pointdata .= $tp['lon']." ".$tp['lat'];
		//echo ($i++).": ".$tp['lat']." ".$tp['lon']."<br />";
		$j++;
	}
	$sqldata = "INSERT INTO my_tracks (wkb_geometry) VALUES ('LINESTRING(".$pointdata.")'::geometry)"; 
	print($sqldata);
	pg_query($sqldata);
}

}
?>
