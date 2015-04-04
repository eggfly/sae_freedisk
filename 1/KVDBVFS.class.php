<?php
if ($_SERVER["SERVER_ADMIN"] !== "saesupport@sina.cn") require_once("local.saekv.class.php");

require_once("FileInfo.class.php");

class KVDBVFS{
	const MAX_BLOCK_SIZE = 4000000;
	const INDEX_KVDB_KEY = "fsIndex";	// 存储所有文件路径,和对应文件块ID,以后可能包含文件属性 
	const FILE_KVDB_PREFIX = "id_";		// 存储所有文件块
	const LINE_SEPARATOR = "\n";
	
	private static $kvdb;
	private static $fsIndexStr;
	private static $fsIndexArray = array();
	
	public function __construct()
	{
		// 初始化kvdb
		self::$kvdb = new SaeKV();
		$ret = self::$kvdb->init();
		if (!$ret) die("kvdb initial failed: " . self::$kvdb->errmsg());
		
		// 获得并解压缩文件索引项字符串
		self::readAndUncompressIndexStringFromDB();
		
		// 建立由FileInfo实例对象组成的索引array
		self::rebuildIndexArrayFromIndexString();
	}
	
	// update here
	private function updateIndex($path, $md5, $partsCount, $modifyTime){
		self::$fsIndexArray[$path] = new FileInfo($md5, $partsCount, $modifyTime);
		self::rebuildIndexStringFromIndexArray();
		self::compressAndStoreCurrentIndexStringIntoDB();
	}
	
	private function rebuildIndexArrayFromIndexString()
	{
		if (!self::$fsIndexStr)
		{
			self::$fsIndexArray = array();
			return;
		}
		$arrLines = explode(self::LINE_SEPARATOR, trim(self::$fsIndexStr));
		foreach ($arrLines as $strLine)
		{
			if (!$strLine) continue;
			list($filePath, $objFileInfo) = FileInfo::fromString($strLine);
			self::$fsIndexArray[$filePath] = $objFileInfo;
		}
	}
	
	private function rebuildIndexStringFromIndexArray()
	{
		$newIndexString = "";
		foreach(self::$fsIndexArray as $key => $objFileInfo)
		{
			$newLine = $objFileInfo->toString($key);
			$newIndexString .= self::LINE_SEPARATOR.$newLine;
		}
		self::$fsIndexStr = $newIndexString;
	}
	
	private function readAndUncompressIndexStringFromDB()
	{
		$ret = self::$kvdb->get(self::INDEX_KVDB_KEY);
		if (!$ret)
		{
			self::$fsIndexStr = "";
			self::compressAndStoreCurrentIndexStringIntoDB();
		}
		else
		{
			self::$fsIndexStr = gzuncompress($ret);
		}
	}
	
	private function compressAndStoreCurrentIndexStringIntoDB()
	{
		self::$kvdb->set(self::INDEX_KVDB_KEY, gzcompress(trim(self::$fsIndexStr)));
	}
	
	private function compressAndSplitFileParts($content)
	{
		 $compressedContent = gzcompress($content);
		 return str_split($compressedContent, self::MAX_BLOCK_SIZE);
	}
	
	private function storeFileParts($md5, $content)
	{
		 $parts = self::compressAndSplitFileParts($content);
		 foreach ($parts as $index => $part)
		 {
		 	self::$kvdb->set(self::FILE_KVDB_PREFIX.$md5."_".$index, $part);
		 }
		 return count($parts);
	}
	
	public function storeFile($path, $content)
	{
		$md5 = md5($content);
		$partsCount = self::storeFileParts($md5, $content);
		$modifyTime = time();
		self::updateIndex($path, $md5, $partsCount, $modifyTime);
	}
	
	public function listDir($path)
	{
		if (substr($path, -1)!=='/') $path.='/';	// 在最后确保包含/

		$result = array();
		foreach (self::$fsIndexArray as $key => $objFileInfo)
		{
			if ((strpos($key, $path) === 0) && (strpos(substr($key, strlen($path)), '/') === false))
			{
				$result[$key] = $objFileInfo;
			}
		}
		return $result;
	}
	
	public function delete($path)
	{
		if (!array_key_exists($path, self::$fsIndexArray)) return false;
		
		// TODO 还没在KVDB中真正删除文件呢，删除时还要考虑交叉文件链接时，不能不管不顾就删除一个文件，其他链接失效
		unset(self::$fsIndexArray[$path]);
		self::rebuildIndexStringFromIndexArray();
		self::compressAndStoreCurrentIndexStringIntoDB();
		return true;
	}
	
	public function getFile($path)
	{
		 if (self::$fsIndexArray[$path] === null) return false;
		 $fileID = self::$fsIndexArray[$path]->fileID;
		 $partsCount = self::$fsIndexArray[$path]->partsCount;
		 $compressedContent = "";
		 for ($index = 0; $index < $partsCount; $index ++)
		 {
		 	$compressedContent .= self::$kvdb->get(self::FILE_KVDB_PREFIX.$fileID.'_'.$index);
		 }
		 $content = gzuncompress($compressedContent);
		 return $content;
	}
	
	public function getIndexJSON()
	{
		return json_encode(self::$fsIndexArray);
	}
	
}

?>
