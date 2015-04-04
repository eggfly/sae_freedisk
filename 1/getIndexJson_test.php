<?php
header('Content-type: application/json');
require_once("KVDBVFS.class.php");
$fs = new KVDBVFS();
$content = $fs->getIndexJSON();
echo $content;
?>
