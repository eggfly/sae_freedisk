<?php
class FileInfo{
	const RECORD_SEPARATOR = ',';
	public $fileID;
	const KEY_FILEID = "fileID";
	public $partsCount;
	const KEY_PARTSCOUNT = "partsCount";
	public $modifyTime;
	const KEY_MODIFYTIME = "modifyTime";

	public function __construct($fileID, $partsCount, $modifyTime)
	{
		// update here
		$this->fileID = $fileID;
		$this->partsCount = $partsCount;
		$this->modifyTime = $modifyTime;
	}

	public static function fromArray($arrRecord)
	{
		// Factory pattern
		// update here
		return new FileInfo($arrRecord[self::key_fileID], $arrRecord[self::key_partsCount], $arrRecord[self::key_modifyTime]);
	}

	public function toArray()
	{
		// update here
		return array(
		self::key_fileID => $this->fileID,
		self::key_partsCount => $this->partsCount,
		self::key_modifyTime => $this->modifyTime,
		);
	}

	public static function fromString($strRecord)
	{
		// Factory pattern
		$arrRecord = explode(self::RECORD_SEPARATOR, $strRecord);
		// update here
		return array(
		base64_decode($arrRecord[0]),
		new FileInfo($arrRecord[1], $arrRecord[2], $arrRecord[3])
		);
	}

	public function toString($filePath)
	{
		// update here
		$arr = array(
		base64_encode($filePath),
		$this->fileID,
		$this->partsCount,
		$this->modifyTime,
		);
		return join(self::RECORD_SEPARATOR, $arr);
	}

}
?>
