<?php

include '/home/5/t/tcarisland/scripts/fontstats/login.php';

function cmp($a, $b)
{
    if ($b['downloads'] == $a['downloads']) {
        return 0;
    }
    return ($b['downloads'] < $a['downloads']) ? -1 : 1;
}

function get_font_stats($url) {
	 $data = file_get_contents($url);
	 $regex = '/;([0-9,]+?) downloads/';
 	 #$regex = '/\<div style="margin-bottom:8px">10 fonts&nbsp; - &nbsp;([0-9,]+?) downloads \(([0-9]+?) yesterday\)<\/div>/';
	 $matches;
	 preg_match_all($regex, $data, $matches, PREG_PATTERN_ORDER);
	 return $matches[1][0];
}

function get_author_stats($url) {
	 $data = file_get_contents( $url );
	 $matches;
	 $regex = '/\<div><a href=\"((?:.+?)(?:\.d)(?:[0-9]+?))\">(.+?)<\/a><\/div>/';
	 preg_match_all($regex, $data, $matches, PREG_PATTERN_ORDER);
	 $url_list = array();

	 for ($i = 0; $i < count($matches[1]); $i++) {
	     #echo $array[$i]['filename'];
	     #echo $array[$i]['filepath'];
	     $value = "http://www.dafont.com/" . $matches[1][$i];
	     $line = array();
	     $link = "<a href=\"" . $value . "\" >" . htmlentities(utf8_encode($matches[2][$i])) . "</a>";
	     $line['downloads'] = str_replace(",", "", get_font_stats($value));
	     $line['link'] = $link;
	     $line['designer'] = htmlentities(utf8_encode($matches[2][$i]));
	     array_push($url_list, $line);
	}
	print_r($url_list);
	usort($url_list,"cmp");
	return $url_list;
}

function get_stats_table($list) {
	 $html_table = "<table>\n";
	 foreach ($list as $line) {
	 $html_table .= "<tr>\n";
	 foreach($line as $entry) {
	 $html_table .= "<td> " . $entry . "</td>\n";		 
	 }
	 $html_table .= "</tr>\n";
	 }
	 $html_table .= "</table>\n";
	 return $html_table;
}

$url = "https://www.dafont.com/authors.php?cc=167";
$stats = get_author_stats($url);
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
foreach ($stats as $person) {
$sql_query = "INSERT INTO daily_stats (downloads, designer, link, date_sampled, country)\n"; 
$sql_query .= 'VALUES (' .$person['downloads'].', '. "'" .$person['designer']."', ". "'" .$person['link']."', ". ' now(), "Norway");';
if ($conn->query($sql_query) === TRUE) {
echo "New record created successfully \ndesigner: " . $person['designer'] . "<br><br>\n";
} else {
echo "Error: " . $sql_query . "<br>\n\n" . $conn->error;
}
}
$conn->close();

?>