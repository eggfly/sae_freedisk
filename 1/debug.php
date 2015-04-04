<?php
if ($_SERVER["SERVER_ADMIN"] !== "saesupport@sina.cn") require_once("local.saekv.class.php");

header("Content-Type: text/plain; charset=utf-8");
echo "debug:\n";
$kvdb = new SaeKV();
$kvdb->init();
$indexs = $kvdb->pkrget("", SaeKV::MAX_PKRGET_SIZE);
var_dump(count($indexs));
foreach ($indexs as $key => $value)
{
	if ($key == "fsIndex"||strpos($key, "id_")===0)
	{
		$value_length = strlen($value);
		echo "\n".$key." (length = ".strlen($value).")\n";
		if ($value_length < 1024) echo "Uncompressed content:\n".gzuncompress($value)."\n";
		else echo "Content is t0o big..\n";
	}
	else echo "\n".$key.": ".$value;
}
echo "\n";
//TODO
?>
