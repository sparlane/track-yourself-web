<?php
require_once('../db.4wd.inc.php');

// Creates the KML/XML Document.
$dom = new DOMDocument('1.0', 'UTF-8');
 
// Creates the root KML element and appends it to the root document.
$node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
$parNode = $dom->appendChild($node);
 
// Creates a KML Document element and append it to the KML element.
$dnode = $dom->createElement('Document');
$docNode = $parNode->appendChild($dnode);
 
 
$bbox = $_GET['bbox']; // get the bbox param from google earth
list($bbox_south, $bbox_west, $bbox_east, $bbox_north) = preg_split("/,/", $bbox); // west, south, east, north

// Get the data from the Database Table (planet_osm_point)
$sql = "SELECT gid AS id, ST_X(ST_PointN(geom,1)) as lon, ST_Y(ST_PointN(geom,1)) as lat FROM nz_runways_and_airstrips WHERE (box(point(" . $bbox_south . "," . $bbox_west . "),point(" . $bbox_east . "," . $bbox_north . ")) ~ (geom)) LIMIT 1000";
                //or with transform:
	//$sql = "SELECT osm_id, name, x(transform(way,4326)) as lon, y(transform(way, 4326)) as lat FROM planet_osm_point WHERE (amenity='" . $what . "') AND (box(point(" . $bbox_south . "," . $bbox_west . "),point(" . $bbox_east . "," . $bbox_north . ")) ~ transform(way,4326)) LIMIT 100";

	// perform query
	$query = pg_query($sql);
	if ($query) {
		if (pg_num_rows($query) > 0) { // found something

			// Iterates through the results, creating one Placemark for each row.
			while ($row = pg_fetch_array($query))
			{
				 // Creates a Placemark and append it to the Document.
				  $node = $dom->createElement('Placemark');
				  $placeNode = $docNode->appendChild($node);

				  // Creates an id attribute and assign it the value of id column.
				  $placeNode->setAttribute('id', 'placemark' . $row['id']);

				  // Create name, and description elements and assigns them the values of the name and address columns from the results.
				  //$nameNode = $dom->createElement('name',htmlentities($row['name']));
				  //$placeNode->appendChild($nameNode);

				  // Creates a Point element.
				  $pointNode = $dom->createElement('Point');
				  $placeNode->appendChild($pointNode);

				  // Creates a coordinates element and gives it the value of the lng and lat columns from the results.
				  $coorStr = $row['lon'] . ','  . $row['lat'];
				  $coorNode = $dom->createElement('coordinates', $coorStr);
				  $pointNode->appendChild($coorNode);
			}

		} else { // nothing found
		}
	}
 
$kmlOutput = $dom->saveXML();
header('Content-type: application/vnd.google-earth.kml+xml');
echo $kmlOutput;
?>
