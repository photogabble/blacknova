<?

	include("config.php");
	updatecookie();

  include("languages/$lang");
	$title="View Galactic Distances";
	include("header.php");

	connectdb();


  $result = $db->Execute ("SELECT sector_id, x, y, z FROM $dbtables[universe] ORDER BY sector_id ASC");
        bigtitle();
	while (!$result->EOF)
	{
		$row = $result->fields;
    echo "$row[sector_id], $row[x], $row[y], $row[z]<BR>";
    $result->MoveNext();
	}
	echo "Click <a href=main.php>here</a> to return to main menu.";
	include("footer.php");

?> 

