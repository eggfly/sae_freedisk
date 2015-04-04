<?php
if (isset($_POST["path"])) {
	header("Content-type: application/octet-stream");
	require_once("KVDBVFS.class.php");
	$path = $_POST['path'];
	$fs = new KVDBVFS();
	$content = $fs->getFile($path);
	echo $content;
}
else{
	header("Content-type: text/html; charset=utf-8");
	echo '<html><form method="POST">path: <input type="text" name="path" /><input type="submit" value="download" /></form></html>';
}
?>
