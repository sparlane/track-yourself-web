<?php
require_once('db.inc.php');

$res = pg_query($db, "SELECT ogc_fid FROM nz_rivers_and_streams_centrelines LIMIT 1;");

$prev_node_val = '';
while($row = pg_fetch_row($res))
{
	$track_res = pg_query($db, "SELECT ST_PointN(wkb_geometry, generate_series(1, ST_NPoints(wkb_geometry))) FROM nz_rivers_and_streams_centrelines WHERE ogc_fid = ".$row[0].";");
	pg_query($db, "INSERT INTO river_stream (type) VALUES ('river_stream')";
	$in_res_rs = pg_query($db, "SELECT currval('way_ids');";
	$i = 0;
	while($track_row = pg_fetch_row($track_row))
	{
		$in_res1 = pg_query($db, "INSERT INTO node_tmp (geom) VALUES (".$track_row[0].");");
		$in_res1 = pg_query($db, "SELECT currval('node_tmp_ids');";
		if($i != 0)
		{
			$in_res2 = pg_query($db, "INSERT INTO link_tmp VALUES (".$in_res_rs[0].",".$in_res1[0].","$prev_node_val");");
		}
		$prev_node_val = $in_res1[0];
	}	
}
?>
