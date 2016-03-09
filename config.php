<?php

/**
* @author Yuri Chetverov <yurictv@gmail.com>
*/



/** path to shell commands (convert, potrace, curl, find. rm)
// Requires only if path is not encountered in environment variable PATH for web user */
// define ('convert', '/usr/bin/convert');
// define ('potrace', '/usr/bin/potrace');

/** path to html template */
define ('template', __DIR__ . '/view/template');

/** pathes to folder with converted files */
define ('converted_dir', __DIR__ . '/converted');
define ('converted_dir_rel', 'converted');
define ('converted_url', dirname ($_SERVER['SCRIPT_NAME']) . '/converted');


/** error messages */
define ('err_upload_1', 'Upload error:<br />The uploaded file exceeds the upload_max_filesize (' . ini_get ('upload_max_filesize') . ')');
define ('err_upload_3', 'Upload error:<br />The uploaded file was only partially uploaded');
define ('err_upload_4', 'Upload error:<br />No file was uploaded');
define ('err_upload_unknown', 'Upload error:<br />File was not uploaded because of unknown error');

define ('err_url_curl', 'Invalid URL or Unable to resolve host address');
define ('err_url_invalid', 'Invalid URL');

define ('err_convert_unknown', 'File cannot be converted to SVG<br />Unknown error while converting');
define ('err_convert_unsupported', 'File cannot be converted to SVG<br />Unsupported format');

/** success message */
define ('msg_success', 'Your file has been successfully converted.<br />If the download does not start, you can click on this');
define ('msg_dnl_link', 'direct download link');






