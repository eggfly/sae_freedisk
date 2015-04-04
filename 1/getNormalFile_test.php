<?php
if (!isset($_GET["p"])) {
	header('HTTP/1.0 400 Bad Request', true, 400);
	die("path not given.");
}
require_once("KVDBVFS.class.php");
$path = base64_decode($_GET['p']);
$fs = new KVDBVFS();
$result = $fs->getFile($path);
if ($result === false)
{
	header("HTTP/1.0 404 Not Found");
	die("file not exist.");
}
header("Content-type: application/octet-stream");
$filename = basename($path);
header("Content-Disposition: attachment; filename=\"$filename\"");
echo $result;
?>
