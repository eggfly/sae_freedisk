<?php
if (isset($_POST["path"])) {
	header("Content-type: text/plain; charset=utf-8");
	require_once("KVDBVFS.class.php");
	$path = $_POST['path'];
	$fs = new KVDBVFS();
	$result = $fs->listDir($path);
	foreach ($result as $path => $objFileInfo)
	{
		echo $path.", ".$objFileInfo->fileID.", ".$objFileInfo->partsCount.", ".$objFileInfo->modifyTime."\n";
	}
}
else{
	header("Content-type: text/html; charset=utf-8");
	echo '<html><form method="POST">list path: <input type="text" name="path" /><input type="submit" value="listdir"></form></html>';
}
?>
