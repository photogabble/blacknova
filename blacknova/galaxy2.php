<?

	include("config.php");
	updatecookie();

  include("languages/$lang");
	$title="Map of Ports";
	include("header.php");

	connectdb();
        $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
        $playerinfo = $res->fields;


	$result = $db->Execute ("SELECT sector_id, port_type FROM $dbtables[universe] ORDER BY sector_id ASC");
        $result2 = $db->Execute("SELECT distinct sector_id FROM $dbtables[movement_log] WHERE ship_id = $playerinfo[ship_id] order by sector_id ASC");
        bigtitle();
	$tile[special]="space261_md_blk.gif";
	$tile[ore]="space262_md_blk.gif";
	$tile[organics]="space263_md_blk.gif";
	$tile[energy]="space264_md_blk.gif";
	$tile[goods]="space265_md_blk.gif";
	$tile[none]="space.gif";
        $tile[unknown] ="uspace.gif";
        echo "<TABLE border=0 cellpadding=0 >\n";
        if(!$result2->Eof) $row2 = $result2->fields;
        while(!$result->EOF)
        {
           $row = $result->fields;
            $break=($row[sector_id]+1)%50;
            if ($break==1)
            {
               echo "<TR ><TD>$row[sector_id]</TD> ";

            }
	   if(!$result2->EOF)
	   {
           
          
                if($row2[sector_id] == $row[sector_id])
                {         
                   $port=$row[port_type];
                   $alt = "$row[sector_id] - $row[port_type]";
                   $result2->Movenext();
                   $row2 = $result2->fields;
                }
                else
                {
                   $port="unknown";
                   $alt = "$row[sector_id] - unknown";
                }
            }
            else
            {
                   $port = "unknown";
                   $alt = "$row[sector_id] -  unknown";

            }
           
                  echo "<TD><A HREF=rsmove.php?engage=1&destination=$row[sector_id]><img src=images/" . $tile[$port] . " alt=\"$alt\" border=0></A></TD>";

                  if ($break==0)
                  {
                     echo "<TD>$row[sector_id]</TD></TR>\n";

                  }

              
              $result->Movenext();
        }
        echo "</TABLE>\n";

        echo "<BR><BR>";
        echo "<img src=images/" . $tile[special] . "> - Special Port<BR>\n";
        echo "<img src=images/" . $tile[ore] . "> - Ore Port<BR>\n";
        echo "<img src=images/" . $tile[organics] . "> - Organics Port<BR>\n";
        echo "<img src=images/" . $tile[energy] . "> - Energy Port<BR>\n";
        echo "<img src=images/" . $tile[goods] . "> - Goods Port<BR>\n";
        echo "<img src=images/" . $tile[none] . "> - No Port<BR><BR>\n";
        echo "<img src=images/" . $tile[unknown] . "> - Unexplored<BR><BR>\n";
	echo "Click <a href=main.php>here</a> to return to main menu.";
	include("footer.php");

?> 

