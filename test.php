<?php

include "config.php";

echo "<pre>";

echo shell_exec ("whereis convert");
echo shell_exec ("whereis potrace");
echo shell_exec ("whereis find");
echo shell_exec ("whereis rm");
echo shell_exec ("whereis wget");
echo shell_exec ("whereis curl");
echo "\n";

echo 'PATH: ' . shell_exec ('echo $PATH');
echo "\n";

echo 'upload_max_filesize:' . ini_get ('upload_max_filesize');
echo "\n\n";

echo shell_exec ('convert' . " -version");
echo "\n";
echo shell_exec ('potrace' . " -v");
echo "\n";

print_r ($GLOBALS);
echo "</pre>";


