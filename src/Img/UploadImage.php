<?php
namespace Furo\Img;

use Furo\Img\Image;
use Furo\Img\UploadException;

class UploadImage
{
	protected $MaxSize = 50000; // bajtów - 50kb

	function __construct($size_mb = 0) {
		if($size_mb > 0) { $this->maxSizeMb($size_mb); }
	}

	function upload($dest_path, $files_attr = 'file', $width = 0, $allow_ext = ['jpg','jpeg','png','gif','webp'])
	{
		if(!empty($_FILES[$files_attr])) {
            if($_FILES[$files_attr]['error'] == 0) {
				$this->createDir($dest_path);
				$this->isValidExt($files_attr, $allow_ext);
				$this->isValidFileSize($files_attr);
				$this->saveFile($files_attr, $dest_path, (int) $width);
				return 1;
			}
		}
		return 0;
	}

	function createDir($dest_path)
	{
		$dir = dirname($dest_path);
		if(!is_dir($dir)) {
			@mkdir($dir, 0777, true);
		}
	}

	function isValidExt($attr, $allow_ext)
    {
		$ext = $this->extension($_FILES[$attr]['name']);
        if(!in_array($ext, $allow_ext)) {
            throw new UploadException("ERR_UPLOAD_FILE_EXT");
        }
	}

	function isValidFileSize($attr)
    {
		if($_FILES[$attr]['size'] > $this->MaxSize) {
            throw new UploadException("ERR_UPLOAD_FILE_SIZE");
        }
	}

	function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	function maxSizeMb($mb)
    {
        $this->MaxSize = ((int) $mb * (1024 * 1024));
	}

	function saveFile($attr, $dest_path, int $width = 0)
	{
		$ext = $this->extension($_FILES[$attr]['name']);
		$tmp = '/tmp/'.md5(microtime()).'-'.md5($_FILES[$attr]['name']).'.'.$ext;
		$ok = move_uploaded_file($_FILES[$attr]['tmp_name'], $tmp);
		if($ok > 0) {
			try {
				$img = new Image();
				if($width > 0) {
					$img->load($tmp)->resizeImage($width, 0)->save($dest_path);
				}else{
					$img->load($tmp)->save($dest_path);
				}
			} catch(Exception $e) {
				unset($tmp);
				throw new UploadException("ERR_UPLOAD_SAVE_FILE", $e->getCode());
			}
		} else {
			unset($tmp);
			throw new UploadException("ERR_UPLOAD_MOVE_FILE");
		}
		unset($tmp);
	}
}