<?php

/**
* @author Yuri Chetverov <yurictv@gmail.com>
*/

setlocale (LC_ALL, "en_US.UTF-8");


include "config.php";

/** inserts error or success message into template */
function put_msg (&$tmpl, $msg='') {
$re = "'^svg_url=(.*)$'";
if (preg_match ($re, $msg)) { /** composes success message */
$url = preg_replace ($re, "$1", $msg);
$dlurl = "download.php?svg=" . base64_encode (urlencode ($url));
$msg = msg_success . ' ';
$msg .= "<a href=\"$dlurl\" download target=\"_blank\">" . msg_dnl_link . "</a>";
$msg .= "<iframe src=\"$dlurl\" width=1 height=1 style=\"display:none;\"></iframe>";
$msg .= "<br /><br /><a href=\"$dlurl\" download target=\"_blank\" title=\"" . msg_dnl_link . "\">";
$msg .= "<img src=\"$url\" style=\"max-width:100%;\" /></a>";
}
if (defined ($msg)) $msg = constant ($msg); /** replaces constant name (if exists) with its content */
$tmpl = str_replace ("%msg%", $msg, $tmpl);/** replaces %msg% with $msg variable in template */
}

/** close the script and pass message to next screen */
function quit ($msg) {
global $src_fn;
if (isset ($src_fn) && !empty ($src_fn) && is_file ($src_fn)) unlink ($src_fn);
if (isset ($pdf_fn) && !empty ($pdf_fn) && is_file ($pdf_fn)) unlink ($pdf_fn);
$msg = base64_encode (urlencode ($msg));
header ('location: ' . $_SERVER['SCRIPT_NAME'] . '?msg=' . $msg);
exit;
}

//header ("Content-type: text/plain;"); print_r ($GLOBALS); exit;


/** if processing is not required, outputs template with message(s) if any */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
$tmpl = file_get_contents (template);
$msg = isset ($_GET['msg']) ? urldecode (base64_decode ($_GET['msg'])) : '';
put_msg ($tmpl, $msg);
die ($tmpl);
}

/** puts into each variable named exactly as shell command (from the array $commands bellow) 
'command with path' from the constant with the same name (if exists) or command name itself (otherwise) */
$commands = array ('convert', 'potrace', 'pdf2svg', 'epstopdf', 'curl', 'rm', 'find', 'file');
foreach ($commands as $comm) $$comm = defined ($comm) ? constant ($comm) : $comm;



/** Create folder for converted files if not exists */
if (!is_dir (converted_dir)) mkdir (converted_dir);
$dir = converted_dir . '/';

/** erase converted files older than 120 mins */
shell_exec ("$find " . converted_dir . " -type f -mmin +120 -exec $rm {} \;");



if (isset ($_POST['imgurl'])) {
/** Get source file from given URL */
$url = trim ($_POST['imgurl']);
if (!preg_match ("'.*://.+'i", $url)) $url = 'http://' . $url;
extract (parse_url($url));
if (!isset ($path)) quit ('err_url_invalid');
$name = basename ($path);
if (empty ($name)) quit ('err_url_invalid');
$src_fn = $dir . $name;
$comm = "$curl -L -o '$src_fn' --user-agent 'convertor' '$url'";
system ($comm, $ret);
if ($ret || !is_file ($src_fn) || filesize ($src_fn) == 0) quit ('err_url_curl');
} else {
/** Upload source file from local machine */
if (isset ($_FILES['file'])) extract ($_FILES['file']); else quit ('err_upload_unknown');
if ($error == 1) quit ("err_upload_1");
else if ($error == 3) quit ("err_upload_3");
else if ($error == 4) quit ("err_upload_4");
else if ($error != 0) quit ('err_upload_unknown');
$src_fn = $dir . $name;
move_uploaded_file ($tmp_name, $src_fn);
}



/** compose pathes to .ppm & .svg files */
$base = preg_replace ("'^(.*)\..*?$'", "$1", $name);
$ppm_fn = $dir . $base . '.ppm';
$pdf_fn = $dir . $base . '.pdf';
$svg_fn = $dir . $base . '.svg';
$svg_url = converted_dir_rel . '/' . $base . '.svg';

/** check if source file should be processed as pdf or eps */
$ext = str_replace ($base . '.', '', $name);
$mime = trim (shell_exec ("$file -b --mime-type '$src_fn'"));
$forse = (isset ($_POST['forse'])) ? 1 : 0;
$iseps = (!$forse && $ext == 'eps') ? 1 : 0;
$ispdf = (!$forse && ($mime == 'application/pdf' || $ext == 'pdf')) ? 1 : 0;

/** convert .eps to .pdf */
if ($iseps) {
shell_exec ("$epstopdf '$src_fn'");
unlink ($src_fn);
if (!is_file ($pdf_fn) || filesize ($pdf_fn) == 0) quit ("err_convert_unknown");
$src_fn = $pdf_fn; $ispdf = 1;
}

/** convert .pdf to .svg */
if ($ispdf) {
shell_exec ("$pdf2svg '$src_fn' '$svg_fn'");
unlink ($src_fn);
if (!is_file ($svg_fn) || filesize ($svg_fn) == 0) quit ("err_convert_unknown");

} else {

/** convert image to .ppm */
$comm = "$convert '$src_fn' '$ppm_fn'";
system ($comm, $ret);
if ($ret) quit ("err_convert_unsupported");

/** convert .ppm to .svg */
$comm = "$potrace -s '$ppm_fn'";
shell_exec ($comm);

/** delete intermediate files */
unlink ($src_fn);
unlink ($ppm_fn);

/** unknown error while converting */
if (!is_file ($svg_fn) || filesize ($svg_fn) == 0) 
quit ("err_convert_unknown");
}

/** success */
$msg = "svg_url=$svg_url";
quit ($msg);























