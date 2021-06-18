<?php
namespace Furo\Img;

class FileInfo
{
	/**
	 * getExtension
	 * Get file extension
	 *
	 * @param string $path Set path to file
	 * @return string Return file extension
	 */
	function getExtension($path){
		return strtolower(pathinfo(basename($path), PATHINFO_EXTENSION));
	}

	/**
	 * getName
	 * Get file name
	 *
	 * @param string $path Set path to file
	 * @return string Return file name without extensions
	 */
	function getFileName($path, $tolower = 1)
	{
		if($tolower == 1){
			return strtolower(pathinfo(basename($path), PATHINFO_FILENAME));
		}else{
			return pathinfo(basename($path), PATHINFO_FILENAME);
		}
	}

	/**
	 * getDirectory function
	 *
	 * @param string $path File path
	 * @return string Directory path (/path/to/file/directory)
	 */
	function getDirectory($path)
	{
		return pathinfo($path, PATHINFO_DIRNAME);
	}

	/**
	 * getInfo function
	 *
	 * Get file info array
	 * @param string $path File path
	 * @return array File info array
	 */
	function getInfo($path)
	{
		return getimagesize($path);
	}

	/**
	 * getMime function
	 *
	 * Get file mime type
	 * @param string $path File path
	 * @return string Mime type (e.g. image/jpeg)
	 */
	function getMime($path)
	{
		$file = $this->getInfo($path);
		return $file['mime'];
	}
}
?>
