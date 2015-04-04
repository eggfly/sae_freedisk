<?php
header("Content-type: text/html; charset=utf-8");
?>
<html>
<?php
if (isset($_POST["filename"]) && isset($_POST["content"])) {
	require_once("KVDBVFS.class.php");
	$filename = $_POST["filename"];
	$content = $_POST["content"];
	$fs = new KVDBVFS();
	$fs->storeFile($filename, $content);
	echo "\nok";
}
else echo '<form method="POST"><input type="text" name="filename" /><input type="text" name="content" /><input type="submit" value="submit"></form>';
?>
</html>
