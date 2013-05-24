<?php
require_once('db.inc.php');

$res = pg_query($db, "SELECT * FROM ways AS W WHERE W.tags @> hstore('natural','coastline') AND defined(W.tags, 'name')");

echo "<h1>".pg_num_rows($res)." Results</h1>";

while($row = pg_fetch_row($res))
{
?>
<p>
<?php echo $row[5]." ".$row[6]; ?>
</p>
<?php
}
?>
