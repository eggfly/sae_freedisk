<?php
if ($_SERVER["SERVER_ADMIN"] !== "saesupport@sina.cn") require_once("local.saekv.class.php");

header("Content-Type: text/plain; charset=utf-8");
echo "Clear All KVDB:\nkeys:\n\n";
$kvdb = new SaeKV();
$kvdb->init();
$indexs = $kvdb->pkrget("", SaeKV::MAX_PKRGET_SIZE);
foreach ($indexs as $key => $value)
{
	echo $key."\n";
	$kvdb->delete($key);
}
echo "\nClear OK.\n";
?>
