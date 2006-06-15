<?php
            function BuildTwoCol( $text_col1 = "&nbsp;", $text_col2 = "&nbsp;", $align_col1 = "left", $align_col2 = "left" )
            {
                echo"<tr>";
                echo"<td align=".$align_col1.">".$text_col1."</td>";
                echo"<td align=".$align_col2.">".$text_col2."</td>";
                echo"</tr>";
            }
?>
