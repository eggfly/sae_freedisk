<?php
header("Content-type: text/html; charset=utf-8");
?>
<html>
<?php
if (isset($_FILES["file"])) {
	require_once("KVDBVFS.class.php");
	
	$filename = basename($_FILES["file"]['name']);
	$path = $_POST['path'];
	
	var_dump($_FILES["file"]);
	var_dump($path);
	
	$content = file_get_contents($_FILES['file']['tmp_name']);
	$fs = new KVDBVFS();
	$fs->storeFile($path, $content);
	echo "\nok";
}
else echo '<form method="POST" enctype="multipart/form-data"><input type="text" name="path" value="/filename" /><input type="file" name="file" /><input type="submit" value="upload" /></form>';
?>
</html>
