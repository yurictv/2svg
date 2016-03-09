<form action="" method="post" enctype="multipart/form-data" onsubmit="show_wait()">
			<input title="Choose file to convert to SVG image" name="file" id="file" type="file">
			<input value="Go" type="submit">
</form>

<?php

echo "<pre>";
print_r ($_FILES);
echo "</pre>";
