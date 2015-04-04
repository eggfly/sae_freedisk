<?php
if (isset($_POST["path"])) {
	header("Content-type: text/plain; charset=utf-8");
	require_once("KVDBVFS.class.php");
	$path = $_POST['path'];
	$fs = new KVDBVFS();
	$result = $fs->delete($path);
	var_dump($result);
}
else{
	header("Content-type: text/html; charset=utf-8");
	echo '<html><form method="POST">delete file full path: <input type="text" name="path" /><input type="submit" value="delete"></form></html>';
}
?>
