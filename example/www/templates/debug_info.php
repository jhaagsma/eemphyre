<?php
/*---------------------------------------------------
These files are part of the empiresPHPframework;
The original framework core (specifically the mysql.php
the router.php and the errorlog) was started by Timo Ewalds,
and rewritten to use APC and extended by Julian Haagsma,
for use in Earth Empires (located at http://www.earthempires.com );
it was spun out for use on other projects.

The general.php contains content from Earth Empires
written by Dave McVittie and Joe Obbish.


The example website files were written by Julian Haagsma.

All files are licensed under the MIT License.

First release, September 3, 2012
---------------------------------------------------*/

function debug_info($size = "n/a"){
	global $db, $cache, $time_start;

	$str = "\n<div id='dbug'>The code clocked " . codetime($time_start) . " milliseconds doing " . $db->count . " db queries;";
	$str .=  " This page is " . $size . "k, and was allocated a maximum of " . number_format(memory_get_peak_usage()/1000) . "k of RAM (256,000 max allowed)\n";
	
	
	$str  .= "<table>\n\t<tr><td colspan='5'>APC queries</td></tr><tr class='rowheader'><td>Time</td><td>Action</td><td>Success</td><td>Key</td><td>TTL</td></tr>";
	$totaltime = 0;
	$i = 0;
	$rows = array('row1','row2');

	foreach($cache->queries as $row){
		$totaltime += $row[2];
		$str .=  "\n\t<tr class='" . $rows[$i = !$i] . "'>";
		$str .=  "\n\t\t<td style='white-space:nowrap;text-align:right;vertical-align:top;'>" . number_format($row[2]*1000, 3) . " ms</td>";
		$str .=  "\n\t\t<td style='white-space:nowrap;text-align:left;vertical-align:top;'>$row[0]</td>";
		$str .=  "\n\t\t<td style='white-space:nowrap;text-align:left;vertical-align:top;'>$row[1]</td>";
		$str .=  "\n\t\t<td style='white-space:nowrap;text-align:left;vertical-align:top;'>$row[3]</td>";
		$str .=  "\n\t\t<td style='white-space:nowrap;text-align:left;vertical-align:top;'>$row[4]</td>";
		$str .=  "\n\t</tr>";
	}
	$str .=  "\n\t<tr class='rowheader'><td style='white-space:nowrap;text-align:right;vertical-align:top;'>" . number_format($totaltime*1000, 3) . " ms</td>";
	$str .=  "\n\t\t<td>" . $cache->count . " queries</td>\n\t</tr>\n</table>";

	$str .= "\n<br />";
	
	$str  .= "\n<table>\n\t<tr><td colspan='2'>DB queries</td></tr><tr class='rowheader'><td>Time</td><td style='white-space:nowrap;text-align:left;vertical-align:top;'>Query</td></tr>";
	$totaltime = 0;
	$i = 0;
	foreach($db->queries as $row){
		$colour = (stristr($row[0],'queries') ? " style='color:gray;'" : (stristr($row[0],'explain') ? " style='color:orange'" : null));
		$totaltime += $row[1];
		$str .=  "\n\t<tr class='" . $rows[$i = !$i] . "'$colour>";
		$str .=  "\n\t\t<td style='white-space:nowrap;text-align:right;vertical-align:top;'>" . number_format($row[1]*1000, 3) . " ms</td>";
		//$str .=  "<td style='white-space:nowrap;text-align:left;vertical-align:top;'>".htmlentities($row[0])."</td>";
		$str .=  "\n\t\t<td style='white-space:nowrap;text-align:left;vertical-align:top;'>$row[0]</td>";
		$str .=  "\n\t</tr>";
	}
	$str .=  "\n\t<tr class='rowheader'><td style='white-space:nowrap;text-align:right;vertical-align:top;'>" . number_format($totaltime*1000, 3) . " ms</td>";
	$str .=  "\n\t\t<td>" . $db->count . " queries</td>\n\t</tr>\n</table>\n</div>\n\n\n";

	return $str;
}
