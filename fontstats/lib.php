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
    $matches;
    preg_match_all($regex, $data, $matches, PREG_PATTERN_ORDER);
    return $matches[1][0];
}

function get_author_stats($url) {
    $data = file_get_contents($url);
    $matches;
    $regex = '/\<div><a href=\"((?:.+?)(?:\.d)(?:[0-9]+?))\">(.+?)<\/a><\/div>/';
    preg_match_all($regex, $data, $matches, PREG_PATTERN_ORDER);
    $url_list = array();

    for ($i = 0; $i < count($matches[1]); $i++) {
        $value = "http://www.dafont.com/" . $matches[1][$i];
        $line = array();
        $link = "<a href=\"" . $value . "\" >" . htmlentities(utf8_encode($matches[2][$i])) . "</a>";
        $line['downloads'] = str_replace(",", "", get_font_stats($value));
        $line['link'] = $link;
        $line['designer'] = htmlentities(utf8_encode($matches[2][$i]));
        array_push($url_list, $line);
    }
    print_r($url_list);
    usort($url_list, "cmp");
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

function scrape_and_save($country_code, $country_name) {
    $url = "https://www.dafont.com/authors.php?cc=" . $country_code;
    $stats = get_author_stats($url);

    $results = array();
    $now = date('c');
    foreach ($stats as $person) {
        $results[] = array(
            'downloads' => intval(str_replace(',', '', $person['downloads'])),
            'designer' => $person['designer'],
            'link' => $person['link'],
            'date_sampled' => $now,
            'country' => $country_name,
        );
    }

    $outdir = __DIR__ . '/output';
    if (!file_exists($outdir)) {
        mkdir($outdir, 0755, true);
    }

    $filename = $outdir . '/' . date('Y-m-d') . '_' . $country_code . '.json';
    $json = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($filename, $json) === false) {
        echo "Failed to write JSON to: " . $filename . "\n";
    } else {
        echo "Wrote JSON: " . $filename . "\n";
    }
    return $filename;
}

?>
