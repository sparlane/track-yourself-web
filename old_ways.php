<?php
require_once('db.inc.php');

if(isset($_POST['request']))
{
$res = pg_query($db, "SELECT W.*,ST_AsText(N.geom)  FROM ways AS W, nodes AS N, way_nodes AS WN WHERE WN.way_id = W.id AND WN.node_id = N.id AND contains(setSRID(ST_MakeBox2D(setSRID(ST_MakePoint(".$_POST['llon'].",".$_POST['blat']."), 4326), setSRID(ST_MakePoint(".$_POST['rlon'].",".$_POST['tlat']."),4326)),4326), N.geom) AND exist(W.tags, 'highway');");

echo "<h1>".pg_num_rows($res)." Results</h1>";

while($row = pg_fetch_row($res))
{
?>
<p>
<?php echo $row[7]." ".$row[5]; ?>
</p>
<?php
}
} else {
?>
<form action="index.php" method="post">
Left: <input name="llon" value="174.00"/>
Top: <input name="tlat" value="-45.00"/>
Right: <input name="rlon" value="175.00"/>
Bottom: <input name="blat" value="-49.00"/>
<input type="submit" name="request" value="Get" />
</form>
<?php
}
?>
