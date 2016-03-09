<?php

/**
* @author Yuri Chetverov <yurictv@gmail.com>
*/

setlocale ("LC_ALL", "en_US.UTF-8");

if (!isset($_GET['svg'])) exit;
$fn = urldecode (base64_decode ($_GET['svg']));
if (!is_file ($fn)) exit;

header("Content-Disposition: attachment; filename=\"" . basename($fn) . "\"");
header("Content-Type: application/force-download");
header("Content-Length: " . filesize($fn));
header('Content-Transfer-Encoding: binary');

$fp = fopen ($fn, "r");
fpassthru ($fp);
fclose ($fp);
